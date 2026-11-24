@extends('layouts.app_admin') @section('title') Dashbaord @endsection
@section('styles') @endsection @section('content')

<div class="page-wrapper">
    <div class="content">
        <div class="row">
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="dash-widget dash3 w-100">
                    <div class="dash-widgetimg">
                        <span><img src="assets/img/icons/dash4.svg" alt="img" /></span>
                    </div>
                    <div class="dash-widgetcontent">
                        {{-- Old display doubled "k" and confused counters: {{$totalSales}}k ... k --}}
                        <h5 class="mb-0">
                            $<span class="counters" data-count="{{ number_format($totalSales, 2, '.', '') }}">
                                {{ number_format($totalSales, 2) }}
                            </span>
                        </h5>
                        <p class="text-muted small mb-0">Line sales amount ({{ $defaultYear }}) · excl. estimates</p>
                        <h6 class="mt-1">Total Sales</h6>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="dash-widget dash1 w-100">
                    <div class="dash-widgetimg">
                        <span><img src="assets/img/icons/dash2.svg" alt="img" /></span>
                    </div>
                    <div class="dash-widgetcontent">
                        {{-- Old: {{$revenueAmount}}k ... k --}}
                        <h5 class="mb-0">
                            $<span class="counters" data-count="{{ number_format($revenueAmount, 2, '.', '') }}">
                                {{ number_format($revenueAmount, 2) }}
                            </span>
                        </h5>
                        <p class="text-muted small mb-0">Order revenue amount ({{ $defaultYear }}) · excl. estimates</p>
                        <h6 class="mt-1">Total revenue</h6>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="dash-widget w-100">
                    <div class="dash-widgetimg">
                        <span><img src="assets/img/icons/dash1.svg" alt="img" /></span>
                    </div>
                    <div class="dash-widgetcontent">
                        {{-- Old: showed "{{ $totalStores }}k" — counts are not thousands --}}
                        <h5>
                            <span class="counters" data-count="{{ $totalStores }}">{{ $totalStores }}</span>
                        </h5>
                        <h6>Total number of stores</h6>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="dash-widget dash2 w-100">
                    <div class="dash-widgetimg">
                        <span><img src="assets/img/icons/dash3.svg" alt="img" /></span>
                    </div>
                    <div class="dash-widgetcontent">
                        <h5>
                            <span class="counters" data-count="{{ $lowStockProductsCount }}">{{ $lowStockProductsCount }}</span>
                        </h5>
                        <h6>Low stock SKUs</h6>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-end">
                <div class="dropdown dropdown-wraper">
                    <button class="btn btn-white btn-sm dropdown-toggle d-flex align-items-center" type="button"
                        id="dropdown-sales" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ $defaultYear }}
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdown-sales">
                        @foreach($availableYears as $year)
                        <li>
                            <a href="?year={{ $year }}" class="dropdown-item">{{ $year }}</a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Profit & Loss Chart -->
            <div class="col-sm-12 col-md-12 col-xl-6 d-flex">
                <div class="card flex-fill">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Sales vs stock adjustments</h5>
                        <small class="text-muted">Bar = line-item sales; orange = stock adj. value (operational, not GL P&amp;L)</small>
                    </div>
                    <div class="card-body">
                        <div id="s-col-graph" class="chart-set"></div>
                    </div>
                </div>
            </div>

            <!-- Sales Trends Chart -->
            <div class="col-xl-6 col-sm-12 col-12 d-flex">
                <div class="card flex-fill default-cover">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Sales Trends</h5>
                    </div>
                    <div class="card-body">
                        <div id="sales-analysis-chart" class="chart-set"></div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-sm-12 col-md-12 col-xl-4 d-flex">
                <div class="card flex-fill default-cover w-100 mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Top-5 performing stores</h4>
                        <div class="dropdown">
                            <!-- <a href="javascript:void(0);" class="view-all d-flex align-items-center">
                                View All<span class="ps-2 d-flex align-items-center"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="feather feather-arrow-right feather-16">
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                        <polyline points="12 5 19 12 12 19"></polyline>
                                    </svg>
                                </span>
                            </a> -->
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless recent-transactions">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Store Name</th>
                                        <th>Sales(amt)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($topStores as $store)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="store-info">
                                                <a href="#" class="store-img">
                                                    <img src="assets/img/store/store-01.png" alt="store" />
                                                </a>
                                                <div class="info">
                                                    <a href="#">{{$store->name}}</a>
                                                </div>
                                            </div>
                                        </td>
                                        <td>${{ number_format((float) $store->total_sales, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-lg-4 col-sm-12 col-12">
                <div class="card flex-fill default-cover w-100 mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">High-demand Products</h4>
                        <div class="dropdown">
                            <a href="{{route('admin.top-view-all')}}" class="view-all d-flex align-items-center">

                                View All<i data-feather="arrow-right" class="feather-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless best-seller">
                                <tbody>
                                    @foreach($highDemandProducts as $product)
                                    @php $stock = $product->stocks->first(); @endphp
                                    {{-- Old: nested @foreach($product->stocks) duplicated one row per store --}}
                                    <tr>
                                        <td>
                                            <div class="product-info">
                                                <a href="#" class="product-img">
                                                    <img src="{{ asset($stock && $stock->images ? 'storage/'.$stock->images : 'admin/assets/img/avatar/default-product.png') }}"
                                                        alt="product" />
                                                </a>
                                                <div class="info">
                                                    <a href="#">{{ $product->product_name }}</a>
                                                    <p class="dull-text">
                                                        ${{ number_format((float) $product->price, 2) }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="head-text">Units sold</p>
                                            {{ $product->total_sold }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-lg-4 col-sm-12 col-12">
                <div class="card flex-fill default-cover w-100 mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Low-stock Products</h4>
                        <div class="dropdown">
                            <a href="{{route('admin.view-all-low-stock-products')}}"
                                class="view-all d-flex align-items-center">

                                View All<i data-feather="arrow-right" class="feather-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless best-seller low-stock">
                                <tbody>
                                    @foreach($lowStockProducts as $product)
                                    <tr>
                                        <td>
                                            <div class="product-info">
                                                <a href="#" class="product-img">
                                                    <img src="{{asset($product->images ? 'storage/'.$product->images : 'admin\assets\img\avatar\default-product.png')}}"
                                                        alt="product" />
                                                </a>
                                                <div class="info">
                                                    <a href="#">{{ $product->product_name }}</a>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="head-text">
                                                <i class="fa-solid fa-arrow-trend-down"></i>
                                                {{ $product->quantity }}
                                                <span>units</span>
                                            </p>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12 col-lg-12">
                <div class="card flex-fill">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Total revenue</h5>
                    </div>
                    <div class="card-body">
                        <div id="s-line-graph" class="chart-set"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script>
    const salesByMonth = @json($salesByMonth);
    const availableYears = @json($availableYears);
    const defaultYear = @json($defaultYear);
</script>

<script>
    if ($("#sales-analysis-chart").length > 0) {
        const chartVals = salesByMonth.map(v => parseFloat(v) || 0);
        const maxSales = Math.max(...chartVals, 1);
        const options = {
            series: [
                {
                    name: "Sales (line totals)",
                    data: salesByMonth,
                },
            ],
            chart: {
                height: 273,
                type: "area",
                zoom: {
                    enabled: false,
                },
            },
            colors: ["#FF9F43"],
            dataLabels: {
                enabled: false,
            },
            stroke: {
                curve: "straight",
            },
            xaxis: {
                categories: [
                    "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec",
                ],
            },
            yaxis: {
                min: 0,
                max: maxSales * 1.15,
                labels: {
                    formatter: val => (val / 1000) + "K",
                },
            },
            legend: {
                position: "top",
                horizontalAlign: "left",
            },
        };

        const chart = new ApexCharts(document.querySelector("#sales-analysis-chart"), options);
        chart.render();
    }
</script>
<script>
    var profitByMonth = @json($profitByMonth);
    var lossByMonth = @json($lossByMonth);

    if ($("#s-col-graph").length > 0) {
        var sCol = {
            chart: {
                height: 350,
                type: "bar",
                toolbar: {
                    show: false,
                },
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: "55%",
                    endingShape: "rounded",
                },
            },
            dataLabels: {
                enabled: false,
            },
            stroke: {
                show: true,
                width: 2,
                colors: ["transparent"],
            },
            series: [
                {
                    name: "Line sales",
                    data: profitByMonth,
                },
                {
                    name: "Stock adj. value",
                    data: lossByMonth,
                },
            ],
            xaxis: {
                categories: [
                    "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                ],
            },
            yaxis: {
                title: {
                    text: "$ (thousands)",
                },
            },
            fill: {
                opacity: 1,
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return "$ " + val + "k";
                    },
                },
            },
        };

        var chart = new ApexCharts(document.querySelector("#s-col-graph"), sCol);
        chart.render();
    }
</script>
<script>
    const monthlyRevenue = @json($monthlyRevenueData);

    if ($('#s-line-graph').length > 0) {
        var sline = {
            chart: {
                height: 350,
                type: 'line',
                zoom: { enabled: false },
                toolbar: { show: false }
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'straight' },
            series: [{
                name: "Total Revenue",
                data: monthlyRevenue
            }],
            title: {
                text: 'Total Revenue by Month',
                align: 'left'
            },
            grid: {
                row: {
                    colors: ['#f1f2f3', 'transparent'],
                    opacity: 0.5
                },
            },
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            },
            yaxis: {
                min: 0,
                tickAmount: 5,
                labels: {
                    formatter: (val) => {
                        return val + 'K'
                    }
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'left'
            }
        }

        var chart = new ApexCharts(document.querySelector("#s-line-graph"), sline);
        chart.render();
    }
</script>



@endsection