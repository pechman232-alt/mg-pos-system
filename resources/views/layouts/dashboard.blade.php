@extends('layouts.homePages')

@section('content')

@php
    $user = auth('users')->user();
    $roleId = (int)($user->role_id ?? 0);
    
    // ✅ កំណត់សិទ្ធិ
    $isAdmin = in_array($roleId, [1, 2, 6]);
    $isManager = in_array($roleId, [3, 4, 5, 7, 8]);
    $isStaff = ($roleId == 9);

    // ទាញឈ្មោះសាខាបង្ហាញ
    $currentBranchName = 'All Branches';
    if (!$isAdmin && isset($branches)) {
        $myBranch = collect($branches)->firstWhere('id', $user->branch_id);
        $currentBranchName = $myBranch ? str_replace('MG ', '', $myBranch->branch_name) : 'My Branch';
    }
@endphp

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Noto+Sans+Khmer:wght@600;700;800&family=JetBrains+Mono:wght@700;800&display=swap" rel="stylesheet">
<script src="https://unpkg.com/@phosphor-icons/web"></script>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

<style>
/* ================= Base Optimization & Fonts ================= */
body, main, #app, .main-content { 
    font-family: 'Plus Jakarta Sans', sans-serif !important; 
    background-color: #f8fafc !important; 
    margin: 0 !important; 
    padding: 0 !important; 
}

/* ================= Custom Animations ================= */
@keyframes fadeInUp {
    from { opacity: 0; transform: translate3d(0, 30px, 0); }
    to { opacity: 1; transform: translate3d(0, 0, 0); }
}

.animate-fade-up {
    animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    opacity: 0; /* លាក់សិនមុនពេល Animation ដើរ */
    will-change: opacity, transform; /* ប្រាប់ Browser ឱ្យត្រៀម Render ឱ្យលឿន */
}

/* សម្រាប់ឱ្យចលនាលោតចេញមកម្តងមួយៗ (Staggering) */
.delay-100 { animation-delay: 100ms; }
.delay-200 { animation-delay: 200ms; }
.delay-300 { animation-delay: 300ms; }

/* ================= Component Styling ================= */
.dashboard-header { 
    background: rgba(255, 255, 255, 0.9); 
    backdrop-filter: blur(10px); /* ធ្វើឱ្យ Header ថ្លាៗបែបទំនើប */
    border-bottom: 1px solid #f1f5f9; 
    padding: 1rem 1.5rem; 
    display: flex; justify-content: space-between; align-items: center; 
    position: sticky; top: 0; z-index: 50; 
    box-shadow: 0 4px 20px -2px rgba(0,0,0,0.03); 
}

.chart-card { 
    background: #ffffff; 
    border: 1px solid #f1f5f9; 
    border-radius: 1.25rem; /* 20px */
    padding: 1.5rem; 
    box-shadow: 0 4px 15px rgba(0,0,0,0.02); 
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); 
    height: 100%; 
}
.chart-card:hover { 
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01); 
    transform: translateY(-4px); 
}

/* Table Clean Styling */
.performance-table thead th { 
    font-family: 'Noto Sans Khmer', sans-serif; 
    font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;
    color: #64748b; padding: 14px 12px; border-bottom: 2px solid #f1f5f9; 
}
.performance-table tbody td { 
    padding: 14px 12px; font-size: 13px; font-weight: 500;
    border-bottom: 1px dashed #f1f5f9; transition: background 0.2s ease;
}
.performance-table tbody tr:hover td { background-color: #f8fafc; }

/* Hover Effect សម្រាប់ List Data */
.list-item-hover {
    transition: all 0.3s ease;
}
.list-item-hover:hover {
    background-color: #f8fafc;
    transform: translateX(6px); /* រុញទៅស្តាំបន្តិចពេលដាក់ Mouse លើ */
}
</style>

<div class="w-full flex flex-col flex-grow h-full m-0 p-0">

    <!-- 🌟 HEADER SECTION 🌟 -->
    <div class="dashboard-header w-full flex-wrap gap-4 m-0">
        <div class="animate-fade-up">
            <h1 class="text-xl md:text-2xl font-black text-slate-800 tracking-tight italic m-0 leading-none">
                {{ $isStaff ? 'My Sales Performance' : 'Sales Performance Overview' }}
            </h1>
            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[2px] m-0 mt-1 font-khmer" style="font-family: 'Noto Sans Khmer'">
                @if($isAdmin) សង្ខេបប្រសិទ្ធភាពលក់គ្រប់សាខា @elseif($isManager) សង្ខេបប្រសិទ្ធភាពលក់ - {{ $currentBranchName }} @else ការតាមដានលទ្ធផលការងារបុគ្គល @endif
            </p>
        </div>

        <div class="flex items-center gap-3 animate-fade-up delay-100">
            <form method="GET" action="" id="filterForm" class="flex items-center gap-2 bg-slate-50 hover:bg-slate-100 transition-colors border border-slate-200 rounded-xl px-4 py-2 shadow-sm">
                <i class="ph-bold ph-funnel text-slate-400 text-base"></i>
                
                {{-- 📍 ផ្នែកសាខា --}}
                @if($isAdmin)
                    <select name="branch" onchange="this.form.submit()" class="bg-transparent text-xs font-extrabold text-slate-700 outline-none cursor-pointer">
                        <option value="">All Branches</option>
                        @if(isset($branches))
                            @foreach($branches as $b)
                                <option value="{{ $b->branch_name }}" {{ request('branch') == $b->branch_name ? 'selected' : '' }}>
                                    {{ str_replace('MG ', '', $b->branch_name) }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <div class="w-[2px] h-3 bg-slate-300 mx-1 rounded-full"></div>
                @else
                    <span class="text-[11px] font-black text-indigo-600 uppercase px-2 border-r-2 border-slate-300">{{ $currentBranchName }}</span>
                @endif

                <select name="year" onchange="this.form.submit()" class="bg-transparent text-xs font-extrabold text-slate-700 outline-none ms-1 cursor-pointer">
                    @for($i = date('Y'); $i >= 2022; $i--)
                        <option value="{{ $i }}" {{ request('year', date('Y')) == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
                <div class="w-[2px] h-3 bg-slate-300 mx-1 rounded-full"></div>
                <select name="month" onchange="this.form.submit()" class="bg-transparent text-xs font-extrabold text-slate-700 outline-none cursor-pointer">
                    <option value="">All Months</option>
                    @foreach(range(1, 12) as $m)
                        <option value="{{ sprintf('%02d', $m) }}" {{ request('month') == sprintf('%02d', $m) ? 'selected' : '' }}>
                            {{ date('M', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endforeach
                </select>
            </form>

            <!-- Live Clock -->
            <div class="hidden md:flex items-center gap-3 bg-white border border-slate-200 shadow-sm rounded-xl px-4 py-2">
                <div id="live-clock" class="text-sm font-black text-slate-700 font-['JetBrains_Mono'] leading-none">00:00:00</div>
                <div class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-pulse shadow-[0_0_8px_rgba(16,185,129,0.6)]"></div>
            </div>
        </div>
    </div>

    <!-- 🌟 MAIN CONTENT 🌟 -->
    <div class="w-full p-4 md:p-6 mt-0 flex-grow flex flex-col gap-6">
        
        {{-- Row 1: Source & Models --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 animate-fade-up delay-100">
            <!-- Card 1 -->
            <div class="chart-card border-t-[5px] border-t-emerald-500">
                <div class="flex justify-between items-center mb-5">
                    <h5 class="text-xs font-black text-emerald-600 uppercase tracking-widest">Top Lead Sources</h5>
                    <span class="bg-emerald-50 text-emerald-600 border border-emerald-100 text-[10px] font-bold px-2.5 py-1 rounded-md shadow-sm">{{ $isStaff ? 'MY SOURCES' : 'OVERALL' }}</span>
                </div>
                <div class="space-y-2">
                    @forelse($sourceStats ?? [] as $index => $source)
                    <div class="flex justify-between items-center p-2 rounded-xl list-item-hover" style="animation: fadeInUp 0.5s ease forwards; animation-delay: {{ $index * 50 }}ms; opacity: 0;">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-slate-50 border border-slate-100 shadow-sm flex items-center justify-center text-lg">
                                @if(stripos($source->source, 'Facebook') !== false) <i class="ph-fill ph-facebook-logo text-blue-600"></i>
                                @elseif(stripos($source->source, 'Telegram') !== false) <i class="ph-fill ph-telegram-logo text-sky-500"></i>
                                @elseif(stripos($source->source, 'TikTok') !== false) <i class="ph-fill ph-tiktok-logo text-slate-900"></i>
                                @elseif(stripos($source->source, 'Walk-in') !== false) <i class="ph-fill ph-footprints text-emerald-500"></i>
                                @else <i class="ph-fill ph-share-network text-slate-400"></i> @endif
                            </div>
                            <span class="text-[13px] font-bold text-slate-700">{{ $source->source }}</span>
                        </div>
                        <span class="text-base font-black text-slate-800 bg-slate-100 px-3 py-1 rounded-lg">{{ $source->total }}</span>
                    </div>
                    @empty
                    <div class="text-center text-slate-400 text-xs py-6 flex flex-col items-center gap-2">
                        <i class="ph-duotone ph-ghost text-3xl"></i>
                        <span>No source data available.</span>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Card 2 -->
            <div class="chart-card border-t-[5px] border-t-violet-500">
                <div class="flex justify-between items-center mb-5">
                    <h5 class="text-xs font-black text-violet-600 uppercase tracking-widest">Best Selling Models</h5>
                    <span class="bg-violet-50 text-violet-600 border border-violet-100 text-[10px] font-bold px-2.5 py-1 rounded-md shadow-sm">TOP 5</span>
                </div>
                <div class="space-y-2">
                    @forelse($topModelStats ?? [] as $index => $model)
                    <div class="flex justify-between items-center p-2 rounded-xl list-item-hover" style="animation: fadeInUp 0.5s ease forwards; animation-delay: {{ $index * 50 }}ms; opacity: 0;">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-xs font-black shadow-sm border border-slate-100
                                {{ $index == 0 ? 'bg-gradient-to-br from-violet-500 to-indigo-600 text-white border-none' : 'bg-slate-50 text-slate-500' }}">
                                #{{ $index + 1 }}
                            </div>
                            <span class="text-[13px] font-bold text-slate-700">{{ $model->model }}</span>
                        </div>
                        <div class="flex items-baseline gap-1 bg-slate-100 px-3 py-1 rounded-lg">
                            <span class="text-base font-black text-slate-800">{{ $model->total }}</span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase">Units</span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-slate-400 text-xs py-6 flex flex-col items-center gap-2">
                        <i class="ph-duotone ph-car-profile text-3xl"></i>
                        <span>No model data available.</span>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Row 2: Charts --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-up delay-200">
            <div class="chart-card border-t-[5px] border-t-blue-500 flex flex-col">
                <h5 class="text-[11px] font-black text-blue-600 uppercase tracking-widest mb-4">Location Statistics</h5>
                <div class="h-[220px] w-full flex-grow"><canvas id="provinceChart"></canvas></div>
            </div>
            <div class="chart-card border-t-[5px] border-t-amber-500 flex flex-col">
                <h5 class="text-[11px] font-black text-amber-600 uppercase tracking-widest mb-4">Model Distribution</h5>
                <div class="h-[220px] w-full flex-grow"><canvas id="modelChart"></canvas></div>
            </div>
            <div class="chart-card border-t-[5px] border-t-pink-500 flex flex-col">
                <h5 class="text-[11px] font-black text-pink-600 uppercase tracking-widest mb-4">{{ $isStaff ? 'My Daily Performance' : 'Top Salespeople' }}</h5>
                <div class="h-[220px] w-full flex-grow"><canvas id="salesChart"></canvas></div>
            </div>
        </div>

        {{-- Row 3: Table Performance --}}
        <div class="chart-card border-t-[5px] border-t-indigo-600 mb-4 animate-fade-up delay-300">
            <div class="flex justify-between items-center mb-6 border-b border-slate-100 pb-4">
                <div>
                    <h5 class="text-sm font-black text-indigo-600 uppercase tracking-widest">
                        {{ $isStaff ? 'My Activity Progress' : 'Salesperson Update Progress' }}
                    </h5>
                    <p class="text-[11px] text-slate-500 font-bold mt-1 font-khmer" style="font-family: 'Noto Sans Khmer'">
                        {{ $isStaff ? 'តារាងតាមដានសកម្មភាពផ្ទាល់ខ្លួន' : 'តារាងតាមដានសកម្មភាពបច្ចុប្បន្នភាពរបស់អ្នកលក់ (%)' }}
                    </p>
                </div>
                <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border border-indigo-100 px-4 py-2 rounded-xl text-indigo-700 font-black text-[10px] shadow-sm">
                    @if($isAdmin) DIRECTOR VIEW @elseif($isManager) MANAGER VIEW @else PERSONAL VIEW @endif
                </div>
            </div>

            <div class="overflow-x-auto rounded-xl border border-slate-100">
                <table class="w-full performance-table text-left whitespace-nowrap bg-white">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="font-khmer">អ្នកលក់ (Consultant)</th>
                            <th class="font-khmer">សាខា (Branch)</th>
                            <th class="text-center font-khmer">ថ្ងៃចុះឈ្មោះ (Reg Date)</th>
                            <th class="text-center font-khmer">ថ្ងៃតាមដាន (Next Action)</th>
                            <th class="text-center font-khmer">ដំណើរការ (%)</th>
                            <th class="w-1/3 font-khmer px-6">កម្រិតសម្រេចបាន (Progress Bar)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salesActivityProgress ?? [] as $item)
                        <tr class="{{ ($isStaff && $item->name == $user->full_name) ? 'bg-indigo-50/30' : '' }}">
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-xs font-bold shadow-md" style="background-color: {{ $item->color ?? '#4f46e5' }};">
                                        {{ substr($item->name, 0, 1) }}
                                    </div>
                                    <span class="font-bold text-slate-800">
                                        {{ $item->name }}
                                        @if($isStaff && $item->name == $user->full_name) <span class="text-[9px] bg-indigo-600 text-white px-2 py-0.5 rounded-md ms-2 shadow-sm">YOU</span> @endif
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="text-[11px] font-bold text-slate-600 bg-slate-100 border border-slate-200 px-2.5 py-1 rounded-md">
                                    {{ str_replace('MG ', '', $item->branch_name ?? 'N/A') }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="text-xs font-bold text-slate-500">
                                    {{ isset($item->created_at) ? \Carbon\Carbon::parse($item->created_at)->format('d M Y') : 'N/A' }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if(!empty($item->remind_date))
                                    @php
                                        $rDate = \Carbon\Carbon::parse($item->remind_date);
                                        $isOverdue = $rDate->isPast() && !$rDate->isToday();
                                        $isToday = $rDate->isToday();
                                        $badgeClass = $isOverdue ? 'bg-red-50 text-red-600 border border-red-200' : ($isToday ? 'bg-blue-50 text-blue-600 border border-blue-200' : 'bg-slate-50 text-slate-600 border border-slate-200');
                                    @endphp
                                    <span class="px-3 py-1.5 rounded-lg text-[11px] font-black tracking-wide shadow-sm {{ $badgeClass }}">
                                        {{ $rDate->format('d M Y') }}
                                    </span>
                                @else
                                    <span class="text-xs font-semibold text-slate-300 italic">N/A</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="px-3 py-1 rounded-lg text-xs font-black border" style="background-color: {{ $item->color ?? '#4f46e5' }}10; color: {{ $item->color ?? '#4f46e5' }}; border-color: {{ $item->color ?? '#4f46e5' }}30;">
                                    {{ $item->avg_percentage ?? 0 }}%
                                </span>
                            </td>
                            <td class="px-6">
                                <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden shadow-inner">
                                    <!-- 🌟 ដាក់ចលនា Animation ឱ្យ Progress Bar រត់ 🌟 -->
                                    <div class="h-full rounded-full transition-all duration-1000 ease-out relative" 
                                         style="width: 0%; background-color: {{ $item->color ?? '#4f46e5' }};"
                                         data-width="{{ $item->avg_percentage ?? 0 }}%">
                                         <div class="absolute inset-0 bg-white/20"></div> <!-- ធ្វើឱ្យមានពន្លឺរលោងៗលើ Bar -->
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-12 text-slate-400 font-semibold text-sm">No activity data available yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script>
    // Live Clock Function
    function updateDashboardClock() {
        const now = new Date();
        const clockElement = document.getElementById('live-clock');
        if(clockElement) { clockElement.textContent = now.toLocaleTimeString('en-GB'); }
    }
    setInterval(updateDashboardClock, 1000);
    updateDashboardClock();

    // 🌟 Animation សម្រាប់ Progress Bar រត់ពេល Load 🌟
    document.addEventListener("DOMContentLoaded", function() {
        setTimeout(() => {
            document.querySelectorAll('[data-width]').forEach(bar => {
                bar.style.width = bar.getAttribute('data-width');
            });
        }, 500); // រង់ចាំកន្លះវិនាទីចាំឱ្យរត់
    });

    // Chart Setup
    Chart.register(ChartDataLabels);

    const chartOptions = {
        indexAxis: 'y', 
        responsive: true, 
        maintainAspectRatio: false,
        animation: {
            duration: 1500,
            easing: 'easeOutQuart' // ធ្វើឱ្យ Chart គូសចេញមករលូន
        },
        plugins: { 
            legend: { display: false },
            datalabels: { 
                color: '#1e293b', 
                anchor: 'end', 
                align: 'right', 
                offset: 6, 
                font: { family: 'Plus Jakarta Sans', weight: '800', size: 11 } 
            }
        },
        scales: {
            x: { display: false },
            y: { 
                grid: { display: false }, 
                ticks: { color: '#64748b', font: { family: 'Noto Sans Khmer', size: 10, weight: '700' } } 
            }
        },
        layout: { padding: { right: 30 } } // បន្ថែម Space ខាងស្តាំកុំឱ្យដាច់លេខ
    };

    const provinceLabels = {!! json_encode($provinceStats->pluck('location') ?? []) !!};
    const provinceData = {!! json_encode($provinceStats->pluck('total') ?? []) !!};
    if(provinceLabels.length > 0) {
        new Chart(document.getElementById('provinceChart'), {
            type: 'bar', data: { labels: provinceLabels, datasets: [{ data: provinceData, backgroundColor: '#3b82f6', borderRadius: 6, barThickness: 14 }] }, options: chartOptions
        });
    }

    const modelLabels = {!! json_encode($modelStats->pluck('model') ?? []) !!};
    const modelData = {!! json_encode($modelStats->pluck('total') ?? []) !!};
    if(modelLabels.length > 0) {
        new Chart(document.getElementById('modelChart'), {
            type: 'bar', data: { labels: modelLabels, datasets: [{ data: modelData, backgroundColor: '#f59e0b', borderRadius: 6, barThickness: 14 }] }, options: chartOptions
        });
    }

    const salesLabels = {!! json_encode($salesStats->pluck('sales_person') ?? []) !!};
    const salesData = {!! json_encode($salesStats->pluck('total') ?? []) !!};
    if(salesLabels.length > 0) {
        new Chart(document.getElementById('salesChart'), {
            type: 'bar', data: { labels: salesLabels, datasets: [{ data: salesData, backgroundColor: '#ec4899', borderRadius: 6, barThickness: 14 }] }, options: chartOptions
        });
    }
</script>
@endsection