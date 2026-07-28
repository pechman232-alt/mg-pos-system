@extends('layouts.homePages')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    :root { 
        --brand-red: #CE181E; 
        --brand-red-hover: #a81116;
        --text-heading: #0f172a; 
        --text-body: #475569; 
        --border-color: #cbd5e1; 
    }

    .pos-container { 
        font-family: 'Plus Jakarta Sans', sans-serif !important; 
        padding: 1.5rem; 
        background: #f8fafc; 
        min-height: 100vh; 
    }

    .pos-card { 
        background: #fff; 
        border: 1px solid var(--border-color); 
        border-radius: 12px; 
        padding: 1.5rem; 
        margin-bottom: 1.5rem; 
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); 
    }

    /* ផ្នែកបង្ហាញព័ត៌មានក្បាលវិក្កយបត្រ */
    .header-info-box { 
        display: flex; 
        justify-content: space-between; 
        align-items: center;
        background: #f1f5f9; 
        padding: 1.25rem; 
        border-radius: 10px; 
        border: 1px solid var(--border-color); 
        margin-bottom: 1.5rem; 
    }

    .header-info-box p { margin: 0; font-size: 0.9rem; color: var(--text-body); line-height: 1.6; }
    .header-info-box strong { color: var(--text-heading); }

    /* ប្លង់ Form បញ្ចូលទំនិញ */
    .form-grid { 
        display: grid; 
        grid-template-columns: 2.5fr 1fr 1.2fr auto; 
        gap: 1rem; 
        align-items: end; 
    }

    .pos-label { 
        display: block; 
        font-weight: 700; 
        color: var(--text-heading); 
        margin-bottom: 0.5rem; 
        font-size: 0.85rem; 
    }

    .pos-input, .pos-select { 
        width: 100%; 
        padding: 0.75rem; 
        border: 1px solid var(--border-color); 
        border-radius: 8px; 
        outline: none; 
        font-size: 0.9rem;
        transition: border-color 0.2s;
    }

    .pos-input:focus, .pos-select:focus { 
        border-color: var(--brand-red); 
        box-shadow: 0 0 0 3px rgba(206, 24, 30, 0.1); 
    }

    .btn-add { 
        background: var(--brand-red); 
        color: #fff; 
        border: none; 
        padding: 0 1.5rem; 
        border-radius: 8px; 
        font-weight: 700; 
        cursor: pointer; 
        transition: background 0.2s; 
        height: 48px; 
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-add:hover { background: var(--brand-red-hover); }
    .btn-add:disabled { background: #94a3b8; cursor: not-allowed; }

    /* តារាងទំនិញ */
    .pos-table { width: 100%; border-collapse: collapse; margin-top: 0.5rem; }
    .pos-table th { 
        background: #f8fafc; 
        text-align: left; 
        padding: 1rem; 
        font-size: 0.8rem; 
        text-transform: uppercase;
        letter-spacing: 0.025em;
        color: #64748b;
        border-bottom: 2px solid var(--border-color); 
    }

    .pos-table td { padding: 1rem; border-bottom: 1px solid #f1f5f9; font-size: 0.95rem; color: var(--text-heading); }
    .pos-table tr:hover { background: #fafafa; }

    .btn-delete {
        background: #fee2e2;
        color: #ef4444;
        border: 1px solid #fecaca;
        padding: 5px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.8rem;
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-delete:hover { background: #fca5a5; color: #fff; }

    .badge-doc { background: #fff; color: var(--brand-red); border: 1px solid var(--brand-red); padding: 2px 8px; border-radius: 5px; font-weight: 700; }
</style>

<div class="pos-container">
    
    @if(session('success'))
        <div style="background: #ecfdf5; color: #065f46; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #a7f3d0; font-weight: 500;">
            ✓ {{ session('success') }}
        </div>
    @endif

    <div class="pos-card">
        <div class="header-info-box">
            <div>
                <p>លេខឯកសារ: <span class="badge-doc">{{ $header->document_no }}</span></p>
                <p>កាលបរិច្ឆេទ: <strong>{{ date('d-M-Y', strtotime($header->date)) }}</strong></p>
            </div>
            <div style="text-align: right;">
                <p>អ្នកផ្គត់ផ្គង់: <strong>{{ $header->vendor->vendor_name ?? 'N/A' }}</strong></p>
                <div style="margin-top: 12px;">
                    <a href="{{ route('receive-headers.print', $header->id) }}" target="_blank" style="background:#1e293b; color:#fff; padding:10px 18px; border-radius:8px; text-decoration:none; font-size:0.85rem; font-weight:600;">🖨️ Print Receipt</a>
                    <a href="{{ route('receive-headers.index') }}" style="margin-left:12px; color:#64748b; text-decoration:none; font-size:0.85rem; font-weight:600;">← Back to List</a>
                </div>
            </div>
        </div>

        <form action="{{ route('receive-items.store') }}" method="POST" id="receiveForm">
            @csrf
            <input type="hidden" name="receive_header_id" value="{{ $header->id }}">
            
            <div class="form-grid">
                <div>
                    <label class="pos-label">Select Product (ជ្រើសរើសទំនិញ) *</label>
                    <select name="product_id" id="productSelect" required class="pos-select" onchange="setCost()">
                        <option value="">-- សូមជ្រើសរើសទំនិញ --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" data-cost="{{ $product->cost }}">
                                {{ $product->item_code }} - {{ $product->item_name }} ({{ $product->unit ?? 'UNIT' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="pos-label">Quantity (ចំនួន) *</label>
                    <input type="number" name="qty" min="1" step="0.01" required class="pos-input" value="1" style="text-align: center;">
                </div>
                <div>
                    <label class="pos-label">Unit Cost (តម្លៃដើម $) *</label>
                    <input type="number" name="cost" id="unitCost" step="0.001" min="0" required class="pos-input" placeholder="0.00" style="text-align: right;">
                </div>
                <div>
                    <button type="submit" class="btn-add" id="submitBtn"><span>+</span> Add Item</button>
                </div>
            </div>
        </form>
    </div>

    <div class="pos-card">
        <h3 style="margin: 0 0 1.5rem 0; color: var(--text-heading); font-size: 1.1rem; border-left: 4px solid var(--brand-red); padding-left: 10px;">Items in this Receipt (ទំនិញក្នុងវិក្កយបត្រនេះ)</h3>
        
        <table class="pos-table">
            <thead>
                <tr>
                    <th width="150">Item Code</th>
                    <th>Product Name</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Unit Cost</th>
                    <th style="text-align: right;">Total Cost</th>
                    <th style="text-align: center;">Action</th>
                </tr>
            </thead>
            <tbody>
                @php $grand_total = 0; @endphp
                @forelse($details as $detail)
                    @php $grand_total += $detail->total_cost; @endphp
                    <tr>
                        <td style="font-weight:700;">{{ $detail->product->item_code ?? 'N/A' }}</td>
                        <td>{{ $detail->product->item_name ?? 'N/A' }}</td>
                        <td style="text-align:center; font-weight: 600;">{{ number_format($detail->qty, 0) }}</td>
                        <td style="text-align:right;">${{ number_format($detail->cost, 2) }}</td>
                        <td style="text-align:right; font-weight:700; color:var(--brand-red);">${{ number_format($detail->total_cost, 2) }}</td>
                        <td style="text-align:center;">
                            <form action="{{ route('receive-items.destroy', $detail->id) }}" method="POST" onsubmit="return confirm('តើអ្នកពិតជាចង់លុបទំនិញនេះចេញពីវិក្កយបត្រ និងដកស្តុកវិញមែនទេ?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-delete">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center; padding:3rem; color:#94a3b8;">
                            <img src="https://cdn-icons-png.flaticon.com/512/11329/11329061.png" width="50" style="opacity: 0.3; margin-bottom: 10px;"><br>
                            មិនទាន់មានទំនិញក្នុងបញ្ជីនេះនៅឡើយទេ
                        </td>
                    </tr>
                @endforelse
            </tbody>
            
            @if(count($details) > 0)
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align:right; font-weight:700; font-size:1.1rem; padding-top:1.5rem; border: none;">Grand Total (សរុបរួម):</td>
                    <td colspan="2" style="text-align:left; font-weight:800; font-size:1.4rem; color:var(--brand-red); padding-top:1.5rem; padding-left:1.5rem; border: none;">
                        ${{ number_format($grand_total, 2) }}
                    </td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

<script>
    // មុខងារទាញយកតម្លៃដើមពីទំនិញមកដាក់ក្នុងប្រអប់អូតូ
    function setCost() {
        var select = document.getElementById("productSelect");
        var selectedOption = select.options[select.selectedIndex];
        var cost = selectedOption.getAttribute("data-cost");
        
        // ការពារតម្លៃទទេ ឬ Null
        if (cost && cost !== "null" && cost !== "") {
            document.getElementById("unitCost").value = parseFloat(cost).toFixed(2);
        } else {
            document.getElementById("unitCost").value = "";
        }
    }

    // ការពារការចុច Submit ពីរដង
    document.getElementById('receiveForm').addEventListener('submit', function() {
        var btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = 'Adding...';
    });
</script>
@endsection