@extends('layouts.homePages')
@section('content')

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    :root {
        --brand-red: #CE181E;
        --brand-blue: #002663;
        --dark-bg: #0f172a;
        --card-bg: #ffffff;
        --text-main: #334155;
        --text-muted: #64748b;
        --border-light: #e2e8f0;
    }
    body { font-family: 'Plus Jakarta Sans', sans-serif !important; background-color: #f8fafc; color: var(--text-main); margin: 0; padding: 0; }
    .content-fluid-wrapper { width: 100% !important; max-width: 100% !important; padding: 1.5rem !important; animation: fadeIn 0.5s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }

    /* NAVBAR COMMAND BAR */
    .nav-command-bar { background: var(--card-bg); border: 1px solid var(--border-light); border-radius: 16px; padding: 15px 25px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03); }
    .nav-title { font-size: 1.2rem; font-weight: 800; color: var(--dark-bg); display: flex; align-items: center; gap: 12px; }
    .btn-back { background: var(--card-bg); color: var(--text-main); border: 1px solid var(--border-light); border-radius: 10px; font-weight: 700; padding: 10px 18px; font-size: 0.85rem; text-decoration: none; transition: 0.2s; display: flex; align-items: center; gap: 8px; }
    .btn-back:hover { background: #f1f5f9; color: var(--dark-bg); border-color: #cbd5e1; }

    /* COUNTER WIDGETS SYSTEM */
    .counter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 25px; }
    .widget-box { background: var(--card-bg); border: 1px solid var(--border-light); border-radius: 16px; padding: 20px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 15px rgba(0,0,0,0.01); }
    .widget-info p { margin: 0; font-size: 0.8rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
    .widget-info h2 { margin: 5px 0 0 0; font-size: 1.6rem; font-weight: 800; color: var(--dark-bg); font-family: 'JetBrains Mono', monospace; }
    .widget-icon-wrapper { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; }

    /* LEADERBOARD CARDS */
    .leaderboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem; margin-bottom: 25px; }
    .lb-card { background: var(--card-bg); border-radius: 16px; border: 1px solid var(--border-light); padding: 1.5rem; box-shadow: 0 4px 20px -2px rgba(0,0,0,0.03); }
    .lb-header { display: flex; align-items: center; gap: 15px; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px dashed var(--border-light); }
    .lb-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; }
    .lb-title { font-size: 1rem; font-weight: 800; color: var(--dark-bg); margin: 0; }
    .lb-subtitle { font-size: 0.75rem; color: var(--text-muted); font-weight: 700; }
    .lb-item { display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f8fafc; }
    .lb-item:last-child { border-bottom: none; padding-bottom: 0; }
    
    .lb-rank { width: 26px; height: 26px; border-radius: 6px; font-weight: 800; font-size: 0.75rem; display: flex; align-items: center; justify-content: center; margin-right: 12px; }
    .rank-1 { background: #fef08a; color: #b45309; }
    .rank-2 { background: #e2e8f0; color: #475569; }
    .rank-3 { background: #ffedd5; color: #9a3412; }
    .rank-other { background: #f1f5f9; color: var(--text-muted); }
    .lb-name { font-weight: 700; font-size: 0.9rem; color: var(--dark-bg); }
    .lb-stat-qty { font-weight: 800; font-size: 0.95rem; color: var(--brand-red); font-family: 'JetBrains Mono', monospace; text-align: right; }
    .lb-stat-rev { font-size: 0.75rem; font-weight: 700; color: #059669; }

    /* DASHBOARD SPLIT LAYOUT */
    .dashboard-split { display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 25px; }
    @media (max-width: 1200px) { .dashboard-split { grid-template-columns: 1fr; } }

    .chart-container { background: var(--card-bg); border-radius: 16px; border: 1px solid var(--border-light); padding: 1.5rem; box-shadow: 0 4px 20px -2px rgba(0,0,0,0.03); }
    .section-title { font-size: 1rem; font-weight: 800; color: var(--dark-bg); margin: 0 0 1.5rem 0; display: flex; align-items: center; gap: 10px; }
    
    .list-container { background: var(--card-bg); border-radius: 16px; border: 1px solid var(--border-light); box-shadow: 0 4px 20px -2px rgba(0,0,0,0.03); overflow: hidden; display: flex; flex-direction: column; max-height: 380px; }
    .list-header { padding: 1.2rem 1.5rem; border-bottom: 1px solid var(--border-light); }
    .list-body { overflow-y: auto; padding: 0 1rem 1rem 1rem; }
    .month-item { display: flex; justify-content: space-between; align-items: center; padding: 12px 10px; border-bottom: 1px dashed var(--border-light); }
    .m-date { font-weight: 700; color: var(--dark-bg); font-size: 0.9rem; }
    .m-qty { font-size: 0.75rem; font-weight: 700; color: #059669; background: #ecfdf5; padding: 2px 8px; border-radius: 4px; }
    .m-rev { font-weight: 800; font-family: 'JetBrains Mono', monospace; font-size: 0.95rem; }

    /* MODERN TABLE DESIGN */
    .table-card { background: var(--card-bg); border: 1px solid var(--border-light); border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03); }
    .modern-table { width: 100%; border-collapse: separate; border-spacing: 0; margin-top: 10px; }
    .modern-table th { background: #f8fafc; color: var(--text-muted); font-weight: 800; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; padding: 14px 20px; border-bottom: 1px solid var(--border-light); }
    .modern-table td { padding: 14px 20px; font-size: 0.9rem; font-weight: 600; border-bottom: 1px solid #f1f5f9; color: var(--dark-bg); }
    .modern-table tr:last-child td { border-bottom: none; }
    .badge-model { background: #eff6ff; color: #2563eb; font-weight: 700; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; border: 1px solid #bfdbfe; }
</style>

<div class="content-fluid-wrapper">
    
    <div class="nav-command-bar">
        <div class="nav-title">
            <div class="bg-dark text-white rounded-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-ranking-star text-white fs-6"></i>
            </div>
            Management Analytics & Performance Dashboard
        </div>
        <div class="d-flex align-items-center gap-3">
            <form method="GET" action="{{ route('sale-reports.dashboard') }}" class="d-flex align-items-center gap-2">
                <input type="month" name="month" class="form-control form-control-sm" style="border-radius: 8px; border: 1px solid #cbd5e1; font-weight: 600; padding: 6px 12px;" value="{{ request('month', date('Y-m')) }}">
                <button type="submit" class="btn btn-dark btn-sm fw-bold rounded-2 px-3 py-2">Filter</button>
                @if(request('month'))
                    <a href="{{ route('sale-reports.dashboard') }}" class="btn btn-light btn-sm border text-danger fw-bold py-2"><i class="fa-solid fa-xmark"></i></a>
                @endif
            </form>
            <div class="vr opacity-25"></div>
            <a href="{{ url('sale-reports') }}" class="btn-back">
                <i class="fa-solid fa-arrow-left"></i> Back to Database
            </a>
        </div>
    </div>

    {{-- 🌟 មុខងារច្រោះយកតែសាខាលក់ និងកាត់ Service ចេញ យកត្រឹម ៥ សាខា 🌟 --}}
    @php
        $activeBranches = collect($topBranches ?? [])->filter(function($b) {
            return stripos($b->branch_name, 'service') === false; // លាក់ពាក្យ Service
        })->take(5); // យកតែ ៥ ប៉ុណ្ណោះ
        
        $totalUnitsSoldInMonth = $activeBranches->sum('total_qty');
        $totalRevenueInMonth = $activeBranches->sum('total_revenue');
    @endphp

    <div class="counter-grid">
        <div class="widget-box">
            <div class="widget-info">
                <p>Active Branches</p>
                <h2>{{ $activeBranches->count() }} <span style="font-size: 0.8rem; color: var(--text-muted);">សាខាមានការលក់</span></h2>
            </div>
            <div class="widget-icon-wrapper" style="background: #eff6ff; color: #2563eb;"><i class="fa-solid fa-store"></i></div>
        </div>
        <div class="widget-box">
            <div class="widget-info">
                <p>Total Units Sold</p>
                <h2 style="color: var(--brand-red);">{{ $totalUnitsSoldInMonth }} <span style="font-size: 0.8rem; color: var(--text-muted);">Cars</span></h2>
            </div>
            <div class="widget-icon-wrapper" style="background: #fff5f5; color: var(--brand-red);"><i class="fa-solid fa-car-side"></i></div>
        </div>
        <div class="widget-box">
            <div class="widget-info">
                <p>Gross Revenue</p>
                <h2 style="color: #059669;">${{ number_format($totalRevenueInMonth, 2) }}</h2>
            </div>
            <div class="widget-icon-wrapper" style="background: #ecfdf5; color: #059669;"><i class="fa-solid fa-sack-dollar"></i></div>
        </div>
    </div>

    <div class="leaderboard-grid">
        <div class="lb-card">
            <div class="lb-header">
                <div class="lb-icon" style="background: #eff6ff; color: #2563eb;"><i class="fa-solid fa-building-flag"></i></div>
                <div>
                    <h3 class="lb-title">Top Branches</h3>
                    <span class="lb-subtitle">សាខាលក់ដាច់បំផុត (Top 5)</span>
                </div>
            </div>
            <div class="lb-list">
                @forelse($activeBranches as $index => $branch)
                    <div class="lb-item">
                        <div class="d-flex align-items-center">
                            <div class="lb-rank {{ $index < 3 ? 'rank-'.($index+1) : 'rank-other' }}"><i class="fa-solid fa-trophy" style="font-size: 0.65rem;"></i></div>
                            <div class="lb-name">{{ $branch->branch_name }}</div>
                        </div>
                        <div class="lb-stat-box">
                            <div class="lb-stat-qty">{{ $branch->total_qty }} <span style="font-size:0.7rem; color:#94a3b8;">Units</span></div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted small py-4 fw-bold">មិនមានទិន្នន័យទេ</div>
                @endforelse
            </div>
        </div>

        <div class="lb-card">
            <div class="lb-header">
                <div class="lb-icon" style="background: #f3e8ff; color: #9333ea;"><i class="fa-solid fa-map-location-dot"></i></div>
                <div>
                    <h3 class="lb-title">Top Provinces</h3>
                    <span class="lb-subtitle">ខេត្តទិញច្រើនជាងគេ (Top 5)</span>
                </div>
            </div>
            <div class="lb-list">
                @forelse($topProvinces ?? [] as $index => $prov)
                    <div class="lb-item">
                        <div class="d-flex align-items-center">
                            <div class="lb-rank {{ $index < 3 ? 'rank-'.($index+1) : 'rank-other' }}">{{ $index + 1 }}</div>
                            <div class="lb-name">{{ $prov->province_name ?? 'N/A' }}</div>
                        </div>
                        <div class="lb-stat-box">
                            <div class="lb-stat-qty" style="color: #9333ea;">{{ $prov->total_qty }} <span style="font-size:0.7rem; color:#94a3b8;">Units</span></div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted small py-4 fw-bold">មិនមានទិន្នន័យទេ</div>
                @endforelse
            </div>
        </div>

        <div class="lb-card">
            <div class="lb-header">
                <div class="lb-icon" style="background: #ccfbf1; color: #0d9488;"><i class="fa-solid fa-users-viewfinder"></i></div>
                <div>
                    <h3 class="lb-title">Customer Sources</h3>
                    <span class="lb-subtitle">ប្រភពអតិថិជនមកពីណា?</span>
                </div>
            </div>
            <div class="lb-list">
                @forelse($topSources ?? [] as $index => $source)
                    <div class="lb-item">
                        <div class="d-flex align-items-center">
                            <div class="lb-rank {{ $index < 3 ? 'rank-'.($index+1) : 'rank-other' }}">{{ $index + 1 }}</div>
                            <div class="lb-name">{{ $source->source_name ?? 'N/A' }}</div>
                        </div>
                        <div class="lb-stat-box">
                            <div class="lb-stat-qty" style="color: #0d9488;">{{ $source->total_qty }} <span style="font-size:0.7rem; color:#94a3b8;">Units</span></div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted small py-4 fw-bold">មិនមានទិន្នន័យទេ</div>
                @endforelse
            </div>
        </div>
        
        <div class="lb-card">
            <div class="lb-header">
                <div class="lb-icon" style="background: #ecfdf5; color: #059669;"><i class="fa-solid fa-user-tie"></i></div>
                <div>
                    <h3 class="lb-title">Top Sales Persons</h3>
                    <span class="lb-subtitle">អ្នកលក់ឆ្នើម (Top 5)</span>
                </div>
            </div>
            <div class="lb-list">
                @forelse(collect($topSalePersons ?? [])->take(5) as $index => $person)
                    <div class="lb-item">
                        <div class="d-flex align-items-center">
                            <div class="lb-rank {{ $index < 3 ? 'rank-'.($index+1) : 'rank-other' }}"><i class="fa-solid fa-trophy" style="font-size: 0.65rem;"></i></div>
                            <div class="lb-name">{{ $person->sale_person ?? 'Unknown' }}</div>
                        </div>
                        <div class="lb-stat-box">
                            <div class="lb-stat-qty" style="color: #059669;">{{ $person->total_qty }} <span style="font-size:0.7rem; color:#94a3b8;">Units</span></div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted small py-4 fw-bold">មិនមានទិន្នន័យទេ</div>
                @endforelse
            </div>
        </div>
        
        <div class="lb-card">
            <div class="lb-header">
                <div class="lb-icon" style="background: #fffbeb; color: #d97706;"><i class="fa-solid fa-car"></i></div>
                <div>
                    <h3 class="lb-title">Top Car Models</h3>
                    <span class="lb-subtitle">ម៉ូដែលពេញនិយម (Top 5)</span>
                </div>
            </div>
            <div class="lb-list">
                @forelse(collect($topModels ?? [])->take(5) as $index => $model)
                    <div class="lb-item">
                        <div class="d-flex align-items-center">
                            <div class="lb-rank {{ $index < 3 ? 'rank-'.($index+1) : 'rank-other' }}"><i class="fa-solid fa-trophy" style="font-size: 0.65rem;"></i></div>
                            <div class="lb-name">{{ $model->model_name ?? 'Unknown' }}</div>
                        </div>
                        <div class="lb-stat-box">
                            <div class="lb-stat-qty" style="color: #d97706;">{{ $model->total_qty }} <span style="font-size:0.7rem; color:#94a3b8;">Units</span></div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted small py-4 fw-bold">មិនមានទិន្នន័យទេ</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="dashboard-split">
        <div class="chart-container">
            <h3 class="section-title">
                <i class="fa-solid fa-chart-column" style="color: var(--brand-blue);"></i> Sales Trend Overview (និន្នាការលក់)
            </h3>
            <div style="height: 300px; width: 100%;">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <div class="list-container">
            <div class="list-header">
                <h3 class="section-title m-0">
                    <i class="fa-solid fa-clock-rotate-left text-danger"></i> Monthly History
                </h3>
            </div>
            <div class="list-body pt-2">
                @forelse($monthlySales ?? [] as $m)
                    <div class="month-item">
                        <div>
                            <div class="m-date">
                                <i class="fa-regular fa-calendar me-1 text-muted"></i> 
                                {{ \Carbon\Carbon::createFromFormat('Y-m', $m->month_year)->format('M Y') }}
                            </div>
                            <div class="mt-1">
                                <span class="m-qty">{{ $m->total_qty }} Cars</span>
                            </div>
                        </div>
                        <div class="m-rev text-primary">
                            ${{ number_format($m->total_revenue, 2) }}
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted fw-bold">
                        <i class="fa-solid fa-folder-open fs-2 mb-3 d-block opacity-25"></i> 
                        មិនទាន់មានទិន្នន័យ...
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="table-card">
        <h3 class="section-title mb-3">
            <i class="fa-solid fa-list-check text-success"></i> Top 5 Branches Breakdown
        </h3>
        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>សាខា (Top 5 Branches)</th>
                        <th>ម៉ូដែលលក់ដាច់ប្រចាំសាខា (Top Model)</th>
                        <th class="text-center">ចំនួនលក់ក្នុងខែ (Units Sold)</th>
                        <th class="text-end">ទឹកប្រាក់សរុប (Revenue)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activeBranches as $b)
                        @php
                            $topModelBadge = collect($topModels)->first()->model_name ?? 'N/A';
                        @endphp
                        <tr>
                            <td>
                                <div class="fw-800 d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-location-dot text-danger"></i>
                                    {{ $b->branch_name }}
                                </div>
                            </td>
                            <td>
                                <span class="badge-model"><i class="fa-solid fa-car me-1"></i> {{ $topModelBadge }}</span>
                            </td>
                            <td class="text-center fw-800 text-danger" style="font-family: 'JetBrains Mono'; font-size: 1rem;">
                                {{ $b->total_qty }} <span style="font-size: 0.75rem; color: var(--text-muted);">Units</span>
                            </td>
                            <td class="text-end fw-800 text-success" style="font-family: 'JetBrains Mono'; font-size: 1rem;">
                                ${{ number_format($b->total_revenue, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4 fw-bold">មិនមានទិន្នន័យសាខាដែលមានការលក់ក្នុងខែនេះទេ</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@php
    $chartLabels = [];
    $chartDataQty = [];
    $chartDataRev = [];
    
    if(isset($monthlySales)) {
        $reversedSales = collect($monthlySales)->reverse();
        foreach($reversedSales as $m) {
            $chartLabels[] = \Carbon\Carbon::createFromFormat('Y-m', $m->month_year)->format('M-Y');
            $chartDataQty[] = $m->total_qty;
            $chartDataRev[] = $m->total_revenue;
        }
    }
@endphp

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        const labels = {!! json_encode($chartLabels) !!};
        const dataQty = {!! json_encode($chartDataQty) !!};
        const dataRev = {!! json_encode($chartDataRev) !!};

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Units Sold (គ្រឿង)',
                        data: dataQty,
                        backgroundColor: 'rgba(0, 38, 99, 0.85)',
                        borderRadius: 6,
                        yAxisID: 'y',
                        order: 2
                    },
                    {
                        label: 'Revenue ($)',
                        data: dataRev,
                        type: 'line',
                        borderColor: '#CE181E',
                        backgroundColor: '#CE181E',
                        borderWidth: 3,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#CE181E',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        tension: 0.3,
                        yAxisID: 'y1',
                        order: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 8, font: { family: 'Plus Jakarta Sans', weight: 'bold' } } },
                    tooltip: { backgroundColor: 'rgba(15, 23, 42, 0.9)', titleFont: { size: 13, family: 'Plus Jakarta Sans' }, bodyFont: { size: 13, family: 'Plus Jakarta Sans' }, padding: 12, cornerRadius: 8 }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: { type: 'linear', position: 'left', title: { display: true, text: 'Cars Sold', font: { weight: 'bold' } }, beginAtZero: true },
                    y1: { type: 'linear', position: 'right', title: { display: true, text: 'Revenue (USD)', font: { weight: 'bold' } }, grid: { drawOnChartArea: false }, beginAtZero: true }
                }
            }
        });
    });
</script>

@endsection