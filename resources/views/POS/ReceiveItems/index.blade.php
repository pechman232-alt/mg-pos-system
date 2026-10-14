@extends('layouts.homePages')

@section('content')
<!-- Add SweetAlert2 CSS & JS CDN for better delete confirmation -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
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

    /* តារាងទំនិញ UI ថ្មីស្អាតជាងមុន */
    .pos-table { width: 100%; border-collapse: collapse; margin-top: 0.5rem; }
    .pos-table th { 
        background: #F8FAFC; 
        text-align: left; 
        padding: 1rem; 
        font-size: 0.8rem; 
        text-transform: uppercase;
        letter-spacing: 0.025em;
        color: #64748b;
        border-bottom: 2px solid var(--border-color); 
    }

    .pos-table td { padding: 1rem; border-bottom: 1px solid #f1f5f9; font-size: 0.95rem; color: var(--text-heading); vertical-align: middle; }
    .pos-table tr:hover { background: #fafafa; }

    .btn-delete {
        background: #fee2e2;
        color: #ef4444;
        border: 1px solid #fecaca;
        padding: 6px 14px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.8rem;
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-delete:hover { background: #ef4444; color: #fff; }

    .badge-doc { background: #fff; color: var(--brand-red); border: 1px solid var(--brand-red); padding: 2px 8px; border-radius: 5px; font-weight: 700; }
    .badge-cat { background: #dcfce7; color: #16a34a; font-size: 10px; padding: 2px 6px; border-radius: 4px; font-weight: 600; margin-left: 6px; }
    .text-qty { color: #3b82f6; font-weight: 700; }
    .text-amount { color: var(--brand-red); font-weight: 700; }
</style>

<div class="pos-container">
    
    @if(session('success'))
        <div style="background: #ecfdf5; color: #065f46; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #a7f3d0; font-weight: 500;">
            ✓ {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #ffeeec; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #fecaca; font-weight: 500;">
            ✕ {{ session('error') }}
        </div>
    @endif

    <div class="pos-card">
        <div class="header-info-box">
            <div>
                <p>លេខឯកសារ: <span class="badge-doc">{{ $header->document_no }}</span></p>
                <p>កាលបរិច្ឆេទ: <strong>{{ date('d-M-Y', strtotime($header->date)) }}</strong></p>
            </div>
            <div>
                <!-- ✅ បន្ថែមការបង្ហាញទីតាំងសាខា/ឃ្លាំងនៅទីនេះ ដើម្បីឱ្យងាយមើល -->
                <p>ឃ្លាំង/សាខាទទួល: <strong style="color: #CE181E; font-size: 1rem;"><i class="bx bx-store-alt"></i> {{ $header->branch->branch_name ?? 'N/A' }}</strong></p>
                <p>អ្នកផ្គត់ផ្គង់: <strong>{{ $header->vendor->vendor_name ?? 'N/A' }}</strong></p>
            </div>
            <div style="text-align: right;">
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
                    <!-- បានបន្ថែម data-stock និង onchange="updateProductInfo()" -->
                    <select name="product_id" id="productSelect" required class="pos-select" onchange="updateProductInfo()">
                        <option value="">-- សូមជ្រើសរើសទំនិញ --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" 
                                    data-cost="{{ $product->cost }}" 
                                    data-stock="{{ $product->stock_qty }}">
                                {{ $product->item_code }} - {{ $product->item_name }} ({{ $product->unit ?? 'UNIT' }})
                            </option>
                        @endforeach
                    </select>
                    <!-- បន្ថែម DIV សម្រាប់បង្ហាញស្តុកបច្ចុប្បន្នអូតូ -->
                    <div id="stockInfo" style="margin-top: 6px; font-size: 0.8rem; font-weight: 600;"></div>
                </div>
                <div>
                    <label class="pos-label">Quantity (ចំនួន) *</label>
                    <input type="number" name="qty" min="0.01" step="0.01" required class="pos-input" value="1" style="text-align: center;">
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
        <h3 style="margin: 0 0 1.5rem 0; color: var(--text-heading); font-size: 1.1rem; border-left: 4px solid var(--brand-red); padding-left: 10px; display: flex; align-items: center; justify-content: space-between;">
            Items in this Receipt (ទំនិញក្នុងវិក្កយបត្រនេះ)
            <span class="badge" style="background: #f1f5f9; color: #475569; font-size: 0.8rem; font-weight: 600;">សរុបមុខទំនិញ: {{ count($details) }}</span>
        </h3>
        
        <div class="table-responsive">
            <table class="pos-table">
                <thead>
                    <tr>
                        <th width="150">Item Code</th>
                        <th>Product Name</th>
                        <th style="text-align: center;">Qty</th>
                        <th style="text-align: right;">Unit Cost</th>
                        <th style="text-align: right;">Total Cost</th>
                        <th style="text-align: center;" width="120">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php $grand_total = 0; @endphp
                    @forelse($details as $detail)
                        @php $grand_total += $detail->total_cost; @endphp
                        <tr>
                            <td style="font-weight:700; color: #3b82f6;">{{ $detail->product->item_code ?? 'N/A' }}</td>
                            <td>
                                {{ $detail->product->item_name ?? 'N/A' }}
                                @if(isset($detail->product->category) && $detail->product->category)
                                    <span class="badge-cat">{{ $detail->product->category }}</span>
                                @endif
                            </td>
                            <td style="text-align:center;" class="text-qty">{{ number_format($detail->qty, 2) }}</td>
                            <td style="text-align:right;">${{ number_format($detail->cost, 2) }}</td>
                            <td style="text-align:right;" class="text-amount">${{ number_format($detail->total_cost, 2) }}</td>
                            <td style="text-align:center;">
                                <form action="{{ route('receive-items.destroy', $detail->id) }}" method="POST" id="delete-form-{{ $detail->id }}">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="confirmDelete('{{ $detail->id }}', '{{ $detail->product->item_name ?? 'ទំនិញនេះ' }}', '{{ $detail->qty }}')" class="btn-delete">
                                        <i class="bx bx-trash"></i> Delete
                                    </button>
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
</div>

<!-- SweetAlert2 JS CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // មុខងារទាញយកតម្លៃដើម និងបង្ហាញស្តុកបច្ចុប្បន្នអូតូ
    function updateProductInfo() {
        var select = document.getElementById("productSelect");
        var selectedOption = select.options[select.selectedIndex];
        
        var cost = selectedOption.getAttribute("data-cost");
        var stock = selectedOption.getAttribute("data-stock");
        var stockInfoDiv = document.getElementById("stockInfo");

        // 1. បង្ហាញតម្លៃដើមចូលប្រអប់ Cost
        if (cost && cost !== "null" && cost !== "") {
            document.getElementById("unitCost").value = parseFloat(cost).toFixed(2);
        } else {
            document.getElementById("unitCost").value = "";
        }

        // 2. បង្ហាញស្តុកបច្ចុប្បន្ននៅខាងក្រោម Select
        if (selectedOption.value !== "") {
            stockInfoDiv.innerHTML = '📊 ស្តុកបច្ចុប្បន្ននៅក្នុងប្រព័ន្ធ: <span style="color:#10b981; font-weight:700;">' + parseFloat(stock).toLocaleString() + '</span>';
        } else {
            stockInfoDiv.innerHTML = '';
        }
    }

    // មុខងារបញ្ជាក់ការលុបដោយប្រើ SweetAlert2 ព្រមទាំងបង្ហាញចំនួនដែលត្រូវកាត់ចេញ
    function confirmDelete(id, name, qty) {
        Swal.fire({
            title: 'តើអ្នកប្រាកដឬទេ?',
            html: `អ្នកនឹងលុប <b>"${name}"</b> ចេញពីវិក្កយបត្រ!<br>` +
                  `ស្តុកនឹងត្រូវកាត់ចេញវិញចំនួន: <span style="color:#ef4444; font-weight:bold;">-${qty}</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#CE181E',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'យល់ព្រម, លុបវាចេញ!',
            cancelButtonText: 'បោះបង់'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }

    // ការពារការចុច Submit ពីរដង
    document.getElementById('receiveForm').addEventListener('submit', function() {
        var btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = 'Adding...';
    });
</script>
@endsection