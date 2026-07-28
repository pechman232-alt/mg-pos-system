@extends('layouts.homePages')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    .product-container { font-family: 'Plus Jakarta Sans', 'Battambang', sans-serif !important; }
    .form-control:focus, .form-select:focus {
        border-color: #CE181E;
        box-shadow: 0 0 0 0.25rem rgba(206, 24, 30, 0.15);
    }
    .input-group-text { background-color: #f8fafc; color: #64748b; }
    .btn-brand { background-color: #CE181E; color: white; border: none; transition: 0.3s; }
    .btn-brand:hover { background-color: #a81116; color: white; transform: translateY(-2px); }
</style>

<div class="container-fluid flex-grow-1 container-p-y product-container">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <h4 class="fw-bold mb-0 text-uppercase">
            <span class="text-muted fw-light">ទំនិញស្តុក /</span> បន្ថែមទំនិញថ្មី (Add Product)
        </h4>
        <div class="mt-2 mt-md-0">
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary shadow-sm fw-bold">
                <i class="bx bx-arrow-back me-1"></i> ត្រឡប់ក្រោយ
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center">
                    <div class="bg-label-primary p-2 rounded me-3" style="background-color: rgba(206, 24, 30, 0.1); color: #CE181E;">
                        <i class="bx bx-box fs-4"></i>
                    </div>
                    <h5 class="m-0 fw-bold" style="color: #0f172a;">ព័ត៌មានទំនិញ (Product Information)</h5>
                </div>
                
                <div class="card-body pt-4">
                    <form action="{{ route('products.store') }}" method="POST">
                        @csrf
                        
                        <div class="row g-4">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-12">
                                <label class="form-label fw-semibold text-muted">Item Code (កូដទំនិញ) <span class="text-danger">*</span></label>
                                <div class="input-group input-group-merge shadow-none border rounded">
                                    <span class="input-group-text border-0"><i class="bx bx-barcode-reader"></i></span>
                                    <input type="text" name="item_code" class="form-control border-0 ps-0" required placeholder="Ex: PRD-001">
                                </div>
                            </div>

                            <div class="col-xl-6 col-lg-6 col-md-6 col-12">
                                <label class="form-label fw-semibold text-muted">Product Name (ឈ្មោះទំនិញ) <span class="text-danger">*</span></label>
                                <div class="input-group input-group-merge shadow-none border rounded">
                                    <span class="input-group-text border-0"><i class="bx bx-package"></i></span>
                                    <input type="text" name="item_name" class="form-control border-0 ps-0" required placeholder="Ex: MG Oil Filter">
                                </div>
                            </div>

                            <div class="col-xl-6 col-lg-6 col-md-6 col-12">
                                <label class="form-label fw-semibold text-muted">Category (ប្រភេទទំនិញ)</label>
                                <div class="input-group input-group-merge shadow-none border rounded">
                                    <span class="input-group-text border-0"><i class="bx bx-category"></i></span>
                                    <select name="category" class="form-select border-0 ps-0" required>
                                        <option value="">-- ជ្រើសរើសប្រភេទ --</option>
                                        <optgroup label="4 WHEEL (រថយន្ត)">
                                            <option value="MG 4WHEEL">MG 4WHEEL</option>
                                            <option value="LP 4WHEEL">LP 4WHEEL (LEAPMOTOR)</option>
                                            <option value="IM 4WHEEL">IM 4WHEEL</option>
                                        </optgroup>
                                        <optgroup label="2 WHEEL (ម៉ូតូ)">
                                            <option value="RE 2WHEEL">RE 2WHEEL (ROYAL ENFIELD)</option>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>

                            <div class="col-xl-6 col-lg-6 col-md-6 col-12">
                                <label class="form-label fw-semibold text-muted">Initial Stock (ស្តុកដើម)</label>
                                <div class="input-group input-group-merge shadow-none border rounded">
                                    <span class="input-group-text border-0"><i class="bx bx-layer"></i></span>
                                    <input type="number" name="stock_qty" class="form-control border-0 ps-0" value="0" min="0">
                                </div>
                            </div>

                            <div class="col-xl-6 col-lg-6 col-md-6 col-12">
                                <label class="form-label fw-semibold text-muted">Cost (តម្លៃទិញចូល) <span class="text-danger">*</span></label>
                                <div class="input-group input-group-merge shadow-none border rounded">
                                    <span class="input-group-text border-0 fw-bold text-success">$</span>
                                    <input type="number" step="0.01" name="cost" class="form-control border-0 ps-0" required placeholder="0.00">
                                </div>
                            </div>

                            <div class="col-xl-6 col-lg-6 col-md-6 col-12">
                                <label class="form-label fw-semibold text-muted">Price (តម្លៃលក់ចេញ) <span class="text-danger">*</span></label>
                                <div class="input-group input-group-merge shadow-none border rounded">
                                    <span class="input-group-text border-0 fw-bold text-primary">$</span>
                                    <input type="number" step="0.01" name="price" class="form-control border-0 ps-0" required placeholder="0.00">
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold text-muted">Description (ការពិពណ៌នា)</label>
                                <div class="input-group input-group-merge shadow-none border rounded">
                                    <span class="input-group-text border-0 align-items-start pt-2"><i class="bx bx-comment-detail"></i></span>
                                    <textarea name="description" class="form-control border-0 ps-0" rows="3" placeholder="ព័ត៌មានបន្ថែមពីទំនិញ..."></textarea>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 border-secondary">

                        <div class="d-flex justify-content-end gap-3">
                            <a href="{{ route('products.index') }}" class="btn btn-light fw-semibold px-4 shadow-sm border">
                                <i class="bx bx-x me-1"></i> បោះបង់ (Cancel)
                            </a>
                            <button type="submit" class="btn btn-brand fw-bold px-5 shadow-sm">
                                <i class="bx bx-save me-1"></i> រក្សាទុក (Save Product)
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection