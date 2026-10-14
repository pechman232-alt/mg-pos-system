<?php

namespace App\Http\Controllers;

use App\Models\EmployeeModel;
use App\Models\ServiceBooking;
use App\Models\User;
use App\Services\FirebaseService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ServiceBookingController extends Controller
{
    private function resolveId($id)
    {
        try {
            return decrypt($id);
        } catch (Exception $e) {
            return $id;
        }
    }

    private function applyBranchFilterScope($query, $branchId)
    {
        return $query->where(function ($q) use ($branchId) {
            $q->where('location', $branchId);
            if ($branchId == 11) {
                $q->orWhere('location', 'LIKE', '%50m%');
            } elseif ($branchId == 10) {
                $q->orWhere('location', 'LIKE', '%btb%')->orWhere('location', 'LIKE', '%battambang%');
            } elseif ($branchId == 12) {
                $q->orWhere('location', 'LIKE', '%sr%')->orWhere('location', 'LIKE', '%siem%');
            } elseif ($branchId == 13) {
                $q->orWhere('location', 'LIKE', '%hq%')->orWhere('location', 'LIKE', '%head%');
            }
        });
    }

    private function getCambodiaHolidays()
    {
        return [
            '2026-01-01', '2026-03-08', '2026-04-14', '2026-04-15', '2026-04-16',
            '2026-05-01', '2026-05-05', '2026-05-14', '2026-05-31', '2026-09-24',
            '2026-10-10', '2026-10-11', '2026-10-12', '2026-10-29', '2026-11-09',
            '2026-11-23', '2026-11-24', '2026-11-25',
        ];
    }

    private function calculateNetDurationSeconds($startStr, $endStr, $location)
    {
        if (empty($startStr) || empty($endStr)) {
            return 0;
        }

        $start = Carbon::parse($startStr);
        $end = Carbon::parse($endStr);

        if ($end->lessThan($start)) {
            $end->addDay(); // ការពារករណីឆ្លងថ្ងៃ
        }

        $totalSeconds = $start->diffInSeconds($end);
        $branchStr = strtolower(trim((string) $location));

        $isTuekThla = str_contains($branchStr, 'ទឹកថ្លា') || str_contains($branchStr, 'tuek thla') || str_contains($branchStr, 'hq') || str_contains($branchStr, 'head') || $location == 13;

        if (! $isTuekThla) {
            $lunchStart = $start->copy()->setTime(12, 0, 0);
            $lunchEnd = $start->copy()->setTime(13, 0, 0);

            if ($start < $lunchEnd && $end > $lunchStart) {
                $overlapStart = max($start->timestamp, $lunchStart->timestamp);
                $overlapEnd = min($end->timestamp, $lunchEnd->timestamp);
                $overlapSeconds = max(0, $overlapEnd - $overlapStart);
                $totalSeconds -= $overlapSeconds;
            }
        } else {
            $shiftStart = $start->copy()->setTime(11, 30, 0);
            $shiftEnd = $start->copy()->setTime(13, 30, 0);

            if ($start < $shiftEnd && $end > $shiftStart && $totalSeconds >= 7200) {
                $totalSeconds -= 3600;
            }
        }

        return max(0, $totalSeconds);
    }

    public function publicCreate()
    {
        $bookingToken = Str::random(40);
        session()->put('booking_token', $bookingToken);

        return view('Services.public_booking', compact('bookingToken'));
    }

    public function showTicket($id)
    {
        $booking = ServiceBooking::findOrFail($this->resolveId($id));

        return view('Services.booking_success', compact('booking'));
    }

    public function showPublicTicket($id)
    {
        $booking = ServiceBooking::findOrFail($this->resolveId($id));

        return view('Services.show_completed_ticket', compact('booking'));
    }

    public function checkSlots(Request $request)
    {
        $date = $request->date;
        $location = $request->location;
        $holidays = $this->getCambodiaHolidays();

        if (in_array($date, $holidays)) {
            return response()->json(['isHoliday' => true, 'message' => 'សូមអភ័យទោស! ថ្ងៃនេះគឺជាថ្ងៃឈប់សម្រាកបុណ្យជាតិ។']);
        }

        $bookingDate = Carbon::parse($date);
        $dayOfWeek = $bookingDate->dayOfWeek;

        if ($dayOfWeek == 0) {
            return response()->json(['isHoliday' => true, 'message' => 'សូមអភ័យទោស! យើងបិទធ្វើការនៅថ្ងៃអាទិត្យ។']);
        }

        $branchId = null;
        $branchName = '';

        if (is_numeric($location)) {
            $branchId = $location;
            $branchObj = DB::table('branches')->where('id', $location)->first();
            $branchName = $branchObj ? strtoupper($branchObj->branch_name) : '';
        } else {
            $branchName = strtoupper($location);
        }

        $schedule = [];

        if ($branchId == 13 || str_contains($branchName, 'HQ') || str_contains($branchName, 'HEAD')) {
            if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {
                $schedule = ['08:00 AM' => 7, '10:00 AM' => 7, '01:00 PM' => 7, '03:00 PM' => 7];
            } elseif ($dayOfWeek == 6) {
                $schedule = ['08:00 AM' => 4, '10:00 AM' => 4, '01:00 PM' => 4, '03:00 PM' => 4];
            }
        } elseif ($branchId == 10 || str_contains($branchName, 'BTB') || str_contains($branchName, 'BATTAMBANG')) {
            if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {
                $schedule = ['08:00 AM' => 2, '10:00 AM' => 2, '01:00 PM' => 2, '03:00 PM' => 2];
            } else {
                $schedule = ['08:00 AM' => 2, '10:00 AM' => 2];
            }
        } else {
            if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {
                $schedule = ['08:00 AM' => 3, '10:00 AM' => 3, '01:00 PM' => 3, '03:00 PM' => 3];
            } else {
                $schedule = ['08:00 AM' => 3, '10:00 AM' => 3];
            }
        }

        $data = [];
        foreach ($schedule as $time => $limit) {
            $dbTime = Carbon::parse($time)->format('H:i:s');

            $count = ServiceBooking::whereDate('booking_date', $date)
                ->where(function ($q) use ($location, $branchId) {
                    if ($branchId) {
                        $q->where('location', $location)->orWhere('branch_id', $branchId);
                    } else {
                        $q->where('location', $location);
                    }
                })
                ->whereNotIn('status', ['Cancel', 'Cancelled'])
                ->where(function ($q) use ($time, $dbTime) {
                    $q->where('booking_time', $dbTime)->orWhere('booking_time', $time);
                })->count();

            $remaining = $limit - $count;
            $data[] = [
                'time' => $time,
                'remaining' => max(0, $remaining),
                'isFull' => $remaining <= 0,
            ];
        }

        return response()->json($data);
    }

    public function publicStore(Request $request)
    {
        // 🌟 1. Token Validation
        $clientToken = $request->header('X-DYNAMIC-TOKEN');
        $serverToken = session('booking_token');

        if (! $clientToken || $clientToken !== $serverToken) {
            return response()->json([
                'success' => false,
                'message' => 'Session ផុតកំណត់! សូម Refresh ទំព័រនេះរួចសាកល្បងម្តងទៀត។',
            ], 403);
        }

        session()->forget('booking_token');

        // 🌟 2. Honeypot check
        if ($request->filled('website')) {
            return response()->json(['success' => true, 'booking_id' => 0]);
        }

        // 🌟 3. Validation
        $request->validate([
            'customer_name' => 'required|string|max:150',
            'contact' => 'required|string|max:100',
            'plate' => 'required|string|max:50',
            'model' => 'required|string|max:150',
            'mileage' => 'required|numeric',
            'service_type' => 'required|string|max:150',
            'location' => 'required',
            'booking_date' => 'required|date',
            'booking_time' => 'required',
            'remark' => 'nullable|string|max:1000',
        ]);

        // 🌟 4. Holiday Check
        $holidays = $this->getCambodiaHolidays();
        if (in_array($request->booking_date, $holidays)) {
            return response()->json([
                'success' => false,
                'message' => 'សូមអភ័យទោស! ថ្ងៃដែលអ្នកជ្រើសរើសគឺជាថ្ងៃឈប់សម្រាកបុណ្យជាតិ។',
            ], 422);
        }

        try {
            // 🌟 5. Location & Time Setup
            $formattedTime = Carbon::parse($request->booking_time)->format('H:i:s');
            $locationValue = $request->location;
            $branchId = null;

            if (is_numeric($locationValue)) {
                $branch = DB::table('branches')->where('id', $locationValue)->first();
                if (! $branch) {
                    return response()->json(['success' => false, 'message' => 'ទិន្នន័យសាខាមិនត្រឹមត្រូវទេ!'], 422);
                }
                $locationValue = $branch->branch_name;
                $branchId = $branch->id;
            } else {
                return response()->json(['success' => false, 'message' => 'ទម្រង់សាខាមិនត្រឹមត្រូវទេ!'], 422);
            }

            // 🌟 6. Data Cleaning
            $cleanMileage = (int) preg_replace('/[^0-9]/', '', $request->mileage);

            // 🌟 7. Save Booking
            $booking = new ServiceBooking;
            $booking->fill([
                'customer_name' => trim($request->customer_name),
                'contact' => trim($request->contact),
                'plate' => strtoupper(trim($request->plate)),
                'model' => trim($request->model),
                'mileage' => $cleanMileage,
                'service_type' => trim($request->service_type),
                'remark' => trim($request->remark),
                'booking_date' => $request->booking_date,
                'booking_time' => $formattedTime,
                'location' => $locationValue,
                'branch_id' => $branchId,
                'status' => 'Pending',
                'is_walk_in' => 0,
                'booking_source' => 'Online Booking',
            ]);
            $booking->save();

            // 🌟 8. Alerts & Notifications
            try {
                if (method_exists($this, 'sendNewBookingAlert')) {
                    $this->sendNewBookingAlert($booking, '🌐 ប្រព័ន្ធ Online Booking (អតិថិជនកក់ខ្លួនឯង)');
                }
            } catch (\Throwable $e) {
                Log::error('Telegram Alert Error in PublicStore: '.$e->getMessage());
            }

            try {
                $targetStaff = User::whereIn('role_id', [1, 6, 11])->first();
                if ($targetStaff && $targetStaff->fcm_token && class_exists('\App\Services\FirebaseService')) {
                    $firebase = new FirebaseService;
                    $firebase->sendNotification(
                        $targetStaff->fcm_token,
                        'ការកក់ Online ថ្មី! 🌐',
                        "អតិថិជន: {$booking->customer_name} ({$booking->plate}) បានកក់សេវាកម្ម {$booking->service_type}"
                    );
                }
            } catch (\Throwable $e) {
                Log::error('Firebase Notification Error in PublicStore: '.$e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Booking successfully created!',
                'booking_id' => $booking->id,
            ]);

        } catch (\Throwable $e) {
            Log::error('Public Store Booking Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'បញ្ហាប្រព័ន្ធ! សូមព្យាយាមម្តងទៀត។',
            ], 500);
        }
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = ServiceBooking::query();

        $workshopBranchIds = [10, 11, 12, 13];
        $branches = DB::table('branches')->whereIn('id', $workshopBranchIds)->get();

        $isGlobalManager = $user && in_array((int) $user->role_id, [1, 6, 11]);

        if (! $isGlobalManager && $user) {
            $roleId = (int) $user->role_id;
            $targetBranchId = null;

            if ($roleId === 16) {
                $targetBranchId = 13;
            } elseif ($roleId === 10) {
                $targetBranchId = 11;
            } elseif ($roleId === 14) {
                $targetBranchId = 12;
            } elseif ($roleId === 15) {
                $targetBranchId = 10;
            } else {
                $targetBranchId = $user->branch_id;
            }

            if ($targetBranchId) {
                $query->where(function ($q) use ($targetBranchId) {
                    $q->where('branch_id', $targetBranchId)
                        ->orWhere('location', $targetBranchId);

                    $branchObj = DB::table('branches')->where('id', $targetBranchId)->first();
                    if ($branchObj) {
                        $q->orWhere('location', 'LIKE', '%'.$branchObj->branch_name.'%');
                    }
                });
            }
        } else {
            if ($request->filled('branch') && $request->branch !== 'all') {
                $selectedBranchId = $request->branch;
                $query->where(function ($q) use ($selectedBranchId) {
                    $q->where('branch_id', $selectedBranchId)
                        ->orWhere('location', $selectedBranchId);

                    $branchObj = DB::table('branches')->where('id', $selectedBranchId)->first();
                    if ($branchObj) {
                        $q->orWhere('location', 'LIKE', '%'.$branchObj->branch_name.'%');
                    }
                });
            }
        }

        $todayStr = Carbon::now('Asia/Phnom_Penh')->format('Y-m-d');
        $fromDate = $request->input('from_date', $todayStr);
        $toDate = $request->input('to_date', $todayStr);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'LIKE', "%{$search}%")
                    ->orWhere('plate', 'LIKE', "%{$search}%")
                    ->orWhere('contact', 'LIKE', "%{$search}%");
            });
        } else {
            $query->whereDate('booking_date', '>=', $fromDate)
                ->whereDate('booking_date', '<=', $toDate);
        }

        if ($request->ajax() && $request->filled('fetch_all_for_export')) {
            $exportData = clone $query;
            $exportData = $exportData->reorder()
                ->orderByRaw("DATE(booking_date) = '{$todayStr}' DESC")
                ->orderBy('booking_date', 'desc')
                ->orderBy('booking_time', 'asc')
                ->get();

            $formattedData = $exportData->map(function ($book) use ($branches) {
                $rawLoc = $book->location;
                $locObj = collect($branches)->firstWhere('id', $rawLoc);
                $displayLoc = $locObj ? str_ireplace(['MG ', 'Mg '], '', $locObj->branch_name) : str_ireplace(['MG ', 'Mg '], '', $rawLoc);

                $rawDurationStr = 'N/A';
                $netDurationStr = 'N/A';
                $lunchBreakStr = '0m';

                if (! empty($book->start_time) && ! empty($book->end_time)) {
                    try {
                        $dateStr = ! empty($book->booking_date) ? date('Y-m-d', strtotime($book->booking_date)) : date('Y-m-d');
                        $startStr = $dateStr.' '.trim($book->start_time);
                        $endStr = $dateStr.' '.trim($book->end_time);

                        $netSeconds = $this->calculateNetDurationSeconds($startStr, $endStr, $book->location);

                        $startObj = Carbon::parse($startStr);
                        $endObj = Carbon::parse($endStr);
                        if ($endObj->lessThan($startObj)) {
                            $endObj->addDay();
                        }
                        $rawSeconds = $startObj->diffInSeconds($endObj);

                        $rHours = floor($rawSeconds / 3600);
                        $rMinutes = floor(($rawSeconds % 3600) / 60);
                        $rawDurationStr = ($rHours > 0 ? $rHours.'h ' : '').$rMinutes.'m';

                        $nHours = floor($netSeconds / 3600);
                        $nMinutes = floor(($netSeconds % 3600) / 60);
                        $netDurationStr = ($nHours > 0 ? $nHours.'h ' : '').$nMinutes.'m';

                        if ($rawSeconds > $netSeconds) {
                            $lunchBreakStr = '1h (-1h)';
                        }
                    } catch (Exception $e) {
                    }
                }

                $statusKey = strtolower(trim((string) $book->status));
                $adminNoteText = $book->note ?? '';
                if (in_array($statusKey, ['completed', 'close'])) {
                    $adminNoteText = 'បានបញ្ចប់ (Close)';
                } elseif (in_array($statusKey, ['archived', 'archive'])) {
                    $adminNoteText = 'បានទុកក្នុងបណ្ណសារ (Archived)';
                } elseif (in_array($statusKey, ['cancel', 'cancelled'])) {
                    $adminNoteText = 'បានបោះបង់ (Cancelled)';
                }

                $estComplete = ! empty($book->estimated_end_datetime)
                                ? Carbon::parse($book->estimated_end_datetime)->format('d-M-Y h:i A')
                                : 'N/A';

                return [
                    $book->customer_name ?? 'N/A',
                    $book->contact ?? 'N/A',
                    $book->model ?? 'N/A',
                    $book->plate ?? 'N/A',
                    ! empty($book->mileage) ? number_format($book->mileage).' KM' : 'N/A',
                    $book->service_type ?? 'N/A',
                    $book->sa_name ?? 'N/A',
                    $book->technician_name ?? 'N/A',
                    ucwords(trim($displayLoc) ?: 'N/A'),
                    (! empty($book->booking_date) ? date('d-M-Y', strtotime($book->booking_date)) : 'N/A').' '.(! empty($book->booking_time) ? date('h:i A', strtotime($book->booking_time)) : ''),
                    $estComplete,
                    str_ireplace('Waiting Parts Order', 'Parts', $book->status ?? 'Pending'),
                    ! empty($book->start_time) ? date('h:i A', strtotime($book->start_time)) : '-',
                    ! empty($book->end_time) ? date('h:i A', strtotime($book->end_time)) : '-',
                    $rawDurationStr,
                    $lunchBreakStr,
                    $netDurationStr,
                    $book->remark ?? '-',
                    $adminNoteText ?: '-',
                ];
            });

            return response()->json(['success' => true, 'data' => $formattedData]);
        }

        $allBookingsForStats = clone $query;
        $allBookingsForStats = $allBookingsForStats->get();

        $stats = ['total' => 0, 'pending' => 0, 'waiting' => 0, 'progress' => 0, 'completed' => 0, 'cancel' => 0, 'archive' => 0, 'overdue' => 0];
        $activeCat = $request->input('category', 'all');
        $filteredIds = [];

        $nowTs = Carbon::now('Asia/Phnom_Penh')->timestamp;

        foreach ($allBookingsForStats as $book) {
            $stats['total']++;
            $statusKey = strtolower(trim((string) ($book->status ?? '')));
            $isOverdue = false;
            $bookCategory = 'pending';

            if (in_array($statusKey, ['pending', 'waiting approved', 'waiting', 'waiting service'])) {
                try {
                    $bDate = Carbon::parse($book->booking_date)->format('Y-m-d');
                    $bTime = ! empty($book->booking_time) ? Carbon::parse($book->booking_time)->format('H:i:s') : '00:00:00';
                    $bookDateTime = Carbon::parse($bDate.' '.$bTime, 'Asia/Phnom_Penh');

                    if ($bookDateTime->isPast()) {
                        $isOverdue = true;
                    }
                } catch (Exception $e) {
                }
            }

            if ($isOverdue) {
                $stats['overdue']++;
                $bookCategory = 'overdue';
            } else {
                if (in_array($statusKey, ['pending', 'waiting approved'])) {
                    $stats['pending']++;
                    $bookCategory = 'pending';
                } elseif (in_array($statusKey, ['waiting', 'waiting service', 'waiting parts order local', 'waiting parts order dhl', 'waiting parts order by sea', 'waiting delivery', 'arrived'])) {
                    $stats['waiting']++;
                    $bookCategory = 'waiting';
                } elseif (in_array($statusKey, ['in progress', 'wip', 'under repair', 'under diagnosis'])) {
                    $stats['progress']++;
                    $bookCategory = 'progress';
                } elseif (in_array($statusKey, ['completed', 'close'])) {
                    $stats['completed']++;
                    $bookCategory = 'completed';
                } elseif (in_array($statusKey, ['cancel', 'cancelled'])) {
                    $stats['cancel']++;
                    $bookCategory = 'cancel';
                } elseif (in_array($statusKey, ['archive', 'archived'])) {
                    $stats['archive']++;
                    $bookCategory = 'archive';
                }
            }

            if ($activeCat === 'all' || $activeCat === 'total') {
                if (! in_array($bookCategory, ['cancel', 'archive', 'overdue', 'completed'])) {
                    $filteredIds[] = $book->id;
                }
            } else {
                if ($activeCat === $bookCategory) {
                    $filteredIds[] = $book->id;
                }
            }
        }

        $nowDateTimeStr = Carbon::now('Asia/Phnom_Penh')->format('Y-m-d H:i:s');

        $bookings = ServiceBooking::whereIn('id', count($filteredIds) > 0 ? $filteredIds : [0])
            ->orderByRaw("DATE(booking_date) = '{$todayStr}' DESC")
            ->orderByRaw("CASE WHEN status IN ('completed', 'close', 'cancel', 'cancelled', 'archive', 'archived') THEN 1 ELSE 0 END ASC")
            ->orderByRaw("ABS(UNIX_TIMESTAMP(CONCAT(booking_date, ' ', booking_time)) - UNIX_TIMESTAMP('{$nowDateTimeStr}')) ASC")
            ->paginate(15)
            ->withQueryString();

        return view('Services.services_index', compact('bookings', 'stats', 'branches', 'fromDate', 'toDate'));
    }

    public function updateEstimation(Request $request, $id)
    {
        try {
            $realId = decrypt($id);
            $booking = ServiceBooking::findOrFail($realId);

            $user = auth()->user();
            $userName = $user->name ?? $user->username ?? $user->first_name ?? 'Admin (ID: '.($user->id ?? 0).')';

            $history = $booking->estimation_history;
            if (is_string($history)) {
                $history = json_decode($history, true);
            }
            if (is_string($history)) {
                $history = json_decode($history, true);
            } // Double encode check
            if (! is_array($history)) {
                $history = [];
            }

            $history[] = [
                'estimated_datetime' => $request->new_estimated_datetime,
                'remark' => $request->estimation_remark,
                'updated_by' => $userName,
                'updated_at' => now()->toDateTimeString(),
            ];

            $booking->estimation_history = json_encode($history);
            $booking->estimated_end_datetime = $request->new_estimated_datetime;
            $booking->save();

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function dailyReport(Request $request)
    {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $selectedStatus = $request->input('status', 'pending_wip');
        $selectedBranch = $request->input('branch', 'all');

        $query = ServiceBooking::query();

        $user = auth()->user();
        $isGlobalManager = $user && in_array((int) $user->role_id, [1, 6, 11]);

        // 1. Branch Filtering Logic
        if (! $isGlobalManager && $user) {
            $roleId = (int) $user->role_id;
            $targetBranchId = null;

            if ($roleId === 16) { $targetBranchId = 13; } // HQ
            elseif ($roleId === 10) { $targetBranchId = 11; } // 50M
            elseif ($roleId === 14) { $targetBranchId = 12; } // SR
            elseif ($roleId === 15) { $targetBranchId = 10; } // BTB
            else { $targetBranchId = $user->branch_id; }

            if ($targetBranchId) {
                $query->where(function ($q) use ($targetBranchId) {
                    $q->where('branch_id', $targetBranchId)
                        ->orWhere('location', $targetBranchId);

                    $branchObj = DB::table('branches')->where('id', $targetBranchId)->first();
                    if ($branchObj) {
                        $q->orWhere('location', 'LIKE', '%'.$branchObj->branch_name.'%');
                    }
                });
            }
        } else {
            if ($selectedBranch !== 'all') {
                $query->where(function ($q) use ($selectedBranch) {
                    $q->where('location', 'LIKE', '%'.$selectedBranch.'%');

                    $str = strtolower($selectedBranch);
                    if (str_contains($str, '50m') || $str == '11') {
                        $q->orWhere('location', 'LIKE', '%50m%')->orWhere('location', '11')->orWhere('branch_id', 11);
                    } elseif (str_contains($str, 'hq') || str_contains($str, 'head') || $str == '13') {
                        $q->orWhere('location', 'LIKE', '%hq%')->orWhere('location', 'LIKE', '%head%')->orWhere('location', '13')->orWhere('branch_id', 13);
                    } elseif (str_contains($str, 'btb') || str_contains($str, 'battambang') || $str == '10') {
                        $q->orWhere('location', 'LIKE', '%btb%')->orWhere('location', 'LIKE', '%battambang%')->orWhere('location', '10')->orWhere('branch_id', 10);
                    } elseif (str_contains($str, 'sr') || str_contains($str, 'siem') || $str == '12') {
                        $q->orWhere('location', 'LIKE', '%sr%')->orWhere('location', 'LIKE', '%siem%')->orWhere('location', '12')->orWhere('branch_id', 12);
                    }
                });
            }
        }

        // 2. Date & Status Filters
        if (! empty($fromDate) && ! empty($toDate)) {
            $query->whereDate('booking_date', '>=', $fromDate)
                ->whereDate('booking_date', '<=', $toDate);
        }

        if ($selectedStatus !== 'all') {
            if ($selectedStatus === 'pending') {
                $query->whereIn('status', ['Pending', 'Waiting Approved', 'Waiting', 'Waiting Parts Order Local', 'Waiting Parts Order DHL', 'Waiting Parts Order by Sea', 'Waiting Delivery']);
            } elseif ($selectedStatus === 'wip') {
                $query->whereIn('status', ['In Progress', 'WIP', 'Under Repair', 'Under Diagnosis']);
            } elseif ($selectedStatus === 'pending_wip') {
                $query->whereNotIn('status', ['Completed', 'Close', 'Cancel', 'Cancelled', 'Archive', 'Archived']);
            } else {
                $query->where('status', $selectedStatus);
            }
        }

        $reports = $query->orderBy('booking_date', 'desc')
            ->orderBy('booking_time', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($request->ajax() && $request->has('fetch_all_for_export')) {
            return response()->json(['success' => true, 'data' => $reports]);
        }

        // 🌟 3. រៀបចំទិន្នន័យវិភាគ (បំបែក Completed និង គណនា Aging ជាចំនួនគត់) 🌟
        
        $branchSummaries = [];
        $globalSummary = [
            'total_pending'     => 0,
            'total_wip'         => 0,
            'total_completed'   => 0, // បន្ថែម Completed សរុប
            'critical_delayed'  => 0, // រាប់តែឡានគាំងមិនទាន់ចប់
            'waiting_parts'     => 0,
            'waiting_approval'  => 0,
        ];

        $now = \Carbon\Carbon::now();

        foreach ($reports as $booking) {
            $branch = $booking->location ?? 'Unknown';
            $branch = ucwords(trim(str_ireplace(['MG ', 'Mg '], '', $branch)));

            $branchLower = strtolower($branch);
            $branchId = $booking->branch_id ?? '';

            if (str_contains($branchLower, '50m') || $branch == '11' || $branchId == 11) {
                $branch = 'Service 50M';
            } elseif (str_contains($branchLower, 'hq') || str_contains($branchLower, 'head') || $branch == '13' || $branchId == 13) {
                $branch = 'Service HQ';
            } elseif (str_contains($branchLower, 'btb') || str_contains($branchLower, 'battambang') || $branch == '10' || $branchId == 10) {
                $branch = 'Service BTB';
            } elseif (str_contains($branchLower, 'sr') || str_contains($branchLower, 'siem') || $branch == '12' || $branchId == 12) {
                $branch = 'Service SR';
            }

            $statusKey = strtolower(trim($booking->status));

            // កំណត់ស្ថានភាពច្បាស់លាស់
            $isCompleted = in_array($statusKey, ['completed', 'close', 'done']);
            $isWaitingParts = in_array($statusKey, ['waiting parts order local', 'waiting parts order dhl', 'waiting parts order by sea']);
            $isWaitingApproval = in_array($statusKey, ['waiting approved', 'pending', 'waiting']);
            $isWaiting = in_array($statusKey, ['pending', 'waiting approved', 'waiting', 'waiting parts order local', 'waiting parts order dhl', 'waiting parts order by sea', 'waiting delivery']);
            $isWip = in_array($statusKey, ['in progress', 'wip', 'under repair', 'under diagnosis']);

            // គណនាអាយុកាលជាចំនួនគត់ (Integer Days គ្មានកន្ទុយទសភាគ)
            $bookingDate = $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date) : \Carbon\Carbon::parse($booking->created_at);
            $agingDays = (int) $bookingDate->diffInDays($now);
            
            // Severity គិតតែលើឡានដែលមិនទាន់ Completed ប៉ុណ្ណោះ
            $severity = 'normal';
            if (!$isCompleted) {
                if ($agingDays >= 3 && $agingDays <= 7) { $severity = 'warning'; }
                if ($agingDays > 7) { 
                    $severity = 'critical'; 
                    $globalSummary['critical_delayed']++;
                }
            } else {
                $severity = 'completed'; // ប្រសិនបើចប់ហើយ មិនឱ្យចូល Critical ទេ
            }

            // បូកសរុប Global
            if ($isCompleted) {
                $globalSummary['total_completed']++;
            } else {
                if ($isWaiting) { $globalSummary['total_pending']++; }
                if ($isWip) { $globalSummary['total_wip']++; }
                if ($isWaitingParts) { $globalSummary['waiting_parts']++; }
                if ($isWaitingApproval) { $globalSummary['waiting_approval']++; }
            }

           
            $isDelayed = false;
            if (!$isCompleted) {
                if (empty($booking->estimated_end_datetime) || \Carbon\Carbon::parse($booking->estimated_end_datetime)->isPast()) {
                    $isDelayed = true;
                }
            }


            if (! isset($branchSummaries[$branch])) {
                $branchSummaries[$branch] = [
                    'waiting'   => 0, 
                    'wip'       => 0, 
                    'completed' => 0, 
                    'delayed'   => 0, 
                    'critical'  => 0, 
                    'total'     => 0, 
                    'cars'      => []
                ];
            }

            if ($isCompleted) {
                $branchSummaries[$branch]['completed']++;
            } else {
                if ($isWaiting) { $branchSummaries[$branch]['waiting']++; }
                if ($isWip) { $branchSummaries[$branch]['wip']++; }
                if ($isDelayed) { $branchSummaries[$branch]['delayed']++; }
                if ($severity === 'critical') { $branchSummaries[$branch]['critical']++; }
            }
            $branchSummaries[$branch]['total']++;

            $branchSummaries[$branch]['cars'][] = [
                'plate'         => $booking->plate ?? 'N/A',
                'customer'      => $booking->customer_name ?? 'N/A',
                'status'        => $booking->status,
                'aging_days'    => $agingDays,
                'severity'      => $severity,
                'is_completed'  => $isCompleted,
                'is_wip'        => $isWip,
                'is_delayed'    => $isDelayed,
                'est_complete'  => $booking->estimated_end_datetime ? \Carbon\Carbon::parse($booking->estimated_end_datetime)->format('d-M-Y h:i A') : 'N/A',
            ];
        }

        // រៀបចំបញ្ជីសាខា
        $rawBranches = ServiceBooking::whereNotNull('location')->pluck('location')->toArray();
        $cleanBranches = ['Service 50M', 'Service HQ', 'Service BTB', 'Service SR'];
        sort($cleanBranches);
        $allBranches = $cleanBranches;

        return view('Services.daily_report', compact(
            'reports', 'fromDate', 'toDate', 'selectedStatus', 'selectedBranch', 
            'branchSummaries', 'allBranches', 'globalSummary'
        ));
    }

    public function create()
    {
        if (auth()->check() && auth()->user()->role_id == 18) {
            abort(403, 'Access Denied! POS accounts do not have permission to access the booking creation page.');
        }

        return view('Services.add_services');
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required',
            'contact' => 'required',
            'plate' => 'required',
            'model' => 'required',
            'mileage' => 'required|numeric',
            'service_type' => 'required',
            'location' => 'required',
            'booking_date' => 'required|date',
            'booking_time' => 'required',
        ]);

        $holidays = $this->getCambodiaHolidays();
        if (in_array($request->booking_date, $holidays)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'ថ្ងៃដែលអ្នកជ្រើសរើសគឺជាថ្ងៃឈប់សម្រាកបុណ្យជាតិ។'], 422);
            }

            return back()->with('error', 'សូមអភ័យទោស! ថ្ងៃដែលអ្នកជ្រើសរើសគឺជាថ្ងៃឈប់សម្រាកបុណ្យជាតិ។')->withInput();
        }

        $cleanPlate = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $request->plate));

        $activeBooking = ServiceBooking::whereRaw("UPPER(REPLACE(REPLACE(plate, ' ', ''), '-', '')) = ?", [$cleanPlate])
            ->whereNotIn('status', [
                'completed', 'close', 'cancel', 'cancelled', 'archived', 'archive',
                'Completed', 'Close', 'Cancel', 'Cancelled', 'Archived', 'Archive',
            ])
            ->first();

        if ($activeBooking) {
            $errorMsg = 'សុំទោស! ស្លាកលេខរថយន្តនេះ កំពុងមានការកក់នៅក្នុងប្រព័ន្ធរួចហើយ។ សូមរង់ចាំការកក់ចាស់បញ្ចប់សិន។';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $errorMsg], 422);
            }

            return back()->with('error', $errorMsg)->withInput();
        }

        try {
            $formattedTime = Carbon::parse($request->booking_time)->format('H:i:s');
            $locationValue = $request->location;
            $branchId = is_numeric($locationValue) ? $locationValue : null;

            if ($branchId) {
                $branch = DB::table('branches')->where('id', $locationValue)->first();
                if ($branch) {
                    $locationValue = $branch->branch_name;
                }
            }

            $isForceBooking = $request->has('force_booking') && $request->force_booking == '1';

            if (! $isForceBooking) {
                $quota = $this->getWalkInQuota($locationValue, $request->booking_date, $formattedTime);

                $currentWalkInCount = ServiceBooking::whereDate('booking_date', $request->booking_date)
                    ->where(function ($q) use ($locationValue, $branchId) {
                        $q->where('location', $locationValue);
                        if ($branchId) {
                            $q->orWhere('branch_id', $branchId);
                        }
                    })
                    ->where('is_walk_in', 1)
                    ->whereNotIn('status', ['completed', 'close', 'cancel', 'cancelled', 'archive', 'archived'])
                    ->where('booking_time', $formattedTime)
                    ->count();

                if ($currentWalkInCount >= $quota) {
                    $timeDisplay = date('h:i A', strtotime($formattedTime));
                    $errorMsg = "សុំទោស! ម៉ោង {$timeDisplay} នេះពេញកូតាសម្រាប់ Walk-in ហើយ (ទទួលបានត្រឹម {$quota} ឡាន)។ សូមជ្រើសរើសម៉ោងផ្សេង ឬបើកមុខងារកក់ករណីពិសេស។";

                    if ($request->wantsJson() || $request->ajax()) {
                        return response()->json(['success' => false, 'message' => $errorMsg], 422);
                    }

                    return back()->with('error', $errorMsg)->withInput();
                }
            }

            $cleanMileage = (int) preg_replace('/[^0-9]/', '', $request->mileage);
            $bookingStatus = ($request->has('is_walk_in') && $request->is_walk_in == 1) ? 'Arrived' : 'Pending';

            $authUser = auth('users')->user() ?? auth()->user();
            $addedBy = $authUser ? ($authUser->name ?? $authUser->full_name ?? 'បុគ្គលិក MG') : 'បុគ្គលិក MG';

            $booking = new ServiceBooking;
            $booking->fill([
                'customer_name' => $request->customer_name,
                'contact' => $request->contact,
                'plate' => $cleanPlate,
                'model' => $request->model,
                'mileage' => $cleanMileage,
                'service_type' => $request->service_type,
                'booking_date' => $request->booking_date,
                'booking_time' => $formattedTime,
                'location' => $locationValue,
                'branch_id' => $branchId,
                'status' => $bookingStatus,
                'is_walk_in' => 1,
                'booking_source' => $request->booking_source,
                'arrived_date' => Carbon::now('Asia/Phnom_Penh'),
                'sa_name' => $addedBy,
            ]);

            $finalRemark = trim((string) $request->input('remark'));
            if ($isForceBooking) {
                $finalRemark = '[ករណីពិសេស] '.$finalRemark;
            }

            $booking->remark = trim($finalRemark);
            $booking->added_by = $addedBy;
            $booking->save();

            $this->sendNewBookingAlert($booking, "👨‍💻 <b>អ្នកបញ្ចូល (Walk-in):</b> {$addedBy}");

            try {
                $targetStaff = User::where('role', 'admin')->first();
                if ($targetStaff && $targetStaff->fcm_token) {
                    $firebase = new FirebaseService;
                    $firebase->sendNotification(
                        $targetStaff->fcm_token,
                        'ការកក់សេវាកម្មថ្មី! 📅',
                        "អតិថិជន: {$booking->customer_name} ({$booking->plate}) បានកក់សេវាកម្ម {$booking->service_type}"
                    );
                }
            } catch (Exception $e) {
                Log::error('Firebase Notification Error: '.$e->getMessage());
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'ការកក់របស់អ្នកទទួលបានជោគជ័យ!', 'booking_id' => $booking->id]);
            }

            return redirect()->route('services.index')->with('success', 'បន្ថែមការកក់ថ្មីជោគជ័យ!');
        } catch (Exception $e) {
            Log::error('Store Booking Error: '.$e->getMessage());
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'បញ្ហាប្រព័ន្ធ៖ '.$e->getMessage()], 500);
            }

            return back()->with('error', 'បញ្ហាប្រព័ន្ធ៖ '.$e->getMessage())->withInput();
        }
    }

    private function getWalkInQuota($location, $dateStr, $timeStr)
    {
        $date = Carbon::parse($dateStr);
        if ($date->isSunday()) {
            return 0;
        }

        $isSat = $date->isSaturday();
        $loc = strtolower(trim((string) $location));

        $isHQ = str_contains($loc, 'hq') || str_contains($loc, 'tuek thla') || str_contains($loc, 'ទឹកថ្លា') || $loc == '13';
        $isBTB = str_contains($loc, 'btb') || str_contains($loc, 'battambang') || str_contains($loc, 'បាត់ដំបង') || $loc == '10';

        $timePrefix = Carbon::parse($timeStr)->format('H:i');

        if ($isHQ) {
            return $isSat ? 2 : 5;
        } elseif ($isBTB) {
            if ($isSat) {
                return in_array($timePrefix, ['08:00', '10:00']) ? 1 : 0;
            }

            return 1;
        } else {
            if ($isSat) {
                return in_array($timePrefix, ['08:00', '10:00']) ? 2 : 0;
            }

            return 2;
        }
    }

    public function checkWalkinSlots(Request $request)
    {
        $date = $request->date;
        $location = $request->location;

        if (! $date || ! $location) {
            return response()->json(['success' => false, 'message' => 'Missing param']);
        }

        if (is_numeric($location)) {
            $branch = DB::table('branches')->where('id', $location)->first();
            $locationName = $branch ? $branch->branch_name : $location;
        } else {
            $locationName = $location;
        }

        $slots = ['08:00', '10:00', '13:00', '15:00'];
        $result = [];

        foreach ($slots as $slot) {
            $timeStr = $slot.':00';
            $quota = $this->getWalkInQuota($locationName, $date, $timeStr);

            $currentCount = ServiceBooking::whereDate('booking_date', $date)
                ->where(function ($q) use ($locationName, $location) {
                    $q->where('location', $locationName)->orWhere('branch_id', $location);
                })
                ->where('is_walk_in', 1)
                ->whereNotIn('status', ['completed', 'close', 'cancel', 'cancelled', 'archive', 'archived'])
                ->where('booking_time', $timeStr)
                ->count();

            $result[] = [
                'time' => $slot,
                'quota' => $quota,
                'booked' => $currentCount,
                'available' => max(0, $quota - $currentCount),
                'is_full' => $currentCount >= $quota,
            ];
        }

        return response()->json(['success' => true, 'data' => $result]);
    }

    public function edit($id)
    {
        $booking = ServiceBooking::findOrFail($this->resolveId($id));

        $adminRoles = [1, 2, 6, 11];
        $isAdmin = in_array(auth()->user()->role_id, $adminRoles);

        $targetBranchId = $booking->location ?? $booking->branch_id ?? auth()->user()->branch_id;

        $saQuery = User::where('role_id', 12)->where('status', 1);
        $techQuery = EmployeeModel::where('status', 1);

        $techPositionIds = [31, 151, 191, 193, 195, 196, 199, 200, 201, 202];

        if ($isAdmin) {
            $techQuery->whereIn('position_id', $techPositionIds);
        } else {
            $userBranch = auth()->user()->branch_id;

            $saQuery->where('branch_id', $userBranch);
            $techQuery->where('branch_id', $userBranch)
                ->whereIn('position_id', $techPositionIds);
        }

        $serviceAdvisors = $saQuery->pluck('full_name');
        $technicians = $techQuery->pluck('full_name');

        $currentStatus = $booking->status ? strtolower($booking->status) : '';
        $isLocked = in_array($currentStatus, ['completed', 'close', 'cancelled', 'archive', 'archived']);

        $currentUserName = auth()->user()->full_name ?? auth()->user()->name ?? auth()->user()->username ?? '';

        return view('Services.edit_services', compact('booking', 'serviceAdvisors', 'technicians', 'isLocked', 'currentUserName'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'customer_name' => 'required',
            'contact' => 'required',
            'plate' => 'required',
            'model' => 'required',
            'service_type' => 'required',
            'booking_date' => 'required|date',
            'booking_time' => 'required',
        ]);

        try {
            $booking = ServiceBooking::findOrFail($this->resolveId($id));

            $newStatus = $request->input('status', $booking->status);
            $arrivedDateToSave = $booking->arrived_date;

            if (in_array(strtolower(trim($newStatus)), ['arrived', 'in progress', 'wip', 'under repair', 'under diagnosis'])) {
                if (empty($booking->arrived_date)) {
                    $arrivedDateToSave = Carbon::now('Asia/Phnom_Penh');
                }
            }

            $booking->update([
                'customer_name' => $request->customer_name,
                'contact' => $request->contact,
                'plate' => strtoupper($request->plate),
                'model' => $request->model,
                'mileage' => $request->mileage,
                'service_type' => $request->service_type,

                'booking_date' => $request->booking_date,
                'booking_time' => $request->booking_time,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,

                'location' => $request->location ?? $booking->location,
                'sa_name' => $request->sa_name,
                'technician_name' => $request->technician_name,

                'status' => $newStatus,
                'arrived_date' => $arrivedDateToSave,
                'remark' => $request->remark,
                'note' => $request->note,
                'booking_source' => $request->booking_source,
            ]);

            return redirect()->route('services.index')->with('success', 'កែប្រែព័ត៌មានជោគជ័យ!');

        } catch (Exception $e) {
            Log::error('Update Booking Error: '.$e->getMessage());

            return back()->with('error', 'បញ្ហាប្រព័ន្ធ៖ '.$e->getMessage())->withInput();
        }
    }

    public function delete($id)
    {
        ServiceBooking::findOrFail($this->resolveId($id))->delete();

        return back()->with('success', 'លុបទិន្នន័យជោគជ័យ!');
    }

    public function workshopIndex(Request $request)
    {
        $user = auth('users')->user() ?? auth()->user();

        $workshopBranchIds = [10, 11, 12, 13];
        $branches = DB::table('branches')->whereIn('id', $workshopBranchIds)->get();

        $today = Carbon::now('Asia/Phnom_Penh')->format('Y-m-d');
        $selectedDate = $request->filled('date') ? $request->date : $today;
        $category = $request->input('category', 'all');

        $jobsQuery = ServiceBooking::query();

        $jobsQuery->where(function ($q) use ($selectedDate) {
            $q->whereDate('booking_date', $selectedDate);
        });

        $saStatsQuery = ServiceBooking::whereDate('booking_date', $selectedDate)->whereNotNull('sa_name');

        $roleId = (int) ($user->role_id ?? 0);
        $isManager = in_array($roleId, [1, 6, 11]);
        $currentBranchFilter = 'all';

        if ($isManager) {
            if ($request->filled('branch') && $request->branch !== 'all') {
                $currentBranchFilter = $request->branch;
                $jobsQuery->where(function ($q) use ($currentBranchFilter) {
                    $this->applyBranchFilterScope($q, $currentBranchFilter);
                });
                $saStatsQuery->where(function ($q) use ($currentBranchFilter) {
                    $this->applyBranchFilterScope($q, $currentBranchFilter);
                });
            }
        } else {
            $currentBranchFilter = $user->branch_id;
            $jobsQuery->where(function ($q) use ($currentBranchFilter) {
                $this->applyBranchFilterScope($q, $currentBranchFilter);
            });
            $saStatsQuery->where(function ($q) use ($currentBranchFilter) {
                $this->applyBranchFilterScope($q, $currentBranchFilter);
            });
        }

        $saByBranch = ['10' => [], '11' => [], '12' => [], '13' => []];
        $techByBranch = ['10' => [], '11' => [], '12' => [], '13' => []];
        $allStaffsFlat = [];
        $dbErrorMsg = '';

        $busyTechnicians = ServiceBooking::where('status', 'In Progress')
            ->whereNotNull('technician_name')
            ->pluck('technician_name')
            ->toArray();

        try {
            $tableName = Schema::hasTable('employees') ? 'employees' : 'employee';
            $targetDeptIds = [20, 48, 49, 50, 57, 52, 55, 56, 51];

            $employees = DB::table($tableName)->whereIn('department_id', $targetDeptIds)->get();

            foreach ($employees as $emp) {
                $empName = trim($emp->full_name ?? $emp->name ?? '');
                if (empty($empName)) {
                    continue;
                }

                $dId = (int) $emp->department_id;
                $allStaffsFlat[] = $empName;

                if ($dId == 48) {
                    $saByBranch['13'][] = $empName;
                } elseif ($dId == 49) {
                    $saByBranch['11'][] = $empName;
                } elseif ($dId == 50) {
                    $saByBranch['12'][] = $empName;
                } elseif ($dId == 57) {
                    $saByBranch['10'][] = $empName;
                } elseif ($dId == 52 || $dId == 20) {
                    $techByBranch['13'][] = $empName;
                } elseif ($dId == 55) {
                    $techByBranch['11'][] = $empName;
                } elseif ($dId == 56) {
                    $techByBranch['12'][] = $empName;
                } elseif ($dId == 51) {
                    $techByBranch['10'][] = $empName;
                }
            }
        } catch (Exception $e) {
            $dbErrorMsg = $e->getMessage();
            Log::error('Staff Fetch Error: '.$dbErrorMsg);
        }

        $allStaffsFlat = array_unique($allStaffsFlat);
        sort($allStaffsFlat);

        $employeesByBranch = [];
        foreach ($saByBranch as $bId => $sas) {
            $employeesByBranch[$bId] = array_unique($sas);
        }
        foreach ($techByBranch as $bId => $techs) {
            if (! isset($employeesByBranch[$bId])) {
                $employeesByBranch[$bId] = [];
            }
            $employeesByBranch[$bId] = array_unique(array_merge($employeesByBranch[$bId], $techs));
        }

        foreach ($saByBranch as $key => $arr) {
            $saByBranch[$key] = array_unique($arr);
            sort($saByBranch[$key]);
        }
        foreach ($techByBranch as $key => $arr) {
            $techByBranch[$key] = array_unique($arr);
            sort($techByBranch[$key]);
        }
        foreach ($employeesByBranch as $key => $arr) {
            sort($employeesByBranch[$key]);
        }

        if ($isManager) {
            $branchEmployees = ($currentBranchFilter !== 'all') ? ($employeesByBranch[$currentBranchFilter] ?? []) : $allStaffsFlat;
        } else {
            $branchEmployees = $employeesByBranch[(string) $user->branch_id] ?? [];
        }

        $jobsQuery->where(function ($q) use ($category) {
            if ($category === 'all') {
                $q->whereIn('status', ['Pending', 'Waiting Approved', 'Arrived', 'In Progress', 'Hold']);
            } else {
                $q->where('status', 'LIKE', "%{$category}%");
            }
        });

        $jobs = $jobsQuery
            ->orderByRaw("FIELD(status, 'In Progress', 'Arrived', 'Pending', 'Waiting Approved', 'Hold', 'Completed')")
            ->orderBy('booking_date', 'asc')
            ->orderBy('booking_time', 'asc')
            ->get();

        $saStats = $saStatsQuery->select('sa_name',
            DB::raw("COUNT(CASE WHEN status = 'Completed' THEN 1 END) as done"),
            DB::raw("COUNT(CASE WHEN status = 'In Progress' THEN 1 END) as doing"),
            DB::raw("COUNT(CASE WHEN status = 'Arrived' THEN 1 END) as arrived"),
            DB::raw("COUNT(CASE WHEN status = 'Pending' OR status = 'Hold' THEN 1 END) as wait")
        )->groupBy('sa_name')->get();

        return view('Services.workshop_board', compact(
            'jobs', 'saStats', 'branches', 'branchEmployees', 'employeesByBranch', 'allStaffsFlat', 'selectedDate', 'saByBranch', 'techByBranch', 'busyTechnicians', 'dbErrorMsg', 'category'
        ));
    }

    public function arriveJob(Request $request, $id)
    {
        try {
            $job = ServiceBooking::findOrFail($id);

            $today = Carbon::now('Asia/Phnom_Penh')->format('Y-m-d');
            $bookingDate = Carbon::parse($job->booking_date)->format('Y-m-d');

            if ($today < $bookingDate && ! $request->has('force_update')) {
                $formattedBooking = date('d-M-Y', strtotime($job->booking_date));
                $formattedToday = date('d-M-Y', strtotime($today));

                return response()->json([
                    'success' => false,
                    'is_early' => true,
                    'message' => "អតិថិជនមកដល់មុនថ្ងៃកក់!\n• ថ្ងៃកក់: {$formattedBooking}\n• ថ្ងៃមកដល់ (ថ្ងៃនេះ): {$formattedToday}\n\nតើអ្នកចង់ប្តូរថ្ងៃ Booking មកថ្ងៃនេះ ហើយបន្តដំណើរការទទួលសេវាកម្មដែរឬទេ?",
                ], 422);
            }

            if ($today < $bookingDate && $request->has('force_update')) {
                $job->booking_date = $today;
            }

            $job->status = 'Arrived';

            $authUser = auth('users')->user() ?? auth()->user();

            if ($request->has('assigned_sa') && ! empty($request->assigned_sa)) {
                $job->sa_name = $request->assigned_sa;
            } else {
                if (empty($job->sa_name) && $authUser) {
                    $job->sa_name = trim($authUser->name ?? $authUser->full_name ?? 'SA');
                }
            }

            try {
                $job->arrival_time = Carbon::now('Asia/Phnom_Penh');
            } catch (Exception $ex) {
            }

            $job->save();

            if (method_exists($this, 'sendTelegramStatusAlert')) {
                $this->sendTelegramStatusAlert($job);
            }

            return response()->json(['success' => true]);

        } catch (Exception $e) {
            Log::error('Arrive Job Error: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function startJob(Request $request, $id)
    {
        try {
            $job = ServiceBooking::findOrFail($id);

            $job->update([
                'booking_date' => $request->booking_date,
                'status' => 'In Progress',
                'start_time' => Carbon::now()->format('H:i:s'),
                'job_started_at' => Carbon::now(),
                'sa_name' => $request->sa_name,
                'technician_name' => $request->technician_name,
            ]);

            $this->sendTelegramStatusAlert($job);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function holdJob($id)
    {
        try {
            $booking = ServiceBooking::findOrFail($id);

            $updateData = ['status' => 'Hold'];

            if ($booking->job_started_at) {
                $startTime = Carbon::parse($booking->job_started_at);
                $secondsWorked = $startTime->diffInSeconds(Carbon::now());

                $updateData['total_seconds'] = $booking->total_seconds + $secondsWorked;
                $updateData['job_started_at'] = null;
            }

            $booking->update($updateData);
            $this->sendTelegramStatusAlert($booking);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function resumeJob($id)
    {
        try {
            $booking = ServiceBooking::findOrFail($id);

            $booking->update([
                'status' => 'In Progress',
                'job_started_at' => Carbon::now(),
            ]);

            $this->sendTelegramStatusAlert($booking);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function finishJob($id)
    {
        try {
            $job = ServiceBooking::findOrFail($id);

            $updateData = [
                'status' => 'Completed',
                'end_time' => now('Asia/Phnom_Penh')->format('H:i:s'),
            ];

            if ($job->job_started_at) {
                $startTime = Carbon::parse($job->job_started_at);
                $secondsWorked = $startTime->diffInSeconds(Carbon::now());

                $updateData['total_seconds'] = $job->total_seconds + $secondsWorked;
                $updateData['job_started_at'] = null;
            }

            $job->update($updateData);

            if (method_exists($this, 'sendTelegramStatusAlert')) {
                $this->sendTelegramStatusAlert($job);
            }

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            Log::error('Finish Job Error: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function updateNote(Request $request, $id)
    {
        try {
            $booking = ServiceBooking::findOrFail($this->resolveId($id));
            $booking->note = $request->note;
            $booking->save();

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function checkNewBooking(Request $request)
    {
        $lastId = $request->last_id;
        $user = auth()->user();
        $query = ServiceBooking::where('id', '>', $lastId);
        if ($user && ! in_array((int) $user->role_id, [1, 6, 11])) {
            $query->where('location', $user->branch_id);
        } $newBooking = $query->latest()->first();
        if ($newBooking) {
            return response()->json(['hasNew' => true, 'customer' => $newBooking->customer_name, 'plate' => $newBooking->plate, 'new_id' => $newBooking->id]);
        }

        return response()->json(['hasNew' => false]);
    }

    public function checkNewBookings()
    {
        try {
            $count = ServiceBooking::where('status', 'Pending')->count();

            return response()->json(['success' => true, 'new_bookings' => $count]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    public function getNotifications()
    {
        try {
            // Return an empty array for now to stop the 500 errors
            return response()->json([
                'success' => true,
                'data' => [],
                'count' => 0
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function performanceOverview(Request $request)
    {
        $user = auth()->user();
        $workshopBranchIds = [10, 11, 12, 13];
        $branches = DB::table('branches')->whereIn('id', $workshopBranchIds)->get();

        $fromDate = $request->input('from_date', Carbon::today('Asia/Phnom_Penh')->startOfMonth()->format('Y-m-d'));
        $toDate = $request->input('to_date', Carbon::today('Asia/Phnom_Penh')->format('Y-m-d'));

        $selectedDate = $toDate;
        $baseQuery = ServiceBooking::query();

        if ($user && ! in_array((int) $user->role_id, [1, 6, 11])) {
            $this->applyBranchFilterScope($baseQuery, $user->branch_id);
        } elseif ($request->filled('branch') && $request->branch !== 'all') {
            $this->applyBranchFilterScope($baseQuery, $request->branch);
        }

        $dailyStandardSeconds = 8 * 3600;

        $monthlyBookings = (clone $baseQuery)
            ->whereNotNull('technician_name')
            ->where('technician_name', '!=', '')
            ->where(function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('booking_date', [$fromDate, $toDate])
                    ->orWhereBetween('updated_at', [$fromDate.' 00:00:00', $toDate.' 23:59:59'])
                    ->orWhereBetween('created_at', [$fromDate.' 00:00:00', $toDate.' 23:59:59']);
            })
            ->get();

        $dailyCars = (clone $baseQuery)->whereDate('booking_date', $selectedDate)->get();
        $carsIn = $dailyCars->count();
        $carsDelivery = $dailyCars->whereIn('status', ['Completed', 'Close', 'completed', 'close'])->count();
        $todayPending = $dailyCars->whereNotIn('status', ['Completed', 'Close', 'Cancel', 'Archive', 'completed', 'close', 'cancel', 'archive'])->count();

        $allPendingCars = (clone $baseQuery)->whereDate('booking_date', '<=', $selectedDate)->whereNotIn('status', ['Completed', 'Close', 'Cancel', 'Archive', 'completed', 'close', 'cancel', 'archive'])->get();
        $totalPending = $allPendingCars->count();
        $totalOrderPartLocal = $allPendingCars->whereIn('status', ['Waiting Parts Order Local', 'waiting parts order local'])->count();
        $totalOrderPartSea = $allPendingCars->whereIn('status', ['Waiting Parts Order by Sea', 'waiting parts order by sea'])->count();

        $totalPartWarranty = 0;
        $totalPartRetail = 0;
        $saDailyStats = [];
        $saCarsInStats = [];
        $saDeliveryStats = [];

        foreach ($dailyCars->whereNotNull('sa_name')->where('sa_name', '!=', '')->groupBy('sa_name') as $sa => $cars) {
            $walkInCount = $cars->filter(function ($car) {
                return empty($car->booking_time);
            })->count();

            $bookingCount = $cars->count() - $walkInCount;

            $saDailyStats[$sa] = [
                'total' => $cars->count(),
                'booking' => $bookingCount,
                'walkin' => $walkInCount,
            ];

            $saCarsInStats[$sa] = $cars->count();
        }

        foreach ($dailyCars->whereIn('status', ['Completed', 'Close', 'completed', 'close'])->whereNotNull('sa_name')->where('sa_name', '!=', '')->groupBy('sa_name') as $sa => $cars) {
            $saDeliveryStats[$sa] = $cars->count();
        }

        $saPendingStats = [];
        foreach ($allPendingCars->whereNotNull('sa_name')->where('sa_name', '!=', '')->groupBy('sa_name') as $sa => $cars) {
            $saPendingStats[$sa] = $cars->count();
        }

        $pendingDescStats = [
            'Under Repair' => $allPendingCars->whereIn('status', ['In Progress', 'WIP', 'Under Repair', 'in progress', 'wip', 'under repair'])->count(),
            'Under Diagnostic' => $allPendingCars->whereIn('status', ['Under Diagnosis', 'under diagnosis'])->count(),
            'Waiting Part' => $allPendingCars->whereIn('status', ['Waiting Parts Order Local', 'Waiting Parts Order DHL', 'Waiting Parts Order by Sea'])->count(),
            'Waiting Approve' => $allPendingCars->whereIn('status', ['Waiting Approved', 'waiting approved'])->count(),
            'Waiting Delivery' => $allPendingCars->whereIn('status', ['Waiting Delivery', 'waiting delivery'])->count(),
            'Waiting QT' => 0,
            'Under BP' => 0,
        ];

        $branchPendingStats = [];
        foreach ($allPendingCars->groupBy('location') as $loc => $cars) {
            $locObj = collect($branches)->firstWhere('id', $loc);
            $displayLoc = $locObj ? str_ireplace(['MG ', 'Mg '], '', $locObj->branch_name) : $loc;
            $branchPendingStats[$displayLoc] = $cars->count();
        }

        $completedQuery = (clone $baseQuery)->whereIn('status', ['Completed', 'Close', 'completed', 'close']);
        if ($request->filled('to_date') || $request->filled('date') || $request->filled('from_date')) {
            $completedQuery->whereBetween('booking_date', [$fromDate, $toDate]);
        }

        $topSA = (clone $completedQuery)->select('sa_name', DB::raw('count(*) as total'))->whereNotNull('sa_name')->where('sa_name', '!=', '')->groupBy('sa_name')->orderBy('total', 'desc')->take(10)->get();
        $topTech = (clone $completedQuery)->select('technician_name', DB::raw('count(*) as total'))->whereNotNull('technician_name')->where('technician_name', '!=', '')->groupBy('technician_name')->orderBy('total', 'desc')->take(10)->get();
        $totalThroughput = (clone $completedQuery)->count();

        $techPerformances = [];
        $groupedByTech = $monthlyBookings->groupBy('technician_name');

        foreach ($groupedByTech as $techName => $bookings) {

            $calculateSeconds = function ($booking) {
                try {
                    $dateStr = ! empty($booking->booking_date)
                        ? Carbon::parse($booking->booking_date)->format('Y-m-d')
                        : date('Y-m-d');

                    if (! empty($booking->start_time) && ! empty($booking->end_time)) {
                        $startStr = $dateStr.' '.trim($booking->start_time);
                        $endStr = $dateStr.' '.trim($booking->end_time);

                        return $this->calculateNetDurationSeconds($startStr, $endStr, $booking->location ?? $booking->branch_id);
                    }

                    if (isset($booking->total_seconds) && (int) $booking->total_seconds > 0) {
                        return (int) $booking->total_seconds;
                    }

                } catch (Exception $e) {
                    Log::error('Performance Check Error: '.$e->getMessage());

                    return 0;
                }

                return 0;
            };

            $dailySeconds = $bookings->filter(function ($booking) use ($selectedDate) {
                try {
                    $dateToParse = $booking->booking_date ?? $booking->updated_at ?? $booking->created_at ?? now();

                    return Carbon::parse($dateToParse)->timezone('Asia/Phnom_Penh')->format('Y-m-d') === $selectedDate;
                } catch (Exception $e) {
                    return false;
                }
            })->sum($calculateSeconds);

            $dailyPercent = ($dailySeconds > 0) ? ($dailySeconds / $dailyStandardSeconds) * 100 : 0;
            $dailyOTSeconds = max(0, $dailySeconds - $dailyStandardSeconds);

            $activeDaysCount = $bookings->map(function ($booking) {
                $dateToParse = $booking->booking_date ?? $booking->updated_at ?? $booking->created_at;

                return $dateToParse ? Carbon::parse($dateToParse)->format('Y-m-d') : null;
            })->filter()->unique()->count();

            $activeDaysCount = $activeDaysCount > 0 ? $activeDaysCount : 1;
            $periodStandardSeconds = $activeDaysCount * $dailyStandardSeconds;

            $monthlySeconds = $bookings->sum($calculateSeconds);
            $monthlyPercent = ($monthlySeconds > 0 && $periodStandardSeconds > 0) ? ($monthlySeconds / $periodStandardSeconds) * 100 : 0;
            $monthlyOTSeconds = max(0, $monthlySeconds - $periodStandardSeconds);

            $techPerformances[] = (object) [
                'name' => $techName,

                'daily_actual_hours' => floor($dailySeconds / 3600),
                'daily_actual_minutes' => floor(($dailySeconds % 3600) / 60),
                'daily_percent' => $dailyPercent,
                'daily_bar_percent' => min(100, $dailyPercent),
                'daily_ot_hours' => floor($dailyOTSeconds / 3600),
                'daily_ot_minutes' => floor(($dailyOTSeconds % 3600) / 60),

                'monthly_percent' => $monthlyPercent,
                'monthly_actual_hours' => floor($monthlySeconds / 3600),
                'monthly_actual_minutes' => floor(($monthlySeconds % 3600) / 60),
                'monthly_ot_hours' => floor($monthlyOTSeconds / 3600),
                'monthly_ot_minutes' => floor(($monthlyOTSeconds % 3600) / 60),
            ];
        }

        return view('Services.performance_overview', compact(
            'branches', 'fromDate', 'toDate', 'selectedDate', 'carsIn', 'carsDelivery', 'todayPending', 'totalPending',
            'totalOrderPartLocal', 'totalOrderPartSea', 'totalPartWarranty', 'totalPartRetail',
            'saDailyStats', 'saCarsInStats', 'saDeliveryStats', 'saPendingStats', 'pendingDescStats',
            'branchPendingStats', 'topSA', 'topTech', 'totalThroughput', 'techPerformances'
        ));
    }

    public function cancelBooking($id)
    {
        try {
            $realId = decrypt($id);
        } catch (Exception $e) {
            $realId = $id;
        }
        $booking = ServiceBooking::findOrFail($realId);
        $booking->update(['status' => 'Cancel']);
        if (method_exists($this, 'sendTelegramStatusAlert')) {
            $this->sendTelegramStatusAlert($booking);
        }

        return back()->with('success', 'ការកក់ត្រូវបានបោះបង់ជោគជ័យ!');
    }

    public function archive($id)
    {
        try {
            $realId = decrypt($id);
        } catch (Exception $e) {
            $realId = $id;
        }
        $booking = ServiceBooking::findOrFail($realId);
        $booking->update(['status' => 'archive']);

        return back()->with('success', 'បញ្ជូនទៅកាន់បណ្ណសារជោគជ័យ!');
    }

    private function executeTelegramCurl($text, $locationData = null)
    {
        $chat_id = (string) env('TELEGRAM_CHAT_ID_DEFAULT', '');

        if (! empty($locationData)) {
            $loc = strtolower(trim((string) $locationData));
            if ($loc === '13' || strpos($loc, 'hq') !== false || strpos($loc, 'head') !== false) {
                $chat_id = (string) env('TELEGRAM_CHAT_ID_HQ', $chat_id);
            } elseif ($loc === '11' || strpos($loc, '50m') !== false || strpos($loc, '50') !== false) {
                $chat_id = (string) env('TELEGRAM_CHAT_ID_50M', $chat_id);
            } elseif ($loc === '12' || strpos($loc, 'sr') !== false || strpos($loc, 'siem') !== false) {
                $chat_id = (string) env('TELEGRAM_CHAT_ID_SR', $chat_id);
            } elseif ($loc === '10' || strpos($loc, 'btb') !== false || strpos($loc, 'battambang') !== false) {
                $chat_id = (string) env('TELEGRAM_CHAT_ID_BTB', $chat_id);
            }
        }

        if ($chat_id === '') {
            Log::warning('Telegram Chat ID is missing for location: '.$locationData);

            return;
        }

        $token = env('TELEGRAM_BOT_TOKEN');
        if (empty($token)) {
            Log::error('Telegram Bot Token is missing in .env');

            return;
        }

        $url = "https://api.telegram.org/bot{$token}/sendMessage";
        $data = [
            'chat_id' => $chat_id,
            'text' => $text,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch) || $httpCode !== 200) {
            Log::error('Telegram Send Error: '.curl_error($ch).' | HTTP Code: '.$httpCode);
        }

        curl_close($ch);
    }

    private function getDisplayBranch($location)
    {
        if (is_numeric($location)) {
            $branchName = DB::table('branches')->where('id', $location)->value('branch_name');

            return $branchName ? str_ireplace('MG ', '', $branchName) : $location;
        }

        return str_ireplace('MG ', '', $location ?? 'N/A');
    }

    private function sendNewBookingAlert($booking, $sourceText = null)
    {
        $displayBranch = $this->getDisplayBranch($booking->location);
        $date = date('d-M-Y', strtotime($booking->booking_date));
        $time = date('h:i A', strtotime($booking->booking_time));

        $mileage = ! empty($booking->mileage) ? number_format((float) $booking->mileage).' KM' : 'N/A';
        $remark = ! empty($booking->remark) ? htmlspecialchars($booking->remark) : 'None';

        $addedByText = $sourceText
            ? $sourceText."\n\n"
            : "🌐 <b>Source:</b> Online Booking\n\n";

        // Map service duration
        $serviceDurations = [
            'Engine Malfunction' => '4-6 ម៉ោង',
            'Check Vibration' => '4-6 ម៉ោង',
            'General Inspection' => '1.5 ម៉ោង',
            'Check Noise' => '3-4 ម៉ោង',
            'Interval Maintenance' => '1.5 ម៉ោង',
            'Service 20000KM' => '3 ម៉ោង',
            'Service 40000KM' => '4 ម៉ោង',
            'Service 50000KM' => '4 ម៉ោង',
            'Service 60000KM' => '5 ម៉ោង',
            'Service 80000KM' => '6 ម៉ោង',
            'Service 100000KM' => '6 ម៉ោង',
            'General Repair' => '3 ម៉ោង',
            'Body and Paint' => '8 ម៉ោង',
            'Warranty' => '2 ម៉ោង',
        ];

        $sType = $booking->service_type ?? '';
        $estDuration = $serviceDurations[$sType] ?? '1.5 ម៉ោង';

        $text = "🔔 <b>NEW SERVICE BOOKING | MG</b>\n"
              ."━━━━━━━━━━━━━━━━━━\n"
              .(! empty($booking->id)
                    ? '🆔 <b>Reference No.:</b> #MG-'.str_pad($booking->id, 4, '0', STR_PAD_LEFT)."\n"
                    : '')
              .$addedByText

              ."👤 <b>Customer Information</b>\n"
              ."├ Name: <b>{$booking->customer_name}</b>\n"
              ."└ Contact: <code>{$booking->contact}</code>\n\n"

              ."🚘 <b>Vehicle Information</b>\n"
              ."├ Model: <b>{$booking->model}</b>\n"
              ."├ License Plate: <code>{$booking->plate}</code>\n"
              ."└ Mileage: {$mileage}\n\n"

              ."🛠️ <b>Service Details</b>\n"
              ."├ Service Type: <b>{$booking->service_type}</b>\n"
              ."├ Est. Duration: <b>{$estDuration}</b>\n"
              ."├ Branch: <b>{$displayBranch}</b>\n"
              ."├ Appointment Date: <b>{$date}</b>\n"
              ."└ Appointment Time: <b>{$time}</b>\n\n"

              ."💬 <b>Customer Remarks</b>\n"
              ."<i>{$remark}</i>\n"

              ."━━━━━━━━━━━━━━━━━━\n"
              .'👉 Open Service Index';

        $this->executeTelegramCurl(
            $text,
            $booking->branch_id ?? $booking->location
        );
    }

    private function sendTelegramStatusAlert($booking)
    {
        $statusText = strtoupper(trim($booking->status));

        // Disable alert when Arrived
        if ($statusText === 'ARRIVED') {
            return;
        }

        $durationText = 'N/A';

        if (! empty($booking->start_time) && ! empty($booking->end_time)) {
            try {
                $dateStr = ! empty($booking->booking_date) ? date('Y-m-d', strtotime($booking->booking_date)) : date('Y-m-d');
                $startStr = $dateStr.' '.trim($booking->start_time);
                $endStr = $dateStr.' '.trim($booking->end_time);

                $sec = $this->calculateNetDurationSeconds($startStr, $endStr, $booking->location ?? $booking->branch_id);

                $hours = floor($sec / 3600);
                $minutes = floor(($sec % 3600) / 60);
                $durationText = ($hours > 0) ? "{$hours}h {$minutes}m" : "{$minutes}m";
            } catch (Exception $e) {
            }
        }

        $displayBranch = $this->getDisplayBranch($booking->location);
        $bookingId = str_pad($booking->id ?? 0, 4, '0', STR_PAD_LEFT);
        $internalNote = ! empty($booking->note) ? $booking->note : (! empty($booking->remark) ? $booking->remark : 'Normal');

        if (in_array($statusText, ['COMPLETED', 'CLOSE'])) {
            $headerTitle = '✅ <b>JOB COMPLETED</b>';
            $footerText = 'Open Service Index';
        } elseif (in_array($statusText, ['IN PROGRESS', 'WIP'])) {
            $headerTitle = '⚙️ <b>IN PROGRESS</b>';
            $footerText = 'Open Workshop Board';
        } elseif ($statusText == 'HOLD') {
            $headerTitle = '⏸️ <b>JOB ON HOLD</b>';
            $footerText = 'Open Workshop Board';
        } else {
            $headerTitle = '🔄 <b>STATUS UPDATE</b>';
            $footerText = 'Open Service Index';
        }

        $saName = $booking->sa_name ?? 'N/A';
        $techName = $booking->technician_name ?? 'N/A';
        $mileage = ! empty($booking->mileage) ? number_format((float) $booking->mileage).' KM' : 'N/A';

        $text = "{$headerTitle}\n"
              ."━━━━━━━━━━━━━━━━━━\n"
              ."🆔 <b>Reference No.:</b> #MG-{$bookingId}\n"
              ."📊 <b>Status:</b> {$booking->status}\n\n"

              ."🚘 <b>Vehicle Information</b>\n"
              ."├ Customer: {$booking->customer_name}\n"
              ."├ License Plate: <code>{$booking->plate}</code>\n"
              ."├ Model: {$booking->model}\n"
              ."└ Mileage: {$mileage}\n\n"

              ."🛠️ <b>Service Details</b>\n"
              ."├ Service Type: {$booking->service_type}\n"
              ."├ SA: {$saName}\n"
              ."├ Technician: {$techName}\n"
              ."└ Branch: {$displayBranch}\n\n";

        if (! empty($booking->start_time) || ! empty($booking->end_time)) {
            $text .= "⏱️ <b>Timing</b>\n";
            if (! empty($booking->start_time)) {
                $text .= '├ Started: '.date('h:i A', strtotime($booking->start_time))."\n";
            }
            if (! empty($booking->end_time)) {
                $text .= '├ Ended: '.date('h:i A', strtotime($booking->end_time))."\n";
            }
            if ($statusText == 'COMPLETED') {
                $text .= "└ Duration: <code>{$durationText}</code>\n\n";
            } else {
                $text .= "\n";
            }
        }

        $text .= "📝 <b>Internal Note:</b>\n<i>{$internalNote}</i>\n"
              ."━━━━━━━━━━━━━━━━━━\n"
              ."👉 {$footerText}";

        $this->executeTelegramCurl($text, $booking->branch_id ?? $booking->location);
    }
}
