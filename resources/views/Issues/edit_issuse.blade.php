@extends('layouts.homePages')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Noto+Sans+Khmer:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    :root {
        --brand-indigo: #4f46e5;
        --exec-dark: #0f172a;
        --bg-body: #f1f5f9;
        --glass-white: rgba(255, 255, 255, 0.1);
    }

    body { 
        font-family: 'Plus Jakarta Sans', 'Noto Sans Khmer', sans-serif !important; 
        background-color: var(--bg-body); 
    }

    .container-full {
        width: 100% !important;
        max-width: 100% !important;
        padding: 20px 40px !important;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(30px); filter: blur(10px); }
        to { opacity: 1; transform: translateY(0); filter: blur(0); }
    }
    .animate-form { animation: slideUp 0.7s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }

    .card-ticket { 
        border: none; border-radius: 30px; 
        box-shadow: 0 25px 60px rgba(0,0,0,0.08); 
        background: #fff; overflow: hidden; 
        width: 100%; max-width: 1400px; margin: 20px auto;
    }

    .header-ticket { 
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important; 
        padding: 35px 45px; color: white; 
        display: flex; justify-content: space-between; align-items: center;
        border-bottom: 6px solid var(--brand-indigo);
    }

    .header-info h2 { font-weight: 800; font-size: 1.4rem; margin: 0; letter-spacing: 1px; }
    .header-info span { font-weight: 700; font-size: 0.75rem; opacity: 0.8; letter-spacing: 2px; text-transform: uppercase; }

    .btn-cancel-top {
        background: var(--glass-white); border: 1.5px solid rgba(255, 255, 255, 0.3);
        color: white; padding: 10px 25px; border-radius: 12px;
        font-weight: 800; font-size: 0.75rem; text-decoration: none;
        transition: 0.3s; text-transform: uppercase;
    }
    .btn-cancel-top:hover { background: #e11d48; border-color: #e11d48; transform: translateX(-5px); color: white; }

    .form-section-head {
        font-size: 0.9rem; font-weight: 800; color: var(--exec-dark);
        margin-bottom: 25px; padding-bottom: 15px; border-bottom: 2px solid #f1f5f9;
        display: flex; align-items: center; gap: 12px;
    }

    .label-official { 
        font-weight: 800; color: #475569; font-size: 0.75rem; 
        text-transform: uppercase; letter-spacing: 1.2px; margin-bottom: 12px; display: block;
    }
    .kh-sub { font-family: 'Noto Sans Khmer', sans-serif; font-size: 0.65rem; color: var(--brand-indigo); font-weight: 600; }

    .input-official { 
        width: 100%; padding: 16px 20px; background: #f8fafc; border: 2px solid #edf2f7; 
        border-radius: 18px; font-weight: 600; font-size: 0.95rem; transition: 0.3s; color: #1e293b;
    }
    .input-official:focus { border-color: var(--brand-indigo); outline: none; background: #fff; box-shadow: 0 0 0 5px rgba(79, 70, 229, 0.1); }

    .select2-container--default .select2-selection--single {
        height: 56px !important; border: 2px solid #edf2f7 !important;
        border-radius: 18px !important; padding: 14px 18px !important;
    }

    .btn-update-ticket { 
        background: var(--exec-dark); color: white; border: none; border-radius: 20px; 
        padding: 22px; width: 100%; font-weight: 800; font-size: 1.1rem; 
        text-transform: uppercase; letter-spacing: 2px; transition: 0.4s; margin-top: 30px;
    }
    .btn-update-ticket:hover { background: var(--brand-indigo); transform: translateY(-5px); box-shadow: 0 20px 40px rgba(79, 70, 229, 0.3); }

    #prioritySelect { border-left: 10px solid {{ $issue->priority == 'Urgent' ? '#e11d48' : '#4f46e5' }}; }
</style>

<div class="container-full py-4 animate-form">
    <div class="card-ticket shadow-lg">
        <div class="header-ticket">
            <div class="header-info">
                <span><i class="fas fa-fingerprint me-2"></i> DATA MODIFICATION PROTOCOL</span>
                <h2>MODIFY INTEL #{{ $issue->issue_id }}</h2>
            </div>
            
            <a href="{{ route('issues.index') }}" class="btn-cancel-top">
                <i class="fas fa-arrow-left me-1"></i> BACK TO DIRECTORY
            </a>
        </div>

        <form action="{{ route('issues.update', $issue->id) }}" method="POST" class="p-5" id="updateForm">
            @csrf
            @method('PUT')
            
            <div class="form-section-head"><i class="fas fa-project-diagram text-primary"></i> 01. CORE CLASSIFICATION</div>
            <div class="row g-4 mb-5">
                <div class="col-12 col-xl-8">
                    <label class="label-official">Issue Subject <span class="kh-sub">(កម្មវត្ថុនៃបញ្ហា)</span></label>
                    <input type="text" name="subject" class="input-official" value="{{ $issue->subject }}" required>
                </div>
                <div class="col-12 col-xl-4">
                    <label class="label-official">Ticket Identifier <span class="kh-sub">(លេខកូដសំបុត្រ)</span></label>
                    <input type="text" class="input-official" value="{{ $issue->issue_id }}" readonly style="background: #eef2ff; color: #4f46e5; border-color: #c7d2fe; font-family: 'JetBrains Mono';">
                </div>

                <div class="col-md-6">
                    <label class="label-official">Category <span class="kh-sub">(ប្រភេទបញ្ហា)</span></label>
                    <select name="category" class="input-official" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ $issue->category == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="label-official">Relevant Department <span class="kh-sub">(ផ្នែកដែលពាក់ព័ន្ធ)</span></label>
                    <select name="department" class="input-official" required>
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}" {{ $issue->department == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-section-head"><i class="fas fa-shield-alt text-primary"></i> 02. PRIORITY & STATUS MANAGEMENT</div>
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <label class="label-official">Reporting Personnel <span class="kh-sub">(រាយការណ៍ដោយ)</span></label>
                    <select name="reported_by" class="input-official select2-staff" required>
                        @foreach($staffs as $staff)
                            <option value="{{ $staff }}" {{ $issue->reported_by == $staff ? 'selected' : '' }}>{{ $staff }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="label-official">Urgency Priority <span class="kh-sub">(កម្រិតអាទិភាព)</span></label>
                    <select name="priority" id="prioritySelect" class="input-official" required>
                        <option value="Low" {{ $issue->priority == 'Low' ? 'selected' : '' }}>Low - Informational</option>
                        <option value="Medium" {{ $issue->priority == 'Medium' ? 'selected' : '' }}>Medium - Standard</option>
                        <option value="High" {{ $issue->priority == 'High' ? 'selected' : '' }}>High - Priority</option>
                        <option value="Urgent" {{ $issue->priority == 'Urgent' ? 'selected' : '' }}>Urgent - Critical</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="label-official">Lifecycle Status <span class="kh-sub">(ស្ថានភាពបច្ចុប្បន្ន)</span></label>
                    <select name="status" id="statusSelect" class="input-official" required style="border-left: 10px solid #fde68a;">
                        <option value="Open" {{ $issue->status == 'Open' ? 'selected' : '' }}>🔴 Open / Pending</option>
                        <option value="In Progress" {{ $issue->status == 'In Progress' ? 'selected' : '' }}>🟠 In Progress</option>
                        <option value="Resolved" {{ $issue->status == 'Resolved' ? 'selected' : '' }}>🟢 Resolved / Fixed</option>
                        <option value="Closed" {{ $issue->status == 'Closed' ? 'selected' : '' }}>⚫ Closed / Archived</option>
                    </select>
                </div>
            </div>

            <div class="form-section-head"><i class="fas fa-file-invoice text-primary"></i> 03. CASE DOCUMENTATION</div>
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="label-official">Issue Description <span class="kh-sub">(បរិយាយលម្អិតនៃបញ្ហា)</span></label>
                    <textarea name="description" class="input-official" rows="6" required>{{ $issue->description }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="label-official">Administrator Remark <span class="kh-sub">(កំណត់ចំណាំពីអ្នកពិនិត្យ)</span></label>
                    <textarea name="admin_remark" class="input-official" rows="6" placeholder="Enter resolution details or internal notes...">{{ $issue->admin_remark }}</textarea>
                </div>
            </div>

            <button type="submit" class="btn-update-ticket" id="btnUpdate">
                <i class="fas fa-save me-2"></i> COMMIT CHANGES TO DIRECTORY
            </button>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        const successSound = new Audio("https://assets.mixkit.co/active_storage/sfx/2568/2568-preview.mp3");

        $('.select2-staff').select2({ width: '100%' });

        // Change Priority Color
        $('#prioritySelect').on('change', function() {
            const val = $(this).val();
            const colors = { 'Low': '#cbd5e1', 'Medium': '#4f46e5', 'High': '#f59e0b', 'Urgent': '#e11d48' };
            $(this).css('border-left-color', colors[val]);
        });

        // AJAX Update Submission
        $('#updateForm').on('submit', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Commit Changes?',
                text: "Are you sure you want to update this record?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0f172a',
                confirmButtonText: 'Yes, Update Now',
                borderRadius: '20px'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Synchronizing...',
                        text: 'Updating master intelligence directory',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });

                    $.ajax({
                        url: $(this).attr('action'),
                        type: "POST",
                        data: $(this).serialize(),
                        success: function(res) {
                            successSound.play();
                            Swal.fire({
                                icon: 'success',
                                title: 'UPDATED',
                                text: 'Operational record has been synchronized successfully.',
                                timer: 2500,
                                showConfirmButton: false,
                                borderRadius: '20px'
                            }).then(() => {
                                window.location.href = "{{ route('issues.index') }}";
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'FAILED',
                                text: 'Unable to synchronize data. Please check inputs.',
                                confirmButtonColor: '#0f172a'
                            });
                        }
                    });
                }
            });
        });
    });
</script>
@endsection