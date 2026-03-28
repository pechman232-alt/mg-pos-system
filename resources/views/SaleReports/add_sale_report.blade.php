@extends('layouts.homePages')

@section('content')
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* ==================== 100% FIT SCREEN UI ==================== */
        :root {
            --brand-red: #CE181E;
            --brand-indigo: #4f46e5;
            --bg-body: #f8fafc;
            --text-heading: #0f172a;
            --border-soft: #e2e8f0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            background-color: var(--bg-body);
            margin: 0; padding: 0;
        }

        /* FORCED 100% WIDTH WRAPPER */
        .content-fluid-wrapper {
            width: 100% !important;
            max-width: 100% !important;
            padding: 1.5rem !important;
            animation: fadeIn 0.4s ease-out;
        }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }

        /* ENTERPRISE CARD SYSTEM */
        .card {
            border: 1px solid var(--border-soft) !important;
            border-radius: 1.25rem !important;
            background: #ffffff !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05) !important;
            margin-bottom: 1.5rem;
            width: 100%;
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

        /* CLEAN FORM CONTROLS */
        .form-label-custom {
            font-size: 0.8rem !important;
            font-weight: 800 !important;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 0.5rem;
            display: block;
            letter-spacing: 0.5px;
        }

        .form-control, .form-select {
            height: 46px !important;
            border-radius: 0.75rem !important;
            border: 1.5px solid var(--border-soft) !important;
            font-size: 0.9rem !important;
            font-weight: 700 !important;
            color: var(--text-heading) !important;
            transition: 0.3s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--brand-indigo) !important;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1) !important;
            outline: none;
        }

        /* HIGHLIGHT FOR SELECT2 CONTAINERS */
        .select2-container--default .select2-selection--single {
            height: 48px !important;
            border: 1.5px solid var(--brand-indigo) !important;
            border-radius: 0.75rem !important;
            background-color: rgba(79, 70, 229, 0.02) !important;
            display: flex; align-items: center;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: var(--brand-indigo) !important; font-weight: 800 !important;
        }

        .btn-submit-node {
            background: linear-gradient(135deg, var(--brand-red), #b91c1c) !important;
            color: white !important;
            padding: 14px 60px !important;
            font-weight: 800 !important;
            border-radius: 0.75rem !important;
            border: none; cursor: pointer; transition: 0.3s;
            box-shadow: 0 10px 20px rgba(206, 24, 30, 0.2);
        }

        .img-preview-frame {
            border-radius: 1rem !important; border: 2px dashed var(--border-soft);
            padding: 8px; width: 160px; height: 110px; object-fit: contain; background: #fff;
        }

        /* HIGHLIGHT SPECIFIC TEXT */
        .text-primary-bold { color: var(--brand-indigo) !important; font-weight: 800; }


        
    </style>

    <div class="content-fluid-wrapper">
        @include('Helps.alert')

        <form action="{{ url('/sale-reports/store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row g-4">
                <div class="col-xl-6">
                    <div class="card h-100">
                        <div class="card-header"><span><i class="fas fa-user-circle"></i> Customer & Branch Profile</span></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label-custom">Customer Name *</label>
                                    <input type="text" name="customer_name" class="form-control text-primary-bold" placeholder="Enter full name" value="{{ old('customer_name') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">Phone Number</label>
                                    <input type="text" name="phone_number" class="form-control" placeholder="012 345 678" value="{{ old('phone_number') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">Gender</label>
                                    <select name="gender" class="form-select">
                                        <option value="Male" @selected(old('gender') == 'Male')>Male</option>
                                        <option value="Female" @selected(old('gender') == 'Female')>Female</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">Date of Birth</label>
                                    <input type="date" name="dob" class="form-control" value="{{ old('dob') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">Province / Address</label>
                                    <select name="province_id" class="form-select">
                                        @foreach ($provinces as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">Branch</label>
                                    <select name="branch_id" class="form-select">
                                        @foreach ($branchs as $item)
                                            <option value="{{ $item->id }}">{{ $item->branch_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">Source Customer</label>
                                    <select name="source_id" class="form-select">
                                        <option value="Walk In">Walk In</option>
                                        <option value="Call In">Call In</option>
                                        <option value="Facebook">Facebook</option>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label-custom text-primary-bold"><i class="fas fa-id-card"></i> Member Card Identity</label>
                                    <input type="hidden" id="old-memberCard-id" value="{{ old('member_card_id') }}">
                                    <select id="memberCard-select" class="form-select" name="member_card_id"></select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="card h-100">
                        <div class="card-header"><span><i class="fas fa-car"></i> Vehicle Specifications</span></div>
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-4 mb-4 pb-3 border-bottom">
                                <img src="{{ asset('backend/assets/img/avatars/image.png') }}" id="target" class="img-preview-frame shadow-sm" />
                                <div>
                                    <button type="button" class="btn btn-dark btn-sm fw-bold px-3" data-bs-toggle="modal" data-bs-target="#largeModal">
                                        <i class="fas fa-search me-2"></i> Select Brand Model
                                    </button>
                                    <p class="mt-2 mb-0 small fw-800">MODEL: <span id="modelName" class="text-danger">NONE</span></p>
                                </div>
                                <input type="hidden" name="image" id="carImageId">
                                <input type="hidden" name="model_name" id="modelNameInput">
                            </div>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label-custom text-primary-bold"><i class="fas fa-microchip"></i> Vehicle Model Selector (VIN/Engine) *</label>
                                    <input type="hidden" id="old-logistic-id" value="{{ old('logistics_id') }}">
                                    <select id="logistic-select" class="form-select" name="logistics_id"></select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">Brand Name</label>
                                    <select name="brand_name" class="form-select">
                                        <option value="MG">MG</option>
                                        <option value="Maxus">Maxus</option>
                                        <option value="LeapMotor">LeapMotor</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">Plate Number</label>
                                    <input type="text" name="plate_number" class="form-control" placeholder="ABC-1234">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">Warranty MG</label>
                                    <select name="warranty_id" class="form-select">
                                        @foreach ($warrantys as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">Battery Warranty Leap</label>
                                    <select name="warranty_leap_motor" class="form-select">
                                        @foreach ($warrantys as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header"><span><i class="fas fa-file-invoice-dollar"></i> Commercial Details & Offers</span></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3"><label class="form-label-custom">Contract No</label><input type="text" name="contract_no" class="form-control"></div>
                                <div class="col-md-3"><label class="form-label-custom">Contract Date</label><input type="date" name="contract_date" class="form-control"></div>
                                <div class="col-md-3"><label class="form-label-custom">Invoice No</label><input type="text" name="invoice_no" class="form-control highlight-input"></div>
                                <div class="col-md-3"><label class="form-label-custom">Invoice Date</label><input type="date" name="invoice_date" class="form-control"></div>
                                <div class="col-md-3"><label class="form-label-custom">Delivery Date</label><input type="date" name="delivery_date" class="form-control"></div>
                                <div class="col-md-3"><label class="form-label-custom">Sale Person</label>
                                    <select name="employee_id" class="form-select">
                                        @foreach ($employees as $item)
                                            <option value="{{ $item->id }}">{{ $item->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3"><label class="form-label-custom">Price ($) *</label><input type="text" name="price" class="form-control fw-bold text-primary-bold"></div>
                                <div class="col-md-3"><label class="form-label-custom">VPC VDR</label><select name="vpc_vdr" class="form-select"><option value="Both">Both</option><option value="N/A">N/A</option></select></div>

                                <div class="col-md-2"><label class="form-label-custom">Floor Mat</label><select name="offer_floor_mat" class="form-select"><option value="No">No</option><option value="Yes">Yes</option></select></div>
                                <div class="col-md-2"><label class="form-label-custom">UV Film</label><select name="offer_uv_film" class="form-select"><option value="No">No</option><option value="Yes">Yes</option></select></div>
                                <div class="col-md-2"><label class="form-label-custom">License Plate</label><select name="offer_license_plate" class="form-select"><option value="No">No</option><option value="Yes">Yes</option></select></div>
                                <div class="col-md-2"><label class="form-label-custom">Side Step</label><select name="side_step" class="form-select"><option value="No">No</option><option value="Yes">Yes</option></select></div>
                                <div class="col-md-2"><label class="form-label-custom">Auto Tailgate</label><select name="auto_tailgate" class="form-select"><option value="No">No</option><option value="Yes">Yes</option></select></div>
                                <div class="col-md-2"><label class="form-label-custom">Rear Spoiler</label><select name="rear_spoiler" class="form-select"><option value="No">No</option><option value="Yes">Yes</option></select></div>

                                <div class="col-md-3"><label class="form-label-custom">Pro Offer Oil</label><input type="text" name="pro_offer_oil" class="form-control"></div>
                                <div class="col-md-3"><label class="form-label-custom">Pro Oil Date</label><input type="date" name="pro_offer_oil_date" class="form-control"></div>
                                <div class="col-md-3"><label class="form-label-custom">Cert Engine Oil</label><input type="text" name="certificate_engine_oil" class="form-control"></div>
                                <div class="col-md-3"><label class="form-label-custom">Battery IR Date</label><input type="date" name="vihicle_battery" class="form-control"></div>

                                <div class="col-md-6"><label class="form-label-custom">Remark SA</label><input type="text" name="remark_sa" class="form-control"></div>
                                <div class="col-md-6"><label class="form-label-custom">Feedback Command</label><textarea name="feedback_command" class="form-control" rows="1"></textarea></div>
                                <div class="col-md-12"><label class="form-label-custom">Attachment (PDF)</label><input type="file" name="attach_file" class="form-control" accept=".pdf"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 text-center pb-5">
                    <button type="submit" class="btn-submit-node shadow-lg">CREATE SALE REPORT NODE</button>
                </div>
            </div>
        </form>
    </div>

    <div class="modal fade" id="largeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header border-bottom"><h5 class="fw-800 m-0">Select Model Node</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body p-0">
                    <table id="datatable" class="table table-hover mb-0">
                        <thead><tr><th>No</th><th>Preview</th><th>Model Name</th><th>Color Specs</th></tr></thead>
                        <tbody>
                            @foreach ($carImages as $index => $c)
                                <tr class="select-image" data-id="{{ $c->id }}" data-model="{{ $c->modelName }}" data-image="{{ url($c->image_name ? '/imageCar/'.$c->image_name : 'backend/assets/img/avatars/1.png') }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td><img src="{{ url($c->image_name ? '/imageCar/'.$c->image_name : 'backend/assets/img/avatars/1.png') }}" width="60" height="40" style="object-fit: contain;"></td>
                                    <td class="fw-800">{{ $c->modelName }}</td>
                                    <td class="small fw-bold text-muted">{{ $c->colorName }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#datatable').DataTable({
                "pageLength": 10,
                "language": {
                    "search": "Search models:",
                    "lengthMenu": "Show _MENU_ models per page"
                }
            })
        });

        document.addEventListener('DOMContentLoaded', function() {
            const table = document.getElementById('datatable');
            table.addEventListener('click', function(e) {
                const row = e.target.closest('tr.select-image');
                if (!row) return;

                const imageUrl = row.getAttribute('data-image');
                const modelName = row.getAttribute('data-model');
                const imageId = row.getAttribute('data-id');

                document.getElementById('target').src = imageUrl;
                document.getElementById('modelName').innerText = modelName;
                document.getElementById('carImageId').value = imageId;

                const modal = document.getElementById('largeModal');
                bootstrap.Modal.getOrCreateInstance(modal).hide();
            });
        });

        $('#logistic-select').select2({
            placeholder: "Select Logistic",
            allowClear: true,
            ajax: {
                url: "{{ url('/sale-reports/create') }}",
                type: "GET",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term,
                        type: 'logistic'
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                id: item.id,
                                text: item.model_name + ' - ' + item.vin_number + ' - ' + item.engine_number
                            };
                        })
                    };
                },
                cache: true
            }
        });

        let oldLogisticId = $('#old-logistic-id').val();
        if (oldLogisticId) {
            $.ajax({
                url: "{{ url('/sale-reports/create') }}",
                type: "GET",
                data: { search: '', type: 'logistic' },
                success: function(data) {
                    let selectedItem = data.find(item => item.id == oldLogisticId);
                    if (selectedItem) {
                        let optionText = selectedItem.model_name + ' - ' + selectedItem.vin_number + ' - ' + selectedItem.engine_number;
                        let option = new Option(optionText, selectedItem.id, true, true);
                        $('#logistic-select').append(option).trigger('change');
                    }
                }
            });
        }

        $('#memberCard-select').select2({
            placeholder: "Select Member Card",
            allowClear: true,
            ajax: {
                url: "{{ url('/sale-reports/create') }}",
                type: "GET",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term,
                        type: 'memberCard'
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return { id: item.id, text: item.member_card };
                        })
                    };
                },
                cache: true
            }
        });

        let oldMemberCardId = $('#old-memberCard-id').val();
        if (oldMemberCardId) {
            $.ajax({
                url: "{{ url('/sale-reports/create') }}",
                type: "GET",
                data: { search: '', type: 'memberCard' },
                success: function(data) {
                    let selectedItem = data.find(item => item.id == oldMemberCardId);
                    if (selectedItem) {
                        let optionText = selectedItem.member_card;
                        let option = new Option(optionText, selectedItem.id, true, true);
                        $('#memberCard-select').append(option).trigger('change');
                    }
                }
                
            });
        }
    </script>
@endsection