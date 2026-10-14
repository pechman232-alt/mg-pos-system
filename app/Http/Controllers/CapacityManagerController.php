<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceBooking;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CapacityManagerController extends Controller
{
    private function getQuotaConfig($branchId, $dateStr, $timeStr) {
        $date = Carbon::parse($dateStr);
        if ($date->isSunday()) return ['total' => 0, 'walkin' => 0];
        
        $isSat = $date->isSaturday();
        $timePrefix = Carbon::parse($timeStr)->format('H:i');

        if ($branchId == 13) { // HQ
            if ($isSat) return ['total' => 4, 'walkin' => 2];
            return ['total' => 7, 'walkin' => 5];
        } 
        elseif ($branchId == 10) { // BTB
            if ($isSat) return in_array($timePrefix, ['08:00', '10:00']) ? ['total' => 2, 'walkin' => 1] : ['total' => 0, 'walkin' => 0];
            return ['total' => 2, 'walkin' => 1];
        } 
        else { // PP2 (11) & SR (12)
            if ($isSat) return in_array($timePrefix, ['08:00', '10:00']) ? ['total' => 3, 'walkin' => 2] : ['total' => 0, 'walkin' => 0];
            return ['total' => 3, 'walkin' => 2];
        }
    }

    public function index(Request $request)
    {
        $selectedDate = $request->input('date', Carbon::now('Asia/Phnom_Penh')->format('Y-m-d'));
        $user = auth()->user();
        
        $isGlobalManager = $user && in_array((int)$user->role_id, [1, 6, 11]);
        $targetBranchId = 13; // Default HQ

        if (!$isGlobalManager && $user) {
            $roleId = (int)$user->role_id;
            if ($roleId === 16) $targetBranchId = 13; // HQ
            elseif ($roleId === 10) $targetBranchId = 11; // 50M
            elseif ($roleId === 14) $targetBranchId = 12; // SR
            elseif ($roleId === 15) $targetBranchId = 10; // BTB
            else $targetBranchId = $user->branch_id;
        } else {
            if ($request->filled('branch')) {
                $targetBranchId = $request->branch;
            }
        }

        $branch = DB::table('branches')->where('id', $targetBranchId)->first();
        $branchNameClean = $branch ? str_ireplace(['MG ', 'Mg '], '', $branch->branch_name) : 'HQ';

        // 🌟 កំណត់ម៉ោងសរុប (Capacity Hours) តាមសាខា 🌟
        // HQ=15នាក់(120h), PP2=4នាក់(32h), SR=4នាក់(32h), BTB=3នាក់(24h)
        $branchTechConfig = [
            13 => ['techs' => 15, 'hours' => 120],
            11 => ['techs' => 4,  'hours' => 32],
            12 => ['techs' => 4,  'hours' => 32],
            10 => ['techs' => 3,  'hours' => 24],
        ];
        $techQty = $branchTechConfig[$targetBranchId]['techs'] ?? 4;
        $maxCapacityHours = $branchTechConfig[$targetBranchId]['hours'] ?? 32;

        $slots = ['08:00:00', '10:00:00', '13:00:00', '15:00:00'];
        $displaySlots = ['08:00:00' => '08:00 AM', '10:00:00' => '10:00 AM', '13:00:00' => '01:00 PM', '15:00:00' => '03:00 PM'];

        $bookings = ServiceBooking::whereDate('booking_date', $selectedDate)
            ->where(function($q) use ($targetBranchId, $branchNameClean) {
                $q->where('branch_id', $targetBranchId)
                  ->orWhere('location', $targetBranchId)
                  ->orWhere('location', 'LIKE', '%' . $branchNameClean . '%');
            })
            ->whereNotIn('status', ['Cancel', 'Cancelled', 'Archive', 'Archived'])
            ->get();

        $slotData = [];
        $dailyTotals = [
            'total_bay_quota' => 0, 'total_walkin_quota' => 0, 
            'total_booked' => 0, 'walkin_booked' => 0, 'online_booked' => 0,
            'total_booked_hours' => 0 // ម៉ោងកក់សរុប
        ];

        // ម៉ោងប៉ាន់ស្មាន
        $serviceDurations = [
            'Engine Malfunction' => 5, 'Check Vibration' => 5, 'General Inspection' => 1.5,
            'Check Noise' => 3.5, 'Interval Maintenance' => 1.5, 'Service 20000KM' => 3,
            'Service 40000KM' => 4, 'Service 50000KM' => 4, 'Service 60000KM' => 5,
            'Service 80000KM' => 6, 'Service 100000KM' => 6, 'General Repair' => 3,
            'Body and Paint' => 8, 'Warranty' => 2
        ];

        foreach ($slots as $time) {
            $quota = $this->getQuotaConfig($targetBranchId, $selectedDate, $time);
            
            $slotBookings = $bookings->filter(function($b) use ($time, $displaySlots, $serviceDurations, &$dailyTotals) {
                if (empty($b->booking_time)) return false;
                $dbTime = Carbon::parse($b->booking_time)->format('H:i:s');
                $isMatch = ($dbTime === $time || $dbTime === Carbon::parse($displaySlots[$time])->format('H:i:s'));
                
                if ($isMatch) {
                    $estHrs = $serviceDurations[$b->service_type] ?? 1.5;
                    if (in_array(strtolower($b->status), ['completed', 'close']) && $b->total_seconds) {
                        $estHrs = round($b->total_seconds / 3600, 2);
                    }
                    $b->est_duration = $estHrs; // ដាក់ម៉ោងទៅក្នុង object ឡាន
                    $dailyTotals['total_booked_hours'] += $estHrs;
                }
                return $isMatch;
            });

            $totalBooked = $slotBookings->count();
            $walkInBooked = $slotBookings->where('is_walk_in', 1)->count();
            $onlineBooked = $totalBooked - $walkInBooked;

            $slotData[$time] = [
                'display_time' => $displaySlots[$time],
                'quota_total' => $quota['total'],
                'quota_walkin' => $quota['walkin'],
                'booked_total' => $totalBooked,
                'booked_walkin' => $walkInBooked,
                'booked_online' => $onlineBooked,
                'is_closed' => $quota['total'] === 0,
                'bookings' => $slotBookings 
            ];

            $dailyTotals['total_bay_quota'] += $quota['total'];
            $dailyTotals['total_walkin_quota'] += $quota['walkin'];
            $dailyTotals['total_booked'] += $totalBooked;
            $dailyTotals['walkin_booked'] += $walkInBooked;
            $dailyTotals['online_booked'] += $onlineBooked;
        }

        $allBranches = DB::table('branches')->whereIn('id', [10, 11, 12, 13])->get();

        return view('CapacityManager.daily_tracking', compact(
            'selectedDate', 'slotData', 'targetBranchId', 'allBranches', 'isGlobalManager', 'dailyTotals', 'techQty', 'maxCapacityHours'
        ));
    }
}