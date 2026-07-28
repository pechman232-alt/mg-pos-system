@extends('layouts.homePages')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Kantumruy+Pro:wght@400;500;600;700&family=JetBrains+Mono:wght@700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* ==========================================================================
       ✨ 1. PROFESSIONAL & FULL-SCREEN DESIGN SYSTEM
       ========================================================================== */
    :root {
        --primary: #2563eb;       /* Royal Blue */
        --primary-light: #eff6ff;
        --success: #059669;       /* Emerald */
        --warning: #d97706;       /* Amber */
        --danger: #dc2626;        /* Red */
        --dark: #0f172a;          /* Slate 900 */
        --muted: #64748b;         /* Slate 500 */
        --border-light: #f1f5f9;
        --border: #e2e8f0;
        --bg-app: #f8fafc;
        --card-bg: #ffffff;
        --shadow-sm: 0 1px 2px rgba(0,0,0,0.04);
        --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.05);
        --shadow-hover: 0 10px 15px -3px rgba(0,0,0,0.08);
        --radius-lg: 12px;
        --radius-md: 8px;
    }
    body { font-family: 'Plus Jakarta Sans', 'Kantumruy Pro', sans-serif !important; background-color: var(--bg-app); color: var(--dark); }
    
    /* 🌟 លាតពេញ ១០០% និងបន្ថយ Padding ជុំវិញ */
    .container-full { width: 100%; max-width: 100%; padding: 1rem 1.25rem; margin: 0; }
    
    /* ==========================================================================
       📊 CLEAN SUMMARY CARDS
       ========================================================================== */
    .summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1rem; }
    .stat-card-pro { 
        background: var(--card-bg); border-radius: var(--radius-lg); padding: 1.25rem; 
        border: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; 
        transition: all 0.2s ease; box-shadow: var(--shadow-sm); cursor: pointer; position: relative;
    }
    .stat-card-pro:hover { transform: translateY(-2px); box-shadow: var(--shadow-hover); border-color: #cbd5e1; }
    
    /* Active Filter States */
    .stat-card-pro.active-filter { border-color: var(--primary); box-shadow: 0 0 0 1px var(--primary); background: #f8fafc; }
    .stat-card-pro.active-filter-booking { border-color: var(--warning); box-shadow: 0 0 0 1px var(--warning); background: #fffbeb; }
    .stat-card-pro.active-filter-payoff { border-color: var(--success); box-shadow: 0 0 0 1px var(--success); background: #ecfdf5; }

    .stat-title { font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
    .stat-value { font-size: 22px; font-weight: 800; color: var(--dark); margin: 0; line-height: 1.2; font-family: 'JetBrains Mono', monospace; }
    .stat-icon-box { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; }

    /* ==========================================================================
       🏢 BRANCH SUMMARY TABLE
       ========================================================================== */
    .summary-table-card { 
        background: var(--card-bg); border-radius: var(--radius-lg); border: 1px solid var(--border); 
        box-shadow: var(--shadow-sm); margin-bottom: 1rem; overflow: hidden;
    }
    .summary-header-clean { 
        background: #ffffff; padding: 0.75rem 1.25rem; border-bottom: 1px solid var(--border); 
        font-weight: 700; color: var(--dark); display: flex; align-items: center; gap: 10px; font-size: 14px;
    }
    .table-summary { width: 100%; border-collapse: collapse; text-align: center; margin: 0; }
    .table-summary th { background: #f8fafc; padding: 8px 15px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--muted); border-bottom: 1px solid var(--border); }
    .table-summary td { padding: 10px 15px; font-size: 13px; border-bottom: 1px solid var(--border-light); vertical-align: middle; color: var(--dark); font-weight: 600; }
    .table-summary tfoot th { background: #f1f5f9; font-size: 13px; font-weight: 800; color: var(--dark); border-top: 2px solid var(--border); padding: 10px 15px; }

    /* ==========================================================================
       📋 MAIN TABLE & HEADER
       ========================================================================== */
    .main-data-card { 
        border-radius: var(--radius-lg); background: var(--card-bg); border: 1px solid var(--border); 
        overflow: hidden; box-shadow: var(--shadow-sm); 
    }
    .header-clean { 
        background: #ffffff; padding: 1rem 1.25rem; border-bottom: 1px solid var(--border); 
        display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;
    }
    
    .table-wrapper { padding: 0; background: #fff; overflow-x: auto; }
    .table-custom { min-width: 1400px; width: 100%; border-collapse: collapse !important; margin: 0; }
    
    .table-custom th, .table-custom td { border-bottom: 1px solid var(--border-light); border-right: 1px solid var(--border-light); }
    .table-custom th:last-child, .table-custom td:last-child { border-right: none; }
    .table-custom th { 
        background: #f8fafc; padding: 10px 12px; color: var(--muted); 
        font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; vertical-align: middle; 
        border-bottom: 2px solid var(--border);
    }
    .table-custom td { padding: 8px 12px; vertical-align: middle; font-size: 12.5px; font-weight: 500; white-space: nowrap; color: var(--dark); }
    .table-custom tbody tr:hover { background-color: #f8fafc; }
    
    /* ==========================================================================
       🏷️ BADGES, NOTES & ICONS
       ========================================================================== */
    .status-badge { padding: 5px 10px; border-radius: 50px; font-size: 10.5px; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; min-width: 85px; text-transform: uppercase; letter-spacing: 0.5px; }
    .st-booking { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
    .st-payoff { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
    .st-delivery { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
    .st-lost { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .st-default { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }

    .btn-action-sm { 
        width: 30px; height: 30px; border-radius: 6px; border: 1px solid var(--border); 
        display: inline-flex; align-items: center; justify-content: center; background: white; 
        transition: 0.2s; color: var(--muted); cursor: pointer; box-shadow: var(--shadow-sm); font-size: 12px;
    }
    .btn-action-sm:hover:not([disabled]) { border-color: var(--primary); color: var(--primary); box-shadow: var(--shadow-md); background: #f8fafc; }

    .note-box {
        display: block; width: 150px; white-space: normal; font-size: 11.5px; line-height: 1.4; 
        padding: 6px 8px; border-radius: 6px; cursor: pointer; transition: 0.2s; text-align: left;
    }
    .note-box.empty { background: #f8fafc; border: 1px dashed #cbd5e1; color: #94a3b8; text-align: center; }
    .note-box.empty:hover { background: #f1f5f9; color: var(--muted); border-color: #94a3b8; }
    .note-box.filled { background: #fffbeb; border: 1px solid #fde68a; color: #b45309; font-weight: 600; box-shadow: var(--shadow-sm); }
    .note-box.filled:hover { background: #fef3c7; border-color: #f59e0b; }

    .branch-tag { display: inline-flex; align-items: center; gap: 6px; font-weight: 600; font-size: 12px; color: var(--dark); }
    .b-icon { width: 22px; height: 22px; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 10px; }

    /* ==========================================================================
       🎛️ CONTROLS
       ========================================================================== */
    .control-input {
        background: #ffffff; color: var(--dark); border: 1px solid var(--border);
        border-radius: 6px; padding: 6px 12px; height: 36px; font-weight: 500; font-size: 13px; 
        outline: none; transition: 0.2s; box-shadow: var(--shadow-sm);
    }
    .control-input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-light); }
    
    .btn-primary-action {
        background: var(--primary); color: #fff; border: none; border-radius: 6px; padding: 0 16px; height: 36px;
        font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; 
        transition: 0.2s; box-shadow: var(--shadow-sm); text-decoration: none;
    }
    .btn-primary-action:hover { background: #1d4ed8; transform: translateY(-1px); box-shadow: var(--shadow-md); color: #fff; }
    
    .btn-outline-action {
        background: #fff; color: var(--dark); border: 1px solid var(--border); border-radius: 6px; padding: 0 16px; height: 36px;
        font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; 
        transition: 0.2s; box-shadow: var(--shadow-sm); text-decoration: none;
    }
    .btn-outline-action:hover { background: #f8fafc; border-color: #cbd5e1; color: var(--dark); }

    .swal-custom-textarea:focus { border-color: var(--primary) !important; outline: none !important; box-shadow: 0 0 0 3px var(--primary-light) !important; }
</style>

<div class="container-full">
    
    <!-- 📊 CLICKABLE SUMMARY CARDS -->
    <div class="summary-grid text-start">
        <div class="stat-card-pro filter-trigger" data-target-status="all" title="View all records">
            <div>
                <div class="stat-title">Total Revenue</div>
                <h4 class="stat-value text-primary" id="sum-revenue">${{ number_format($totalRevenue ?? 0, 2) }}</h4>
            </div>
            <div class="stat-icon-box" style="background: var(--primary-light); color: var(--primary);"><i class="fas fa-wallet"></i></div>
        </div>
        
        <div class="stat-card-pro filter-trigger" data-target-status="booking" title="Filter by Booking">
            <div>
                <div class="stat-title">Active Bookings</div>
                <h4 class="stat-value text-warning" id="sum-booking">0</h4>
            </div>
            <div class="stat-icon-box" style="background: #fffbeb; color: var(--warning);"><i class="fas fa-file-signature"></i></div>
        </div>
        
        <div class="stat-card-pro filter-trigger" data-target-status="payoff" title="Filter by Payoff">
            <div>
                <div class="stat-title">Total Payoffs</div>
                <h4 class="stat-value text-success" id="sum-payoff">0</h4>
            </div>
            <div class="stat-icon-box" style="background: #ecfdf5; color: var(--success);"><i class="fas fa-check-circle"></i></div>
        </div>
        
        <div class="stat-card-pro filter-trigger" data-target-status="all" title="View all active cars">
            <div>
                <div class="stat-title">Total Active Cars</div>
                <h4 class="stat-value text-dark" id="sum-cars">0</h4>
            </div>
            <div class="stat-icon-box" style="background: #f1f5f9; color: var(--muted);"><i class="fas fa-car"></i></div>
        </div>
    </div>

    <!-- 🏢 BRANCH SUMMARY TABLE -->
    <div class="summary-table-card">
        <div class="summary-header-clean">
            <i class="fas fa-chart-bar text-primary"></i> Branch Performance Overview
        </div>
        <div class="table-responsive">
            <table class="table-summary">
                <thead>
                    <tr>
                        <th class="text-start ps-4">Branch Name</th>
                        <th>Active Cars</th>
                        <th>Bookings</th>
                        <th>Payoffs</th>
                        <th class="text-end pe-4">Revenue Generated</th>
                    </tr>
                </thead>
                <tbody id="summaryTableBody">
                    <!-- Data rendered by JS -->
                </tbody>
                <tfoot>
                    <tr>
                        <th class="text-start ps-4">GRAND TOTAL</th>
                        <th id="tot-cars">0</th>
                        <th id="tot-booking" class="text-warning">0</th>
                        <th id="tot-payoff" class="text-success">0</th>
                        <th id="tot-revenue" class="text-end text-primary pe-4 font-monospace">$0.00</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- 🗂️ MAIN DATA TABLE -->
    <div class="main-data-card">
        <!-- Clean White Header -->
        <div class="header-clean">
            <div>
                <h2 class="m-0" style="font-size: 17px; font-weight: 800; color: var(--dark);">Sales Contracts Registry</h2>
                <p class="small text-muted m-0 mt-1">Manage, filter, and track customer contracts</p>
            </div>
            
            <div class="d-flex align-items-center gap-2 flex-wrap">
                
                <!-- 📅 MONTH FILTER (NEW) -->
                @php
                    // បង្កើតបញ្ជីខែថយក្រោយរយ:ពេល ១ ឆ្នាំដោយស្វ័យប្រវត្តិ
                    $filterMonths = [];
                    for ($i = 0; $i < 12; $i++) {
                        $mDate = \Carbon\Carbon::now()->subMonths($i);
                        $filterMonths[$mDate->format('Y-m')] = $mDate->format('M Y'); 
                    }
                @endphp
                <select id="monthFilter" class="control-input" style="width: 140px;">
                    <option value="">📅 គ្រប់ខែ (All Time)</option>
                    @foreach ($filterMonths as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </select>

                <!-- 🌟 BRANCH FILTER (Role Based Access) -->
                @php
                    $currentUser = auth('users')->user();
                    $isAdminOrDirector = $currentUser ? in_array((int) $currentUser->role_id, [1, 2, 6]) : false;
                    $myBranchName = $currentUser ? \App\Models\BranchModel::where('id', $currentUser->branch_id)->value('branch_name') : 'Unknown';
                @endphp

                <select id="branchFilter" class="control-input" style="width: 170px;" @disabled(!$isAdminOrDirector)>
                    @if ($isAdminOrDirector)
                        <option value="">🏠 All Branches</option>
                        <option value="mg head quarters">MG Head Quarters</option>
                        <option value="siem reap">Siem Reap</option>
                        <option value="Battambong"> Battambong</option>
                        <option value="60m branch">60M Branch</option>
                        <option value="leap motors">Leap Motors</option>
                    @else
                        <option value="{{ strtolower($myBranchName) }}" selected>{{ $myBranchName }}</option>
                    @endif
                </select>

                <!-- Search -->
                <div class="position-relative">
                    <i class="fas fa-search position-absolute text-muted" style="left: 12px; top: 50%; transform: translateY(-50%); font-size: 12px;"></i>
                    <input type="text" id="liveSearch" class="control-input ps-5" style="width: 200px;" placeholder="Search Name, Phone, Note...">
                </div>

             <!-- Actions -->
<div class="d-flex align-items-center gap-2 flex-wrap">
    
    <!-- ... (កូដ Month & Branch Filters នៅទីនេះដដែល) ... -->

    <!-- 🌟 កំណត់សិទ្ធិមើលប៊ូតុង Export 🌟 -->
    @php
        $currentUser = auth('users')->user();
        // សិទ្ធិ Admin/Director (1, 2, 6) ឬ Branch Manager (3, 4, 5, 7, 8) ទើបមានសិទ្ធិ Export
        $canExport = $currentUser ? in_array((int) $currentUser->role_id, [1, 2, 3, 4, 5, 6, 7, 8]) : false;
    @endphp

    @if ($canExport)
        <a href="{{ route('contracts.export', request()->all()) }}" class="btn-outline-action text-success border-success" style="background: #f0fdf4;">
            <i class="fas fa-file-excel"></i> Export
        </a>
    @endif

    <a href="{{ route('contracts.create') }}" class="btn-primary-action">
        <i class="fas fa-plus"></i> New Contract
    </a>
</div>
            </div>
        </div>

        <div class="table-wrapper">
            <table class="table-custom text-start" id="contractTable">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 40px;">#</th>
                        <th>Customer Details</th>
                        <th>Contract ID</th>
                        <th>Phone</th>
                        <th>Order Date</th>
                        <th>Branch</th>
                        <th>Sale Rep</th>
                        <th>Vehicle Model</th>
                        <th>Sale Note / ចំណាំ</th> 
                        <th class="text-end">Sub Total</th>
                        <th class="text-end">Discount</th>
                        <th class="text-end">Deposit</th>
                        <th class="text-end bg-light text-dark">Grand Total</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" style="width: 130px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contracts as $index => $c)
                        @php
                            $statusLower = strtolower(trim($c->status));
                            $isLocked = in_array($statusLower, ['delivery', 'payoff', 'settled', 'paid', 'lost', 'cancelled']);

                            $badgeClass = match($statusLower) {
                                'booking' => 'st-booking',
                                'payoff', 'paid', 'settled' => 'st-payoff',
                                'delivery' => 'st-delivery',
                                'lost', 'cancelled' => 'st-lost',
                                default => 'st-default',
                            };
                        @endphp
                    
                    <!-- 🌟 Update: បន្ថែម data-month ទៅក្នុង Row ដើម្បីឱ្យ JavaScript ចាប់យកបាន -->
                    <tr class="contract-row" 
                        data-branch="{{ strtolower($c->branch_name) }}" 
                        data-status="{{ $statusLower }}" 
                        data-revenue="{{ $c->grand_total }}"
                        data-qty="{{ $c->qty ?? 1 }}"
                        data-month="{{ \Carbon\Carbon::parse($c->order_date)->format('Y-m') }}">
                        
                        <td class="text-center text-muted small">{{ (isset($contracts) && method_exists($contracts, 'firstItem')) ? $contracts->firstItem() + $index : $index + 1 }}</td>
                        
                        <td>
                            <div class="fw-bold text-dark">{{ $c->customer_name }}</div>
                            @if($c->customer_name_kh) <div class="text-[11px] text-muted fw-bold">{{ $c->customer_name_kh }}</div> @endif
                        </td>
                        
                        <td><span class="text-danger fw-bold" style="background: #fef2f2; padding: 2px 6px; border-radius: 4px;">{{ $c->contract_number }}</span></td>
                        <td class="fw-bold font-monospace">{{ $c->phone_number }}</td>
                        <td class="small fw-bold text-muted">{{ \Carbon\Carbon::parse($c->order_date)->format('d-M-Y') }}</td>
                        
                        <td>
                            <div class="branch-tag">
                                @if(str_contains(strtolower($c->branch_name), 'head quarters') || str_contains(strtolower($c->branch_name), '60m'))
                                    <div class="b-icon" style="background: #fee2e2; color: #dc2626;"><i class="fas fa-building"></i></div>
                                @elseif(str_contains(strtolower($c->branch_name), 'siem reap'))
                                    <div class="b-icon" style="background: #e0f2fe; color: #0284c7;"><i class="fas fa-temple"></i></div>
                                @elseif(str_contains(strtolower($c->branch_name), 'battambang'))
                                    <div class="b-icon" style="background: #fef3c7; color: #d97706;"><i class="fas fa-city"></i></div>
                                @elseif(str_contains(strtolower($c->branch_name), 'leap'))
                                    <div class="b-icon" style="background: #f3e8ff; color: #7c3aed;"><i class="fas fa-leaf"></i></div>
                                @else
                                    <div class="b-icon" style="background: #f1f5f9; color: #64748b;"><i class="fas fa-store"></i></div>
                                @endif
                                <span>{{ $c->branch_name }}</span>
                            </div>
                        </td>
                        
                        <td class="text-primary fw-bold">{{ $c->sale_person }}</td>
                        
                        <td>
                            <div class="fw-bold text-dark">{{ $c->model }}</div>
                            <div class="text-[11px] text-muted font-monospace">VIN: {{ $c->vin_number ?? 'N/A' }}</div>
                        </td>
                        
                        <td>
                            @php $hasNote = !empty($c->note); @endphp
                            <textarea id="note-data-{{ $c->id }}" style="display: none;">{{ $c->note ?? '' }}</textarea>
                            <div class="note-box {{ $hasNote ? 'filled' : 'empty' }}" onclick="showNotePopup({{ $c->id }})" title="Click to update note">
                                @if($hasNote)
                                    <i class="fas fa-info-circle"></i> {{ \Illuminate\Support\Str::limit(preg_replace('/\[.*?\] 👤 .*?:/', '', $c->note), 35) }}
                                @else
                                    <i class="fas fa-plus"></i> Add Note
                                @endif
                            </div>
                        </td>
                        
                        <td class="text-end font-monospace text-muted">${{ number_format($c->sub_total, 2) }}</td>
                        <td class="text-end text-danger font-monospace">-${{ number_format($c->discount, 2) }}</td>
                        <td class="text-end text-success font-monospace">-${{ number_format($c->deposit, 2) }}</td>
                        
                        <td class="text-end bg-light">
                            <div class="fw-bold text-dark font-monospace">${{ number_format($c->grand_total, 2) }}</div>
                        </td>
                        
                        <td class="text-center">
                            <span class="status-badge {{ $badgeClass }}">{{ strtoupper($c->status) }}</span>
                        </td>
                        
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('contracts.show', $c->id) }}" class="btn-action-sm" title="View"><i class="fas fa-eye text-primary"></i></a>
                                
                                @if($isLocked)
                                    <button class="btn-action-sm bg-light" disabled title="Locked"><i class="fas fa-lock text-muted opacity-50"></i></button>
                                @else
                                    <form action="{{ route('contracts.update', $c->id) }}" method="POST" class="d-inline">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="Payoff">
                                        <input type="hidden" name="quick_toggle" value="true">
                                        <button type="button" class="btn-action-sm" onclick="confirmPayoff(this)" title="Settle Payoff"><i class="fas fa-check-double text-success"></i></button>
                                    </form>

                                    <form action="{{ route('contracts.update', $c->id) }}" method="POST" class="d-inline">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="Delivery">
                                        <input type="hidden" name="quick_toggle" value="true">
                                        <button type="button" class="btn-action-sm" onclick="confirmDelivery(this)" title="Mark Delivered"><i class="fas fa-truck text-info"></i></button>
                                    </form>

                                    <a href="{{ route('contracts.edit', encrypt($c->id)) }}" class="btn-action-sm" title="Edit"><i class="fas fa-pen text-warning"></i></a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="15" class="py-5 text-center text-muted fw-bold">No active sales registries found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($contracts) && method_exists($contracts, 'hasPages') && $contracts->hasPages())
        <div class="p-3 border-top d-flex justify-content-center bg-light/30">
            {{ $contracts->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById('liveSearch');
        const branchSelect = document.getElementById('branchFilter');
        const monthSelect = document.getElementById('monthFilter'); // 🌟 New: Month Filter Variable
        const rows = document.querySelectorAll('.contract-row');
        const cards = document.querySelectorAll('.filter-trigger');
        
        let activeStatusFilter = 'all';

        // 🌟 1. Logic Clickable Cards
        cards.forEach(card => {
            card.addEventListener('click', function() {
                cards.forEach(c => c.classList.remove('active-filter', 'active-filter-booking', 'active-filter-payoff'));
                let target = this.getAttribute('data-target-status');
                
                if(activeStatusFilter === target) {
                    activeStatusFilter = 'all'; 
                } else {
                    activeStatusFilter = target;
                    if(target === 'booking') this.classList.add('active-filter-booking');
                    else if(target === 'payoff') this.classList.add('active-filter-payoff');
                    else this.classList.add('active-filter');
                }
                filterAndCalculate(); 
            });
        });
        
        // 🌟 2. Logic Live Calculation & Table Summary
        function filterAndCalculate() {
            let searchText = searchInput.value.toLowerCase();
            let selectedBranch = branchSelect.value.toLowerCase();
            let selectedMonth = monthSelect.value; // 🌟 ទាញយកតម្លៃពី Month Filter
            
            let branchStats = {};
            let totalRev = 0, countBooking = 0, countPayoff = 0, totalCars = 0;

            rows.forEach(row => {
                let rowText = row.innerText.toLowerCase();
                let rowBranch = row.getAttribute('data-branch') || 'Unknown';
                let branchDisplayName = row.querySelector('td:nth-child(6) span').innerText; // Branch text
                let rowStatus = row.getAttribute('data-status');
                let rowMonth = row.getAttribute('data-month'); // 🌟 ទាញយកខែរបស់ Row
                let rowRev = parseFloat(row.getAttribute('data-revenue')) || 0;
                let rowQty = parseInt(row.getAttribute('data-qty')) || 1;

                let matchesSearch = rowText.includes(searchText);
                let matchesBranch = selectedBranch === "" || rowBranch.includes(selectedBranch);
                let matchesMonth = selectedMonth === "" || rowMonth === selectedMonth; // 🌟 ឆែកមើលខែ
                let matchesStatus = (activeStatusFilter === 'all') || 
                                    (activeStatusFilter === 'booking' && rowStatus === 'booking') ||
                                    (activeStatusFilter === 'payoff' && ['payoff', 'paid', 'settled'].includes(rowStatus));

                // 🌟 បញ្ចូលលក្ខខណ្ឌ matchesMonth ទៅក្នុងការស្វែងរក
                if (matchesSearch && matchesBranch && matchesStatus && matchesMonth) {
                    row.style.display = ''; 
                    
                    if(!branchStats[branchDisplayName]) {
                        branchStats[branchDisplayName] = { cars: 0, booking: 0, payoff: 0, revenue: 0 };
                    }
                    
                    totalRev += rowRev;
                    totalCars += rowQty;
                    branchStats[branchDisplayName].cars += rowQty;
                    branchStats[branchDisplayName].revenue += rowRev;
                    
                    if(rowStatus === 'booking') { 
                        countBooking++; 
                        branchStats[branchDisplayName].booking++;
                    }
                    if(['payoff', 'paid', 'settled'].includes(rowStatus)) { 
                        countPayoff++; 
                        branchStats[branchDisplayName].payoff++;
                    }
                } else {
                    row.style.display = 'none';
                }
            });

            // Update Summary Table
            let tbodyHTML = '';
            for(let bName in branchStats) {
                let stats = branchStats[bName];
                tbodyHTML += `
                    <tr>
                        <td class="text-start ps-4 fw-bold text-dark">${bName}</td>
                        <td class="fw-bold">${stats.cars}</td>
                        <td class="text-warning fw-bold">${stats.booking}</td>
                        <td class="text-success fw-bold">${stats.payoff}</td>
                        <td class="text-dark fw-bold text-end pe-4 font-monospace">$${stats.revenue.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                    </tr>
                `;
            }
            
            if(Object.keys(branchStats).length === 0) {
                tbodyHTML = `<tr><td colspan="5" class="text-muted text-center py-4">No data available matching your filter</td></tr>`;
            }
            
            document.getElementById('summaryTableBody').innerHTML = tbodyHTML;

            // Update Cards and Totals
            document.getElementById('sum-cars').innerText = totalCars;
            document.getElementById('sum-booking').innerText = countBooking;
            document.getElementById('sum-payoff').innerText = countPayoff;
            document.getElementById('sum-revenue').innerText = "$" + totalRev.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            
            document.getElementById('tot-cars').innerText = totalCars;
            document.getElementById('tot-booking').innerText = countBooking;
            document.getElementById('tot-payoff').innerText = countPayoff;
            document.getElementById('tot-revenue').innerText = "$" + totalRev.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }

        searchInput.addEventListener('keyup', filterAndCalculate);
        branchSelect.addEventListener('change', filterAndCalculate);
        monthSelect.addEventListener('change', filterAndCalculate); // 🌟 ចាប់យកពេលប្តូរខែ
        filterAndCalculate(); 
    });

    // 🌟 3. PROFESSIONAL NOTE HISTORY TRACKING
    function showNotePopup(id) {
        let existingNote = document.getElementById('note-data-' + id).value.trim();
        let currentUser = "{{ auth('users')->user()->full_name ?? auth()->user()->name ?? 'User' }}"; 
        
        let historyHtml = existingNote 
            ? `<div style="max-height: 180px; overflow-y: auto; background: #f1f5f9; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 12.5px; white-space: pre-wrap; margin-bottom: 15px; color: #334155; line-height: 1.6; text-align: left;">${existingNote}</div>`
            : `<div class="text-muted fst-italic mb-3" style="font-size: 13px; text-align: left;"><i class="fas fa-folder-open me-1"></i> មិនទាន់មានចំណាំពីមុនទេ (No previous notes)</div>`;

        Swal.fire({
            title: '<div class="text-start fs-5 fw-bold"><i class="fas fa-comment-dots text-primary me-2"></i>Sale Note Tracking</div>',
            html: `
                <div class="text-start mt-2">
                    <label class="form-label fw-bold text-muted mb-2" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">📝 Note History (Locked)</label>
                    ${historyHtml}
                    
                    <label class="form-label fw-bold text-dark mb-2" style="font-size: 13px;">➕ Add New Update</label>
                    <textarea id="swal-new-note" class="form-control swal-custom-textarea w-100 p-3" style="height: 100px; border-radius: 8px; border: 1px solid #cbd5e1; font-family: 'Kantumruy Pro', sans-serif; resize: none; font-size: 13.5px;" placeholder="វាយបញ្ចូលការអាប់ដេតថ្មីនៅទីនេះ... (ឧ. រង់ចាំស្តុកថ្មី)"></textarea>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-save me-1"></i> Save Note',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#2563eb',
            width: '550px',
            borderRadius: '16px',
            preConfirm: () => {
                const newNoteText = document.getElementById('swal-new-note').value.trim();
                if (!newNoteText) {
                    Swal.showValidationMessage('⚠️ សូមវាយបញ្ចូលចំណាំថ្មីសិន! (Please enter a note)');
                    return false;
                }
                return newNoteText;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let newText = result.value;
                
                let d = new Date();
                let dateOptions = { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true };
                let dateStr = d.toLocaleString('en-GB', dateOptions);
                
                let formattedNewNote = `[${dateStr}] 👤 ${currentUser}:\n${newText}`;
                let finalNote = existingNote ? existingNote + "\n\n" + formattedNewNote : formattedNewNote;

                let form = document.createElement('form');
                form.method = 'POST';
                let baseUrl = "{{ route('contracts.update', ':id') }}";
                form.action = baseUrl.replace(':id', id);
                
                let csrfInput = document.createElement('input');
                csrfInput.type = 'hidden'; csrfInput.name = '_token'; csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);

                let methodInput = document.createElement('input');
                methodInput.type = 'hidden'; methodInput.name = '_method'; methodInput.value = 'PUT';
                form.appendChild(methodInput);

                let noteInput = document.createElement('input');
                noteInput.type = 'hidden'; noteInput.name = 'note'; noteInput.value = finalNote;
                form.appendChild(noteInput);

                let flagInput = document.createElement('input');
                flagInput.type = 'hidden'; flagInput.name = 'update_note_only'; flagInput.value = 'true';
                form.appendChild(flagInput);
                
                document.body.appendChild(form);
                form.submit();
                
                Swal.fire({ title: 'Saving Update...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            }
        });
    }

    // --- SWEETALERT ACTIONS ---
    function confirmPayoff(button) {
        Swal.fire({
            title: 'Settle Payment?',
            text: "Mark this contract as PAID and LOCK the record?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#059669',
            confirmButtonText: 'Yes, Confirm Paid',
            borderRadius: '12px'
        }).then((result) => { 
            if (result.isConfirmed) { 
                Swal.fire({ title: 'Processing...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                button.closest('form').submit(); 
            } 
        });
    }

    function confirmDelivery(button) {
        Swal.fire({
            title: 'Mark as Delivered?',
            text: "Update status to DELIVERY and LOCK the record?",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            confirmButtonText: 'Yes, Delivered!',
            borderRadius: '12px'
        }).then((result) => { 
            if (result.isConfirmed) { 
                Swal.fire({ title: 'Processing...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                button.closest('form').submit(); 
            } 
        });
    }
    
    function confirmDelete(id) {
        Swal.fire({
            title: 'Permanent Delete?',
            text: "Warning: This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'Yes, Delete Now',
            borderRadius: '12px'
        }).then((result) => { 
            if (result.isConfirmed) { 
                Swal.fire({ title: 'Deleting...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                document.getElementById('del-' + id).submit(); 
            } 
        });
    }
</script>
@endsection