@extends('layouts.homePages')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <h4 class="fw-bold mb-0 text-uppercase">
            <span class="text-muted fw-light">ទិញចូល /</span> កែប្រែវិក្កយបត្រ (Edit Purchase Invoice)
        </h4>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="{{ route('receive-headers.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="bx bx-arrow-back me-1"></i> ត្រឡប់ក្រោយ
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10 col-12">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center">
                    <div class="bg-label-warning p-2 rounded me-3" style="background-color: rgba(255, 171, 0, 0.1); color: #ffab00;">
                        <i class="bx bx-edit-alt fs-4"></i>
                    </div>
                    <h5 class="m-0 fw-bold text-dark">កែប្រែព័ត៌មានវិក្កយបត្រទិញចូល</h5>
                </div>
                
                <div class="card-body pt-4">
                    <form action="{{ route('receive-headers.update', $header->id) }}" method="POST">
                        @csrf
                        @method('PUT') 

                        <div class="row g-4">
                            
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted">លេខឯកសារ (Document No)</label>
                                <div class="input-group input-group-merge shadow-none border rounded">
                                    <span class="input-group-text border-0 bg-light"><i class="bx bx-file"></i></span>
                                    <input type="text" class="form-control border-0 ps-0 bg-light fw-bold text-primary" value="{{ $header->document_no }}" readonly>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted">កាលបរិច្ឆេទ (Date) <span class="text-danger">*</span></label>
                                <div class="input-group input-group-merge shadow-none border rounded">
                                    <span class="input-group-text border-0 bg-transparent"><i class="bx bx-calendar"></i></span>
                                    <input type="date" name="date" class="form-control border-0 ps-0" value="{{ date('Y-m-d', strtotime($header->date)) }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted">អ្នកផ្គត់ផ្គង់ (Vendor) <span class="text-danger">*</span></label>
                                <div class="input-group input-group-merge shadow-none border rounded">
                                    <span class="input-group-text border-0 bg-transparent"><i class="bx bx-buildings"></i></span>
                                    <select name="vendor_id" class="form-select border-0 ps-0" required>
                                        <option value="">-- ជ្រើសរើសអ្នកផ្គត់ផ្គង់ --</option>
                                        @foreach($vendors as $vendor)
                                            <option value="{{ $vendor->id }}" {{ $header->vendor_id == $vendor->id ? 'selected' : '' }}>
                                                {{ $vendor->vendor_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted">សាខា / ទីតាំង (Location) <span class="text-danger">*</span></label>
                                <div class="input-group input-group-merge shadow-none border rounded">
                                    <span class="input-group-text border-0 bg-transparent"><i class="bx bx-store"></i></span>
                                    <select name="location_id" class="form-select border-0 ps-0" required>
                                        <option value="">-- ជ្រើសរើសទីតាំង --</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ $header->location_id == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->branch_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted">លេខវិក្កយបត្រ (Purchase Invoice No)</label>
                                <div class="input-group input-group-merge shadow-none border rounded">
                                    <span class="input-group-text border-0 bg-transparent"><i class="bx bx-receipt"></i></span>
                                    <input type="text" name="purchase_invoice_no" class="form-control border-0 ps-0" value="{{ $header->purchase_invoice_no }}" placeholder="Ex: INV-001">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted">ចំណាំ (Remark / Note)</label>
                                <div class="input-group input-group-merge shadow-none border rounded">
                                    <span class="input-group-text border-0 bg-transparent"><i class="bx bx-comment-detail"></i></span>
                                    <input type="text" name="remark" class="form-control border-0 ps-0" value="{{ $header->remark }}" placeholder="វាយចំណាំផ្សេងៗនៅទីនេះ...">
                                </div>
                            </div>

                        </div>

                        <hr class="my-4 border-secondary">

                        <div class="d-flex justify-content-end gap-3">
                            <a href="{{ route('receive-headers.index') }}" class="btn btn-light fw-semibold px-4 shadow-sm">
                                <i class="bx bx-x me-1"></i> បោះបង់ (Cancel)
                            </a>
                            <button type="submit" class="btn btn-warning text-dark fw-bold px-5 shadow-sm">
                                <i class="bx bx-save me-1"></i> រក្សាទុកការកែប្រែ (Update)
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection