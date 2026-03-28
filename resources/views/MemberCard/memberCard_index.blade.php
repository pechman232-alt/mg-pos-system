@extends('layouts.homePages')
@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* ====== BRAND IDENTITY & COLORS ====== */
    :root {
        --brand-indigo: #4f46e5;
        --brand-purple: #7c3aed;
        --brand-pink: #ec4899;
        --brand-red: #ef4444;
        --brand-success: #10b981;
        --bg-body: #f8fafc;
        --font-family: 'Plus Jakarta Sans', sans-serif !important;
        --pop-gradient: linear-gradient(135deg, var(--brand-indigo) 0%, var(--brand-purple) 50%, var(--brand-pink) 100%);
    }

    body { background-color: var(--bg-body); font-family: var(--font-family); font-size: 13px; color: #1e293b; margin: 0; }

    /* ====== 100% FIT SCREEN WRAPPER ====== */
    .fluid-page-wrapper { width: 100% !important; max-width: 100% !important; padding: 1.5rem !important; animation: fadeInPage 0.5s ease-out; }

    @keyframes fadeInPage { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    /* ====== CARD STYLING ====== */
    .card-main { border: none !important; border-radius: 16px !important; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05) !important; background: #fff; overflow: hidden; width: 100%; }
    .branded-header { background: var(--pop-gradient) !important; padding: 1.2rem 2rem; display: flex; justify-content: space-between; align-items: center; }
    .header-title { color: white !important; font-weight: 800; font-size: 1.2rem; display: flex; align-items: center; gap: 12px; margin: 0; }

    /* ====== NEW ID HIGHLIGHT (ON TOP) ====== */
    .row-new-entry { background-color: rgba(16, 185, 129, 0.05) !important; border-left: 5px solid var(--brand-success) !important; }
    .badge-new-glow { background: var(--brand-success); color: white; font-size: 9px; font-weight: 900; padding: 2px 8px; border-radius: 4px; margin-left: 10px; animation: glow 1.5s infinite; }
    @keyframes glow { 0% { opacity: 1; } 50% { opacity: 0.5; } 100% { opacity: 1; } }

    /* ====== ACTION BUTTONS ====== */
    .btn-action { border-radius: 8px !important; font-weight: 700; font-size: 12px !important; padding: 10px 22px !important; transition: 0.3s; border: none; text-decoration: none !important; }
    .btn-add { background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(8px); color: white !important; border: 1px solid rgba(255, 255, 255, 0.3); }
    .btn-add:hover { background: white; color: var(--brand-indigo) !important; transform: translateY(-2px); }

    /* ====== TABLE ====== */
    .table-responsive { width: 100%; margin: 0; }
    #posts { width: 100% !important; border-collapse: collapse !important; }
    #posts thead th { background-color: #fcfdfe !important; color: #64748b !important; font-weight: 800; text-transform: uppercase; font-size: 11px; padding: 1.5rem 2rem !important; border-bottom: 2px solid #f1f5f9 !important; }
    #posts tbody td { padding: 14px 2rem !important; vertical-align: middle; border-bottom: 1px solid #f1f5f9; font-weight: 600; }

    .highlight-label { font-weight: 700; color: var(--brand-indigo); background: rgba(79, 70, 229, 0.08); padding: 6px 14px; border-radius: 8px; display: inline-flex; align-items: center; }
    .badge-status { font-size: 11px; font-weight: 800; padding: 6px 14px; border-radius: 8px; display: inline-flex; align-items: center; }
    .status-sold { background: #fee2e2; color: var(--brand-red); }
    .status-available { background: #ecfdf5; color: #059669; }

    /* SEARCH UI */
    .search-container { position: relative; flex-grow: 1; }
    .btn-clear { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: #e2e8f0; color: #64748b; border: none; width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px; cursor: pointer; z-index: 10; }
    .btn-clear:hover { background: var(--brand-red); color: white; }

    .form-control-custom { padding: 12px 18px; border-radius: 12px; border: 1.5px solid #e2e8f0; font-size: 13px; font-weight: 600; background: #f8fafc; width: 100%; transition: 0.3s; }
    .form-control-custom:focus { border-color: var(--brand-indigo); background: #fff; outline: none; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }
</style>

<div class="fluid-page-wrapper">
    <div class="card-main">
        <div class="branded-header">
            <h5 class="header-title"><i class="fas fa-id-card"></i> MEMBER CARD INVENTORY</h5>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-action btn-add bg-white text-dark shadow-sm" data-bs-toggle="modal" data-bs-target="#basicModal" style="color: var(--brand-indigo) !important;">
                    <i class="fas fa-file-import me-1"></i> IMPORT
                </button>
                <a href="{{ url('member-cards/create') }}" class="btn btn-action btn-add">
                    <i class="fas fa-plus-circle me-1"></i> NEW CARD
                </a>
            </div>
        </div>

        <div class="p-4 border-bottom bg-white">
            <div class="row g-4 align-items-center">
                <div class="col-xl-7">
                    <form method="GET" action="{{ url('member-cards') }}" class="d-flex gap-3 flex-wrap">
                        <select name="isSale" class="form-select w-auto fw-bold" style="border-radius: 10px; height: 48px;">
                            <option value="">All Status</option>
                            <option value="0" {{ request('isSale') == '0' ? 'selected' : '' }}>SOLD</option>
                            <option value="1" {{ request('isSale') == '1' ? 'selected' : '' }}>AVAILABLE</option>
                        </select>
                        <input class="form-control-custom w-auto" type="date" name="date_from" value="{{ $date_from }}">
                        <input class="form-control-custom w-auto" type="date" name="date_to" value="{{ $date_to }}">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-800" style="background: var(--brand-indigo); border:none; height: 48px;">
                            <i class="fas fa-filter me-2"></i> FILTER
                        </button>
                    </form>
                </div>
                <div class="col-xl-5">
                    <form action="{{ url('member-cards') }}" method="GET" id="searchForm">
                        <div class="input-group shadow-sm" style="border-radius: 12px; overflow: hidden;">
                            <div class="search-container">
                                <input type="text" class="form-control-custom border-0" style="height: 48px;" placeholder="Search ID (e.g. TF-001)..." name="keyword" value="{{ request('keyword') }}">
                                @if(request('keyword'))
                                    <a href="{{ url('member-cards') }}" class="btn-clear"><i class="fas fa-times"></i></a>
                                @endif
                            </div>
                            <button type="submit" class="btn px-4" style="background: var(--brand-indigo); color:white; border-radius: 0;">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table id="posts" class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 80px;">REF ID</th>
                        <th>Card Digital Identifier</th>
                        <th class="text-center">Inventory State</th>
                        <th class="text-center">Database Management</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($member_cards as $index => $item)
                        @php $isNew = $item->created_at && $item->created_at->isToday(); @endphp
                        <tr class="{{ $isNew ? 'row-new-entry' : '' }}">
                            <td class="text-center text-muted fw-800">#{{ $member_cards->firstItem() + $index }}</td>
                            <td>
                                <div class="highlight-label">
                                    {{ $item->member_card }}
                                    @if($isNew) <span class="badge-new-glow">NEW</span> @endif
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge-status {{ $item->is_sale == 0 ? 'status-sold' : 'status-available' }}">
                                    <i class="fas {{ $item->is_sale == 0 ? 'fa-hand-holding-dollar' : 'fa-check-circle' }} me-2"></i>
                                    {{ $item->is_sale == 0 ? 'SOLD' : 'AVAILABLE' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <button onclick="showCardDetails('{{ $item->member_card }}')" class="btn btn-sm btn-light border shadow-sm" style="width:38px; height:38px; border-radius:10px;"><i class="fas fa-eye text-indigo"></i></button>
                                    <a href="{{ url('member-cards/edit', encrypt($item->id)) }}" class="btn btn-sm btn-light border shadow-sm" style="width:38px; height:38px; border-radius:10px;"><i class="fas fa-pen text-muted"></i></a>
                                    <button onclick="confirmDelete('{{ url('member-cards/destroy', encrypt($item->id)) }}')" class="btn btn-sm btn-light border shadow-sm" style="width:38px; height:38px; border-radius:10px;"><i class="fas fa-trash-alt text-danger"></i></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="text-muted fw-bold">No results found for "{{ request('keyword') }}"</div>
                                @if(request('keyword'))
                                    <a href="{{ url('member-cards') }}" class="btn btn-sm btn-outline-primary mt-2 rounded-pill px-3">Show All Cards</a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-top d-flex justify-content-between align-items-center bg-white">
            <small class="text-muted fw-800 text-uppercase">Records: {{ $member_cards->firstItem() }} - {{ $member_cards->lastItem() }} of {{ $member_cards->total() }}</small>
            <div>{{ $member_cards->appends(request()->input())->links('paginitaion_backend') }}</div>
        </div>
    </div>
</div>

<script>
    // Preserved Logic
    function confirmDelete(url) {
        Swal.fire({
            title: 'Delete Asset?',
            text: "This will remove the card from the system.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            confirmButtonText: 'Yes, Delete',
            buttonsStyling: false,
            customClass: { confirmButton: 'btn btn-primary px-4 me-3', cancelButton: 'btn btn-label-secondary px-4' }
        }).then((result) => { if (result.isConfirmed) { window.location.href = url; } });
    }
</script>

@endsection