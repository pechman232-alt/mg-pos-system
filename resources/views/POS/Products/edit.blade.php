@extends('layouts.homePages')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    .product-container { font-family: 'Plus Jakarta Sans', 'Battambang', sans-serif !important; }
    
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
    
    .edit-card {
        border-radius: 12px;
        transition: all 0.3s ease;
    }
</style>

<div class="container-fluid flex-grow-1 container-p-y product-container">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <h4 class="fw-bold mb-0 text-uppercase">
            <span class="text-muted fw-light">ទំនិញស្តុក /</span> កែប្រែព័ត៌មានទំនិញ (Edit Product)
        </h4>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary shadow-sm fw-semibold">
                <i class="bx bx-arrow-back me-1"></i> ត្រឡប់ក្រោយ
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card edit-card border-0 shadow-sm mb-4">
                
                <div class="card-header bg-white border-bottom p-4 d-flex align-items-center">
                    <div class="bg-label-warning p-2 rounded me-3 d-flex align-items-center justify-content-center" style="background-color: rgba(255, 171, 0, 0.1); color: #ffab00; width: 45px; height: 45px;">
                        <i class="bx bx-edit-alt fs-3"></i>
                    </div>
                    <div>
                        <h5 class="m-0 fw-bold text-dark">កែប្រែព័ត៌មានទំនិញ</h5>
                        <small class="text-muted">សូមបំពេញព័ត៌មានខាងក្រោមឱ្យបានត្រឹមត្រូវ</small>
                    </div>
                </div>
                
                <div class="card-body p-4 p-md-5">
                    <form action="{{ route('products.update', $product->id) }}" method="POST">
                        @csrf
                        @method('PUT') 

                        <div class="row g-4">
                            <div class="col-md-6 col-12">
                                <label class="form-label fw-semibold text-dark">Item Code (កូដទំនិញ) <span class="text-danger">*</span></label>
                                <div class="input-group input-group-merge shadow-none border rounded">
                                    <span class="input-group-text border-0"><i class="bx bx-barcode-reader fs-5"></i></span>
                                    <input type="text" name="item_code" class="form-control border-0 ps-0" value="{{ $product->item_code }}" required>
                                </div>
                            </div>

                            <div class="col-md-6 col-12">
                                <label class="form-label fw-semibold text-dark">Product Name (ឈ្មោះទំនិញ) <span class="text-danger">*</span></label>
                                <div class="input-group input-group-merge shadow-none border rounded">
                                    <span class="input-group-text border-0"><i class="bx bx-package fs-5"></i></span>
                                    <input type="text" name="item_name" class="form-control border-0 ps-0" value="{{ $product->item_name }}" required>
                                </div>
                            </div>

                            <div class="col-md-6 col-12">
                                <label class="form-label fw-semibold text-dark">Category (ប្រភេទទំនិញ)</label>
                                <div class="input-group input-group-merge shadow-none border rounded">
                                    <span class="input-group-text border-0"><i class="bx bx-category fs-5"></i></span>
                                    <select name="category" class="form-select border-0 ps-0">
                                        <option value="">-- ជ្រើសរើសប្រភេទ --</option>
                                        <optgroup label="4 WHEEL (រថយន្ត)">
                                            <option value="MG 4WHEEL" {{ $product->category == 'MG 4WHEEL' ? 'selected' : '' }}>MG 4WHEEL</option>
                                            <option value="LP 4WHEEL" {{ $product->category == 'LP 4WHEEL' ? 'selected' : '' }}>LP 4WHEEL (LEAPMOTOR)</option>
                                            <option value="IM 4WHEEL" {{ $product->category == 'IM 4WHEEL' ? 'selected' : '' }}>IM 4WHEEL</option>
                                        </optgroup>
                                        <optgroup label="2 WHEEL (ម៉ូតូ)">
                                            <option value="RE 2WHEEL" {{ $product->category == 'RE 2WHEEL' ? 'selected' : '' }}>RE 2WHEEL (ROYAL ENFIELD)</option>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 col-12">
                                <label class="form-label fw-semibold text-dark">Current Stock (ចំនួនស្តុក)</label>
                                <div class="input-group input-group-merge shadow-none border rounded bg-light">
                                    <span class="input-group-text border-0 bg-transparent"><i class="bx bx-layer fs-5 text-muted"></i></span>
                                    <input type="number" name="stock_qty" class="form-control border-0 bg-transparent ps-0 fw-bold text-dark" value="{{ $product->stock_qty }}" min="0">
                                </div>
                            </div>

                            <div class="col-md-6 col-12">
                                <label class="form-label fw-semibold text-dark">Cost (តម្លៃទិញចូល) <span class="text-danger">*</span></label>
                                <div class="input-group input-group-merge shadow-none border rounded">
                                    <span class="input-group-text border-0 fw-bold text-success fs-5">$</span>
                                    <input type="number" step="0.01" name="cost" class="form-control border-0 ps-0 fw-semibold" value="{{ $product->cost }}" required>
                                </div>
                            </div>

                            <div class="col-md-6 col-12">
                                <label class="form-label fw-semibold text-dark">Price (តម្លៃលក់ចេញ) <span class="text-danger">*</span></label>
                                <div class="input-group input-group-merge shadow-none border rounded" style="border-color: rgba(206, 24, 30, 0.3) !important; background-color: #fffafb;">
                                    <span class="input-group-text border-0 bg-transparent fw-bold text-primary fs-5">$</span>
                                    <input type="number" step="0.01" name="selling_price" class="form-control border-0 bg-transparent ps-0 fw-bold text-primary" value="{{ $product->selling_price ?? $product->price }}" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark">Description (ការពិពណ៌នា)</label>
                                <div class="input-group input-group-merge shadow-none border rounded">
                                    <span class="input-group-text border-0 align-items-start pt-3"><i class="bx bx-comment-detail fs-5"></i></span>
                                    <textarea name="description" class="form-control border-0 ps-0 py-2" rows="4" placeholder="បញ្ចូលព័ត៌មានបន្ថែមនៅទីនេះ...">{{ $product->description }}</textarea>
                                </div>
                            </div>
                        </div>

                        <hr class="my-5 border-light">

                        <div class="d-flex flex-column flex-sm-row justify-content-end gap-3">
                            <a href="{{ route('products.index') }}" class="btn btn-light fw-bold px-4 py-2 shadow-sm order-2 order-sm-1">
                                <i class="bx bx-x me-1"></i> បោះបង់ (Cancel)
                            </a>
                            <button type="submit" class="btn btn-warning text-dark fw-bold px-5 py-2 shadow-sm order-1 order-sm-2">
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