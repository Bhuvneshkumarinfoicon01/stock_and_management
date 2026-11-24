@extends('layouts.app_admin')

@section('title')
Dashbaord
@endsection

@section('styles')

@endsection

@section('content')


<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title me-auto">
                <h4>Inventory Dashboard</h4>
            </div>
            <ul class="table-top-head low-stock-top-head">
                <!-- <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Pdf"><img src="assets/img/icons/pdf.svg"
                            alt="img"></a>
                </li>
                <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Excel"><img
                            src="assets/img/icons/excel.svg" alt="img"></a>
                </li>
                <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Print"><i data-feather="printer"
                            class="feather-rotate-ccw"></i></a>
                </li> -->
                <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Refresh" id="refresh-page"><i data-feather="rotate-ccw"
                            class="feather-rotate-ccw"></i></a>
                </li>
                <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Collapse" id="collapse-header"><i
                            data-feather="chevron-up" class="feather-chevron-up"></i></a>
                </li>
            </ul>
        </div>
        <div class="table-tab">
            <ul class="nav nav-pills" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill"
                        data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home"
                        aria-selected="true">Low Stocks</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill"
                        data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile"
                        aria-selected="false">Out Of Stocks</button>
                </li>
                <!-- <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-out-of-stock-tab" data-bs-toggle="pill"
                        data-bs-target="#pills-out-of-stock" type="button" role="tab" aria-controls="pills-out-of-stock"
                        aria-selected="false">Out of Stock</button>
                </li> -->
                <!-- <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-discrepencies-tab" data-bs-toggle="pill"
                        data-bs-target="#pills-discrepencies" type="button" role="tab"
                        aria-controls="pills-discrepencies" aria-selected="false">Discrepencies in stock</button>
                </li> -->
            </ul>
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                    <div class="card table-list-card">
                        <div class="card-body">
                            <div class="table-top">
                               <div class="search-set">
                                    <div class="search-input d-flex align-items-center">
                                        <div id="custom-search-low" class="me-2"></div> <!-- DataTable input will be moved here -->
                                        <a href="#" class="btn btn-searchset">
                                            <i data-feather="search" class="feather-search"></i>
                                        </a>
                                    </div>
                                </div>
                                <!-- <div class="search-path">
                                    <a class="btn btn-filter" id="filter_search">
                                        <i data-feather="filter" class="filter-icon"></i>
                                        <span><img src="assets/img/icons/closes.svg" alt="img"></span>
                                    </a>
                                </div> -->
                                <div class="form-sort">
                                    <i data-feather="sliders" class="info-img"></i>
                                    <select class="select" id="sort-order-low">
                                        <option value="">Sort by</option>
                                        <option value="desc">Newest First</option>
                                        <option value="asc">Oldest First</option>
                                    </select>
                                </div>
                            </div>
                            <!-- /Filter -->
                            <div class="card" id="filter_inputs">
                                <div class="card-body pb-0">
                                    <div class="row">
                                      
                                        <div class="col-lg-3 col-sm-6 col-12">
                                            <div class="input-blocks">
                                                <i data-feather="zap" class="info-img"></i>
                                                <select class="select" name="categoryId"  id="categoryId">
                                                    <option value="">Choose Category</option>
    
                                                    @foreach($categories as $category)
                                                    <option
                                                        value="{{$category->id}}"
                                                    >
                                                        {{$category->name}}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                       
                                        <!-- <div class="col-lg-3 col-sm-6 col-12 ms-auto">
                                            <div class="input-blocks">
                                                <a class="btn btn-filters ms-auto" id="lowStockSearchBtn"> 
                                                    <i data-feather="search" class="feather-search"></i> Search 
                                                </a>
                                            </div>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                            <!-- /Filter -->
                            <div class="table-responsive">
                                <table class="table  datanew" id="low-stock-table">
                                    <thead>
                                        <tr>
                                            <!-- <th class="no-sort">
                                                <label class="checkboxs">
                                                    <input type="checkbox" id="select-all">
                                                    <span class="checkmarks"></span>
                                                </label>
                                            </th> -->
                                            <th>Store</th>
                                            <th>Product</th>
                                            <th>Category</th>
                                            <th>SKU</th>
                                            <th>Qty</th>
                                            <th>Qty Alert</th>
                                        </tr>
                                    </thead>
                                    
                                </table>
                                
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                    <div class="card table-list-card">
                        <div class="card-body">
                            <div class="table-top">
                                <div class="search-set">
                                    <div class="search-input d-flex align-items-center">
                                        <div id="custom-search-out" class="me-2"></div> <!-- DataTable input will be moved here -->
                                        <a href="#" class="btn btn-searchset">
                                            <i data-feather="search" class="feather-search"></i>
                                        </a>
                                    </div>
                                </div>
                                <!-- <div class="search-path">
                                    <a class="btn btn-filter" id="filter_search1">
                                        <i data-feather="filter" class="filter-icon"></i>
                                        <span><img src="assets/img/icons/closes.svg" alt="img"></span>
                                    </a>
                                </div> -->
                                <div class="form-sort">
                                    <i data-feather="sliders" class="info-img"></i>
                                    <select class="select" id="sort-order-out">
                                        <option value="">Sort by</option>
                                        <option value="desc">Newest First</option>
                                        <option value="asc">Oldest First</option>
                                    </select>
                                </div>
                            </div>
                            <!-- /Filter -->
                            <div class="card" id="filter_inputs">
                                <div class="card-body pb-0">
                                    <div class="row">
                                        <div class="col-lg-3 col-sm-6 col-12">
                                            <div class="input-blocks">
                                                <i data-feather="zap" class="info-img"></i>
                                                <select class="select" name="category"  id="category">
                                                    <option value="">Choose Category</option>
    
                                                    @foreach($categories as $category)
                                                    <option
                                                        value="{{$category->id}}"
                                                    >
                                                        {{$category->name}}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <!-- <div class="col-lg-3 col-sm-6 col-12 ms-auto">
                                            <div class="input-blocks">
                                                <a class="btn btn-filters ms-auto" id="outOfStockSearchBtn"> 
                                                    <i data-feather="search" class="feather-search"></i> Search 
                                                </a>
                                            </div>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                            <!-- /Filter -->
                            <div class="table-responsive">
                                <table class="table  datanew" id="out-stock-table" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <!-- <th class="no-sort">
                                                <label class="checkboxs">
                                                    <input type="checkbox" id="select-all">
                                                    <span class="checkmarks"></span>
                                                </label>
                                            </th> -->
                                            <th>Store</th>
                                            <th>Product</th>
                                            <th>Category</th>
                                            <th>SKU</th>
                                            <th>Qty</th>
                                            <th>Qty Alert</th>
                                        </tr>
                                    </thead>
                                    
                                </table>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-sm-12 col-md-12 col-xl-6 d-flex">
                <div class="card flex-fill">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Incoming stock</h5>
                    </div>
                   
                    <div class="card-body">
                        <div id="in-stock-level-dashboard" class="chart-set"></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-12 col-xl-6 d-flex">
                <div class="card flex-fill">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Outgoing stock</h5>
                    </div>
                    <div class="card-body">
                        <div id="out-stock-level-dashboard" class="chart-set"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-12 col-sm-12 col-12 d-flex">
                <div class="card flex-fill default-cover">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Current stock levels</h5>
                    </div>
                    <div class="card-body">
                        <div id="stock-analysis-dashboard" class="chart-set"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>





@endsection
@section('scripts')
<script>
    var incomingStockLabels = {!! json_encode(array_keys($incomingStockData)) !!};
    var incomingStockValues = {!! json_encode(array_values($incomingStockData)) !!};
    console.log(incomingStockLabels);
</script>
<script>
    $(document).ready(function() {
        console.log
        if($('#in-stock-level-dashboard').length > 0){
            var sCol = {
                chart: {
                    height: 350,
                    type: 'bar',
                    toolbar: { show: false }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded' 
                    },
                },
                dataLabels: { enabled: false },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                series: [{
                    name: 'Transfers',
                    data: incomingStockValues
                }],
                xaxis: {
                    categories: incomingStockLabels
                },
                yaxis: {
                    title: {
                        text: 'Qty (units)'
                    }
                },
                fill: { opacity: 1 },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val + " units"
                        }
                    }
                }
            };

            var chart = new ApexCharts(
                document.querySelector("#in-stock-level-dashboard"),
                sCol
            );

            chart.render();
        }
    });
</script>
<script>
    var outgoingTransferLabels = {!! json_encode(array_keys($salesData)) !!}; // Assuming all 3 share same months
    var salesData = {!! json_encode(array_values($salesData)) !!};
    var transferOutData = {!! json_encode(array_values($outgoingTransferData)) !!};
</script>
<script>
    $(document).ready(function () {
        if ($('#out-stock-level-dashboard').length > 0) {
            var sCol = {
                chart: {
                    height: 350,
                    type: 'bar',
                    toolbar: { show: false }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    },
                },
                dataLabels: { enabled: false },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                series: [{
                    name: 'Sales',
                    data: salesData
                }, {
                    name: 'Transfers',
                    data: transferOutData
                }],
                xaxis: {
                    categories: outgoingTransferLabels
                },
                yaxis: {
                    title: {
                        text: 'Qty (units)'
                    }
                },
                fill: { opacity: 1 },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val + " units"
                        }
                    }
                }
            };

            var chart = new ApexCharts(
                document.querySelector("#out-stock-level-dashboard"),
                sCol
            );

            chart.render();
        }
    });
</script>
<script>
    var stockAnalysisLabels = {!! json_encode(array_keys($stockAnalysisData)) !!};
    var stockAnalysisValues = {!! json_encode(array_values($stockAnalysisData)) !!};
</script>
<script>
    $(document).ready(function () {
        if ($("#stock-analysis-dashboard").length > 0) {
    var options = {
        series: [
            {
                name: "Stock Analysis",
                data: stockAnalysisValues,
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
            categories: stockAnalysisLabels,
        },
        yaxis: {
            min: 0,
            tickAmount: 5,
            labels: {
                formatter: function (val) {
                return (val / 1000).toFixed(2) + "K";
                },
            },
            forceNiceScale: true
        },
        legend: {
            position: "top",
            horizontalAlign: "left",
        },
    };

    var chart = new ApexCharts(
        document.querySelector("#stock-analysis-dashboard"),
        options
    );
    chart.render();
}

    });
</script>
<script>
    $(document).ready(function () {
        
        loadLowStockTable();
        loadOutOfStockTable();
    
        
        $('#categoryId, #sort-order-low').on('change', function () {
            loadLowStockTable();
        });
        $('#category, #sort-order-out').on('change', function () {
            loadOutOfStockTable();
        });
    
       
    
        function loadLowStockTable() {
            $('#low-stock-table').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    url: "{{ route('admin.real-time-stock') }}",
                    data: function (d) {
                        d.type = 'low';
                        d.category_id = $('#categoryId').val(); // for low stock
                        d.sort_order = $('#sort-order-low').val();
                    }
                },
                columns: [
                    { data: 'store', name: 'store' },
                    { data: 'product_info', name:'product_info' },
                    { data: 'category', name: 'category' },
                    { data: 'sku', name: 'sku' },
                    { data: 'quantity', name: 'quantity' },
                    { data: 'qty_alert', name: 'qty_alert' }
                ],
                pageLength:5,
                initComplete: function () {
                    let searchWrapper = $('#low-stock-table_filter');
                    let defaultSearchBox = searchWrapper.find('input');
                    $('#custom-search-low').empty();
                    $('#custom-search-low').append(defaultSearchBox);
                    defaultSearchBox.attr('placeholder', 'Search...');
                    searchWrapper.remove();
                }
            });
        }
    
        function loadOutOfStockTable() {
            $('#out-stock-table').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    url: "{{ route('admin.real-time-stock') }}",
                    data: function (d) {
                        d.type = 'out';
                        d.category_id = $('#category').val(); // for out of stock
                        d.sort_order = $('#sort-order-out').val();
                    }
                },
                columns: [
                { data: 'store', name: 'store' },
                    { data: 'product_info', name:'product_info' },
                    { data: 'category', name: 'category' },
                    { data: 'sku', name: 'sku' },
                    { data: 'quantity', name: 'quantity' },
                    { data: 'qty_alert', name: 'qty_alert' }
                ],
                pageLength:5,
                initComplete: function () {
                    let searchWrapper = $('#out-stock-table_filter');
                    let defaultSearchBox = searchWrapper.find('input');
                    $('#custom-search-out').empty();
                    $('#custom-search-out').append(defaultSearchBox);
                    defaultSearchBox.attr('placeholder', 'Search...');
                    searchWrapper.remove();
                }
            });
        }
    });
</script>





@endsection


