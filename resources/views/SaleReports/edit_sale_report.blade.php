@extends('layouts.homePages')

@section('content')
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* ==================== ENTERPRISE MANAGEMENT UI ==================== */
        :root {
            --brand-red: #CE181E;
            --brand-red-hover: #b91c1c;
            --bg-body: #f8fafc;
            --text-heading: #0f172a;
            --border-soft: #e2e8f0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            background-color: var(--bg-body);
            color: #475569;
            margin: 0; padding: 0;
        }

        /* 100% FIT SCREEN WRAPPER */
        .content-fluid-wrapper {
            width: 100% !important;
            max-width: 100% !important;
            padding: 1.5rem !important;
            animation: fadeIn 0.4s ease-out;
        }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }

        /* PROGRESS BAR STYLING */
        .progress-container {
            position: sticky; top: 0; z-index: 1000;
            background: var(--bg-body); padding: 10px 0;
        }
        .progress-bar-wrapper {
            height: 10px; background: #e2e8f0; border-radius: 20px; overflow: hidden;
        }
        .progress-fill {
            height: 100%; background: linear-gradient(90deg, var(--brand-red), #ff4d4d);
            width: 0%; transition: width 0.5s ease;
        }

        /* SELECT2 FIXES */
        .select2-container--default .select2-selection--single {
            height: 44px !important; border: 1.5px solid var(--border-soft) !important;
            border-radius: 0.75rem !important; padding-left: 35px !important;
            display: flex; align-items: center;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 42px !important; }
        .select2-container { width: 100% !important; }

        /* SECTION CARDS */
        .card {
            border: 1px solid var(--border-soft) !important;
            border-radius: 1.25rem !important;
            background: #ffffff !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05) !important;
            margin-bottom: 1.5rem;
        }

        .card-header {
            background: transparent !important;
            padding: 1.25rem 1.5rem !important;
            border-bottom: 1px solid var(--border-soft) !important;
            font-weight: 800 !important;
            color: var(--text-heading);
            display: flex; align-items: center; justify-content: space-between;
        }

        .card-header i { color: var(--brand-red); margin-right: 10px; }

        .form-label-custom {
            font-size: 0.8rem !important;
            font-weight: 700 !important;
            text-transform: uppercase;
            color: #94a3b8;
            margin-bottom: 0.4rem;
            display: block;
        }

        .form-control, .form-select {
            height: 44px !important; border-radius: 0.75rem !important;
            border: 1.5px solid var(--border-soft) !important;
            font-size: 0.9rem !important; font-weight: 600 !important;
        }

        .input-icon-group { position: relative; width: 100%; }
        .input-icon-group i {
            position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
            color: #94a3b8; font-size: 0.9rem; z-index: 10;
        }
        .input-icon-group .form-control, .input-icon-group .form-select { padding-left: 2.6rem !important; }

        .img-preview-frame {
            border-radius: 1rem !important; border: 2px dashed var(--border-soft);
            padding: 8px; width: 160px; height: 110px; object-fit: contain; background: #fff;
        }

        .btn-update-node {
            background-color: var(--brand-red) !important; color: white !important;
            padding: 12px 45px !important; font-weight: 800 !important; border-radius: 0.75rem !important;
            border: none; transition: 0.3s;
        }
    </style>

    <div class="content-fluid-wrapper">
        <div class="progress-container">
            <div class="d-flex justify-content-between align-items-end mb-2">
                <span class="fw-800 text-dark small">FIELD COMPLETION PROGRESS</span>
                <span id="progressText" class="fw-800 text-danger small">0%</span>
            </div>
            <div class="progress-bar-wrapper">
                <div id="progressBar" class="progress-fill"></div>
            </div>
        </div>

        <form id="authorityForm" action="{{ url('sale-reports/update', encrypt($saleReports->id)) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row g-4 mt-2">
                <div class="col-xl-6">
                    <div class="card h-100">
                        <div class="card-header"><span><i class="fas fa-user-circle"></i> Identity & Location</span></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label-custom">Customer Name *</label>
                                    <div class="input-icon-group"><i class="fas fa-user"></i>
                                        <input type="text" class="form-control track-field" name="customer_name" value="{{ $saleReports->customer_name }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">Phone Number</label>
                                    <div class="input-icon-group"><i class="fas fa-phone"></i>
                                        <input type="text" class="form-control track-field" name="phone_number" value="{{ $saleReports->phone_number }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">Gender</label>
                                    <select name="gender" class="form-select track-field">
                                        <option value="Male" {{ $saleReports->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ $saleReports->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">Date of Birth</label>
                                    <input type="date" class="form-control track-field" name="dob" value="{{ $saleReports->dob }}">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label-custom">Address / Province</label>
                                    <select name="province_id" class="form-select track-field">
                                        @foreach ($provinces as $item)
                                            <option value="{{ $item->id }}" @if($item->id == $saleReports->province_id) selected @endif>{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">Operational Branch</label>
                                    <select name="branch_id" class="form-select track-field">
                                        @foreach ($branchs as $item)
                                            <option value="{{ $item->id }}" @if($item->id == $saleReports->branch_id) selected @endif>{{ $item->branch_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">Sale Person</label>
                                    <select name="employee_id" class="form-select track-field">
                                        @foreach ($employees as $item)
                                            <option value="{{ $item->id }}" @if($item->id == $saleReports->employee_id) selected @endif>{{ $item->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="card h-100">
                        <div class="card-header"><span><i class="fas fa-car"></i> Vehicle Spec & Warranty</span></div>
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-4 mb-4 pb-3 border-bottom">
                                <img src="{{ url($getCarImages?->image_name ? '/imageCar/' . $getCarImages->image_name : 'backend/assets/img/avatars/image.png') }}"
                                     id="target" class="img-preview-frame shadow-sm" />
                                <div>
                                    <button type="button" class="btn btn-dark btn-sm fw-bold px-3" data-bs-toggle="modal" data-bs-target="#largeModal">
                                        <i class="fas fa-images me-2"></i> Brand Model
                                    </button>
                                    <p class="mt-2 mb-0 small fw-800">MODEL: <span id="modelName" class="text-danger">{{ $getCarImages?->modelName ?? 'N/A' }}</span></p>
                                </div>
                                <input type="hidden" name="image" id="carImageId" class="track-field" value="{{ $saleReports->image }}">
                                <input type="hidden" name="model_name" id="modelNameInput" value="{{ $getCarImages?->modelName }}">
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label-custom">Vehicle Model Selector (VIN/Engine) *</label>
                                    <div class="input-icon-group"><i class="fas fa-barcode"></i>
                                        <input type="hidden" id="old-logistic-id" value="{{ $saleReports->logistics_id }}">
                                        <select id="logistic-select" class="form-select track-field" name="logistics_id"></select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">Brand Name</label>
                                    <input type="text" class="form-control track-field" name="brand_name" value="{{ $saleReports->brand_name }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">Plate Number</label>
                                    <input type="text" class="form-control track-field" name="plate_number" value="{{ $saleReports->plate_number }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">Warranty MG</label>
                                    <select name="warranty_id" class="form-select track-field">
                                        @foreach ($warrantys as $item)
                                            <option value="{{ $item->id }}" @if($item->id == $saleReports->warranty_id) selected @endif>{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">Battery Warranty LeapMotor</label>
                                    <select name="warranty_leap_motor" class="form-select track-field">
                                        @foreach ($warrantys as $item)
                                            <option value="{{ $item->id }}" @if($item->id == $saleReports->warranty_leap_motor) selected @endif>{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header"><span><i class="fas fa-file-contract"></i> Logistics, Pricing & Promotions</span></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label-custom">Contract No *</label>
                                    <input type="text" class="form-control track-field" name="contract_no" value="{{ $saleReports->contract_no }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label-custom">Contract Date</label>
                                    <input type="date" class="form-control track-field" name="contract_date" value="{{ $saleReports->contract_date }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label-custom">Invoice No</label>
                                    <input type="text" class="form-control track-field" name="invoice_no" value="{{ $saleReports->invoice_no }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label-custom">Invoice Date</label>
                                    <input type="date" class="form-control track-field" name="invoice_date" value="{{ $saleReports->invoice_date }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label-custom">Delivery Date</label>
                                    <input type="date" class="form-control track-field" name="delivery_date" value="{{ $saleReports->delivery_date }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label-custom">Price ($) *</label>
                                    <input type="text" class="form-control fw-bold text-primary track-field" name="price" value="{{ $saleReports->price }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label-custom">VPC VDR</label>
                                    <input type="text" class="form-control track-field" name="vpc_vdr" value="{{ $saleReports->vpc_vdr }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label-custom">Member Card Identity</label>
                                    <div class="input-icon-group"><i class="fas fa-id-card"></i>
                                        <input type="hidden" id="old-member-card" value="{{ $saleReports->member_card_id }}">
                                        <select id="member-card-select" class="form-select track-field" name="member_card_id"></select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label-custom">Offer Floor Mat</label>
                                    <select name="offer_floor_mat" class="form-select track-field">
                                        <option value="Yes" {{ $saleReports->offer_floor_mat == 'Yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="No" {{ $saleReports->offer_floor_mat == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label-custom">Offer UV Film</label>
                                    <select name="offer_uv_film" class="form-select track-field">
                                        <option value="Yes" {{ $saleReports->offer_uv_film == 'Yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="No" {{ $saleReports->offer_uv_film == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label-custom">Offer License Plate</label>
                                    <input type="text" class="form-control track-field" name="offer_license_plate" value="{{ $saleReports->offer_license_plate }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label-custom">Side Step</label>
                                    <select name="side_step" class="form-select track-field">
                                        <option value="Yes" {{ $saleReports->side_step == 'Yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="No" {{ $saleReports->side_step == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label-custom">Auto Tailgate</label>
                                    <select name="auto_tailgate" class="form-select track-field">
                                        <option value="Yes" {{ $saleReports->auto_tailgate == 'Yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="No" {{ $saleReports->auto_tailgate == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label-custom">Rear Spoiler</label>
                                    <select name="rear_spoiler" class="form-select track-field">
                                        <option value="Yes" {{ $saleReports->rear_spoiler == 'Yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="No" {{ $saleReports->rear_spoiler == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label-custom">Pro Offer Oil</label>
                                    <input type="text" class="form-control track-field" name="pro_offer_oil" value="{{ $saleReports->pro_offer_oil }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label-custom">Pro Oil Date</label>
                                    <input type="date" class="form-control track-field" name="pro_offer_oil_date" value="{{ $saleReports->pro_offer_oil_date }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label-custom">Cert Engine Oil</label>
                                    <input type="text" class="form-control track-field" name="certificate_engine_oil" value="{{ $saleReports->certificate_engine_oil }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label-custom">Vehicle Battery IR</label>
                                    <input type="date" class="form-control track-field" name="vihicle_battery" value="{{ $saleReports->vihicle_battery }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label-custom">Feedback Command</label>
                                    <textarea class="form-control track-field" name="feedback_command" rows="2">{{ $saleReports->feedback_command }}</textarea>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label-custom">Remark Sa</label>
                                    <input type="text" class="form-control track-field" name="remark_sa" value="{{ $saleReports->remark_sa }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label-custom">Attachment (PDF)</label>
                                    <input type="file" class="form-control track-field" name="attach_file" accept=".pdf">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 text-center pb-5">
                    <button type="submit" class="btn-update-node shadow-lg">UPDATE 38-FIELD DATA NODE</button>
                </div>
            </div>
        </form>
    </div>

    <div class="modal fade" id="largeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header">
                    <h5 class="fw-800 m-0">Fleet Brand Selector</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="table-responsive">
                        <table id="datatable" class="table table-hover mb-0">
                            <thead><tr><th>Preview</th><th>Model</th><th>Colors</th></tr></thead>
                            <tbody>
                                @foreach ($carImages as $carImage)
                                    <tr class="select-image" data-id="{{ $carImage->id }}"
                                        data-image="{{ url($carImage->image_name == '' ? 'backend/assets/img/avatars/1.png' : '/imageCar/' . $carImage->image_name) }}"
                                        data-model="{{ $carImage->modelName }}">
                                        <td><img src="{{ url($carImage->image_name == '' ? 'backend/assets/img/avatars/1.png' : '/imageCar/' . $carImage->image_name) }}" style="width: 70px; height: 50px; object-fit: contain;"></td>
                                        <td class="fw-800">{{ $carImage->modelName }}</td>
                                        <td class="small fw-bold text-muted">{{ $carImage->colorName }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateProgress() {
            const fields = document.querySelectorAll('.track-field');
            let filled = 0;
            fields.forEach(f => { if(f.value && f.value !== "") filled++; });
            const percent = Math.round((filled / fields.length) * 100);
            document.getElementById('progressBar').style.width = percent + "%";
            document.getElementById('progressText').innerText = percent + "% COMPLETE";
        }
        document.querySelectorAll('.track-field').forEach(f => f.addEventListener('input', updateProgress));
        setTimeout(updateProgress, 1000);

        $('#authorityForm').on('submit', function() {
            Swal.fire({ title: 'Syncing...', text: 'Updating node data...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        });

        $('#member-card-select').select2({
            placeholder: "Select Member Card", allowClear: true,
            ajax: {
                url: "{{ url('/sale-reports/create') }}", dataType: 'json',
                data: (params) => ({ type: 'memberCard', search: params.term }),
                processResults: (data) => ({ results: $.map(data, (i) => ({ id: i.id, text: i.member_card })) })
            }
        });

        $('#logistic-select').select2({
            placeholder: "Select Logistic", allowClear: true,
            ajax: {
                url: "{{ url('/sale-reports/create') }}", dataType: 'json',
                data: (params) => ({ search: params.term, type: 'logistic' }),
                processResults: (data) => ({ results: $.map(data, (i) => ({ id: i.id, text: i.model_name + ' - ' + i.vin_number })) })
            }
        });

        // Initialize Select2 with old data
        let oldMemberCard = $('#old-member-card').val();
        if (oldMemberCard) {
            $.ajax({ url: "{{ url('/sale-reports/create') }}", data: { type: 'memberCard', search: '' }, success: (data) => {
                let selected = data.find(i => i.id == oldMemberCard);
                if (selected) { $('#member-card-select').append(new Option(selected.member_card, selected.id, true, true)).trigger('change'); }
            }});
        }

        let oldLogisticId = $('#old-logistic-id').val();
        if (oldLogisticId) {
            $.ajax({ url: "{{ url('/sale-reports/create') }}", data: { search: '', type: 'logistic' }, success: (data) => {
                let selected = data.find(i => i.id == oldLogisticId);
                if (selected) { 
                    let txt = selected.model_name + ' - ' + selected.vin_number;
                    $('#logistic-select').append(new Option(txt, selected.id, true, true)).trigger('change'); 
                }
            }});
        }

        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll(".select-image").forEach(row => {
                row.addEventListener("click", function() {
                    document.getElementById("target").src = this.getAttribute("data-image");
                    document.getElementById("modelNameInput").value = this.getAttribute("data-model");
                    document.getElementById("modelName").innerText = this.getAttribute("data-model");
                    document.getElementById("carImageId").value = this.getAttribute("data-id");
                    bootstrap.Modal.getInstance(document.getElementById('largeModal')).hide();
                    updateProgress();
                });
            });
        });
    </script>
@endsection