<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BranchModel;
use App\Models\ProvinceModel;
use App\Models\Lead; 
use App\Models\User;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * 🔒 មុខងារត្រងទិន្នន័យតាម Role សម្រាប់អ្នកប្រើប្រាស់ (Data Security Scope)
     */
    private function applyRoleScope($query)
    {
        $user = auth('users')->user(); 
        if (!$user) return $query;

        $roleId = (int)$user->role_id;

        // 👑 ក្រុម Admin / Director / Group Manager (ID: 1, 2, 6, 11) -> ឃើញទាំងអស់
        if (in_array($roleId, [1, 2, 6, 11])) {
            return $query;
        } 
        
        // 🏢 ក្រុម Sale Manager (ID: 3, 4, 5, 7, 8) -> ឃើញតាមសាខា និងកូនចៅក្រោមបង្គាប់
        if (in_array($roleId, [3, 4, 5, 7, 8])) {
            $branchName = BranchModel::where('id', $user->branch_id)->value('branch_name');
            $subordinateNames = User::where('branch_id', $user->branch_id)->pluck('full_name')->toArray();

            return $query->where(function($q) use ($branchName, $subordinateNames) {
                if ($branchName) { 
                    $searchName = trim(str_ireplace(['MG ', 'Mg '], '', $branchName));
                    $q->where('branch_name', 'LIKE', "%{$searchName}%"); 
                }
                if (!empty($subordinateNames)) { 
                    $q->orWhereIn('sales_person', $subordinateNames); 
                }
            });
        } 
        
        // 👤 ក្រុម Sale Consultant (ID: 9) -> ឃើញតែទិន្នន័យរបស់ខ្លួនឯងប៉ុណ្ណោះ
        return $query->where('sales_person', $user->full_name);
    }

    public function homePage(Request $request)
    {
        $user = auth('users')->user(); 
        $roleId = (int)($user->role_id ?? 0);

        // 🚀 កម្រិតទី ១៖ កែប្រែ SQL សម្រាប់គណនា Progress (ប្រើ LOWER ដើម្បីការពារអក្សរធំ/តូចខុសគ្នា)
        $statusCases = "
            CASE
                WHEN LOWER(status) = 'delivery' THEN 100
                WHEN LOWER(status) = 'sales ( contract of sales )' THEN 96
                WHEN LOWER(status) = 'double close' THEN 90
                WHEN LOWER(status) = 'negotiation' THEN 84
                WHEN LOWER(status) = 'trial close' THEN 74
                WHEN LOWER(status) = 'test drive' THEN 64
                WHEN LOWER(status) = 'presentation' THEN 52
                WHEN LOWER(status) = 'double open' THEN 40
                WHEN LOWER(status) = 'consulting ( q & a )' THEN 32
                WHEN LOWER(status) = 'meet & greet' THEN 24
                WHEN LOWER(status) = 'appointment' THEN 16
                WHEN LOWER(status) = 'prospecting' THEN 8
                ELSE 0
            END
        ";

        // 🔍 ចាប់ផ្តើម Query (មិនរាប់បញ្ចូលទិន្នន័យ Archive)
        $query = Lead::query()->where(function($q){
            $q->whereNull('is_archived')->orWhere('is_archived', 0);
        });

        // 🔒 អនុវត្តការបែងចែកសិទ្ធិលើ Query (Security Context)
        $query = $this->applyRoleScope($query);

        // 🌟 មុខងារថ្មី៖ Filter តាមសាខា (សម្រាប់ Admin / Director / Manager)
        if (in_array($roleId, [1, 2, 6, 11]) && $request->filled('branch') && $request->branch !== 'all') {
            $branchId = $request->branch;
            $branch = BranchModel::find($branchId);
            
            if ($branch) {
                $branchSearch = trim(str_ireplace(['MG ', 'Mg '], '', $branch->branch_name));
                
                $query->where(function($q) use ($branchId, $branchSearch) {
                    $q->where('branch_name', 'LIKE', "%{$branchSearch}%");
                    
                    // Smart Fallback (ការពារទិន្នន័យចាស់ៗដែលប្រើអក្សរកាត់)
                    if ($branchId == 11) {
                        $q->orWhere('branch_name', 'LIKE', '%50m%')->orWhere('branch_name', 'LIKE', '%50 M%');
                    } elseif ($branchId == 10) {
                        $q->orWhere('branch_name', 'LIKE', '%btb%')
                          ->orWhere('branch_name', 'LIKE', '%battambang%');
                    } elseif ($branchId == 12) {
                        $q->orWhere('branch_name', 'LIKE', '%sr%')
                          ->orWhere('branch_name', 'LIKE', '%siem%');
                    } elseif ($branchId == 13) {
                        $q->orWhere('branch_name', 'LIKE', '%hq%')
                          ->orWhere('branch_name', 'LIKE', '%head%');
                    }
                });
            }
        }

        // 🔍 Filter តាមខែ និង ឆ្នាំ
        if ($request->filled('year')) { 
            $query->whereYear('created_at', $request->year); 
        }
        if ($request->filled('month') && $request->month !== 'all') { 
            $query->whereMonth('created_at', $request->month); 
        }

        // ✅ ទាញយកទិន្នន័យសាខាទាំងអស់សម្រាប់ Dropdown Filter របស់ Admin
        $branches = BranchModel::all();

        // 📍 ១. Leads តាមខេត្ត
        $provinceStats = (clone $query)->select('location', DB::raw('count(*) as total'))
            ->whereNotNull('location')->where('location', '!=', '')
            ->groupBy('location')->orderBy('total', 'desc')->get();

        // 🚗 ២. Leads តាមម៉ូដែលឡាន (សម្រាប់ Chart)
        $modelStats = (clone $query)->select('model', DB::raw('count(*) as total'))
            ->whereNotNull('model')->where('model', '!=', '')
            ->groupBy('model')->orderBy('total', 'desc')->get();

        // 👤 ៣. Leads តាមអ្នកលក់ (Top 15 សម្រាប់ Bar Chart)
        $salesStats = (clone $query)->select('sales_person', DB::raw('count(*) as total'))
            ->whereNotNull('sales_person')->where('sales_person', '!=', '')
            ->groupBy('sales_person')->orderBy('total', 'desc')->take(15)->get();

        // 📊 ៤. គណនា Sales Activity Progress (%) សម្រាប់អ្នកលក់ម្នាក់ៗ
        $salesActivityProgress = (clone $query)
            ->select(
                'sales_person as name', 
                DB::raw('count(*) as total_leads'),
                DB::raw('MAX(branch_name) as branch_name'), 
                DB::raw('MAX(created_at) as created_at'),   
                DB::raw('MAX(remind_date) as remind_date'), 
                DB::raw("ROUND(AVG($statusCases)) as avg_percentage") 
            )
            ->whereNotNull('sales_person')
            ->where('sales_person', '!=', '')
            ->groupBy('sales_person')
            ->orderBy('avg_percentage', 'desc')
            ->get()
            ->map(function ($item) {
                // កំណត់ពណ៌ដោយស្វ័យប្រវត្តិ (Auto-generate Colors) សម្រាប់អ្នកលក់ម្នាក់ៗ
                $colors = ['#4f46e5', '#10b981', '#f59e0b', '#ec4899', '#3b82f6', '#8b5cf6', '#06b6d4'];
                $item->color = $colors[abs(crc32($item->name)) % count($colors)];
                return $item;
            });

        // 🌐 ៥. ប្រភពអតិថិជន (Top Lead Sources)
        $sourceStats = (clone $query)->select('source', DB::raw('count(*) as total'))
            ->whereNotNull('source')->where('source', '!=', '')
            ->groupBy('source')->orderBy('total', 'desc')->limit(5)->get();

        // 🏆 ៦. ម៉ូដែលលក់ដាច់ជាងគេ (Best Selling Models - Only Sales & Delivery)
        $soldModelStats = (clone $query)->select('model', DB::raw('count(*) as total'))
            ->whereNotNull('model')->where('model', '!=', '')
            ->where(function($q) {
                $q->where(DB::raw('LOWER(status)'), 'sales ( contract of sales )')
                  ->orWhere(DB::raw('LOWER(status)'), 'delivery');
            })
            ->groupBy('model')->orderBy('total', 'desc')->get();

        $topModelStats = (clone $modelStats)->take(5);

        // ៧. រាប់ចំនួនសម្រាប់កាត (Summary Cards)
        $countBranch = (clone $query)->distinct('branch_name')->whereNotNull('branch_name')->count('branch_name');
        $countProvince = $provinceStats->count();
        $countBrand = 1; // ឧទាហរណ៍ថាយើងផ្តោតលើ Brand ធំតែមួយសិន
        $countModel = $modelStats->count();
        $totalLeads = (clone $query)->count();

        // ✅ បញ្ជូនទិន្នន័យទាំងអស់ទៅកាន់ View
        return view('layouts.dashboard', compact(
            'branches', 
            'provinceStats', 'modelStats', 'salesStats', 'sourceStats', 
            'soldModelStats', 'topModelStats', 'countBranch', 'countProvince', 'countBrand', 
            'countModel', 'totalLeads', 'salesActivityProgress'
        ));
    }

    // 🌟 មុខងារទាញយក Notification Lead ថ្មីប្រចាំថ្ងៃ (បាន Update ការពារ Error 500)
    public function getLeadNotifications()
    {
        try {
            $user = auth('users')->user();
            if (! $user) {
                return response()->json(['count' => 0, 'data' => []]);
            }

            $roleId = (int) $user->role_id;
            
            // ទាញយកតែ Lead ដែលបង្កើតថ្ងៃនេះ និងមិនទាន់លុប
            $query = \App\Models\Lead::whereDate('created_at', \Carbon\Carbon::today())
                                     ->where(function($q) {
                                         $q->whereNull('is_archived')->orWhere('is_archived', 0);
                                     });

            // អនុវត្តសិទ្ធិដូចដើម (Role Base Access)
            if (!in_array($roleId, [1, 2, 6, 11])) {
                if (in_array($roleId, [3, 4, 5, 7, 8])) {
                    $branchName = \App\Models\BranchModel::where('id', $user->branch_id)->value('branch_name');
                    $subordinates = \App\Models\User::where('branch_id', $user->branch_id)->pluck('full_name')->toArray();
                    
                    $query->where(function ($q) use ($branchName, $subordinates) {
                        if ($branchName) {
                            $searchName = trim(str_ireplace(['MG ', 'Mg '], '', $branchName));
                            $q->where('branch_name', 'LIKE', "%{$searchName}%");
                        }
                        if (!empty($subordinates)) {
                            $q->orWhereIn('sales_person', $subordinates);
                        }
                    });
                } else {
                    $query->where('sales_person', $user->full_name);
                }
            }

            // 🌟 ១. ត្រូវរាប់ចំនួន (Count) មុនពេលយើងកាត់យកត្រឹម ១០ (Limit 10)
            $totalCount = (clone $query)->count();
            
            $notifications = $query->orderBy('id', 'desc')->limit(10)->get();
            
            $data = $notifications->map(function ($lead) {
                // 🌟 ២. ការពារ Error Fatal ពេល created_at ជា String (អត្ថបទ)
                $timeString = 'ថ្មីៗនេះ';
                if ($lead->created_at) {
                    $timeString = \Carbon\Carbon::parse($lead->created_at)->diffForHumans();
                }

                return [
                    'id' => $lead->id, 
                    'customer' => $lead->customer_name ?? 'គ្មានឈ្មោះ', 
                    'phone' => $lead->contact_info ?? 'N/A',
                    'model' => $lead->model ?? '',
                    'time' => $timeString
                ];
            });

            return response()->json([
                'count' => $totalCount, 
                'data' => $data
            ]);
            
        } catch (\Throwable $e) { 
            // 🌟 ៣. ប្តូរពី \Exception ទៅ \Throwable ដើម្បីចាប់ Error លំដាប់ធ្ងន់
            // បើនៅតែ Error ទៀត វានឹងបោះ JSON មកប្រាប់យ៉ាងច្បាស់ថាខុស File មួយណា និងបន្ទាត់ទីប៉ុន្មាន!
            return response()->json([
                'count' => 0, 
                'data' => [], 
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500); 
        }
    }
}