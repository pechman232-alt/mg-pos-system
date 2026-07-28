<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\POS\ReceiveHeader;
use App\Models\POS\Product;
use Illuminate\Support\Facades\DB;

class ReceiveItemController extends Controller
{
    /**
     * បង្ហាញទំព័របញ្ចូលទំនិញលម្អិត
     */
    public function index(Request $request)
    {
        $header_id = $request->header_id;
        if (!$header_id) {
            return redirect()->route('receive-headers.index')->with('error', 'សូមជ្រើសរើសវិក្កយបត្រជាមុនសិន!');
        }

        // ទាញយកព័ត៌មានក្បាលវិក្កយបត្រ និងទំនិញដែលបានបញ្ចូលរួច
        $header = ReceiveHeader::with(['vendor', 'branch', 'details.product'])->findOrFail($header_id);
        $products = Product::orderBy('item_name', 'asc')->get();
        $details = $header->details; // ទាញយក Items ទាំងអស់

        return view('POS.ReceiveItems.index', compact('header', 'products', 'details'));
    }

    /**
     * រក្សាទុកទំនិញចូលក្នុងវិក្កយបត្រ និង បូកស្តុក
     */
    public function store(Request $request)
    {
        $request->validate([
            'receive_header_id' => 'required',
            'product_id'        => 'required',
            'qty'               => 'required|numeric|min:1',
            'cost'              => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $total_cost = $request->qty * $request->cost;

            // ១. បញ្ចូលទិន្នន័យទៅក្នុងតារាង Receive Details (ប្រយ័ត្ន៖ បើបងប្រើតារាងឈ្មោះផ្សេង សូមដូរទីនេះ)
            DB::table('receive_details')->insert([
                'receive_header_id' => $request->receive_header_id,
                'product_id'        => $request->product_id,
                'qty'               => $request->qty,
                'cost'              => $request->cost,
                'total_cost'        => $total_cost,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            // ២. ធ្វើបច្ចុប្បន្នភាពស្តុកទំនិញ (បូកបន្ថែមស្តុក និង តម្លៃទិញចូលចុងក្រោយ)
            $product = Product::findOrFail($request->product_id);
            $product->stock_qty += $request->qty;
            $product->cost = $request->cost; // Update last cost
            $product->save();

            DB::commit();
            return back()->with('success', 'បន្ថែមទំនិញ និងបូកស្តុកជោគជ័យ!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * លុបទំនិញចេញពីវិក្កយបត្រ និង ដកស្តុកចេញវិញ
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $detail = DB::table('receive_details')->where('id', $id)->first();
            
            if ($detail) {
                // ១. ដកស្តុកចេញពី Product វិញ
                $product = Product::findOrFail($detail->product_id);
                $product->stock_qty -= $detail->qty;
                // ការពារកុំឱ្យស្តុកធ្លាក់ក្រោមសូន្យ
                if($product->stock_qty < 0) $product->stock_qty = 0; 
                $product->save();

                // ២. លុបទិន្នន័យក្នុងតារាង Details
                DB::table('receive_details')->where('id', $id)->delete();
            }

            DB::commit();
            return back()->with('success', 'លុបទំនិញ និងដកស្តុកវិញជោគជ័យ!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}