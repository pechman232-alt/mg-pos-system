<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\POS\ReceiveHeader;
use App\Models\POS\Vendor;
use App\Models\BranchModel;
use App\Models\POS\Product;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;

class ReceiveHeaderController extends Controller
{
    /**
     * ១. បង្ហាញបញ្ជីវិក្កយបត្រទិញចូល (Purchase History)
     */
    public function index(Request $request)
    {
        $query = ReceiveHeader::with(['vendor', 'branch', 'details.product']);

        if ($request->filled('branch_id')) {
            $query->where('location_id', $request->branch_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('document_no', 'like', "%{$search}%")
                  ->orWhere('purchase_invoice_no', 'like', "%{$search}%");
            });
        }

        $headers = $query->orderBy('id', 'desc')->paginate(15);
        
        // ទាញយកតែសាខា ID 23, 24, 25, 27 សម្រាប់ Filter
        $branches = BranchModel::whereIn('id', [23, 24, 25, 27])->get();

        return view('POS.ReceiveHeaders.index', compact('headers', 'branches'));
    }

    /**
     * ២. បង្ហាញទំព័រ Form សម្រាប់បង្កើតថ្មី
     */
    public function create()
    {
        // ទាញយកតែសាខា ID 23, 24, 25, 27
        $branches = BranchModel::whereIn('id', [23, 24, 25, 27])->get();
        
        $vendors = Vendor::all();
        $products = Product::orderBy('item_name', 'asc')->get();

        $nextId = ReceiveHeader::max('id') + 1;
        $document_no = 'RC-' . date('ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        return view('POS.ReceiveHeaders.create', compact('branches', 'vendors', 'products', 'document_no'));
    }

    /**
     * ៣. រក្សាទុកទិន្នន័យ (Store) - ✅ បានកែប្រែកន្លែង Redirect មិនឱ្យចេញ 404
     */
    public function store(Request $request)
    {
        $request->validate([
            'document_no' => 'required|unique:receive_headers,document_no',
            'date'        => 'required|date',
            'location_id' => 'required',
            'vendor_id'   => 'required',
        ]);

        DB::beginTransaction();
        try {
            // ចាប់យកតែ Column ដែលមានពិតប្រាកដក្នុង Database
            $data = $request->only([
                'document_no', 
                'date', 
                'purchase_invoice_no',
                'vendor_id', 
                'location_id', 
                'tran_vendor'
            ]);
            
            $header = ReceiveHeader::create($data);

            DB::commit();

            // ✅ កែសម្រួលទីនេះ៖ ប្រើប្រាស់ Plain ID ឬ Direct ID ដើម្បីបញ្ជូនទៅ Query String ត្រឹមត្រូវ មិនឱ្យ 404
            return redirect()->route('receive-items.index', ['header_id' => $header->id])
                ->with('success', 'បង្កើតក្បាលវិក្កយបត្រជោគជ័យ! សូមបញ្ចូលមុខទំនិញបន្ត។');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * ៤. បង្ហាញទំព័រ Edit (មាន Encrypt ID)
     */
    public function edit($id)
    {
        try {
            $realId = Crypt::decrypt($id);
            $header = ReceiveHeader::findOrFail($realId);
            
            // ទាញយកតែសាខា ID 23, 24, 25, 27
            $branches = BranchModel::whereIn('id', [23, 24, 25, 27])->get();
            $vendors = Vendor::all();

            return view('POS.ReceiveHeaders.edit', compact('header', 'branches', 'vendors'));
        } catch (\Exception $e) {
            return redirect()->route('receive-headers.index')->with('error', 'លេខកូដសម្ងាត់មិនត្រឹមត្រូវ!');
        }
    }

    /**
     * ៥. Update ទិន្នន័យ (មាន Encrypt ID)
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'date'        => 'required|date',
            'location_id' => 'required',
            'vendor_id'   => 'required',
        ]);

        try {
            $realId = Crypt::decrypt($id);
            $header = ReceiveHeader::findOrFail($realId);

            $header->update([
                'date'                => $request->date,
                'vendor_id'           => $request->vendor_id,
                'location_id'         => $request->location_id,
                'purchase_invoice_no' => $request->purchase_invoice_no,
                'tran_vendor'         => $request->tran_vendor,
            ]);

            return redirect()->route('receive-headers.index')->with('success', 'កែប្រែទិន្នន័យជោគជ័យ!');
        } catch (\Exception $e) {
            return back()->with('error', 'មានបញ្ហាបច្ចេកទេស៖ ' . $e->getMessage());
        }
    }

    /**
     * ៦. បង្ហាញព័ត៌មានលម្អិត (Show)
     */
    public function show($id)
    {
        try {
            $realId = is_numeric($id) ? $id : Crypt::decrypt($id);
            $header = ReceiveHeader::with(['vendor', 'branch.company', 'details.product'])->findOrFail($realId);
        } catch (\Exception $e) {
            $header = ReceiveHeader::with(['vendor', 'branch.company', 'details.product'])->findOrFail($id);
        }

        $products = Product::orderBy('item_name', 'asc')->get();
        $details = $header->details;
        return view('POS.ReceiveItems.index', compact('header', 'products', 'details'));
    }

    /**
     * ៧. Print វិក្កយបត្រ (GRN)
     */
    public function print($id)
    {
        try {
            $realId = is_numeric($id) ? $id : Crypt::decrypt($id);
            $receiveHeader = ReceiveHeader::with(['vendor', 'branch.company', 'details.product'])->findOrFail($realId);
        } catch (\Exception $e) {
            $receiveHeader = ReceiveHeader::with(['vendor', 'branch.company', 'details.product'])->findOrFail($id);
        }
        
        return view('POS.ReceiveHeaders.print', compact('receiveHeader'));
    }

    /**
     * ៨. លុបវិក្កយបត្រ
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $realId = is_numeric($id) ? $id : Crypt::decrypt($id);
            $header = ReceiveHeader::findOrFail($realId);

            foreach ($header->details as $detail) {
                $product = Product::find($detail->product_id);
                if ($product) {
                    $product->stock_qty -= $detail->qty;
                    if ($product->stock_qty < 0) $product->stock_qty = 0;
                    $product->save();
                }
            }

            $header->details()->delete();
            $header->delete();

            DB::commit();
            return redirect()->route('receive-headers.index')->with('success', 'លុបវិក្កយបត្រ និងដកស្តុកវិញជោគជ័យ!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'មិនអាចលុបបាន៖ ' . $e->getMessage());
        }
    }
}