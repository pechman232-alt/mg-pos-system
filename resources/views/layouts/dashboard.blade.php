@extends('layouts.homePages')

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@700&display=swap" rel="stylesheet">
<script src="https://unpkg.com/@phosphor-icons/web"></script>
<script src="https://cdn.tailwindcss.com"></script>

<style>
/* ====== BRANDED DESIGN SYSTEM ====== */
:root {
  --brand-indigo: #4f46e5;
  --brand-purple: #7c3aed;
  --brand-pink: #ec4899;
}

body {
  font-family: 'Plus Jakarta Sans', sans-serif !important;
  background-color: #f8fafc;
}

/* Custom Growth Animation */
@keyframes growUp {
  from { height: 0; opacity: 0; }
  /* Target height will be set via inline style */
}

.animate-grow {
  animation: growUp 1.2s cubic-bezier(0.4, 0, 0.2, 1) forwards;
}

.container-full {
  width: 100% !important;
  max-width: 100% !important;
  padding: 1.5rem;
}
</style>

<div class="container-full min-h-screen">
  
  <div class="mb-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
    <div>
      <h1 class="text-3xl font-black text-slate-800 tracking-tighter italic">TF MOTORS CORE</h1>
      <p class="text-slate-400 text-xs font-bold uppercase tracking-[4px] mt-1">Intelligence Management Interface</p>
    </div>
    
    <div class="flex items-center gap-4">
        <div class="bg-white px-6 py-3 rounded-2xl shadow-sm border border-slate-200 flex items-center gap-4">
            <div class="text-right">
                <div id="live-clock" class="text-2xl font-bold text-slate-800 tracking-tighter font-['JetBrains_Mono']">00:00:00</div>
                <div class="text-[9px] font-black text-indigo-500 uppercase tracking-widest">Global Server Time</div>
            </div>
            <div class="w-[1px] h-8 bg-slate-100"></div>
            <div class="flex flex-col items-center">
                <div class="w-2 h-2 bg-emerald-500 rounded-full animate-ping"></div>
                <span class="text-[8px] font-black text-emerald-600 uppercase mt-1">Active</span>
            </div>
        </div>
    </div>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-10">
    
    <div class="bg-white p-6 rounded-[30px] border border-slate-100 shadow-sm hover:shadow-xl transition-all duration-500 group overflow-hidden relative">
      <div class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
      <div class="relative flex items-center gap-5">
        <div class="w-16 h-16 bg-gradient-to-br from-indigo-600 to-indigo-400 text-white rounded-2xl flex items-center justify-center text-3xl shadow-lg shadow-indigo-100">
          <i class="ph-bold ph-buildings"></i>
        </div>
        <div>
          <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Branches</span>
          <h3 class="text-3xl font-black text-slate-800 tracking-tight">{{ $countBranch ?? 0 }}</h3>
        </div>
      </div>
    </div>

    <div class="bg-white p-6 rounded-[30px] border border-slate-100 shadow-sm hover:shadow-xl transition-all duration-500 group overflow-hidden relative">
      <div class="absolute -right-4 -top-4 w-24 h-24 bg-purple-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
      <div class="relative flex items-center gap-5">
        <div class="w-16 h-16 bg-gradient-to-br from-purple-600 to-purple-400 text-white rounded-2xl flex items-center justify-center text-3xl shadow-lg shadow-purple-100">
          <i class="ph-bold ph-map-trifold"></i>
        </div>
        <div>
          <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Provinces</span>
          <h3 class="text-3xl font-black text-slate-800 tracking-tight">{{ $countProvince ?? 0 }}</h3>
        </div>
      </div>
    </div>

    <div class="bg-white p-6 rounded-[30px] border border-slate-100 shadow-sm hover:shadow-xl transition-all duration-500 group overflow-hidden relative">
      <div class="absolute -right-4 -top-4 w-24 h-24 bg-pink-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
      <div class="relative flex items-center gap-5">
        <div class="w-16 h-16 bg-gradient-to-br from-pink-600 to-pink-400 text-white rounded-2xl flex items-center justify-center text-3xl shadow-lg shadow-pink-100">
          <i class="ph-bold ph-tag"></i>
        </div>
        <div>
          <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Brands</span>
          <h3 class="text-3xl font-black text-slate-800 tracking-tight">{{ $countBrand ?? 0 }}</h3>
        </div>
      </div>
    </div>

    <div class="bg-white p-6 rounded-[30px] border border-slate-100 shadow-sm hover:shadow-xl transition-all duration-500 group overflow-hidden relative">
      <div class="absolute -right-4 -top-4 w-24 h-24 bg-slate-100 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
      <div class="relative flex items-center gap-5">
        <div class="w-16 h-16 bg-gradient-to-br from-slate-800 to-slate-600 text-white rounded-2xl flex items-center justify-center text-3xl shadow-lg shadow-slate-200">
          <i class="ph-bold ph-steering-wheel"></i>
        </div>
        <div>
          <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Models</span>
          <h3 class="text-3xl font-black text-slate-800 tracking-tight">{{ $countModel ?? 0 }}</h3>
        </div>
      </div>
    </div>
  </div>

  @php
    $v1 = (int)($countBranch ?? 0);
    $v2 = (int)($countProvince ?? 0);
    $v3 = (int)($countBrand ?? 0);
    $v4 = (int)($countModel ?? 0);
    $max = max(1, $v1, $v2, $v3, $v4);
  @endphp

  <div class="bg-white p-8 md:p-12 rounded-[40px] border border-slate-200 shadow-sm relative overflow-hidden">
    <div class="absolute top-0 right-0 p-8 opacity-5">
        <i class="ph ph-chart-bar text-[150px]"></i>
    </div>
    
    <div class="mb-12">
        <h5 class="text-xl font-black text-slate-800 uppercase tracking-tighter">Inventory Scale Analysis</h5>
        <p class="text-slate-400 text-xs font-bold uppercase tracking-[3px]">Departmental Data Comparative</p>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-12 md:gap-20 items-end h-[350px]">
      <div class="flex flex-col items-center gap-6 h-full">
        <div class="w-full bg-slate-50 rounded-3xl flex-grow relative overflow-hidden flex items-end">
            <div class="w-full bg-gradient-to-t from-indigo-600 via-indigo-500 to-indigo-400 rounded-3xl animate-grow shadow-lg" 
                 style="height: {{ ($v1/$max)*100 }}%"></div>
        </div>
        <div class="text-center">
            <div class="text-lg font-black text-slate-800">{{ $v1 }}</div>
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Branches</span>
        </div>
      </div>

      <div class="flex flex-col items-center gap-6 h-full">
        <div class="w-full bg-slate-50 rounded-3xl flex-grow relative overflow-hidden flex items-end">
            <div class="w-full bg-gradient-to-t from-purple-600 via-purple-500 to-purple-400 rounded-3xl animate-grow shadow-lg" 
                 style="height: {{ ($v2/$max)*100 }}%"></div>
        </div>
        <div class="text-center">
            <div class="text-lg font-black text-slate-800">{{ $v2 }}</div>
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Provinces</span>
        </div>
      </div>

      <div class="flex flex-col items-center gap-6 h-full">
        <div class="w-full bg-slate-50 rounded-3xl flex-grow relative overflow-hidden flex items-end">
            <div class="w-full bg-gradient-to-t from-pink-600 via-pink-500 to-pink-400 rounded-3xl animate-grow shadow-lg" 
                 style="height: {{ ($v3/$max)*100 }}%"></div>
        </div>
        <div class="text-center">
            <div class="text-lg font-black text-slate-800">{{ $v3 }}</div>
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Brands</span>
        </div>
      </div>

      <div class="flex flex-col items-center gap-6 h-full">
        <div class="w-full bg-slate-50 rounded-3xl flex-grow relative overflow-hidden flex items-end">
            <div class="w-full bg-gradient-to-t from-slate-800 via-slate-700 to-slate-600 rounded-3xl animate-grow shadow-lg" 
                 style="height: {{ ($v4/$max)*100 }}%"></div>
        </div>
        <div class="text-center">
            <div class="text-lg font-black text-slate-800">{{ $v4 }}</div>
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Models</span>
        </div>
      </div>
    </div>
  </div>

  <footer class="mt-16 mb-8 flex flex-col md:flex-row justify-between items-center gap-4 px-4">
    <div class="text-slate-400 text-[10px] font-black uppercase tracking-[2px]">
        © {{ date('Y') }} TF Motors | Adaptive System V.3.3
    </div>
    <div class="flex gap-8 text-[10px] font-black uppercase tracking-[2px]">
        <a href="#" class="text-slate-400 hover:text-indigo-600 transition-colors">Documentation</a>
        <a href="#" class="text-slate-400 hover:text-indigo-600 transition-colors">Server Status</a>
    </div>
  </footer>

</div>

<script>
    // LIVE CLOCK ENGINE
    function updateDashboardClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const clockElement = document.getElementById('live-clock');
        if(clockElement) {
            clockElement.textContent = `${hours}:${minutes}:${seconds}`;
        }
    }
    setInterval(updateDashboardClock, 1000);
    updateDashboardClock();
</script>
@endsection