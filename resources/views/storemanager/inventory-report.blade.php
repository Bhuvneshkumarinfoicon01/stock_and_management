@extends('layouts.app_admin') @section('title') Dashbaord @endsection
@section('styles') @endsection @section('content')

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4>Stock Transfer</h4>
                    <h6>Manage your stock transfer</h6>
                </div>
            </div>
            <ul class="table-top-head">
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
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Refresh" id="refresh-page"><i
                            data-feather="rotate-ccw" class="feather-rotate-ccw"></i></a>
                </li>
                <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Collapse" id="collapse-header"><i
                            data-feather="chevron-up" class="feather-chevron-up"></i></a>
                </li>
            </ul>
        </div>

        <div class="card table-list-card">
            <div class="card-body">
                <div class="table-top">
                    <div class="search-set">
                        <div class="search-input d-flex align-items-center">
                            <div id="custom-search" class="me-2"></div> <!-- DataTable input will be moved here -->
                            <a href="#" class="btn btn-searchset">
                                <i data-feather="search" class="feather-search"></i>
                            </a>
                        </div>
                    </div>
                    <!-- <div class="search-path">
                        <div class="d-flex align-items-center">
                            <a class="btn btn-filter" id="filter_search">
                                <i
                                    data-feather="filter"
                                    class="filter-icon"
                                ></i>
                                <span
                                    ><img
                                        src="assets/img/icons/closes.svg"
                                        alt="img"
                                /></span>
                            </a>
                        </div>
                    </div> -->
                    <div class="form-sort">
                        <i data-feather="sliders" class="info-img"></i>
                        <select class="select" id="sort-order">
                            <option value="">Sort by Date</option>
                            <option value="desc">Newest</option>
                            <option value="asc">Oldest</option>
                        </select>
                    </div>
                </div>
                <!-- /Filter -->
                <div class="card" id="filter_inputs">
                    <div class="card-body pb-0">
                        <div class="row">
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="input-blocks">
                                    <i data-feather="archive" class="info-img"></i>
                                    @php
                                    $selectedStoreId = session('selected_store_id');
                                    @endphp
                                    <select id="storeSelect" class="select">
                                        <option value="">Store From</option>
                                        @foreach($stores as $store)
                                        @if($store->id != $selectedStoreId)
                                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- <div class="col-lg-3 col-sm-6 col-12">
                                <div class="input-blocks">
                                    <i data-feather="user" class="info-img"></i>
                                    <select class="select">
                                        <option>Store To</option>
                                        <option>Storename 1</option>
                                        <option>Storename 2</option>
                                        <option>Storename 3</option>
                                        <option>Storename 4</option>
                                    </select>
                                </div>
                            </div> -->
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
                </div>
                <!-- /Filter -->
                <div class="table-responsive">
                    <table class="table datanew" id="transfer-table">
                        <thead>
                            <tr>
                                <th>From Store</th>
                                <th>To Store</th>
                                <th>Product Name</th>
                                <th>Quantity transfered</th>
                                <th>Ref Number</th>
                                <th>Date</th>
                            </tr>
                        </thead>

                    </table>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 col-lg-6 col-sm-12 col-12">
                <div class="card flex-fill default-cover w-100 mb-4">
                    <div
                        class="d-flex justify-content-between align-items-center alert alert-solid-danger border border-danger">
                        <h4 class="card-title mb-0 text-fixed-white">
                            Out-Of-stock Products
                        </h4>
                        <div class="dropdown">
                            <a href="{{route('admin.outOfStock-view')}}"
                                class="view-all d-flex align-items-center text-white">

                                View All<i data-feather="arrow-right" class="feather-arrow-right text-white"></i>
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless best-seller low-stock">
                                <tbody>
                                    @foreach($outofstockProducts as $product)
                                    <tr>
                                        <td>
                                            <div class="product-info">
                                                <a href="#" class="product-img">
                                                    <img src="{{asset($product->images ? 'storage/'.$product->images : 'admin\assets\img\avatar\default-product.png')}}"
                                                        alt="product" />
                                                </a>
                                                <div class="info">
                                                    <a href="#">{{$product->product_name}}</a>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="head-text">
                                                <i class="fa-solid fa-arrow-trend-down"></i>{{$product->quantity}}
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

            <div class="col-md-12 col-lg-6 col-sm-12 col-12">
                <div class="card flex-fill default-cover w-100 mb-4">
                    <div
                        class="d-flex justify-content-between align-items-center alert alert-solid-primary border border-primary">
                        <h4 class="card-title mb-0 text-fixed-white">
                            Low-stock Products
                        </h4>
                        <div class="dropdown">
                            <a href="{{route('admin.lowOnStock-view')}}"
                                class="view-all d-flex align-items-center text-white">

                                View All<i data-feather="arrow-right" class="feather-arrow-right text-white"></i>
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
                                                    <img src="{{ asset('storage/'.$product->images) }}" alt="product" />
                                                </a>
                                                <div class="info">
                                                    <a href="#">{{$product->product_name}}</a>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="head-text">
                                                <i class="fa-solid fa-arrow-trend-down"></i>{{$product->quantity}}
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
    </div>
</div>

@endsection
@section('scripts')
<script>
    $(document).ready(function () {
        let table = $('#transfer-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.inventory-report') }}",
                data: function (d) {
                    d.date_from = $('#date_from').val();
                    d.date_to = $('#date_to').val();
                    d.store_id = $('#storeSelect').val();
                    d.order_type = $('#sort-order').val();
                }
            },
            columns: [
                { data: 'from_store', name: 'from_store' },
                { data: 'to_store', name: 'to_store' },
                { data: 'transfer_product_name', name: 'transfer_product_name' },
                { data: 'qty', name: 'qty' },
                { data: 'reference', name: 'reference' },
                { data: 'date', name: 'date' },
            ],
            pageLength: 10,
            initComplete: function () {
                let searchWrapper = $('#transfer-table_filter');
                let defaultSearchBox = searchWrapper.find('input');
                $('#custom-search').append(defaultSearchBox);
                defaultSearchBox.attr('placeholder', 'Search Products...');
                searchWrapper.remove();
            }
        });
        $('#searchBtn').on('click', function () {
            table.ajax.reload();
        });
        $('#sort-order').on('change', function () {
            table.ajax.reload();
        });

    });
</script>
@endsection