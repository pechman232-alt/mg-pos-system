@extends('layouts.homePages')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    .receive-container { font-family: 'Plus Jakarta Sans', 'Battambang', sans-serif !important; }
    .form-control:focus, .form-select:focus {
        border-color: #CE181E;
        box-shadow: 0 0 0 0.25rem rgba(206, 24, 30, 0.15);
    }
    .input-group-text { background-color: #f8fafc; color: #64748b; }
    .btn-brand { background-color: #CE181E; color: white; border: none; transition: 0.3s; }
    .btn-brand:hover { background-color: #a81116; color: white; transform: translateY(-2px); }
    .input-readonly { background-color: #f1f5f9 !important; font-weight: 700; color: #0f172a; pointer-events: none; }
    .error-text { color: #ef4444; font-size: 0.8rem; margin-top: 0.25rem; font-weight: 500; }
</style>

<div class="container-fluid flex-grow-1 container-p-y receive-container">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <h4 class="fw-bold mb-0 text-uppercase">
            <span class="text-muted fw-light">ទិញចូល /</span> បង្កើតវិក្កយបត្រទិញចូល (Create Stock Receipt)
        </h4>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="{{ route('receive-headers.index') }}" class="btn btn-outline-secondary shadow-sm fw-bold">
                <i class="bx bx-arrow-back me-1"></i> ត្រឡប់ក្រោយ
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center">
                    <div class="bg-label-primary p-2 rounded me-3" style="background-color: rgba(206, 24, 30, 0.1); color: #CE181E;">
                        <i class="bx bx-receipt fs-4"></i>
                    </div>
                    <h5 class="m-0 fw-bold text-dark">ព័ត៌មានក្បាលវិក្កយបត្រ</h5>
                </div>
                
                <div class="card-body pt-4">
                    <form action="{{ route('receive-headers.store') }}" method="POST">
                        @csrf
                        
                        <div class="row g-4 px-lg-4">
                            
                            <div class="col-xl-4 col-md-6 col-12">
                                <label class="form-label fw-semibold text-muted">លេខឯកសារ (Document No)</label>
                                <div class="input-group input-group-merge shadow-none border rounded">
                                    <span class="input-group-text border-0"><i class="bx bx-file"></i></span>
                                    <input type="text" name="document_no" class="form-control border-0 ps-0 input-readonly text-primary" value="{{ $document_no }}" readonly>
                                </div>
                            </div>

                            <div class="col-xl-4 col-md-6 col-12">
                                <label class="form-label fw-semibold text-muted">កាលបរិច្ឆេទ (Date) <span class="text-danger">*</span></label>
                                <div class="input-group input-group-merge shadow-none border rounded @error('date') border-danger @enderror">
                                    <span class="input-group-text border-0 bg-transparent"><i class="bx bx-calendar"></i></span>
                                    <input type="date" name="date" class="form-control border-0 ps-0" value="{{ old('date', date('Y-m-d')) }}" required>
                                </div>
                                @error('date') <div class="error-text"><i class="bx bx-error-circle me-1"></i>{{ $message }}</div> @enderror
                            </div>

                            <div class="col-xl-4 col-md-6 col-12">
                                <label class="form-label fw-semibold text-muted">លេខវិក្កយបត្រយោង (Ref Invoice No)</label>
                                <div class="input-group input-group-merge shadow-none border rounded @error('purchase_invoice_no') border-danger @enderror">
                                    <span class="input-group-text border-0 bg-transparent"><i class="bx bx-hash"></i></span>
                                    <input type="text" name="purchase_invoice_no" class="form-control border-0 ps-0" value="{{ old('purchase_invoice_no') }}" placeholder="Ex: INV-001">
                                </div>
                                @error('purchase_invoice_no') <div class="error-text"><i class="bx bx-error-circle me-1"></i>{{ $message }}</div> @enderror
                            </div>

                            <div class="col-xl-4 col-md-6 col-12">
                                <label class="form-label fw-semibold text-muted">អ្នកផ្គត់ផ្គង់ (Vendor) <span class="text-danger">*</span></label>
                                <div class="input-group input-group-merge shadow-none border rounded @error('vendor_id') border-danger @enderror">
                                    <span class="input-group-text border-0 bg-transparent"><i class="bx bx-buildings"></i></span>
                                    <select name="vendor_id" class="form-select border-0 ps-0" required>
                                        <option value="">-- ជ្រើសរើសអ្នកផ្គត់ផ្គង់ --</option>
                                        @foreach($vendors as $vendor)
                                            <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                                {{ $vendor->vendor_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('vendor_id') <div class="error-text"><i class="bx bx-error-circle me-1"></i>{{ $message }}</div> @enderror
                            </div>

                            <div class="col-xl-4 col-md-6 col-12">
                                <label class="form-label fw-semibold text-muted">សាខា / ទីតាំង (Location) <span class="text-danger">*</span></label>
                                <div class="input-group input-group-merge shadow-none border rounded @error('location_id') border-danger @enderror">
                                    <span class="input-group-text border-0 bg-transparent"><i class="bx bx-store"></i></span>
                                    <select name="location_id" class="form-select border-0 ps-0" required>
                                        <option value="">-- ជ្រើសរើសទីតាំង --</option>
                                        
                                        {{-- ✅ បានកែប្រែ៖ បង្ហាញសាខាទាំងអស់ ដើម្បីកុំឱ្យបាត់ Vive Warehouse --}}
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ old('location_id') == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->branch_name }}
                                            </option>
                                        @endforeach
                                        
                                    </select>
                                </div>
                                @error('location_id') <div class="error-text"><i class="bx bx-error-circle me-1"></i>{{ $message }}</div> @enderror
                            </div>

                            <div class="col-xl-4 col-md-6 col-12">
                                <label class="form-label fw-semibold text-muted">អ្នកដឹកជញ្ជូន (Transport Vendor)</label>
                                <div class="input-group input-group-merge shadow-none border rounded">
                                    <span class="input-group-text border-0 bg-transparent"><i class="bx bx-truck"></i></span>
                                    <input type="text" name="tran_vendor" class="form-control border-0 ps-0" value="{{ old('tran_vendor') }}" placeholder="Ex: J&T, DHL, Or Driver Name...">
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 border-secondary">

                        <div class="d-flex justify-content-end gap-3 px-lg-4">
                            <a href="{{ route('receive-headers.index') }}" class="btn btn-light fw-semibold px-4 shadow-sm border">
                                <i class="bx bx-x me-1"></i> បោះបង់ (Cancel)
                            </a>
                            <button type="submit" class="btn btn-brand fw-bold px-5 shadow-sm">
                                រក្សាទុក និងបន្ត <i class="bx bx-right-arrow-alt ms-1"></i>
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection