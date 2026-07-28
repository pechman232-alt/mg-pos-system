@extends('layouts.homePages')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    .vendor-container { font-family: 'Plus Jakarta Sans', 'Battambang', sans-serif !important; }
    
    .form-control, .form-select {
        padding: 0.6rem 1rem;
        font-size: 0.95rem;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #CE181E;
        box-shadow: 0 0 0 0.25rem rgba(206, 24, 30, 0.15);
    }
    
    .input-group-text { 
        background-color: #f8fafc; 
        color: #475569; 
        padding-left: 1.2rem;
        padding-right: 0.8rem;
    }
    
    .btn-brand { background-color: #CE181E; color: white; border: none; transition: 0.3s; }
    .btn-brand:hover { background-color: #a81116; color: white; transform: translateY(-2px); }
    
    .vendor-card {
        border-radius: 12px;
        transition: all 0.3s ease;
    }
</style>

<div class="container-fluid flex-grow-1 container-p-y vendor-container">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <h4 class="fw-bold mb-0 text-uppercase">
            <span class="text-muted fw-light">ទិន្នន័យដើម /</span> បន្ថែមអ្នកផ្គត់ផ្គង់ (Add Vendor)
        </h4>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="{{ route('vendors.index') }}" class="btn btn-outline-secondary shadow-sm fw-semibold">
                <i class="bx bx-arrow-back me-1"></i> ត្រឡប់ក្រោយ
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card vendor-card border-0 shadow-sm mb-4">
                
                <div class="card-header bg-white border-bottom p-4 d-flex align-items-center">
                    <div class="bg-label-primary p-2 rounded me-3 d-flex align-items-center justify-content-center" style="background-color: rgba(206, 24, 30, 0.1); color: #CE181E; width: 45px; height: 45px;">
                        <i class="bx bx-buildings fs-3"></i>
                    </div>
                    <div>
                        <h5 class="m-0 fw-bold text-dark">ព័ត៌មានអ្នកផ្គត់ផ្គង់ (Vendor Information)</h5>
                        <small class="text-muted">សូមបញ្ជូលព័ត៌មានលម្អិតរបស់អ្នកផ្គត់ផ្គង់ថ្មី</small>
                    </div>
                </div>
                
                <div class="card-body p-4 p-md-5">
                    <form action="{{ route('vendors.store') }}" method="POST">
                        @csrf
                        
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark">ឈ្មោះក្រុមហ៊ុន / អ្នកផ្គត់ផ្គង់ (Vendor Name) <span class="text-danger">*</span></label>
                                <div class="input-group input-group-merge shadow-none border rounded">
                                    <span class="input-group-text border-0"><i class="bx bx-buildings fs-5"></i></span>
                                    <input type="text" name="vendor_name" class="form-control border-0 ps-0" required placeholder="Ex: TF Motors (Cambodia) Co., Ltd.">
                                </div>
                            </div>

                            <div class="col-md-6 col-12">
                                <label class="form-label fw-semibold text-dark">លេខទូរស័ព្ទ (Phone Number)</label>
                                <div class="input-group input-group-merge shadow-none border rounded">
                                    <span class="input-group-text border-0"><i class="bx bx-phone fs-5"></i></span>
                                    <input type="text" name="phone" class="form-control border-0 ps-0" placeholder="Ex: 010 882 316">
                                </div>
                            </div>

                            <div class="col-md-6 col-12">
                                <label class="form-label fw-semibold text-dark">អ៊ីមែល (Email Address)</label>
                                <div class="input-group input-group-merge shadow-none border rounded">
                                    <span class="input-group-text border-0"><i class="bx bx-envelope fs-5"></i></span>
                                    <input type="email" name="email" class="form-control border-0 ps-0" placeholder="Ex: contact@vendor.com">
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark">អាសយដ្ឋាន (Address)</label>
                                <div class="input-group input-group-merge shadow-none border rounded">
                                    <span class="input-group-text border-0 align-items-start pt-3"><i class="bx bx-map fs-5"></i></span>
                                    <textarea name="address" class="form-control border-0 ps-0 py-2" rows="4" placeholder="បញ្ចូលអាសយដ្ឋានក្រុមហ៊ុននៅទីនេះ..."></textarea>
                                </div>
                            </div>
                        </div>

                        <hr class="my-5 border-light">

                        <div class="d-flex flex-column flex-sm-row justify-content-end gap-3">
                            <a href="{{ route('vendors.index') }}" class="btn btn-light fw-bold px-4 py-2 shadow-sm order-2 order-sm-1">
                                <i class="bx bx-x me-1"></i> បោះបង់ (Cancel)
                            </a>
                            <button type="submit" class="btn btn-brand fw-bold px-5 py-2 shadow-sm order-1 order-sm-2">
                                <i class="bx bx-save me-1"></i> រក្សាទុក (Save Vendor)
                            </button>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection