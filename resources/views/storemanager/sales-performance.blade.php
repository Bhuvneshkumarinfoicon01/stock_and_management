@extends('layouts.app_admin') @section('title') Dashbaord @endsection
@section('styles') @endsection @section('content')

<div class="page-wrapper">
    <div class="content">
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5>Annual Sales Achieved</h5>
                        <h6 class="counter text-success">${{ number_format($annualSales, 2) }}</h6>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5>Quarterly Sales Achieved</h5>
                        <h6 class="counter text-primary">${{ number_format($quarterlySales, 2) }}</h6>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5>Monthly Sales Achieved</h5>
                        <h6 class="counter text-info">${{ number_format($monthlySales, 2) }}</h6>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12 col-sm-12 col-12 d-flex">
                <div class="card flex-fill">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Monthly Achieved Sales - {{ $selectedYear }}</h5>
                        <div class="graph-sets">
                            <div class="dropdown dropdown-wraper">
                                <button class="btn btn-light btn-sm dropdown-toggle" type="button"
                                    id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ $selectedYear }}
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    @foreach ($availableYears as $year)
                                    <li>
                                        <a href="{{ route('admin.sales-performance', ['year' => $year]) }}"
                                            class="dropdown-item">
                                            {{ $year }}
                                        </a>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="sales_target_charts"></div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row mt-5">
            <div class="col-xl-12 col-sm-12 col-12">
                <div class="card table-list-card">
                    <div class="card-body">
                        <div class="table-top">
                            <div class="search-set">
                                <div class="search-input d-flex align-items-center">
                                    <div id="custom-search" class="me-2"></div>
                                    <!-- DataTable input will be moved here -->
                                    <a href="#" class="btn btn-searchset">
                                        <i data-feather="search" class="feather-search"></i>
                                    </a>
                                </div>
                            </div>
                            <!-- <div class="search-path">
                                <div class="d-flex align-items-center">
                                    <a class="btn btn-filter" id="filter_search">
                                        <i data-feather="filter" class="filter-icon"></i>
                                        <span><img src="assets/img/icons/closes.svg" alt="img"></span>
                                    </a>

                                </div>
                            </div> -->
                            <div class="form-sort">
                                <i data-feather="sliders" class="info-img"></i>
                                <select class="select" id="sort-order">
                                    <option value="">Sort by</option>
                                    <option value="desc">Newest First</option>
                                    <option value="asc">Oldest First</option>
                                </select>
                            </div>
                        </div>
                        <div class="card" id="filter_inputs">
                            <div class="card-body pb-0">
                                <div class="row">
                                    <!-- Replace the dropdown with a hidden input -->
                                    <input type="hidden" name="store_id" id="store_id"
                                        value="{{ session('selected_store_id') }}">
                                    <div class="col-lg-2">
                                        <div class="input-blocks">
                                            <i data-feather="zap" class="info-img"></i>
                                            <select class="select" name="category_id" id="categorySelect">
                                                <option value="">Choose Category</option>

                                                @foreach($categories as $category)
                                                <option value="{{$category->id}}">
                                                    {{$category->name}}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6 col-12">
                                        <div class="input-blocks">
                                            <i data-feather="calendar" class="info-img"></i>
                                            <div class="input-groupicon">
                                                <input type="text" name="date_from" id="dateFrom" class="datetimepicker"
                                                    placeholder="Choose Date From" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6 col-12">
                                        <div class="input-blocks">
                                            <i data-feather="calendar" class="info-img"></i>
                                            <div class="input-groupicon">
                                                <input type="text" name="date_to" id="dateTo" class="datetimepicker"
                                                    placeholder="Choose Date To" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-1 col-sm-6 col-12">
                                        <div class="input-blocks">
                                            <a class="btn btn-filters ms-auto" id="filterBtn">
                                                <i data-feather="search" class="feather-search"></i>
                                                Search
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <h5 class="card-title mb-0">Recent Sales</h5>
                            <table class="table  datanew" id="recent-sales-table">
                                <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>SKU</th>
                                        <th>Category</th>
                                        <th>Sold Qty</th>
                                        <th>Sold Amount</th>
                                        <th>Instock Qty</th>
                                    </tr>
                                </thead>


                            </table>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-12 col-sm-12 col-12 d-flex">
                <div class="card flex-fill default-cover">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Sales Analytics</h5>
                        <div class="finance-charts d-flex gap-3">
                            <input type="hidden" name="store_id" id="store_id"
                                value="{{ session('selected_store_id') }}">
                            <!-- Product Filter -->
                            <!-- <select class="select" id="filterProduct">
                                <option value="">Choose Product</option>
                                <option value="1">Productname1</option>
                                <option value="2">Productname2</option>
                                <option value="3">Productname3</option>
                            </select> -->

                            <!-- Category Filter -->
                            <select class="select" name="category_id" id="category_id">
                                <option value="">Choose Category</option>

                                @foreach($categories as $category)
                                <option value="{{$category->id}}">
                                    {{$category->name}}
                                </option>
                                @endforeach
                            </select>

                            <!-- From Date Filter -->
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="input-blocks">
                                    <i data-feather="calendar" class="info-img"></i>
                                    <div class="input-groupicon">
                                        <input type="text" name="date_from" id="date_from" class="datetimepicker"
                                            placeholder="Choose Date From" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="input-blocks">
                                    <i data-feather="calendar" class="info-img"></i>
                                    <div class="input-groupicon">
                                        <input type="text" name="date_to" id="date_to" class="datetimepicker"
                                            placeholder="Choose Date To" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-1 col-sm-6 col-12">
                                <div class="input-blocks">
                                    <a class="btn btn-filters ms-auto" id="searchBtn">
                                        <i data-feather="search" class="feather-search"></i>
                                        Search
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
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
    $(document).ready(function () {
        let table = $('#recent-sales-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.sales-performance') }}",
                data: function (d) {
                    d.category_id = $('#categorySelect').val();  // Filter by category
                    d.date_from = $('#dateFrom').val();  // Filter by date
                    d.date_to = $('#dateTo').val();  // Filter by date
                    d.store_id = $('#storeSelect').val();  // (if applicable) filter by store
                    d.order_type = $('#sort-order').val();
                }
            },
            columns: [
                { data: 'sku', name: 'sku' },
                { data: 'product_info', name: 'product_info' },
                { data: 'category', name: 'category' },
                { data: 'sold_qty', name: 'sold_qty' },
                { data: 'sold_amount', name: 'sold_amount' },
                { data: 'quantity', name: 'quantity' },

            ],
            order: [[0, 'asc']],
            pageLength: 10,
            initComplete: function () {
                let searchWrapper = $('#recent-sales-table_filter');
                let defaultSearchBox = searchWrapper.find('input');
                $('#custom-search').append(defaultSearchBox);
                defaultSearchBox.attr('placeholder', 'Search orders...');
                searchWrapper.remove();
            },
        });
        $('#sort-order').on('change', function () {
            table.ajax.reload();
        });
        $('#filterBtn').on('click', function () {
            let category_id = $('#categorySelect').val();
            let date_from = $('#dateFrom').val();
            let date_to = $('#dateTo').val();
            let store_id = $('#store_id').val();

            $.ajax({
                url: "{{ route('admin.sales-trend-filter') }}",
                type: 'GET',
                data: {
                    category_id: category_id,
                    date_from: date_from,
                    date_to: date_to,
                    store_id: store_id
                },
                success: function (response) {


                    table.ajax.reload(); // Reload the datatable

                }
            });
        });
    });

</script>
<script>

    document.addEventListener('DOMContentLoaded', function () {
        if ($('#sales_target_charts').length > 0) {
            var options = {
                chart: {
                    height: 350,
                    type: 'line',
                    toolbar: { show: false }
                },
                series: [{
                    name: 'Achieved Sales',
                    type: 'line',
                    data: @json($monthlySalesArray)

                }],
                stroke: { width: 2 },
                title: {
                    text: 'Achieved Monthly Sales - {{ $selectedYear }}'
                },
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                    'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                xaxis: {
                    type: 'category'
                },
                yaxis: {
                    title: {
                        text: 'Achieved Sales'
                    },
                    labels: {
                        formatter: (val) => (val / 1000).toFixed(2) + 'K'

                    }
                }
            };

            var chart = new ApexCharts(
                document.querySelector("#sales_target_charts"),
                options
            );

            chart.render();
        }
    });

</script>
<script>
    $(document).ready(function () {
        // Check if the chart container exists
        if ($('#sales-analysis-chart').length > 0) {
            // Initialize chart with static data (you can update this later with AJAX response)
            var options = {
                series: [{
                    name: "Sales Analysis",
                    data: [] // Empty array for initial data
                }],
                chart: {
                    height: 273,
                    type: 'area',
                    zoom: {
                        enabled: false
                    }
                },
                colors: ['#FF9F43'],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'straight'
                },
                title: {
                    text: '',
                    align: 'left'
                },
                xaxis: {
                    categories: [] // Empty categories initially
                },
                yaxis: {
                    min: 0,
                    labels: {
                        formatter: (val) => val + 'K'

                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'left'
                }
            };

            // Create and render the initial chart
            var chart = new ApexCharts(document.querySelector("#sales-analysis-chart"), options);
            chart.render();

            // Event handler for the search button
            $("#searchBtn").on("click", function () {
                let store_id = $("#store_id").val();
                let category_id = $("#category_id").val();
                let date_from = $("#date_from").val();
                let date_to = $("#date_to").val();

                // Make an AJAX request to fetch filtered data
                $.ajax({
                    url: "{{ route('admin.sales-trend-filter') }}",
                    type: "GET",
                    data: {
                        store_id,
                        category_id,
                        date_from,
                        date_to
                    },
                    success: function (res) {
                        console.log('Response:', res); // Log the response for debugging

                        // Ensure the response contains valid chart data
                        if (res.chartData && res.chartData.labels && res.chartData.values) {
                            // Update the chart with dynamic data from the response
                            chart.updateOptions({
                                series: [{
                                    name: "Sales Analysis",
                                    data: res.chartData.values // Use chart data from the response
                                }],
                                xaxis: {
                                    categories: res.chartData.labels // Use the labels from the response
                                }
                            });
                        } else {
                            console.error("Invalid chart data in the response.");
                        }
                    },
                    error: function (err) {
                        console.error("AJAX request failed:", err);
                    }
                });
            });
        }
    });


</script>
@endsection