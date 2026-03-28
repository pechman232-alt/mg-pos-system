<?php

namespace App\Http\Controllers;

use App\Models\SalesContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesExport;
use Carbon\Carbon;
use Exception;

class ContractController extends Controller
{
    /**
     * 1. បង្ហាញបញ្ជីកិច្ចសន្យា និងស្ថិតិសរុប
     */
    public function index(Request $request)
    {
        // ទាញយក Filter inputs
        $filters = $request->only(['search', 'branch', 'status', 'from_date', 'to_date', 'sale_person']);

        // --- គណនាស្ថិតិសរុប (Stats) ---
        $totalRevenue = SalesContract::sum('grand_total');
        $totalContracts = SalesContract::count();
        $settledCount = SalesContract::where('status', 'Payoff')->count();
        $todayEntries = SalesContract::whereDate('created_at', Carbon::today())->count();

        // --- ទាញយកទិន្នន័យបញ្ជី (Filtered List) ---
        $contracts = SalesContract::query()
            ->when($request->search, function ($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('customer_name', 'like', "%{$search}%")
                      ->orWhere('contract_number', 'like', "%{$search}%")
                      ->orWhere('phone_number', 'like', "%{$search}%")
                      ->orWhere('model', 'like', "%{$search}%");
                });
            })
            ->when($request->branch, fn($q, $branch) => $q->where('branch_name', $branch))
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->sale_person, fn($q, $sp) => $q->where('sale_person', $sp))
            ->when($request->from_date, fn($q, $from) => $q->whereDate('order_date', '>=', $from))
            ->when($request->to_date, fn($q, $to) => $q->whereDate('order_date', '<=', $to))
            ->latest('order_date')
            ->paginate(15)
            ->withQueryString();

        return view('Sales.contracts_index', array_merge(compact(
            'contracts', 'totalRevenue', 'totalContracts', 'settledCount', 'todayEntries'
        ), $filters));
    }

    /**
     * 2. បង្កើតកិច្ចសន្យាថ្មី (Store)
     */
    public function store(Request $request)
    {
        $request->validate([
            'contract_number'  => 'required|unique:sales_contracts,contract_number',
            'order_date'       => 'required|date',
            'branch_name'      => 'required|string',
            'status'           => 'required|in:Booking,Payoff',
            'customer_name'    => 'required|string|max:255',
            'phone_number'     => 'required|string',
            'sub_total'        => 'required|numeric|min:0',
            'sale_person'      => 'required|string',
            'customer_id_card' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        try {
            DB::beginTransaction();
            $data = $request->all();

            // Handle ID Card Upload
            if ($request->hasFile('customer_id_card')) {
                $data['customer_id_card'] = $request->file('customer_id_card')->store('identity_docs', 'public');
            }

            // Handle Offers (Array to String)
            if ($request->has('offers')) {
                $data['offers'] = implode(', ', array_filter((array)$request->offers));
            }

            // Auto-Math for Grand Total
            $sub = (float)($request->sub_total ?? 0);
            $disc = (float)($request->discount ?? 0);
            $dep = (float)($request->deposit ?? 0);
            $data['grand_total'] = $sub - $disc - $dep;
            $data['source'] = $request->source ?? 'Direct';

            SalesContract::create($data);
            DB::commit();

            return redirect()->route('contracts.index')->with('success', 'កិច្ចសន្យាថ្មីត្រូវបានបង្កើតដោយជោគជ័យ!');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'មានបញ្ហា៖ ' . $e->getMessage())->withInput();
        }
    }

    /**
     * 3. កែប្រែកិច្ចសន្យា (Update)
     */
    public function update(Request $request, SalesContract $contract)
    {
        // កម្រិតសិទ្ធិ៖ ប្រសិនបើ Payoff ហើយ មិនអនុញ្ញាតឱ្យ Update ទេ (លើកលែងតែការដូរ Status ក្នុង quick_toggle)
        if ($contract->status === 'Payoff' && !$request->has('quick_toggle')) {
            return redirect()->route('contracts.index')->with('error', 'កិច្ចសន្យានេះត្រូវបានបង់ផ្ដាច់រួចរាល់ មិនអាចកែសម្រួលបានទេ!');
        }

        // មុខងារ Toggle ស្ថានភាពរហ័ស
        if ($request->has('quick_toggle')) {
            $contract->update(['status' => $request->status]);
            return redirect()->back()->with('success', 'ស្ថានភាពត្រូវបានប្តូរទៅជា ' . $request->status);
        }

        $request->validate([
            'contract_number'  => 'required|unique:sales_contracts,contract_number,' . $contract->id,
            'customer_name'    => 'required|string|max:255',
            'phone_number'     => 'required|string',
            'order_date'       => 'required|date',
            'branch_name'      => 'required|string',
            'sub_total'        => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();
            $data = $request->all();

            // Update Image
            if ($request->hasFile('customer_id_card')) {
                if ($contract->customer_id_card) {
                    Storage::disk('public')->delete($contract->customer_id_card);
                }
                $data['customer_id_card'] = $request->file('customer_id_card')->store('identity_docs', 'public');
            }

            // Handle Offers
            if ($request->has('offers')) {
                $data['offers'] = implode(', ', array_filter((array)$request->offers));
            }

            // Recalculate Grand Total
            $sub = (float)($request->sub_total ?? 0);
            $disc = (float)($request->discount ?? 0);
            $dep = (float)($request->deposit ?? 0);
            $data['grand_total'] = $sub - $disc - $dep;

            $contract->update($data);
            DB::commit();

            return redirect()->route('contracts.index')->with('success', 'កិច្ចសន្យាត្រូវបានធ្វើបច្ចុប្បន្នភាព!');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'ការ Update បរាជ័យ៖ ' . $e->getMessage());
        }
    }

    /**
     * 4. ទាញយកឯកសារ Excel
     */
    public function export(Request $request) 
    {
        $fileName = 'Sales_Report_' . now()->format('Y-m-d_His') . '.xlsx';
        return Excel::download(new SalesExport(
            $request->search, $request->branch, $request->status, 
            $request->from_date, $request->to_date, $request->sale_person
        ), $fileName);
    }

    /**
     * 5. Views
     */
    public function create() { return view('Sales.add_contracts'); }
    
    public function edit(SalesContract $contract) 
    { 
        if ($contract->status === 'Payoff') {
            return redirect()->route('contracts.index')->with('error', 'កិច្ចសន្យាដែលបង់ផ្ដាច់ហើយ មិនអាចកែសម្រួលបានទេ!');
        }
        return view('Sales.edit_contracts', compact('contract')); 
    }
    
    public function show(SalesContract $contract) { return view('Sales.view_contracts', compact('contract')); }

    /**
     * 6. លុបកិច្ចសន្យា (Destroy)
     */
    public function destroy(SalesContract $contract)
    {
        // កម្រិតសិទ្ធិ៖ បើ Payoff ហើយ ហាមលុប
        if ($contract->status === 'Payoff') {
            return redirect()->route('contracts.index')->with('error', 'មិនអាចលុបកិច្ចសន្យាដែលបង់ផ្ដាច់រួចហើយបានទេ!');
        }

        if ($contract->customer_id_card) {
            Storage::disk('public')->delete($contract->customer_id_card);
        }

        $contract->delete();
        return redirect()->route('contracts.index')->with('success', 'កិច្ចសន្យាត្រូវបានលុបចេញពីប្រព័ន្ធ!');
    }
}