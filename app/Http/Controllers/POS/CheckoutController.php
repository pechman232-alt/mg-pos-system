<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\BranchModel;
use App\Models\POS\Product;
use App\Models\POS\SalesHeader;
use App\Models\AuditLog; // Added AuditLog
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    /**
     * Show the POS Checkout Page
     */
    public function index()
    {
        $products = Product::where('stock_qty', '>', 0)->orderBy('item_name', 'asc')->get();
        $branches = BranchModel::whereIn('id', [23, 24, 25, 26])->orderBy('id', 'asc')->get();
        
        // Fetch employees using full_name
        $employees = DB::table('employees')->select('id', 'full_name')->orderBy('full_name', 'asc')->get();
        
        $nextId = SalesHeader::max('id') + 1;
        $invoiceNo = 'INV-' . date('ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        $purchaseInvoices = DB::table('receive_headers')
            ->select('id', 'document_no as invoice_no', 'purchase_invoice_no')
            ->orderBy('id', 'desc')
            ->get();

        return view('POS.Sales.create', compact('products', 'branches', 'invoiceNo', 'purchaseInvoices', 'employees'));
    }

    /**
     * Check if Car Invoice is duplicated
     */
    public function checkCarInvoice(Request $request)
    {
        $carInvoice = $request->car_invoice_no;
        
        // Find Sales Header using this car invoice number
        $sales = DB::table('sales_headers')
                    ->where('car_invoice_no', $carInvoice)
                    ->pluck('id');
        
        if ($sales->isNotEmpty()) {
            // If exists, fetch the items previously checked out
            $items = DB::table('sales_details')
                        ->whereIn('sales_header_id', $sales)
                        ->select('item_code', 'item_name', 'qty')
                        ->get();
                        
            return response()->json([
                'exists' => true,
                'items' => $items
            ]);
        }
        
        return response()->json(['exists' => false]);
    }

    /**
     * Process the sale and cut stock
     */
    public function process(Request $request)
    {
        $request->validate([
            'location_id' => 'required',
            'cart'        => 'required|array|min:1',
        ]);

        $carInvoiceNo = $request->input('car_invoice_no');
        $reason = $request->input('reason');
        $remark = $request->input('remark'); // Header Remark
        $buyerName = $request->input('car_buyer_name', 'General Customer');
        
        // Prevent duplicate car invoice without a reason
        if (!empty($carInvoiceNo)) {
            $exists = DB::table('sales_headers')->where('car_invoice_no', $carInvoiceNo)->exists();
            if ($exists && empty($reason)) {
                return response()->json([
                    'success' => false, 
                    'message' => 'This Car Invoice has already been processed! Please provide a Reason.'
                ], 422);
            }
        }

        DB::beginTransaction();
        try {
            $cart = $request->input('cart');
            $nextId = SalesHeader::max('id') + 1;
            $invoiceNo = 'INV-' . date('ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            // Calculate totals
            $subtotal = collect($cart)->sum(fn($item) => (float)$item['qty'] * (float)$item['price']);
            $discount = (float)$request->input('discount_amount', 0);
            $taxPercent = (float)$request->input('tax_percent', 0);
            
            $afterDiscount = $subtotal - $discount;
            $taxAmount = $afterDiscount * ($taxPercent / 100);
            $grandTotal = $afterDiscount + $taxAmount;

            // 1. Save Sales Header
            $saleId = DB::table('sales_headers')->insertGetId([
                'invoice_no'          => $invoiceNo,
                'sale_date'           => Carbon::now(),
                'customer_name'       => $request->input('customer_name', 'General Customer'),
                'location_id'         => $request->location_id,
                'user_id'             => auth()->id(), 
                'employee_id'         => $request->input('employee_id'), 
                'car_buyer_name'      => $buyerName, 
                'car_invoice_no'      => $carInvoiceNo, 
                'purchase_invoice_id' => null,
                'reason'              => $reason,
                'remark'              => $remark, // Save Header Remark
                'subtotal'            => $subtotal,
                'discount_amount'     => $discount,
                'tax_percent'         => $taxPercent,
                'grand_total'         => $grandTotal,
                'created_at'          => Carbon::now(),
                'updated_at'          => Carbon::now(),
            ]);

            // 2. Prepare Details Data and Cut Stock
            $detailsData = [];
            foreach ($cart as $item) {
                $product = Product::find($item['id']);
                
                if (!$product || $product->stock_qty < $item['qty']) {
                    throw new \Exception("Insufficient stock for item: [" . ($product->item_name ?? 'Unknown') . "]!");
                }

                $detailsData[] = [
                    'sales_header_id'     => $saleId,
                    'product_id'          => $product->id,
                    'item_code'           => $product->item_code,
                    'item_name'           => $product->item_name,
                    'qty'                 => $item['qty'],
                    'unit_price'          => $item['price'],
                    'total_price'         => $item['qty'] * $item['price'],
                    'purchase_invoice_no' => $item['purchase_invoice_no'] ?? null, 
                    'remark'              => $item['remark'] ?? null, 
                    'created_at'          => Carbon::now(),
                    'updated_at'          => Carbon::now(),
                ];

                $product->decrement('stock_qty', $item['qty']);
            }

            DB::table('sales_details')->insert($detailsData);

            DB::commit();

            // 3. Record Audit Log (After successful commit)
            if (class_exists(\App\Models\AuditLog::class)) {
                \App\Models\AuditLog::record(
                    'POS Checkout', 
                    'Create', 
                    "Created new sales invoice: {$invoiceNo} for {$buyerName}",
                    null, 
                    ['total' => $grandTotal, 'items_count' => count($cart)]
                );
            }
            
            return response()->json([
                'success' => true, 
                'sale_id' => encrypt($saleId),
                'message' => 'Checkout processed successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("POS Checkout Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Show Print Document Page
     */
    public function printDocument(Request $request, $encryptedId)
    {
        try {
            $id = decrypt($encryptedId);
            
            // Load relationships including company
            $sale = SalesHeader::with(['branch.company', 'details.product'])->findOrFail($id);
            
            // Get doc type from URL (default is 'invoice')
            $docType = $request->query('type', 'invoice'); 

            return view('POS.Sales.print_document', compact('sale', 'docType'));
            
        } catch (\Exception $e) {
            abort(404, "Invalid Document ID or Type");
        }
    }

    /**
     * View Sales History
     */
    public function history(Request $request)
    {
        // 1. Fetch header data and join related tables
        $query = DB::table('sales_headers as sh')
            ->leftJoin('branches as b', 'sh.location_id', '=', 'b.id')
            ->leftJoin('users as u', 'sh.user_id', '=', 'u.id')
            ->leftJoin('employees as e', 'sh.employee_id', '=', 'e.id') 
            ->select(
                'sh.id',
                'sh.invoice_no as document_no',
                'sh.sale_date as date',
                'sh.created_at',
                'b.branch_name as loc_id',
                DB::raw('COALESCE(e.full_name, sh.customer_name) as customer_name'),
                'sh.car_buyer_name',
                'sh.car_invoice_no as reference',
                'sh.reason',
                'sh.remark', 
                'sh.grand_total',
                'u.username as by_user' 
            );

        // 2. Search Functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('sh.invoice_no', 'like', "%{$search}%")
                  ->orWhere('sh.customer_name', 'like', "%{$search}%")
                  ->orWhere('e.full_name', 'like', "%{$search}%")
                  ->orWhere('sh.car_invoice_no', 'like', "%{$search}%")
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

        // 3. Date Range Filter
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween(DB::raw('DATE(sh.sale_date)'), [$request->from_date, $request->to_date]);
        } elseif ($request->filled('from_date')) {
            $query->whereDate('sh.sale_date', '>=', $request->from_date);
        } elseif ($request->filled('to_date')) {
            $query->whereDate('sh.sale_date', '<=', $request->to_date);
        }

        if ($request->filled('branch_id')) {
            $query->where('sh.location_id', $request->branch_id);
        }

        // 4. Paginate
        $sales = $query->orderBy('sh.id', 'desc')->paginate(15);

        // 5. Attach Item Details
        $saleIds = $sales->pluck('id')->toArray();
        $details = DB::table('sales_details')
                     ->whereIn('sales_header_id', $saleIds)
                     ->get()
                     ->groupBy('sales_header_id');

        $sales->getCollection()->transform(function ($sale) use ($details) {
            $sale->encrypted_id = encrypt($sale->id);
            $sale->items = $details->get($sale->id, collect());
            return $sale;
        });

        $branches = BranchModel::whereIn('id', [23, 24, 25, 26])->orderBy('id', 'asc')->get();

        return view('POS.Sales.index', compact('sales', 'branches'));
    }

    /**
     * Get Latest PI for Product
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
            $pi_no = $latestReceive->purchase_invoice_no ? $latestReceive->purchase_invoice_no : $latestReceive->document_no;
        }

        return response()->json(['pi_no' => $pi_no]);
    }
    
    /**
     * Get Products with Stock by Branch
     */
    public function getItemsByBranch($branch_id)
    {
        try {
            $items = \Illuminate\Support\Facades\DB::table('products')
                ->where('stock_qty', '>', 0) 
                ->whereExists(function ($query) use ($branch_id) {
                    $query->select(\Illuminate\Support\Facades\DB::raw(1))
                          ->from('receive_details')
                          ->join('receive_headers', 'receive_details.receive_header_id', '=', 'receive_headers.id')
                          ->whereColumn('receive_details.product_id', 'products.id') 
                          ->where('receive_headers.location_id', $branch_id); 
                })
                ->select(
                    'id',
                    'item_code',
                    'item_name',
                    'price',
                    'stock_qty as qty'
                )
                ->get();

            return response()->json($items);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Database Error: ' . $e->getMessage()
            ], 500);
        }
    }
}