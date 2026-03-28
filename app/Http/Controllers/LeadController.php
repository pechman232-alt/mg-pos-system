<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;
use Carbon\Carbon;

class LeadController extends Controller
{
    private $botToken = "8652350134:AAH--f2l4hp5zxcPIrjYtG7kVaY4JUhQvOI";
    private $chatId = "-1003720998244";

    /**
     * ១. បង្ហាញបញ្ជី Leads ទាំងអស់
     */
    public function index() {
        $leads = Lead::with(['lastActivity.user'])->orderBy('updated_at', 'desc')->get();
        return view('Leads.lead_index', compact('leads'));
    }

    /**
     * ២. ទំព័របង្កើត Lead ថ្មី
     */
    public function create() {
        $staff = ['Pum Sorya', 'Sanh Rady', 'Sem SereyYanuth', 'Thul Ratha', 'Poeng Lyly', 'Oum Kim', 'Bun Narith', 'Say Toeun', 'Sib KimCheu', 'Liam Sophuon', 'Sem Sonita', 'Heang Dara', 'Sin Chanraksmey', 'Kuch Piseth', 'Heng Sorphea', 'Loem Koemheng', 'San Nimol', 'Sok Phen', 'Mek Samnob', 'Seng Sophanha', 'Sal Sophea'];
        sort($staff);
        return view('Leads.add_lead', compact('staff'));
    }

    /**
     * ៣. រក្សាទុក Lead ថ្មី និងផ្ញើ Telegram ដំបូង
     */
    public function store(Request $request) {
        $request->validate(['customer_name' => 'required', 'contact_info' => 'required', 'sales_person' => 'required', 'model' => 'required']);
        try {
            DB::beginTransaction();
            $phone = str_replace(' ', '', $request->contact_info);
            if (Lead::where('contact_info', $phone)->exists()) { throw new Exception("លេខទូរស័ព្ទនេះមានក្នុងប្រព័ន្ធរួចហើយ!"); }

            $lead = Lead::create(array_merge($request->all(), ['contact_info' => $phone, 'status' => $request->status ?? 'Interest']));
            $lead->activities()->create([
                'user_id' => auth('users')->id(), 
                'status' => $lead->status, 
                'comment' => $request->comment ?? 'បញ្ចូលអតិថិជនថ្មី'
            ]);
            DB::commit();

            $this->sendToTelegram($lead);
            return redirect()->route('leads.index')->with('success', 'Lead record authorized successfully!');
        } catch (Exception $e) { DB::rollBack(); return back()->withInput()->with('error', $e->getMessage()); }
    }

    /**
     * ៤. មុខងារ Sync Status (AJAX) និងផ្ញើ Journey Logs ទាំងអស់
     */
    public function quickUpdate(Request $request) {
        try {
            DB::beginTransaction();
            $lead = Lead::findOrFail($request->id);
            
            // Lock ប្រសិនបើបិទការលក់ហើយ
            if (in_array(strtolower($lead->status), ['delivery', 'lost', 'won'])) {
                return response()->json(['success' => false, 'message' => 'Record is finalized and locked.'], 403);
            }

            $lead->update(['status' => $request->status]);
            $lead->activities()->create([
                'user_id' => auth('users')->id(), 
                'status' => $request->status, 
                'comment' => $request->comment
            ]);
            DB::commit();

            $this->sendToTelegram($lead);
            return response()->json(['success' => true]);
        } catch (Exception $e) { DB::rollBack(); return response()->json(['success' => false, 'message' => $e->getMessage()], 500); }
    }

    /**
     * 🛰️ ៥. មុខងារផ្ញើសារទៅកាន់ Telegram (Premium Operational Intelligence)
     */
    private function sendToTelegram($lead) {
        // 🔄 ១. រៀបចំ Logs ពីថ្មីទៅចាស់ (DESC)៖ លើកទី៤ ឬអាថ្មីបំផុត នឹងឡើងមកលេខ ១
        $activities = $lead->activities()->with('user')->orderBy('created_at', 'desc')->get();
        $status = strtolower($lead->status);
    
        // 🎯 ២. Alert តែលើ Interest, Book, និង Delivery (ការពារ Spam)
        $alertStages = ['interest', 'book', 'delivery', 'won'];
        if (!in_array($status, $alertStages)) {
            return; 
        }
    
        // 🎨 ៣. ស្ទីល Status (Bilingual)
        $style = match($status) {
            'interest' => ['icon' => '💡', 'en' => 'NEW INTEREST', 'kh' => 'ចាប់អារម្មណ៍ថ្មី'],
            'book'     => ['icon' => '💰', 'en' => 'BOOKING CONFIRMED', 'kh' => 'កក់ប្រាក់រួចរាល់'],
            'delivery' => ['icon' => '🏆', 'en' => 'DELIVERY SUCCESS', 'kh' => 'ប្រគល់ឡានជោគជ័យ'],
            default    => ['icon' => '🔄', 'en' => strtoupper($lead->status), 'kh' => $lead->status],
        };
    
        // 📝 ៤. គ្រោងសារ
        $msg = "{$style['icon']} *PIPELINE NOTIFICATION*\n";
        $msg .= "━━━━━━━━━━━━━━━━━━━━\n";
        $msg .= "🕹 *STATUS:* `{$style['en']}` | {$style['kh']}\n";
        $msg .= "👤 *CLIENT:* `{$lead->customer_name}`\n";
        $msg .= "🚗 *MODEL:* `{$lead->model}`\n";
    
        // 📜 ៥. រៀបចំ History Logs (អាថ្មីបំផុតនៅលេខ ១)
        $logText = "";
        $totalLogs = $activities->count();
        
        foreach ($activities as $index => $act) {
            // បញ្ច្រាសលេខរៀង៖ បើមាន ៤ អាថ្មីបំផុតគឺលេខ ១
            $num = $index + 1;
            
            // Fix Syntax Error ?? (ប្រើ Ternary ជំនួស)
            $editorName = ($act->user && isset($act->user->full_name)) ? $act->user->full_name : 'Staff';
            
            // ម៉ោង AM/PM ជាក់ស្ដែង (Phnom Penh Time)
            $time = $act->created_at->format('d/M h:i A');
            
            $logText .= "{$num}. [{$act->status}] By: {$editorName}\n";
            $logText .= "   └─ {$act->comment} ({$time})\n";
        }
    
        $msg .= "\n📜 *LATEST JOURNEY LOGS:*\n```\n{$logText}```\n";
    
        $msg .= "━━━━━━━━━━━━━━━━━━━━\n";
        // បង្ហាញ Lead Owner (Sale)
        $msg .= "👨‍💼 *LEAD OWNER:* {$lead->sales_person}\n"; 
        $msg .= "⏰ *UPDATED:* " . now()->format('d-M-Y h:i A');
    
        // 🔗 ៦. ប៊ូតុង Link ទៅកាន់ System
        $viewUrl = url("/leads/" . \Illuminate\Support\Facades\Crypt::encryptString($lead->id));
        $keyboard = ['inline_keyboard' => [[['text' => '🔍 VIEW IN SYSTEM', 'url' => $viewUrl]]]];
    
        $this->callTelegramApi($msg, $keyboard);
    }
    public function checkDuplicatePhone(Request $request)
    {
        // លុបចន្លោះចេញមុននឹងឆែក
        $phone = str_replace(' ', '', $request->phone);
        
        // ស្វែងរកលេខទូរស័ព្ទក្នុង Database
        $lead = \App\Models\Lead::where('contact_info', $phone)->first();
    
        if ($lead) {
            return response()->json([
                'duplicate' => true,
                'customer_name' => $lead->customer_name,
                'sales_person' => $lead->sales_person
            ]);
        }
    
        return response()->json(['duplicate' => false]);
    }
    /**
     * 📊 ៦. មុខងារ Daily Summary Report
     */
    public function sendDailySummary() {
        $today = Carbon::today();
        $totalNew = Lead::whereDate('created_at', $today)->count();
        $totalWon = Lead::whereIn('status', ['delivery', 'won'])->whereDate('updated_at', $today)->count();
        $totalLost = Lead::where('status', 'lost')->whereDate('updated_at', $today)->count();
        
        $summary = "📊 *DAILY SALES SUMMARY* (" . $today->format('d-M-Y') . ")\n";
        $summary .= "━━━━━━━━━━━━━━━━━━━━\n";
        $summary .= "🆕 Lead ថ្មីបញ្ចូលថ្ងៃនេះ: *{$totalNew}*\n";
        $summary .= "✅ បិទការលក់បាន (Delivery): *{$totalWon}* 🏆\n";
        $summary .= "❌ បោះបង់ (Lost): *{$totalLost}*\n";
        $summary .= "━━━━━━━━━━━━━━━━━━━━\n";
        $summary .= "🚀 *Keep up the good work, MG Team!*";

        return $this->callTelegramApi($summary);
    }

    /**
     * 📁 ៧. មុខងារ Export Data ជា CSV (Excel Support)
     */
    public function exportLeads() {
        $fileName = 'leads_report_' . date('Y-m-d') . '.csv';
        $leads = Lead::all();
        $headers = ["Content-type" => "text/csv; charset=UTF-8", "Content-Disposition" => "attachment; filename=$fileName", "Pragma" => "no-cache", "Cache-Control" => "must-revalidate, post-check=0, pre-check=0", "Expires" => "0"];
        $columns = ['Date', 'Customer', 'Phone', 'Model', 'Sales Person', 'Status', 'Branch', 'Location'];

        $callback = function() use($leads, $columns) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // BOM for Excel Khmer
            fputcsv($file, $columns);
            foreach ($leads as $l) {
                fputcsv($file, [$l->date, $l->customer_name, $l->contact_info, $l->model, $l->sales_person, $l->status, $l->branch, $l->location]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    private function callTelegramApi($text) {
        $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['chat_id' => $this->chatId, 'text' => $text, 'parse_mode' => 'Markdown']));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }

    public function show($id) {
        $decryptedId = is_numeric($id) ? $id : Crypt::decryptString($id);
        $lead = Lead::with(['activities.user'])->findOrFail($decryptedId);
        return view('Leads.view_lead', compact('lead'));
    }
}