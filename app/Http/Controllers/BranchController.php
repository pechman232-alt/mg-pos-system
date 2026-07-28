<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BranchController extends Controller
{
    public function index()
    {
        try {
            // ១. ទាញទិន្នន័យសាខាពី Table branches (ឬទីតាំងសាខា)
            $branches = DB::table('branches')->get();

            // ២. រៀបចំទម្រង់ទិន្នន័យ និងរាប់ចំនួន Bookings ពី Table 'service_booking'
            $formattedBranches = $branches->map(function ($branch) {
                
                // 🌟 ចាប់ផ្តើមរាប់ទិន្នន័យពី Table: service_booking តាម branch_id
                $bookingsQuery = DB::table('service_booking')
                                   ->where('branch_id', $branch->id);

                // រាប់ចំនួនការកក់សរុបទូទៅ
                $totalCount = $bookingsQuery->count();
                
                // 🌟 រាប់បំបែកតាម Status (ផ្អែកតាមរូបភាព DB របស់បង)
                // ប្រើប្រាស់ clone ដើម្បីកុំឱ្យ Query វាត្រួតគ្នា
                $pendingCount = (clone $bookingsQuery)
                                ->whereIn('status', ['Pending', 'pending'])->count();
                                
                $confirmedCount = (clone $bookingsQuery)
                                ->whereIn('status', ['Arrived', 'arrived', 'Confirmed', 'In Progress'])->count();
                                
                $completedCount = (clone $bookingsQuery)
                                ->whereIn('status', ['Completed', 'completed', 'Done'])->count();

                return [
                    'id'              => $branch->id,
                    'name'            => $branch->branch_name ?? $branch->name ?? 'មិនបញ្ជាក់ឈ្មោះ', 
                    'address'         => $branch->address ?? 'មិនមានអាសយដ្ឋាន',
                    'type'            => in_array($branch->id, [13]) ? 'hq' : 'branch', 
                    
                    // 🌟 បោះទិន្នន័យ Count នេះទៅឱ្យ Flutter (ត្រូវតែមាន Field ទាំងនេះ)
                    'total_count'     => $totalCount,
                    'pending_count'   => $pendingCount,
                    'confirmed_count' => $confirmedCount,
                    'completed_count' => $completedCount,
                    
                    // ទុក Back up ការពារ Error
                    'bookings_count'  => $totalCount,
                    'records'         => $totalCount,
                    'staff'           => 0,
                ];
            });

            // ៣. បោះ JSON ទៅ Flutter
            return response()->json([
                'success' => true,
                'data'    => $formattedBranches
            ], 200, [], JSON_UNESCAPED_UNICODE);

        } catch (\Exception $e) {
            // បើមាន Error វានឹងបោះសារនេះទៅឱ្យ Flutter
            return response()->json([
                'success' => false,
                'message' => 'DB Error: ' . $e->getMessage()
            ], 500);
        }
    }
}