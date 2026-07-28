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
</style>

<div class="container-fluid flex-grow-1 container-p-y receive-container ">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <h4 class="fw-bold mb-0 text-uppercase">
            <span class="text-muted fw-light">ទិញចូល /</span> កែប្រែវិក្កយបត្រ (Edit Stock Receipt)
        </h4>
        <div class="mt-2 mt-md-0">
            <a href="{{ route('receive-headers.index') }}" class="btn btn-outline-secondary shadow-sm fw-bold">
                <i class="bx bx-arrow-back me-1"></i> ត្រឡប់ក្រោយ
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center">
            <div class="p-2 rounded me-3" style="background-color: rgba(206, 24, 30, 0.1); color: #CE181E;">
                <i class="bx bx-edit fs-4"></i>
            </div>
            <h5 class="m-0 fw-bold text-dark">កែសម្រួលព័ត៌មានវិក្កយបត្រ</h5>
        </div>
        
        <div class="card-body pt-4">
            <form action="{{ route('receive-headers.update', encrypt($header->id)) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row g-4 px-lg-2">
                    
                    <div class="col-xl-4 col-md-6">
                        <label class="form-label fw-semibold text-muted">លេខឯកសារ (Document No)</label>
                        <div class="input-group input-group-merge border rounded shadow-none">
                            <span class="input-group-text border-0"><i class="bx bx-file"></i></span>
                            <input type="text" class="form-control border-0 ps-0 input-readonly text-primary" value="{{ $header->document_no }}" readonly>
                        </div>
                    </div>

                    <div class="col-xl-4 col-md-6">
                        <label class="form-label fw-semibold text-muted">កាលបរិច្ឆេទ (Date) <span class="text-danger">*</span></label>
                        <div class="input-group input-group-merge border rounded shadow-none">
                            <span class="input-group-text border-0"><i class="bx bx-calendar"></i></span>
                            <input type="date" name="date" class="form-control border-0 ps-0" value="{{ old('date', $header->date ? \Carbon\Carbon::parse($header->date)->format('Y-m-d') : date('Y-m-d')) }}" required>
                        </div>
                    </div>

                    <div class="col-xl-4 col-md-6">
                        <label class="form-label fw-semibold text-muted">លេខវិក្កយបត្រយោង (Ref Invoice No)</label>
                        <div class="input-group input-group-merge border rounded shadow-none">
                            <span class="input-group-text border-0"><i class="bx bx-hash"></i></span>
                            <input type="text" name="purchase_invoice_no" class="form-control border-0 ps-0" value="{{ old('purchase_invoice_no', $header->purchase_invoice_no) }}" placeholder="Ex: INV-001">
                        </div>
                    </div>

                    <div class="col-xl-4 col-md-6">
                        <label class="form-label fw-semibold text-muted">អ្នកផ្គត់ផ្គង់ (Vendor) <span class="text-danger">*</span></label>
                        <div class="input-group input-group-merge border rounded shadow-none">
                            <span class="input-group-text border-0"><i class="bx bx-buildings"></i></span>
                            <select name="vendor_id" class="form-select border-0 ps-0" required>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}" {{ old('vendor_id', $header->vendor_id) == $vendor->id ? 'selected' : '' }}>
                                        {{ $vendor->vendor_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-xl-4 col-md-6">
                        <label class="form-label fw-semibold text-muted">សាខា (Location) <span class="text-danger">*</span></label>
                        <div class="input-group input-group-merge border rounded shadow-none">
                            <span class="input-group-text border-0"><i class="bx bx-store"></i></span>
                            <select name="location_id" class="form-select border-0 ps-0" required>
                                <option value="">-- ជ្រើសរើសទីតាំង --</option>
                                
                                {{-- ✅ ចាប់យកតែសាខា ៤ ដែលទើបបង្កើតថ្មីបំផុត --}}
                                @foreach($branches->sortByDesc('id')->take(4) as $branch)
                                    <option value="{{ $branch->id }}" {{ old('location_id', $header->location_id) == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->branch_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-xl-4 col-md-6">
                        <label class="form-label fw-semibold text-muted">អ្នកដឹកជញ្ជូន (Transport Vendor)</label>
                        <div class="input-group input-group-merge border rounded shadow-none">
                            <span class="input-group-text border-0"><i class="bx bx-truck"></i></span>
                            <input type="text" name="tran_vendor" class="form-control border-0 ps-0" value="{{ old('tran_vendor', $header->tran_vendor) }}" placeholder="Ex: J&T, DHL...">
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold text-muted">ចំណាំ (Remark / Note)</label>
                        <div class="input-group input-group-merge border rounded shadow-none">
                            <span class="input-group-text border-0 align-items-start pt-2"><i class="bx bx-comment-detail"></i></span>
                            <textarea name="remark" class="form-control border-0 ps-0" rows="2" placeholder="វាយចំណាំផ្សេងៗនៅទីនេះ...">{{ old('remark', $header->remark) }}</textarea>
                        </div>
                    </div>
                </div>

                <hr class="my-4 border-secondary">

                <div class="d-flex justify-content-end gap-3">
                    <a href="{{ route('receive-headers.index') }}" class="btn btn-light fw-semibold px-4 border shadow-sm">
                        <i class="bx bx-x me-1"></i> បោះបង់
                    </a>
                    <button type="submit" class="btn btn-brand fw-bold px-5 shadow-sm">
                        <i class="bx bx-save me-1"></i> រក្សាទុកការកែប្រែ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection