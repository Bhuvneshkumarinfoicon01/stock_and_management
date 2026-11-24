@extends('layouts.app_admin')

@section('title')
Sales Analysis
@endsection

@section('styles')
@endsection

@section('content')

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4>Sales Analysis</h4>
                    <h6 class="text-muted">Monthly sales from completed invoices and POS orders (excludes estimates).</h6>
                </div>
            </div>
            <ul class="table-top-head">
                <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Refresh" id="refresh-page"><i data-feather="rotate-ccw" class="feather-rotate-ccw"></i></a>
                </li>
                <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Collapse" id="collapse-header"><i data-feather="chevron-up" class="feather-chevron-up"></i></a>
                </li>
            </ul>
        </div>

        <div class="row mt-3">
            <div class="col-xl-12 col-sm-12 col-12 d-flex">
                <div class="card flex-fill default-cover w-100">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="finance-charts d-flex flex-wrap gap-3 align-items-center">
                            <select id="filter-year" class="select">
                                @for ($y = now()->year; $y >= now()->year - 5; $y--)
                                    <option value="{{ $y }}" @selected($y === now()->year)>{{ $y }}</option>
                                @endfor
                            </select>
                            <select id="filter-store" class="select">
                                <option value="">All Stores</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->name }}</option>
                                @endforeach
                            </select>
                            <select id="filter-category" class="select">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <button type="button" id="search-btn" class="btn btn-primary">Search</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="sa-summary" class="small text-muted mb-2"></div>
                        <div id="sales-analysis-chart" class="chart-set"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script>
(function () {
    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    const apiUrl = @json(route('admin.sales-analysis-superadmin'));
    let chartInstance = null;

    function fmtMoney(val) {
        const n = parseFloat(val) || 0;
        if (n >= 1000000) return (n / 1000000).toFixed(2) + 'M';
        if (n >= 1000) return (n / 1000).toFixed(2) + 'K';
        return n.toFixed(2);
    }

    function renderChart(data, year) {
        const monthlyTotals = {};
        months.forEach(m => { monthlyTotals[m] = 0; });
        (data || []).forEach(row => {
            monthlyTotals[row.month] = parseFloat(row.total) || 0;
        });

        const chartData = months.map(m => monthlyTotals[m]);
        const yearTotal = chartData.reduce((a, b) => a + b, 0);
        $('#sa-summary').text('Year ' + year + ' — total sales: $' + yearTotal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

        const maxVal = Math.max(...chartData, 1);
        const options = {
            series: [{ name: 'Sales', data: chartData }],
            chart: { height: 320, type: 'area', zoom: { enabled: false }, toolbar: { show: false } },
            colors: ['#FF9F43'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05 } },
            xaxis: { categories: months },
            yaxis: {
                min: 0,
                max: maxVal * 1.15,
                labels: { formatter: val => fmtMoney(val) }
            },
            tooltip: {
                y: { formatter: val => '$' + Number(val).toLocaleString(undefined, { minimumFractionDigits: 2 }) }
            },
            legend: { show: false }
        };

        const el = document.querySelector('#sales-analysis-chart');
        el.innerHTML = '';
        if (chartInstance) {
            chartInstance.destroy();
        }
        chartInstance = new ApexCharts(el, options);
        chartInstance.render();
    }

    function loadSales() {
        const storeId = document.getElementById('filter-store').value;
        const categoryId = document.getElementById('filter-category').value;
        const year = document.getElementById('filter-year').value;

        const params = new URLSearchParams({ year: year });
        if (storeId) params.set('store_id', storeId);
        if (categoryId) params.set('category_id', categoryId);

        fetch(apiUrl + '?' + params.toString(), {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => {
            if (!r.ok) throw new Error('Failed to load');
            return r.json();
        })
        .then(data => renderChart(data, year))
        .catch(() => {
            $('#sa-summary').text('Could not load sales data.');
            Swal.fire({ icon: 'error', title: 'Error', text: 'Could not load sales analysis.' });
        });
    }

    document.getElementById('search-btn').addEventListener('click', loadSales);
    document.getElementById('refresh-page')?.addEventListener('click', loadSales);

    /* Legacy: required store_id; summed orders.total_price per order_items row (double-counted). */
    loadSales();
})();
</script>
@endsection
