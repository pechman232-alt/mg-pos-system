<?php

namespace App\Http\Controllers;

use App\Exports\LeadsExport;
use App\Models\BranchModel;
use App\Models\Lead;
use App\Models\ManagerInstruction;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\FirebaseService; 

class LeadController extends Controller
{
    // --- 🤖 Telegram Bot Configuration (Leads Bot & Group) ---
    private $botToken = '8652350134:AAH--f2l4hp5zxcPIrjYtG7kVaY4JUhQvOI';
    private $chatId = '-1003720998244';

    private function applyRoleScope($query, $customUser = null)
    {
        $user = $customUser ?? auth('users')->user();
        if (! $user) {
            return $query->whereRaw('1=0');
        }

        $roleId = (int) $user->role_id;

        // 1. Admin, Director, GM (ឃើញទាំងអស់)
        if (in_array($roleId, [1, 2, 6])) {
            return $query;
        }

        // 2. Branch Managers (ប្រើ Branch ID ឬ Name)
        if (in_array($roleId, [3, 4, 5, 7, 8])) {
            return $query->where(function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id)
                    ->orWhere('branch_name', $user->branch_name);
            });
        }

        // 3. Sale Consultant (ប្រើ User ID ឬឈ្មោះ)
        return $query->where(function ($q) use ($user) {
            $q->where('sales_person_id', $user->id)
                ->orWhere('sales_person', $user->full_name);
        });
    }

    public function index(Request $request)
    {
        $user = auth('users')->user();
        if (! $user) {
            return redirect()->route('login');
        }

        $isAdminOrDirector = in_array((int) $user->role_id, [1, 2, 6]);
        $showArchived = $request->has('view_archive') && $isAdminOrDirector;

        $query = Lead::query();

        if ($showArchived) {
            $query->where('is_archived', 1);
        } else {
            $query->where(function ($q) {
                $q->whereNull('is_archived')->orWhere('is_archived', 0);
            });
        }

        $query = $this->applyRoleScope($query);

        $alertLeads = (clone $query)->whereHas('managerInstructions')
            ->where('is_archived', 0)
            ->with(['managerInstructions' => fn ($q) => $q->latest()])
            ->get();

        $leads = $query->orderBy('created_at', 'desc')->get();

        $stats = [
            'total' => $leads->count(),
            'booking' => $leads->filter(fn ($l) => preg_match('/book|appointment/i', $l->status))->count(),
            'delivery' => $leads->filter(fn ($l) => stripos($l->status, 'delivery') !== false)->count(),
            'lost' => $leads->filter(fn ($l) => stripos($l->status, 'lost') !== false)->count(),
            'totalArchived' => $this->applyRoleScope(Lead::where('is_archived', 1))->count(),
            'no_action' => $leads->filter(function ($l) {
                $st = strtolower($l->status);
                if (in_array($st, ['delivery', 'lost', 'won', 'sales ( contract of sales )'])) {
                    return false;
                }

                return empty($l->remind_date) || (Carbon::parse($l->remind_date)->isPast() && ! Carbon::parse($l->remind_date)->isToday());
            })->count(),
        ];

        $staff = User::where('status', 1)->pluck('full_name')->toArray();

        return view('Leads.lead_index', array_merge($stats, compact('leads', 'staff', 'showArchived', 'alertLeads')));
    }

    public function dashboard(Request $request)
    {
        $query = Lead::where(function ($q) {
            $q->whereNull('is_archived')->orWhere('is_archived', 0);
        });
        $query = $this->applyRoleScope($query);

        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        $data = [
            'provinceStats' => (clone $query)->select('location', DB::raw('count(*) as total'))->whereNotNull('location')->groupBy('location')->orderBy('total', 'desc')->get(),
            'modelStats' => (clone $query)->select('model', DB::raw('count(*) as total'))->whereNotNull('model')->groupBy('model')->orderBy('total', 'desc')->get(),
            'salesStats' => (clone $query)->select('sales_person', DB::raw('count(*) as total'))->whereNotNull('sales_person')->groupBy('sales_person')->orderBy('total', 'desc')->take(15)->get(),
            'sourceStats' => (clone $query)->select('source', DB::raw('count(*) as total'))->whereNotNull('source')->groupBy('source')->orderBy('total', 'desc')->get(),
            'soldModelStats' => (clone $query)->select('model', DB::raw('count(*) as total'))->whereIn('status', ['Sales ( contract of sales )', 'Delivery'])->groupBy('model')->orderBy('total', 'desc')->get(),
        ];

        $weights = ['Prospecting' => 8, 'Appointment' => 16, 'Meet & Greet' => 24, 'Consulting ( Q & A )' => 32, 'Double Open' => 40, 'Presentation' => 52, 'Test Drive' => 64, 'Trial Close' => 74, 'Negotiation' => 84, 'Double Close' => 90, 'Sales ( contract of sales )' => 96, 'Delivery' => 100, 'Lost' => 0];

        $data['salesActivityProgress'] = (clone $query)
            ->select('sales_person as name', DB::raw('count(*) as total_leads'))
            ->groupBy('sales_person')->get()
            ->map(function ($item) use ($weights) {
                $statuses = Lead::where('sales_person', $item->name)->where(fn ($q) => $q->whereNull('is_archived')->orWhere('is_archived', 0))->pluck('status');
                $sum = 0;
                foreach ($statuses as $s) {
                    $sum += $weights[$s] ?? 0;
                }
                $item->avg_percentage = $item->total_leads > 0 ? round($sum / $item->total_leads) : 0;

                return $item;
            })->sortByDesc('avg_percentage');

        return view('layouts.dashboard', $data);
    }

    public function create()
    {
        $user = auth('users')->user();

        return view('Leads.add_lead', [
            'staff' => User::whereNotNull('full_name')->orderBy('full_name')->pluck('full_name')->toArray(),
            'user' => $user,
            'myBranch' => BranchModel::where('id', $user->branch_id)->value('branch_name') ?? 'N/A',
            'allBranches' => BranchModel::pluck('branch_name')->toArray(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required',
            'contact_info' => 'required',
            'sales_person' => 'required',
            'model' => 'required',
            'branch_name' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->except(['model']);
            $data['model'] = is_array($request->model) ? implode(', ', $request->model) : $request->model;

            $phone = str_replace([' ', '-'], '', $request->contact_info);
            $data['contact_info'] = $phone;
            $data['is_archived'] = 0;
            $data['status'] = $request->status ?? 'Prospecting';

            $lead = Lead::create($data);
            $lead->activities()->create([
                'user_id' => auth('users')->id() ?? ($request->user() ? $request->user()->id : 1),
                'status' => $lead->status,
                'comment' => $request->comment ?? 'បញ្ចូលអតិថិជនថ្មី',
            ]);

            DB::commit();

            try {
                \Log::info('--- ចាប់ផ្តើមដំណើរការផ្ញើ Notification ---');
                \Log::info('ឈ្មោះ Sales បោះមកពី App គឺ៖ ' . $request->sales_person);

                $salesStaff = \App\Models\User::where('full_name', $request->sales_person)->first();
                
                if (!$salesStaff) {
                    \Log::warning('⚠️ បញ្ហាទី១៖ រកមិនឃើញបុគ្គលិកឈ្មោះ: ' . $request->sales_person . ' នៅក្នុង Table Users ទេ!');
                } else {
                    \Log::info('✅ រកឃើញបុគ្គលិក: ' . $salesStaff->full_name);
                    
                    if (empty($salesStaff->fcm_token)) {
                        \Log::warning('⚠️ បញ្ហាទី២៖ បុគ្គលិកនេះរកឃើញ តែគាត់អត់មាន fcm_token ទេ!');
                    } else {
                        \Log::info('✅ ត្រៀមបាញ់ទៅ Token: ' . $salesStaff->fcm_token);
                        $firebase = new \App\Services\FirebaseService();
                        $firebase->sendNotification(
                            $salesStaff->fcm_token,
                            'មានអតិថិជនថ្មី (Lead)! 🚗',
                            'អតិថិជន៖ ' . $lead->customer_name . ' បានចាប់អារម្មណ៍លើម៉ូដែល ' . $lead->model
                        );
                        \Log::info('✅ ផ្ញើទៅ Firebase រួចរាល់ដោយជោគជ័យ!');
                    }
                }
            } catch (\Exception $e) {
                \Log::error('❌ Firebase Error: ' . $e->getMessage());
            }

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['success' => true, 'message' => 'រក្សាទុកជោគជ័យ']);
            }
            
            return redirect()->route('leads.index')->with('success', 'រក្សាទុកជោគជ័យ!');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['success' => false, 'message' => 'Error: '.$e->getMessage()], 500);
            }

            return back()->withInput()->with('error', 'Error: '.$e->getMessage());
        }
    }

    public function show($id)
    {
        $decId = is_numeric($id) ? $id : Crypt::decryptString($id);
        $lead = Lead::with(['activities.user', 'managerInstructions.user'])->findOrFail($decId);

        return view('Leads.view_lead', compact('lead'));
    }

    public function edit($id)
    {
        $decId = is_numeric($id) ? $id : Crypt::decryptString($id);
        $lead = Lead::findOrFail($decId);
        $staff = User::whereNotNull('full_name')->orderBy('full_name')->pluck('full_name')->toArray();

        return view('Leads.edit_lead', compact('lead', 'staff'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'customer_name' => 'required',
            'contact_info' => 'required|unique:leads,contact_info,'.$id,
        ]);

        $lead = Lead::findOrFail($id);
        $data = $request->all();

        if ($request->has('model')) {
            $data['model'] = is_array($request->model) ? implode(', ', $request->model) : $request->model;
        }

        $lead->update($data);

        return redirect()->route('leads.index')->with('success', 'Master record updated!');
    }

    public function quickUpdate(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:leads,id',
            'comment' => 'required|string|min:5',
        ]);

        try {
            DB::beginTransaction();

            $lead = Lead::findOrFail($request->id);

            if ($request->remind_date) {
                $lead->remind_date = $request->remind_date;
            }
            if ($request->status != 'current') {
                $lead->status = $request->status;
            }
            $lead->save();

            $lead->activities()->create([
                'user_id' => auth('users')->id(),
                'status' => $lead->status,
                'comment' => $request->comment,
            ]);

            DB::commit();

            return response()->json(['success' => true]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Quick Update Error: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function transfer(Request $request)
    {
        if (!$request->has('new_branch_id') || $request->new_branch_id == null) {
            return response()->json(['success' => false, 'message' => '❌ បញ្ហា: App មិនបានបញ្ជូនលេខ new_branch_id មក Server ទេ!']);
        }

        try {
            DB::beginTransaction();
            $lead = Lead::findOrFail($request->id);
            
            $oldBranch = $lead->branch_name ?? 'None';
            $oldSale = $lead->sales_person ?? 'None';

            $lead->branch_id = $request->new_branch_id; 
            $lead->branch_name = $request->new_branch;
            $lead->sales_person = $request->new_sale;
            $lead->is_transferred = true;
            $lead->save();

            $lead->activities()->create([
                'user_id' => auth('sanctum')->id(), 
                'status' => $lead->status,
                'comment' => "🔄 ផ្ទេរពី {$oldBranch} ({$oldSale}) ទៅ {$request->new_branch} ({$request->new_sale})។ មូលហេតុ៖ {$request->comment}"
            ]);

            DB::commit();

            // 🌟 1. ផ្ញើទៅ Telegram
            $tgMsg = "🔄 *LEAD TRANSFERRED*\n━━━━━━━━━━━━━━━━━━━━\n👤 *CLIENT:* `{$lead->customer_name}`\n🚗 *MODEL:* `{$lead->model}`\n━━━━━━━━━━━━━━━━━━━━\n⬅️ *FROM:* {$oldBranch} ({$oldSale})\n➡️ *TO:* {$request->new_branch} ({$request->new_sale})\n📝 *REASON:* {$request->comment}";
            
            try {
                $this->callTelegramApi($tgMsg);
            } catch (\Exception $e) {
                \Log::error('Telegram Error: ' . $e->getMessage());
            }

            // 🌟 2. ផ្ញើ Notification ទៅកាន់ Staff ដែលទទួល Lead ថ្មី (Firebase)
            try {
                // រកមើល User ដែលត្រូវទទួល Lead (ផ្អែកលើឈ្មោះ new_sale)
                $targetStaff = \App\Models\User::where('full_name', $request->input('new_sale'))->first();
                
                if ($targetStaff && !empty($targetStaff->fcm_token)) {
                    $firebase = new \App\Services\FirebaseService();
                    $firebase->sendNotification(
                        $targetStaff->fcm_token,
                        'មាន Lead ថ្មីត្រូវទទួល! 🔄',
                        'អតិថិជន៖ ' . $lead->customer_name . ' ត្រូវបានផ្ទេរមកជូនលោក/អ្នក។'
                    );
                }
            } catch (\Exception $e) {
                \Log::error('Firebase Error (Transfer): ' . $e->getMessage());
            }

            return response()->json(['success' => true, 'message' => 'ផ្ទេរការងារបានជោគជ័យ!']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function reschedule(Request $request)
    {
        try {
            DB::beginTransaction();
            $lead = Lead::findOrFail($request->id);
            $oldDate = $lead->remind_date ? \Carbon\Carbon::parse($lead->remind_date)->format('d-M-Y') : 'None';
            $newDate = \Carbon\Carbon::parse($request->new_date)->format('d-M-Y');

            $lead->update(['remind_date' => $request->new_date]);
            
            $lead->activities()->create([
                'user_id' => auth('sanctum')->id(), 
                'status' => $lead->status, 
                'comment' => "🗓️ ប្តូរថ្ងៃណាត់ពី {$oldDate} ទៅ {$newDate}។ មូលហេតុ៖ {$request->comment}"
            ]);
            
            DB::commit();

            $tgMsg = "🗓️ *LEAD RESCHEDULED*\n━━━━━━━━━━━━━━━━━━━━\n👤 *CLIENT:* `{$lead->customer_name}`\n🚗 *MODEL:* `{$lead->model}`\n👨‍💼 *SALES:* {$lead->sales_person}\n━━━━━━━━━━━━━━━━━━━━\n⬅️ *OLD DATE:* {$oldDate}\n➡️ *NEW DATE:* {$newDate}\n📝 *REASON:* {$request->comment}";
            
            try {
                $this->callTelegramApi($tgMsg);
            } catch (\Exception $e) {
                // អត់មានបញ្ហាទេ
            }

            return response()->json(['success' => true, 'message' => 'ប្តូរថ្ងៃណាត់បានជោគជ័យ']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateManagerComment(Request $request)
    {
        try {
            ManagerInstruction::create([
                'lead_id' => $request->id,
                'user_id' => auth('users')->id(),
                'instruction' => $request->manager_comment,
            ]);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function archive($id)
    {
        $lead = Lead::findOrFail($id);
        $lead->update(['is_archived' => 1]);
        $lead->activities()->create(['user_id' => auth('users')->id(), 'status' => $lead->status, 'comment' => '🔒 Moved to Archive']);

        return back()->with('success', 'Lead archived.');
    }

    public function restore($id)
    {
        $lead = Lead::findOrFail($id);
        $lead->update(['is_archived' => 0]);
        $lead->activities()->create(['user_id' => auth('users')->id(), 'status' => $lead->status, 'comment' => '🔓 Restored from Archive']);

        return back()->with('success', 'Lead restored.');
    }

    public function exportLeads(Request $request)
    {
        $user = auth('users')->user() ?? auth()->user();
        $allowedRoles = [1, 2, 3, 4, 5, 6, 7, 8];
        if (! in_array($user->role_id, $allowedRoles)) {
            abort(403, 'សុំទោស! មានតែថ្នាក់គ្រប់គ្រងប៉ុណ្ណោះ ទើបមានសិទ្ធិទាញយកទិន្នន័យនេះ។');
        }

        $startDate = $request->start_date ? \Carbon\Carbon::parse($request->start_date)->format('d-M-Y') : 'All Time';
        $endDate = $request->end_date ? \Carbon\Carbon::parse($request->end_date)->format('d-M-Y') : 'All Time';

        $filters = [
            'exported_by' => $user->customer_name ?? $user->full_name ?? 'Manager',
            'date_range' => $startDate.'  ដល់  '.$endDate,
        ];

        $fileName = 'Leads_Marketing_Report_'.date('Y-m-d').'.xlsx';
        $leads = $this->applyRoleScope(Lead::with('activities')->where('is_archived', 0))->latest()->get();

        return Excel::download(new LeadsExport($leads, $filters), $fileName);
    }

    public function checkDuplicate(Request $request)
    {
        $phone = $request->phone;
        $cleanPhone = str_replace(' ', '', $phone);

        $lead = Lead::where(function ($q) use ($cleanPhone) {
            $q->where('contact_info', $cleanPhone)
                ->orWhere('contact_info', 'LIKE', "%{$cleanPhone}%");
        })
            ->where(function ($q) {
                $q->whereNull('is_archived')->orWhere('is_archived', 0);
            })
            ->orderBy('updated_at', 'desc')
            ->first();

        if ($lead) {
            $modelName = is_array($lead->model) ? implode(', ', $lead->model) :
                         (is_string($lead->model) && is_array(json_decode($lead->model)) ? implode(', ', json_decode($lead->model)) : $lead->model);

            $correctRegDate = Carbon::parse($lead->date ?? $lead->created_at)->timezone('Asia/Phnom_Penh')->format('d-M-Y');

            $lastUpdate = Carbon::parse($lead->updated_at)->timezone('Asia/Phnom_Penh');
            $daysInactive = (int) $lastUpdate->diffInDays(Carbon::now()->timezone('Asia/Phnom_Penh'));
            $allowTakeover = $daysInactive >= 15;

            return response()->json([
                'exists' => true,
                'allow_takeover' => $allowTakeover,
                'days_inactive' => $daysInactive,
                'last_update' => $lastUpdate->format('d-M-Y'),
                'customer_name' => $lead->customer_name ?? 'មិនមានឈ្មោះ',
                'reg_date' => $correctRegDate,
                'sales_person' => $lead->sales_person ?? 'N/A',
                'branch' => $lead->branch_name ?? 'N/A',
                'model' => $modelName ?? 'N/A',
                'status' => $lead->status ?? 'Prospecting',
            ]);
        }

        return response()->json(['exists' => false]);
    }

    public function sendPayoffAlert(Request $request)
    {
        try {
            $request->validate([
                'lead_id' => 'required',
                'date' => 'required',
                'time' => 'required',
                'location' => 'required',
                'customer_name' => 'required',
                'model' => 'required',
                'contract_number' => 'required',
                'chassis_number' => 'required',
                'customer_pay' => 'required',
                'payment_type' => 'required',
                'sales_person' => 'required',
            ]);

            $lead = \App\Models\Lead::find($request->lead_id);
            if ($lead) {
                $lead->status = 'Payoff';
                $lead->remind_date = null;
                $lead->save();
            } else {
                return response()->json(['success' => false, 'message' => 'រកអតិថិជននេះមិនឃើញទេ!']);
            }

            $botToken = $this->botToken;
            $chatId = $this->chatId;

            if (empty($botToken) || empty($chatId)) {
                return response()->json(['success' => false, 'message' => 'សូមបញ្ចូល Token និង Chat ID ក្នុង Controller សិន!']);
            }

            $message = "<b>🔔 VEHICLE PAYOFF NOTIFICATION</b>\n"
                     ."━━━━━━━━━━━━━━━━━━━━\n"
                     ."<b>📅 Schedule Details</b>\n"
                     ."• <b>Date:</b> {$request->date}\n"
                     ."• <b>Time:</b> {$request->time}\n"
                     ."• <b>Branch:</b> {$request->location}\n\n"
                     ."<b>👤 Lead & Vehicle Info</b>\n"
                     ."• <b>Customer:</b> {$request->customer_name}\n"
                     ."• <b>Lead Owner:</b> {$request->sales_person}\n"
                     ."• <b>Model:</b> {$request->model}\n"
                     ."• <b>Contract No:</b> {$request->contract_number}\n"
                     ."• <b>Chassis (VIN):</b> <code>{$request->chassis_number}</code>\n\n"
                     ."<b>💰 Payment Summary</b>\n"
                     ."• <b>Amount to Pay:</b> {$request->customer_pay} $\n"
                     ."• <b>Method:</b> {$request->payment_type}\n"
                     ."━━━━━━━━━━━━━━━━━━━━\n"
                     .'<i>Auto-generated by System</i>';

            $response = \Illuminate\Support\Facades\Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);

            if ($response->successful()) {
                return response()->json(['success' => true, 'message' => 'សារត្រូវបានបាញ់ចូល Telegram ជោគជ័យ!']);
            } else {
                return response()->json(['success' => false, 'message' => 'បាញ់សារទៅ Telegram បរាជ័យ៖ '.$response->json('description')]);
            }

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: '.$e->getMessage()], 500);
        }
    }

    public function dailyTasks(Request $request)
    {
        $user = auth('users')->user();
        if (! $user) {
            return redirect()->route('login');
        }

        $baseQuery = \App\Models\Lead::where(function ($q) {
            $q->whereNull('is_archived')->orWhere('is_archived', 0);
        });
        $baseQuery = $this->applyRoleScope($baseQuery);

        $userRole = (int) $user->role;
        $branches = [];
        if (in_array($userRole, [1, 2, 6])) {
            $branches = \App\Models\Lead::whereNotNull('branch_name')->distinct()->pluck('branch_name');
            if ($request->filled('branch')) {
                $baseQuery->where('branch_name', $request->branch);
            }
        }

        $salesQuery = (clone $baseQuery)->where(DB::raw('LOWER(status)'), 'sales ( contract of sales )');
        $salesCount = $salesQuery->count();
        $salesList = $salesQuery->get(['id', 'customer_name', 'contact_info', 'model', 'sales_person']);

        $deliveryQuery = (clone $baseQuery)->where(DB::raw('LOWER(status)'), 'delivery');
        $deliveryCount = $deliveryQuery->count();
        $deliveryList = $deliveryQuery->get(['id', 'customer_name', 'contact_info', 'model', 'sales_person']);

        $tasks = (clone $baseQuery)
            ->whereNotIn(DB::raw('LOWER(status)'), ['delivery', 'lost', 'sales ( contract of sales )'])
            ->whereNotNull('remind_date')
            ->orderBy('remind_date', 'asc')
            ->with(['activities' => function ($q) {
                $q->latest();
            }])
            ->get();

        $stageSummary = [
            'today' => ['title' => 'Today & Overdue', 'count' => 0, 'stages' => []],
            'days_1_7' => ['title' => 'In 1 - 7 Days', 'count' => 0, 'stages' => []],
            'days_8_14' => ['title' => 'In 8 - 14 Days', 'count' => 0, 'stages' => []],
            'days_15_30' => ['title' => 'In 15 - 30 Days', 'count' => 0, 'stages' => []],
        ];

        foreach ($tasks as $lead) {
            $remindDate = \Carbon\Carbon::parse($lead->remind_date)->startOfDay();
            $today = \Carbon\Carbon::today();
            $diffDays = $today->diffInDays($remindDate, false);
            $stage = $lead->status;

            if ($diffDays <= 0) {
                $lead->filter_group = 'today';
                $stageSummary['today']['count']++;
                $stageSummary['today']['stages'][$stage] = ($stageSummary['today']['stages'][$stage] ?? 0) + 1;
            } elseif ($diffDays >= 1 && $diffDays <= 7) {
                $lead->filter_group = 'days_1_7';
                $stageSummary['days_1_7']['count']++;
                $stageSummary['days_1_7']['stages'][$stage] = ($stageSummary['days_1_7']['stages'][$stage] ?? 0) + 1;
            } elseif ($diffDays >= 8 && $diffDays <= 14) {
                $lead->filter_group = 'days_8_14';
                $stageSummary['days_8_14']['count']++;
                $stageSummary['days_8_14']['stages'][$stage] = ($stageSummary['days_8_14']['stages'][$stage] ?? 0) + 1;
            } elseif ($diffDays >= 15 && $diffDays <= 30) {
                $lead->filter_group = 'days_15_30';
                $stageSummary['days_15_30']['count']++;
                $stageSummary['days_15_30']['stages'][$stage] = ($stageSummary['days_15_30']['stages'][$stage] ?? 0) + 1;
            } else {
                $lead->filter_group = 'future';
            }
        }

        return view('Leads.daily_tasks', compact('tasks', 'user', 'stageSummary', 'salesCount', 'deliveryCount', 'branches', 'salesList', 'deliveryList'));
    }

    private function notifyTelegram($lead, $title)
    {
        $msg = "{$title}\n━━━━━━━━━━━━━━━━━━━━\n"
             ."👤 *CLIENT:* `{$lead->customer_name}`\n"
             ."📱 *PHONE:* `{$lead->contact_info}`\n"
             ."🚗 *MODEL:* {$lead->model}\n"
             ."📍 *BRANCH:* {$lead->branch_name}\n"
             ."👨‍💼 *SALES:* {$lead->sales_person}\n"
             ."━━━━━━━━━━━━━━━━━━━━\n"
             .'Please check the system for details. ⚡';

        $this->callTelegramApi($msg);
    }

    private function callTelegramApi($text)
    {
        $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";
        $data = ['chat_id' => $this->chatId, 'text' => $text, 'parse_mode' => 'Markdown'];

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_exec($ch);
            curl_close($ch);
        } catch (Exception $e) {
            Log::error('Telegram API Error: '.$e->getMessage());
        }
    }
    public function apiMobileIndex(Request $request)
    {
        $user = $request->user();
        $query = Lead::query();

        // 🌟 ១. ផ្តាច់ Admin / Director ចេញឱ្យមានសិទ្ធិពិសេស
        $directorRoles = [1, 2, 6]; 
        $managerRoles = [3, 4, 5, 7, 8, 10, 11, 12, 14, 15, 16];
        $saleRole = 9;

        if (in_array($user->role_id, $directorRoles)) {
            if ($request->has('branch_name') && $request->branch_name != '') {
                $query->where('branch_name', $request->branch_name);
            } elseif ($request->has('branch_id') && $request->branch_id != 'all' && $request->branch_id != '') {
                $query->where('branch_id', $request->branch_id);
            }
        } elseif (in_array($user->role_id, $managerRoles)) {
            // មេខេត្ត មើលបានតែខេត្តខ្លួនឯង
            $query->where('branch_id', $user->branch_id);
        } elseif ($user->role_id == $saleRole) {
            // Sale មើលបានតែរបស់ខ្លួនឯង
            $query->where('sales_person_id', $user->id);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied: លោកអ្នកមិនមានសិទ្ធិប្រើប្រាស់មុខងារនេះទេ!',
            ], 403);
        }

        // 🌟 ២. កុំឱ្យលោតចូល Archived Leads
        $query->where(function ($q) {
             $q->whereNull('is_archived')->orWhere('is_archived', 0);
        });

        $leads = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $leads,
        ]);
    }

    public function apiMobileStore(Request $request)
    {
        try {
            DB::beginTransaction();
    
            $user = $request->user();
            $branchId = $request->input('branch_id') ?: ($user ? $user->branch_id : null);
            $branchName = 'Head Quarters';
    
            if ($branchId) {
                $branch = DB::table('branches')->where('id', $branchId)->first();
                if ($branch && isset($branch->branch_name)) {
                    $branchName = $branch->branch_name;
                }
            }
    
            $lead = new Lead;
            $lead->customer_name = $request->input('customer_name');
            $lead->contact_info = $request->input('contact_info');
            $lead->status = $request->input('status', 'Prospecting');
            $lead->model = $request->input('model');
            $lead->location = $request->input('location');
            $lead->source = $request->input('source');
            $lead->remind_date = $request->input('remind_date');
    
            $lead->sales_person_id = $user ? $user->id : null;
            $lead->branch_id = $branchId; 
    
            $lead->sales_person = ($user && $user->full_name) ? $user->full_name : 'Unknown';
            $lead->branch_name = $branchName; 
    
            $userComment = $request->input('comment');
            if (!empty($userComment)) {
                $lead->comment = $userComment;
            }
    
            $lead->date = date('Y-m-d H:i:s');
            $lead->is_transferred = 0;
            $lead->is_archived = 0;
    
            $lead->save();
    
            $lead->activities()->create([
                'user_id' => $user ? $user->id : 1, 
                'status' => $lead->status,
                'comment' => !empty($userComment) ? $userComment : 'បញ្ចូលអតិថិជនថ្មី',
            ]);
    
            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'បង្កើត Lead បានជោគជ័យ!',
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Database Error: '.$e->getMessage(),
            ], 500);
        }
    }

    public function apiMobileUpdate(Request $request, $id)
    {
        try {
            $lead = Lead::find($id);
            if (!$lead) {
                return response()->json(['success' => false, 'message' => 'រកមិនឃើញទិន្នន័យទេ'], 404);
            }

            $data = $request->only([
                'customer_name', 'contact_info', 'location', 'source', 'comment', 'remind_date',
            ]);

            if ($request->has('model')) {
                $data['model'] = is_array($request->model) ? implode(', ', $request->model) : $request->model;
            }

            $lead->update($data);

            $user = $request->user();
            $lead->activities()->create([
                'user_id' => $user ? $user->id : 1,
                'status' => $lead->status,
                'comment' => $request->input('comment') ?? 'បានកែប្រែព័ត៌មានអតិថិជន',
            ]);

            return response()->json(['success' => true, 'message' => 'កែប្រែជោគជ័យ!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function apiMobileShow($id)
    {
        $lead = Lead::with(['activities.user'])->find($id);
        if (!$lead) {
            return response()->json(['success' => false, 'message' => 'រកមិនឃើញទិន្នន័យ'], 404);
        }

        return response()->json(['success' => true, 'data' => $lead]);
    }

    public function apiMobileUpdateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $lead = Lead::find($id);
            if (!$lead) {
                return response()->json(['success' => false, 'message' => 'រកមិនឃើញទិន្នន័យអតិថិជននេះទេ'], 404);
            }

            $newStatus = $request->input('status');
            $newComment = $request->input('comment');
            $remindDate = $request->input('remind_date');

            $lead->status = $newStatus;

            if (!empty($newComment)) {
                $lead->comment = $newComment;
            }
            if (!empty($remindDate)) {
                $lead->remind_date = $remindDate;
            }
            $lead->save();

            $user = $request->user();
            $activityComment = !empty($newComment) ? $newComment : 'បានផ្លាស់ប្តូរស្ថានភាពអតិថិជន (គ្មានការកត់សម្គាល់)';

            $lead->activities()->create([
                'user_id' => $user ? $user->id : 1,
                'status' => $newStatus,
                'comment' => $activityComment,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'បានផ្លាស់ប្តូរស្ថានភាពជោគជ័យ!',
                'recorded_comment' => $activityComment,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: '.$e->getMessage()], 500);
        }
    }

    public function apiMobileNotifications(Request $request)
    {
        $user = $request->user();
        $query = Lead::query();

        $adminRoles = [1, 2, 3, 6, 10, 11, 12, 14, 15, 16];

        if (!in_array($user->role_id, $adminRoles)) {
            $query->where('branch_id', $user->branch_id);
        }

        $count = $query->count();

        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }

    public function apiCheckDuplicatePhone($phone)
    {
        $lead = Lead::where('contact_info', $phone)
            ->where('is_archived', 0)
            ->first();

        if ($lead) {
            return response()->json([
                'exists' => true,
                'lead_data' => $lead, 
            ]);
        }

        return response()->json(['exists' => false]);
    }

    public function apiDirectorBranchSummary(Request $request)
    {
        $user = $request->user();

        if (!$user || !in_array((int) $user->role_id, [1, 2, 6])) {
            return response()->json([
                'success' => false,
                'message' => 'អ្នកគ្មានសិទ្ធិមើលទិន្នន័យនេះទេ!',
            ], 403);
        }

        $branches = DB::table('branches')->get();
        $summaryData = [];

        foreach ($branches as $branch) {
            $leads = Lead::where('branch_id', $branch->id)
                ->where(function ($q) {
                    $q->whereNull('is_archived')->orWhere('is_archived', 0);
                })->get();

            $booking = $leads->filter(fn ($l) => preg_match('/book|appointment/i', $l->status))->count();
            $salesContract = $leads->filter(fn ($l) => preg_match('/contract|won/i', $l->status))->count();
            $delivery = $leads->filter(fn ($l) => stripos($l->status, 'delivery') !== false)->count();

            $summaryData[] = [
                'branch_id' => $branch->id,
                'branch_name' => $branch->name ?? $branch->branch_name ?? 'Unknown',
                'total_leads' => $leads->count(),
                'total_booking' => $booking,
                'total_contract' => $salesContract,
                'total_delivery' => $delivery,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $summaryData,
        ]);
    }

    public function getTransferOptions()
    {
        $branches = DB::table('branches')
                        ->whereIn('id', [4, 5, 9, 20, 21, 27])
                        ->select('id', 'branch_name as name') 
                        ->get();
    
        return response()->json([
            'success' => true,
            'branches' => $branches,
            'staffs' => User::whereIn('role_id', [1,2,3,4,5,6,7,8,9,10,11,14,15,16])->pluck('full_name')
        ]);
    }

    public function getStaffByBranch(Request $request)
    {
        try {
            $branchId = $request->branch_id;

            $staffs = User::where('branch_id', $branchId)
                            ->whereIn('role_id', [1, 2, 3, 4, 5, 6, 7, 8, 9])
                            ->select('id', 'full_name') 
                            ->get(); 

            return response()->json([
                'success' => true,
                'staffs' => $staffs 
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function dashboardStats(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            $query = Lead::query();
            
            // 🌟 កន្លែងដែលយើងថែមថ្មី៖ ចាប់យក branch_id ពី Flutter App ដើម្បី Filter តាមសាខា
            if ($request->has('branch_id') && $request->branch_id != '') {
                $query->where('branch_id', $request->branch_id);
            }

            // លក្ខខណ្ឌចាស់របស់បង (រក្សាទុកដដែល)
            if (in_array($user->role_id, [2, 4, 5])) { 
                $query->where('branch_id', $user->branch_id);
            } 
            elseif (in_array($user->role_id, [3, 6, 7, 9])) { 
                $query->where('sales_person_id', $user->id);
            }

            $query->where(function ($q) {
                $q->whereNull('is_archived')->orWhere('is_archived', 0);
            });

            // ... កូដខាងក្រោមរក្សាទុកដូចដើមទាំងអស់ ...
            $total = (clone $query)->count();
            $overdue = (clone $query)->whereIn('status', ['ហួសកំណត់', 'Overdue'])->count();
            $today = (clone $query)->whereDate('remind_date', Carbon::today())->count();
            
            $pipeline = [
                'prospect' => (clone $query)->whereIn('status', ['Prospecting', 'New'])->count(),
                'appt'     => (clone $query)->whereIn('status', ['Appointment', 'Meet & Greet'])->count(),
                'consult'  => (clone $query)->whereIn('status', ['Consulting ( Q & A )', 'Presentation', 'Test Drive'])->count(),
                'sales'    => (clone $query)->whereIn('status', ['Negotiation', 'Trail Close', 'Contract of Sales'])->count(),
            ];

            $followUps = (clone $query)->whereDate('remind_date', Carbon::today())
                ->select('id', 'customer_name', 'status', 'remind_date', 'branch_name', 'sales_person', 'created_at')
                ->orderBy('remind_date', 'asc')
                ->get()
                ->map(function($lead) {
                    return [
                        'id'          => $lead->id,
                        'name'        => $lead->customer_name ?? 'មិនស្គាល់ឈ្មោះ',
                        'stage'       => $lead->status ?? 'N/A',
                        'branch'      => $lead->branch_name ?? 'N/A',
                        'sale_name'   => $lead->sales_person ?? 'N/A',
                        'reg_date'    => $lead->created_at ? Carbon::parse($lead->created_at)->format('d-M-Y') : 'N/A',
                        'remind_time' => $lead->remind_date ? Carbon::parse($lead->remind_date)->format('h:i A') : '--:--',
                    ];
                });

            $deals = [
                'close_deal' => (clone $query)->where('status', 'Double Close')->count(),
                'contract'   => (clone $query)->where('status', 'Contract of Sales')->count(),
                'delivery'   => (clone $query)->where('status', 'Delivery')->count(),
            ];

            $unreadNoti = $user->unreadNotifications()->count();
    
            return response()->json([
                'success' => true,
                'data' => [
                    'total'       => $total,
                    'overdue'     => $overdue,
                    'today'       => $today,
                    'pipeline'    => $pipeline,
                    'deals'       => $deals,       
                    'follow_ups'  => $followUps,
                    'unread_noti' => $unreadNoti 
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function storeCallActivity(Request $request, $id)
    {
        $request->validate([
            'result' => 'required|string',
            'note'   => 'nullable|string',
        ]);
    
        try {
            $lead = Lead::find($id);
            if (!$lead) {
                return response()->json(['success' => false, 'message' => 'រកមិនឃើញ Lead ទេ'], 404);
            }
    
            $user = $request->user();
    
            $lead->activities()->create([
                'user_id' => $user->id,
                'status'  => $request->result, 
                'comment' => ($request->note ?? '') . " | ថ្ងៃណាត់បន្ទាប់: " . ($request->nextFollowUp ?? 'គ្មាន'),
            ]);
    
            return response()->json(['success' => true, 'message' => 'រក្សាទុកជោគជ័យ!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getCallHistory($id)
    {
        try {
            $lead = Lead::find($id);
            if (!$lead) {
                return response()->json(['success' => false, 'message' => 'រកមិនឃើញ Lead ទេ'], 404);
            }

            // 🌟 កែត្រង់នេះ៖ ប្រើត្រឹម with('user') ដើម្បីការពារកុំឱ្យ Error ជាន់ Column
            $activities = $lead->activities()
                               ->with('user') 
                               ->orderBy('created_at', 'desc')
                               ->get();

            return response()->json(['success' => true, 'data' => $activities]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
