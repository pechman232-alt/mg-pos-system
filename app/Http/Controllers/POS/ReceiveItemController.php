<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\POS\ReceiveHeader;
use App\Models\POS\Product;
use Illuminate\Support\Facades\DB;

class ReceiveItemController extends Controller
{
    
    public function index(Request $request)
    {
        $header_id = $request->header_id;
        if (!$header_id) {
            return redirect()->route('receive-headers.index')->with('error', 'សូមជ្រើសរើសវិក្កយបត្រជាមុនសិន!');
        }

        // ១. ទាញយកព័ត៌មានក្បាលវិក្កយបត្រ និងសាខារបស់វា
        $header = ReceiveHeader::with(['vendor', 'branch', 'details.product'])->findOrFail($header_id);

        // ២. ✅ ទាញយកតែទំនិញណាដែលមាន location_id ស្មើនឹង location_id របស់វិក្កយបត្រនេះ
        $products = Product::where('location_id', $header->location_id)
                           ->orderBy('item_name', 'asc')
                           ->get();

        // (ជាជម្រើស៖ បើសាខាខ្លះចង់ឱ្យបង្ហាញទំនិញដែលគ្មាន location_id ផង អាចប្រើ orWhereNull បាន)

        $details = $header->details;

        return view('POS.ReceiveItems.index', compact('header', 'products', 'details'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'receive_header_id' => 'required',
            'product_id'        => 'required',
            'qty'               => 'required|numeric|min:0.01',
            'cost'              => 'required|numeric',
        ]);

        DB::beginTransaction();
        try {
            $headerId = $request->receive_header_id;
            $productId = $request->product_id;
            $inputQty = $request->qty;
            $inputCost = $request->cost;

            $product = Product::findOrFail($productId);
            
            // កត់ត្រាស្តុកដើមមុនពេលបូក
            $oldStock = $product->stock_qty;

            // ១. ឆែកមើលថាតើទំនិញនេះធ្លាប់បានបញ្ចូលក្នុងវិក្កយបត្រនេះហើយឬនៅ?
            $existingDetail = DB::table('receive_details')
                                ->where('receive_header_id', $headerId)
                                ->where('product_id', $productId)
                                ->first();

            if ($existingDetail) {
                // បើមានហើយ គឺយើងគ្រាន់តែ Update ចំនួន (បូកថែម) និងគណនាតម្លៃសរុបឡើងវិញ
                $newQty = $existingDetail->qty + $inputQty;
                DB::table('receive_details')
                    ->where('id', $existingDetail->id)
                    ->update([
                        'qty'        => $newQty,
                        'cost'       => $inputCost, // យកតម្លៃចុងក្រោយ
                        'total_cost' => $newQty * $inputCost,
                        'updated_at' => now()
                    ]);
            } else {
                // បើមិនទាន់មានទេ ទើបយើងបង្កើតជួរថ្មី (Insert)
                DB::table('receive_details')->insert([
                    'receive_header_id' => $headerId,
                    'product_id'        => $productId,
                    'item_code'         => $product->item_code,
                    'qty'               => $inputQty,
                    'cost'              => $inputCost,
                    'total_cost'        => $inputQty * $inputCost,
                    'created_at'        => now(),
                    'updated_at'        => now()
                ]);
            }

            // ២. បូកស្តុកចូលទៅក្នុង Table Products
            $product->stock_qty += $inputQty;
            $product->save();

            DB::commit();
            
            // ៣. រៀបចំសារបង្ហាញពីព័ត៌មានស្តុក
            $message = "បន្ថែមទំនិញ ({$product->item_code}) ជោគជ័យ! " .
                       "ស្តុកមុន: {$oldStock} | " .
                       "បូកបន្ថែម: +{$inputQty} | " .
                       "ស្តុកចុងក្រោយសល់: {$product->stock_qty}";

            // ✅ កែសម្រួលទីនេះ៖ ពេល Save រួច ឱ្យវាលោតមកកាន់បញ្ជីវិក្កយបត្រ (receive-headers.index) វិញ
            return redirect()->route('receive-headers.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * លុបទំនិញចេញពីវិក្កយបត្រ និង ដកស្តុកចេញវិញ (បង្ហាញព័ត៌មានស្តុកច្បាស់លាស់)
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            // ទាញយកទិន្នន័យ Detail តាម ID ដែលចុចលុប
            $detail = DB::table('receive_details')->where('id', $id)->first();
            
            if (!$detail) {
                return back()->with('error', 'មិនមានទិន្នន័យនេះទេ!');
            }

            $product = Product::findOrFail($detail->product_id);
            
            // កត់ត្រាស្តុកដើមមុនពេលកាត់ចេញ
            $oldStock = $product->stock_qty;
            $qtyToRemove = $detail->qty;

            // ១. ដកស្តុកចេញវិញ តាមចំនួនដែលធ្លាប់បានបញ្ចូល
            $product->stock_qty -= $qtyToRemove;
            if ($product->stock_qty < 0) {
                $product->stock_qty = 0; // ការពារកុំឱ្យស្តុកជាប់អវិជ្ជមាន
            }
            $product->save();

            // ២. លុបទិន្នន័យចេញពីតារាង
            DB::table('receive_details')->where('id', $id)->delete();

            DB::commit();

            // ៣. បោះសារប្រាប់ពីស្តុកដើម ចំនួនដក និងស្តុកចុងក្រោយយ៉ាងច្បាស់
            $message = "បានលុបទំនិញ ({$product->item_code}) ចេញ! " .
                       "ស្តុកមុន: {$oldStock} | " .
                       "កាត់ចេញ: -{$qtyToRemove} | " .
                       "ស្តុកចុងក្រោយសល់: {$product->stock_qty}";

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}