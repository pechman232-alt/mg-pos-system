@extends('layouts.homePages')

@section('content')
<!-- ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    :root {
        --saas-primary: #4F46E5;
        --saas-danger: #EF4444;
        --saas-success: #10B981;
        --saas-warning: #F59E0B;
        --saas-bg: #F3F4F6;
        --saas-card-bg: #FFFFFF;
        --saas-border: #E5E7EB;
        --saas-text-main: #111827;
        --saas-text-muted: #6B7280;
    }

    .analytics-container { font-family: 'Inter', sans-serif; }
    
    /* Cards Style */
    .metric-card {
        background-color: var(--saas-card-bg);
        border: 1px solid var(--saas-border);
        border-radius: 12px;
        padding: 20px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        height: 100%;
    }
    .metric-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    }
    
    .metric-title { font-size: 13px; font-weight: 600; color: var(--saas-text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
    .metric-value { font-size: 24px; font-weight: 700; color: var(--saas-text-main); margin-top: 8px; }
    
    .icon-box {
        width: 48px; height: 48px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 24px;
    }
    .icon-danger { background-color: #FEF2F2; color: var(--saas-danger); }
    .icon-primary { background-color: #EEF2FF; color: var(--saas-primary); }
    .icon-warning { background-color: #FFFBEB; color: var(--saas-warning); }
    .icon-success { background-color: #ECFDF5; color: var(--saas-success); }

    /* Chart Card Style */
    .chart-card {
        background-color: var(--saas-card-bg);
        border: 1px solid var(--saas-border);
        border-radius: 12px;
        padding: 20px;
        margin-top: 20px;
    }
    .chart-header {
        font-size: 16px; font-weight: 700; color: var(--saas-text-main); margin-bottom: 20px;
        border-bottom: 1px solid var(--saas-border); padding-bottom: 15px;
    }
</style>

<div class="container-fluid flex-grow-1 analytics-container py-4">
    
    <!-- Header and Filter -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0 text-secondary">
            <span>Analytics /</span> <span style="color: #111827;">EXPENSE DASHBOARD</span>
        </h4>
        <div>
            <select class="form-select form-select-sm shadow-sm" style="border-color: var(--saas-border); width: 150px; font-weight: 500;">
                <option value="this_month">This Month</option>
                <option value="last_month">Last Month</option>
                <option value="this_year">This Year</option>
            </select>
        </div>
    </div>

    <!-- 1. Top Metrics -->
    <div class="row g-3">
        <!-- Total Expense -->
        <div class="col-xl-3 col-md-6">
            <div class="metric-card d-flex justify-content-between align-items-center">
                <div>
                    <div class="metric-title">Total Expenses</div>
                    <div class="metric-value">${{ number_format($totalExpense, 2) }}</div>
                    <small class="text-danger fw-bold mt-2 d-block"><i class="bx bx-trending-up"></i> +12% vs last month</small>
                </div>
                <div class="icon-box icon-danger"><i class="bx bx-wallet"></i></div>
            </div>
        </div>
        
        <!-- Top Category -->
        <div class="col-xl-3 col-md-6">
            <div class="metric-card d-flex justify-content-between align-items-center">
                <div>
                    <div class="metric-title">Top Category</div>
                    <div class="metric-value" style="font-size: 18px; margin-top: 14px;">{{ $topCategory }}</div>
                    <small class="text-muted mt-2 d-block">Highest spend area</small>
                </div>
                <div class="icon-box icon-primary"><i class="bx bx-pie-chart-alt-2"></i></div>
            </div>
        </div>

        <!-- Daily Average -->
        <div class="col-xl-3 col-md-6">
            <div class="metric-card d-flex justify-content-between align-items-center">
                <div>
                    <div class="metric-title">Daily Average</div>
                    <div class="metric-value">${{ number_format($dailyAvg, 2) }}</div>
                    <small class="text-muted mt-2 d-block">Based on last 7 days</small>
                </div>
                <div class="icon-box icon-warning"><i class="bx bx-line-chart"></i></div>
            </div>
        </div>

        <!-- Budget Status -->
        <div class="col-xl-3 col-md-6">
            <div class="metric-card d-flex justify-content-between align-items-center">
                <div class="w-100">
                    <div class="metric-title mb-2">Budget Status</div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-bold" style="font-size: 14px;">65%</span>
                        <span class="text-muted small">35% Left</span>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 4px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 65%;"></div>
                    </div>
                </div>
                <div class="icon-box icon-success ms-3"><i class="bx bx-check-shield"></i></div>
            </div>
        </div>
    </div>

    <!-- 2. Charts Section -->
    <div class="row">
        <!-- Area Chart: Daily Trend -->
        <div class="col-xl-8 col-lg-7">
            <div class="chart-card shadow-sm">
                <div class="chart-header">Daily Expense Trend (7 Days)</div>
                <div id="trendChart"></div>
            </div>
        </div>

        <!-- Donut Chart: By Category -->
        <div class="col-xl-4 col-lg-5">
            <div class="chart-card shadow-sm">
                <div class="chart-header">Expenses by Category</div>
                <div id="categoryChart" class="d-flex justify-content-center"></div>
            </div>
        </div>
    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        // 1. Setup Trend Chart (Area Chart)
        const trendData = @json($expenseTrend);
        
        var trendOptions = {
            series: [{
                name: 'Amount ($)',
                data: trendData.data
            }],
            chart: {
                height: 320,
                type: 'area',
                fontFamily: 'Inter, sans-serif',
                toolbar: { show: false }
            },
            colors: ['#4F46E5'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.05,
                    stops: [0, 90, 100]
                }
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            xaxis: {
                categories: trendData.categories,
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: { formatter: (value) => { return "$" + value } }
            },
            grid: {
                borderColor: '#E5E7EB',
                strokeDashArray: 4,
            }
        };
        var trendChart = new ApexCharts(document.querySelector("#trendChart"), trendOptions);
        trendChart.render();

        // 2. Setup Category Chart (Donut Chart)
        const categoryData = @json($expenseByCategory);
        
        var categoryOptions = {
            series: categoryData.series,
            labels: categoryData.labels,
            chart: {
                type: 'donut',
                height: 330,
                fontFamily: 'Inter, sans-serif',
            },
            colors: ['#4F46E5', '#10B981', '#F59E0B', '#EF4444', '#6B7280'],
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            name: { fontSize: '12px', color: '#6B7280' },
                            value: { fontSize: '20px', fontWeight: 700, color: '#111827', formatter: (val) => "$" + val },
                            total: {
                                show: true,
                                label: 'Total',
                                formatter: function (w) {
                                    return "$" + w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                }
                            }
                        }
                    }
                }
            },
            dataLabels: { enabled: false },
            legend: {
                position: 'bottom',
                markers: { radius: 12 },
                itemMargin: { horizontal: 10, vertical: 5 }
            },
            stroke: { show: true, colors: ['#ffffff'], width: 2 }
        };
        var categoryChart = new ApexCharts(document.querySelector("#categoryChart"), categoryOptions);
        categoryChart.render();

    });
</script>
@endsection