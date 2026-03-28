<?php

namespace App\Http\Controllers;

use App\Models\ServiceBooking;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServiceBookingController extends Controller
{
    /**
     * ១. Dashboard បញ្ជីការកក់សរុប (Admin Service Index)
     */
    public function index(Request $request)
    {
        $query = ServiceBooking::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'LIKE', "%{$search}%")
                  ->orWhere('plate', 'LIKE', "%{$search}%")
                  ->orWhere('contact', 'LIKE', "%{$search}%");
            });
        }

        $stats = [
            'total'      => ServiceBooking::count(),
            'pending'    => ServiceBooking::where('status', 'Pending')->count(),
            'progress'   => ServiceBooking::where('status', 'In Progress')->count(),
            'hold'       => ServiceBooking::where('status', 'Hold')->count(),
            'completed'  => ServiceBooking::where('status', 'Completed')->count(),
            'today'      => ServiceBooking::whereDate('booking_date', Carbon::today())->count(),
        ];

        $bookings = $query->latest()->paginate(15);
        return view('Services.services_index', compact('bookings', 'stats'));
    }

    /**
     * ២. Workshop Board (Real-time Sorting)
     */
    public function workshopIndex()
    {
        $jobs = ServiceBooking::whereIn('status', ['Pending', 'Arrived', 'In Progress', 'Hold'])
            ->orderByRaw("FIELD(status, 'In Progress', 'Arrived', 'Pending', 'Hold')")
            ->orderBy('booking_time', 'asc')
            ->get();

        $saStats = ServiceBooking::whereDate('booking_date', Carbon::today())
            ->select('sa_name',
                DB::raw("COUNT(CASE WHEN status = 'Completed' THEN 1 END) as done"),
                DB::raw("COUNT(CASE WHEN status = 'In Progress' THEN 1 END) as doing"),
                DB::raw("COUNT(CASE WHEN status = 'Arrived' THEN 1 END) as arrived"),
                DB::raw("COUNT(CASE WHEN status = 'Pending' OR status = 'Hold' THEN 1 END) as wait")
            )
            ->whereNotNull('sa_name')
            ->groupBy('sa_name')->get();

        return view('Services.workshop_board', compact('jobs', 'saStats'));
    }

    /**
     * ៣. មុខងារសម្រាប់ភ្ញៀវកក់ Online (Public Store)
     */
    /**
     * បង្ហាញ Form កក់ Online សម្រាប់ភ្ញៀវ (GET)
     */
    public function publicCreate()
    {
        return view('Services.public_booking');
    }

    /**
     * រក្សាទុកទិន្នន័យកក់ពី Online (POST)
     */
    public function publicStore(Request $request)
    {
        $request->validate([
            'customer_name' => 'required',
            'contact'       => 'required',
            'plate'         => 'required',
            'model'         => 'required',
            'service_type'  => 'required',
            'booking_date'  => 'required|date',
            'booking_time'  => 'required',
            'location'      => 'required',
        ]);

        try {
            // ដោះស្រាយបញ្ហា Format ម៉ោង (បំប្លែង "04:00 PM" ទៅជា "16:00:00")
            $formattedTime = \Carbon\Carbon::parse($request->booking_time)->format('H:i:s');

            $booking = ServiceBooking::create([
                'customer_name' => $request->customer_name,
                'contact'       => $request->contact,
                'plate'         => strtoupper($request->plate),
                'model'         => $request->model,
                'service_type'  => $request->service_type,
                'booking_date'  => $request->booking_date,
                'booking_time'  => $formattedTime,
                'location'      => $request->location,
                'status'        => 'Pending',
            ]);

            // ផ្ញើសារទៅ Telegram
            $this->sendNewBookingAlert($booking);

            return back()->with('success', 'ការកក់របស់លោកអ្នកទទួលបានជោគជ័យ!');
        } catch (\Exception $e) {
            return back()->with('error', 'មានបញ្ហា៖ ' . $e->getMessage());
        }
    }
    /**
     * ៤. AJAX ឆែកម៉ោងទំនេរ (Check Slots)
     */
    public function checkSlots(Request $request)
    {
        $date = $request->date;
        $location = $request->location;
        $timeslots = ['08:00 AM', '09:00 AM', '10:00 AM', '11:00 AM', '01:00 PM', '02:00 PM', '03:00 PM', '04:00 PM'];

        $data = [];
        foreach ($timeslots as $time) {
            $count = ServiceBooking::where('booking_date', $date)
                                   ->where('location', $location)
                                   ->where('booking_time', $time)
                                   ->count();
            $max = 3; 
            $data[] = ['time' => $time, 'remaining' => $max - $count, 'isFull' => $count >= $max];
        }
        return response()->json($data);
    }

    /**
     * ៥. មុខងារបញ្ជាការងារ (Arrive, Start, Finish, Hold, Resume)
     */
    public function arriveJob($id)
    {
        try {
            DB::table('service_bookings')->where('id', $id)->update([
                'status' => 'Arrived',
                'arrival_time' => now(),
                'updated_at' => now()
            ]);
            $job = ServiceBooking::find($id);
            if ($job) { $this->sendArrivalNotification($job); }
            return response()->json(['success' => true]);
        } catch (Exception $e) { return response()->json(['success' => false, 'message' => $e->getMessage()], 500); }
    }

    public function startJob(Request $request, $id)
    {
        try {
            $job = ServiceBooking::findOrFail($id);
            $job->update([
                'status'          => 'In Progress',
                'start_time'      => Carbon::now()->format('H:i:s'),
                'sa_name'         => $request->sa_name,
                'technician_name' => $request->technician_name,
            ]);
            $this->sendStartNotification($job);
            return response()->json(['success' => true]);
        } catch (Exception $e) { return response()->json(['success' => false, 'message' => $e->getMessage()]); }
    }

    public function finishJob($id)
    {
        try {
            $job = ServiceBooking::findOrFail($id);
            if ($job->status == 'In Progress' && $job->start_time) {
                $diff = now()->diffInSeconds(Carbon::parse($job->start_time));
                $job->total_seconds = ($job->total_seconds ?? 0) + $diff;
            }
            $job->update([
                'status' => 'Completed', 
                'end_time' => Carbon::now()->format('H:i:s'), 
                'total_seconds' => $job->total_seconds
            ]);
            $this->sendCompletionNotification($job);
            $this->notifyCustomerViaTelegram($job);
            return response()->json(['success' => true]);
        } catch (Exception $e) { return response()->json(['success' => false]); }
    }

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          

    public function holdJob($id)
    {
        $booking = ServiceBooking::findOrFail($id);
    
        // ១. Update Status ទៅជា Hold ក្នុង Database
        $booking->update([
            'status' => 'Hold'
        ]);
    
        // ២. ហៅ Function ផ្ញើសារទៅ Telegram (នេះជាកន្លែងដែលបងត្រូវថែម)
        $this->sendHoldNotification($booking); 
    
        return response()->json(['success' => true]);
    }
    
    public function resumeJob($id)
    {
        $booking = ServiceBooking::findOrFail($id);
    
        // ១. Update Status មកជា In Progress វិញ
        $booking->update([
            'status' => 'In Progress'
        ]);
    
        // ២. ហៅ Function ផ្ញើសារប្រាប់ថាបន្តការងារវិញ
        $this->sendResumeNotification($booking);
    
        return response()->json(['success' => true]);
    }

    public function checkNewBookings()
    {
        // ឆែករកការកក់ណាដែលទើបតែបង្កើតក្នុងរយៈពេល ១ នាទីចុងក្រោយ និងមាន Status ជា Pending
        $newBooking = \App\Models\ServiceBooking::where('status', 'Pending')
            ->where('created_at', '>=', now()->subSeconds(30))
            ->first();
    
        if ($newBooking) {
            return response()->json([
                'hasNew' => true,
                'customer' => $newBooking->customer_name,
                'plate' => $newBooking->plate
            ]);
        }
    
        return response()->json(['hasNew' => false]);
    }

    /**
     * ៦. Telegram Notifications Engine (FULL INFO)
     */
    private function executeTelegramCurl($text) {
        $token = env('TELEGRAM_BOT_TOKEN');
        $chat_id = env('TELEGRAM_CHAT_ID');
        if (!$token || !$chat_id) return;
        $url = "https://api.telegram.org/bot$token/sendMessage";
        $data = ['chat_id' => $chat_id, 'text' => $text, 'parse_mode' => 'HTML', 'disable_web_page_preview' => true];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url); curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch); curl_close($ch);
    }

    private function sendNewBookingAlert($booking) {
        $text = "🆕 <b>ការកក់ថ្មី (NEW ONLINE BOOKING)</b>\n"
              . "━━━━━━━━━━━━━━━━━━\n"
              . "👤 <b>អតិថិជន:</b> {$booking->customer_name}\n"
              . "📞 <b>ទូរស័ព្ទ:</b> <code>{$booking->contact}</code>\n"
              . "🔢 <b>ស្លាកលេខ:</b> <code>{$booking->plate}</code>\n"
              . "🚘 <b>ម៉ូឌែល:</b> {$booking->model}\n"
              . "🛠️ <b>សេវាកម្ម:</b> {$booking->service_type}\n"
              . "📅 <b>ថ្ងៃណាត់:</b> " . date('d-M-Y', strtotime($booking->booking_date)) . "\n"
              . "⏰ <b>ម៉ោង:</b> " . date('h:i A', strtotime($booking->booking_time)) . "\n"
              . "📍 <b>សាខា:</b> {$booking->location}\n"
              . "━━━━━━━━━━━━━━━━━━\n"
              . "👉 <a href='".url('/workshop-board')."'>ចូលទៅកាន់ Dashboard</a>";
        $this->executeTelegramCurl($text);
    }

    private function sendArrivalNotification($booking) {
        $text = "📍 <b>អតិថិជនបានមកដល់ (ARRIVED)</b>\n"
              . "━━━━━━━━━━━━━━━━━━\n"
              . "👤 <b>អតិថិជន:</b> {$booking->customer_name}\n"
              . "📞 <b>ទូរស័ព្ទ:</b> <code>{$booking->contact}</code>\n"
              . "🔢 <b>ស្លាកលេខ:</b> <code>{$booking->plate}</code>\n"
              . "🚘 <b>ម៉ូឌែល:</b> {$booking->model}\n"
              . "🛠️ <b>សេវាកម្ម:</b> {$booking->service_type}\n"
              . "⏰ <b>ម៉ោងមកដល់:</b> " . now()->format('h:i A') . "\n"
              . "📍 <b>សាខា:</b> {$booking->location}";
        $this->executeTelegramCurl($text);
    }

    private function sendStartNotification($booking) {
        $text = "🚀 <b>ចាប់ផ្ដើមការងារ (JOB STARTED)</b>\n"
              . "━━━━━━━━━━━━━━━━━━\n"
              . "🔢 <b>ស្លាកលេខ:</b> <code>{$booking->plate}</code>\n"
              . "🚘 <b>ម៉ូឌែល:</b> {$booking->model}\n"
              . "👤 <b>អតិថិជន:</b> {$booking->customer_name}\n"
              . "👨‍💼 <b>SA:</b> {$booking->sa_name}\n"
              . "🛠️ <b>ជាង:</b> {$booking->technician_name}\n"
              . "🕒 <b>Start:</b> " . now()->format('h:i A');
        $this->executeTelegramCurl($text);
    }

    private function sendCompletionNotification($booking)
    {
        // ១. គណនារយៈពេលសរុបឱ្យចេញជាលេខមូល (Round)
        $sec = $booking->total_seconds ?? 0;
        $minutes = round($sec / 60); // បង្ហត់នាទីឱ្យនៅត្រឹមលេខគត់
        
        if ($minutes >= 60) {
            $h = floor($minutes / 60);
            $m = $minutes % 60;
            $durationText = "{$h} ម៉ោង {$m} នាទី";
        } else {
            $durationText = "{$minutes} នាទី";
        }
    
        // ២. រៀបចំសារ Full Info តាមទម្រង់ដែលបងចង់បាន
        $text = "✅ <b>ការជួសជុលបានបញ្ចប់ (JOB COMPLETED)</b>\n"
              . "━━━━━━━━━━━━━━━━━━\n"
              . "📍 <b>សាខា៖</b> {$booking->location}\n"
              . "🚗 <b>ម៉ូឌែល៖</b> {$booking->model}\n"
              . "🔢 <b>ស្លាកលេខ៖</b> <code>{$booking->plate}</code>\n"
              . "👤 <b>អតិថិជន៖</b> {$booking->customer_name}\n"
              . "📞 <b>លេខទូរស័ព្ទ៖</b> <code>{$booking->contact}</code>\n"
              . "🛠 <b>ប្រភេទសេវាកម្ម៖</b> {$booking->service_type}\n"
              . "━━━━━━━━━━━━━━━━━━\n"
              . "⏰ <b>ចាប់ផ្ដើម៖</b> " . ($booking->start_time ? date('h:i A', strtotime($booking->start_time)) : 'N/A') . "\n"
              . "⏰ <b>បញ្ចប់៖</b> " . ($booking->end_time ? date('h:i A', strtotime($booking->end_time)) : now()->format('h:i A')) . "\n"
              . "⏳ <b>ចំណាយពេលសរុប៖</b> {$durationText}\n"
              . "━━━━━━━━━━━━━━━━━━\n"
              . "👨‍💼 <b>ទីប្រឹក្សាសេវាកម្ម (SA)៖</b> " . ($booking->sa_name ?? 'N/A') . "\n"
              . "👨‍🔧 <b>មេជាងទទួលបន្ទុក៖</b> " . ($booking->technician_name ?? 'N/A') . "\n"
              . "━━━━━━━━━━━━━━━━━━\n"
              . "📢 <b>SA សូមទាក់ទងទៅអតិថិជនដើម្បីប្រគល់ឡាន!</b>";
    
        $this->executeTelegramCurl($text);
    }

    private function sendHoldNotification($booking) {
        $text = "⚠️ <b>ផ្អាកការងារ (JOB ON HOLD)</b>\n"
              . "━━━━━━━━━━━━━━━━━━\n"
              . "🔢 <b>ស្លាកលេខ:</b> <code>{$booking->plate}</code>\n"
              . "👤 <b>អតិថិជន:</b> {$booking->customer_name}\n"
              . "🛠️ <b>សេវាកម្ម:</b> {$booking->service_type}\n"
              . "👨‍កមេជាង: {$booking->technician_name}\n"
              . "🕒 <b>ផ្អាកនៅម៉ោង:</b> " . now()->format('h:i A') . "\n"
              . "📍 <b>សាខា:</b> {$booking->location}";
        $this->executeTelegramCurl($text);
    }
    
    private function sendResumeNotification($booking) {
        $text = "⏯️ <b>បន្តការងារ (JOB RESUMED)</b>\n"
              . "━━━━━━━━━━━━━━━━━━\n"
              . "🔢 <b>ស្លាកលេខ:</b> <code>{$booking->plate}</code>\n"
              . "👤 <b>អតិថិជន:</b> {$booking->customer_name}\n"
              . "👨‍កមេជាង: {$booking->technician_name}\n"
              . "🕒 <b>បន្តនៅម៉ោង:</b> " . now()->format('h:i A') . "\n"
              . "📍 <b>សាខា:</b> {$booking->location}";
        $this->executeTelegramCurl($text);
    }
    private function notifyCustomerViaTelegram($job) {
        $cleanPhone = preg_replace('/[^0-9]/', '', $job->contact);
        $tgPhone = str_starts_with($cleanPhone, '0') ? '855'.substr($cleanPhone, 1) : $cleanPhone;
        $msg = "🔔 <b>MG SERVICE</b>\nឡានស្លាកលេខ <b>{$job->plate}</b> រួចរាល់ហើយ!\n🔗 <a href='https://t.me/+{$tgPhone}'>តបវិញ</a>";
        $this->executeTelegramCurl($msg);
    }
}