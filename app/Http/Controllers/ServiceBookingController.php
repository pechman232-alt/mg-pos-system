<?php

namespace App\Http\Controllers;

use App\Models\ServiceBooking;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\FirebaseService;

class ServiceBookingController extends Controller

    
{
    private function resolveId($id)
    {
        try {
            return decrypt($id);
        } catch (Exception $e) {
            return $id;
        }
    }

    private function applyBranchFilterScope($query, $branchId)
    {
        return $query->where(function ($q) use ($branchId) {
            $q->where('location', $branchId);
            if ($branchId == 11) {
                $q->orWhere('location', 'LIKE', '%50m%');
            } elseif ($branchId == 10) {
                $q->orWhere('location', 'LIKE', '%btb%')->orWhere('location', 'LIKE', '%battambang%');
            } elseif ($branchId == 12) {
                $q->orWhere('location', 'LIKE', '%sr%')->orWhere('location', 'LIKE', '%siem%');
            } elseif ($branchId == 13) {
                $q->orWhere('location', 'LIKE', '%hq%')->orWhere('location', 'LIKE', '%head%');
            }
        });
    }

    private function getCambodiaHolidays()
    {
        return [
            '2026-01-01', '2026-03-08', '2026-04-14', '2026-04-15', '2026-04-16',
            '2026-05-01',  '2026-05-05', '2026-05-14', '2026-05-31', '2026-09-24', '2026-10-10',
            '2026-10-11', '2026-10-12', '2026-10-29', '2026-11-09', '2026-11-23', '2026-11-24', '2026-11-25',
        ];
    }

    public function publicCreate()
    {
        return view('Services.public_booking');
    }

    public function showTicket($id)
    {
        $booking = ServiceBooking::findOrFail($this->resolveId($id));

        return view('Services.booking_success', compact('booking'));
    }

    public function showPublicTicket($id)
    {
        $booking = ServiceBooking::findOrFail($this->resolveId($id));

        return view('Services.show_completed_ticket', compact('booking'));
    }

    public function checkSlots(Request $request)
    {
        $date = $request->date;
        $location = $request->location;
        $holidays = $this->getCambodiaHolidays();

        if (in_array($date, $holidays)) {
            return response()->json(['isHoliday' => true, 'message' => 'សូមអភ័យទោស! ថ្ងៃនេះគឺជាថ្ងៃឈប់សម្រាកបុណ្យជាតិ។']);
        }

        $bookingDate = Carbon::parse($date);
        $dayOfWeek = $bookingDate->dayOfWeek;

        if ($dayOfWeek == 0) {
            return response()->json(['isHoliday' => true, 'message' => 'សូមអភ័យទោស! យើងបិទធ្វើការនៅថ្ងៃអាទិត្យ។']);
        }

        $branchId = null;
        $branchName = '';
        if (is_numeric($location)) {
            $branchId = $location;
            $branchObj = DB::table('branches')->where('id', $location)->first();
            $branchName = $branchObj ? strtoupper($branchObj->branch_name) : '';
        } else {
            $branchName = strtoupper($location);
        }

        $schedule = [];

        // ផ្នែកកំណត់ចំនួន Slot សម្រាប់សាខា HQ
        if ($branchId == 13 || str_contains($branchName, 'HQ') || str_contains($branchName, 'HEAD')) {
            // ថ្ងៃច័ន្ទ ដល់ សុក្រ (DayOfWeek: 1-5)
            if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {
                $schedule = [
                    '08:00 AM' => 7,
                    '10:00 AM' => 7,
                    '01:00 PM' => 7,
                    '03:00 PM' => 7,
                ];
            }
            // ថ្ងៃសៅរ៍ (DayOfWeek: 6)
            elseif ($dayOfWeek == 6) {
                $schedule = [
                    '08:00 AM' => 4,
                    '10:00 AM' => 4,
                    '01:00 PM' => 4,
                    '03:00 PM' => 4,
                ];
            }
            // ថ្ងៃអាទិត្យ (DayOfWeek: 0) បិទធ្វើការ
            else {
                $schedule = [];
            }
        } elseif ($branchId == 10 || str_contains($branchName, 'BTB') || str_contains($branchName, 'BATTAMBANG')) {
            if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {
                $schedule = ['08:00 AM' => 2, '10:00 AM' => 2, '01:00 PM' => 2, '03:00 PM' => 2];
            } else {
                $schedule = ['08:00 AM' => 2, '10:00 AM' => 2];
            }
        } else {
            if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {
                $schedule = ['08:00 AM' => 3, '10:00 AM' => 3, '01:00 PM' => 3, '03:00 PM' => 3];
            } else {
                $schedule = ['08:00 AM' => 3, '10:00 AM' => 3];
            }
        }

        $data = [];
        foreach ($schedule as $time => $limit) {
            $dbTime = Carbon::parse($time)->format('H:i:s');

            $count = ServiceBooking::whereDate('booking_date', $date)
                ->where(function ($q) use ($location, $branchId) {
                    if ($branchId) {
                        $q->where('location', $location)->orWhere('branch_id', $branchId);
                    } else {
                        $q->where('location', $location);
                    }
                })
                ->whereNotIn('status', ['Cancel', 'Cancelled'])
                ->where(function ($q) use ($time, $dbTime) {
                    $q->where('booking_time', $dbTime)->orWhere('booking_time', $time);
                })->count();

            $remaining = $limit - $count;
            $data[] = [
                'time' => $time,
                'remaining' => max(0, $remaining),
                'isFull' => $remaining <= 0,
            ];
        }

        return response()->json($data);
    }
    public function publicStore(Request $request)
    {
        // 🌟 របាំងការពារកំពូល៖ ឆែកមើលថាតើមានសោសម្ងាត់ (API Key) ដែរឬទេ?
        $secretKey = $request->header('X-API-KEY');
        
        // សោសម្ងាត់នេះ មានតែ Website របស់បងទេដែលដឹង! (បងអាចដូរពាក្យនេះបានតាមចិត្ត)
        if ($secretKey !== 'MgDealer@2026!Secure') {
            // បើអត់មានសោ ឬសោខុស ទាត់ចោលភ្លាមៗ មិនបាច់ឱ្យឆ្លងទៅឆែកអីផ្សេងទៀតទេ!
            return response()->json([
                'success' => false, 
                'message' => 'Access Denied! អ្នកគ្មានសិទ្ធិបាញ់ទិន្នន័យចូលប្រព័ន្ធនេះទេ។ (Invalid API Key)'
            ], 403);
        }

        // ១. តម្រូវឱ្យមានទិន្នន័យគ្រប់គ្រាន់
        $request->validate([
            'customer_name' => 'required', 
            'contact'       => 'required', 
            'plate'         => 'required', 
            'model'         => 'required', 
            'mileage'       => 'required', 
            'service_type'  => 'required', 
            'location'      => 'required', 
            'booking_date'  => 'required|date', 
            'booking_time'  => 'required'
        ]);

        // ២. ឆែកមើលថ្ងៃឈប់សម្រាកបុណ្យជាតិ
        $holidays = $this->getCambodiaHolidays();
        if (in_array($request->booking_date, $holidays)) {
            return response()->json(['success' => false, 'message' => 'សូមអភ័យទោស! ថ្ងៃដែលអ្នកជ្រើសរើសគឺជាថ្ងៃឈប់សម្រាកបុណ្យជាតិ។'], 422);
        }

        // ៣. ឆែកមើលក្រែងលោឡាននេះកំពុងជាប់កក់ (Active Booking)
        $cleanPlate = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $request->plate));
        
        $activeBooking = \App\Models\ServiceBooking::whereRaw("UPPER(REPLACE(REPLACE(plate, ' ', ''), '-', '')) = ?", [$cleanPlate])
            ->whereNotIn('status', [
                'completed', 'close', 'cancel', 'cancelled', 'archived', 'archive',
                'Completed', 'Close', 'Cancel', 'Cancelled', 'Archived', 'Archive'
            ])
            ->first();

        if ($activeBooking) {
            return response()->json([
                'success' => false, 
                'message' => 'សុំទោស! ស្លាកលេខរថយន្តនេះ កំពុងមានការកក់នៅក្នុងប្រព័ន្ធរួចហើយ។ សូមរង់ចាំការកក់ចាស់បញ្ចប់សិន។'
            ], 422);
        }

        try {
            $formattedTime = \Carbon\Carbon::parse($request->booking_time)->format('H:i:s');
            
            // 🌟 ៤. របាំងការពារការបាញ់ទិន្នន័យសាខាក្លែងបន្លំពី PowerShell / Postman 🌟
            $locationValue = $request->location;
            
            // ឆែកមើលថា location ដែលបាញ់មកជាលេខ ID ដែរឬទេ?
            if (is_numeric($locationValue)) {
                
                // ទៅឆែកក្នុង Database ថាតើមាន ID សាខានេះពិតប្រាកដឬអត់?
                $branch = DB::table('branches')->where('id', $locationValue)->first();
                
                if (!$branch) {
                    // បើរកមិនឃើញ មានន័យថាគេលួចបន្លំលេខ ID (ឧ. ដាក់ 999) ត្រូវទាត់ចោលភ្លាម!
                    return response()->json(['success' => false, 'message' => 'ទិន្នន័យសាខាមិនត្រឹមត្រូវទេ! (Invalid Branch)'], 422);
                }
                
                // បើត្រឹមត្រូវ យកឈ្មោះសាខាទៅ Save ជំនួសលេខ ID
                $locationValue = $branch->branch_name;
                $branchId = $branch->id;
                
            } else {
                // បើគេបាញ់មកជាអក្សរផ្ដេសផ្ដាស មិនមែនជាលេខ ក៏ត្រូវទាត់ចោលដែរ
                return response()->json(['success' => false, 'message' => 'ទម្រង់សាខាមិនត្រឹមត្រូវទេ!'], 422);
            }
            // 🌟 បញ្ចប់របាំងការពារ 🌟
            
            // ៥. សម្អាតទិន្នន័យលេខកុងទ័រ (យកតែលេខ)
            $cleanMileage = (int) preg_replace('/[^0-9]/', '', $request->mileage);
            $bookingStatus = ($request->has('is_walk_in') && $request->is_walk_in == 1) ? 'Arrived' : 'Pending';

            // ៦. បញ្ចូលទិន្នន័យទៅក្នុង Database
            $booking = new \App\Models\ServiceBooking;
            $booking->fill([
                'customer_name' => $request->customer_name, 
                'contact'       => $request->contact, 
                'plate'         => strtoupper($request->plate), 
                'model'         => $request->model, 
                'mileage'       => $cleanMileage, 
                'service_type'  => $request->service_type, 
                'remark'        => $request->remark, 
                'booking_date'  => $request->booking_date, 
                'booking_time'  => $formattedTime, 
                'location'      => $locationValue, 
                'branch_id'     => $branchId, 
                'status'        => $bookingStatus, 
                'is_walk_in'    => 0
            ]);
            $booking->save();

            // ៧. ផ្ញើ Notification
            $this->sendNewBookingAlert($booking, '🌐 ប្រព័ន្ធ Online Booking (អតិថិជនកក់ខ្លួនឯង)');

            return response()->json(['success' => true, 'message' => 'Booking successfully created!', 'booking_id' => $booking->id]);
            
        } catch (\Exception $e) {
            \Log::error('Public Store Booking Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'បញ្ហាប្រព័ន្ធ៖ '.$e->getMessage()], 500);
        }
    }
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = ServiceBooking::query();
        

        $workshopBranchIds = [10, 11, 12, 13];
        $branches = DB::table('branches')->whereIn('id', $workshopBranchIds)->get();

        if ($user && ! in_array($user->role_id, [1, 11])) {
            $this->applyBranchFilterScope($query, $user->branch_id);
        } else {
            if ($request->filled('branch') && $request->branch !== 'all') {
                $this->applyBranchFilterScope($query, $request->branch);
            }
        }

        // ប្រើ Carbon::now('Asia/Phnom_Penh') ដើម្បីធានាថាវាយកម៉ោងនៅខ្មែរជានិច្ច
        $fromDate = $request->input('from_date', \Carbon\Carbon::now('Asia/Phnom_Penh')->format('Y-m-d'));
        $toDate = $request->input('to_date', \Carbon\Carbon::now('Asia/Phnom_Penh')->format('Y-m-d'));

        $query->whereDate('booking_date', '>=', $fromDate);
        $query->whereDate('booking_date', '<=', $toDate);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'LIKE', "%{$search}%")->orWhere('plate', 'LIKE', "%{$search}%")->orWhere('contact', 'LIKE', "%{$search}%");
            });
        }

        if ($request->ajax() && $request->filled('fetch_all_for_export')) {
            $exportData = (clone $query)->orderBy('booking_date', 'desc')->orderBy('booking_time', 'desc')->get();
            $formattedData = $exportData->map(function ($book) use ($branches) {
                $rawLoc = $book->location;
                $locObj = collect($branches)->firstWhere('id', $rawLoc);
                $displayLoc = $locObj ? str_ireplace(['MG ', 'Mg '], '', $locObj->branch_name) : str_ireplace(['MG ', 'Mg '], '', $rawLoc);
                $durationStr = 'N/A';
                if (! empty($book->start_time) && ! empty($book->end_time)) {
                    try {
                        $start = \Carbon\Carbon::parse($book->start_time);
                        $end = \Carbon\Carbon::parse($book->end_time);
                        $diff = $start->diff($end);
                        $durationStr = ($diff->h > 0 ? $diff->h.'h ' : '').$diff->i.'m';
                    } catch (\Exception $e) {
                    }
                }
                $statusKey = strtolower(trim((string) $book->status));
                $adminNoteText = $book->note ?? '';
                if (in_array($statusKey, ['completed', 'close'])) {
                    $adminNoteText = 'បានបញ្ចប់ (Close)';
                } elseif (in_array($statusKey, ['archived', 'archive'])) {
                    $adminNoteText = 'បានទុកក្នុងបណ្ណសារ (Archived)';
                } elseif (in_array($statusKey, ['cancel', 'cancelled'])) {
                    $adminNoteText = 'បានបោះបង់ (Cancelled)';
                }

                return [$book->customer_name ?? 'N/A', $book->contact ?? 'N/A', $book->model ?? 'N/A', $book->plate ?? 'N/A', ! empty($book->mileage) ? number_format($book->mileage).' KM' : 'N/A', $book->service_type ?? 'N/A', $book->sa_name ?? 'N/A', $book->technician_name ?? 'N/A', ucwords(trim($displayLoc) ?: 'N/A'), (! empty($book->booking_date) ? date('d-M-Y', strtotime($book->booking_date)) : 'N/A').' '.(! empty($book->booking_time) ? date('h:i A', strtotime($book->booking_time)) : ''), str_ireplace('Waiting Parts Order', 'Parts', $book->status ?? 'Pending'), ! empty($book->start_time) ? date('h:i A', strtotime($book->start_time)) : '-', ! empty($book->end_time) ? date('h:i A', strtotime($book->end_time)) : '-', $durationStr, $book->remark ?? '-', $adminNoteText ?: '-'];
            });

            return response()->json(['success' => true, 'data' => $formattedData]);
        }

        $allBookingsForStats = (clone $query)->get();
        $stats = ['total' => 0, 'pending' => 0, 'waiting' => 0, 'progress' => 0, 'completed' => 0, 'cancel' => 0, 'archive' => 0, 'overdue' => 0];

        $activeCat = $request->input('category', 'all');
        $filteredIds = [];

        date_default_timezone_set('Asia/Phnom_Penh');
        $nowPhnomPenhTs = time();

        foreach ($allBookingsForStats as $book) {
            $stats['total']++;
            $statusKey = strtolower(trim((string) ($book->status ?? '')));
            $isOverdue = false;
            $bookCategory = 'pending';

            if (in_array($statusKey, ['pending', 'waiting approved', 'waiting', 'waiting service'])) {
                try {
                    $bDate = \Carbon\Carbon::parse($book->booking_date)->format('Y-m-d');
                    $bTime = \Carbon\Carbon::parse($book->booking_time)->format('H:i:s');
                    $bookDateTime = \Carbon\Carbon::parse($bDate.' '.$bTime, 'Asia/Phnom_Penh');

                    if ($bookDateTime->isPast()) {
                        $isOverdue = true;
                    }
                } catch (\Exception $e) {
                }
            }

            if ($isOverdue) {
                $stats['overdue']++;
                $bookCategory = 'overdue';
            } else {
                if (in_array($statusKey, ['pending', 'waiting approved'])) {
                    $stats['pending']++;
                    $bookCategory = 'pending';
                } elseif (in_array($statusKey, ['waiting', 'waiting service', 'waiting parts order local', 'waiting parts order dhl', 'waiting parts order by sea', 'waiting delivery', 'arrived'])) {
                    $stats['waiting']++;
                    $bookCategory = 'waiting';
                } elseif (in_array($statusKey, ['in progress', 'wip', 'under repair', 'under diagnosis'])) {
                    $stats['progress']++;
                    $bookCategory = 'progress';
                } elseif (in_array($statusKey, ['completed', 'close'])) {
                    $stats['completed']++;
                    $bookCategory = 'completed';
                } elseif (in_array($statusKey, ['cancel', 'cancelled'])) {
                    $stats['cancel']++;
                    $bookCategory = 'cancel';
                } elseif (in_array($statusKey, ['archive', 'archived'])) {
                    $stats['archive']++;
                    $bookCategory = 'archive';
                }
            }

            // 🌟 នេះគឺជាចំណុចដែលកែសម្រួលដើម្បីលាក់ Status ទាំង ៣ ពីទំព័រមុខ 🌟
            if ($activeCat === 'all' || $activeCat === 'total') {
                // ប្រសិនបើកំពុងមើល All យើងដក Cancel, Archive, និង Overdue ចេញពីតារាង
                if (!in_array($bookCategory, ['cancel', 'archive', 'overdue'])) {
                    $filteredIds[] = $book->id;
                }
            } else {
                // ប្រសិនបើចុចលើកាតណាមួយ (ឧទាហរណ៍ចុចកាត Cancel) នោះយើងបង្ហាញវាធម្មតា
                if ($activeCat === $bookCategory) {
                    $filteredIds[] = $book->id;
                }
            }
        }

        // ដូរ booking_time ទៅជា 'asc' ដើម្បីរៀបម៉ោង ៨, ៩, ១០ តាមលំដាប់
        $finalQuery = ServiceBooking::whereIn('id', $filteredIds)
            ->orderBy('booking_date', 'desc') 
            ->orderBy('booking_time', 'asc'); 
            
        $bookings = $finalQuery->paginate(15)->withQueryString();

        return view('Services.services_index', compact('bookings', 'stats', 'branches', 'fromDate', 'toDate'));
    }
    public function updateEstimation(Request $request, $id)
    {
        try {
            $realId = decrypt($id);
            $booking = ServiceBooking::findOrFail($realId);

            // 🌟 ១. ចាប់យកឈ្មោះអ្នកកំពុង Login ឱ្យបានប្រាកដ 🌟
            $user = auth()->user();
            $userName = $user->name ?? $user->username ?? $user->first_name ?? 'Admin (ID: ' . ($user->id ?? 0) . ')';

            // 🌟 ២. ទាញប្រវត្តិ និងការពារកុំឱ្យ Error JSON 🌟
            $history = $booking->estimation_history;
            if (is_string($history)) {
                $history = json_decode($history, true);
            }
            if (is_string($history)) { // ការពារ Double Encode
                $history = json_decode($history, true);
            }
            if (!is_array($history)) {
                $history = [];
            }

            // 🌟 ៣. បង្កើតកំណត់ត្រាថ្មី 🌟
            $history[] = [
                'estimated_datetime' => $request->new_estimated_datetime,
                'remark'             => $request->estimation_remark,
                'updated_by'         => $userName, // បោះឈ្មោះដែលចាប់បានចូល
                'updated_at'         => now()->toDateTimeString(),
            ];

            // 🌟 ៤. Save ចូល Database 🌟
            $booking->estimation_history = json_encode($history);
            $booking->estimated_end_datetime = $request->new_estimated_datetime;
            $booking->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    public function dailyReport(Request $request)
    {
        $fromDate = $request->input('from_date'); 
        $toDate = $request->input('to_date');
        $selectedStatus = $request->input('status', 'pending_wip'); 
        $selectedBranch = $request->input('branch', 'all'); 
    
        $query = \App\Models\ServiceBooking::query();
    
        // 🌟 1. Filter តាមសាខា (ចងទិន្នន័យលេខ និងឈ្មោះបញ្ចូលគ្នា) 🌟
        if ($selectedBranch !== 'all') {
            $query->where(function($q) use ($selectedBranch) {
                // ស្វែងរកតាមពាក្យដែលបានរើសធម្មតា
                $q->where('location', 'LIKE', '%' . $selectedBranch . '%');

                // ស្វែងរកបន្ថែមសម្រាប់សាខាធំៗ ដើម្បីចាប់យកលេខ ID បញ្ចូលគ្នា
                $str = strtolower($selectedBranch);
                if (str_contains($str, '50m') || $str == '11') {
                    $q->orWhere('location', 'LIKE', '%50m%')->orWhere('location', '11')->orWhere('branch_id', 11);
                } elseif (str_contains($str, 'hq') || str_contains($str, 'head') || $str == '13') {
                    $q->orWhere('location', 'LIKE', '%hq%')->orWhere('location', 'LIKE', '%head%')->orWhere('location', '13')->orWhere('branch_id', 13);
                } elseif (str_contains($str, 'btb') || str_contains($str, 'battambang') || $str == '10') {
                    $q->orWhere('location', 'LIKE', '%btb%')->orWhere('location', 'LIKE', '%battambang%')->orWhere('location', '10')->orWhere('branch_id', 10);
                } elseif (str_contains($str, 'sr') || str_contains($str, 'siem') || $str == '12') {
                    $q->orWhere('location', 'LIKE', '%sr%')->orWhere('location', 'LIKE', '%siem%')->orWhere('location', '12')->orWhere('branch_id', 12);
                }
            });
        }
    
        if (!empty($fromDate) && !empty($toDate)) {
            $query->whereDate('booking_date', '>=', $fromDate)
                  ->whereDate('booking_date', '<=', $toDate);
        }
    
        if ($selectedStatus !== 'all') {
            if ($selectedStatus === 'pending') {
                $query->whereIn('status', ['Pending', 'Waiting Approved', 'Waiting', 'Waiting Parts Order Local', 'Waiting Parts Order DHL', 'Waiting Parts Order by Sea', 'Waiting Delivery']);
            } elseif ($selectedStatus === 'wip') {
                $query->whereIn('status', ['In Progress', 'WIP', 'Under Repair', 'Under Diagnosis']);
            } elseif ($selectedStatus === 'pending_wip') {
                $query->whereNotIn('status', ['Completed', 'Close', 'Cancel', 'Cancelled', 'Archive', 'Archived']);
            } else {
                $query->where('status', $selectedStatus);
            }
        }
    
        $reports = $query->orderBy('created_at', 'desc')->get();
    
        if ($request->ajax() && $request->has('fetch_all_for_export')) {
            return response()->json(['success' => true, 'data' => $reports]);
        }
    
        $branchSummaries = [];
        foreach ($reports as $booking) {
            $branch = $booking->location ?? 'Unknown';
            $branch = ucwords(trim(str_ireplace(['MG ', 'Mg '], '', $branch))); 
            
            // 🌟 2. ច្របាច់បញ្ចូលទិន្នន័យសាខាឱ្យចេញជា Card តែមួយរួមគ្នា 🌟
            $branchLower = strtolower($branch);
            $branchId = $booking->branch_id ?? '';

            if (str_contains($branchLower, '50m') || $branch == '11' || $branchId == 11) {
                $branch = 'Service 50m';
            } elseif (str_contains($branchLower, 'hq') || str_contains($branchLower, 'head') || $branch == '13' || $branchId == 13) {
                $branch = 'Service Hq (head Office)';
            } elseif (str_contains($branchLower, 'btb') || str_contains($branchLower, 'battambang') || $branch == '10' || $branchId == 10) {
                $branch = 'Service Btb (battambang)';
            } elseif (str_contains($branchLower, 'sr') || str_contains($branchLower, 'siem') || $branch == '12' || $branchId == 12) {
                $branch = 'Service Sr (siem Reap)';
            }
    
            $statusKey = strtolower(trim($booking->status));
    
            if (!isset($branchSummaries[$branch])) {
                $branchSummaries[$branch] = ['waiting' => 0, 'wip' => 0, 'delayed' => 0, 'total' => 0, 'cars' => []];
            }
    
            $isWaiting = in_array($statusKey, ['pending', 'waiting approved', 'waiting', 'waiting parts order local', 'waiting parts order dhl', 'waiting parts order by sea', 'waiting delivery']);
            $isWip = in_array($statusKey, ['in progress', 'wip', 'under repair', 'under diagnosis']);
            
            $isDelayed = false;
            if (empty($booking->estimated_end_datetime)) {
                $isDelayed = true; 
            } else {
                if (\Carbon\Carbon::parse($booking->estimated_end_datetime)->isPast()) {
                    $isDelayed = true; 
                }
            }
    
            if ($isWaiting) $branchSummaries[$branch]['waiting']++;
            if ($isWip) $branchSummaries[$branch]['wip']++;
            if ($isDelayed) $branchSummaries[$branch]['delayed']++;
            $branchSummaries[$branch]['total']++;
    
            $branchSummaries[$branch]['cars'][] = [
                'plate' => $booking->plate ?? 'N/A',
                'customer' => $booking->customer_name ?? 'N/A',
                'status' => $booking->status,
                'is_wip' => $isWip,
                'is_delayed' => $isDelayed,
                'est_complete' => $booking->estimated_end_datetime ? \Carbon\Carbon::parse($booking->estimated_end_datetime)->format('d-M-Y h:i A') : 'N/A'
            ];
        }
    
        // 🌟 3. ទាញយកឈ្មោះសាខាស្អាតៗទៅឱ្យ Filter Dropdown ប្រើ 🌟
        $rawBranches = \App\Models\ServiceBooking::whereNotNull('location')->pluck('location')->toArray();
        $cleanBranches = [];
        foreach($rawBranches as $loc) {
            $b = ucwords(trim(str_ireplace(['MG ', 'Mg '], '', $loc)));
            $str = strtolower($b);
            
            if (str_contains($str, '50m') || $b == '11') $b = 'Service 50m';
            elseif (str_contains($str, 'hq') || str_contains($str, 'head') || $b == '13') $b = 'Service Hq (head Office)';
            elseif (str_contains($str, 'btb') || str_contains($str, 'battambang') || $b == '10') $b = 'Service Btb (battambang)';
            elseif (str_contains($str, 'sr') || str_contains($str, 'siem') || $b == '12') $b = 'Service Sr (siem Reap)';
            
            if (!in_array($b, $cleanBranches)) {
                $cleanBranches[] = $b;
            }
        }
        sort($cleanBranches);
        $allBranches = $cleanBranches;
    
        return view('Services.daily_report', compact('reports', 'fromDate', 'toDate', 'selectedStatus', 'selectedBranch', 'branchSummaries', 'allBranches'));
    }
    public function create()
    {
        // ពិនិត្យសិទ្ធិ User ហាមឃាត់ POS account (role_id == 18)
        if (auth()->check() && auth()->user()->role_id == 18) {
            abort(403, 'Access Denied! POS accounts do not have permission to access the booking creation page.');
        }

        // បង្ហាញផ្ទាំង Form បញ្ចូលទិន្នន័យ (Add Service)
        return view('Services.add_services');
    }

    public function store(Request $request)
    
    {
        $request->validate([
            'customer_name' => 'required',
            'contact'       => 'required',
            'plate'         => 'required',
            'model'         => 'required',
            'mileage'       => 'required|numeric',
            'service_type'  => 'required',
            'location'      => 'required',
            'booking_date'  => 'required|date',
            'booking_time'  => 'required'
        ]);

        $holidays = $this->getCambodiaHolidays();
        if (in_array($request->booking_date, $holidays)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'ថ្ងៃដែលអ្នកជ្រើសរើសគឺជាថ្ងៃឈប់សម្រាកបុណ្យជាតិ។'], 422);
            }
            return back()->with('error', 'សូមអភ័យទោស! ថ្ងៃដែលអ្នកជ្រើសរើសគឺជាថ្ងៃឈប់សម្រាកបុណ្យជាតិ។')->withInput();
        }

      // 🌟 ដាក់កូដនេះ នៅក្នុង Function publicStore (ឬ public_store) 🌟

// ១. សម្អាតស្លាកលេខ (លុបដកឃ្លា និងសញ្ញាដក (-) ចេញទាំងអស់ ដើម្បីកុំឱ្យប្រព័ន្ធច្រឡំ)
$cleanPlate = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $request->plate));

// ២. ស្វែងរកទិន្នន័យចាស់ក្នុង Database ដែលកំពុង Pending ឬ In Progress
$activeBooking = \App\Models\ServiceBooking::whereRaw("UPPER(REPLACE(REPLACE(plate, ' ', ''), '-', '')) = ?", [$cleanPlate])
    ->whereNotIn('status', [
        'completed', 'close', 'cancel', 'cancelled', 'archived', 'archive',
        'Completed', 'Close', 'Cancel', 'Cancelled', 'Archived', 'Archive'
    ])
    ->first();

// ៣. បើរកឃើញថាមានឡានហ្នឹងកំពុងជាប់កក់មែន បោះ Error ទៅវិញភ្លាម
if ($activeBooking) {
    $errorMsg = 'សុំទោស! ស្លាកលេខរថយន្តនេះ កំពុងមានការកក់នៅក្នុងប្រព័ន្ធរួចហើយ។ សូមរង់ចាំការកក់ចាស់បញ្ចប់សិន។';
    
    if ($request->wantsJson() || $request->ajax()) {
        return response()->json(['success' => false, 'message' => $errorMsg], 422);
    }
    return back()->with('error', $errorMsg)->withInput(); 
}

        try {
            $formattedTime = \Carbon\Carbon::parse($request->booking_time)->format('H:i:s');
            $locationValue = $request->location;
            $branchId = is_numeric($locationValue) ? $locationValue : null;

            if ($branchId) {
                $branch = DB::table('branches')->where('id', $locationValue)->first();
                if ($branch) {
                    $locationValue = $branch->branch_name;
                }
            }

            $cleanMileage = (int) preg_replace('/[^0-9]/', '', $request->mileage);
            $bookingStatus = ($request->has('is_walk_in') && $request->is_walk_in == 1) ? 'Arrived' : 'Pending';

            // 🌟 ចាប់យកឈ្មោះ User ដែលកំពុង Login 🌟
            $authUser = auth('users')->user() ?? auth()->user();
            $addedBy = $authUser ? ($authUser->name ?? $authUser->full_name ?? 'បុគ្គលិក MG') : 'បុគ្គលិក MG';

            $booking = new \App\Models\ServiceBooking;
            $booking->fill([
                'customer_name' => $request->customer_name,
                'contact'       => $request->contact,
                'plate'         => $cleanPlate, // ប្រើ $cleanPlate ដែលបាន Convert ជាអក្សរធំរួច
                'model'         => $request->model,
                'mileage'       => $cleanMileage,
                'service_type'  => $request->service_type,
                'booking_date'  => $request->booking_date,
                'booking_time'  => $formattedTime,
                'location'      => $locationValue,
                'branch_id'     => $branchId,
                'status'        => $bookingStatus,
                'is_walk_in'    => 1
            ]);

            // 🌟 Save Remark និង added_by 🌟
            $booking->remark = $request->input('remark');
            $booking->added_by = $addedBy;
            $booking->save();

            // 1. ប្រព័ន្ធ Alert ចាស់ (Telegram/Email)
            $this->sendNewBookingAlert($booking, "👨‍💻 <b>អ្នកបញ្ចូល (Walk-in):</b> {$addedBy}");

            // 2. 🌟 Firebase Notification ថ្មី 🌟
            try {
                // រកមើល Admin ឬ Staff ដែលត្រូវទទួលដំណឹង (បងអាចកែ លក្ខខណ្ឌ Users នេះតាមតម្រូវការ)
                $targetStaff = \App\Models\User::where('role', 'admin')->first(); 

                if ($targetStaff && $targetStaff->fcm_token) {
                    $firebase = new \App\Services\FirebaseService();
                    $firebase->sendNotification(
                        $targetStaff->fcm_token,
                        'ការកក់សេវាកម្មថ្មី! 📅',
                        "អតិថិជន: {$booking->customer_name} ({$booking->plate}) បានកក់សេវាកម្ម {$booking->service_type}"
                    );
                }
            } catch (\Exception $e) {
                // ដាក់ Log កុំឱ្យប៉ះពាល់ដល់ប្រព័ន្ធ Booking
                \Log::error('Firebase Notification Error: ' . $e->getMessage());
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'ការកក់របស់អ្នកទទួលបានជោគជ័យ!', 'booking_id' => $booking->id]);
            }

            return redirect()->route('services.index')->with('success', 'បន្ថែមការកក់ថ្មីជោគជ័យ!');
        } catch (\Exception $e) {
            \Log::error('Store Booking Error: '.$e->getMessage());
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'បញ្ហាប្រព័ន្ធ៖ '.$e->getMessage()], 500);
            }
            return back()->with('error', 'បញ្ហាប្រព័ន្ធ៖ '.$e->getMessage())->withInput();
        }
    
    }
    public function edit($id)
    {
        $booking = \App\Models\ServiceBooking::findOrFail($this->resolveId($id));

        $adminRoles = [1, 2, 6, 11];
        $isAdmin = in_array(auth()->user()->role_id, $adminRoles);
        
        // 🌟 ចាប់យកទីតាំង (ការពារបញ្ហា Column ខុសគ្នា)
        $targetBranchId = $booking->location ?? $booking->branch_id ?? auth()->user()->branch_id;
        
        $saQuery = \App\Models\User::where('role_id', 12)->where('status', 1);
        $techQuery = \App\Models\EmployeeModel::where('status', 1);

        $techPositionIds = [195, 196, 199, 200]; 

        if ($isAdmin) {
            // បើជា Admin គឺអាចមើលឃើញ SAs & មេជាងទាំងអស់
            $techQuery->whereIn('position_id', $techPositionIds);
        } else {
            // 🌟 បើជា User ធម្មតា ទាញយកតែ SAs និងជាង ដែលមាន branch_id ដូចគ្នានឹងសាខាខ្លួនឯងប៉ុណ្ណោះ
            $userBranch = auth()->user()->branch_id;
            
            $saQuery->where('branch_id', $userBranch);
            $techQuery->where('branch_id', $userBranch)
                      ->whereIn('position_id', $techPositionIds);
        }

        // ៤. ទាញយកទិន្នន័យចេញមកជា Array
        $serviceAdvisors = $saQuery->pluck('full_name');
        $technicians = $techQuery->pluck('full_name');

        // ៥. ឡុកទម្រង់មិនឱ្យកែប្រែពេលឡានធ្វើរួច
        $currentStatus = $booking->status ? strtolower($booking->status) : '';
        $isLocked = in_array($currentStatus, ['completed', 'close', 'cancelled', 'archive', 'archived']);

        // ៦. ចាប់យកឈ្មោះ User ដែលកំពុង Login
        $currentUserName = auth()->user()->full_name ?? auth()->user()->name ?? auth()->user()->username ?? '';

        return view('Services.edit_services', compact('booking', 'serviceAdvisors', 'technicians', 'isLocked', 'currentUserName'));
    }
    public function update(Request $request, $id)
    {
        // ១. Validate ទិន្នន័យចាំបាច់ (ដក 'location' ออกពី required ដើម្បីការពាររឿងអត់រើសសាខា)
        $request->validate([
            'customer_name' => 'required', 
            'contact'       => 'required', 
            'plate'         => 'required', 
            'model'         => 'required', 
            'service_type'  => 'required',
            'booking_date'  => 'required|date',
            'booking_time'  => 'required',
        ]);
    
        try {
            $booking = ServiceBooking::findOrFail($this->resolveId($id));
    
            // ២. ប្រើ update() ដើម្បី Save ទិន្នន័យថ្មីទាំងអស់ចូល Database
            $booking->update([
                'customer_name'   => $request->customer_name,
                'contact'         => $request->contact,
                'plate'           => strtoupper($request->plate), // បំប្លែងស្លាកលេខជាអក្សរធំជានិច្ច
                'model'           => $request->model,
                'mileage'         => $request->mileage,          // 🌟 គីឡូម៉ែត្រ
                'service_type'    => $request->service_type,
                
                // 🌟 ពេលវេលា និងកាលបរិច្ឆេទកក់ 🌟
                'booking_date'    => $request->booking_date,
                'booking_time'    => $request->booking_time,
                'start_time'      => $request->start_time,
                'end_time'        => $request->end_time,
                
                // 🌟 សាខា និងបុគ្គលិកចាត់តាំង (ប្រើ ?? $booking->location ដើម្បីការពារពេលអត់រើសសាខាថ្មី) 🌟
                'location'        => $request->location ?? $booking->location,
                'sa_name'         => $request->sa_name,
                'technician_name' => $request->technician_name,
                
                // 🌟 ស្ថានភាព និងកំណត់ចំណាំ 🌟
                'status'          => $request->status ?? $booking->status,
                'remark'          => $request->remark,
                'note'            => $request->note,
            ]);
    
            return redirect()->route('services.index')->with('success', 'កែប្រែព័ត៌មានជោគជ័យ!');
            
        } catch (\Exception $e) {
            // កត់ត្រា Error ទុកក្នុង Log និងត្រឡប់ទៅវិញព្រមទាំងបង្ហាញមូលហេតុ
            \Log::error('Update Booking Error: ' . $e->getMessage()); 
            return back()->with('error', 'បញ្ហាប្រព័ន្ធ៖ '.$e->getMessage())->withInput();
        }
    }
    public function delete($id)
    {
        ServiceBooking::findOrFail($this->resolveId($id))->delete();
        return back()->with('success', 'លុបទិន្នន័យជោគជ័យ!');
    }

    public function workshopIndex(Request $request)
    {
        $user = auth('users')->user() ?? auth()->user();
        $selectedDate = $request->input('date', Carbon::today('Asia/Phnom_Penh')->format('Y-m-d'));

        $workshopBranchIds = [10, 11, 12, 13];
        $branches = DB::table('branches')->whereIn('id', $workshopBranchIds)->get();

        $jobsQuery = ServiceBooking::where(function ($q) use ($selectedDate) {
            $q->whereDate('booking_date', $selectedDate)
                ->orWhere(function ($subQ) use ($selectedDate) {
                    $subQ->whereDate('booking_date', '<', $selectedDate)
                        ->whereIn('status', ['Pending', 'Arrived', 'In Progress', 'Hold', 'Waiting Approved']);
                });
        });

        $saStatsQuery = ServiceBooking::whereDate('booking_date', $selectedDate)->whereNotNull('sa_name');

        $roleId = (int) ($user->role_id ?? 0);
        $isManager = in_array($roleId, [1, 6, 11]);
        $currentBranchFilter = 'all';

        if ($isManager) {
            if ($request->filled('branch') && $request->branch !== 'all') {
                $currentBranchFilter = $request->branch;
                $this->applyBranchFilterScope($jobsQuery, $currentBranchFilter);
                $this->applyBranchFilterScope($saStatsQuery, $currentBranchFilter);
            }
        } else {
            $currentBranchFilter = $user->branch_id;
            $this->applyBranchFilterScope($jobsQuery, $currentBranchFilter);
            $this->applyBranchFilterScope($saStatsQuery, $currentBranchFilter);
        }

        // 🌟 ១. រៀបចំអថេរសម្រាប់បំបែក SA និង ជាង (គ្មានពាក្យ 'all' ទៀតទេ) 🌟
        $saByBranch = ['10' => [], '11' => [], '12' => [], '13' => []];
        $techByBranch = ['10' => [], '11' => [], '12' => [], '13' => []];
        $allStaffsFlat = [];
        $dbErrorMsg = '';

        $busyTechnicians = ServiceBooking::where('status', 'In Progress')
            ->whereNotNull('technician_name')
            ->pluck('technician_name')
            ->toArray();

        try {
            $tableName = \Illuminate\Support\Facades\Schema::hasTable('employees') ? 'employees' : 'employee';

            // 🔥 កំណត់យកលេខ ID នាយកដ្ឋានចំៗទាំង ៨ នេះគត់ (លុបលេខ 17 ចោលដាច់ខាត) 🔥
            $targetDeptIds = [48, 49, 50, 57, 52, 55, 56, 51];

            $employees = DB::table($tableName)->whereIn('department_id', $targetDeptIds)->get();

            foreach ($employees as $emp) {
                $empName = trim($emp->full_name ?? $emp->name ?? '');
                if (empty($empName)) {
                    continue;
                }

                $dId = (int) $emp->department_id;
                $allStaffsFlat[] = $empName;

                // 👨‍💼 សម្រាប់ Service Advisor (SA)
                if ($dId == 48) {
                    $saByBranch['13'][] = $empName;
                } // SA HQ
                elseif ($dId == 49) {
                    $saByBranch['11'][] = $empName;
                } // SA 50M
                elseif ($dId == 50) {
                    $saByBranch['12'][] = $empName;
                } // SA SR
                elseif ($dId == 57) {
                    $saByBranch['10'][] = $empName;
                } // SA BTB

                // 🔧 សម្រាប់ Technician (ជាង)
                elseif ($dId == 52) {
                    $techByBranch['13'][] = $empName;
                } // Tech HQ
                elseif ($dId == 55) {
                    $techByBranch['11'][] = $empName;
                } // Tech 50M
                elseif ($dId == 56) {
                    $techByBranch['12'][] = $empName;
                } // Tech SR
                elseif ($dId == 51) {
                    $techByBranch['10'][] = $empName;
                } // Tech BTB
            }
        } catch (\Exception $e) {
            $dbErrorMsg = $e->getMessage();
            Log::error('Staff Fetch Error: '.$dbErrorMsg);
        }

        $allStaffsFlat = array_unique($allStaffsFlat);
        sort($allStaffsFlat);

        $employeesByBranch = [];
        foreach ($saByBranch as $bId => $sas) {
            $employeesByBranch[$bId] = array_unique($sas);
        }
        foreach ($techByBranch as $bId => $techs) {
            if (! isset($employeesByBranch[$bId])) {
                $employeesByBranch[$bId] = [];
            }
            $employeesByBranch[$bId] = array_unique(array_merge($employeesByBranch[$bId], $techs));
        }

        foreach ($saByBranch as $key => $arr) {
            $saByBranch[$key] = array_unique($arr);
            sort($saByBranch[$key]);
        }
        foreach ($techByBranch as $key => $arr) {
            $techByBranch[$key] = array_unique($arr);
            sort($techByBranch[$key]);
        }
        foreach ($employeesByBranch as $key => $arr) {
            sort($employeesByBranch[$key]);
        }

        if ($isManager) {
            $branchEmployees = ($currentBranchFilter !== 'all') ? ($employeesByBranch[$currentBranchFilter] ?? []) : $allStaffsFlat;
        } else {
            $branchEmployees = $employeesByBranch[(string) $user->branch_id] ?? [];
        }

        $jobs = $jobsQuery->whereIn('status', ['Pending', 'Arrived', 'In Progress', 'Hold'])
            ->orderByRaw("FIELD(status, 'In Progress', 'Arrived', 'Pending', 'Hold')")
            ->orderBy('booking_time', 'asc')
            ->get();

        $saStats = $saStatsQuery->select('sa_name',
            DB::raw("COUNT(CASE WHEN status = 'Completed' THEN 1 END) as done"),
            DB::raw("COUNT(CASE WHEN status = 'In Progress' THEN 1 END) as doing"),
            DB::raw("COUNT(CASE WHEN status = 'Arrived' THEN 1 END) as arrived"),
            DB::raw("COUNT(CASE WHEN status = 'Pending' OR status = 'Hold' THEN 1 END) as wait")
        )->groupBy('sa_name')->get();

        return view('Services.workshop_board', compact('jobs', 'saStats', 'branches', 'branchEmployees', 'employeesByBranch', 'allStaffsFlat', 'selectedDate', 'saByBranch', 'techByBranch', 'busyTechnicians', 'dbErrorMsg'));
    }

    public function arriveJob($id)
    {
        try {
            $job = ServiceBooking::findOrFail($id);
            $job->status = 'Arrived';

            // 🌟 ចាប់យកឈ្មោះអ្នកដែលកំពុង Login ដាក់ជា SA ស្វ័យប្រវត្តិ (លឿន និងច្បាស់លាស់) 🌟
            $authUser = auth('users')->user() ?? auth()->user();
            if (empty($job->sa_name) && $authUser) {
                // យកឈ្មោះរបស់គាត់ទៅរក្សាទុកក្នុង Column sa_name
                $job->sa_name = trim($authUser->name ?? $authUser->full_name ?? 'SA');
            }

            try {
                $job->arrival_time = \Carbon\Carbon::now('Asia/Phnom_Penh');
            } catch (\Exception $ex) {
                // ការពារ Error បើគ្មាន Column
            }

            $job->save();

            if (method_exists($this, 'sendTelegramStatusAlert')) {
                $this->sendTelegramStatusAlert($job);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Arrive Job Error: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function startJob(Request $request, $id)
    {
        try {
            $job = \App\Models\ServiceBooking::findOrFail($id);
            
            // បន្ថែមការកត់ត្រា job_started_at និងចាប់យក booking_date ពី Popup
            $job->update([
                'booking_date'    => $request->booking_date, // 🌟 ចាប់យកថ្ងៃកក់ដែលបញ្ជូនមកពី AJAX
                'status'          => 'In Progress', 
                'start_time'      => \Carbon\Carbon::now()->format('H:i:s'), 
                'job_started_at'  => \Carbon\Carbon::now(), 
                'sa_name'         => $request->sa_name, 
                'technician_name' => $request->technician_name
            ]);
            
            $this->sendTelegramStatusAlert($job);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    public function holdJob($id)
    {
        try {
            $booking = ServiceBooking::findOrFail($id);
            
            $updateData = ['status' => 'Hold'];

            // 🌟 គណនាវិនាទីដែលធ្វើបាន រួចបូកបញ្ចូលទៅ total_seconds
            if ($booking->job_started_at) {
                $startTime = Carbon::parse($booking->job_started_at);
                $secondsWorked = $startTime->diffInSeconds(Carbon::now());
                
                $updateData['total_seconds'] = $booking->total_seconds + $secondsWorked;
                $updateData['job_started_at'] = null; // លុបម៉ោងចាប់ផ្តើមចោល ដើម្បីត្រៀម Resume លើកក្រោយ
            }

            $booking->update($updateData);
            $this->sendTelegramStatusAlert($booking);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function resumeJob($id)
    {
        try {
            $booking = ServiceBooking::findOrFail($id);
            
            // 🌟 ពេល Resume ត្រូវកត់ត្រា job_started_at សារជាថ្មី ដើម្បីឱ្យនាឡិកាបន្តរាប់
            $booking->update([
                'status' => 'In Progress',
                'job_started_at' => Carbon::now() 
            ]);
            
            $this->sendTelegramStatusAlert($booking);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function finishJob($id)
    {
        try {
            $job = ServiceBooking::findOrFail($id);
            
            $updateData = [
                'status' => 'Completed', 
                'end_time' => now('Asia/Phnom_Penh')->format('H:i:s')
            ];

            // 🌟 គណនាវិនាទីចុងក្រោយ បូកបញ្ចូលទៅ total_seconds មុននឹងបញ្ចប់
            if ($job->job_started_at) {
                $startTime = Carbon::parse($job->job_started_at);
                $secondsWorked = $startTime->diffInSeconds(Carbon::now());
                
                $updateData['total_seconds'] = $job->total_seconds + $secondsWorked;
                $updateData['job_started_at'] = null; 
            }

            $job->update($updateData);
            $this->sendTelegramStatusAlert($job);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            Log::error('Finish Job Error: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
  

    public function updateNote(Request $request, $id)
    {
        try {
            $booking = ServiceBooking::findOrFail($this->resolveId($id));
            $booking->note = $request->note;
            $booking->save();

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function checkNewBooking(Request $request)
    {
        $lastId = $request->last_id;
        $user = auth()->user();
        $query = ServiceBooking::where('id', '>', $lastId);
        if ($user && ! in_array((int) $user->role_id, [1, 6, 11])) {
            $query->where('location', $user->branch_id);
        } $newBooking = $query->latest()->first();
        if ($newBooking) {
            return response()->json(['hasNew' => true, 'customer' => $newBooking->customer_name, 'plate' => $newBooking->plate, 'new_id' => $newBooking->id]);
        }

        return response()->json(['hasNew' => false]);
    }

    public function checkNewBookings()
    {
        try {
            $count = ServiceBooking::where('status', 'Pending')->count();

            return response()->json(['success' => true, 'new_bookings' => $count]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    public function performanceOverview (Request $request)
    {
        $user = auth()->user();
        $workshopBranchIds = [10, 11, 12, 13];
        $branches = DB::table('branches')->whereIn('id', $workshopBranchIds)->get();
        
        // 🌟 ទទួលតម្លៃ From Date និង To Date
        $fromDate = $request->input('from_date', Carbon::today('Asia/Phnom_Penh')->startOfMonth()->format('Y-m-d'));
        $toDate = $request->input('to_date', Carbon::today('Asia/Phnom_Penh')->format('Y-m-d'));
        
        $selectedDate = $toDate;
        $baseQuery = ServiceBooking::query();
        
        if ($user && ! in_array((int) $user->role_id, [1, 6, 11])) {
            $this->applyBranchFilterScope($baseQuery, $user->branch_id);
        } elseif ($request->filled('branch') && $request->branch !== 'all') {
            $this->applyBranchFilterScope($baseQuery, $request->branch);
        }

        $dailyStandardSeconds = 8 * 3600; // 8 ម៉ោង = 28,800 វិនាទី

        // ទាញយកទិន្នន័យពី Database តាមចន្លោះ From Date និង To Date
        $monthlyBookings = (clone $baseQuery)
            ->whereNotNull('technician_name')
            ->where('technician_name', '!=', '')
            ->where(function($query) use ($fromDate, $toDate) {
                $query->whereBetween('booking_date', [$fromDate, $toDate])
                      ->orWhereBetween('updated_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
                      ->orWhereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
            })
            ->get();
        
        $dailyCars = (clone $baseQuery)->whereDate('booking_date', $selectedDate)->get();
        $carsIn = $dailyCars->count();
        $carsDelivery = $dailyCars->whereIn('status', ['Completed', 'Close', 'completed', 'close'])->count();
        $todayPending = $dailyCars->whereNotIn('status', ['Completed', 'Close', 'Cancel', 'Archive', 'completed', 'close', 'cancel', 'archive'])->count();
        
        $allPendingCars = (clone $baseQuery)->whereDate('booking_date', '<=', $selectedDate)->whereNotIn('status', ['Completed', 'Close', 'Cancel', 'Archive', 'completed', 'close', 'cancel', 'archive'])->get();
        $totalPending = $allPendingCars->count();
        $totalOrderPartLocal = $allPendingCars->whereIn('status', ['Waiting Parts Order Local', 'waiting parts order local'])->count();
        $totalOrderPartSea = $allPendingCars->whereIn('status', ['Waiting Parts Order by Sea', 'waiting parts order by sea'])->count();
        
        $totalPartWarranty = 0;
        $totalPartRetail = 0;
        $saDailyStats = [];
        $saCarsInStats = [];
        $saDeliveryStats = [];
        
        foreach ($dailyCars->whereNotNull('sa_name')->where('sa_name', '!=', '')->groupBy('sa_name') as $sa => $cars) {
            $walkInCount = $cars->filter(function ($car) {
                return empty($car->booking_time);
            })->count();
            
            $bookingCount = $cars->count() - $walkInCount; 
            
            $saDailyStats[$sa] = [
                'total' => $cars->count(), 
                'booking' => $bookingCount, 
                'walkin' => $walkInCount
            ];
            
            $saCarsInStats[$sa] = $cars->count();
        }
        
        foreach ($dailyCars->whereIn('status', ['Completed', 'Close', 'completed', 'close'])->whereNotNull('sa_name')->where('sa_name', '!=', '')->groupBy('sa_name') as $sa => $cars) {
            $saDeliveryStats[$sa] = $cars->count();
        }
        
        $saPendingStats = [];
        foreach ($allPendingCars->whereNotNull('sa_name')->where('sa_name', '!=', '')->groupBy('sa_name') as $sa => $cars) {
            $saPendingStats[$sa] = $cars->count();
        }
        
        $pendingDescStats = [
            'Under Repair' => $allPendingCars->whereIn('status', ['In Progress', 'WIP', 'Under Repair', 'in progress', 'wip', 'under repair'])->count(), 
            'Under Diagnostic' => $allPendingCars->whereIn('status', ['Under Diagnosis', 'under diagnosis'])->count(), 
            'Waiting Part' => $allPendingCars->whereIn('status', ['Waiting Parts Order Local', 'Waiting Parts Order DHL', 'Waiting Parts Order by Sea'])->count(), 
            'Waiting Approve' => $allPendingCars->whereIn('status', ['Waiting Approved', 'waiting approved'])->count(), 
            'Waiting Delivery' => $allPendingCars->whereIn('status', ['Waiting Delivery', 'waiting delivery'])->count(), 
            'Waiting QT' => 0, 
            'Under BP' => 0
        ];
        
        $branchPendingStats = [];
        foreach ($allPendingCars->groupBy('location') as $loc => $cars) {
            $locObj = collect($branches)->firstWhere('id', $loc);
            $displayLoc = $locObj ? str_ireplace(['MG ', 'Mg '], '', $locObj->branch_name) : $loc;
            $branchPendingStats[$displayLoc] = $cars->count();
        }
        
        $completedQuery = (clone $baseQuery)->whereIn('status', ['Completed', 'Close', 'completed', 'close']);
        if ($request->filled('to_date') || $request->filled('date') || $request->filled('from_date')) {
            $completedQuery->whereBetween('booking_date', [$fromDate, $toDate]);
        }
        
        $topSA = (clone $completedQuery)->select('sa_name', DB::raw('count(*) as total'))->whereNotNull('sa_name')->where('sa_name', '!=', '')->groupBy('sa_name')->orderBy('total', 'desc')->take(10)->get();
        $topTech = (clone $completedQuery)->select('technician_name', DB::raw('count(*) as total'))->whereNotNull('technician_name')->where('technician_name', '!=', '')->groupBy('technician_name')->orderBy('total', 'desc')->take(10)->get();
        $totalThroughput = (clone $completedQuery)->count();

        $techPerformances = [];
        $groupedByTech = $monthlyBookings->groupBy('technician_name');

        foreach ($groupedByTech as $techName => $bookings) {
            
            $calculateSeconds = function($booking) {
                try {
                    if (isset($booking->total_seconds) && (int)$booking->total_seconds > 0) {
                        return (int) $booking->total_seconds;
                    }
                    
                    if (!empty($booking->start_time) && !empty($booking->end_time)) {
                        $dateStr = !empty($booking->booking_date) 
                            ? Carbon::parse($booking->booking_date)->format('Y-m-d') 
                            : date('Y-m-d');
                        
                        $start = Carbon::parse($dateStr . ' ' . trim($booking->start_time));
                        $end = Carbon::parse($dateStr . ' ' . trim($booking->end_time));
                        
                        if ($end->lessThan($start)) {
                             $end->addDay();
                        }
                        
                        return $start->diffInSeconds($end);
                    }
                } catch (\Exception $e) {
                    return 0; 
                }
                return 0;
            };

            // គណនាប្រចាំថ្ងៃ (Daily)
            $dailySeconds = $bookings->filter(function($booking) use ($selectedDate) {
                try {
                    $dateToParse = $booking->booking_date ?? $booking->updated_at ?? $booking->created_at ?? now();
                    return Carbon::parse($dateToParse)->timezone('Asia/Phnom_Penh')->format('Y-m-d') === $selectedDate;
                } catch (\Exception $e) {
                    return false;
                }
            })->sum($calculateSeconds);
            
            $dailyPercent = ($dailySeconds > 0) ? ($dailySeconds / $dailyStandardSeconds) * 100 : 0;
            $dailyOTSeconds = max(0, $dailySeconds - $dailyStandardSeconds);
            
            // 🌟 គណនា Period Target តាមចំនួនថ្ងៃជាក់ស្តែងដែលជាងម្នាក់ៗមានការងារធ្វើ (Active Days)
            $activeDaysCount = $bookings->map(function($booking) {
                $dateToParse = $booking->booking_date ?? $booking->updated_at ?? $booking->created_at;
                return $dateToParse ? Carbon::parse($dateToParse)->format('Y-m-d') : null;
            })->filter()->unique()->count();
            
            $activeDaysCount = $activeDaysCount > 0 ? $activeDaysCount : 1;
            $periodStandardSeconds = $activeDaysCount * $dailyStandardSeconds;

            // គណនាសរុបតាម Range (Period) ធៀបនឹង Target ថ្ងៃជាក់ស្តែង
            $monthlySeconds = $bookings->sum($calculateSeconds);
            $monthlyPercent = ($monthlySeconds > 0 && $periodStandardSeconds > 0) ? ($monthlySeconds / $periodStandardSeconds) * 100 : 0;
            $monthlyOTSeconds = max(0, $monthlySeconds - $periodStandardSeconds);

            $techPerformances[] = (object)[
                'name' => $techName,
                
                // Daily Data
                'daily_actual_hours'   => floor($dailySeconds / 3600),
                'daily_actual_minutes' => floor(($dailySeconds % 3600) / 60),
                'daily_percent'        => $dailyPercent,
                'daily_bar_percent'    => min(100, $dailyPercent), 
                'daily_ot_hours'       => floor($dailyOTSeconds / 3600),
                'daily_ot_minutes'     => floor(($dailyOTSeconds % 3600) / 60),
                
                // Period Data (Calculated based on Active Working Days)
                'monthly_percent'      => $monthlyPercent,
                'monthly_actual_hours' => floor($monthlySeconds / 3600),
                'monthly_actual_minutes'=> floor(($monthlySeconds % 3600) / 60),
                'monthly_ot_hours'     => floor($monthlyOTSeconds / 3600),
                'monthly_ot_minutes'   => floor(($monthlyOTSeconds % 3600) / 60),
            ];
        }

        return view('Services.performance_overview', compact(
            'branches', 'fromDate', 'toDate', 'selectedDate', 'carsIn', 'carsDelivery', 'todayPending', 'totalPending', 
            'totalOrderPartLocal', 'totalOrderPartSea', 'totalPartWarranty', 'totalPartRetail', 
            'saDailyStats', 'saCarsInStats', 'saDeliveryStats', 'saPendingStats', 'pendingDescStats', 
            'branchPendingStats', 'topSA', 'topTech', 'totalThroughput', 'techPerformances'
        ));
    }
    public function cancelBooking($id)
    {
        try {
            $realId = decrypt($id);
        } catch (\Exception $e) {
            $realId = $id;
        }
        $booking = \App\Models\ServiceBooking::findOrFail($realId);
        $booking->update(['status' => 'Cancel']);
        if (method_exists($this, 'sendTelegramStatusAlert')) {
            $this->sendTelegramStatusAlert($booking);
        }

        return back()->with('success', 'ការកក់ត្រូវបានបោះបង់ជោគជ័យ!');
    }

    public function archive($id)
    {
        try {
            $realId = decrypt($id);
        } catch (\Exception $e) {
            $realId = $id;
        }
        $booking = \App\Models\ServiceBooking::findOrFail($realId);
        $booking->update(['status' => 'archive']);

        return back()->with('success', 'បញ្ជូនទៅកាន់បណ្ណសារជោគជ័យ!');
    }

    public function getNotifications()
    {
        try {
            $user = auth('users')->user();
            if (! $user) {
                return response()->json(['count' => 0, 'data' => []]);
            }

            // 🌟 ១. លុប Date Filter ចេញ! រក្សាតែលក្ខខណ្ឌ Status
            // បញ្ចូលតែ Status ណាដែលចាត់ទុកថាការងារមិនទាន់បានបញ្ចប់ (Active Tasks)
            $query = \App\Models\ServiceBooking::whereIn('status', [
                'Pending', 'Waiting Approved', 'pending', 'PENDING', 'waiting approved',
            ]);

            // បើសិនជាបងចង់ប្រើវិធី "យកទាំងអស់ លើកលែងតែ Status Closed" បងអាចសរសេរអញ្ចេះវិញក៏បាន៖
            // $query = \App\Models\ServiceBooking::whereNotIn('status', ['Closed', 'Completed', 'Done']);

            $topLevelRoles = [1, 6, 11];

            if (! in_array((int) $user->role_id, $topLevelRoles)) {
                $branchId = (string) $user->branch_id;

                if (empty($branchId)) {
                    return response()->json(['count' => 0, 'data' => []]);
                }

                $query->where(function ($q) use ($branchId) {
                    $q->where('location', $branchId);

                    if ($branchId == '13') {
                        $q->orWhere('location', 'LIKE', '%ការិយាល័យកណ្តាល%')->orWhere('location', 'LIKE', '%HQ%');
                    } elseif ($branchId == '11') {
                        $q->orWhere('location', 'LIKE', '%៥០%')->orWhere('location', 'LIKE', '%50m%');
                    } elseif ($branchId == '12') {
                        $q->orWhere('location', 'LIKE', '%សៀមរាប%')->orWhere('location', 'LIKE', '%Siem%');
                    } elseif ($branchId == '10') {
                        $q->orWhere('location', 'LIKE', '%បាត់ដំបង%')->orWhere('location', 'LIKE', '%Battambang%');
                    }
                });
            }

            // 🌟 រាប់ចំនួនការងារដែលមិនទាន់ដោះស្រាយទាំងអស់
            $totalCount = (clone $query)->count();

            // ទាញយកតែ ១០ ថ្មីៗបំផុតមកបង្ហាញក្នុងកន្ត្រក Dropdown
            $notifications = $query->orderBy('id', 'desc')->limit(10)->get();

            $data = $notifications->map(function ($book) {
                return [
                    'id' => $book->id,
                    'customer' => $book->customer_name ?? 'គ្មានឈ្មោះ',
                    'plate' => $book->plate ?? 'គ្មានស្លាកលេខ',
                    'time' => $book->created_at ? $book->created_at->diffForHumans() : 'ថ្មីៗនេះ',
                ];
            });

            return response()->json([
                'count' => $totalCount,
                'data' => $data,
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'count' => 0,
                'data' => [],
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function executeTelegramCurl($text, $locationData = null)
    {
        $chat_id = (string) env('TELEGRAM_CHAT_ID_DEFAULT', '');
        
        if (! empty($locationData)) {
            $loc = strtolower(trim((string) $locationData));
            if ($loc === '13' || strpos($loc, 'hq') !== false || strpos($loc, 'head') !== false) {
                $chat_id = (string) env('TELEGRAM_CHAT_ID_HQ', $chat_id);
            } elseif ($loc === '11' || strpos($loc, '50m') !== false || strpos($loc, '50') !== false) {
                $chat_id = (string) env('TELEGRAM_CHAT_ID_50M', $chat_id);
            } elseif ($loc === '12' || strpos($loc, 'sr') !== false || strpos($loc, 'siem') !== false) {
                $chat_id = (string) env('TELEGRAM_CHAT_ID_SR', $chat_id);
            } elseif ($loc === '10' || strpos($loc, 'btb') !== false || strpos($loc, 'battambang') !== false) {
                $chat_id = (string) env('TELEGRAM_CHAT_ID_BTB', $chat_id);
            }
        }
    
        // បើរកមិនឃើញ Chat ID ណាសោះ ឱ្យវា return ឈប់ដំណើរការដើម្បីកុំឱ្យ Error
        if ($chat_id === '') {
            \Log::warning('Telegram Chat ID is missing for location: ' . $locationData);
            return;
        }
    
        $token = env('TELEGRAM_BOT_TOKEN');
        if (empty($token)) {
            \Log::error('Telegram Bot Token is missing in .env');
            return;
        }
    
        $url = "https://api.telegram.org/bot{$token}/sendMessage";
        $data = [
            'chat_id' => $chat_id, 
            'text' => $text, 
            'parse_mode' => 'HTML', 
            'disable_web_page_preview' => true
        ];
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch) || $httpCode !== 200) {
            \Log::error('Telegram Send Error: ' . curl_error($ch) . ' | HTTP Code: ' . $httpCode);
        }
        
        curl_close($ch);
    }

    private function getDisplayBranch($location)
    {
        if (is_numeric($location)) {
            $branchName = DB::table('branches')->where('id', $location)->value('branch_name');

            return $branchName ? str_ireplace('MG ', '', $branchName) : $location;
        }

        return str_ireplace('MG ', '', $location ?? 'N/A');
    }

    private function sendNewBookingAlert($booking, $sourceText = null)
    {
        $displayBranch = $this->getDisplayBranch($booking->location);
        $date = date('d-M-Y', strtotime($booking->booking_date));
        $time = date('h:i A', strtotime($booking->booking_time));
        $mileage = ! empty($booking->mileage) ? number_format((float) $booking->mileage).' KM' : 'N/A';
        $remark = ! empty($booking->remark) ? htmlspecialchars($booking->remark) : 'គ្មាន / None';

        $addedByText = $sourceText ? $sourceText."\n\n" : "🌐 <b>ប្រភព (Source):</b> Online Booking\n\n";

        $text = "🔔 <b>NEW BOOKING | MG</b>\n━━━━━━━━━━━━━━━━━━\n"
              .(! empty($booking->id) ? '🆔 <b>លេខយោង:</b> #MG-'.str_pad($booking->id, 4, '0', STR_PAD_LEFT)."\n" : '')
              .$addedByText
              ."👤 <b>អតិថិជន (Customer Info):</b>\n"
              ."├ ឈ្មោះ: <b>{$booking->customer_name}</b>\n"
              ."└ ទូរស័ព្ទ: <code>{$booking->contact}</code>\n\n"
              ."🚘 <b>រថយន្ត (Vehicle Info):</b>\n"
              ."├ ម៉ូឌែល: <b>{$booking->model}</b>\n"
              ."├ ស្លាកលេខ: <code>{$booking->plate}</code>\n"
              ."└ គីឡូម៉ែត្រ: {$mileage}\n\n"
              ."🛠️ <b>សេវាកម្ម (Service Details):</b>\n"
              ."├ ប្រភេទ: <a href=''>{$booking->service_type}</a>\n"
              ."├ សាខា: <b>{$displayBranch}</b>\n"
              ."├ ថ្ងៃណាត់: <b>{$date}</b>\n"
              ."└ ម៉ោង: <b>{$time}</b>\n\n"
              ."💬 <b>សំណូមពរអតិថិជន (Remark):</b>\n<i>{$remark}</i>\n"
              ."━━━━━━━━━━━━━━━━━━\n"
              ."👉 <a href='".url('/workshop/board')."'>ចុចទីនេះដើម្បីចូលទៅកាន់ប្រព័ន្ធ</a>";

        $this->executeTelegramCurl($text, $booking->branch_id ?? $booking->location);
    }

    private function sendTelegramStatusAlert($booking)
    {
        $durationText = 'មិនទាន់កំណត់';
        if (! empty($booking->start_time) && ! empty($booking->end_time)) {
            try {
                $start = Carbon::parse($booking->start_time);
                $end = Carbon::parse($booking->end_time);
                $diff = abs($end->getTimestamp() - $start->getTimestamp());
                $sec = $diff % 43200;
                if ($sec > 21600) {
                    $sec = 43200 - $sec;
                } $hours = floor($sec / 3600);
                $minutes = floor(($sec % 3600) / 60);
                $durationText = ($hours > 0) ? "{$hours} ម៉ោង {$minutes} នាទី" : "{$minutes} នាទី";
            } catch (Exception $e) {
            }
        }
        $displayBranch = $this->getDisplayBranch($booking->location);
        $bookingId = str_pad($booking->id ?? 0, 4, '0', STR_PAD_LEFT);
        $internalNote = ! empty($booking->note) ? $booking->note : 'ធម្មតា / Normal';
        $statusText = strtoupper($booking->status);
        if (in_array($statusText, ['COMPLETED', 'CLOSE'])) {
            $headerTitle = '✅ JOB COMPLETED | ជួសជុលរួចរាល់';
        } elseif (in_array($statusText, ['IN PROGRESS', 'WIP'])) {
            $headerTitle = '⚙️ IN PROGRESS | កំពុងជួសជុល';
        } elseif ($statusText == 'HOLD') {
            $headerTitle = '⏸️ JOB ON HOLD | ផ្អាកការងារបណ្តោះអាសន្ន';
        } elseif ($statusText == 'ARRIVED') {
            $headerTitle = '📍 CUSTOMER ARRIVED | អតិថិជនមកដល់';
        } else {
            $headerTitle = '🔄 STATUS UPDATE | ផ្លាស់ប្តូរស្ថានភាព';
        }
        $text = "{$headerTitle}\n━━━━━━━━━━━━━━━━━━\n🆔 លេខយោង: #MG-{$bookingId}\n📊 ស្ថានភាព: <b>{$booking->status}</b>\n\n🚘 ព័ត៌មានរថយន្ត (Vehicle Info):\n├ អតិថិជន: {$booking->customer_name}\n├ ស្លាកលេខ: <code>{$booking->plate}</code>\n├ ម៉ូឌែល: {$booking->model}\n└ គីឡូម៉ែត្រ: ".number_format((float) $booking->mileage)." KM\n\n🛠️ សេវាកម្ម (Service Details):\n├ ប្រភេទ: {$booking->service_type}\n├ SA: ".($booking->sa_name ?? 'មិនទាន់មាន')."\n├ មេជាង: ".($booking->technician_name ?? 'មិនទាន់មាន')."\n└ សាខា: {$displayBranch}\n\n";
        if (! empty($booking->start_time) || ! empty($booking->end_time)) {
            $text .= "⏱️ ពេលវេលាជួសជុល (Timing):\n";
            if (! empty($booking->start_time)) {
                $text .= '├ ចាប់ផ្តើម: '.date('h:i A', strtotime($booking->start_time))."\n";
            } if (! empty($booking->end_time)) {
                $text .= '├ បញ្ចប់: '.date('h:i A', strtotime($booking->end_time))."\n";
            } if ($statusText == 'COMPLETED') {
                $text .= "└ ប្រើពេល: <code>{$durationText}</code>\n\n";
            } else {
                $text .= "\n";
            }
        }
        $text .= "📝 កំណត់ចំណាំ (Internal Note):\n<i>{$internalNote}</i>\n━━━━━━━━━━━━━━━━━━\n👉 <a href='".url('/services')."'>ចូលទៅប្រព័ន្ធ Service Index</a>";
        $this->executeTelegramCurl($text, $booking->branch_id ?? $booking->location);
    }
    public function getHistory(Request $request)
    {
        try {
            $user = $request->user(); 
            
            if (!$user) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }
            
            $branchId = $request->input('branch_id') ?? $request->input('location_id'); 
            $query = \App\Models\ServiceBooking::query();
        
            // ==========================================
            // ១. លក្ខខណ្ឌបែងចែកសិទ្ធិ
            // ==========================================
            if ((int)$user->role_id === 19) {
                // 🔒 អតិថិជន: រកទិន្នន័យដែលត្រូវនឹងលេខទូរស័ព្ទរបស់គាត់
                $phoneFromUser = trim($user->phone ?? ''); 
                
                if (empty($phoneFromUser)) {
                    $query->whereRaw('1 = 0'); 
                } else {
                    // លុបដកឃ្លាដើម្បីប្រៀបធៀបលេខ
                    $query->where(function($q) use ($phoneFromUser) {
                        $q->whereRaw("REPLACE(contact, ' ', '') LIKE ?", ['%' . str_replace(' ', '', $phoneFromUser) . '%']);
                    });
                }
            } else {
                // 💼 បុគ្គលិក/Admin: រកតាមសាខា
                $adminRoles = [1, 2, 3, 6, 10, 11, 12, 14, 15, 16]; 
                
                if (in_array((int)$user->role_id, $adminRoles)) {
                    if (!empty($branchId) && $branchId !== 'all') {
                        $query->where('branch_id', $branchId);
                    }
                } else {
                    $query->where('branch_id', $user->branch_id);
                }
            }

            // ==========================================
            // ២. លក្ខខណ្ឌស្វែងរក (Search)
            // ==========================================
            $search = trim($request->input('q') ?? '');
            if (!empty($search)) {
                $searchClean = str_replace(' ', '', $search);
                $query->where(function ($q) use ($search, $searchClean) {
                    $q->where('plate', 'LIKE', "%{$search}%")
                      ->orWhereRaw("REPLACE(contact, ' ', '') LIKE ?", ['%' . $searchClean . '%'])
                      ->orWhere('customer_name', 'LIKE', "%{$search}%")
                      ->orWhere('model', 'LIKE', "%{$search}%");
                });
            }

            // ==========================================
            // ៣. ទាញទិន្នន័យ
            // ==========================================
            $bookings = $query->orderBy('id', 'desc')->get();

            $formattedData = $bookings->map(function ($book) {
                return [
                    'id' => str_pad($book->id, 4, '0', STR_PAD_LEFT),
                    'date' => $book->booking_date ? date('d-M-Y', strtotime($book->booking_date)) : 'N/A',
                    'time' => $book->booking_time ? date('h:i A', strtotime($book->booking_time)) : 'N/A',
                    'service' => $book->service_type ?? 'N/A',
                    'model' => $book->model ?? 'N/A', 
                    'plate' => $book->plate ?? 'N/A',
                    'mileage' => number_format((float)($book->mileage ?? 0)) . ' KM',
                    'status' => $book->status ?? 'Pending',
                    'note' => $book->note ?? '',
                    'customer' => $book->customer_name ?? 'N/A',
                    'phone' => $book->contact ?? 'N/A',
                    'branch' => $book->location ?? 'N/A',
                    'branch_id' => $book->branch_id, 
                    'technician' => $book->technician_name ?? 'N/A',
                    'advisor' => $book->sa_name ?? 'N/A',
                    
                    // 🌟 កន្លែងដែលខ្វះខាត គឺបានបន្ថែមរួចរាល់ 🌟
                    'remark' => $book->remark ?? '', 
                ];
            });

            return response()->json($formattedData);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Backend Error: ' . $e->getMessage()
            ], 500);
        }
    }
    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'status' => 'required|in:Pending,Arrived,Completed',
        ]);
    
        try {
            // 🌟 ថែមពាក្យ (int) នៅទីនេះ ដើម្បីកាត់លេខសូន្យពីមុខចេញ
            $booking = ServiceBooking::find((int) $request->id);
    
            if (!$booking) {
                return response()->json(['message' => 'Booking not found'], 404);
            }
    
            $booking->status = $request->status;
            $booking->save();
    
            return response()->json([
                'message' => 'Status updated successfully',
                'status' => $booking->status
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json(['message' => 'Server Error', 'error' => $e->getMessage()], 500);
        }
    }

  // Function ទទួលការវាយតម្លៃពី Flutter
  public function updateRemark(Request $request)
  {
      // ១. ផ្ទៀងផ្ទាត់ទិន្នន័យ
      $request->validate([
          'booking_id' => 'required',
          'remark' => 'required|string'
      ]);

      // ២. ស្វែងរកការកក់នោះតាមរយៈ ID (ប្តូរឈ្មោះ Model ServiceBooking តាមការជាក់ស្តែងរបស់បង)
      // ឧទាហរណ៍៖ \App\Models\ServiceBooking::find(...) ឬ \App\Models\Booking::find(...)
      $booking = \App\Models\ServiceBooking::find($request->booking_id);

      if (!$booking) {
          return response()->json([
              'success' => false,
              'message' => 'រកមិនឃើញប្រវត្តិការកក់នេះទេ!'
          ], 404);
      }

      // ៣. Update ទិន្នន័យ Remark
      $booking->remark = $request->remark;
      $booking->save();

      // ៤. បោះសារជោគជ័យទៅឱ្យ Flutter វិញ
      return response()->json([
          'success' => true,
          'message' => 'ការវាយតម្លៃជោគជ័យ'
      ], 200);
  }
}
