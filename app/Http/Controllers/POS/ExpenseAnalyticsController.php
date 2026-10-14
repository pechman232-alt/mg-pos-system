<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // បន្ថែម Carbon សម្រាប់លេងជាមួយកាលបរិច្ឆេទ

class ExpenseAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        // ១. ចាប់យកទិន្នន័យសរុប (Total Expense) ពី sales_details
        $totalExpense = DB::table('sales_details')->sum('total_price');
        
        // ២. រកមើលទំនិញ/ប្រភេទ ដែលលក់ ឬចំណាយដាច់ជាងគេ (Top Category)
        $topItem = DB::table('sales_details')
            ->select('item_name', DB::raw('SUM(total_price) as total'))
            ->groupBy('item_name')
            ->orderBy('total', 'desc')
            ->first();
        
        $topCategory = $topItem ? $topItem->item_name : 'មិនមានទិន្នន័យ';

        // ៣. ទិន្នន័យសម្រាប់ Donut Chart (បង្ហាញការលក់/ចំណាយ 5 កំពូល)
        $categories = DB::table('sales_details')
            ->select('item_name', DB::raw('SUM(total_price) as total'))
            ->groupBy('item_name')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        $expenseByCategory = [
            'labels' => $categories->pluck('item_name')->toArray(),
            'series' => $categories->pluck('total')->map(fn($val) => (float)$val)->toArray()
        ];

        // ៤. ទិន្នន័យសម្រាប់ Trend Chart (លំហូរប្រចាំថ្ងៃ ៧ថ្ងៃចុងក្រោយ)
        $trendCategories = [];
        $trendData = [];
        
        // ប្រើ Vòng Loop 7 ថ្ងៃ ដើម្បីធានាថា Chart ចេញជាប់គ្នា ទោះថ្ងៃខ្លះអត់មានការលក់ (0$) ក៏ដោយ
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            
            // ធ្វើការបូកសរុបលុយ (SUM) ប្រចាំថ្ងៃនីមួយៗ
            // ចំណាំ៖ ខ្ញុំសន្មតថាក្នុង table sales_headers បងមានកូឡុំ 'total_amount' ឫ 'grand_total'
            $dailySum = DB::table('sales_headers')
                ->whereDate('sale_date', $date->format('Y-m-d'))
                ->sum('grand_total'); // 👈 បើបងប្រើកូឡុំឈ្មោះផ្សេង សូមប្តូរត្រង់នេះ (ឧ. grand_total)
                
            $trendCategories[] = $date->format('d M'); // ចេញទម្រង់ '08 Aug' ស្អាតនៅលើ Chart
            $trendData[] = (float) $dailySum;
        }

        $expenseTrend = [
            'categories' => $trendCategories,
            'data' => $trendData
        ];

        // ៥. មធ្យមប្រចាំថ្ងៃ (Daily Avg) - ធៀបនឹង 7 ថ្ងៃចុងក្រោយ
        $dailyAvg = count($trendData) > 0 ? array_sum($trendData) / count($trendData) : 0;

        return view('POS.Reports.expense_dashboard', compact(
            'totalExpense', 
            'dailyAvg', 
            'topCategory', 
            'expenseByCategory', 
            'expenseTrend'
        ));
    }
}