@extends('layouts.homePages')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Kantumruy+Pro:wght@400;600;700&family=JetBrains+Mono:wght@700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    :root {
        --brand-indigo: #4f46e5; --brand-purple: #7c3aed; --brand-pink: #ec4899; --brand-red: #ef4444;
        --tf-gradient-4: linear-gradient(90deg, var(--brand-indigo) 0%, var(--brand-purple) 33%, var(--brand-pink) 66%, var(--brand-red) 100%);
        --slate-50: #f8fafc; --slate-200: #e2e8f0; --slate-800: #1e293b;
    }
    body { font-family: 'Plus Jakarta Sans', 'Kantumruy Pro', sans-serif !important; background-color: #f4f7fa; color: var(--slate-800); }
    .container-full { width: 100%; padding: 1.5rem 2.5rem; }
    
    /* Table Styling */
    .table-viewport { width: 100%; overflow-x: auto; border-radius: 0 0 24px 24px; background: #fff; }
    .table-custom { min-width: 1600px; width: 100%; border-collapse: collapse !important; }
    .table-custom th { background: var(--slate-50); padding: 12px 10px; color: #64748b; font-size: 10px; font-weight: 800; text-transform: uppercase; border: 1px solid #eef2f6; }
    .table-custom td { padding: 12px 10px; vertical-align: middle; font-size: 12px; font-weight: 600; border: 1px solid #eef2f6; white-space: nowrap; }
    
    /* Header & Action UI */
    .summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
    .stat-card { background: #fff; border-radius: 24px; padding: 1.5rem; border: 1px solid var(--slate-200); display: flex; align-items: center; justify-content: space-between; }
    .card-main { border-radius: 28px; background: #fff; border: 1px solid var(--slate-200); overflow: hidden; box-shadow: 0 10px 15px rgba(0,0,0,0.03); }
    .header-gradient { background: var(--tf-gradient-4); padding: 1.5rem 2.5rem; color: white; }
    
    /* Badges & Buttons */
    .status-badge { padding: 4px 12px; border-radius: 8px; font-size: 10px; font-weight: 800; }
    .status-payoff { background: #ecfdf5; color: #059669; }
    .status-booking { background: #fffbeb; color: #d97706; }
    .btn-action { width: 34px; height: 34px; border-radius: 10px; border: 1.5px solid var(--slate-200); display: inline-flex; align-items: center; justify-content: center; background: white; transition: 0.2s; color: #64748b; }
    .btn-action:hover:not([disabled]) { transform: translateY(-2px); border-color: var(--brand-indigo); color: var(--brand-indigo); }


    /* Branch Wrapper */
.branch-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Icon Style */
.branch-icon {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
}

/* Color Coding for each Branch */
.branch-pp { background: #fee2e2; color: #dc2626; } /* Phnom Penh - Red */
.branch-sr { background: #e0f2fe; color: #0284c7; } /* Siem Reap - Blue */
.branch-bb { background: #fef3c7; color: #d97706; } /* Battambang - Orange */
.branch-default { background: #f1f5f9; color: #64748b; }

.branch-text {
    font-weight: 700;
    color: #475569;
    font-size: 12px;
}
</style>

<div class="container-full">
    <div class="summary-grid text-start">
        <div class="stat-card">
            <div><span class="text-[10px] font-black text-muted uppercase">Total Revenue</span><div class="h4 font-black m-0">${{ number_format($totalRevenue, 2) }}</div></div>
            <div class="text-indigo-600 bg-indigo-50 p-3 rounded-xl"><i class="fas fa-money-bill-wave fa-lg"></i></div>
        </div>
        <div class="stat-card">
            <div><span class="text-[10px] font-black text-muted uppercase">Active Sales</span><div class="h4 font-black m-0 text-danger">{{ $totalContracts }}</div></div>
            <div class="text-red-600 bg-red-50 p-3 rounded-xl"><i class="fas fa-file-invoice-dollar fa-lg"></i></div>
        </div>
        <div class="stat-card">
            <div><span class="text-[10px] font-black text-muted uppercase">Payoff Rate</span><div class="h4 font-black m-0 text-success">{{ $settledCount }}</div></div>
            <div class="text-success bg-green-50 p-3 rounded-xl"><i class="fas fa-check-circle fa-lg"></i></div>
        </div>
        <div class="stat-card">
            <div><span class="text-[10px] font-black text-muted uppercase">New Today</span><div class="h4 font-black m-0 text-orange-500">{{ $todayEntries }}</div></div>
            <div class="text-orange-500 bg-orange-50 p-3 rounded-xl"><i class="fas fa-bolt fa-lg"></i></div>
        </div>
    </div>

    <div class="card-main">
        <div class="header-gradient d-flex justify-content-between align-items-center text-start">
            <div>
                <h2 class="text-white font-black m-0 h5 uppercase tracking-wider">Showroom Registry Intelligence</h2>
                <p class="text-white/70 small m-0">Real-time sales tracking & contract management</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="position-relative">
                    <i class="fas fa-search position-absolute text-white/50" style="left: 16px; top: 50%; transform: translateY(-50%);"></i>
                    <input type="text" id="liveSearch" class="form-control border-0 rounded-pill px-5 shadow-sm" style="width: 250px; height: 38px; background: rgba(255,255,255,0.15); color: white;" placeholder="Live search...">
                </div>

                <a href="{{ route('contracts.export', request()->all()) }}" class="btn bg-white rounded-pill px-4 font-black shadow-lg d-flex align-items-center" style="color: #107c10; height: 38px; border: none;">
                    <i class="fas fa-file-excel me-2"></i> EXCEL
                </a>

                <a href="{{ route('contracts.create') }}" class="btn bg-white rounded-pill px-4 font-black shadow-lg d-flex align-items-center" style="color: var(--brand-indigo); height: 38px; border: none;">
                    <i class="fas fa-plus-circle me-2"></i> NEW CONTRACT
                </a>
            </div>
        </div>

        <div class="table-viewport">
            <table class="table table-hover table-custom mb-0 text-start" id="contractTable">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Customer / Identity</th>
                        <th>Contract No.</th>
                        <th>Phone / Contact</th>
                        <th>Order Date</th>
                        <th>Showroom / Branch</th>
                        <th>Sale Person</th>
                        <th>Product Model</th>
                        <th class="text-end">Sub Total</th>
                        <th class="text-end text-danger">Discount</th>
                        <th class="text-end text-primary">Deposit</th>
                        <th class="text-end bg-light">Grand Total</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contracts as $index => $c)
                    <tr class="animate-row contract-row" style="animation-delay: {{ $index * 0.03 }}s">
                        <td class="text-center text-muted small">{{ $contracts->firstItem() + $index }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $c->customer_name }}</div>
                            <div class="text-[10px] text-muted uppercase">Source: {{ $c->source ?? 'Direct' }}</div>
                        </td>
                        <td><code class="text-danger fw-bold bg-danger/5 px-2 py-1 rounded">{{ $c->contract_number }}</code></td>
                        <td class="fw-bold font-monospace">{{ $c->phone_number }}</td>
                        <td class="small">{{ \Carbon\Carbon::parse($c->order_date)->format('d-M-Y') }}</td>
                        <td>
                            <div class="branch-wrapper">
                                @if($c->branch_name == 'Phnom Penh')
                                    <span class="branch-icon branch-pp"><i class="fas fa-location-dot"></i></span>
                                @elseif($c->branch_name == 'Siem Reap')
                                    <span class="branch-icon branch-sr"><i class="fas fa-temple"></i></span>
                                @elseif($c->branch_name == 'Battambang')
                                    <span class="branch-icon branch-bb"><i class="fas fa-city"></i></span>
                                @else
                                    <span class="branch-icon branch-default"><i class="fas fa-store"></i></span>
                                @endif
                                <span class="branch-text">{{ $c->branch_name }}</span>
                            </div>
                        </td>
                        <td class="text-primary fw-bold">{{ $c->sale_person }}</td>
                        <td>
                            <div class="fw-bold">{{ $c->model }}</div>
                            <div class="text-[10px] text-muted">{{ $c->color }}</div>
                        </td>
                        <td class="text-end font-monospace">${{ number_format($c->sub_total, 2) }}</td>
                        <td class="text-end text-danger font-monospace">-${{ number_format($c->discount, 2) }}</td>
                        <td class="text-end text-primary font-monospace">-${{ number_format($c->deposit, 2) }}</td>
                        <td class="text-end fw-bold text-dark bg-light font-monospace">${{ number_format($c->grand_total, 2) }}</td>
                        <td class="text-center">
                            <span class="status-badge status-{{ strtolower($c->status) }}">{{ $c->status }}</span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('contracts.show', $c->id) }}" class="btn-action shadow-sm" title="View Detail"><i class="fas fa-eye text-primary"></i></a>
                                
                                {{-- Protective Logic: Only Allow Edit/Delete if NOT Payoff --}}
                                @if($c->status == 'Booking')
                                    <form action="{{ route('contracts.update', $c->id) }}" method="POST" class="d-inline">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="Payoff">
                                        <input type="hidden" name="quick_toggle" value="true">
                                        <button type="button" class="btn-action text-success shadow-sm" onclick="confirmPayoff(this)" title="Settle as Paid">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('contracts.edit', $c->id) }}" class="btn-action shadow-sm" title="Edit Record"><i class="fas fa-edit text-warning"></i></a>
                                    <form action="{{ route('contracts.destroy', $c->id) }}" method="POST" id="del-{{ $c->id }}" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn-action text-danger shadow-sm" onclick="confirmDelete({{ $c->id }})" title="Delete Record">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                @else
                                    {{-- Payoff Records are Locked --}}
                                    <button class="btn-action bg-light border-0" disabled title="Locked (Paid Off)">
                                        <i class="fas fa-lock text-muted"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="14" class="py-5 text-center text-muted fw-bold">No active sales registries found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($contracts->hasPages())
        <div class="p-4 border-top d-flex justify-content-center bg-light/30 text-start">
            {{ $contracts->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

<script>
    // --- LIVE SEARCH ---
    document.getElementById('liveSearch').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('.contract-row');
        rows.forEach(row => {
            let text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });

    // --- SWEETALERT ACTIONS ---
    function confirmPayoff(button) {
        Swal.fire({
            title: 'Settle Payment?',
            text: "Mark this contract as PAID and LOCK record?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            confirmButtonText: 'Yes, Confirm Paid',
            borderRadius: '16px'
        }).then((result) => { if (result.isConfirmed) { button.closest('form').submit(); } });
    }

    function confirmDelete(id) {
        Swal.fire({
            title: 'Permanent Delete?',
            text: "Warning: This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Yes, Delete Now',
            borderRadius: '16px'
        }).then((result) => { if (result.isConfirmed) { document.getElementById('del-' + id).submit(); } });
    }
</script>
@endsection