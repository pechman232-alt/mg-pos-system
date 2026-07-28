@extends('layouts.homePages')

@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    :root { 
        --brand-red: #CE181E; --brand-red-hover: #a81116; 
        --text-heading: #0f172a; --text-body: #475569; 
        --bg-main: #f8fafc; --border-color: #cbd5e1; --bg-white: #ffffff;
    }
    .pos-container { font-family: 'Plus Jakarta Sans', sans-serif !important; background-color: var(--bg-main); color: var(--text-body); min-height: calc(100vh - 60px); padding: 1.5rem; }
    .pos-card { background-color: var(--bg-white); border: 1px solid var(--border-color); border-radius: 10px; box-shadow: 0 1px 3px 0 rgba(15, 23, 42, 0.05); padding: 1.5rem; margin-bottom: 1.5rem; }
    .pos-header { border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem; }
    .pos-title { margin: 0; font-size: 1.25rem; font-weight: 700; color: var(--text-heading); }
    .pos-grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.25rem; }
    .pos-label { display: block; font-weight: 600; color: var(--text-heading); margin-bottom: 0.4rem; font-size: 0.85rem; }
    .pos-input { width: 100%; padding: 0.6rem 0.75rem; border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.875rem; outline: none; transition: all 0.2s; }
    .pos-input:focus { border-color: var(--brand-red); box-shadow: 0 0 0 3px rgba(206, 24, 30, 0.1); }
    .btn-pos-primary { background-color: var(--brand-red); color: white; border: none; padding: 0.6rem 1.5rem; border-radius: 6px; font-weight: 600; cursor: pointer; }
    .btn-pos-secondary { background: white; border: 1px solid var(--border-color); padding: 0.6rem 1.5rem; border-radius: 6px; text-decoration: none; color: var(--text-body); }
    .pos-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
    .pos-table th { background: #f1f5f9; padding: 0.75rem; text-align: left; font-size: 0.85rem; border-bottom: 1px solid var(--border-color); }
    .pos-table td { padding: 0.5rem; border-bottom: 1px solid var(--border-color); }
    .pos-btn-delete { color: #ef4444; background: none; border: none; cursor: pointer; }
</style>

<div class="pos-container">
    <form action="{{ route('receive-headers.store') }}" method="POST">
        @csrf

        <div class="pos-card">
            <div class="pos-header">
                <h2 class="pos-title">Stock Receive / បង្កើតឯកសារទទួលទំនិញ</h2>
            </div>

            <div class="pos-grid-3">
                <div>
                    <label class="pos-label">Date (កាលបរិច្ឆេទ) *</label>
                    <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="pos-input">
                </div>
                <div>
                    <label class="pos-label">Document Nº *</label>
                    <input type="text" name="document_no" value="{{ $document_no }}" readonly class="pos-input" style="background: #f1f5f9; font-weight: bold; color: var(--brand-red);">
                </div>
                <div>
                    <label class="pos-label">Location (សាខា) *</label>
                    <select name="location_id" required class="pos-input">
                        <option value="">-- Select --</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="pos-label">Vendor (អ្នកផ្គត់ផ្គង់) *</label>
                    <select name="vendor_id" required class="pos-input">
                        <option value="">-- Select --</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}">{{ $vendor->vendor_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="pos-label">Purchase Invoice #</label>
                    <input type="text" name="purchase_invoice_no" placeholder="INV-..." class="pos-input">
                </div>
                <div>
                    <label class="pos-label">Tran. Vendor (អ្នកដឹក)</label>
                    <input type="text" name="tran_vendor" placeholder="ឈ្មោះអ្នកដឹក" class="pos-input">
                </div>
            </div>
        </div>

        <div class="pos-card">
            <h2 class="pos-title" style="margin-bottom: 1rem;">Items Detail / លម្អិតទំនិញ</h2>
            
            <div class="pos-table-wrapper">
                <table class="pos-table">
                    <thead>
                        <tr>
                            <th width="25%">Item (ទំនិញ) *</th>
                            <th width="25%">Description</th>
                            <th width="10%">UM</th>
                            <th width="10%" style="text-align: right;">Qty *</th>
                            <th width="15%" style="text-align: right;">Cost ($) *</th>
                            <th width="10%" style="text-align: right;">Total ($)</th>
                            <th width="5%" style="text-align: center;">Act</th>
                        </tr>
                    </thead>
                    <tbody id="itemBody">
                        <tr id="row_0">
                            <td>
                                <select name="items[0][product_id]" required class="pos-input product-select">
                                    <option value="">-- Select --</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-desc="{{ $product->item_name }}" data-cost="{{ $product->cost }}">{{ $product->item_code }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="text" name="items[0][description]" class="pos-input desc-input"></td>
                            <td><input type="text" name="items[0][rec_um]" value="UNIT" class="pos-input"></td>
                            <td><input type="number" name="items[0][qty]" value="1" min="1" step="0.01" class="pos-input qty-input" style="text-align: right;" oninput="calculateRow(0)"></td>
                            <td><input type="number" name="items[0][cost]" value="0" min="0" step="0.01" class="pos-input cost-input" style="text-align: right;" oninput="calculateRow(0)"></td>
                            <td style="text-align: right; vertical-align: middle; font-weight: 700; color: var(--brand-red);"><span id="total_0">0.00</span></td>
                            <td style="text-align: center;">
                                <button type="button" class="pos-btn-delete" onclick="removeRow(0)">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <button type="button" class="btn-add-row" onclick="addRow()" style="margin-top: 1rem; cursor: pointer; padding: 5px 15px; border-radius: 5px; border: 1px solid #bae6fd; background: #e0f2fe; color: #0284c7; font-weight: 600;">+ Add More Item</button>

            <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                <a href="{{ route('receive-headers.index') }}" class="btn-pos-secondary">Cancel</a>
                <button type="submit" class="btn-pos-primary">Save Document ✓</button>
            </div>
        </div>
    </form>
</div>

<script>
    let rowIdx = 1;

    function calculateRow(id) {
        let qty = document.querySelector(`input[name="items[${id}][qty]"]`).value;
        let cost = document.querySelector(`input[name="items[${id}][cost]"]`).value;
        let total = (parseFloat(qty) || 0) * (parseFloat(cost) || 0);
        document.getElementById(`total_${id}`).innerText = total.toFixed(2);
    }

    function removeRow(id) {
        let row = document.getElementById(`row_${id}`);
        if(row) row.remove();
    }

    function addRow() {
        let options = document.querySelector('.product-select').innerHTML;
        let tr = document.createElement('tr');
        tr.id = `row_${rowIdx}`;
        tr.innerHTML = `
            <td><select name="items[${rowIdx}][product_id]" required class="pos-input product-select">${options}</select></td>
            <td><input type="text" name="items[${rowIdx}][description]" class="pos-input desc-input"></td>
            <td><input type="text" name="items[${rowIdx}][rec_um]" value="UNIT" class="pos-input"></td>
            <td><input type="number" name="items[${rowIdx}][qty]" value="1" step="0.01" class="pos-input qty-input" style="text-align: right;" oninput="calculateRow(${rowIdx})"></td>
            <td><input type="number" name="items[${rowIdx}][cost]" value="0" step="0.01" class="pos-input cost-input" style="text-align: right;" oninput="calculateRow(${rowIdx})"></td>
            <td style="text-align: right; vertical-align: middle; font-weight: 700; color: var(--brand-red);"><span id="total_${rowIdx}">0.00</span></td>
            <td style="text-align: center;"><button type="button" class="pos-btn-delete" onclick="removeRow(${rowIdx})">Delete</button></td>
        `;
        document.getElementById('itemBody').appendChild(tr);
        rowIdx++;
    }

    document.addEventListener('change', function(e) {
        if(e.target && e.target.classList.contains('product-select')) {
            let opt = e.target.options[e.target.selectedIndex];
            let tr = e.target.closest('tr');
            let id = tr.id.split('_')[1];
            if(opt.getAttribute('data-desc')) tr.querySelector('.desc-input').value = opt.getAttribute('data-desc');
            if(opt.getAttribute('data-cost')) {
                tr.querySelector('.cost-input').value = opt.getAttribute('data-cost');
                calculateRow(id);
            }
        }
    });
</script>
@endsection