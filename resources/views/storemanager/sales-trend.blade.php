@extends('layouts.app_admin') @section('title') Dashbaord @endsection
@section('styles') @endsection @section('content')

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4>Sales Trend Reports</h4>
                </div>
            </div>
            <ul class="table-top-head">
                <!-- <li>
                    <a
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="Pdf"
                        ><img src="{{asset('admin/assets/img/icons/pdf.svg')}}" alt="img"
                    /></a>
                </li>
                <li>
                    <a
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="Excel"
                        ><img src="{{asset('admin/assets/img/icons/excel.svg')}}" alt="img"
                    /></a>
                </li>
                <li>
                    <a
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="Print"
                        ><i
                            data-feather="printer"
                            class="feather-rotate-ccw"
                        ></i
                    ></a>
                </li> -->
                <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Refresh" id="refresh-page"><i
                            data-feather="rotate-ccw" class="feather-rotate-ccw"></i></a>
                </li>
                <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Collapse" id="collapse-header"><i
                            data-feather="chevron-up" class="feather-chevron-up"></i></a>
                </li>
            </ul>
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
                            <table class="table datanew" id="sales-report-table">
                                <thead>
                                    <tr>

                                        <th>Product Name</th>
                                        <th>SKU</th>
                                        <th>Category</th>
                                        <th>Sold Qty</th>
                                        <th>Sold Amount ($)</th>
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

                    <div class="card-body">
                        <div id="sales-analysis-graph" class="chart-set"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/chroma-js/2.1.0/chroma.min.js"></script>
<script>
    $(document).ready(function () {
        function generateColors(numColors) {
            const baseScale = chroma.scale([
                '#0074D9', // Bright Blue
            ])
                .mode('lab')
                .colors(numColors);

            // Increase vibrancy by boosting saturation and brightness
            const vibrantColors = baseScale.map(color =>
                chroma(color).saturate(10).hex(5)
            );

            return vibrantColors;
        }

        function loadSalesChart(data) {

            const categories = data.labels;
            const values = data.values;


            console.log("Categories:", categories);
            console.log("Values:", values);

            if (!categories || categories.length === 0 || !values || values.length === 0) {
                console.error("Invalid data structure");
                return;
            }

            const chartData = categories.map((category, index) => ({
                name: category,
                value: parseFloat(values[index]),
            }));
            const colors = generateColors(chartData.length);

            const options = {
                series: [{
                    name: "Sales",
                    data: chartData.map(d => d.value + 'k')
                }],
                chart: {
                    type: 'bar',
                    height: 350
                },
                colors: colors,
                xaxis: {
                    categories: categories
                },
                dataLabels: {
                    enabled: false
                },
                title: {
                    text: 'Sales Trend Analysis',
                    align: 'center'
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val + "k";
                        }
                    }
                },
                dataLabels: {
                    formatter: function (val) {
                        return val + "k";
                    }
                }
            };

            // Initialize the chart
            $("#sales-analysis-graph").html("");
            const chart = new ApexCharts(document.querySelector("#sales-analysis-graph"), options);
            chart.render();
        }

        const table = $('#sales-report-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.salesTrend') }}",
                data: function (d) {
                    d.category_id = $('#categorySelect').val();
                    d.date_from = $('#dateFrom').val();
                    d.date_to = $('#dateTo').val();
                    d.store_id = $('#storeSelect').val();
                    d.order_type = $('#sort-order').val();
                },
            },
            columns: [
                { data: 'product_name', name: 'order_items.product_name' },
                { data: 'sku', name: 'order_items.sku' },
                { data: 'category', name: 'categories.name' },
                { data: 'sold_qty', name: 'sold_qty' },
                { data: 'sold_amount', name: 'sold_amount' },
                { data: 'instock_qty', name: 'stocks.quantity' }
            ],
            order: [[0, 'asc']],
            pageLength: 10,

            initComplete: function () {
                let searchWrapper = $('#sales-report-table_filter');
                let defaultSearchBox = searchWrapper.find('input');
                $('#custom-search').append(defaultSearchBox);
                defaultSearchBox.attr('placeholder', 'Search orders...');
                searchWrapper.remove();
            },
            // dom: '<"top"i>rt<"bottom"lp><"clear">',
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

                    loadSalesChart(response.chartData);
                    // table.clear().rows.add(response.tableData).draw();
                    table.ajax.reload(); // Reload the datatable

                }
            });
        });
    });
</script>

@endsection