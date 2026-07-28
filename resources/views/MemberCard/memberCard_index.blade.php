@extends('layouts.homePages')
@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/davidshimjs-qrcodejs@0.0.2/qrcode.min.js"></script>

<style>
    :root {
        --brand-emerald: #10b981;
        --brand-blue: #002663;
        --brand-red: #CE181E;
        --bg-soft: #f1f5f9;
        --text-dark: #0f172a;
        --border-color: #e2e8f0;
    }

    body { font-family: 'Plus Jakarta Sans', sans-serif !important; background-color: #f8fafc; color: #475569; }
    .fluid-wrapper { width: 100% !important; padding: 1.5rem !important; animation: fadeIn 0.4s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }

    /* HEADER COMMAND BAR */
    .command-bar { 
        background: #fff; border-radius: 16px; padding: 15px 25px; 
        display: flex; justify-content: space-between; align-items: center;
        border: 1px solid var(--border-color); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
        margin-bottom: 20px;
    }
    .page-title { font-size: 1.2rem; font-weight: 800; color: var(--text-dark); margin: 0; display: flex; align-items: center; gap: 10px; }
    .page-title i { color: var(--brand-emerald); }

    /* FILTERS CARD */
    .filter-card { background: #fff; border-radius: 16px; padding: 20px; border: 1px solid var(--border-color); margin-bottom: 20px; }
    .form-control-sm-custom { border-radius: 10px !important; border: 1.5px solid var(--border-color) !important; padding: 10px 15px !important; font-weight: 600; font-size: 0.85rem; height: 45px !important; }
    .form-control-sm-custom:focus { border-color: var(--brand-emerald) !important; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1) !important; }

    /* DATA TABLE */
    .table-container { background: #fff; border-radius: 16px; border: 1px solid var(--border-color); overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
    .table thead th { background: #f8fafc; color: #64748b; font-weight: 800; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em; padding: 15px 20px; border-bottom: 2px solid var(--bg-soft); }
    .table tbody td { padding: 12px 20px; vertical-align: middle; border-bottom: 1px solid var(--bg-soft); font-weight: 600; font-size: 0.85rem; color: var(--text-dark); }
    .table tbody tr:hover { background-color: #fcfdfe; }

    /* BADGES */
    .status-pill { padding: 6px 12px; border-radius: 8px; font-size: 0.7rem; font-weight: 800; display: inline-flex; align-items: center; gap: 5px; text-transform: uppercase; }
    .bg-available { background: #ecfdf5; color: #059669; border: 1px solid #10b98133; }
    .bg-sold { background: #fef2f2; color: #dc2626; border: 1px solid #ef444433; }
    .card-tag { font-family: 'JetBrains Mono', monospace; background: #eff6ff; color: var(--brand-blue); padding: 4px 10px; border-radius: 6px; border: 1px solid #bfdbfe; }

    /* ACTION BUTTONS */
    .btn-circle { width: 34px; height: 34px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; transition: 0.2s; border: 1px solid var(--border-color); background: #fff; color: #64748b; }
    .btn-circle:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.05); color: var(--brand-blue); border-color: var(--brand-blue); }
    .btn-delete:hover { color: var(--brand-red); border-color: var(--brand-red); }

    /* IMPORT MODAL */
    .modal-content-custom { border-radius: 24px; border: none; overflow: hidden; }
    .modal-header-custom { padding: 25px 30px 10px 30px; border: none; position: relative; }
    .btn-close-custom { position: absolute; right: 20px; top: 20px; background: #f1f5f9; border-radius: 50%; padding: 8px; font-size: 0.7rem; }
    
    .drop-zone { border: 2px dashed #cbd5e1; border-radius: 18px; padding: 40px 20px; text-align: center; background: #f8fafc; cursor: pointer; transition: 0.3s; position: relative; }
    .drop-zone:hover { border-color: var(--brand-emerald); background: #f0fdf4; }
    .drop-zone i { font-size: 3rem; color: #94a3b8; margin-bottom: 15px; }
    
    .btn-start-import { 
        background: var(--brand-emerald); color: #fff; border: none; border-radius: 12px; 
        padding: 14px; width: 100%; font-weight: 700; font-size: 1rem;
        transition: 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
    }
    .btn-start-import:hover { background: #059669; transform: translateY(-1px); }
    .btn-start-import:disabled { background: #94a3b8; cursor: not-allowed; }

    /* LOADING OVERLAY */
    #loadingOverlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(255, 255, 255, 0.8); z-index: 9999;
        display: none; flex-direction: column; align-items: center; justify-content: center;
    }
    .loader-spinner { width: 50px; height: 50px; border: 5px solid #f3f3f3; border-top: 5px solid var(--brand-emerald); border-radius: 50%; animation: spin 1s linear infinite; }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

    .btn-qr-view { font-size: 0.8rem; font-weight: 800; border-radius: 8px; transition: 0.2s; }
    .btn-qr-view:hover { background: var(--brand-blue); color: white !important; border-color: var(--brand-blue) !important; }
</style>

<div class="fluid-wrapper">
    
    <div class="command-bar">
        <h1 class="page-title"><i class="fas fa-id-card"></i> Member Card Inventory</h1>
        <div class="d-flex gap-2">
            <button class="btn btn-light fw-800 rounded-3 px-4 shadow-sm border" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="fas fa-file-import me-2 text-success"></i> Import Excel
            </button>
            <a href="{{ url('member-cards/create') }}" class="btn fw-800 rounded-3 px-4 shadow-sm" style="background: var(--brand-blue); color: white;">
                <i class="fas fa-plus-circle me-2"></i> New Card
            </a>
        </div>
    </div>

    <div class="filter-card shadow-sm">
        <form method="GET" action="{{ url('member-cards') }}">
            <div class="row g-3 align-items-end">
                <div class="col-xl-3 col-md-6">
                    <label class="small fw-800 text-muted mb-2 text-uppercase">Status</label>
                    <select name="isSale" class="form-select form-control-sm-custom">
                        <option value="">All Inventory</option>
                        <option value="1" {{ request('isSale') == '1' ? 'selected' : '' }}>Available</option>
                        <option value="0" {{ request('isSale') == '0' ? 'selected' : '' }}>Sold</option>
                    </select>
                </div>
                <div class="col-xl-4 col-md-6">
                    <label class="small fw-800 text-muted mb-2 text-uppercase">Date Range (Sold Date)</label>
                    <div class="input-group">
                        <input type="date" name="date_from" class="form-control form-control-sm-custom" value="{{ request('date_from') }}">
                        <span class="input-group-text bg-white border-0 fw-bold">to</span>
                        <input type="date" name="date_to" class="form-control form-control-sm-custom" value="{{ request('date_to') }}">
                    </div>
                </div>
                <div class="col-xl-3 col-md-8">
                    <label class="small fw-800 text-muted mb-2 text-uppercase">Search Keyword</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0" style="border-radius: 10px 0 0 10px; border: 1.5px solid #e2e8f0;"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="keyword" class="form-control form-control-sm-custom border-start-0" placeholder="Card ID (e.g. TF-001)" value="{{ request('keyword') }}">
                    </div>
                </div>
                <div class="col-xl-2 col-md-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-dark w-100 fw-800 rounded-3" style="height: 45px;">Filter</button>
                        <a href="{{ url('member-cards') }}" class="btn btn-light border w-100 fw-800 rounded-3" style="height: 45px;">Reset</a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="table-container shadow-sm">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 80px;"># REF</th>
                        <th>Member Card Identifier</th>
                        <th class="text-center">VIP Link (QR)</th>
                        <th class="text-center">Inventory Status</th>
                        <th class="text-center">Sold On</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($member_cards as $index => $item)
                        <tr>
                            <td class="text-center text-muted">#{{ $member_cards->firstItem() + $index }}</td>
                            <td><span class="card-tag">{{ $item->member_card }}</span></td>
                            
                            <td class="text-center">
                                @php
                                    $safeEncryptedCode = '';
                                    if (!empty($item->member_card)) {
                                        $encrypted = \Illuminate\Support\Facades\Crypt::encryptString(trim($item->member_card));
                                        $safeEncryptedCode = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($encrypted));
                                    }
                                    // បង្កើត Full Link សម្រាប់កាតនេះ
                                    $vipLink = url('/vip/' . $safeEncryptedCode);
                                @endphp
                                
                                <button type="button" class="btn btn-sm btn-light border text-primary btn-qr-view" onclick="openQrModal('{{ $vipLink }}', '{{ $item->member_card }}')">
                                    <i class="fas fa-qrcode me-1"></i> Get QR
                                </button>
                            </td>

                            <td class="text-center">
                                @if($item->is_sale == 1)
                                    <span class="status-pill bg-available"><i class="fas fa-check-circle"></i> Available</span>
                                @else
                                    <span class="status-pill bg-sold"><i class="fas fa-shopping-cart"></i> Sold Out</span>
                                @endif
                            </td>
                            <td class="text-center small">
                                {{ $item->invoice_date ? \Carbon\Carbon::parse($item->invoice_date)->format('d-M-Y') : '---' }}
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ url('member-cards/edit', encrypt($item->id)) }}" class="btn-circle" title="Edit"><i class="fas fa-pen"></i></a>
                                    <button onclick="confirmDelete('{{ url('member-cards/destroy', encrypt($item->id)) }}')" class="btn-circle btn-delete" title="Delete"><i class="fas fa-trash-alt"></i></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="80" class="mb-3 opacity-25">
                                <p class="fw-800 text-muted mb-0">NO CARDS FOUND IN REGISTRY</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-top bg-white d-flex justify-content-between align-items-center">
            <p class="mb-0 small fw-800 text-muted uppercase">Showing {{ $member_cards->firstItem() }} to {{ $member_cards->lastItem() }} of {{ $member_cards->total() }} Assets</p>
            <div>{{ $member_cards->appends(request()->input())->links('paginitaion_backend') }}</div>
        </div>
    </div>
</div>

<div class="modal fade" id="qrDisplayModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content modal-content-custom shadow-lg text-center p-4">
            <button type="button" class="btn-close position-absolute" style="right: 15px; top: 15px;" data-bs-dismiss="modal"></button>
            <h6 class="fw-800 text-dark mb-1">VIP Card QR Code</h6>
            <p class="small text-muted mb-4">Card ID: <span id="qrCardIdDisplay" class="fw-bold text-primary"></span></p>
            
            <div id="qrImageContainer" class="mx-auto bg-white p-2 border rounded-3 shadow-sm d-inline-block"></div>
            
            <button type="button" class="btn btn-dark w-100 mt-4 rounded-3 fw-bold" onclick="downloadQR()">
                <i class="fas fa-download me-1"></i> Save Image
            </button>
        </div>
    </div>
</div>

<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-custom shadow-lg">
            <div class="modal-header-custom">
                <h5 class="modal-title fw-800 text-dark d-flex align-items-center gap-2">
                    <img src="https://cdn-icons-png.flaticon.com/512/732/732220.png" width="25"> Bulk Import Member Cards
                </h5>
                <button type="button" class="btn-close btn-close-custom" data-bs-dismiss="modal"></button>
            </div>
            <form id="ajaxImportForm">
                @csrf
                <div class="modal-body px-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <label class="fw-800 text-muted small uppercase">Select Excel File <span class="text-danger">*</span></label>
                        <a href="{{ url('/member-cards/download-template') }}" class="btn btn-sm fw-bold border rounded-pill px-3" style="font-size: 0.7rem; color: var(--brand-blue); background: #f1f5f9;">
                            <i class="fas fa-download me-1"></i> Get Template
                        </a>
                    </div>
                    
                    <div class="drop-zone" onclick="document.getElementById('file_input').click()">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <h6 class="fw-800 text-dark mb-1">Click or drag file here</h6>
                        <p class="text-muted small mb-0">Only .xlsx, .xls or .csv formats are supported.</p>
                        <input type="file" name="file" id="file_input" class="d-none" required accept=".xlsx, .xls, .csv">
                        <div id="file_name_display" class="mt-2 fw-bold text-success small"></div>
                    </div>

                    <div id="errorArea" class="mt-3 d-none">
                        <div class="alert alert-danger border-0 small p-3 rounded-4" style="background: #fef2f2; color: #991b1b;">
                            <ul class="mb-0 ps-3" id="errorList"></ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn-start-import" id="btnSubmitImport">
                        <i class="fas fa-check-circle"></i> <span class="btn-text">Start Import Cards</span>
                    </button>
                    <button type="button" class="btn btn-link w-100 text-muted fw-800 mt-2 text-decoration-none" data-bs-dismiss="modal">Cancel Process</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="loadingOverlay">
    <div class="loader-spinner"></div>
    <h5 class="fw-800 mt-3 text-dark">Processing Data...</h5>
    <p class="text-muted small">Please wait, your inventory is being updated.</p>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // 🌟 មុខងារថ្មី៖ បង្ហាញ QR Code ក្នុង Modal ពេលចុច "Get QR"
    let currentCardId = "";
    function openQrModal(vipLink, cardId) {
        currentCardId = cardId;
        document.getElementById('qrCardIdDisplay').innerText = cardId;
        
        // លុប QR ចាស់ចេញសិន (បើមាន)
        const qrContainer = document.getElementById("qrImageContainer");
        qrContainer.innerHTML = "";
        
        // បង្កើត QR ថ្មីផ្អែកលើ Link ដែលទទួលបាន
        new QRCode(qrContainer, {
            text: vipLink,
            width: 180,
            height: 180,
            colorDark : "#0f172a",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.L
        });
        
        // បង្ហាញ Modal
        let qrModal = new bootstrap.Modal(document.getElementById('qrDisplayModal'));
        qrModal.show();
    }

    // 🌟 មុខងារថ្មី៖ Save រូបភាព QR Code ទុក
    function downloadQR() {
        let qrCanvas = document.querySelector("#qrImageContainer canvas");
        if (qrCanvas) {
            let image = qrCanvas.toDataURL("image/png").replace("image/png", "image/octet-stream");
            let link = document.createElement('a');
            link.download = 'VIP_QR_' + currentCardId + '.png';
            link.href = image;
            link.click();
        }
    }

    // Show selected file name
    document.getElementById('file_input').addEventListener('change', function() {
        let name = this.files[0] ? this.files[0].name : "";
        document.getElementById('file_name_display').innerHTML = name ? `<i class="fas fa-file-excel"></i> Selected: ${name}` : "";
    });

    function confirmDelete(url) {
        Swal.fire({
            title: 'Delete Asset?',
            text: "This card will be permanently removed from inventory.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#CE181E',
            confirmButtonText: 'Yes, Delete Card',
            customClass: { confirmButton: 'rounded-3 px-4', cancelButton: 'rounded-3 px-4' }
        }).then((result) => { if (result.isConfirmed) { window.location.href = url; } });
    }

    $(document).ready(function() {
        $('#ajaxImportForm').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            
            // Show Loading
            $('#loadingOverlay').css('display', 'flex');
            $('#btnSubmitImport').prop('disabled', true);
            $('#errorArea').addClass('d-none');
            $('#errorList').empty();

            $.ajax({
                url: "{{ url('/member-cards/import') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    $('#loadingOverlay').hide();
                    Swal.fire({ icon: 'success', title: 'Import Finished!', text: 'Your cards are now available.', confirmButtonColor: '#10b981' })
                    .then(() => { location.reload(); });
                },
                error: function(xhr) {
                    $('#loadingOverlay').hide();
                    $('#btnSubmitImport').prop('disabled', false);
                    $('#errorArea').removeClass('d-none');

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors.file || [], function(k, v) { $('#errorList').append(`<li>${v}</li>`); });
                    } else {
                        $('#errorList').append(`<li>Unexpected system error. Please try again.</li>`);
                    }
                }
            });
        });
    });
</script>

@endsection