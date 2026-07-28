@extends('layouts.homePages')
@section('content')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* ================= SALE REPORT STYLE UI ================= */
    :root { 
        --brand-red: #dc2626; 
        --border-color: #e2e8f0; 
        --bg-light: #f8fafc;
        --text-dark: #1e293b;
        --text-muted: #64748b;
        --link-blue: #2563eb;
    }
    
    body { 
        font-family: 'Plus Jakarta Sans', 'Noto Sans Khmer', sans-serif !important; 
        background-color: var(--bg-light); 
        color: var(--text-dark);
        font-size: 13px; 
    }
    
    /* ======== HEADER & BUTTONS ======== */
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
    .page-title { font-size: 16px; font-weight: 800; display: flex; align-items: center; gap: 8px; margin: 0; }
    
    .btn-custom { 
        font-size: 12px; font-weight: 700; padding: 6px 14px; border-radius: 6px; 
        display: inline-flex; align-items: center; gap: 6px; transition: 0.2s;
    }
    .btn-outline-dark { border: 1px solid #cbd5e1; background: #fff; color: var(--text-dark); }
    .btn-outline-dark:hover { background: #f1f5f9; border-color: #94a3b8; }
    .btn-brand { background: var(--brand-red); color: #fff; border: none; }
    .btn-brand:hover { background: #b91c1c; color: #fff; }

    /* ======== SUMMARY CARDS ======== */
    .stat-card { 
        background: #fff; border: 1px solid var(--border-color); border-radius: 8px; 
        padding: 12px 15px; display: flex; align-items: center; gap: 15px; 
        cursor: pointer; transition: 0.2s; 
    }
    .stat-card:hover { border-color: #cbd5e1; box-shadow: 0 4px 6px rgba(0,0,0,0.02); }
    .stat-card.active-filter { border-color: var(--brand-red); background: #fef2f2; }
    
    .icon-box { 
        width: 36px; height: 36px; border-radius: 8px; display: flex; 
        align-items: center; justify-content: center; font-size: 16px; color: #fff; 
    }
    .bg-dark-icon { background: #334155; }
    .bg-success-icon { background: #10b981; }
    .bg-danger-icon { background: #ef4444; }
    .bg-info-icon { background: #3b82f6; }

    .stat-text { display: flex; flex-direction: column; }
    .stat-label { font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; }
    .stat-value { font-size: 18px; font-weight: 800; line-height: 1.2; }

    /* ======== FILTER ROW ======== */
    .filter-wrapper { 
        background: #fff; border: 1px solid var(--border-color); border-radius: 8px; 
        padding: 10px; margin-bottom: 15px; 
    }
    .form-control-sm, .form-select-sm { 
        height: 36px; border: 1px solid var(--border-color); border-radius: 6px; 
        font-size: 12px; font-weight: 500; color: var(--text-dark);
    }
    .form-control-sm:focus, .form-select-sm:focus { 
        border-color: var(--brand-red); box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1); 
    }

    /* ======== TABLE STYLING ======== */
    .table-container { 
        background: #fff; border: 1px solid var(--border-color); 
        border-radius: 8px; overflow: hidden; position: relative; 
    }
    .table-responsive { overflow-x: auto; }
    .table { width: 100%; border-collapse: collapse; margin: 0; }
    
    .table th, .table td { 
        border: 1px solid var(--border-color); 
        padding: 8px 12px; 
        vertical-align: middle; 
        white-space: nowrap; 
    }
    
    .table thead th { 
        background: #f8fafc; font-size: 10px; font-weight: 800; 
        color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; 
    }
    
    .table tbody td { font-size: 12px; font-weight: 500; }
    .table tbody tr:hover td { background-color: #f1f5f9; }

    .sticky-col-1 { position: sticky; left: 0; background: #fff; z-index: 1; }
    .sticky-col-2 { position: sticky; left: 40px; background: #fff; z-index: 1; border-right: 2px solid var(--border-color) !important; }
    .table thead .sticky-col-1, .table thead .sticky-col-2 { background: #f8fafc; z-index: 2; }
    .table tbody tr:hover .sticky-col-1, .table tbody tr:hover .sticky-col-2 { background-color: #f1f5f9; }

    .text-blue { color: var(--link-blue); font-weight: 600; }
    .text-success-bold { color: #10b981; font-weight: 800; }
    .text-danger-bold { color: #ef4444; font-weight: 800; }
    
    /* Action Buttons (Small) */
    .action-group { display: flex; gap: 4px; justify-content: flex-end; }
    .btn-xs { 
        width: 26px; height: 26px; display: flex; align-items: center; justify-content: center; 
        border: 1px solid var(--border-color); background: #fff; border-radius: 4px; 
        color: var(--text-muted); font-size: 11px; transition: 0.2s;
    }
    .btn-xs:hover { background: #f1f5f9; color: var(--text-dark); border-color: #cbd5e1; }

    /* Scrollbar */
    .table-responsive::-webkit-scrollbar { height: 10px; }
    .table-responsive::-webkit-scrollbar-track { background: var(--bg-light); border-top: 1px solid var(--border-color); }
    .table-responsive::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .table-responsive::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>

<div class="container-fluid flex-grow-1">
    
    <!-- 1. HEADER -->
    <div class="page-header">
        <h1 class="page-title"><i class="fas fa-layer-group text-danger"></i> Personnel Directory</h1>
        <div class="d-flex gap-2">
            <button class="btn-custom btn-outline-dark" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="fas fa-file-import"></i> Import CSV
            </button>
            <a href="{{ route('employees.export') }}" class="btn-custom btn-outline-dark text-success" style="border-color: #10b981;">
                <i class="fas fa-file-excel"></i> Export
            </a>
            <a href="{{ route('employees.create') }}" class="btn-custom btn-brand shadow-sm">
                <i class="fas fa-plus"></i> New Staff
            </a>
        </div>
    </div>

    <!-- 🌟 2. SUMMARY CARDS (មានបន្ថែមUpcoming 🌟 -->
    <div class="row g-3 mb-3">
        @php
            $today = \Carbon\Carbon::today();
            $totalEmp = \App\Models\EmployeeModel::count();
            
            // អ្នកធ្វើការបច្ចុប្បន្ន (Active ហើយដល់ថ្ងៃកុងត្រា ឬ អត់បានដាក់ថ្ងៃកុងត្រា)
            $activeEmp = \App\Models\EmployeeModel::where('status', 1)
                ->where(function($q) use ($today) {
                    $q->whereDate('contract', '<=', $today)->orWhereNull('contract');
                })->count();
            
            // អ្នកត្រៀមចូលធ្វើការ (Active តែថ្ងៃកុងត្រាធំជាងថ្ងៃនេះ)
            $upcomingEmp = \App\Models\EmployeeModel::where('status', 1)
                ->whereDate('contract', '>', $today)->count();
                
            $resignedEmp = \App\Models\EmployeeModel::where('status', 0)->count();

            $stats = [
                ['Total Reports', $totalEmp, 'fa-users', 'bg-dark-icon', 'all'],
                ['Active Staff', $activeEmp, 'fa-check', 'bg-success-icon', 'active'],
                ['Upcoming Staff', $upcomingEmp, 'fa-clock', 'bg-info-icon', 'upcoming'], // 🌟 កាតថ្មី
                ['Inactive / Resigned', $resignedEmp, 'fa-exclamation-triangle', 'bg-danger-icon', '0'] 
            ];
            $currentStatus = request('status', 'all');
        @endphp

        @foreach($stats as $stat)
            @php $isActive = ($currentStatus === (string)$stat[4] || ($currentStatus === '' && $stat[4] === 'all')) && $stat[4] !== ''; @endphp
            <div class="col-xl-3 col-md-6">
                <div class="stat-card {{ $isActive ? 'active-filter' : '' }}" 
                     style="{{ $stat[4] !== '' ? 'cursor: pointer;' : '' }}" 
                     onclick="{{ $stat[4] !== '' ? "applyStatusFilter('".$stat[4]."')" : '' }}">
                    <div class="icon-box {{ $stat[3] }}"><i class="fas {{ $stat[2] }}"></i></div>
                    <div class="stat-text">
                        <span class="stat-label">{{ $stat[0] }}</span>
                        <span class="stat-value">{{ $stat[1] }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- 3. FILTER ROW -->
    <div class="filter-wrapper">
        <form method="GET" action="{{ route('employees.index') }}" id="filterForm" class="row g-2 align-items-center">
            <input type="hidden" name="status" id="filterStatus" value="{{ request('status') }}">
            
            <div class="col-md-2">
                <select name="company" class="form-control form-select-sm" onchange="this.form.submit()">
                    <option value="">All Entities...</option>
                    @foreach($companies as $com)
                        <option value="{{ $com->id }}" {{ request('company') == $com->id ? 'selected' : '' }}>{{ $com->company_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="branch" class="form-control form-select-sm" onchange="this.form.submit()">
                    <option value="">All Branches...</option>
                    @if(isset($branches))
                        @foreach($branches as $br)
                            <option value="{{ $br->id }}" {{ request('branch') == $br->id ? 'selected' : '' }}>{{ $br->branch_name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-2">
                <select name="dept" class="form-control form-select-sm" onchange="this.form.submit()">
                    <option value="">All Departments...</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('dept') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 ms-auto">
                <div class="input-group">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search by customer, phone, or ID...">
                    <button class="btn btn-brand btn-sm px-3 fw-bold" style="border-radius: 0 6px 6px 0;" type="submit">Filter</button>
                    <a href="{{ route('employees.index') }}" class="btn btn-outline-dark btn-sm px-3 fw-bold ms-1" style="border-radius: 6px;">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <!-- 4. MAIN TABLE -->
    <div class="table-container shadow-sm">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th class="text-center sticky-col-1" style="width: 40px;">#</th>
                        <th class="sticky-col-2">CUSTOMER NAME (EN/KH)</th>
                        <th>PHONE NUMBER</th>
                        <th>EMPLOYEE ID</th>
                        <th>GENDER</th>
                        <th>AGE / DOB</th>
                        <th>POSITION ROLE</th>
                        <th>DEPARTMENT</th>
                        <th>BRANCH</th>
                        <th>COMPANY</th>
                        <th>CONTRACT DATE</th>
                        <th>SYSTEM USER ID</th>
                        <th class="text-center">STATUS</th>
                        <th class="text-center" style="width: 90px;">MANAGE</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employees as $index => $item)
                        @php 
                            $encryptedId = encrypt($item->id); 
                            
                            // 🌟 គណនាថ្ងៃសម្រាប់ធ្វើ Alert 🌟
                            $isUpcoming = $item->status == 1 && $item->contract && \Carbon\Carbon::parse($item->contract)->startOfDay()->gt(\Carbon\Carbon::today());
                            $daysLeft = $isUpcoming ? \Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($item->contract)->startOfDay()) : 0;
                        @endphp
                        <tr>
                            <td class="text-center sticky-col-1" style="color: var(--text-muted);">{{ ($employees->currentPage() - 1) * $employees->perPage() + $index + 1 }}</td>
                            
                            <td class="sticky-col-2">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $item->image_employee ? url('/imageEmployee/' . $item->image_employee) : url('backend/assets/img/avatars/1.png') }}" 
                                         style="width:26px; height:26px; border-radius:4px; object-fit:cover; border: 1px solid var(--border-color);">
                                    <div>
                                        <div class="fw-bold" style="color: var(--text-dark);">{{ $item->full_name }}</div>
                                        @if($item->first_name_kh || $item->last_name_kh)
                                            <div style="font-size: 10px; color: var(--text-muted);">{{ $item->first_name_kh }} {{ $item->last_name_kh }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="text-blue">{{ $item->phone_number ?? '---' }}</td>
                            <td class="fw-bold" style="color: var(--text-muted);">{{ $item->user_id }}</td>
                            <td>{{ $item->gender }}</td>
                            <td><span class="text-danger fw-bold">{{ $item->age }}</span> <span style="font-size: 10px; color: var(--text-muted);">({{ $item->dob ? $item->dob->format('d/m/Y') : '-' }})</span></td>
                            <td class="fw-bold">{{ $item->position->name ?? 'N/A' }}</td>
                            <td>{{ $item->department->name ?? 'N/A' }}</td>
                            <td>{{ $item->branch->branch_name ?? 'N/A' }}</td>
                            <td>{{ $item->company->company_name ?? 'N/A' }}</td>
                            
                            <!-- បន្លិចពណ៌បើមិនទាន់ដល់ថ្ងៃ -->
                            <td class="{{ $isUpcoming ? 'fw-bold text-primary' : '' }}">
                                {{ $item->contract ? $item->contract->format('d-M-Y') : '---' }}
                            </td>
                            
                            <td><code style="color: #0f172a; background: transparent;">{{ $item->official_userid ?: '---' }}</code></td>

                            <!-- 🌟 STATUS WITH UPCOMING ALERT 🌟 -->
                            <td class="text-center">
                                @if($item->status == 0)
                                    <span class="text-danger-bold">INACTIVE</span>
                                @elseif($isUpcoming)
                                    <!-- Alert ប្រាប់ចំនួនថ្ងៃ -->
                                    <span class="badge" style="background-color: #fef08a; color: #b45309; border: 1px solid #fde047; font-size: 10px; padding: 4px 8px;">
                                        <i class="fas fa-hourglass-half me-1"></i> IN {{ (int)$daysLeft }} DAYS
                                    </span>
                                @else
                                    <span class="text-success-bold">ACTIVE</span>
                                @endif
                            </td>

                            <td>
                                <div class="action-group">
                                    <a href="{{ route('employees.details', $encryptedId) }}" class="btn-xs" title="View"><i class="fas fa-eye text-primary"></i></a>
                                    <a href="{{ route('employees.edit', $encryptedId) }}" class="btn-xs" title="Edit"><i class="fas fa-pen text-dark"></i></a>
                                    
                                    @if($item->status == 1)
                                        <button type="button" onclick="resignEmployee('{{ $encryptedId }}')" class="btn-xs" title="Resign"><i class="fas fa-user-minus text-warning"></i></button>
                                    @endif
                                    
                                    <button type="button" onclick="deleteEmployee('{{ $encryptedId }}')" class="btn-xs" title="Archive"><i class="fas fa-trash-alt text-danger"></i></button>
                                    
                                    <form id="delete-form-{{ $encryptedId }}" action="{{ route('employees.destroy', $encryptedId) }}" method="POST" style="display: none;">@csrf @method('DELETE')</form>
                                    <form id="resign-form-{{ $encryptedId }}" action="{{ route('employees.resign', $encryptedId) }}" method="POST" style="display: none;">@csrf @method('PATCH')</form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" class="text-center py-4" style="color: var(--text-muted);">
                                <i class="fas fa-inbox fa-2x mb-2" style="opacity: 0.3;"></i>
                                <div class="fw-bold">No data found</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-white border-top p-2 d-flex justify-content-end">
            {!! $employees->appends(request()->query())->links('pagination::bootstrap-4') !!}
        </div>
    </div>
</div>

<!-- Modal Import (រក្សាទុកធម្មតា) -->
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 8px;">
            <div class="modal-header border-bottom pb-3">
                <h6 class="modal-title fw-bold"><i class="fas fa-file-import me-2 text-primary"></i> Import CSV Data</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('employees.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body py-4 text-center px-4">
                    <a href="{{ route('employees.download_template') }}" class="btn btn-dark btn-sm fw-bold mb-4">
                        <i class="fas fa-download me-1"></i> Download Template
                    </a>
                    <input type="file" name="file" class="form-control form-control-sm" accept=".xlsx, .xls, .csv" required>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light btn-sm fw-bold border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-brand btn-sm fw-bold px-4">Start Sync</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function applyStatusFilter(status) {
        document.getElementById('filterStatus').value = status === 'all' ? '' : status;
        document.getElementById('filterForm').submit();
    }

    window.deleteEmployee = function(id) {
        Swal.fire({
            title: 'Archive Record?', text: "It will be moved to archive.", icon: 'error',
            showCancelButton: true, confirmButtonColor: '#dc2626', cancelButtonColor: '#64748b', confirmButtonText: 'Archive'
        }).then((result) => { if (result.isConfirmed) { document.getElementById(`delete-form-${id}`).submit(); } })
    }

    window.resignEmployee = function(id) {
        Swal.fire({
            title: 'Mark Inactive?', text: "Node status will be updated.", icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#f59e0b', cancelButtonColor: '#64748b', confirmButtonText: 'Confirm'
        }).then((result) => { if (result.isConfirmed) { document.getElementById(`resign-form-${id}`).submit(); } })
    }
</script>

@endsection