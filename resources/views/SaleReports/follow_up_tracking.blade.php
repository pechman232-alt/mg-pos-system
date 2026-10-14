@extends('layouts.homePages')
@section('content')

<!-- 🌟 Import Google Fonts: Inter, Plus Jakarta Sans, JetBrains Mono & Kantumruy Pro 🌟 -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Kantumruy+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
    :root {
        --font-body: 'Inter', sans-serif;
        --font-display: 'Plus Jakarta Sans', sans-serif;
        --font-mono: 'JetBrains Mono', monospace;
    }

    body { 
        font-family: var(--font-body); 
        background-color: #f8fafc;
    }
    
    h1, h2, h3, h4, h5, h6, .font-display, .nav-tabs .nav-link { 
        font-family: var(--font-display); 
    }
    
    .khmer-font { 
        font-family: 'Kantumruy Pro', sans-serif; 
    }
    
    /* Colors */
    .text-blue-custom { color: #2563eb !important; font-weight: 600; }
    .text-red-custom { color: #dc2626 !important; font-weight: 600; }
    .text-green-custom { color: #16a34a !important; font-weight: 600; }
    
    /* Table Container */
    .table-container {
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .table-responsive::-webkit-scrollbar { width: 6px; height: 6px; }
    .table-responsive::-webkit-scrollbar-track { background: #f8fafc; }
    .table-responsive::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    
    /* 🌟 Table Borders Styling 🌟 */
    #posts {
        border-collapse: collapse;
        border: 1px solid #dee2e6;
    }
    #posts thead th {
        font-family: var(--font-display);
        font-size: 11px;
        text-transform: uppercase;
        color: #475569;
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 12px 10px;
        white-space: nowrap;
        vertical-align: middle;
    }
    #posts tbody td {
        font-size: 12px;
        color: #334155;
        vertical-align: middle;
        padding: 10px;
        white-space: nowrap;
        border: 1px solid #e2e8f0;
    }
    
    /* 🌟 Invoice uses JetBrains Mono 🌟 */
    .invoice-badge { 
        font-family: var(--font-mono);
        background: #fee2e2; 
        color: #dc2626; 
        padding: 2px 6px; 
        border-radius: 4px; 
        font-weight: 600; 
        font-size: 11.5px; 
        letter-spacing: 0.5px;
    }
</style>

<div class="container-fluid p-4">

    <!-- Header & Filter -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-3">
        <div>
            <h4 class="mb-0 fw-bold"><i class="fas fa-headset text-danger me-2"></i> Call Center Follow-Up</h4>
            <small class="text-muted khmer-font">ប្រព័ន្ធតាមដានអតិថិជន</small>
        </div>
        
        <form method="GET" action="{{ route('sale-reports.follow-up-tracking') }}" class="d-flex align-items-center gap-2">
            <input type="hidden" name="tab" value="{{ $tab }}">
            
            <select name="branch_id" class="form-select form-select-sm fw-bold text-secondary font-display" style="width: 180px;">
                <option value="">-- All Branches --</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ $branchId == $branch->id ? 'selected' : '' }}>
                        {{ $branch->branch_name }}
                    </option>
                @endforeach
            </select>

            <div class="input-group input-group-sm" style="width: 250px;">
                <input type="text" name="keyword" value="{{ $keyword }}" class="form-control" placeholder="Search customer, phone...">
                <button type="submit" class="btn btn-dark"><i class="fas fa-search"></i></button>
            </div>
        </form>
    </div>

    <!-- Tabs Menu -->
    <ul class="nav nav-tabs mb-4 border-bottom-0 font-display">
        <li class="nav-item">
            <a class="nav-link {{ $tab == 'pending' ? 'active fw-bold text-warning border-warning border-bottom-2' : 'text-muted' }}" 
               href="?tab=pending&branch_id={{ $branchId }}&keyword={{ $keyword }}" 
               style="{{ $tab == 'pending' ? 'border-bottom: 3px solid #f59e0b; background: #fff;' : '' }}">
                <i class="fas fa-phone-volume"></i> Pending ({{ $pendingCount }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab == 'completed' ? 'active fw-bold text-success border-success border-bottom-2' : 'text-muted' }}" 
               href="?tab=completed&branch_id={{ $branchId }}&keyword={{ $keyword }}" 
               style="{{ $tab == 'completed' ? 'border-bottom: 3px solid #10b981; background: #fff;' : '' }}">
                <i class="fas fa-check-circle"></i> Completed ({{ $completedCount }})
            </a>
        </li>
    </ul>

    <!-- Table -->
    <div class="table-container shadow-sm">
        <div class="table-responsive">
            <table id="posts" class="table table-bordered table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 40px;">#</th>
                        <th>Customer Name</th>
                        <th>Phone Number</th>
                        <th>Source</th>
                        <th>Brand</th>
                        <th>Branch</th>
                        <th>Sales Person</th>
                        <th>Contract No</th>
                        <th>Invoice No</th>
                        <th>Invoice Date</th>
                        <th>Delivery Date</th>
                        @if($tab == 'completed')
                            <th>Feedback Comment</th>
                            <th>SA Remark</th>
                        @endif
                        <th class="text-center" style="width: 70px;">Status</th>
                        <th class="text-center" style="width: 100px; position: sticky; right: 0; background: #f8fafc; z-index: 2; border-left: 2px solid #dee2e6;">Manage</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $index => $item)
                        <tr>
                            <td class="text-center text-muted fw-bold">{{ $reports->firstItem() + $index }}</td>
                            <td class="text-uppercase fw-bold text-dark">{{ $item->customer_name }}</td>
                            <td class="text-blue-custom font-mono">{{ $item->phone_number }}</td>
                            <td class="text-muted">{{ $item->source_id ?? 'N/A' }}</td>
                            <td class="text-red-custom">{{ $item->brand_name ?? 'MG' }}</td>
                            <td>{{ $item->branch->branch_name ?? 'N/A' }}</td>
                            <td>{{ $item->employee->full_name ?? 'N/A' }}</td>
                            <td class="font-mono text-muted">{{ $item->contract_no ?? 'N/A' }}</td>
                            <td>
                                @if($item->invoice_no)
                                    <span class="invoice-badge">#{{ $item->invoice_no }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>{{ $item->invoice_date ? date('d-M-Y', strtotime($item->invoice_date)) : 'N/A' }}</td>
                            <td class="text-green-custom">{{ $item->delivery_date ? date('d-M-Y', strtotime($item->delivery_date)) : 'N/A' }}</td>
                            
                            @if($tab == 'completed')
                                <td style="max-width: 250px; white-space: normal;">
                                    @if($item->feedback_command)
                                        <div class="khmer-font text-dark fw-semibold mb-1">{{ Str::limit($item->feedback_command, 60) }}</div>
                                        <small class="text-muted" style="font-size: 10px;"><i class="fas fa-user text-warning"></i> By: {{ $item->created_by }}</small>
                                    @endif
                                </td>
                                <td style="max-width: 200px; white-space: normal;">
                                    @if($item->remark_sa)
                                        <div class="khmer-font text-dark fw-semibold mb-1">{{ Str::limit($item->remark_sa, 50) }}</div>
                                        <small class="text-muted" style="font-size: 10px;"><i class="fas fa-user text-primary"></i> By: {{ $item->created_by }}</small>
                                    @endif
                                </td>
                            @endif
                            
                            <td class="text-center">
                                @if($tab == 'pending')
                                    <span class="badge bg-warning text-dark font-display px-2 py-1">Pending</span>
                                @else
                                    <span class="badge bg-success font-display px-2 py-1">Active</span>
                                @endif
                            </td>
                            
                            <td class="text-center" style="position: sticky; right: 0; background: #fff; z-index: 1; border-left: 2px solid #dee2e6;">
                                @if($tab == 'pending')
                                    <button class="btn btn-sm btn-warning fw-bold px-3 btn-call-modal font-display" 
                                            data-id="{{ encrypt($item->id) }}"
                                            data-customer="{{ $item->customer_name }}"
                                            data-phone="{{ $item->phone_number }}"
                                            data-car="{{ $item->brand_name ?? 'MG' }}"
                                            data-sale="{{ $item->employee->full_name ?? 'N/A' }}"
                                            title="Call Customer">
                                        <i class="fas fa-phone-alt"></i> Call
                                    </button>
                                @else
                                    <a href="{{ url('sale-reports/view', encrypt($item->id)) }}" class="btn btn-sm btn-light border shadow-sm" title="View Report">
                                        <i class="fas fa-eye text-primary"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $tab == 'completed' ? '15' : '13' }}" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-2x mb-2 text-light"></i><br>
                                <span class="font-display">No records found.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-3 font-display">
        {{ $reports->appends(request()->input())->links('paginitaion_backend') }}
    </div>

</div>
<!-- ================= MODAL: CALL WORKSPACE ================= -->
<div class="modal fade" id="callCenterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <h6 class="modal-title fw-bold font-display"><i class="fas fa-phone-volume text-warning me-2"></i> Call Workspace</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <!-- ដាក់ Form គ្របដណ្តប់ទាំង Body និង Footer -->
            <form id="callSubmitForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="bg-light p-3 rounded border mb-4 font-display">
                        <div class="row g-3">
                            <div class="col-6">
                                <small class="text-muted text-uppercase fw-bold" style="font-size: 10px;">Customer Name</small><br>
                                <span class="fw-bold text-dark text-uppercase" id="info_customer"></span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted text-uppercase fw-bold" style="font-size: 10px;">Phone Number</small><br>
                                <span class="fw-bold text-primary font-mono" id="info_phone"></span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted text-uppercase fw-bold" style="font-size: 10px;">Car Details</small><br>
                                <span class="fw-bold text-danger" id="info_car"></span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted text-uppercase fw-bold" style="font-size: 10px;">Sale Agent</small><br>
                                <span class="fw-bold text-dark" id="info_sale"></span>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="report_id" name="report_id">
                    
                    <div class="mb-2">
                        <label class="form-label fw-bold text-dark font-display" style="font-size: 13px;">
                            Customer Feedback <span class="khmer-font text-muted">(មតិយោបល់)</span> <span class="text-danger">*</span>
                        </label>
                        <textarea id="feedback_content" name="feedback_command" rows="4" required 
                                  class="form-control khmer-font shadow-sm" placeholder="វាយបញ្ចូលមតិយោបល់នៅទីនេះ..."></textarea>
                    </div>
                </div>

                <!-- 🌟 ប្រើប្រាស់ Modal Footer ស្តង់ដាររបស់ Bootstrap វិញ 🌟 -->
                <div class="modal-footer bg-light border-top-0 pt-0">
                    <button type="button" class="btn btn-secondary font-display" data-bs-dismiss="modal" style="width: auto !important; display: inline-block;">Cancel</button>
                    <button type="submit" id="btnSave" class="btn btn-warning fw-bold font-display shadow-sm" style="width: auto !important; display: inline-block;">
                        <i class="fas fa-save me-1"></i> Save Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.btn-call-modal').on('click', function() {
            $('#report_id').val($(this).data('id'));
            $('#info_customer').text($(this).data('customer'));
            $('#info_phone').text($(this).data('phone'));
            $('#info_car').text($(this).data('car'));
            $('#info_sale').text($(this).data('sale'));
            
            $('#feedback_content').val('');
            var modal = new bootstrap.Modal(document.getElementById('callCenterModal'));
            modal.show();
        });

        let isSubmitting = false;

        $('#callSubmitForm').off('submit').on('submit', function(e) {
            e.preventDefault();
            
            if (isSubmitting) return; 
            isSubmitting = true; 
            
            let btn = $('#btnSave');
            let originalHtml = btn.html();
            
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');
            
            $.ajax({
                url: "{{ route('sale-reports.quick-feedback') }}", 
                type: "POST",
                data: $(this).serialize(),
                success: function(res) {
                    $('#callCenterModal').modal('hide');
                    let branch = $('select[name="branch_id"]').val();
                    let keyword = $('input[name="keyword"]').val();
                    window.location.href = "?tab=completed&branch_id=" + branch + "&keyword=" + keyword;
                },
                error: function(err) {
                    isSubmitting = false; 
                    btn.prop('disabled', false).html(originalHtml);
                    alert('Error saving record. Please try again!');
                }
            });
        });
    });
</script>

@endsection