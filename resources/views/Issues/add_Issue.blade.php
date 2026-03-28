@extends('layouts.homePages')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Noto+Sans+Khmer:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    :root {
        --brand-indigo: #6366f1;
        --exec-dark: #0f172a;
        --bg-body: #f8fafc;
        --glass-white: rgba(255, 255, 255, 0.15);
    }

    body { 
        font-family: 'Plus Jakarta Sans', 'Noto Sans Khmer', sans-serif !important; 
        background-color: var(--bg-body); 
        margin: 0; padding: 0;
        font-size: 0.85rem;
    }

    .container-full {
        width: 100% !important;
        max-width: 100% !important;
        padding: 15px 35px !important;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(15px); filter: blur(5px); }
        to { opacity: 1; transform: translateY(0); filter: blur(0); }
    }
    .animate-form { animation: slideUp 0.5s ease-out forwards; }

    .card-ticket { 
        border: none; border-radius: 20px; 
        box-shadow: 0 10px 40px rgba(0,0,0,0.04); 
        background: #fff; overflow: hidden; 
        width: 100%; max-width: 1500px; margin: 0 auto;
    }

    .header-ticket { 
        background: linear-gradient(135deg, #6366f1 0%, #a855f7 50%, #ec4899 100%) !important; 
        padding: 25px 40px; color: white; 
        display: flex; justify-content: space-between; align-items: center;
    }

    .header-info h2 { font-weight: 800; font-size: 1.25rem; margin: 0; letter-spacing: 0.5px; }
    .header-info span { font-weight: 700; font-size: 0.65rem; opacity: 0.85; letter-spacing: 2px; text-transform: uppercase; }

    .btn-cancel-top {
        background: var(--glass-white); border: 1px solid rgba(255, 255, 255, 0.3);
        color: white; padding: 8px 20px; border-radius: 10px;
        font-weight: 800; font-size: 0.7rem; text-decoration: none;
        transition: 0.3s; text-transform: uppercase;
    }
    .btn-cancel-top:hover { background: #e11d48; border-color: #e11d48; transform: translateX(-5px); color: white; }

    .form-section-head {
        font-size: 0.8rem; font-weight: 800; color: #64748b;
        margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #f1f5f9;
        display: flex; align-items: center; gap: 8px;
    }

    .label-official { 
        font-weight: 800; color: #475569; font-size: 0.68rem; 
        text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 8px; display: block;
    }
    .kh-sub { font-family: 'Noto Sans Khmer', sans-serif; font-size: 0.62rem; color: #6366f1; font-weight: 600; }

    .input-official { 
        width: 100%; padding: 12px 15px; background: #f8fafc; border: 1.5px solid #edf2f7; 
        border-radius: 12px; font-weight: 600; font-size: 0.85rem; transition: 0.2s; color: #1e293b;
    }
    .input-official:focus { border-color: #6366f1; outline: none; background: #fff; box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.08); }

    .select2-container--default .select2-selection--single {
        height: 48px !important; border: 1.5px solid #edf2f7 !important;
        border-radius: 12px !important; padding: 10px !important;
        background-color: #f8fafc !important; font-size: 0.85rem !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        font-weight: 600 !important; color: #1e293b !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 46px !important; }

    .btn-submit-ticket { 
        background: #1e293b; color: white; border: none; border-radius: 12px; 
        padding: 16px; width: 100%; font-weight: 800; font-size: 0.85rem; 
        text-transform: uppercase; letter-spacing: 1.5px; transition: 0.3s; margin-top: 20px;
        display: flex; justify-content: center; align-items: center; gap: 10px;
    }
    .btn-submit-ticket:hover { background: #6366f1; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(99, 102, 241, 0.2); }
    .btn-submit-ticket:disabled { background: #94a3b8; cursor: not-allowed; transform: none; }

    #prioritySelect { border-left: 6px solid #6366f1; }
    .char-count { font-size: 10px; font-weight: 700; color: #94a3b8; margin-top: 5px; float: right; }
</style>

<div class="container-full py-3 animate-form">
    <div class="card-ticket shadow-lg">
        <div class="header-ticket">
            <div class="header-info">
                <span><i class="fas fa-shield-alt me-2"></i> ISSUE INTELLIGENCE PROTOCOL</span>
                <h2>CREATE OPERATIONS TICKET</h2>
            </div>
            <a href="{{ route('issues.index') }}" class="btn-cancel-top">
                <i class="fas fa-times-circle me-1"></i> CANCEL & RETURN
            </a>
        </div>

        <form action="{{ route('issues.store') }}" method="POST" class="p-4 p-md-5" id="ticketForm">
            @csrf
            
            <div class="form-section-head"><i class="fas fa-database text-primary"></i> 01. GENERAL CLASSIFICATION</div>
            <div class="row g-4 mb-4">
                <div class="col-12 col-xl-6">
                    <label class="label-official">Issue Subject <span class="kh-sub">(កម្មវត្ថុនៃបញ្ហា)</span></label>
                    <input type="text" name="subject" class="input-official" placeholder="Enter problem title..." required autocomplete="off">
                </div>
                <div class="col-md-6 col-xl-3">
                    <label class="label-official">Category <span class="kh-sub">(ប្រភេទបញ្ហា)</span></label>
                    <select name="category" class="input-official" required>
                        <option value="" selected disabled>-- Select --</option>
                        @foreach($categories as $cat) <option value="{{ $cat }}">{{ $cat }}</option> @endforeach
                    </select>
                </div>
                <div class="col-md-6 col-xl-3">
                    <label class="label-official">Department <span class="kh-sub">(ផ្នែកដែលពាក់ព័ន្ធ)</span></label>
                    <select name="department" class="input-official" required>
                        <option value="" selected disabled>-- Select --</option>
                        @foreach($departments as $dept) <option value="{{ $dept }}">{{ $dept }}</option> @endforeach
                    </select>
                </div>
            </div>

            <div class="form-section-head"><i class="fas fa-user-shield text-primary"></i> 02. REPORT DETAILS</div>
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <label class="label-official">Reported By <span class="kh-sub">(រាយការណ៍ដោយបុគ្គលិក)</span></label>
                    <select name="reported_by" class="input-official select2-staff" required>
                        <option value="" selected disabled>Search name...</option>
                        @foreach($staffs as $staff)
                            <option value="{{ $staff }}">{{ $staff }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="label-official">Urgency Priority <span class="kh-sub">(កម្រិតអាទិភាព)</span></label>
                    <select name="priority" id="prioritySelect" class="input-official" required>
                        <option value="Low">Low - Informational</option>
                        <option value="Medium" selected>Medium - Normal</option>
                        <option value="High">High - Priority</option>
                        <option value="Urgent">Urgent - Critical</option>
                    </select>
                </div>
            </div>

            <div class="form-section-head"><i class="fas fa-file-invoice text-primary"></i> 03. FULL DESCRIPTION</div>
            <div class="row g-4">
                <div class="col-12">
                    <label class="label-official">Problem Details <span class="kh-sub">(បរិយាយលម្អិតនៃបញ្ហា)</span></label>
                    <textarea name="description" id="descInput" class="input-official" rows="5" placeholder="Describe the situation clearly..." required></textarea>
                    <span class="char-count" id="charCount">0 / 10 characters minimum</span>
                </div>
            </div>

            <button type="submit" class="btn-submit-ticket" id="submitBtn">
                <span id="btnText"><i class="fas fa-paper-plane me-2"></i> DISPATCH OFFICIAL TICKET</span>
                <span id="btnLoader" class="d-none"><i class="fas fa-circle-notch fa-spin"></i> DISPATCHING...</span>
            </button>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    const successSound = new Audio("https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3");

    // 1. Initialize Select2
    $('.select2-staff').select2({ placeholder: "Search staff name...", allowClear: true });

    // 2. Change Priority Border Color
    $('#prioritySelect').on('change', function() {
        const val = $(this).val();
        const colors = { 'Low': '#cbd5e1', 'Medium': '#6366f1', 'High': '#f59e0b', 'Urgent': '#e11d48' };
        $(this).css('border-left-color', colors[val]);
    });

    // 3. Live Character Count for Description
    $('#descInput').on('input', function() {
        let len = $(this).val().length;
        $('#charCount').text(len + ' characters');
        if(len < 10) { $('#charCount').css('color', '#ef4444'); } 
        else { $('#charCount').css('color', '#10b981'); }
    });

    // 4. ✨ AJAX Submission
    $('#ticketForm').on('submit', function(e) {
        e.preventDefault();
        
        let form = $(this);
        let btn = $('#submitBtn');
        let btnText = $('#btnText');
        let btnLoader = $('#btnLoader');

        Swal.fire({
            title: 'Confirm Dispatch?',
            text: "Are you sure you want to create this ticket?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1e293b',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Dispatch Now',
            borderRadius: '15px'
        }).then((result) => {
            if (result.isConfirmed) {
                // UI Loading State
                btn.prop('disabled', true);
                btnText.addClass('d-none');
                btnLoader.removeClass('d-none');

                $.ajax({
                    url: form.attr('action'),
                    type: "POST",
                    data: form.serialize(),
                    dataType: 'json',
                    success: function(res) {
                        successSound.play();
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: res.message,
                            timer: 2500,
                            showConfirmButton: false,
                            borderRadius: '15px'
                        });

                        // Reset Form
                        form[0].reset();
                        $('.select2-staff').val(null).trigger('change');
                        $('#prioritySelect').css('border-left-color', '#6366f1');
                        $('#charCount').text('0 / 10 characters minimum').css('color', '#94a3b8');
                        
                        // Restore Button
                        btn.prop('disabled', false);
                        btnText.removeClass('d-none');
                        btnLoader.addClass('d-none');
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false);
                        btnText.removeClass('d-none');
                        btnLoader.addClass('d-none');
                        
                        let msg = 'សូមពិនិត្យទិន្នន័យឡើងវិញ!';
                        if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                        
                        Swal.fire({ icon: 'error', title: 'Error', text: msg, confirmButtonColor: '#1e293b' });
                    }
                });
            }
        });
    });
});
</script>
@endsection