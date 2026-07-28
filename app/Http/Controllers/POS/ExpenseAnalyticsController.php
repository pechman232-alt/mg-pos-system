<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        // ១. ចាប់យកទិន្នន័យសរុប (Total Expense) ពី sales_details ឬ tarrary ចំណាយរបស់បង
        $totalExpense = DB::table('sales_details')->sum('total_price');
        
        // ២. រកមើលទំនិញ/ប្រភេទ ដែលលក់ ឬចំណាយដាច់ជាងគេ (Top Category)
        $topItem = DB::table('sales_details')
            ->select('item_name', DB::raw('SUM(total_price) as total'))
            ->groupBy('item_name')
            ->orderBy('total', 'desc')
            ->first();
        
        $topCategory = $topItem ? $topItem->item_name : 'មិនមានទិន្នន័យ';

        // ៣. ទិន្នន័យសម្រាប់ Donut Chart (បង្ហាញការលក់/ចំណាយតាម Item ឬ Category ជាក់ស្តែង)
        $categories = DB::table('sales_details')
            ->select('item_name', DB::raw('SUM(total_price) as total'))
            ->groupBy('item_name')
            ->orderBy('total', 'desc')
            ->limit(5) // យកតែ 5 កំពូល
            ->get();

        $expenseByCategory = [
            'labels' => $categories->pluck('item_name')->toArray(),
            'series' => $categories->pluck('total')->map(fn($val) => (float)$val)->toArray()
        ];

        // 4. ទិន្នន័យសម្រាប់ Trend Chart (លំហូរប្រចាំថ្ងៃ ពី sales_headers)
        $trends = DB::table('sales_headers')
            ->select(DB::raw('DATE(sale_date) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(7)
            ->get()
            ->reverse();

        $expenseTrend = [
            'categories' => $trends->pluck('date')->toArray(),
            'data' => $trends->pluck('count')->toArray()
        ];

        $dailyAvg = count($expenseTrend['data']) > 0 ? array_sum($expenseTrend['data']) / count($expenseTrend['data']) : 0;

        return view('POS.Reports.expense_dashboard', compact(
            'totalExpense', 
            'dailyAvg', 
            'topCategory', 
            'expenseByCategory', 
            'expenseTrend'
        ));
    }
}