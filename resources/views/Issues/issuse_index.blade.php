@extends('layouts.homePages')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Noto+Sans+Khmer:wght@400;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --brand-indigo: #6366f1;
        --exec-dark: #0f172a;
        --bg-body: #f8fafc;
    }

    body, .container-full { 
        font-family: 'Plus Jakarta Sans', 'Noto Sans Khmer', sans-serif !important; 
        background-color: var(--bg-body); 
        color: #334155;
        font-size: 0.82rem;
    }

    .container-full {
        width: 100% !important;
        max-width: 100% !important;
        padding: 20px 40px !important;
    }

    /* ✨ ANIMATIONS */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); filter: blur(4px); }
        to { opacity: 1; transform: translateY(0); filter: blur(0); }
    }
    .animate-card { animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; }
    .delay-1 { animation-delay: 0.1s; }
    .delay-2 { animation-delay: 0.2s; }
    .delay-3 { animation-delay: 0.3s; }
    .delay-4 { animation-delay: 0.4s; }

    /* 📊 ANALYTICS GRID */
    .analysis-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 30px; }
    .analysis-card { background: #fff; border-radius: 22px; padding: 22px; border: 1px solid rgba(226, 232, 240, 0.7); display: flex; align-items: center; gap: 18px; transition: 0.4s ease; }
    .analysis-card:hover { transform: translateY(-6px); box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05); }
    .analysis-icon { width: 54px; height: 54px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }

    /* 📋 MASTER CARD */
    .card-main { border: none; border-radius: 26px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.04); background: #fff; overflow: hidden; animation: fadeInUp 0.7s ease-out 0.4s forwards; opacity: 0; }
    .card-header-gradient { background: var(--exec-dark) !important; padding: 22px 35px; }
    
    .header-search-group { position: relative; flex-grow: 1; max-width: 380px; }
    .header-search-input { width: 100%; background: rgba(255, 255, 255, 0.07); border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 14px; padding: 11px 15px 11px 42px; color: #fff; font-size: 0.78rem; transition: 0.3s; }
    .header-search-input:focus { background: #fff; color: #000; outline: none; }
    .header-search-group i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #64748b; }

    /* 📏 TABLE SYSTEM */
    .table-custom-wrapper { width: 100%; overflow-x: auto; padding: 15px; }
    #ticketTable { width: 100% !important; border-spacing: 0 10px; border-collapse: separate; }
    #ticketTable thead th { background: transparent; text-transform: uppercase; font-size: 0.62rem; font-weight: 800; letter-spacing: 1px; color: #94a3b8; padding: 12px 15px; border: none; text-align: center; }
    #ticketTable tbody tr { background: #fff; transition: 0.3s; }
    #ticketTable tbody td { padding: 15px; vertical-align: middle; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; font-size: 0.78rem; }
    #ticketTable tbody td:first-child { border-left: 1px solid #f1f5f9; border-top-left-radius: 16px; border-bottom-left-radius: 16px; }
    #ticketTable tbody td:last-child { border-right: 1px solid #f1f5f9; border-top-right-radius: 16px; border-bottom-right-radius: 16px; }

    /* 🏷️ INDICATORS */
    .kh-font { font-family: 'Noto Sans Khmer', sans-serif; }
    .kh-sub { font-family: 'Noto Sans Khmer'; font-size: 0.6rem; color: #94a3b8; font-weight: 400; display: block; margin-top: 2px; }
    
    .status-pill { padding: 6px 12px; border-radius: 8px; font-weight: 800; font-size: 0.6rem; display: inline-flex; align-items: center; gap: 6px; text-transform: uppercase; }
    .st-open { background: #fee2e2; color: #dc2626; }
    .st-inprogress { background: #fef3c7; color: #d97706; }
    .st-resolved { background: #d1fae5; color: #059669; }
    .st-closed { background: #f1f5f9; color: #475569; }

    .priority-badge { font-size: 0.6rem; font-weight: 700; padding: 4px 8px; border-radius: 6px; display: inline-flex; align-items: center; gap: 5px; border: 1px solid transparent; }
    .p-urgent { border-color: #fee2e2; color: #dc2626; }
    .p-high { border-color: #ffedd5; color: #ea580c; }
    .p-medium { border-color: #e0e7ff; color: #4f46e5; }
    .p-low { border-color: #f1f5f9; color: #64748b; }

    .btn-action-official { width: 34px; height: 34px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; border: 1.2px solid #f1f5f9; background: #fff; color: #64748b; transition: 0.3s; }
    .btn-action-official:hover { transform: translateY(-3px); box-shadow: 0 4px 10px rgba(0,0,0,0.08); }
    
    /* Avatar Style */
    .name-avatar { width: 28px; height: 28px; border-radius: 8px; background: #f1f5f9; color: var(--brand-indigo); display: inline-flex; align-items: center; justify-content: center; font-size: 0.75rem; margin-right: 8px; }
</style>

<div class="container-full">
    <div class="analysis-grid">
        @php
            $cards = [
                ['label' => 'សរុប (TOTAL)', 'val' => $stats['total'], 'icon' => 'fa-server', 'bg' => '#eef2ff', 'clr' => '#6366f1'],
                ['label' => 'កំពុងបើក (OPEN)', 'val' => $stats['open'], 'icon' => 'fa-triangle-exclamation', 'bg' => '#fff1f2', 'clr' => '#e11d48'],
                ['label' => 'ដោះស្រាយរួច (RESOLVED)', 'val' => $stats['resolved'], 'icon' => 'fa-circle-check', 'bg' => '#ecfdf5', 'clr' => '#059669'],
                ['label' => 'បន្ទាន់ (URGENT)', 'val' => $stats['urgent'], 'icon' => 'fa-fire-flame-curved', 'bg' => '#fff7ed', 'clr' => '#f59e0b'],
            ];
        @endphp
        @foreach($cards as $index => $card)
            <div class="analysis-card animate-card delay-{{ $index + 1 }}">
                <div class="analysis-icon" style="background:{{ $card['bg'] }}; color:{{ $card['clr'] }}"><i class="fa-solid {{ $card['icon'] }}"></i></div>
                <div>
                    <small class="text-muted fw-800 kh-font" style="font-size:0.55rem">{{ $card['label'] }}</small>
                    <div class="h4 fw-800 m-0" style="color:{{ $card['clr'] }}">{{ $card['val'] ?? 0 }}</div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card-main">
        <div class="card-header-gradient">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center flex-grow-1 gap-4">
                    <div>
                        <h5 class="text-white fw-800 mb-0 kh-font" style="letter-spacing: 1px;"><i class="fa-solid fa-database me-2 opacity-50"></i>បញ្ជីប្រតិបត្តិការសំបុត្រ</h5>
                        <p class="text-white-50 mb-0 small" style="font-size: 0.6rem;">OPERATIONS PROTOCOL v2.5</p>
                    </div>
                    <div class="header-search-group d-none d-md-block">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="ticketSearch" class="header-search-input" placeholder="ស្វែងរកតាមលេខសម្គាល់ ឬឈ្មោះ...">
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('issues.export') }}" class="btn btn-sm btn-outline-light fw-800 rounded-pill px-3 kh-font"><i class="fa-solid fa-file-export me-1"></i> EXPORT</a>
                    <a href="{{ route('issues.add') }}" class="btn btn-sm btn-primary fw-800 rounded-pill px-4 shadow-lg border-0 kh-font" style="background: var(--brand-indigo);"><i class="fa-solid fa-plus-circle me-1"></i> បង្កើតថ្មី</a>
                </div>
            </div>
        </div>

        <div class="table-custom-wrapper">
            <table id="ticketTable" class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 10%;">សម្គាល់ <span class="kh-sub">ID</span></th>
                        <th style="width: 20%; text-align: left;">កម្មវត្ថុ <span class="kh-sub">SUBJECT & CAT</span></th>
                        <th style="width: 12%;">ផ្នែក <span class="kh-sub">DEPT</span></th>
                        <th style="width: 15%;">បរិយាយ <span class="kh-sub">DESCRIPTION</span></th>
                        <th style="width: 15%;">អ្នករាយការណ៍ <span class="kh-sub">REPORTER</span></th>
                        <th style="width: 10%;">ស្ថានភាព <span class="kh-sub">STATUS</span></th>
                        <th style="width: 10%;">កំណត់សម្គាល់ <span class="kh-sub">REMARK</span></th>
                        <th style="width: 8%;">ចាត់ចែង <span class="kh-sub">ACTION</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($issues as $issue)
                        @php $statusKey = strtolower(str_replace(' ', '', $issue->status)); @endphp
                        <tr>
                            <td class="text-center"><span style="font-family: 'JetBrains Mono'; font-weight: 700; color: #6366f1;">#{{ $issue->issue_id }}</span></td>
                            <td style="text-align: left;">
                                <div class="fw-800 text-dark mb-0 text-truncate" style="max-width: 180px;"><i class="fa-solid fa-file-lines me-2 text-muted"></i>{{ $issue->subject }}</div>
                                <span class="kh-sub" style="font-size: 0.55rem;"><i class="fa-solid fa-tag me-1"></i>{{ $issue->category }}</span>
                            </td>
                            <td class="text-center small fw-700 text-muted"><i class="fa-solid fa-building-user me-1 text-muted opacity-50"></i>{{ strtoupper($issue->department) }}</td>
                            <td>
                                <div class="text-muted text-truncate" style="max-width: 150px; font-size: 0.7rem;" title="{{ $issue->description }}"><i class="fa-solid fa-align-left me-1 opacity-50"></i>{{ $issue->description }}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="name-avatar fw-800">{{ substr($issue->reported_by, 0, 1) }}</div>
                                    <div>
                                        <div class="fw-700 text-dark small">{{ $issue->reported_by }}</div>
                                        <div class="priority-badge p-{{ strtolower($issue->priority) }} mt-1">{{ strtoupper($issue->priority) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="status-pill st-{{ $statusKey }}"><i class="fa-solid fa-circle-dot me-1" style="font-size: 0.4rem;"></i>{{ $issue->status }}</span>
                            </td>
                            <td>
                                <div class="text-indigo-600 fw-600 text-truncate" style="max-width: 130px; font-size: 0.7rem;"><i class="fa-solid fa-comment-dots me-1 opacity-50"></i>{{ $issue->admin_remark ?? '---' }}</div>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('issues.view', $issue->id) }}" class="btn-action-official" title="មើល" style="color:#10b981;"><i class="fa-solid fa-eye"></i></a>
                                    @if($issue->status !== 'Closed')
                                        <a href="{{ route('issues.edit', $issue->id) }}" class="btn-action-official" title="កែ" style="color:#6366f1;"><i class="fa-solid fa-pen-to-square"></i></a>
                                        <button onclick="deleteTicket('{{ $issue->id }}', '{{ $issue->issue_id }}')" class="btn-action-official border-0" title="លុប" style="color:#ef4444;"><i class="fa-solid fa-trash-can"></i></button>
                                        <form action="{{ route('issues.destroy', $issue->id) }}" method="POST" id="del-{{ $issue->id }}" class="d-none">@csrf @method('DELETE')</form>
                                    @else
                                        <span class="btn-action-official bg-light border-0"><i class="fa-solid fa-lock text-muted opacity-40"></i></span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center p-5 text-muted kh-font">មិនមានទិន្នន័យត្រូវបានរកឃើញទេ។</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        var table = $('#ticketTable').DataTable({
            "dom": 'rt<"d-flex justify-content-between align-items-center p-4"ip>',
            "pageLength": 10,
            "autoWidth": false,
            "order": [[0, "desc"]],
            "language": {
                "paginate": { "previous": "<i class='fa-solid fa-chevron-left'></i>", "next": "<i class='fa-solid fa-chevron-right'></i>" },
                "info": "បង្ហាញពី <span class='fw-800 text-dark'>_START_ ដល់ _END_</span> នៃសរុប <span class='fw-800 text-dark'>_TOTAL_</span>"
            }
        });
        $('#ticketSearch').on('input', function() { table.search(this.value).draw(); });
    });

    function deleteTicket(id, ticketId) {
        Swal.fire({
            title: 'លុបទិន្នន័យ?',
            html: `សំបុត្រសម្គាល់៖ <b class="text-danger">#${ticketId}</b> នឹងត្រូវលុប!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0f172a',
            confirmButtonText: 'យល់ព្រម',
            cancelButtonText: 'បោះបង់',
            borderRadius: '20px'
        }).then((result) => { if (result.isConfirmed) { document.getElementById('del-' + id).submit(); } });
    }
</script>
@endsection