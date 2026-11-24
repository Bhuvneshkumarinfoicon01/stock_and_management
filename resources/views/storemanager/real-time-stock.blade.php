@extends('layouts.app_admin') @section('title') Dashbaord @endsection
@section('styles') @endsection @section('content')

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title me-auto">
                <h4>Stock Real Time Updates</h4>
            </div>
            <ul class="table-top-head low-stock-top-head">
                <!-- <li>
                    <a
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="Pdf"
                        ><img src="assets/img/icons/pdf.svg" alt="img"
                    /></a>
                </li>
                <li>
                    <a
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="Excel"
                        ><img src="assets/img/icons/excel.svg" alt="img"
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
                    <a
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="Refresh"
                        id="refresh-page"
                        ><i
                            data-feather="rotate-ccw"
                            class="feather-rotate-ccw"
                        ></i
                    ></a>
                </li>
                <li>
                    <a
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="Collapse"
                        id="collapse-header"
                        ><i
                            data-feather="chevron-up"
                            class="feather-chevron-up"
                        ></i
                    ></a>
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
    </div>
</div>

@endsection
@section('scripts')
<script>
   
    // // For Low Stock
    /*$('.btn-filters').eq(0).on('click', function() {
        var categoryId = $('#categoryId').val();
        
        $.ajax({
            url: "{{ route('admin.low-stock.filter') }}",
            method: "GET",
            data: { category_id: categoryId },
            success: function(response) {
                $('#pills-home tbody').html(response.html);
            }
        });
    });

    // For Out of Stock
    $('.btn-filters').eq(1).on('click', function() {
        var categoryId = $('#category').val();

        $.ajax({
            url: "{{ route('admin.out-of-stock.filter') }}",
            method: "GET",
            data: { category: categoryId },
            success: function(response) {
                $('#pills-profile tbody').html(response.html);
            }
        });
    });*/
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
            pageLength:10,
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
