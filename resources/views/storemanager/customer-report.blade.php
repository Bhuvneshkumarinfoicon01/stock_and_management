@extends('layouts.app_admin') @section('title') Dashbaord @endsection
@section('styles') @endsection @section('content')

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4>Customer Reports</h4>
                </div>
            </div>
            <ul class="table-top-head">
                <!-- <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="pdf"><img src="assets/img/icons/pdf.svg"
                            alt="img"></a>
                </li>
                <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="excel"><img
                            src="assets/img/icons/excel.svg" alt="img"></a>
                </li>
                <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="print"><img
                            src="assets/img/icons/printer.svg" alt="img"></a>
                </li> -->
                <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="rotate" id="refresh-page"><i
                            data-feather="rotate-ccw" class="feather-rotate-ccw"></i></a>
                </li>
                <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="up"><i data-feather="chevron-up"
                            class="feather-chevron-up"></i></a>
                </li>
            </ul>
        </div>


        <!-- /product list -->
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
                <!-- /Filter -->
                <div class="card" id="filter_inputs">
                    <div class="card-body pb-0">
                        <div class="row">
                            <!-- <div class="col-lg-2 col-sm-6 col-12">
                                <div class="input-blocks">
                                    <i data-feather="stop-circle" class="info-img"></i>
                                    <select class="select">
                                        <option>Choose Status</option>
                                        <option>Completed</option>
                                        <option>Incompleted</option>
                                    </select>
                                </div>
                            </div> -->
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="input-blocks">
                                    <i data-feather="stop-circle" class="info-img"></i>
                                    <select id="payment-method-filter" class="select">
                                        <option value="">Choose Payment Method</option>
                                        @foreach ($payment_method as $method)
                                        <option value="{{ $method }}">{{ $method }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- <div class="col-lg-3 col-sm-6 col-12">
                                <div class="input-blocks">
                                    <a class="btn btn-filters ms-auto"> <i data-feather="search"
                                            class="feather-search"></i> Search </a>
                                </div>
                            </div> -->
                        </div>
                    </div>
                </div>
                <!-- /Filter -->
                <div class="table-responsive">
                    <table class="table datanew" id="customer-report-table">
                        <thead>
                            <tr>
                                <th>Customer ID</th>
                                <th>Customer Name</th>
                                <th>Amount</th>
                                <th>Paid</th>
                                <th>Due Amount</th>
                                <th>Payment Status</th>
                                <th>Payment Method</th>
                            </tr>
                        </thead>

                    </table>
                </div>
            </div>
        </div>
        <!-- /product list -->



        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4>Purchase History Tracking</h4>
                </div>
            </div>
        </div>

        <div class="card table-list-card">
            <div class="card-body">
                <div class="table-top">
                    <div class="search-set">
                        <div class="search-input d-flex align-items-center">
                            <div id="custom-search-second" class="me-2"></div>
                            <!-- DataTable input will be moved here -->
                            <a href="#" class="btn btn-searchset">
                                <i data-feather="search" class="feather-search"></i>
                            </a>
                        </div>
                    </div>

                    <div class="form-sort">
                        <i data-feather="sliders" class="info-img"></i>
                        <select class="select" id="sort-order-second">
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


                        </div>
                    </div>
                </div>
                <!-- /Filter -->
                <div class="table-responsive">
                    <table class="table  datanew" id="purchase-history-table">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Sold Amount</th>
                                <th>Sold Qty</th>
                                <th>Instock Qty</th>
                            </tr>
                        </thead>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>




@endsection
@section('scripts')

<script>
    $(document).ready(function () {
        let table = $('#customer-report-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.customer-report') }}",
                data: function (d) {
                    d.method = $('#payment-method-filter').val();
                    d.order_type = $('#sort-order').val();
                }
            },
            columns: [
                { data: 'customer_id', name: 'customer_id' },
                { data: 'customer_name', name: 'customer_name' },
                { data: 'amount', name: 'total_price' },
                { data: 'paid', name: 'total_price' },
                { data: 'due', name: 'due', orderable: false, searchable: false },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'payment_method', name: 'payment_method', orderable: false },

            ],
            pageLength: 10,
            initComplete: function () {
                let searchWrapper = $('#customer-report-table_filter');
                let defaultSearchBox = searchWrapper.find('input');
                $('#custom-search').append(defaultSearchBox);
                defaultSearchBox.attr('placeholder', 'Search Customers...');
                searchWrapper.remove();
                defaultSearchBox.on('keyup', function () {
                    table.search(this.value).draw();
                });
            }
        });
        $('#payment-method-filter').on('change', function () {
            table.ajax.reload();
        });
        $('#sort-order').on('change', function () {
            table.ajax.reload();
        });

        let secondTable = $('#purchase-history-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.customer-report-sold-products') }}",
                data: function (d) {
                    d.order_type = $('#sort-order-second').val();
                }
            },
            columns: [
                { data: 'product_info', name: 'product_name' },
                { data: 'sold_amount', name: 'price' },
                { data: 'sold_qty', name: 'qty', orderable: false },
                { data: 'instock_qty', name: 'stock.quantity', orderable: false },

            ],
            pageLength: 10,
            initComplete: function () {
                let searchWrapper = $('#purchase-history-table_filter');
                let defaultSearchBox = searchWrapper.find('input');
                $('#custom-search-second').append(defaultSearchBox);
                defaultSearchBox.attr('placeholder', 'Search Products...');
                searchWrapper.remove();
                defaultSearchBox.on('keyup', function () {
                    secondTable.search(this.value).draw();
                });
            }
        });
        $('#sort-order-second').on('change', function () {
            secondTable.ajax.reload();  // This reloads the table data with the new order
        });

    });
</script>
@endsection