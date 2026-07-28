<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\BranchModel;
use App\Models\POS\Product;
use App\Models\POS\SalesHeader;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    /**
     * បង្ហាញទំព័រសម្រាប់បង្កើតវិក្កយបត្រ (POS)
     */
    public function index()
    {
        $products = Product::where('stock_qty', '>', 0)->orderBy('item_name', 'asc')->get();
        $branches = BranchModel::all();
        
        // ប្រើ max('id') ដើម្បីបង្កើត Invoice No បណ្តោះអាសន្ន
        $nextId = SalesHeader::max('id') + 1;
        $invoiceNo = 'INV-' . date('ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        $purchaseInvoices = DB::table('receive_headers')
            ->select('id', 'document_no as invoice_no', 'purchase_invoice_no')
            ->orderBy('id', 'desc')
            ->get();

        return view('POS.Sales.create', compact('products', 'branches', 'invoiceNo', 'purchaseInvoices'));
    }

    /**
     * ឆែកមើលថាតើលេខវិក្កយបត្រឡាន (Car Invoice) ស្ទួនដែរឬទេ
     */
    public function checkCarInvoice(Request $request)
    {
        $carInvoiceNo = $request->input('car_invoice_no');
        $exists = false;
        
        if (!empty($carInvoiceNo)) {
            $exists = DB::table('sales_headers')->where('car_invoice_no', $carInvoiceNo)->exists();
        }

        return response()->json(['exists' => $exists]);
    }

    /**
     * ដំណើរការរក្សាទុកទិន្នន័យលក់ និងកាត់ស្តុក
     */
    public function process(Request $request)
    {
        $request->validate([
            'location_id' => 'required',
            'cart'        => 'required|array|min:1',
        ]);

        $carInvoiceNo = $request->input('car_invoice_no');
        $reason = $request->input('reason');
        
        // ការពារករណីលេខវិក្កយបត្រឡានស្ទួន តែអត់មាន Reason
        if (!empty($carInvoiceNo)) {
            $exists = DB::table('sales_headers')->where('car_invoice_no', $carInvoiceNo)->exists();
            if ($exists && empty($reason)) {
                return response()->json([
                    'success' => false, 
                    'message' => 'លេខវិក្កយបត្រឡានធ្លាប់បានបញ្ចេញកាដូរួចហើយ! សូមវាយបញ្ចូលមូលហេតុ (Reason)។'
                ], 422);
            }
        }

        DB::beginTransaction();
        try {
            $cart = $request->input('cart');
            $nextId = SalesHeader::max('id') + 1;
            $invoiceNo = 'INV-' . date('ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            // គណនាទឹកប្រាក់
            $subtotal = collect($cart)->sum(fn($item) => (float)$item['qty'] * (float)$item['price']);
            $discount = (float)$request->input('discount_amount', 0);
            $taxPercent = (float)$request->input('tax_percent', 0);
            
            $afterDiscount = $subtotal - $discount;
            $taxAmount = $afterDiscount * ($taxPercent / 100);
            $grandTotal = $afterDiscount + $taxAmount;

            // ១. រក្សាទុកក្បាលវិក្កយបត្រ (Header)
            $saleId = DB::table('sales_headers')->insertGetId([
                'invoice_no'          => $invoiceNo,
                'sale_date'           => Carbon::now(),
                'customer_name'       => $request->input('customer_name', 'General Customer'),
                'location_id'         => $request->location_id,
                'user_id'             => auth()->id(),
                'car_buyer_name'      => $request->input('car_buyer_name'), 
                'car_invoice_no'      => $carInvoiceNo, 
                'purchase_invoice_id' => null, // ✅ លែងប្រើនៅ Header ទៀតហើយ
                'reason'              => $reason,
                'subtotal'            => $subtotal,
                'discount_amount'     => $discount,
                'tax_percent'         => $taxPercent,
                'grand_total'         => $grandTotal,
                'created_at'          => Carbon::now(),
                'updated_at'          => Carbon::now(),
            ]);

            // ២. រៀបចំទិន្នន័យ Details និងកាត់ស្តុក
            $detailsData = [];
            foreach ($cart as $item) {
                $product = Product::find($item['id']);
                
                if (!$product || $product->stock_qty < $item['qty']) {
                    throw new \Exception("ទំនិញ [" . ($product->item_name ?? 'មិនស្គាល់') . "] មិនគ្រប់គ្រាន់ក្នុងស្តុក!");
                }

                $detailsData[] = [
                    'sales_header_id'     => $saleId,
                    'product_id'          => $product->id,
                    'item_code'           => $product->item_code,
                    'item_name'           => $product->item_name,
                    'qty'                 => $item['qty'],
                    'unit_price'          => $item['price'],
                    'total_price'         => $item['qty'] * $item['price'],
                    // ✅ ចាប់យកលេខ Purchase Invoice ប្រចាំមុខទំនិញនីមួយៗ (Item Level)
                    'purchase_invoice_no' => $item['purchase_invoice_no'] ?? null, 
                    'created_at'          => Carbon::now(),
                    'updated_at'          => Carbon::now(),
                ];

                $product->decrement('stock_qty', $item['qty']);
            }

            DB::table('sales_details')->insert($detailsData);

            DB::commit();
            
            return response()->json([
                'success' => true, 
                'sale_id' => encrypt($saleId),
                'message' => 'រក្សាទុក និងកាត់ស្តុកជោគជ័យ!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("POS Checkout Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * ==============================================================
     * បង្ហាញទំព័រ Print (Dynamic: Invoice, Tax Invoice, Delivery Note)
     * ==============================================================
     */
    public function printDocument(Request $request, $encryptedId)
    {
        try {
            $id = decrypt($encryptedId);
            
            // បន្ថែម 'branch.company' ដើម្បីឱ្យវាស្គាល់ក្រុមហ៊ុន
            $sale = SalesHeader::with(['branch.company', 'details.product'])->findOrFail($id);
            
            // ចាប់យក type ពី URL (បើអត់មានទេ កំណត់ជា 'invoice')
            $docType = $request->query('type', 'invoice'); 

            // បញ្ជូនទៅកាន់ File View
            return view('POS.Sales.print_document', compact('sale', 'docType'));
            
        } catch (\Exception $e) {
            abort(404, "Invalid Document ID or Type");
        }
    }

   /**
     * បង្ហាញទំព័រប្រវត្តិវិក្កយបត្រលក់ (Sales History)
     */
    public function history(Request $request)
    {
        // ១. ទាញយកតែទិន្នន័យក្បាលវិក្កយបត្រ (Header)
        $query = DB::table('sales_headers as sh')
            ->leftJoin('branches as b', 'sh.location_id', '=', 'b.id')
            ->leftJoin('users as u', 'sh.user_id', '=', 'u.id')
            ->select(
                'sh.id',
                'sh.invoice_no as document_no',
                'sh.sale_date as date',
                'sh.created_at',
                'b.branch_name as loc_id',
                'sh.customer_name',
                'sh.car_buyer_name',
                'sh.car_invoice_no as reference',
                'sh.reason',
                'sh.grand_total', // ទាញយកលុយសរុបចេញពី Header
                'u.username as by_user' 
            );

        // ២. មុខងារស្វែងរក
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('sh.invoice_no', 'like', "%{$search}%")
                  ->orWhere('sh.customer_name', 'like', "%{$search}%")
                  ->orWhere('sh.car_invoice_no', 'like', "%{$search}%")
                  // ស្វែងរកចូលដល់កូដទំនិញ និងឈ្មោះទំនិញ
                  ->orWhereExists(function ($sub) use ($search) {
                      $sub->select(DB::raw(1))
                          ->from('sales_details as sd')
                          ->whereColumn('sd.sales_header_id', 'sh.id')
                          ->where(function($subQ) use ($search) {
                              $subQ->where('sd.item_code', 'like', "%{$search}%")
                                   ->orWhere('sd.item_name', 'like', "%{$search}%");
                          });
                  });
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('sh.sale_date', $request->date);
        }

        if ($request->filled('branch_id')) {
            $query->where('sh.location_id', $request->branch_id);
        }

        // ៣. Paginate តាមចំនួនវិក្កយបត្រ (១ វិក្កយបត្រ = ១ ជួរ)
        $sales = $query->orderBy('sh.id', 'desc')->paginate(15);

        // ៤. ទាញយកមុខទំនិញ (Items) មកភ្ជាប់ជាមួយវិក្កយបត្រនីមួយៗ
        $saleIds = $sales->pluck('id')->toArray();
        $details = DB::table('sales_details')
                     ->whereIn('sales_header_id', $saleIds)
                     ->get()
                     ->groupBy('sales_header_id');

        $sales->getCollection()->transform(function ($sale) use ($details) {
            $sale->encrypted_id = encrypt($sale->id);
            $sale->items = $details->get($sale->id, collect()); // បញ្ចូល Items ទៅក្នុង Object Sale
            return $sale;
        });

        $branches = BranchModel::all();

        return view('POS.Sales.index', compact('sales', 'branches'));
    }
    /**
     * ទាញយកលេខ Purchase Invoice ចុងក្រោយគេបង្អស់របស់មុខទំនិញ
     */
    public function getLatestPI($product_id)
    {
        $latestReceive = DB::table('receive_details as rd')
            ->join('receive_headers as rh', 'rd.receive_header_id', '=', 'rh.id')
            ->where('rd.product_id', $product_id)
            ->orderBy('rh.id', 'desc')
            ->select('rh.purchase_invoice_no', 'rh.document_no')
            ->first();

        $pi_no = '';
        if ($latestReceive) {
            // បើមាន purchase_invoice_no យកអាហ្នឹង, បើអត់ទេ យកលេខឯកសារ (document_no)
            $pi_no = $latestReceive->purchase_invoice_no ? $latestReceive->purchase_invoice_no : $latestReceive->document_no;
        }

        return response()->json(['pi_no' => $pi_no]);
    }
}