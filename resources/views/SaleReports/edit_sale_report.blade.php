@extends('layouts.homePages')

@section('content')
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,800&family=JetBrains+Mono:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <style>
        /* ================= PROFESSIONAL UI SYSTEM (VIEW-STYLE GRID) ================= */
        :root {
            --brand-red: #e31e24;
            --brand-blue: #002663;
            --text-heading: #0f172a;
            --text-body: #475569;
            --text-muted: #64748b;
            --bg-main: #f8fafc;
            --border-color: #e2e8f0;
        }

        body { font-family: 'Plus Jakarta Sans', sans-serif !important; background-color: var(--bg-main); color: var(--text-body); }

        .report-fluid-container { width: 100% !important; max-width: 100% !important; padding: 1.5rem !important; animation: fadeIn 0.4s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }

        /* HEADER & BUTTONS */
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; background: #ffffff; padding: 15px 25px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02); }
        .page-title { font-size: 1.3rem; font-weight: 800; color: var(--brand-blue); margin: 0; letter-spacing: -0.5px; text-transform: uppercase; }

        .btn-outline-custom { background: #ffffff; color: var(--text-heading); border: 1.5px solid var(--border-color); border-radius: 8px; font-weight: 700; padding: 8px 20px; transition: 0.3s; text-decoration: none;}
        .btn-outline-custom:hover { background: #f1f5f9; color: var(--text-heading); }
        
        .btn-update { background: linear-gradient(135deg, var(--brand-red), #b91c1c); color: #ffffff; border: none; border-radius: 8px; font-weight: 800; padding: 14px 24px; width: 100%; transition: 0.3s; box-shadow: 0 4px 10px rgba(227, 30, 36, 0.2); }
        .btn-update:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(227, 30, 36, 0.3); color: white;}

        /* DATA CARDS */
        .app-card { background: #ffffff; border-radius: 12px; margin-bottom: 20px; border: 1px solid var(--border-color); box-shadow: 0 2px 4px rgba(0, 0, 0, 0.01); overflow: hidden; }
        .app-card-header { display: flex; align-items: center; gap: 10px; background: #ffffff; border-bottom: 1px solid var(--border-color); padding: 14px 20px; }
        .app-card-header h6 { margin: 0; font-weight: 800; color: var(--brand-blue); text-transform: uppercase; font-size: 0.9rem; letter-spacing: 0.5px; }

        /* 🌟 BORDERED COMPACT TABLE FOR FORM INPUTS 🌟 */
        .table-compact { width: 100%; border-collapse: collapse; margin-bottom: 0; }
        .table-compact th, .table-compact td { border: 1px solid var(--border-color); padding: 0; vertical-align: middle; }
        .table-compact th { background-color: #f8fafc; width: 20%; color: var(--text-muted); font-weight: 800; font-size: 0.75rem; text-transform: uppercase; text-align: right; padding-right: 15px; letter-spacing: 0.5px; }
        .table-compact td { background-color: #ffffff; width: 30%; position: relative; }
        
        /* SEAMLESS INPUTS INSIDE TABLE */
        .form-control-seamless, .form-select-seamless { 
            width: 100%; height: 45px; border: none !important; border-radius: 0; 
            padding: 8px 15px; font-size: 0.9rem; font-weight: 700; color: var(--text-heading); 
            background: transparent; outline: none; box-shadow: none !important; 
        }
        .form-control-seamless:focus, .form-select-seamless:focus { background-color: #f1f5f9; }
        .form-control-seamless::placeholder { color: #cbd5e1; font-weight: 500; }
        
        select.form-select-seamless { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 1rem center; background-size: 1em; }
        textarea.form-control-seamless { height: auto; min-height: 60px; padding: 12px 15px; resize: vertical; }

        .highlight-text { color: var(--brand-red) !important; font-weight: 800 !important; }
        .mono-font { font-family: 'JetBrains Mono', monospace; }

        /* SELECT2 SEAMLESS OVERRIDES */
        .select2-container--default .select2-selection--single { border: none !important; border-radius: 0 !important; height: 45px !important; background: transparent !important; display: flex; align-items: center; }
        .select2-container--default .select2-selection--single:focus-within { background: #f1f5f9 !important; }
        .select2-container--default .select2-selection--single .select2-selection__rendered { color: var(--text-heading) !important; font-weight: 700 !important; padding-left: 15px !important; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 45px !important; right: 10px !important; }

        /* WIDGETS & SIDEBAR */
        .widget-dark { background: linear-gradient(135deg, #0f172a, #1e293b); border-radius: 12px; padding: 25px 20px; color: white; text-align: center; box-shadow: 0 4px 10px rgba(15, 23, 42, 0.1); }
        .sticky-hud { position: sticky; top: 20px; }
        
        .image-preview-wrapper { background: #f8fafc; border: 1px solid var(--border-color); border-radius: 12px; overflow: hidden; padding: 15px; text-align: center; position: relative; }
        .img-preview-frame { width: 100%; height: auto; max-height: 180px; object-fit: contain; mix-blend-mode: multiply; }

        /* CHECKBOX PILLS */
        .pills-container { padding: 12px 15px; display: flex; flex-wrap: wrap; gap: 8px; }
        .check-pill { display: none; }
        .check-pill-label { padding: 6px 14px; border: 1px solid var(--border-color); border-radius: 20px; font-size: 0.8rem; font-weight: 700; color: var(--text-muted); cursor: pointer; transition: 0.2s; background: #f8fafc; margin: 0; }
        .check-pill:checked + .check-pill-label { background: #ecfdf5; border-color: #10b981; color: #059669; }
        
        /* RADIO PILLS */
        .radio-pill { display: none; }
        .radio-pill-label { padding: 6px 14px; border: 1px solid var(--border-color); border-radius: 20px; font-size: 0.8rem; font-weight: 700; color: var(--text-muted); cursor: pointer; transition: 0.2s; background: #f8fafc; margin: 0; }
        .radio-pill:checked + .radio-pill-label { background: #eff6ff; border-color: var(--brand-blue); color: var(--brand-blue); }

        /* PROGRESS */
        .progress-bar-thin { height: 4px; background: var(--brand-blue); width: 0%; transition: 0.4s; }
    </style>

    <div class="report-fluid-container">
        @include('Helps.alert')

        <div style="background: #e2e8f0; height: 4px; width: 100%; border-radius: 4px; margin-bottom: 15px; overflow: hidden;">
            <div id="formProgress" class="progress-bar-thin"></div>
        </div>

        <form id="authorityForm" action="{{ url('sale-reports/update', encrypt($saleReports->id)) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="page-header">
                <div>
                    <h1 class="page-title"><i class="fas fa-edit me-2 text-danger"></i> UPDATE REPORT <span class="text-muted ms-2 fs-6 fw-bold">#{{ $saleReports->invoice_no ?? 'N/A' }}</span></h1>
                </div>
                <div>
                    <a href="{{ url('sale-reports') }}" class="btn-outline-custom"><i class="fas fa-arrow-left me-1"></i> BACK</a>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-9 col-lg-8">
                    
                    <div class="app-card">
                        <div class="app-card-header"><i class="fas fa-user-circle text-primary fs-5"></i><h6>Core Information</h6></div>
                        <table class="table-compact">
                            <tbody>
                                <tr>
                                    <th>Customer Name <span class="text-danger">*</span></th>
                                    <td><input type="text" name="customer_name" class="form-control-seamless highlight-text track-field" value="{{ $saleReports->customer_name }}" required></td>
                                    <th>Gender</th>
                                    <td>
                                        <select name="gender" class="form-select-seamless track-field">
                                            <option value="Male" {{ $saleReports->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                            <option value="Female" {{ $saleReports->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Phone Number</th>
                                    <td><input type="text" name="phone_number" class="form-control-seamless mono-font highlight-text track-field" value="{{ $saleReports->phone_number }}"></td>
                                    <th>Date of Birth</th>
                                    <td><input type="date" name="dob" class="form-control-seamless mono-font track-field" value="{{ $saleReports->dob }}"></td>
                                </tr>
                                <tr>
                                    <th>Address (Province)</th>
                                    <td>
                                        <select name="province_id" class="form-select-seamless track-field">
                                            @foreach ($provinces as $item)
                                                <option value="{{ $item->id }}" @if($item->id == $saleReports->province_id) selected @endif>{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <th>Brand Name</th>
                                    <td>
                                        <select name="brand_name" class="form-select-seamless highlight-text track-field">
                                            <option value="MG" {{ $saleReports->brand_name == 'MG' ? 'selected' : '' }}>MG</option>
                                            <option value="Maxus" {{ $saleReports->brand_name == 'Maxus' ? 'selected' : '' }}>Maxus</option>
                                            <option value="LeapMotor" {{ $saleReports->brand_name == 'LeapMotor' ? 'selected' : '' }}>LeapMotor</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Branch</th>
                                    <td>
                                        <select name="branch_id" class="form-select-seamless track-field">
                                            @foreach ($branchs as $item)
                                                <option value="{{ $item->id }}" @if($item->id == $saleReports->branch_id) selected @endif>{{ $item->branch_name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <th>Source Customer</th>
                                    <td>
                                        <select name="source_id" class="form-select-seamless track-field">
                                            <option value="Walk In" {{ $saleReports->source_id == 'Walk In' ? 'selected' : '' }}>Walk In</option>
                                            <option value="Call In" {{ $saleReports->source_id == 'Call In' ? 'selected' : '' }}>Call In</option>
                                            <option value="Facebook" {{ $saleReports->source_id == 'Facebook' ? 'selected' : '' }}>Facebook</option>
                                        </select>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="app-card">
                        <div class="app-card-header"><i class="fas fa-file-contract text-warning fs-5"></i><h6>Contractual & Source Data</h6></div>
                        <table class="table-compact">
                            <tbody>
                                <tr>
                                    <th>Contract No</th>
                                    <td><input type="text" name="contract_no" class="form-control-seamless mono-font track-field" value="{{ $saleReports->contract_no }}"></td>
                                    <th>Contract Date</th>
                                    <td><input type="date" name="contract_date" class="form-control-seamless mono-font track-field" value="{{ $saleReports->contract_date }}"></td>
                                </tr>
                                <tr>
                                    <th>Sales Person</th>
                                    <td>
                                        <select name="employee_id" class="form-select-seamless select2-basic">
                                            <option value="" disabled>Select...</option>
                                            @foreach ($employees as $item)
                                                <option value="{{ $item->id }}" {{ $item->id == $saleReports->employee_id ? 'selected' : '' }}>{{ $item->full_name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <th>Member Card</th>
                                    <td>
                                        <select id="member-card-select" class="form-select-seamless track-field" name="member_card_id">
                                            @php $currentMemberCard = \App\Models\MemberCard::find($saleReports->member_card_id); @endphp
                                            @if($currentMemberCard)
                                                <option value="{{ $currentMemberCard->id }}" selected>{{ $currentMemberCard->member_card }}</option>
                                            @endif
                                        </select>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="app-card">
                        <div class="app-card-header"><i class="fas fa-car-side text-danger fs-5"></i><h6>Vehicle Specifications</h6></div>
                        <table class="table-compact">
                            <tbody>
                                <tr>
                                    <th>VIN / Engine Select <span class="text-danger">*</span></th>
                                    <td colspan="3">
                                        <select id="logistic-select" class="form-select-seamless track-field w-100" name="logistics_id">
                                            @php $currentLogistic = \App\Models\LogisticsModel::find($saleReports->logistics_id); @endphp
                                            @if($currentLogistic)
                                                <option value="{{ $currentLogistic->id }}" selected>{{ $currentLogistic->model_name_id }} - {{ $currentLogistic->vin_number }} - {{ $currentLogistic->engine_number }}</option>
                                            @endif
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Plate Number</th>
                                    <td><input type="text" name="plate_number" class="form-control-seamless highlight-text track-field" value="{{ $saleReports->plate_number }}"></td>
                                    <th>Battery IR Date</th>
                                    <td><input type="date" name="vihicle_battery" class="form-control-seamless mono-font track-field" value="{{ $saleReports->vihicle_battery }}"></td>
                                </tr>
                                <tr>
                                    <th>Warranty MG</th>
                                    <td>
                                        <select name="warranty_id" class="form-select-seamless track-field">
                                            <option value="">N/A</option>
                                            @foreach ($warrantys as $item)
                                                <option value="{{ $item->id }}" @if($item->id == $saleReports->warranty_id) selected @endif>{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <th>Warranty LeapMotor</th>
                                    <td>
                                        <select name="warranty_leap_motor" class="form-select-seamless track-field">
                                            <option value="">N/A</option>
                                            @foreach ($warrantys as $item)
                                                <option value="{{ $item->id }}" @if($item->id == $saleReports->warranty_leap_motor) selected @endif>{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th>VPC VDR Status</th>
                                    <td colspan="3">
                                        <select name="vpc_vdr" class="form-select-seamless track-field">
                                            <option value="Both" {{ $saleReports->vpc_vdr == 'Both' ? 'selected' : '' }}>Both</option>
                                            <option value="N/A" {{ $saleReports->vpc_vdr == 'N/A' ? 'selected' : '' }}>N/A</option>
                                        </select>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="app-card">
                        <div class="app-card-header"><i class="fas fa-gift text-success fs-5"></i><h6>Promotions & Options</h6></div>
                        <table class="table-compact">
                            <tbody>
                                <tr>
                                    <th>Accessories</th>
                                    <td colspan="3">
                                        <div class="pills-container">
                                            <input type="hidden" name="offer_floor_mat" value="No"><input type="checkbox" id="c_mat" name="offer_floor_mat" value="Yes" class="check-pill track-field" {{ $saleReports->offer_floor_mat == 'Yes' ? 'checked' : '' }}><label for="c_mat" class="check-pill-label">Floor Mat</label>
                                            <input type="hidden" name="offer_uv_film" value="No"><input type="checkbox" id="c_uv" name="offer_uv_film" value="Yes" class="check-pill track-field" {{ $saleReports->offer_uv_film == 'Yes' ? 'checked' : '' }}><label for="c_uv" class="check-pill-label">UV Film</label>
                                            <input type="hidden" name="offer_license_plate" value="No"><input type="checkbox" id="c_plate" name="offer_license_plate" value="Yes" class="check-pill track-field" {{ $saleReports->offer_license_plate == 'Yes' ? 'checked' : '' }}><label for="c_plate" class="check-pill-label">License Plate</label>
                                            <input type="hidden" name="side_step" value="No"><input type="checkbox" id="c_step" name="side_step" value="Yes" class="check-pill track-field" {{ $saleReports->side_step == 'Yes' ? 'checked' : '' }}><label for="c_step" class="check-pill-label">Side Step</label>
                                            <input type="hidden" name="auto_tailgate" value="No"><input type="checkbox" id="c_tail" name="auto_tailgate" value="Yes" class="check-pill track-field" {{ $saleReports->auto_tailgate == 'Yes' ? 'checked' : '' }}><label for="c_tail" class="check-pill-label">Auto Tailgate</label>
                                            <input type="hidden" name="rear_spoiler" value="No"><input type="checkbox" id="c_spoil" name="rear_spoiler" value="Yes" class="check-pill track-field" {{ $saleReports->rear_spoiler == 'Yes' ? 'checked' : '' }}><label for="c_spoil" class="check-pill-label">Rear Spoiler</label>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Engine Oil Cert</th>
                                    <td colspan="3">
                                        <div class="pills-container">
                                            <input type="radio" id="oil_na" name="certificate_engine_oil" value="" class="radio-pill track-field" {{ empty($saleReports->certificate_engine_oil) ? 'checked' : '' }}><label for="oil_na" class="radio-pill-label">N/A</label>
                                            @if(isset($engineOils))
                                                @foreach ($engineOils as $item)
                                                    <input type="radio" id="oil_{{ $item->id }}" name="certificate_engine_oil" value="{{ $item->id }}" class="radio-pill track-field" {{ $saleReports->certificate_engine_oil == $item->id ? 'checked' : '' }}><label for="oil_{{ $item->id }}" class="radio-pill-label">{{ $item->name }}</label>
                                                @endforeach
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="app-card mb-xl-0">
                        <div class="app-card-header"><i class="fas fa-file-invoice-dollar text-info fs-5"></i><h6>Accounting & Remarks</h6></div>
                        <table class="table-compact">
                            <tbody>
                                <tr>
                                    <th>Invoice No <span class="text-danger">*</span></th>
                                    <td><input type="text" name="invoice_no" class="form-control-seamless mono-font track-field" value="{{ $saleReports->invoice_no }}" required></td>
                                    <th>Invoice Date</th>
                                    <td><input type="date" name="invoice_date" class="form-control-seamless mono-font track-field" value="{{ $saleReports->invoice_date }}"></td>
                                </tr>
                                <tr>
                                    <th>Delivery Date</th>
                                    <td><input type="date" name="delivery_date" class="form-control-seamless mono-font track-field" value="{{ $saleReports->delivery_date }}"></td>
                                    <th>Price ($) <span class="text-danger">*</span></th>
                                    <td><input type="text" name="price" class="form-control-seamless highlight-text mono-font track-field" value="{{ $saleReports->price }}" required></td>
                                </tr>
                                <tr>
                                    <th>Pro Offer Oil</th>
                                    <td><input type="text" name="pro_offer_oil" class="form-control-seamless track-field" value="{{ $saleReports->pro_offer_oil }}"></td>
                                    <th>Pro Oil Date</th>
                                    <td><input type="date" name="pro_offer_oil_date" class="form-control-seamless mono-font track-field" value="{{ $saleReports->pro_offer_oil_date }}"></td>
                                </tr>
                                <tr>
                                    <th style="vertical-align: top; padding-top: 15px;">SA Remarks</th>
                                    <td colspan="3"><textarea name="remark_sa" class="form-control-seamless track-field">{{ $saleReports->remark_sa }}</textarea></td>
                                </tr>
                                <tr>
                                    <th style="vertical-align: top; padding-top: 15px;">Feedback Command</th>
                                    <td colspan="3"><textarea name="feedback_command" class="form-control-seamless track-field">{{ $saleReports->feedback_command }}</textarea></td>
                                </tr>
                                <tr>
                                    <th>Attachment (PDF)</th>
                                    <td colspan="3" style="padding: 10px 15px;">
                                        <input type="file" name="attach_file" accept=".pdf" class="form-control" style="border: 1px dashed var(--border-color); font-size: 0.85rem;">
                                        @if($saleReports->attach_file)
                                            <div class="mt-2 small"><i class="fas fa-check-circle text-success me-1"></i> File: <a href="{{ asset('uploads/sale_reports/'.$saleReports->attach_file) }}" target="_blank" class="fw-bold">View PDF</a></div>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-4">
                    <div class="sticky-hud">
                        
                        <div class="image-preview-wrapper mb-3">
                            <img src="{{ url($getCarImages?->image_name ? '/imageCar/' . $getCarImages->image_name : 'backend/assets/img/avatars/image.png') }}" id="target" class="img-preview-frame mb-3" />
                            <h5 id="modelName" class="text-danger fw-800 mb-3">{{ $getCarImages?->modelName ?? 'NONE' }}</h5>
                            <button type="button" class="btn btn-outline-secondary btn-sm fw-bold w-100" data-bs-toggle="modal" data-bs-target="#largeModal" style="border-radius: 8px;">
                                <i class="fas fa-camera me-1"></i> Change Model Image
                            </button>
                            <input type="hidden" name="image" id="carImageId" class="track-field" value="{{ $saleReports->image }}">
                            <input type="hidden" name="model_name" id="modelNameInput" value="{{ $getCarImages?->modelName }}">
                        </div>

                        <div class="widget-dark mb-4">
                            @php
                                $invDateRaw = $saleReports->invoice_date;
                                $diff = 0;
                                if($invDateRaw) {
                                    $invDate = new \DateTime($invDateRaw);
                                    $now = new \DateTime();
                                    $diff = $now->diff($invDate)->days;
                                }
                            @endphp
                            <div style="font-size: 3.5rem; font-weight: 800; line-height: 1;">{{ $diff }}</div>
                            <div class="fw-800 small text-uppercase mt-2 text-info" style="letter-spacing: 1px;">Days in System</div>
                        </div>

                        <button type="submit" class="btn-update">
                            <i class="fas fa-save me-2"></i> UPDATE REPORT
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="modal fade" id="largeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header border-bottom" style="background: #f8fafc; padding: 20px 24px;">
                    <h5 class="fw-800 m-0 text-dark"><i class="fas fa-car me-2 text-danger"></i> Select Brand Model</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="table-responsive">
                        <table id="datatable" class="table table-hover mb-0 align-middle">
                            <thead style="background: #f1f5f9;">
                                <tr><th class="ps-4">Preview</th><th>Model Name</th><th>Color Specs</th></tr>
                            </thead>
                            <tbody>
                                @foreach ($carImages as $carImage)
                                    <tr class="select-image" style="cursor: pointer;" data-id="{{ $carImage->id }}"
                                        data-image="{{ url($carImage->image_name == '' ? 'backend/assets/img/avatars/1.png' : '/imageCar/' . $carImage->image_name) }}"
                                        data-model="{{ $carImage->modelName }}">
                                        <td class="ps-4 py-2"><img src="{{ url($carImage->image_name == '' ? 'backend/assets/img/avatars/1.png' : '/imageCar/' . $carImage->image_name) }}" style="width: 80px; height: 50px; object-fit: contain; border-radius: 6px; border: 1px solid #e2e8f0; padding: 2px;"></td>
                                        <td class="fw-800 text-dark">{{ $carImage->modelName }}</td>
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
            fields.forEach(f => { 
                if(f.type === 'checkbox' || f.type === 'radio') {
                    if(f.checked) filled++;
                } else if(f.value && f.value !== "") {
                    filled++; 
                }
            });
            const percent = Math.min(100, Math.round((filled / fields.length) * 100));
            document.getElementById('formProgress').style.width = percent + "%";
        }

        $(document).ready(function() {
            document.querySelectorAll('.track-field').forEach(f => f.addEventListener('input', updateProgress));
            setTimeout(updateProgress, 500);

            $('#authorityForm').on('submit', function() {
                let btns = $('.btn-update');
                btns.html('<i class="fas fa-spinner fa-spin me-2"></i> SAVING...');
                btns.prop('disabled', true);
            });

            $('#datatable').DataTable({
                "pageLength": 10,
                "language": { "search": "", "searchPlaceholder": "Search models..." }
            });
            $('.dataTables_filter input').addClass('form-control d-inline-block w-auto ms-2').css('height', '36px');
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
                document.getElementById('modelNameInput').value = modelName;

                const modal = document.getElementById('largeModal');
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) modalInstance.hide();
                updateProgress();
            });
        });

        // SELECT2 SETUP
        $('.select2-basic').select2({ width: '100%' });

        $('#logistic-select').select2({
            placeholder: "Select Logistic (VIN/Engine)", allowClear: true, width: '100%',
            ajax: {
                url: "{{ url('/sale-reports/create') }}", type: "GET", dataType: 'json', delay: 250,
                data: function(params) { return { search: params.term, type: 'logistic' }; },
                processResults: function(data) {
                    return { results: $.map(data, function(item) { return { id: item.id, text: item.model_name + ' - ' + item.vin_number + ' - ' + item.engine_number }; }) };
                }, cache: true
            }
        });

        $('#member-card-select').select2({
            placeholder: "Select Member Card", allowClear: true, width: '100%',
            ajax: {
                url: "{{ url('/sale-reports/create') }}", type: "GET", dataType: 'json', delay: 250,
                data: function(params) { return { search: params.term, type: 'memberCard' }; },
                processResults: function(data) {
                    return { results: $.map(data, function(item) { return { id: item.id, text: item.member_card }; }) };
                }, cache: true
            }
        });
    </script>
@endsection