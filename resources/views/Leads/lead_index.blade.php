@extends('layouts.homePages')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* ==========================================================================
       ✨ 1. CORE DESIGN SYSTEM & TYPOGRAPHY
       ========================================================================== */
    :root {
        --c-primary: #4f46e5;      --c-primary-hover: #4338ca;
        --c-success: #10b981;      --c-warning: #f59e0b;
        --c-danger: #ef4444;       --c-info: #0ea5e9;
        
        --c-slate-50: #f8fafc;     --c-slate-100: #f1f5f9;
        --c-slate-200: #e2e8f0;    --c-slate-300: #cbd5e1;
        --c-slate-400: #94a3b8;    --c-slate-500: #64748b;
        --c-slate-600: #475569;    --c-slate-700: #334155;
        --c-slate-800: #1e293b;    --c-slate-900: #0f172a;

        --radius-sm: 8px;          --radius-md: 12px;         --radius-lg: 16px;
        --border-light: 1px solid var(--c-slate-200);
    }

    /* 🌟 ប្តូរ Font ទៅជា Plus Jakarta Sans តែមួយគត់ 🌟 */
    body, #lead-management-ui, .swal2-popup { 
        font-family: 'Plus Jakarta Sans', sans-serif !important; 
    }
    
    body { background-color: var(--c-slate-50); }

    #lead-management-ui {
        background-color: var(--c-slate-50);
        color: var(--c-slate-800);
        padding: 24px 32px;
        min-height: 100vh;
        width: 100%;
    }

    /* ==========================================================================
       🎬 2. ANIMATIONS
       ========================================================================== */
    @keyframes fadeInUp {
        0% { opacity: 0; transform: translateY(12px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-up { animation: fadeInUp 0.4s ease-out forwards; }
    .stagger-1 { animation-delay: 0.05s; }
    .stagger-2 { animation-delay: 0.10s; }
    .stagger-3 { animation-delay: 0.15s; }
    .stagger-4 { animation-delay: 0.20s; }
    .stagger-5 { animation-delay: 0.25s; }
    .stagger-6 { animation-delay: 0.30s; }

    /* ==========================================================================
       📊 3. SUMMARY CARDS
       ========================================================================== */
    .saas-card {
        background: #ffffff; border: var(--border-light); border-radius: var(--radius-lg); padding: 20px;
        transition: all 0.2s ease; box-shadow: 0 2px 10px rgba(0,0,0,0.02); cursor: pointer;
        display: flex; flex-direction: column; justify-content: space-between; height: 100%;
    }
    .saas-card:hover { border-color: var(--c-primary); box-shadow: 0 10px 20px rgba(79, 70, 229, 0.08); transform: translateY(-3px); }
    .saas-card .card-title { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--c-slate-500); letter-spacing: 0.5px; }
    .saas-card .card-value { font-size: 2rem; font-weight: 800; color: var(--c-slate-900); margin-top: 8px; line-height: 1; }

    .branch-card {
        background: #ffffff; border: var(--border-light); border-radius: var(--radius-lg); padding: 16px;
        display: flex; align-items: center; justify-content: space-between; transition: all 0.2s ease; cursor: pointer;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    }
    .branch-card:hover { border-color: var(--c-primary); transform: translateY(-2px); }
    .branch-card .b-icon { width: 44px; height: 44px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
    .branch-card .b-name { font-size: 0.9rem; font-weight: 700; color: var(--c-slate-800); }
    .branch-card .b-badge { background: var(--c-slate-50); border: var(--border-light); padding: 4px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 700; }

    /* ==========================================================================
       🔍 4. FILTER SECTION
       ========================================================================== */
    .filter-wrapper { background: #ffffff; border: var(--border-light); border-radius: var(--radius-lg); padding: 24px; margin-bottom: 24px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03); }
    .saas-input { border: 1px solid var(--c-slate-300); border-radius: var(--radius-md); padding: 10px 14px; font-size: 0.9rem; font-weight: 500; color: var(--c-slate-800); background-color: #fff; height: 44px; transition: all 0.2s; font-family: 'Plus Jakarta Sans', sans-serif; }
    .saas-input:focus { border-color: var(--c-primary); box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); outline: none; }
    
    .input-with-icon { position: relative; }
    .input-with-icon i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--c-slate-400); font-size: 0.9rem; z-index: 2; pointer-events: none; }
    .input-with-icon .saas-input { padding-left: 40px !important; }

    .saas-checkbox { display: flex; align-items: center; gap: 8px; cursor: pointer; }
    .saas-checkbox input[type="checkbox"] { width: 18px; height: 18px; border-radius: 4px; cursor: pointer; border: 1px solid var(--c-slate-300); accent-color: var(--c-primary); }
    .saas-checkbox label { font-size: 0.85rem; font-weight: 600; color: var(--c-slate-700); cursor: pointer; user-select: none; }
    .saas-checkbox input[type="checkbox"]:checked + label { color: var(--c-primary); }

    /* ==========================================================================
       📋 5. SAAS DATA TABLE
       ========================================================================== */
    .table-container { 
        background: #ffffff; 
        border: 1px solid var(--c-slate-300) !important; 
        border-radius: var(--radius-lg); 
        padding: 0; 
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04); 
        overflow: hidden; 
    }
    
    .dataTables_scrollBody::-webkit-scrollbar { height: 8px; }
    .dataTables_scrollBody::-webkit-scrollbar-track { background: var(--c-slate-50); border-radius: 4px; }
    .dataTables_scrollBody::-webkit-scrollbar-thumb { background: var(--c-slate-300); border-radius: 4px; }
    .dataTables_scrollBody::-webkit-scrollbar-thumb:hover { background: var(--c-slate-400); }

    .saas-table { width: 100% !important; margin: 0 !important; border-collapse: collapse !important; white-space: nowrap; }
    
    .saas-table thead th { 
        background-color: #f1f5f9 !important; 
        border-bottom: 1px solid var(--c-slate-300) !important; 
        border-right: 1px solid var(--c-slate-200) !important; 
        padding: 16px 20px !important; 
        font-size: 0.8rem !important; 
        font-weight: 800 !important; 
        color: var(--c-slate-700) !important; 
        text-transform: uppercase !important; 
        letter-spacing: 0.5px !important; 
        text-align: left !important; 
    }
    
    .saas-table tbody td { 
        padding: 16px 20px !important; 
        font-size: 0.95rem !important; 
        font-weight: 600 !important; 
        color: var(--c-slate-800) !important; 
        border-bottom: 1px solid var(--c-slate-200) !important; 
        border-right: 1px solid var(--c-slate-200) !important; 
        vertical-align: middle !important; 
    }
    
    .saas-table thead th:last-child,
    .saas-table tbody td:last-child { border-right: none !important; }
    
    .saas-table tbody tr { transition: all 0.2s ease; }
    .saas-table tbody tr:hover td { background-color: #f8fafc !important; }

    .t-center { text-align: center !important; }
    .t-right { text-align: right !important; }

    .saas-badge { padding: 6px 12px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; border: 1px solid transparent; display: inline-block; }
    .badge-gray { background: var(--c-slate-100); color: var(--c-slate-700); border: 1px solid var(--c-slate-300); }
    .badge-red { background: #fee2e2; color: var(--c-danger); }
    .badge-blue { background: #e0f2fe; color: var(--c-info); }

    .st-badge { padding: 6px 14px; border-radius: 8px; font-weight: 800; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 6px; text-transform: uppercase; }
    .bg-prospecting { background: #eff6ff; color: #3b82f6; border: 1px solid #bfdbfe; } 
    .bg-appointment { background: #f5f3ff; color: #8b5cf6; border: 1px solid #ddd6fe; } 
    .bg-meetgreet { background: #f0fdf4; color: #22c55e; border: 1px solid #bbf7d0; }  
    .bg-consulting { background: #fefce8; color: #ca8a04; border: 1px solid #fef08a; }  
    .bg-doubleopen { background: #fff7ed; color: #f97316; border: 1px solid #ffedd5; }  
    .bg-presentation { background: #ecfeff; color: #06b6d4; border: 1px solid #cffafe; }
    .bg-testdrive { background: #fafaf9; color: #78350f; border: 1px solid #e7e5e4; }   
    .bg-trialclose { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }   
    .bg-negotiation { background: #fef2f2; color: #ef4444; border: 1px solid #fecaca; }  
    .bg-doubleclose { background: #faf5ff; color: #7e22ce; border: 1px solid #e9d5ff; }  
    .bg-sales { background: #dcfce7; color: #15803d; border: 1px solid #86efac; box-shadow: 0 2px 4px rgba(21, 128, 61, 0.1); } 
    .bg-delivery { background: #fffdf0; color: #b8860b; border: 1px solid #fde047; } 
    .bg-lost { background: #1e293b; color: #ffffff; border: 1px solid #0f172a; } 

    /* Comment Button Style */
    .btn-comment { width: 38px; height: 38px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; transition: 0.2s; border: none; font-size: 1rem; }
    .btn-comment.active { background: #eff6ff; color: var(--c-primary); border: 1px solid #bfdbfe; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    .btn-comment.active:hover { background: var(--c-primary); color: #fff; transform: translateY(-2px); box-shadow: 0 4px 8px rgba(79, 70, 229, 0.2); }
    .btn-comment.empty { background: var(--c-slate-50); color: var(--c-slate-400); border: 1px dashed var(--c-slate-300); cursor: pointer; }
    .btn-comment.empty:hover { background: #fefce8; color: var(--c-warning); border: 1px solid #fde047; }

    /* ==========================================================================
       🏷️ 6. BUTTONS & UI COMPONENTS
       ========================================================================== */
    .btn-saas { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 20px; border-radius: var(--radius-md); font-size: 0.9rem; font-weight: 700; cursor: pointer; transition: all 0.2s; border: none; text-decoration: none; }
    .btn-primary-solid { background: var(--c-primary); color: #fff; box-shadow: 0 4px 10px rgba(79, 70, 229, 0.2); }
    .btn-primary-solid:hover { background: var(--c-primary-hover); color: #fff; transform: translateY(-2px); box-shadow: 0 6px 15px rgba(79, 70, 229, 0.3); }
    
    .action-group { display: flex; gap: 6px; justify-content: flex-end; }
    .btn-icon { width: 36px; height: 36px; border-radius: var(--radius-sm); border: var(--border-light); background: #fff; color: var(--c-slate-500); display: inline-flex; align-items: center; justify-content: center; transition: 0.2s; cursor: pointer; text-decoration: none; box-shadow: 0 1px 2px rgba(0,0,0,0.02); }
    .btn-icon:hover:not([disabled]) { border-color: var(--c-primary); color: var(--c-primary); background: var(--c-slate-50); transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.05); }

    /* ==========================================================================
       🌟 7. PAYOFF MODAL PREMIUM DESIGN
       ========================================================================== */
    .payoff-modal-content { border-radius: 20px; overflow: hidden; border: 1px solid var(--c-slate-200); }
    .payoff-header { background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-bottom: 1px solid var(--c-slate-200); padding: 24px 30px; position: relative; }
    .payoff-icon-box { width: 50px; height: 50px; background: #e0f2fe; color: #0ea5e9; border-radius: 14px; display: inline-flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 15px; box-shadow: 0 4px 6px rgba(14, 165, 233, 0.1); }
    
    .payoff-body { padding: 30px; background: #ffffff; }
    .payoff-label { font-weight: 700; color: var(--c-slate-700); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; display: block; text-align: left; }
    
    .payoff-input-group { position: relative; }
    .payoff-input-group i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--c-slate-400); font-size: 0.9rem; z-index: 5; }
    .payoff-input { width: 100%; padding: 12px 16px 12px 42px; border: 1px solid var(--c-slate-300); border-radius: 10px; font-weight: 600; font-size: 0.95rem; color: var(--c-slate-800); background-color: #fff; transition: all 0.2s; text-align: left; font-family: 'Plus Jakarta Sans', sans-serif; }
    .payoff-input:focus { border-color: var(--c-info); box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1); outline: none; }
    
    .payoff-footer { padding: 20px 30px; background: var(--c-slate-50); border-top: 1px solid var(--c-slate-200); display: flex; justify-content: flex-end; gap: 12px; }
    .btn-payoff-cancel { background: #fff; border: 1px solid var(--c-slate-300); color: var(--c-slate-700); font-weight: 600; border-radius: 10px; padding: 10px 20px; transition: 0.2s; cursor: pointer; }
    .btn-payoff-cancel:hover { background: var(--c-slate-100); }
    .btn-payoff-submit { background: var(--c-info); border: none; color: #fff; font-weight: 700; border-radius: 10px; padding: 10px 24px; box-shadow: 0 4px 10px rgba(14, 165, 233, 0.2); transition: 0.2s; cursor: pointer; }
    .btn-payoff-submit:hover { background: #0284c7; transform: translateY(-1px); box-shadow: 0 6px 15px rgba(14, 165, 233, 0.3); }

    /* SweetAlert Comment Popup */
    .comment-popup { border-radius: 20px !important; padding: 0 !important; overflow: hidden; border: 1px solid var(--c-slate-200); font-family: 'Plus Jakarta Sans', sans-serif !important; }
</style>

<div id="lead-management-ui">
    @php
    $currentUser = auth('users')->user();
    $isAdminOrDirector = $currentUser ? in_array($currentUser->role_id, [1, 2, 6, 11]) : false;
    
    // 🌟 ថែមកូដនេះ៖ អនុញ្ញាតឱ្យ Admin និង Manager (3,4,5,7,8) អាច Comment បាន
    $canComment = $currentUser ? in_array($currentUser->role_id, [1, 2, 3, 4, 5, 6, 7, 8, 11]) : false; 
    
    $carModels = \App\Models\Lead::select('model')->whereNotNull('model')->where('model', '!=', '')->distinct()->pluck('model');
@endphp

    <div class="d-flex justify-content-between align-items-center mb-4 animate-fade-up">
        <div>
            <h2 class="m-0" style="font-weight: 800; color: var(--c-slate-900); font-size: 1.8rem; letter-spacing: -0.5px;">
                Lead <span style="color: var(--c-primary);">Pipeline</span>
                @if(isset($showArchived) && $showArchived)
                    <span class="saas-badge badge-red ms-2 align-middle" style="font-size: 0.8rem;">Archive View</span>
                @endif
            </h2>
            <p class="text-muted m-0 mt-1" style="font-size: 0.95rem; font-weight: 500;">Manage and track your customer journey.</p>
        </div>
        
        <div class="d-flex gap-3">
            @if($isAdminOrDirector)
                <a href="{{ route('leads.export') }}" class="btn-saas" style="background: #fff; border: 1px solid var(--c-slate-300); color: var(--c-slate-700); box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                    <i class="fas fa-download text-success"></i> Export Data
                </a>
                @if(isset($showArchived) && $showArchived)
                    <a href="{{ route('leads.index') }}" class="btn-saas" style="background: #fff; border: 1px solid var(--c-primary); color: var(--c-primary);">
                        <i class="fas fa-arrow-left"></i> Active Leads
                    </a>
                @else
                    <a href="{{ route('leads.index', ['view_archive' => 1]) }}" class="btn-saas" style="background: #fff; border: 1px solid var(--c-slate-300); color: var(--c-slate-700);">
                        <i class="fas fa-archive text-muted"></i> Archive
                    </a>
                @endif
            @endif
        </div>
    </div>

    @php
    $activeCount = 0; $bookingCount = 0; $deliveryCount = 0; $lostCount = 0; $noActionCount = 0;
    $todayDate = \Carbon\Carbon::today();

    if(isset($leads)) {
        foreach($leads as $l) {
            $stLower = strtolower($l->status);
            $isDelivery = $stLower == 'delivery';
            $isLost = $stLower == 'lost';
            $isContract = str_contains($stLower, 'contract') || str_contains($stLower, 'sales');

            if ($isDelivery) $deliveryCount++;
            if ($isLost) $lostCount++;
            if ($isContract) $bookingCount++;

            if (!$isDelivery && !$isLost) {
                $activeCount++;
                if (!$isContract) {
                    if (!$l->remind_date) {
                        $noActionCount++;
                    } else {
                        $rDate = \Carbon\Carbon::parse($l->remind_date)->startOfDay();
                        if ($rDate->lt($todayDate)) {
                            $noActionCount++;
                        }
                    }
                }
            }
        }
    }
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-xl-2 animate-fade-up stagger-1">
            <div class="saas-card" onclick="filterByStatus('')" style="border-bottom: 4px solid var(--c-slate-800);">
                <div class="d-flex justify-content-between"><span class="card-title">Total Active</span><div class="icon-box" style="color:var(--c-slate-400);"><i class="fas fa-users"></i></div></div>
                <div class="card-value" style="color: var(--c-slate-900);">{{ $activeCount }}</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2 animate-fade-up stagger-2">
            <div class="saas-card" onclick="filterByStatus('Sales')" style="border-bottom: 4px solid var(--c-primary);">
                <div class="d-flex justify-content-between"><span class="card-title" style="color: var(--c-primary);">Booking</span><div class="icon-box" style="color:var(--c-primary); opacity:0.7;"><i class="fas fa-bookmark"></i></div></div>
                <div class="card-value" style="color: var(--c-primary);">{{ $bookingCount }}</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2 animate-fade-up stagger-3">
            <div class="saas-card" onclick="filterByStatus('Delivery')" style="border-bottom: 4px solid var(--c-success);">
                <div class="d-flex justify-content-between"><span class="card-title" style="color: var(--c-success);">Delivery</span><div class="icon-box" style="color:var(--c-success); opacity:0.7;"><i class="fas fa-key"></i></div></div>
                <div class="card-value" style="color: var(--c-success);">{{ $deliveryCount }}</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2 animate-fade-up stagger-4">
            <div class="saas-card" onclick="filterByStatus('Lost')" style="border-bottom: 4px solid var(--c-danger);">
                <div class="d-flex justify-content-between"><span class="card-title" style="color: var(--c-danger);">Lost</span><div class="icon-box" style="color:var(--c-danger); opacity:0.7;"><i class="fas fa-times-circle"></i></div></div>
                <div class="card-value" style="color: var(--c-danger);">{{ $lostCount }}</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2 animate-fade-up stagger-5">
            <div class="saas-card" onclick="filterNoAction()" style="border-bottom: 4px solid var(--c-warning);">
                <div class="d-flex justify-content-between"><span class="card-title" style="color: var(--c-warning);">No Action</span><div class="icon-box" style="color:var(--c-warning); opacity:0.7;"><i class="fas fa-exclamation-triangle"></i></div></div>
                <div class="card-value" style="color: var(--c-warning);">{{ $noActionCount }}</div>
            </div>
        </div>
        @if($isAdminOrDirector && (!isset($showArchived) || !$showArchived))
        <div class="col-6 col-md-4 col-xl-2 animate-fade-up stagger-6">
            <div class="saas-card" onclick="window.location.href='{{ route('leads.index', ['view_archive' => 1]) }}'" style="background: var(--c-slate-50); border: 1px dashed var(--c-slate-300);">
                <div class="d-flex justify-content-between"><span class="card-title">Archived</span><i class="fas fa-archive text-muted opacity-50"></i></div>
                <div class="card-value text-muted">{{ $totalArchived ?? 0 }}</div>
            </div>
        </div>
        @endif
    </div>

    <div class="row g-3 mb-4 animate-fade-up">
        @php
        $myBranchName = $currentUser ? \App\Models\BranchModel::where('id', $currentUser->branch_id)->value('branch_name') : '';
        
        $hqC = 0; $m60C = 0; $srC = 0; $btbC = 0; $leapC = 0; 
        
        if(isset($leads)) {
            foreach($leads as $l) {
                $bName = strtolower($l->branch_name ?? $l->branch ?? '');
                if (str_contains($bName, 'head') || str_contains($bName, 'quarter')) $hqC++;
                elseif (str_contains($bName, '60m')) $m60C++;
                elseif (str_contains($bName, 'siem reap') || str_contains($bName, 'sr')) $srC++;
                elseif (str_contains($bName, 'battambang') || str_contains($bName, 'btb')) $btbC++;
                elseif (str_contains($bName, 'leap')) $leapC++; 
            }
        }
        
        $branchConfig = [
            ['en' => 'Head Quarters', 'color' => 'var(--c-info)', 'bg' => '#e0f2fe', 'icon' => 'fa-building', 'count' => $hqC, 'search_key' => 'Head'],
            ['en' => '60M Branch', 'color' => 'var(--c-primary)', 'bg' => '#eef2ff', 'icon' => 'fa-store', 'count' => $m60C, 'search_key' => '60M'],
            ['en' => 'Siem Reap', 'color' => 'var(--c-success)', 'bg' => '#ecfdf5', 'icon' => 'fa-map-marker-alt', 'count' => $srC, 'search_key' => 'Siem'],
            ['en' => 'Battambang', 'color' => 'var(--c-warning)', 'bg' => '#fef3c7', 'icon' => 'fa-map-pin', 'count' => $btbC, 'search_key' => 'Battambang'],
            ['en' => 'Leap Motors', 'color' => '#8b5cf6', 'bg' => '#f3e8ff', 'icon' => 'fa-bolt', 'count' => $leapC, 'search_key' => 'Leap'] 
        ];
        @endphp

        @foreach($branchConfig as $b)
            @php
                $showCard = $isAdminOrDirector;
                if (!$showCard) {
                    $myB = strtolower($myBranchName);
                    if (str_contains($myB, strtolower($b['search_key']))) $showCard = true;
                }
            @endphp
            @if($showCard)
            <div class="col-12 col-md-6 col-lg-3">
                <div class="branch-card" onclick="filterByBranch('{{ $b['search_key'] }}')">
                    <div class="d-flex align-items-center gap-3">
                        <div class="b-icon" style="background: {{ $b['bg'] }}; color: {{ $b['color'] }};"><i class="fas {{ $b['icon'] }}"></i></div>
                        <div class="b-name">{{ $b['en'] }}</div>
                    </div>
                    <div class="b-badge" style="color: {{ $b['color'] }};">{{ $b['count'] }}</div>
                </div>
            </div>
            @endif
        @endforeach
    </div>

    <div class="filter-wrapper animate-fade-up">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4 pb-3 border-bottom border-slate-200">
            <span class="fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;"><i class="fas fa-filter me-2" style="color: var(--c-primary);"></i> Smart Filters</span>
            <button class="btn btn-sm" onclick="clearFilters()" style="background: var(--c-slate-100); color: var(--c-slate-700); border: 1px solid var(--c-slate-300); font-weight: 700; padding: 6px 16px; border-radius: 8px;"><i class="fas fa-undo-alt me-2"></i> Reset Filters</button>
        </div>

        <div class="d-flex flex-wrap gap-4 mb-4">
            <div class="saas-checkbox"><input type="checkbox" id="chkPlanToBuy" class="filter-checkbox"><label for="chkPlanToBuy">Active Intent</label></div>
            <div class="saas-checkbox"><input type="checkbox" id="chkOverdue" class="filter-checkbox"><label for="chkOverdue" class="text-danger fw-bold">Action Overdue</label></div>
            <div class="saas-checkbox"><input type="checkbox" id="chkToday" class="filter-checkbox"><label for="chkToday" class="text-primary fw-bold">Due Today</label></div>
            <div class="saas-checkbox"><input type="checkbox" id="chkTomorrow" class="filter-checkbox"><label for="chkTomorrow">Due Tomorrow</label></div>
            <div class="saas-checkbox"><input type="checkbox" id="chk7Days" class="filter-checkbox"><label for="chk7Days" class="text-success fw-bold">Next 7 Days</label></div>
            <div class="saas-checkbox"><input type="checkbox" id="chk30Days" class="filter-checkbox"><label for="chk30Days">Next 30 Days</label></div>
        </div>

        <div class="row g-3 align-items-center">
            <div class="col-md-3">
                <div class="input-with-icon">
                    <i class="fas fa-search"></i>
                    <input type="text" id="customSearch" class="form-control w-100 saas-input" placeholder="Search name, phone, etc...">
                </div>
            </div>
            
            <div class="col-md-2">
                <div class="input-with-icon">
                    <i class="fas fa-store"></i>
                    <select id="branchFilter" class="form-select w-100 saas-input" {{ $isAdminOrDirector ? '' : 'disabled style=background:#f8fafc;color:#94a3b8;' }}>
                        @if($isAdminOrDirector)
                            <option value="">All Branches</option>
                            <option value="Head Office">Head Office</option>
                            <option value="MG 60M">MG 60M</option>
                            <option value="MG Siem Reap">MG Siem Reap</option>
                            <option value="MG Battambang">MG Battambang</option>
                            <option value="Leap Motors">Leap Motors</option>
                        @else
                            <option value="{{ $myBranchName }}" selected>{{ $myBranchName }}</option>
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="input-with-icon">
                    <i class="fas fa-layer-group"></i>
                    <select id="stageFilter" class="form-select w-100 saas-input">
                        <option value="">All Stages</option>
                        <option value="Prospecting">Prospecting</option>
                        <option value="Appointment">Appointment</option>
                        <option value="Meet & Greet">Meet & Greet</option>
                        <option value="Consulting ( Q & A )">Consulting</option>
                        <option value="Presentation">Presentation</option>
                        <option value="Test Drive">Test Drive</option>
                        <option value="Negotiation">Negotiation</option>
                        <option value="Sales">Contract</option>
                        <option value="Delivery">Delivery</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="input-with-icon">
                    <i class="fas fa-car"></i>
                    <select id="modelFilter" class="form-select w-100 saas-input">
                        <option value="">All Models</option>

                        @php
                            $myBranchNameLower = strtolower($myBranchName ?? '');
                            $isLeapBranch = str_contains($myBranchNameLower, 'leap');
                            $isMGBranch = !$isLeapBranch;
                        @endphp

                        @if($isAdminOrDirector || $isMGBranch)
                            <optgroup label="MG Motors">
                                @foreach(['MG ZS', 'MG HS', 'MG5', 'MG7', 'MG RX5', 'MG ONE', 'MG4 EV', 'MG Cyberster'] as $m)
                                    <option value="{{ $m }}">{{ $m }}</option>
                                @endforeach
                            </optgroup>
                        @endif

                        @if($isAdminOrDirector || $isLeapBranch)
                            <optgroup label="Leapmotor">
                                @foreach(['Leapmotor T03', 'Leapmotor C10', 'Leapmotor C11', 'Leapmotor C01', 'Leapmotor C16'] as $m)
                                    <option value="{{ $m }}">{{ $m }}</option>
                                @endforeach
                            </optgroup>
                        @endif

                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="d-flex gap-2">
                    <div class="input-with-icon w-50">
                        <i class="far fa-calendar text-primary"></i>
                        <input type="date" id="dateFrom" class="form-control w-100 saas-input" title="From Date">
                    </div>
                    <div class="input-with-icon w-50">
                        <i class="far fa-calendar text-danger"></i>
                        <input type="date" id="dateTo" class="form-control w-100 saas-input" title="To Date">
                    </div>
                </div>
            </div>
            
            <div class="col-12 mt-4 d-flex justify-content-end gap-3 pt-3 border-top border-slate-100">
                @if(!isset($showArchived) || !$showArchived)
                    <a href="{{ url('/daily-tasks') }}" class="btn-saas" style="background: #ffffff; border: 1px solid var(--c-slate-300); color: var(--c-slate-800); box-shadow: 0 2px 5px rgba(0,0,0,0.02);">
                        <i class="fas fa-tasks text-primary"></i> Daily Focus
                    </a>
                    <a href="{{ route('leads.create') }}" class="btn-saas btn-primary-solid">
                        <i class="fas fa-plus-circle"></i> Create Lead
                    </a>
                @endif
            </div>
            
        </div>
    </div>

    <div class="table-container animate-fade-up">
        <table id="leadTable" class="saas-table table-hover w-100">
            <thead>
                <tr>
                    <th class="t-center" style="width: 50px;">#</th>
                    <th style="min-width: 120px;">Registry Date</th>
                    <th style="min-width: 180px;">Customer Name</th>
                    <th style="min-width: 150px;">Phone Number</th>
                    <th style="min-width: 140px;">Province / Location</th>
                    <th style="min-width: 120px;">Car Model</th>
                    <th style="min-width: 160px;">Sales Consultant</th>
                    <th style="min-width: 140px;">Branch</th>
                    <th style="min-width: 130px;">Next Action</th>
                    <th class="t-center" style="min-width: 150px;">Pipeline Stage</th>
                    <th class="t-center" style="min-width: 80px;">Manager Note</th>
                    <th class="t-center" style="min-width: 80px;">Aging</th>
                    <th style="display:none;">RawRemind</th>
                    <th style="display:none;">RawOverdue</th>
                    <th class="t-right" style="min-width: 250px;">Operations</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($leads))
                    @foreach ($leads as $index => $lead)
                        @php 
                            $statusLower = strtolower($lead->status);
                            
                            $isDeliveryOrLost = in_array($statusLower, ['delivery', 'lost']);
                            $isClosed = in_array($statusLower, ['delivery', 'lost', 'won', 'sales ( contract of sales )']);

                            $createdAt = \Carbon\Carbon::parse($lead->created_at)->startOfDay();
                            $daysInSystem = $createdAt->diffInDays(\Carbon\Carbon::today());
                            
                            $diffDays = null; $isOverdue = false; $isPlanned = false;
                            if ($lead->remind_date) {
                                $rDate = \Carbon\Carbon::parse($lead->remind_date)->startOfDay();
                                $today = \Carbon\Carbon::today();
                                $diffDays = $today->diffInDays($rDate, false);
                                if (!$isClosed) { 
                                    if ($diffDays < 0) { $isOverdue = true; } 
                                    else { $isPlanned = true; } 
                                }
                            }

                            // ឆែកមើលក្រែងលោមាន Manager Comment ថ្មីបំផុត
                            $latestManagerComment = null;
                            if($lead->managerInstructions && $lead->managerInstructions->count() > 0) {
                                $latestManagerComment = $lead->managerInstructions->first();
                            }

                            $stage = match($statusLower) {
                                'prospecting' => ['class' => 'bg-prospecting', 'icon' => 'fa-search'],
                                'appointment' => ['class' => 'bg-appointment', 'icon' => 'fa-calendar-check'],
                                'meet & greet' => ['class' => 'bg-meetgreet', 'icon' => 'fa-handshake'],
                                'consulting ( q & a )' => ['class' => 'bg-consulting', 'icon' => 'fa-comments'],
                                'double open' => ['class' => 'bg-doubleopen', 'icon' => 'fa-door-open'],
                                'presentation' => ['class' => 'bg-presentation', 'icon' => 'fa-tv'],
                                'test drive' => ['class' => 'bg-testdrive', 'icon' => 'fa-car'],
                                'trail close' => ['class' => 'bg-trialclose', 'icon' => 'fa-hourglass-start'],
                                'negotiation' => ['class' => 'bg-negotiation', 'icon' => 'fa-comments-dollar'],
                                'double close' => ['class' => 'bg-doubleclose', 'icon' => 'fa-check-double'],
                                'sales ( contract of sales )' => ['class' => 'bg-sales', 'icon' => 'fa-file-signature'],
                                'delivery' => ['class' => 'bg-delivery', 'icon' => 'fa-key'],
                                'lost' => ['class' => 'bg-lost', 'icon' => 'fa-user-slash'],
                                default => ['class' => 'bg-prospecting', 'icon' => 'fa-circle'],
                            };
                        @endphp
                        <tr>
                            <td class="t-center text-muted fw-bold">{{ $index + 1 }}</td>
                            
                            <td>
                                <div class="text-slate-600 fw-bold">
                                    <i class="far fa-calendar-alt opacity-50 me-1"></i> {{ \Carbon\Carbon::parse($lead->date ?? $lead->created_at)->timezone('Asia/Phnom_Penh')->format('d-M-y') }}
                                </div>
                            </td>
                            
                            <td>
                                <div class="fw-bold text-dark fs-6" style="letter-spacing: -0.3px;">
                                    {{ $lead->customer_name }}
                                </div>
                            </td>
                            
                            <td>
                                <div class="fw-bold text-primary" style="font-family: monospace; font-size: 0.95rem;">
                                    {{ $lead->contact_info }}
                                </div>
                            </td>

                            <td>
                                <div class="fw-bold" style="color: var(--c-slate-700);">
                                    <i class="fas fa-map-pin text-warning opacity-75 me-1"></i> {{ $lead->location ?? 'N/A' }}
                                </div>
                            </td>
                            
                            <td>
                                <span class="saas-badge badge-gray">{{ $lead->model }}</span>
                            </td>
                            
                            <td>
                                <div class="fw-bold text-slate-700">
                                    <i class="fas fa-user-tie text-primary opacity-50 me-1"></i> {{ $lead->sales_person }}
                                </div>
                            </td>
                            
                            <td>
                                <div class="text-muted fw-bold">
                                    <i class="fas fa-map-marker-alt text-danger opacity-50 me-1"></i> {{ str_replace('MG ', '', $lead->branch_name ?? $lead->branch) }}
                                </div>
                            </td>
                            
                            <td>
                                <div style="font-weight: 700; color: var(--c-slate-900);">
                                    {{ $lead->remind_date ? \Carbon\Carbon::parse($lead->remind_date)->format('d-M-Y') : 'N/A' }}
                                </div>
                                @if($diffDays !== null && !$isClosed)
                                    @if($diffDays == 0) <span class="saas-badge badge-gray mt-1 p-1 px-2" style="font-size:0.65rem;">Today</span>
                                    @elseif($diffDays == 1) <span class="saas-badge badge-blue mt-1 p-1 px-2" style="font-size:0.65rem;">Tomorrow</span>
                                    @elseif($diffDays < 0) <span class="saas-badge badge-red mt-1 p-1 px-2 shadow-sm" style="font-size:0.65rem;">Late {{ abs($diffDays) }}d</span>
                                    @endif
                                @endif
                            </td>
                            
                            <td class="t-center">
                                <span class="st-badge {{ $stage['class'] }}">
                                    <i class="fas {{ $stage['icon'] }}"></i> {{ strtoupper($lead->status) }}
                                </span>
                            </td>
                            <td class="t-center">
                                @if($canComment || $latestManagerComment)
                                    <button type="button" class="btn-comment {{ $latestManagerComment ? 'active' : 'empty' }}" 
                                            onclick="manageInstruction('{{ $lead->id }}', '{{ addslashes($lead->customer_name) }}', '{{ $latestManagerComment ? addslashes($latestManagerComment->instruction) : '' }}', '{{ $latestManagerComment ? \Carbon\Carbon::parse($latestManagerComment->created_at)->format('d M, Y - h:i A') : '' }}', '{{ $latestManagerComment->user->full_name ?? '' }}')" 
                                            title="{{ $latestManagerComment ? 'View/Update Manager Instruction' : 'Add Manager Instruction' }}">
                                        <i class="fas {{ $latestManagerComment ? 'fa-comment-dots' : 'fa-plus' }}"></i>
                                    </button>
                                @else
                                    <button type="button" class="btn-comment empty" disabled title="No Comment" style="opacity: 0.3; cursor: not-allowed; border-style: solid;">
                                        <i class="fas fa-comment-slash"></i>
                                    </button>
                                @endif
                            </td>
                            
                            <td class="t-center">
                                @if($daysInSystem == 0) <span class="saas-badge badge-blue">New</span>
                                @else <span class="saas-badge badge-gray">{{ $daysInSystem }} d</span> @endif
                            </td>
                            
                            <td style="display:none;">{{ $lead->remind_date ? \Carbon\Carbon::parse($lead->remind_date)->format('Y-m-d') : '' }}</td>
                            <td style="display:none;">{{ $isOverdue ? 'YES' : 'NO' }}</td>
                            
                            <td class="t-right">
                                <div class="action-group">
                                    <a href="{{ route('leads.show', Crypt::encryptString($lead->id)) }}" class="btn-icon" title="View Detail">
                                        <i class="fas fa-eye text-primary"></i>
                                    </a>

                                    @if(isset($showArchived) && $showArchived)
                                        <button type="button" class="btn-icon btn-transfer" 
                                                data-id="{{ $lead->id }}" data-name="{{ $lead->customer_name }}" title="Transfer Lead">
                                            <i class="fas fa-exchange-alt text-info"></i>
                                        </button>
                                        <button class="btn-icon btn-restore" onclick="restoreLead({{ $lead->id }})" title="Restore">
                                            <i class="fas fa-undo text-success"></i>
                                        </button>
                                    @else

                                        @if($isDeliveryOrLost)
                                            <button class="btn-icon" disabled style="opacity:0.4; cursor:not-allowed;" title="Lead Locked (View Only)">
                                                <i class="fas fa-lock text-danger"></i>
                                            </button>
                                        @else
                                            
                                            @php
                                                $isSalesContract = str_contains($statusLower, 'sales') || str_contains($statusLower, 'contract');
                                            @endphp

                                            @if($isSalesContract)
                                                <button type="button" class="btn-icon btn-payoff" 
                                                        data-id="{{ $lead->id }}"
                                                        data-name="{{ $lead->customer_name }}"
                                                        data-model="{{ $lead->model }}"
                                                        data-branch="{{ $lead->branch_name ?? $lead->branch ?? '' }}" 
                                                        data-sales="{{ $lead->sales_person }}" 
                                                        title="Send Payoff Alert">
                                                    <i class="fab fa-telegram-plane" style="color: var(--c-info);"></i>
                                                </button>
                                            @else
                                                <button type="button" class="btn-icon" disabled style="opacity:0.3; cursor:not-allowed;" title="តម្រូវឱ្យ Status ដល់វគ្គ 'Sales' ទើបអាចបញ្ជូន Telegram បាន">
                                                    <i class="fab fa-telegram-plane text-muted"></i>
                                                </button>
                                            @endif

                                            <a href="{{ route('leads.edit', Crypt::encryptString($lead->id)) }}" class="btn-icon" title="Edit Information">
                                                <i class="fas fa-pen text-warning"></i>
                                            </a>
                                    
                                            <button class="btn-icon btn-reschedule" 
                                                    data-id="{{ $lead->id }}" 
                                                    data-name="{{ $lead->customer_name }}" 
                                                    data-remind="{{ $lead->remind_date ? \Carbon\Carbon::parse($lead->remind_date)->format('Y-m-d') : date('Y-m-d', strtotime('+1 day')) }}"
                                                    title="Reschedule Date">
                                                <i class="fas fa-calendar-alt text-info"></i>
                                            </button>
                                    
                                            @if($isAdminOrDirector)
                                                <button class="btn-icon btn-transfer" data-id="{{ $lead->id }}" data-name="{{ $lead->customer_name }}" title="Transfer Assignment">
                                                    <i class="fas fa-exchange-alt" style="color: #8b5cf6;"></i>
                                                </button>
                                            @endif
                                    
                                            <button class="btn-icon btn-smart" data-id="{{ $lead->id }}" data-name="{{ $lead->customer_name }}" data-status="{{ $lead->status }}" title="Quick Update">
                                                <i class="fas fa-bolt text-success"></i>
                                            </button>
                                    
                                            @if($isAdminOrDirector)
                                                <button class="btn-icon btn-archive" onclick="archiveLead({{ $lead->id }})" title="Move to Archive">
                                                    <i class="fas fa-trash-alt text-danger"></i>
                                                </button>
                                            @endif
                                        @endif

                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="payoffModal" tabindex="-1" aria-labelledby="payoffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content payoff-modal-content shadow-lg border-0">
            
            <div class="payoff-header">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-4" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="payoff-icon-box">
                    <i class="fab fa-telegram-plane"></i>
                </div>
                <h4 class="modal-title fw-bold" style="font-family: 'Plus Jakarta Sans', sans-serif; color: var(--c-slate-900);">Send Payoff Alert</h4>
                <p class="mb-0 text-muted" style="font-size: 0.9rem;">Broadcast delivery schedule to the management and warehouse team.</p>
            </div>
            
            <form action="{{ route('leads.payoff_alert') }}" method="POST" id="payoffFormModal">
                @csrf

                <input type="hidden" name="lead_id" id="modal_lead_id"> 
                <input type="hidden" name="sales_person" id="modal_sales_person"> 

                <div class="payoff-body">
                    <div class="row g-3 mb-4 pb-4 border-bottom border-slate-100">
                        <div class="col-md-6">
                            <label class="payoff-label">Customer Name</label>
                            <div class="payoff-input-group">
                                <i class="fas fa-user-circle"></i>
                                <input type="text" name="customer_name" id="modal_customer_name" class="payoff-input read-only" readonly tabindex="-1">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="payoff-label">Car Model</label>
                            <div class="payoff-input-group">
                                <i class="fas fa-car-side"></i>
                                <input type="text" name="model" id="modal_model" class="payoff-input read-only" readonly tabindex="-1">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="payoff-label">Delivery Date <span class="text-danger">*</span></label>
                            <div class="payoff-input-group">
                                <i class="fas fa-calendar-day text-info"></i>
                                <input type="date" name="date" id="modal_date" class="payoff-input" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="payoff-label">Time <span class="text-danger">*</span></label>
                            <div class="payoff-input-group">
                                <i class="fas fa-clock text-info"></i>
                                <select name="time" class="payoff-input" required style="padding-left: 42px;">
                                    <option value="" disabled>Select Time</option>
                                    <optgroup label="Morning">
                                        <option value="8:00 AM">8:00 AM</option>
                                        <option value="9:00 AM">9:00 AM</option>
                                        <option value="10:00 AM">10:00 AM</option>
                                        <option value="11:00 AM">11:00 AM</option>
                                    </optgroup>
                                    <optgroup label="Afternoon">
                                        <option value="1:00 PM" selected>1:00 PM</option>
                                        <option value="2:00 PM">2:00 PM</option>
                                        <option value="3:00 PM">3:00 PM</option>
                                        <option value="4:00 PM">4:00 PM</option>
                                        <option value="5:00 PM">5:00 PM</option>
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="payoff-label">Branch / Location <span class="text-danger">*</span></label>
                            <div class="payoff-input-group">
                                <i class="fas fa-map-marker-alt text-info"></i>
                                
                                <select name="location" id="modal_location" class="payoff-input" required 
                                    @if($isAdminOrDirector)
                                        style="padding-left: 42px; cursor: pointer; background-color: #ffffff;"
                                    @else
                                        style="padding-left: 42px; pointer-events: none; background-color: var(--c-slate-50);"
                                        readonly tabindex="-1"
                                    @endif
                                >
                                    <option value="Head Office">Head Office</option>
                                    <option value="MG 60M">MG 60M</option>
                                    <option value="MG Siem Reap">MG Siem Reap</option>
                                    <option value="MG Battambang">MG Battambang</option>
                                    <option value="Leap Motors">Leap Motors</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="payoff-label" style="color: var(--c-danger);">Contract Number <span class="text-danger">*</span></label>
                            <div class="payoff-input-group">
                                <i class="fas fa-file-signature text-danger"></i>
                                <input type="text" name="contract_number" class="payoff-input" style="border-color: #fecaca; background: #fef2f2;" placeholder="Enter Contract No." required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="payoff-label" style="color: var(--c-danger);">Chassis Number (VIN) <span class="text-danger">*</span></label>
                            <div class="payoff-input-group">
                                <i class="fas fa-barcode text-danger"></i>
                                <input type="text" name="chassis_number" class="payoff-input text-uppercase" style="border-color: #fecaca; background: #fef2f2;" placeholder="e.g. LSJW74..." required>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="payoff-label" style="color: var(--c-success);">Customer Pay ($) <span class="text-danger">*</span></label>
                            <div class="payoff-input-group">
                                <i class="fas fa-dollar-sign text-success"></i>
                                <input type="text" name="customer_pay" class="payoff-input fw-bold text-success" style="border-color: #bbf7d0; background: #f0fdf4; font-size: 1.1rem;" placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="payoff-label">Payment Method <span class="text-danger">*</span></label>
                            <div class="payoff-input-group">
                                <i class="fas fa-wallet text-slate-400"></i>
                                <select name="payment_type" class="payoff-input" required style="padding-left: 42px;">
                                    <option value="Bank Transfer" selected>Bank Transfer</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Cheque">Cheque</option>
                                    <option value="Bank Loan">Bank Loan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="payoff-footer">
                    <button type="button" class="btn-payoff-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-payoff-submit">
                        <i class="fas fa-paper-plane me-1"></i> Send Alert
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
    window.clearFilters = function() { window.location.reload(); };

    window.archiveLead = function(id) {
        Swal.fire({ title: 'Archive Lead?', text: 'This lead will be moved to the archive.', icon: 'warning', showCancelButton: true, confirmButtonColor: 'var(--c-danger)', confirmButtonText: 'Archive'
        }).then((result) => { if (result.isConfirmed) window.location.href = "{{ url('/leads/archive') }}/" + id; });
    }
    window.restoreLead = function(id) {
        Swal.fire({ title: 'Restore Lead?', text: 'This lead will be restored to active.', icon: 'question', showCancelButton: true, confirmButtonColor: 'var(--c-success)', confirmButtonText: 'Restore'
        }).then((result) => { if (result.isConfirmed) window.location.href = "{{ url('/leads/restore') }}/" + id; });
    }

    // 🌟 មុខងារ Popup សម្រាប់ Manager Instruction (បញ្ចូល និង មើល)
    window.manageInstruction = function(leadId, customerName, comment, date, managerName) {
        
        // 🌟 ប្រើប្រាស់អថេរថ្មី $canComment ជំនួស $isAdminOrDirector
        var hasCommentAccess = {{ isset($canComment) && $canComment ? 'true' : 'false' }};
        
        let historyHtml = '';
        if(comment) {
            historyHtml = `
                <div style="background: var(--c-slate-50); border: 1px solid var(--c-slate-200); border-radius: 12px; padding: 15px; position:relative; text-align: left; margin-bottom: 15px;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                        <span style="font-size:0.8rem; color:var(--c-slate-800); font-weight:700;"><i class="fas fa-user-tie me-1 opacity-50"></i> ${managerName}</span>
                        <span style="font-weight:700; color:var(--c-primary); font-size:0.75rem;">${date}</span>
                    </div>
                    <p style="margin: 0; font-size: 0.95rem; color: var(--c-slate-700); line-height: 1.6; font-weight: 500;">
                        ${comment}
                    </p>
                </div>
            `;
        } else {
            historyHtml = `<div class="text-muted small mb-3 fst-italic text-center">No instructions provided yet.</div>`;
        }

        let inputHtml = '';
        if(hasCommentAccess) {
            inputHtml = `
                <div style="text-align: left; margin-top: 15px;">
                    <label class="fw-bold mb-2" style="font-size: 0.85rem; color: var(--c-slate-700); text-transform: uppercase;">Add New Instruction:</label>
                    <textarea id="manager_new_comment" class="form-control" rows="3" style="border-radius:12px; border: 1px solid var(--c-slate-300); font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 500;" placeholder="Type your instructions here..."></textarea>
                </div>
            `;
        }

        Swal.fire({
            title: `<div class="text-start" style="font-family:'Plus Jakarta Sans', sans-serif; font-weight:800; color:var(--c-slate-900); font-size:1.3rem;">Manager Instruction</div>`,
            html: `
                <div style="font-family: 'Plus Jakarta Sans', sans-serif;">
                    <div style="text-align:left; margin-bottom:15px;"><span style="font-size:10px; font-weight:800; color:var(--c-slate-400); text-transform:uppercase;">Customer:</span> <span style="font-weight:700; color:var(--c-slate-800); font-size: 1rem;">${customerName}</span></div>
                    ${historyHtml}
                    ${inputHtml}
                </div>
            `,
            showCancelButton: true,
            showConfirmButton: hasCommentAccess, // លាក់ប៊ូតុង Submit បើមិនមែនជាអ្នកមានសិទ្ធិ
            confirmButtonText: '<i class="fas fa-paper-plane me-1"></i> SEND INSTRUCTION',
            cancelButtonText: hasCommentAccess ? 'CANCEL' : 'CLOSE',
            confirmButtonColor: 'var(--c-primary)',
            customClass: { 
                popup: 'comment-popup border border-slate-300 shadow-lg', 
                confirmButton: 'btn btn-primary fw-bold px-4 py-2 rounded-3',
                cancelButton: 'btn btn-light fw-bold px-4 py-2 border border-slate-300 rounded-3 text-dark'
            },
            buttonsStyling: false,
            preConfirm: () => {
                if(hasCommentAccess) {
                    const newComment = $('#manager_new_comment').val().trim();
                    if(!newComment) {
                        Swal.showValidationMessage('Please type an instruction!');
                        return false;
                        
                    }
                    return { id: leadId, manager_comment: newComment, _token: "{{ csrf_token() }}" };
                }
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                Swal.fire({ title: 'Saving...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                $.ajax({
                    url: "{{ url('/leads/update-manager-comment') }}",
                    type: "POST",
                    data: result.value,
                    success: function(res) {
                        if(res.success) {
                            Swal.fire({ icon: 'success', title: 'Instruction Sent!', timer: 1500, showConfirmButton: false }).then(() => location.reload());
                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Something went wrong while saving instruction.', 'error');
                    }
                });
            }
        });
    }

    $(document).ready(function() {
        var table;
        try { 
            // 🌟 Initialize DataTable ជាមួយនឹង ScrollX និងអោយវារត់រលូន
            table = $('#leadTable').DataTable({ 
                "dom": '<"d-flex justify-content-between align-items-center mb-3"i>rt<"d-flex justify-content-between align-items-center mt-3"p>', 
                "pageLength": 15, 
                "order": [[1, "desc"]], 
                "scrollX": true,
                "autoWidth": false,
                "language": { "emptyTable": "No data available in table" }
            }); 
            
            // ជួសជុលបញ្ហា Header មិនត្រូវខ្នាតពេល Load ដំបូង
            setTimeout(function() { table.columns.adjust().draw(); }, 100);
        } catch (e) { console.error(e); }

        if (table) {
            var isAdmin = {{ $isAdminOrDirector ? 'true' : 'false' }};
            var initialBranch = $('#branchFilter').val();
            if (isAdmin && initialBranch !== "") table.column(7).search(initialBranch).draw(); // Update Index: Branch = 7
            else if (!isAdmin) table.column(7).search('').draw(); 

            $('#customSearch').on('keyup', function() { table.search(this.value).draw(); });
            $('#branchFilter').on('change', function() { table.column(7).search(this.value).draw(); });
            $('#stageFilter').on('change', function() { table.column(9).search(this.value).draw(); }); // Status = 9
            $('#modelFilter').on('change', function() { table.column(5).search(this.value).draw(); }); // Model = 5

            // 🌟 កូដថ្មី: ចាប់យកពាក្យពី URL មក Filter អូតូ (សម្រាប់ពេលចុចពី Notification)
            const urlParams = new URLSearchParams(window.location.search);
            const searchVal = urlParams.get('search');
            if (searchVal) {
                $('#customSearch').val(searchVal); // បង្ហាញលេខទូរស័ព្ទក្នុងប្រអប់ Search
                table.search(searchVal).draw();    // បញ្ជាឱ្យតារាង Filter រកអតិថិជននោះភ្លាមៗ
            }
            // 🌟 ------------------------------------------------------------------------- 🌟

            window.filterByStatus = function(status) {
                $('.filter-checkbox').prop('checked', false); $('#dateFrom, #dateTo').val(''); 
                $.fn.dataTable.ext.search.pop(); table.column(9).search(status).draw(); 
            }

            window.filterByBranch = function(branchName) {
                if ($('#branchFilter').is(':disabled') && !$('#branchFilter').val().includes(branchName) && branchName !== "") return;
                table.column(7).search(branchName).draw(); 
            }

            window.filterNoAction = function() {
                $('.filter-checkbox').prop('checked', false); $('#dateFrom, #dateTo').val(''); table.column(9).search(''); 
                $.fn.dataTable.ext.search.push(function(settings, data) {
                    const remindDate = data[12], status = data[9].toLowerCase(); // Remind = 12
                    if (['delivery', 'lost', 'won', 'sales'].some(el => status.includes(el))) return false;
                    if (!remindDate || remindDate === 'N/A' || remindDate === '') return true;
                    const rDate = new Date(remindDate), today = new Date(); today.setHours(0,0,0,0);
                    return rDate < today;
                });
                table.draw(); $.fn.dataTable.ext.search.pop(); 
            }

            $.fn.dataTable.ext.search.push(function(settings, data) {
                const rawRemind = data[12], status = data[9].toLowerCase(); 
                const isClosed = ['delivery', 'lost', 'won', 'sales'].includes(status);
                const fromDate = $('#dateFrom').val(), toDate = $('#dateTo').val();
                let matchDateRange = true;

                if (fromDate || toDate) {
                    if (!rawRemind) matchDateRange = false; 
                    else if (fromDate && !toDate) matchDateRange = rawRemind >= fromDate; 
                    else if (!fromDate && toDate) matchDateRange = rawRemind <= toDate; 
                    else matchDateRange = rawRemind >= fromDate && rawRemind <= toDate;
                }

                const checks = [$('#chkPlanToBuy').is(':checked'), $('#chkOverdue').is(':checked'), $('#chkToday').is(':checked'), $('#chkTomorrow').is(':checked'), $('#chk7Days').is(':checked'), $('#chk30Days').is(':checked')];
                let matchCheckbox = true;
                if (checks.some(c => c)) {
                    let matchDateCheck = false; 
                    if (checks.slice(1).some(c => c)) {
                        if (rawRemind) {
                            const parts = rawRemind.split('-'); 
                            if(parts.length === 3) {
                                const rDate = new Date(parts[0], parts[1]-1, parts[2]); rDate.setHours(0,0,0,0);
                                const today = new Date(); today.setHours(0,0,0,0);
                                const tomorrow = new Date(today); tomorrow.setDate(tomorrow.getDate() + 1);
                                const next7 = new Date(today); next7.setDate(next7.getDate() + 7);
                                const next30 = new Date(today); next30.setDate(next30.getDate() + 30);

                                if (checks[1] && rDate < today && !isClosed) matchDateCheck = true;
                                if (checks[2] && rDate.getTime() === today.getTime()) matchDateCheck = true;
                                if (checks[3] && rDate.getTime() === tomorrow.getTime()) matchDateCheck = true;
                                if (checks[4] && rDate >= today && rDate <= next7) matchDateCheck = true;
                                if (checks[5] && rDate >= today && rDate <= next30) matchDateCheck = true;
                            }
                        }
                    } else { matchDateCheck = true; }
                    let matchPlanCheck = true; if (checks[0] && isClosed) matchPlanCheck = false; 
                    matchCheckbox = matchDateCheck && matchPlanCheck;
                }
                return matchDateRange && matchCheckbox;
            });

            $('.filter-checkbox, #dateFrom, #dateTo').on('change', function() { table.draw(); });
        }

      // ====================================================================
        // 🌟 PAYOFF ALERT LOGIC (UPDATED AJAX, AUTO-BRANCH & ID FIX)
        // ====================================================================
        $(document).on('click', '.btn-payoff', function(e) {
            e.preventDefault();
            
            $('#payoffModal').appendTo("body"); 

            const id = $(this).data('id'); 
            const name = $(this).data('name');
            const model = $(this).data('model');
            const branch = $(this).data('branch') || ''; 
            const sales = $(this).data('sales'); 

            $('#modal_lead_id').val(id); 
            $('#modal_customer_name').val(name);
            $('#modal_model').val(model);
            $('#modal_sales_person').val(sales);
            
            let locationVal = "Head Office"; 
            let branchStr = String(branch).toLowerCase(); 
            
            if (branchStr.includes("60m") || branchStr.includes("60")) {
                locationVal = "MG 60M";
            } 
            else if (branchStr.includes("siem") || branchStr.includes("reap") || branchStr.includes("sr")) {
                locationVal = "MG Siem Reap";
            } 
            else if (branchStr.includes("battambang") || branchStr.includes("bat") || branchStr.includes("btb")) {
                locationVal = "MG Battambang";
            } 
            else if (branchStr.includes("leap")) {
                locationVal = "Leap Motors";
            } 
            else if (branchStr.includes("head") || branchStr.includes("hq") || branchStr.includes("quarter")) {
                locationVal = "Head Office";
            }
            
            $('#modal_location').val(locationVal).change();

            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            const yyyy = tomorrow.getFullYear();
            const mm = String(tomorrow.getMonth() + 1).padStart(2, '0');
            const dd = String(tomorrow.getDate()).padStart(2, '0');
            $('#modal_date').val(`${yyyy}-${mm}-${dd}`);

            var myModal = new bootstrap.Modal(document.getElementById('payoffModal'));
            myModal.show();
        });

        $(document).off('submit', '#payoffFormModal').on('submit', '#payoffFormModal', function(e) {
            e.preventDefault(); 

            Swal.fire({
                title: 'Sending Alert...',
                text: 'Broadcasting to Telegram group',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: $(this).attr('action'),
                type: "POST",
                data: $(this).serialize(),
                success: function(res) {
                    $('#payoffModal').modal('hide');
                    Swal.close(); 

                    if(res.success) {
                        Swal.fire({ 
                            icon: 'success', 
                            title: 'Success!', 
                            text: res.message, 
                            timer: 2000, 
                            showConfirmButton: false 
                        }).then(() => location.reload());
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                },
                error: function(xhr) {
                    Swal.close(); 
                    
                    let errorMsg = 'មានបញ្ហាប្រព័ន្ធ សូមព្យាយាមម្តងទៀត។';
                    
                    if(xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        errorMsg = '';
                        $.each(errors, function(key, value) {
                            errorMsg += value[0] + '<br>';
                        });
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    
                    Swal.fire({ 
                        icon: 'error', 
                        title: 'បរាជ័យ!', 
                        html: errorMsg, 
                        confirmButtonColor: '#ef4444' 
                    });
                }
            });
        });

        @if(session('success'))
            Swal.fire({ icon: 'success', title: 'Success!', text: "{{ session('success') }}", confirmButtonColor: '#0ea5e9', timer: 3000 });
        @endif
        @if(session('error'))
            Swal.fire({ icon: 'error', title: 'Failed', text: "{{ session('error') }}", confirmButtonColor: '#ef4444' });
        @endif
        
        // ====================================================================
        // 🔄 TRANSFER LEAD LOGIC
        // ====================================================================
        $(document).on('click', '.btn-transfer', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const name = $(this).data('name');
            const row = $(this).closest('tr');
            
            const currentSale = row.find('td:nth-child(7)').text().trim(); 
            const currentBranch = row.find('td:nth-child(8)').text().trim();      

            Swal.fire({
                title: '<div style="font-family:\'Plus Jakarta Sans\'; font-weight:800; font-size:1.3rem;">Transfer Assignment</div>',
                html: `
                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:15px; padding:15px; text-align:left; margin-bottom:20px;">
                        <div style="margin-bottom:8px;"><span style="font-size:10px; font-weight:800; color:#94a3b8; text-transform:uppercase;">Customer:</span> <span style="font-weight:700; color:#1e293b;">${name}</span></div>
                        <div style="display:flex; gap:10px;">
                            <div style="flex:1;">
                                <span style="font-size:10px; font-weight:800; color:#94a3b8; text-transform:uppercase; display:block;">Current Branch</span>
                                <span style="font-weight:700; color:#10b981;"><i class="fas fa-map-marker-alt"></i> ${currentBranch}</span>
                            </div>
                            <div style="flex:1;">
                                <span style="font-size:10px; font-weight:800; color:#94a3b8; text-transform:uppercase; display:block;">Current Consultant</span>
                                <span style="font-weight:700; color:#ef4444;"><i class="fas fa-user-circle"></i> ${currentSale}</span>
                            </div>
                        </div>
                    </div>
                    <div style="text-align:left;" class="transfer-input-group">
                        <label>Select New Branch <span class="text-danger">*</span></label>
                        <select id="n_branch" class="form-select transfer-select mb-3" style="padding: 12px; border-radius: 8px;">
                            <option value="Head Office" ${currentBranch.includes('Head') ? 'selected' : ''}>Head Office</option>
                            <option value="MG 60M" ${currentBranch.includes('60M') ? 'selected' : ''}>MG 60M</option>
                            <option value="MG Siem Reap" ${currentBranch.includes('Siem') ? 'selected' : ''}>MG Siem Reap</option>
                            <option value="MG Battambang" ${currentBranch.includes('Battambang') ? 'selected' : ''}>MG Battambang</option>
                        </select>
                        <label>Select New Consultant <span class="text-danger">*</span></label>
                        <select id="n_sale" class="form-select transfer-select mb-3" style="padding: 12px; border-radius: 8px;">
                            <option value="" disabled selected>Choose staff member...</option>
                            @foreach($staff ?? [] as $s)
                                <option value="{{$s}}">{{$s}}</option>
                            @endforeach
                        </select>
                        <label>Reason for Transfer <span class="text-danger">*</span></label>
                        <textarea id="n_reason" class="form-control transfer-textarea" rows="2" placeholder="Why are you transferring this lead?" style="padding: 12px; border-radius: 8px;"></textarea>
                    </div>
                `,
                showCancelButton: true, confirmButtonText: 'CONFIRM TRANSFER', confirmButtonColor: 'var(--c-primary)', cancelButtonText: 'CANCEL', customClass: { popup: 'transfer-popup' },
                preConfirm: () => {
                    const newBranch = $('#n_branch').val(); const newSale = $('#n_sale').val(); const reason = $('#n_reason').val().trim();
                    if(!newBranch) { Swal.showValidationMessage('Please select a branch'); return false; }
                    if(!newSale) { Swal.showValidationMessage('Please select a consultant'); return false; }
                    if(!reason || reason.length < 5) { Swal.showValidationMessage('Please provide a reason (at least 5 characters)'); return false; }
                    return { id: id, new_branch: newBranch, new_sale: newSale, comment: reason, _token: "{{ csrf_token() }}" }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Transferring...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                    $.ajax({
                        url: "{{ url('/leads/transfer') }}", type: "POST", data: result.value,
                        success: function(res) {
                            if(res.success) { Swal.fire({ icon: 'success', title: 'Transferred!', timer: 2000, showConfirmButton: false }).then(() => location.reload()); } 
                            else { Swal.fire('Error', res.message || 'Transfer failed', 'error'); }
                        }
                    });
                }
            });
        });

        // ====================================================================
        // 🗓️ RESCHEDULE LOGIC
        // ====================================================================
        $(document).on('click', '.btn-reschedule', function(e) {
            e.preventDefault();
            const id = $(this).data('id'), name = $(this).data('name'), currentRemind = $(this).data('remind');
            Swal.fire({
                title: '<div style="font-family:\'Plus Jakarta Sans\'; font-weight:800; font-size:1.2rem;">Quick Reschedule</div>',
                html: `
                    <div style="background:#f0f9ff; border:1px solid #e0f2fe; border-radius:12px; padding:15px; text-align:left; margin-bottom:15px;">
                        <span style="font-size:10px; font-weight:800; color:#0ea5e9; text-transform:uppercase; display:block; margin-bottom:3px;">Customer</span>
                        <span style="font-weight:700; color:#1e293b;">${name}</span>
                    </div>
                    <div style="text-align:left;">
                        <label style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; margin-bottom:5px; display:block;">Select New Date</label>
                        <input type="date" id="swal_res_date" class="form-control mb-3" value="${currentRemind}" style="border-radius:10px; border:2px solid #f1f5f9; padding:12px; height: auto;">
                        <label style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; margin-bottom:5px; display:block;">Reason</label>
                        <textarea id="swal_res_reason" class="form-control" rows="3" style="border-radius:10px; border:2px solid #f1f5f9; padding:12px;" placeholder="Provide a reason..."></textarea>
                    </div>
                `,
                showCancelButton: true, confirmButtonText: 'UPDATE DATE', confirmButtonColor: '#06b6d4', cancelButtonText: 'CANCEL', customClass: { popup: 'rounded-4' },
                preConfirm: () => {
                    const newDate = $('#swal_res_date').val(); const reason = $('#swal_res_reason').val().trim();
                    if(!newDate) { Swal.showValidationMessage('Please select a date!'); return false; }
                    if(reason.length < 5) { Swal.showValidationMessage('Please provide a reason (at least 5 characters)!'); return false; }
                    return { id: id, new_date: newDate, comment: reason, _token: "{{ csrf_token() }}" };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Updating...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                    $.ajax({
                        url: "{{ url('/leads/reschedule') }}", type: "POST", data: result.value,
                        success: function(res) {
                            if(res.success) { Swal.fire({ icon: 'success', title: 'Rescheduled!', timer: 1500, showConfirmButton: false }).then(() => location.reload()); } 
                            else { Swal.fire('Error', res.message || 'Update failed', 'error'); }
                        }
                    });
                }
            });
        });

        // ====================================================================
        // ⚡ SMART UPDATE LOGIC
        // ====================================================================
        $(document).on('click', '.btn-smart', function(e) {
            e.preventDefault(); const id = $(this).data('id'), name = $(this).data('name'), status = $(this).data('status');
            const lastRemind = $(this).closest('tr').find('td:nth-child(9) div:first-child').text().trim(); 
            let defaultDate = "{{ date('Y-m-d', strtotime('+1 day')) }}";
            
            if (lastRemind && lastRemind !== 'N/A') {
                const parts = lastRemind.split('-');
                if(parts.length === 3) {
                    const mName = parts[1];
                    const mNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                    const mIndex = mNames.indexOf(mName) + 1;
                    const mm = String(mIndex).padStart(2, '0');
                    defaultDate = `${parts[2]}-${mm}-${parts[0]}`;
                }
            }

            Swal.fire({
                title: '<div style="font-weight:800; font-family:\'Plus Jakarta Sans\'">Quick Update</div>',
                html: `<div class="text-start mt-3 transfer-input-group">
                        <label>New Status</label>
                        <select id="swal_st" class="form-select transfer-select mb-3" style="padding: 12px; border-radius: 8px;">
                            @foreach(['Prospecting', 'Appointment', 'Meet & Greet', 'Consulting ( Q & A )', 'Double Open', 'Presentation', 'Test Drive', 'Trail Close', 'Negotiation', 'Double Close', 'Sales ( contract of sales )', 'Delivery', 'Lost'] as $st)
                                <option value="{{$st}}" ${status.toLowerCase() == '{{strtolower($st)}}' ? 'selected' : ''}>{{$st}}</option>
                            @endforeach
                        </select>
                        <label>Note / Remark</label>
                        <textarea id="swal_cm" class="form-control transfer-textarea mb-3" rows="2" placeholder="Customer feedback..." style="padding: 12px; border-radius: 8px;"></textarea>
                        <label>Next Action Date</label>
                        <input type="date" id="swal_rd" class="form-control transfer-select" value="${defaultDate}" style="padding: 12px; border-radius: 8px;">
                    </div>`,
                showCancelButton: true, confirmButtonText: 'Save Update', confirmButtonColor: 'var(--c-primary)', customClass: { popup: 'transfer-popup' },
                preConfirm: () => { return { id: id, status: $('#swal_st').val(), comment: $('#swal_cm').val(), remind_date: $('#swal_rd').val(), _token: "{{ csrf_token() }}" } }
            }).then((result) => { 
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Saving...', didOpen: () => { Swal.showLoading(); }});
                    $.ajax({ url: "{{ url('/leads/quick-update') }}", type: "POST", data: result.value, success: function(res) { if(res.success) location.reload(); }});
                } 
            });
        });
    });
</script>
@endsection