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
    .readonly-bg { background-color: #f1f5f9 !important; cursor: not-allowed; }
</style>

<div class="container-fluid flex-grow-1 container-p-y product-container">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <h4 class="fw-bold mb-0 text-uppercase">
            <span class="text-muted fw-light">ទំនិញស្តុក /</span> កែប្រែព័ត៌មានទំនិញ (Edit Product)
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
                    <div class="bg-label-warning p-2 rounded me-3" style="background-color: rgba(255, 171, 0, 0.1); color: #ffab00;">
                        <i class="bx bx-edit fs-4"></i>
                    </div>
                    <h5 class="m-0 fw-bold" style="color: #0f172a;">ព័ត៌មានលម្អិតរបស់ទំនិញ</h5>
                </div>
                
                <div class="card-body pt-4">
                    <form action="{{ route('products.update', $product->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-4">
                            
                            <div class="col-xl-6 col-lg-6 col-md-6 col-12">
                                <label class="form-label fw-semibold text-muted mb-1">Item Code (កូដទំនិញ) <span class="text-danger">*</span></label>
                                <div class="input-group input-group-merge shadow-none border rounded">
                                    <span class="input-group-text border-0"><i class="bx bx-barcode-reader"></i></span>
                                    <input type="text" name="item_code" class="form-control border-0 ps-0 fw-bold" required value="{{ $product->item_code }}">
                                </div>
                            </div>

                            <div class="col-xl-6 col-lg-6 col-md-6 col-12">
                                <label class="form-label fw-semibold text-muted mb-1">Product Name (ឈ្មោះទំនិញ) <span class="text-danger">*</span></label>
                                <div class="input-group input-group-merge shadow-none border rounded">
                                    <span class="input-group-text border-0"><i class="bx bx-package"></i></span>
                                    <input type="text" name="item_name" class="form-control border-0 ps-0" required value="{{ $product->item_name }}">
                                </div>
                            </div>

                            <div class="col-xl-6 col-lg-6 col-md-6 col-12">
                                <label class="form-label fw-semibold text-muted mb-1">Category (ប្រភេទទំនិញ) <span class="text-danger">*</span></label>
                                <div class="input-group input-group-merge shadow-none border rounded">
                                    <span class="input-group-text border-0"><i class="bx bx-category"></i></span>
                                    <select name="category" class="form-select border-0 ps-0" required>
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

                            <div class="col-xl-6 col-lg-6 col-md-6 col-12">
                                <label class="form-label fw-semibold text-muted mb-1">Location / Branch (ឃ្លាំងទទួលទំនិញ) <span class="text-danger">*</span></label>
                                <div class="input-group input-group-merge shadow-none border rounded">
                                    <span class="input-group-text border-0"><i class="bx bx-store-alt"></i></span>
                                    <select name="location_id" class="form-select border-0 ps-0" required>
                                        <option value="">-- ជ្រើសរើសឃ្លាំង/សាខា --</option>
                                        <option value="25" {{ $product->location_id == 25 ? 'selected' : '' }}>Mg Cambodia Warehouse (MG)</option>
                                        <option value="23" {{ $product->location_id == 23 ? 'selected' : '' }}>VIVe Warehouse (Leapmotor)</option>
                                        <option value="26" {{ $product->location_id == 26 ? 'selected' : '' }}>IM Warehouse (IM)</option>
                                        <option value="27" {{ $product->location_id == 27 ? 'selected' : '' }}>Royal Warehouse (Royal Enfield)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- ស្តុកទុកជា Readonly ព្រោះការកែប្រែស្តុកគួរធ្វើឡើងតាមការ Receive ឬ Adjust Stock -->
                            <div class="col-xl-6 col-lg-6 col-md-6 col-12">
                                <label class="form-label fw-semibold text-muted mb-1">Current Stock (ស្តុកបច្ចុប្បន្ន)</label>
                                <div class="input-group input-group-merge shadow-none border rounded readonly-bg">
                                    <span class="input-group-text border-0 readonly-bg"><i class="bx bx-layer"></i></span>
                                    <input type="number" name="stock_qty" class="form-control border-0 ps-0 readonly-bg fw-bold text-dark" value="{{ $product->stock_qty }}" readonly>
                                </div>
                                <small class="text-muted" style="font-size: 0.75rem;">* ចំនួនស្តុកអាចកែប្រែបានតាមរយៈការ Receive ឫផ្ទេរទំនិញ</small>
                            </div>

                            <div class="col-xl-6 col-lg-6 col-md-6 col-12">
                                <label class="form-label fw-semibold text-muted mb-1">Cost (តម្លៃទិញចូល) <span class="text-danger">*</span></label>
                                <div class="input-group input-group-merge shadow-none border rounded">
                                    <span class="input-group-text border-0 fw-bold text-success">$</span>
                                    <input type="number" step="0.01" name="cost" class="form-control border-0 ps-0 fw-bold" required value="{{ $product->cost }}">
                                </div>
                            </div>

                            <div class="col-xl-6 col-lg-6 col-md-6 col-12">
                                <label class="form-label fw-semibold text-muted mb-1">Price (តម្លៃលក់ចេញ) <span class="text-danger">*</span></label>
                                <div class="input-group input-group-merge shadow-none border rounded">
                                    <span class="input-group-text border-0 fw-bold text-primary">$</span>
                                    <input type="number" step="0.01" name="price" class="form-control border-0 ps-0 fw-bold" required value="{{ $product->price }}">
                                </div>
                            </div>

                            <!-- ✅ ផ្នែក Description ត្រូវបានបញ្ចូលយ៉ាងត្រឹមត្រូវ -->
                            <div class="col-12">
                                <label class="form-label fw-semibold text-muted mb-1">Description (ការពិពណ៌នា)</label>
                                <div class="input-group input-group-merge shadow-none border rounded">
                                    <span class="input-group-text border-0 align-items-start pt-2"><i class="bx bx-comment-detail"></i></span>
                                    <textarea name="description" class="form-control border-0 ps-0" rows="3" placeholder="ព័ត៌មានបន្ថែមពីទំនិញ...">{{ $product->description }}</textarea>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 border-secondary">

                        <div class="d-flex justify-content-end gap-3">
                            <a href="{{ route('products.index') }}" class="btn btn-light fw-semibold px-4 shadow-sm border">
                                <i class="bx bx-x me-1"></i> បោះបង់ (Cancel)
                            </a>
                            <button type="submit" class="btn btn-warning fw-bold px-5 shadow-sm text-dark">
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