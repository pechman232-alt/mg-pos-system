@extends('layouts.homePages')
@section('content')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    :root { --brand-indigo: #4f46e5; --brand-success: #10b981; }
    body { font-family: 'Plus Jakarta Sans', 'Noto Sans Khmer', sans-serif !important; background-color: #f8fafc; }
    
    /* Summary Card Styling */
    .stat-card { border: none; border-radius: 20px; transition: 0.3s; background: #fff; border: 1px solid #f1f5f9; }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
    .icon-shape { width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 12px; }
    
    /* Table & Filter Styling */
    .card-main { border: none; border-radius: 20px; box-shadow: 0 4px 25px rgba(0,0,0,0.05); background: #fff; overflow: hidden; }
    .filter-wrapper { background: #fff; border-radius: 20px; border: 1px solid #eef2f6; margin-bottom: 25px; padding: 25px; }
    .table thead th { background-color: #f8fafc; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; color: #64748b; padding: 15px; border-bottom: 1px solid #f1f5f9; }
    
    .btn-action { border-radius: 10px; font-weight: 700; padding: 10px 20px; transition: 0.3s; }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Personnel Intelligence</h4>
            <p class="text-muted small mb-0">Manage organizational nodes and digital identities</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary btn-action" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="fas fa-file-import me-2"></i> IMPORT
            </button>
            <a href="{{ route('employees.export') }}" class="btn btn-outline-success btn-action">
                <i class="fas fa-file-export me-2"></i> EXPORT
            </a>
            <a href="{{ route('employees.create') }}" class="btn btn-primary btn-action shadow-sm">
                <i class="fas fa-plus me-2"></i> ADD STAFF
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card p-3 d-flex align-items-center gap-3">
                <div class="icon-shape bg-label-primary"><i class="fas fa-users text-primary"></i></div>
                <div>
                    <h5 class="fw-800 mb-0">{{ $employees->total() }}</h5>
                    <small class="text-muted text-uppercase fw-bold" style="font-size: 10px;">Total Personnel</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card p-3 d-flex align-items-center gap-3">
                <div class="icon-shape bg-label-success"><i class="fas fa-user-check text-success"></i></div>
                <div>
                    <h5 class="fw-800 mb-0">{{ $employees->where('status', 1)->count() }}</h5>
                    <small class="text-muted text-uppercase fw-bold" style="font-size: 10px;">Active Nodes</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card p-3 d-flex align-items-center gap-3">
                <div class="icon-shape bg-label-warning"><i class="fas fa-building text-warning"></i></div>
                <div>
                    <h5 class="fw-800 mb-0">{{ $companies->count() }}</h5>
                    <small class="text-muted text-uppercase fw-bold" style="font-size: 10px;">Entities</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card p-3 d-flex align-items-center gap-3">
                <div class="icon-shape bg-label-info"><i class="fas fa-sitemap text-info"></i></div>
                <div>
                    <h5 class="fw-800 mb-0">{{ $departments->count() }}</h5>
                    <small class="text-muted text-uppercase fw-bold" style="font-size: 10px;">Departments</small>
                </div>
            </div>
        </div>
    </div>

    <div class="filter-wrapper shadow-sm">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="small fw-bold text-muted mb-1 text-uppercase">Live Search</label>
                <div class="input-group input-group-merge">
                    <span class="input-group-text border-0 bg-light"><i class="fas fa-search"></i></span>
                    <input type="text" id="ajaxSearch" class="form-control border-0 bg-light fw-semibold" placeholder="Search name, ID, phone...">
                </div>
            </div>
            <div class="col-md-3">
                <label class="small fw-bold text-muted mb-1 text-uppercase">Entity</label>
                <select id="ajaxCompany" class="form-select border-0 bg-light fw-semibold">
                    <option value="">All Entities</option>
                    @foreach($companies as $com)
                        <option value="{{ $com->id }}">{{ $com->company_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="small fw-bold text-muted mb-1 text-uppercase">Department</label>
                <select id="ajaxDept" class="form-select border-0 bg-light fw-semibold">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-dark w-100 fw-bold shadow-sm" style="height: 45px;" onclick="location.reload()">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="card card-main">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Personnel Identity</th>
                        <th>Organizational Unit</th>
                        <th>Connection</th>
                        <th>System Access</th>
                        <th class="text-center">Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody id="employeeData">
                    @include('Employees.employee_table_partial')
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-bottom pb-3">
                <h5 class="modal-title fw-bold"><i class="fas fa-file-excel me-2 text-success"></i> Import Personnel Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('employees.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body py-4 text-center">
                    <div class="mb-3">
                        <i class="fas fa-cloud-upload-alt fa-3x text-light"></i>
                    </div>
                    <p class="text-muted small">Please select a valid <b>.xlsx</b> or <b>.csv</b> file based on the system template.</p>
                    <input type="file" name="file" class="form-control" accept=".xlsx, .xls, .csv" required>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-label-secondary fw-bold" data-bs-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">START SYNC</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        function fetchData(page = 1) {
            let search = $('#ajaxSearch').val();
            let company = $('#ajaxCompany').val();
            let dept = $('#ajaxDept').val();
            $('#employeeData').css('opacity', '0.5');

            $.ajax({
                url: "{{ route('employees.index') }}?page=" + page + "&search=" + search + "&company=" + company + "&dept=" + dept,
                success: function(data) {
                    $('#employeeData').html(data).animate({opacity: 1}, 200);
                }
            });
        }

        $('#ajaxSearch').on('keyup', function() { fetchData(1); });
        $('#ajaxCompany, #ajaxDept').on('change', function() { fetchData(1); });

        $(document).on('click', '#laravel-ajax-pagination a', function(e) {
            e.preventDefault();
            let page = $(this).attr('href').split('page=')[1];
            fetchData(page);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        window.deleteEmployee = function(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to archive this personnel node?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, archive it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $(`#delete-form-${id}`).submit();
                }
            })
        }
    });
</script>
@endsection