@extends('layouts.homePages')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    :root { --brand-red: #CE181E; --text-heading: #0f172a; --text-body: #475569; --border-color: #cbd5e1; }
    .pos-container { font-family: 'Plus Jakarta Sans', sans-serif !important; padding: 2rem; background: #f8fafc; min-height: 100vh; }
    .pos-card { background: #fff; border: 1px solid var(--border-color); border-radius: 10px; padding: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    
    .invoice-header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid var(--brand-red); padding-bottom: 1.5rem; margin-bottom: 1.5rem; }
    .invoice-title { margin: 0; color: var(--brand-red); font-size: 1.5rem; font-weight: 700; text-transform: uppercase; }
    
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem; }
    .info-box { background: #f8fafc; padding: 1.25rem; border-radius: 8px; border: 1px solid var(--border-color); }
    .info-box p { margin: 0 0 0.5rem 0; color: var(--text-body); font-size: 0.9rem; }
    .info-box strong { color: var(--text-heading); }
    
    .pos-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
    .pos-table th { background: #f1f5f9; text-align: left; padding: 1rem; font-size: 0.85rem; border-bottom: 1px solid var(--border-color); color: var(--text-heading); }
    .pos-table td { padding: 1rem; border-bottom: 1px solid var(--border-color); font-size: 0.9rem; color: var(--text-body); }
    
    .btn-back { display: inline-block; margin-bottom: 1.5rem; color: #0284c7; text-decoration: none; font-weight: 600; font-size: 0.9rem; }
    .btn-back:hover { text-decoration: underline; }
</style>

<div class="pos-container">
    <a href="{{ route('receive-headers.index') }}" class="btn-back">← Back to List</a>
    
    <div class="pos-card">
        <div class="invoice-header">
            <div>
                <h1 class="invoice-title">Receiving Record</h1>
                <p style="margin: 5px 0 0 0; color: var(--text-body);">View details of received items</p>
            </div>
            <div style="text-align: right;">
                <h2 style="margin: 0; color: var(--text-heading);">{{ $receiveHeader->document_no }}</h2>
                <p style="margin: 5px 0 0 0; color: var(--text-body);">Date: <strong>{{ date('d-M-Y', strtotime($receiveHeader->date)) }}</strong></p>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-box">
                <p style="text-transform: uppercase; font-size: 0.75rem; font-weight: 700; color: #94a3b8; margin-bottom: 10px;">Vendor Information</p>
                <p>Name: <strong>{{ $receiveHeader->vendor->vendor_name ?? 'N/A' }}</strong></p>
                <p>Contact: <strong>{{ $receiveHeader->vendor->contact_person ?? '-' }}</strong></p>
                <p>Phone: <strong>{{ $receiveHeader->vendor->phone ?? '-' }}</strong></p>
            </div>
            <div class="info-box">
                <p style="text-transform: uppercase; font-size: 0.75rem; font-weight: 700; color: #94a3b8; margin-bottom: 10px;">Receiving Location</p>
                <p>Branch: <strong>{{ $receiveHeader->branch->branch_name ?? 'N/A' }}</strong></p>
                </div>
        </div>

        <h3 style="margin-top: 0; color: var(--text-heading); font-size: 1.1rem;">Received Items</h3>
        <table class="pos-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item Code</th>
                    <th>Product Name</th>
                    <th style="text-align: right;">Qty</th>
                    <th style="text-align: right;">Unit Cost</th>
                    <th style="text-align: right;">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                @php $grand_total = 0; @endphp
                
                @forelse($receiveHeader->details as $index => $detail)
                    @php $grand_total += $detail->total_cost; @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td style="font-weight: 600;">{{ $detail->product->item_code ?? 'N/A' }}</td>
                        <td>{{ $detail->product->item_name ?? 'N/A' }}</td>
                        <td style="text-align: right;">{{ number_format($detail->qty, 0) }}</td>
                        <td style="text-align: right;">${{ number_format($detail->cost, 2) }}</td>
                        <td style="text-align: right; font-weight: 600; color: var(--text-heading);">${{ number_format($detail->total_cost, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2rem;">មិនទាន់មានទំនិញក្នុងវិក្កយបត្រនេះទេ!</td>
                    </tr>
                @endforelse
            </tbody>
            @if(count($receiveHeader->details) > 0)
            <tfoot>
                <tr>
                    <td colspan="5" style="text-align: right; font-weight: 700; font-size: 1.1rem; padding: 1.5rem 1rem;">Grand Total:</td>
                    <td style="text-align: right; font-weight: 700; font-size: 1.25rem; color: var(--brand-red); padding: 1.5rem 1rem;">${{ number_format($grand_total, 2) }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
        
        <div style="text-align: right; margin-top: 2rem;">
            <button onclick="window.print()" style="background: #0f172a; color: #fff; border: none; padding: 0.75rem 2rem; border-radius: 6px; font-weight: 600; cursor: pointer;">
                Print Record
            </button>
        </div>

    </div>
</div>

<style type="text/css" media="print">
    body { visibility: hidden; }
    .pos-card { visibility: visible; position: absolute; left: 0; top: 0; width: 100%; border: none; box-shadow: none; }
    .btn-back, button { display: none !important; }
</style>
@endsection