<?php

namespace App\Http\Controllers;

use App\Models\BranchModel;
use App\Models\MemberCard;
use App\Models\SalesContract;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesExport;

class ContractController extends Controller
{
    /**
     * 1. បង្ហាញបញ្ជីកិច្ចសន្យា និងស្ថិតិ (Index) - បែងចែកតាម Role
     */
    public function index(Request $request)
    {
        $user = auth('users')->user();
        $filters = $request->only(['search', 'branch', 'status', 'from_date', 'to_date', 'sale_person']);

        // 🌟 ១. ហៅ Scope ពី Model ដើម្បីកាត់សិទ្ធិមើលទិន្នន័យតាម Role
        $baseQuery = SalesContract::byRole();

        // 🌟 ២. គណនាស្ថិតិ (Stats) ឱ្យលោតតាម Role អូតូ
        $totalRevenue = (clone $baseQuery)->sum('grand_total');
        $totalContracts = (clone $baseQuery)->count();
        $settledCount = (clone $baseQuery)->where('status', 'Payoff')->count();
        $todayEntries = (clone $baseQuery)->whereDate('created_at', Carbon::today())->count();

        // 🌟 ៣. បញ្ជីឈ្មោះ Staff សម្រាប់ Filter តាម Role
        if (in_array($user->role_id, [1, 2, 6])) {
            $staffList = User::whereNotNull('full_name')->orderBy('full_name')->pluck('full_name');
        } else {
            $staffList = User::where('branch_id', $user->branch_id)->whereNotNull('full_name')->pluck('full_name');
        }

        $contracts = $baseQuery
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('customer_name', 'like', "%{$search}%")
                        ->orWhere('contract_number', 'like', "%{$search}%")
                        ->orWhere('phone_number', 'like', "%{$search}%")
                        ->orWhere('vin_number', 'like', "%{$search}%");
                });
            })
            ->when($request->branch, fn ($q, $branch) => $q->where('branch_name', $branch))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->sale_person, fn ($q, $sp) => $q->where('sale_person', $sp))
            ->when($request->from_date, fn ($q, $from) => $q->whereDate('order_date', '>=', $from))
            ->when($request->to_date, fn ($q, $to) => $q->whereDate('order_date', '<=', $to))
            ->latest('order_date')
            ->paginate(15)
            ->withQueryString();

        return view('Sales.contracts_index', array_merge(compact(
            'contracts', 'totalRevenue', 'totalContracts', 'settledCount', 'todayEntries', 'user', 'staffList'
        ), $filters));
    }

    /**
     * 2. បើកទំព័របង្កើត (Create)
     */
    public function create(Request $request)
    {
        $user = auth('users')->user();
        $lead = $request->has('lead_id') ? DB::table('leads')->find($request->lead_id) : null;

        // 🌟 ១. រៀបចំបញ្ជី Staff & Branch តាម Role
        if (in_array($user->role_id, [1, 2, 6])) {
            $staff = User::whereNotNull('full_name')->orderBy('full_name')->pluck('full_name')->toArray();
            $allBranches = BranchModel::pluck('branch_name')->toArray();
        } else {
            $myBranch = BranchModel::where('id', $user->branch_id)->value('branch_name');
            $allBranches = [$myBranch];
            $staff = (in_array($user->role_id, [3, 4, 5, 7, 8]))
                     ? User::where('branch_id', $user->branch_id)->whereNotNull('full_name')->pluck('full_name')->toArray()
                     : [$user->full_name];
        }

        // 🌟 ២. ទាញយកបញ្ជីកាតដែលនៅទំនេរពីក្នុងស្តុក
        $availableCards = MemberCard::where('is_sale', 1)->orderBy('member_card', 'ASC')->get();

        return view('Sales.add_contracts', compact('user', 'lead', 'staff', 'allBranches', 'availableCards'));
    }

    /**
     * 3. រក្សាទុកទិន្នន័យ (Store)
     */
    public function store(Request $request)
    {
        $request->validate([
            'contract_number' => 'required|unique:sales_contracts,contract_number',
            'customer_name' => 'required|string',
            'sub_total' => 'required|numeric',
            'customer_id_card' => 'nullable|image|max:5120',
        ]);

        try {
            DB::beginTransaction();
            $data = $request->all();

            // 🛡️ Security Force: បើមិនមែន Admin/Manager គឺ Force ឈ្មោះអ្នកលក់ចេញពី Session ផ្ទាល់
            if (! in_array(auth('users')->user()->role_id, [1, 2, 3, 4, 5, 6, 7, 8])) {
                $data['sale_person'] = auth('users')->user()->full_name;
                $data['branch_name'] = BranchModel::where('id', auth('users')->user()->branch_id)->value('branch_name');
            }

            if ($request->hasFile('customer_id_card')) {
                $data['customer_id_card'] = $request->file('customer_id_card')->store('identity_docs', 'public');
            }

            if ($request->has('offers')) {
                $data['offers'] = array_values(array_filter((array) $request->offers));
            }

            SalesContract::create($data);
            DB::commit();

            return redirect()->route('contracts.index')->with('success', 'កិច្ចសន្យាថ្មីត្រូវបានរក្សាទុក!');
        } catch (Exception $e) {
            DB::rollBack();

            return back()->with('error', 'មានបញ្ហា៖ '.$e->getMessage())->withInput();
        }
    }

    /**
     * 4. បច្ចុប្បន្នភាព (Update)
     */
    public function update(Request $request, $id)
    {
        $contract = SalesContract::findOrFail($id);

        // 🌟 ១. ចាប់យកការចុចបញ្ជូល "Note (ចំណាំ)" ពី Pop-up លើតារាង
        if ($request->has('update_note_only')) {
            $contract->update(['note' => $request->note]);

            return redirect()->back()->with('success', 'ចំណាំ (Note) ត្រូវបានរក្សាទុករួចរាល់!');
        }

        // 🌟 ២. ចាប់យកការចុចប៊ូតុង Payoff ឬ Delivery ពីខាងក្រៅ (Index)
        if ($request->has('quick_toggle')) {
            $contract->update(['status' => $request->status]);

            return redirect()->back()->with('success', 'ស្ថានភាពកិច្ចសន្យាត្រូវបានប្តូរទៅជា '.strtoupper($request->status).' ជោគជ័យ!');
        }

        // 🌟 ៣. Validate ទិន្នន័យដែលទាមទារ (សម្រាប់ការកែប្រែទម្រង់ពេញ Full Edit Form)
        $request->validate([
            'contract_number' => 'required|unique:sales_contracts,contract_number,'.$contract->id,
            'customer_name' => 'required|string',
            'sub_total' => 'required|numeric',
            'id_card_number' => 'nullable',
            'source_id' => 'required',
        ]);

        try {
            DB::beginTransaction();

            // 🌟 ៤. ដករូបភាពចេញពី $data សិន ការពារកុំឱ្យយកប្រអប់ទទេទៅ Save ជាន់រូបចាស់
            $data = $request->except(['customer_id_card', '_token', '_method']);

            // 🛡️ ៥. Security: Sale មិនអាចប្តូរ Sale Person និង Branch បានឡើយ
            if (! in_array(auth('users')->user()->role_id, [1, 2, 3, 4, 5, 6, 7, 8])) {
                unset($data['sale_person']);
                unset($data['branch_name']);
            }

            // 🛠️ ការពារ Error ពេល Membership Number ជាសញ្ញាដក '-'
            if (isset($data['membership_number']) && $data['membership_number'] === '-') {
                $data['membership_number'] = null;
            }

            // 🌟 ៦. ឆែកមើលបើអតិថិជនបាន Upload រូបភាពថ្មី
            if ($request->hasFile('customer_id_card')) {
                // លុបរូបចាស់ចេញពី Folder សិន (បើមាន)
                if ($contract->customer_id_card && Storage::disk('public')->exists($contract->customer_id_card)) {
                    Storage::disk('public')->delete($contract->customer_id_card);
                }
                // Save រូបថ្មីចូល ហើយបញ្ចូលឈ្មោះផ្លូវ (Path) ទៅក្នុង $data
                $data['customer_id_card'] = $request->file('customer_id_card')->store('identity_docs', 'public');
            }

            // 🌟 ៧. រៀបចំទិន្នន័យ កាដូថែមជូន (Offers)
            if ($request->has('offers')) {
                $data['offers'] = array_values(array_filter((array) $request->offers));
            }

            // 🌟 ៨. ធ្វើការ Update ទិន្នន័យចូល Database
            $contract->update($data);

            DB::commit();

            return redirect()->route('contracts.index')->with('success', 'កិច្ចសន្យាត្រូវបានធ្វើបច្ចុប្បន្នភាពជោគជ័យ!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'ការ Update បរាជ័យ៖ '.$e->getMessage())->withInput();
        }
    }

    public function edit($encrypted_id)
    {
        // 🌟 ១. បកប្រែកូដ Encrypt មកជាលេខ ID វិញ (ការពារ Error បើមានគេលួចដូរ URL)
        try {
            $id = \Illuminate\Support\Facades\Crypt::decrypt($encrypted_id);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return redirect()->route('contracts.index')->with('error', 'លេខសម្ងាត់កិច្ចសន្យាមិនត្រឹមត្រូវ ឬផុតកំណត់!');
        }

        // 🌟 ២. ស្វែងរកកិច្ចសន្យាតាមលេខ ID ពិតប្រាកដដែលបានបកប្រែរួច
        $contract = SalesContract::findOrFail($id);

        $user = auth('users')->user();
        if ($contract->status === 'Payoff') {
            return redirect()->route('contracts.index')->with('error', 'កិច្ចសន្យាបង់ផ្ដាច់ហើយ មិនអាចកែបានទេ!');
        }

        // --- 🌟 បែងចែកទិន្នន័យតាម Role ឱ្យដាច់ដោយឡែក ---
        if (in_array($user->role_id, [1, 2, 6])) {
            $staff = User::whereNotNull('full_name')->orderBy('full_name')->pluck('full_name')->toArray();
            $allBranches = BranchModel::pluck('branch_name')->toArray();
        } else {
            $myBranchName = BranchModel::where('id', $user->branch_id)->value('branch_name');
            $allBranches = [$myBranchName];
            $staff = (in_array($user->role_id, [3, 4, 5, 7, 8]))
                     ? User::where('branch_id', $user->branch_id)->whereNotNull('full_name')->pluck('full_name')->toArray()
                     : [$user->full_name];
        }

        // 🔥 ទាញយកបញ្ជីកាតដែលនៅទំនេរ
        $availableCards = MemberCard::where('is_sale', 1)->orderBy('member_card', 'ASC')->get();

        return view('Sales.edit_contracts', compact('contract', 'user', 'staff', 'allBranches', 'availableCards'));
    }

    public function scanIdCard(Request $request)
    {
        $request->validate(['id_image' => 'required|image|max:5120']);

        try {
            $image = $request->file('id_image');
            $base64Image = base64_encode(file_get_contents($image->path()));

            // 🌟 ប្រើប្រាស់ OpenAI API ដើម្បីអានអត្តសញ្ញាណប័ណ្ណខ្មែរពិតប្រាកដ
            $apiKey = env('OPENAI_API_KEY'); // ត្រូវប្រាកដថាបងមាន Key នេះក្នុង File .env

            if (! $apiKey) {
                return response()->json(['success' => false, 'message' => 'សូមបញ្ចូល OPENAI_API_KEY ក្នុង .env សិន!']);
            }

            // បាញ់រូបភាពទៅកាន់ ChatGPT ឱ្យវាស្រូបទិន្នន័យ
            $response = Http::withToken($apiKey)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o', // Model នេះអានអក្សរខ្មែរច្បាស់ណាស់
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => 'Extract data from this Cambodia ID card. Return STRICTLY in JSON format with keys: "name_kh", "name_en", "id_number", "address". Do not include markdown formatting.',
                            ],
                            [
                                'type' => 'image_url',
                                'image_url' => ['url' => "data:image/jpeg;base64,{$base64Image}"],
                            ],
                        ],
                    ],
                ],
                'response_format' => ['type' => 'json_object'], // បង្ខំឱ្យវាឆ្លើយជា JSON
            ]);

            if ($response->successful()) {
                $result = json_decode($response->json()['choices'][0]['message']['content'], true);

                // ត្រឡប់ទិន្នន័យពិតប្រាកដទៅកាន់ Form វិញ
                return response()->json([
                    'success' => true,
                    'name_kh' => $result['name_kh'] ?? '',
                    'name_en' => $result['name_en'] ?? '',
                    'id_number' => $result['id_number'] ?? '',
                    'address' => $result['address'] ?? '',
                ]);
            } else {
                return response()->json(['success' => false, 'message' => 'AI Server Error: '.$response->body()]);
            }

        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function searchMemberCards(Request $request)
    {
        $search = $request->get('q');
        $data = MemberCard::where('member_card', 'LIKE', "%$search%")
            ->limit(15)
            ->get();

        $results = [];
        foreach ($data as $item) {
            $results[] = [
                'id' => $item->member_card,
                'text' => $item->member_card,
            ];
        }

        return response()->json(['results' => $results]);
    }

    public function downloadPdf($id)
    {
        $contract = SalesContract::findOrFail($id);
        $html = view('Sales.pdf_contract', compact('contract'))->render();
    
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
        ]);
    
        // 🌟 ក្បាច់ពិសេស៖ បញ្ជាឱ្យ mPDF បង្រួមតារាងឱ្យល្មមទំព័រ កុំឱ្យរុញធ្លាក់ទៅទំព័រទី២ 🌟
        $mpdf->shrink_tables_to_fit = 1;
    
        $mpdf->WriteHTML($html);
    
        return $mpdf->Output('Contract.pdf', 'D');
    }
    public function destroy(SalesContract $contract)
    {
        if ($contract->status === 'Payoff' && auth('users')->user()->role_id != 1) {
            return redirect()->route('contracts.index')->with('error', 'មិនអាចលុបកិច្ចសន្យាបង់ផ្ដាច់រួចបានទេ!');
        }
        if ($contract->customer_id_card) {
            Storage::disk('public')->delete($contract->customer_id_card);
        }
        $contract->delete();

        return redirect()->route('contracts.index')->with('success', 'កិច្ចសន្យាត្រូវបានលុបចេញ!');
    }

    public function show(SalesContract $contract)
    {
        return view('Sales.view_contracts', compact('contract'));
    }

    public function export(Request $request)
    {
        // កំណត់ឈ្មោះ File ដោយភ្ជាប់ជាមួយថ្ងៃខែឆ្នាំនិងម៉ោង ដើម្បីកុំឱ្យជាន់គ្នា
        $fileName = 'Sales_Contracts_' . date('Y_m_d_His') . '.xlsx';
        
        // ប្រើប្រាស់ Class SalesExport ដែលបាន Import ខាងលើ រួចបោះ Request (ទិន្នន័យ Filter) ទៅឱ្យវា
        return Excel::download(new SalesExport($request->all()), $fileName);
    }

}
