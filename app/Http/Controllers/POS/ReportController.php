<?php

namespace App\Http\Controllers\POS;

use App\Exports\DailySalesExport;
use App\Http\Controllers\Controller;
use App\Models\BranchModel; // ✅ ត្រូវប្រាកដថាមានជួរនេះ
use App\Models\POS\Product;
use App\Models\POS\ReceiveDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * ១. របាយការណ៍លក់ចេញ (Daily Sales Report)
     */
    public function dailySalesReport(Request $request)
    {
        $fromDate = $request->input('from_date', date('Y-m-d'));
        $toDate = $request->input('to_date', date('Y-m-d'));
        $branchId = $request->input('branch_id');
        $search = $request->input('search');

        // ✅ បង្កើត Variable $products ឱ្យ View ដើម្បីកុំឱ្យលោត Undefined variable
        $products = Product::orderBy('item_name', 'asc')->get();

        $branches = BranchModel::all();

        // ១. បង្កើត Query សម្រាប់ទាញយក Header
        $salesQuery = DB::table('sales_headers as sh')
        ->leftJoin('branches as b', 'sh.location_id', '=', 'b.id')
        ->leftJoin('users as u', 'sh.user_id', '=', 'u.id')
        ->select([
            'sh.id',
            'sh.invoice_no as document_no',
            'sh.sale_date as date',
            'b.branch_name as loc_id',
            'sh.customer_name',
            'sh.car_buyer_name',
            'sh.car_invoice_no as reference',
            'sh.reason',
            'sh.grand_total', // ✅ ចាប់យកឈ្មោះដើមសិន
            'sh.grand_total as total_cost', // ✅ ចាប់យក Alias
            'u.username as by_user'
        ])
            ->whereBetween('sh.sale_date', [$fromDate.' 00:00:00', $toDate.' 23:59:59']);

        // បន្ថែមលក្ខខណ្ឌ Search
        if (! empty($search)) {
            $salesQuery->where(function ($q) use ($search) {
                $q->where('sh.invoice_no', 'like', "%{$search}%")
                    ->orWhere('sh.customer_name', 'like', "%{$search}%")
                    ->orWhere('sh.car_buyer_name', 'like', "%{$search}%")
                    ->orWhere('sh.car_invoice_no', 'like', "%{$search}%");
            });
        }

        if (! empty($branchId)) {
            $salesQuery->where('sh.location_id', $branchId);
        }

        $sales = $salesQuery->orderBy('sh.id', 'desc')->paginate(50);

        // ២. ទាញយក Items លម្អិតសម្រាប់ធ្វើ Dropdown (Accordion)
        $saleIds = $sales->pluck('id')->toArray();
        $details = DB::table('sales_details')
            ->whereIn('sales_header_id', $saleIds)
            ->get()
            ->groupBy('sales_header_id');

        // ភ្ជាប់ Items ទៅកាន់ Header នីមួយៗ
        $sales->getCollection()->transform(function ($sale) use ($details) {
            $sale->items = $details->get($sale->id, collect());

            return $sale;
        });

        // ៣. គណនា Total Summary (បូកសរុបតែតាមអ្វីដែលបាន Filter)
        $summaryQuery = DB::table('sales_headers')
            ->whereBetween('sale_date', [$fromDate.' 00:00:00', $toDate.' 23:59:59']);

        if (! empty($branchId)) {
            $summaryQuery->where('location_id', $branchId);
        }
        if (! empty($search)) {
            $summaryQuery->where('invoice_no', 'like', "%{$search}%");
        }

        $totalAmount = $summaryQuery->sum('grand_total');

        // គណនាចំនួនទំនិញសរុប (Qty)
        $qtyQuery = DB::table('sales_details as sd')
            ->join('sales_headers as sh', 'sd.sales_header_id', '=', 'sh.id')
            ->whereBetween('sh.sale_date', [$fromDate.' 00:00:00', $toDate.' 23:59:59']);

        if (! empty($branchId)) {
            $qtyQuery->where('sh.location_id', $branchId);
        }
        if (! empty($search)) {
            $qtyQuery->where('sh.invoice_no', 'like', "%{$search}%");
        }

        $totalQty = $qtyQuery->sum('sd.qty');

        return view('POS.Reports.daily_sales', compact(
            'sales',
            'totalQty',
            'totalAmount',
            'products',
            'fromDate',
            'toDate',
            'branches',
            'search'
        ));
    }

    public function exportExcel(Request $request)
    {
        $fromDate = $request->input('from_date', Carbon::today()->toDateString());
        $toDate = $request->input('to_date', Carbon::today()->toDateString());
        $branchId = $request->input('branch_id');

        $fileName = 'Daily_Sales_Report_'.date('Y_m_d_H_i').'.xlsx';

        return Excel::download(new DailySalesExport($fromDate, $toDate, $branchId), $fileName);
    }

    public function inventoryBalance(Request $request)
    {
        $fromDate = $request->input('from_date', Carbon::now()->startOfMonth()->toDateString());
        $toDate = $request->input('to_date', Carbon::now()->endOfMonth()->toDateString());
        $branchId = $request->input('branch_id');
        $search = $request->input('search');

        $productQuery = Product::query();
        if (! empty($search)) {
            $productQuery->where(function ($q) use ($search) {
                $q->where('item_code', 'like', "%{$search}%")
                    ->orWhere('item_name', 'like', "%{$search}%");
            });
        }
        $products = $productQuery->orderBy('item_code', 'asc')->get();
        $branches = BranchModel::all();

        $reportData = [];
        $grandTotalCost = 0;
        $totalEndingQty = 0;

        foreach ($products as $product) {
            $getReceive = function ($operator, $date) use ($product, $branchId) {
                $q = DB::table('receive_details')
                    ->join('receive_headers', 'receive_details.receive_header_id', '=', 'receive_headers.id')
                    ->where('receive_details.product_id', $product->id);
                if ($operator === 'between') {
                    $q->whereBetween('receive_headers.date', [$date[0].' 00:00:00', $date[1].' 23:59:59']);
                } else {
                    $q->where('receive_headers.date', $operator, $date.' 00:00:00');
                }
                if (! empty($branchId)) {
                    $q->where('receive_headers.location_id', $branchId);
                }

                return $q->sum('receive_details.qty');
            };

            $saleQuery = DB::table('sales_details')
                ->join('sales_headers', 'sales_details.sales_header_id', '=', 'sales_headers.id')
                ->where('sales_details.product_id', $product->id);
            if (! empty($branchId)) {
                $saleQuery->where('sales_headers.location_id', $branchId);
            }

            $soldQty = (clone $saleQuery)->whereBetween('sales_headers.sale_date', [$fromDate.' 00:00:00', $toDate.' 23:59:59'])->sum('sales_details.qty');
            $outDetails = (clone $saleQuery)->whereBetween('sales_headers.sale_date', [$fromDate.' 00:00:00', $toDate.' 23:59:59'])
                ->select('sales_headers.invoice_no', 'sales_headers.sale_date', 'sales_headers.customer_name', 'sales_headers.car_buyer_name', 'sales_details.qty', 'sales_headers.reason')
                ->get();

            $begSold = (clone $saleQuery)->where('sales_headers.sale_date', '<', $fromDate.' 00:00:00')->sum('sales_details.qty');
            $receivedQty = $getReceive('between', [$fromDate, $toDate]);
            $begReceived = $getReceive('<', $fromDate);

            $beginningStock = $begReceived - $begSold;
            $endingStock = $beginningStock + $receivedQty - $soldQty;
            $totalCost = $endingStock * ($product->cost ?? 0);

            $grandTotalCost += $totalCost;
            $totalEndingQty += $endingStock;

            $lastLocation = DB::table('receive_headers')
                ->join('receive_details', 'receive_headers.id', '=', 'receive_details.receive_header_id')
                ->join('branches', 'receive_headers.location_id', '=', 'branches.id')
                ->where('receive_details.product_id', $product->id)
                ->select('branches.branch_name')
                ->first();

            $reportData[] = (object) [
                'item_code' => $product->item_code,
                'description' => $product->item_name,
                'category' => $product->category ?? '-',
                'loc_id' => $lastLocation ? $lastLocation->branch_name : '-',
                'um' => $product->unit ?? 'UNIT',
                'stock_beginning' => $beginningStock,
                'stock_received' => $receivedQty,
                'sold_out' => $soldQty > 0 ? -$soldQty : 0,
                'stock_ending' => $endingStock,
                'total_cost' => $totalCost,
                'out_details' => $outDetails,
            ];
        }

        return view('POS.Reports.inventory', compact('reportData', 'fromDate', 'toDate', 'branches', 'search', 'branchId', 'grandTotalCost', 'totalEndingQty'));
    }

    public function stockInReport(Request $request)
    {
        $fromDate = $request->input('from_date', Carbon::now()->startOfMonth()->toDateString());
        $toDate = $request->input('to_date', Carbon::now()->endOfMonth()->toDateString());
        $branchId = $request->input('branch_id');
        $search = $request->input('search');

        $branches = BranchModel::all();
        $query = ReceiveDetail::with(['header.vendor', 'header.branch', 'product']);

        $query->whereHas('header', function ($q) use ($fromDate, $toDate, $branchId) {
            $q->whereBetween('date', [$fromDate.' 00:00:00', $toDate.' 23:59:59']);
            if (! empty($branchId)) {
                $q->where('location_id', $branchId);
            }
        });

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('header', function ($qHeader) use ($search) {
                    $qHeader->where('document_no', 'like', "%{$search}%")
                        ->orWhere('purchase_invoice_no', 'like', "%{$search}%");
                })->orWhereHas('product', function ($qProduct) use ($search) {
                    $qProduct->where('item_code', 'like', "%{$search}%")
                        ->orWhere('item_name', 'like', "%{$search}%");
                });
            });
        }

        $receiveDetails = $query->orderBy('id', 'desc')->get();
        $totalQtyIn = $receiveDetails->sum('qty');
        $totalCostIn = $receiveDetails->sum('total_cost');

        return view('POS.Reports.stock_in', compact('receiveDetails', 'fromDate', 'toDate', 'totalQtyIn', 'totalCostIn', 'branches', 'search'));
    }

    public function exportInventory(Request $request)
    {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $branchId = $request->input('branch_id');
        $search = $request->input('search');

        $fileName = 'Inventory_Balance_'.date('Y_m_d_H_i').'.csv';
        $headers = [
            'Content-type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $productQuery = Product::query();
        if (! empty($search)) {
            $productQuery->where(function ($q) use ($search) {
                $q->where('item_code', 'like', "%{$search}%")->orWhere('item_name', 'like', "%{$search}%");
            });
        }
        $products = $productQuery->orderBy('item_code', 'asc')->get();

        $callback = function () use ($products, $fromDate, $toDate, $branchId) {
            $file = fopen('php://output', 'w');
            fwrite($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['No', 'Item Code', 'Description', 'Category', 'Location', 'UM', 'Stock Beginning', 'Stock Received', 'Sold Out', 'Stock Ending', 'Total Cost']);

            $index = 1;
            $grandTotalCost = 0;
            $totalEndingQty = 0;

            foreach ($products as $product) {
                $receivedQty = DB::table('receive_details')->join('receive_headers', 'receive_details.receive_header_id', '=', 'receive_headers.id')->where('receive_details.product_id', $product->id)->whereBetween('receive_headers.date', [$fromDate.' 00:00:00', $toDate.' 23:59:59'])->when($branchId, fn ($q) => $q->where('receive_headers.location_id', $branchId))->sum('receive_details.qty');
                $soldQty = DB::table('sales_details')->join('sales_headers', 'sales_details.sales_header_id', '=', 'sales_headers.id')->where('sales_details.product_id', $product->id)->whereBetween('sales_headers.sale_date', [$fromDate.' 00:00:00', $toDate.' 23:59:59'])->when($branchId, fn ($q) => $q->where('sales_headers.location_id', $branchId))->sum('sales_details.qty');

                $begReceived = DB::table('receive_details')->join('receive_headers', 'receive_details.receive_header_id', '=', 'receive_headers.id')->where('receive_details.product_id', $product->id)->where('receive_headers.date', '<', $fromDate.' 00:00:00')->when($branchId, fn ($q) => $q->where('receive_headers.location_id', $branchId))->sum('receive_details.qty');
                $begSold = DB::table('sales_details')->join('sales_headers', 'sales_details.sales_header_id', '=', 'sales_headers.id')->where('sales_details.product_id', $product->id)->where('sales_headers.sale_date', '<', $fromDate.' 00:00:00')->when($branchId, fn ($q) => $q->where('sales_headers.location_id', $branchId))->sum('sales_details.qty');

                $beginningStock = $begReceived - $begSold;
                $endingStock = $beginningStock + $receivedQty - $soldQty;
                $totalCost = $endingStock * ($product->cost ?? 0);
                $grandTotalCost += $totalCost;
                $totalEndingQty += $endingStock;

                $lastLoc = DB::table('receive_headers')->join('receive_details', 'receive_headers.id', '=', 'receive_details.receive_header_id')->join('branches', 'receive_headers.location_id', '=', 'branches.id')->where('receive_details.product_id', $product->id)->select('branches.branch_name')->first();

                fputcsv($file, [$index++, $product->item_code, $product->item_name, $product->category ?? '-', $lastLoc ? $lastLoc->branch_name : '-', $product->unit ?? 'UNIT', $beginningStock, $receivedQty, $soldQty > 0 ? -$soldQty : 0, $endingStock, number_format($totalCost, 2, '.', '')]);
            }
            fputcsv($file, ['', '', '', '', '', 'GRAND TOTAL:', '', '', '', $totalEndingQty, number_format($grandTotalCost, 2, '.', '')]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }


    public function dailySales(Request $request)
{
    $fromDate = $request->input('from_date', date('Y-m-d'));
    $toDate = $request->input('to_date', date('Y-m-d'));
    $branchId = $request->input('branch_id');
    $search = $request->input('search'); // ✅ ចាប់យកពាក្យដែលគេវាយស្វែងរក

    $branches = BranchModel::all();

    $query = Sale::with('items')
        ->whereDate('date', '>=', $fromDate)
        ->whereDate('date', '<=', $toDate);

    if ($branchId) {
        $query->where('loc_id', $branchId);
    }

    // ✅ កូដសម្រាប់ដំណើរការមុខងារ Search All
    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('document_no', 'LIKE', "%{$search}%")
              ->orWhere('customer_name', 'LIKE', "%{$search}%")
              ->orWhere('car_buyer_name', 'LIKE', "%{$search}%")
              ->orWhere('reference', 'LIKE', "%{$search}%")
              ->orWhere('by_user', 'LIKE', "%{$search}%")
              // ស្វែងរកដល់ក្នុងទំនិញ (Items) ទៀត
              ->orWhereHas('items', function($subQuery) use ($search) {
                  $subQuery->where('item_code', 'LIKE', "%{$search}%")
                           ->orWhere('item_name', 'LIKE', "%{$search}%");
              });
        });
    }

    $sales = $query->orderBy('date', 'desc')->paginate(15);
    $totalAmount = $query->sum('total_cost');

    return view('POS.Reports.daily_sales', compact('sales', 'branches', 'fromDate', 'toDate', 'totalAmount'));
}
}
