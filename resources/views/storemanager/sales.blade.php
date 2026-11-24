@extends('layouts.app_admin') @section('title') Dashbaord @endsection
@section('styles') @endsection @section('content')

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4>Sales</h4>
                    <h6>Manage Your Sales</h6>
                </div>
            </div>
            <ul class="table-top-head">
                <!-- <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Pdf"><img src="assets/img/icons/pdf.svg"
                            alt="img" /></a>
                </li>
                <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Excel"><img
                            src="assets/img/icons/excel.svg" alt="img" /></a>
                </li>
                <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Print"><i data-feather="printer"
                            class="feather-rotate-ccw"></i></a>
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
                                <span><img src="assets/img/icons/closes.svg" alt="img" /></span>
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
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="input-blocks">
                                    <div class="input-groupicon calender-input">
                                        <i data-feather="calendar" class="info-img"></i>
                                        <input type="text" class="from-date datetimepicker" placeholder="From Date" />
                                    </div>
                                </div>

                            </div>
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="input-blocks">
                                    <div class="input-groupicon calender-input">
                                        <i data-feather="calendar" class="info-img"></i>
                                        <input type="text" class="to-date datetimepicker" placeholder="To Date">
                                    </div>
                                </div>

                            </div>

                            <div class="col-lg-1 col-sm-6 col-12">
                                <div class="input-blocks">
                                    <a class="btn btn-filters ms-auto" id="search-btn">
                                        <i data-feather="search" class="feather-search"></i> Search
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Filter -->
                <div class="table-responsive">
                    <table class="table datanew sales-table" id="sales-data-table">
                        <thead>
                            <tr>
                                <!-- <th class="no-sort">
                                    <label class="checkboxs">
                                        <input type="checkbox" id="select-all" />
                                        <span class="checkmarks"></span>
                                    </label>
                                </th> -->
                                <th>Order Id</th>
                                <th>Date</th>
                                <th>Store Name</th>
                                <th>Customer Name</th>
                                <th>Qty</th>
                                <th>Paid</th>
                                <th>Due</th>
                                <th>Payment Status</th>
                                <th>Payment Type</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>

                    </table>
                </div>
            </div>
        </div>
        <!-- /product list -->
    </div>
</div>

<!-- details popup -->
@include('admin.partials.storeManagerPartials.sales-view')
<!-- /details popup -->

<!-- edit popup -->
<!-- /edit popup -->

@endsection
@section('scripts')
<script>
    $(document).ready(function () {
        let table = $('#sales-data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.sales') }}",
                data: function (d) {
                    d.from_date = $('.from-date').val();
                    d.to_date = $('.to-date').val();
                    d.order_type = $('#sort-order').val();
                }
            },
            columns: [
                { data: 'order_id', name: 'order_id' },
                { data: 'date', name: 'date' },
                { data: 'store_name', name: 'store.name' },
                { data: 'customer_name', name: 'customer.customer_name' },
                { data: 'quantity', name: 'quantity' },
                { data: 'total_price', name: 'total_price' },
                { data: 'due', name: 'due' },
                { data: 'status', name: 'status' },
                { data: 'payment_type', name: 'payment_type' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            pageLength: 10,
            drawCallback: function () {
                feather.replace();
            },
            initComplete: function () {
                let searchWrapper = $('#sales-data-table_filter');
                let defaultSearchBox = searchWrapper.find('input');
                $('#custom-search').append(defaultSearchBox);
                defaultSearchBox.attr('placeholder', 'Search orders...');
                searchWrapper.remove();
            }
        });

        $('#search-btn').on('click', function () {
            table.ajax.reload();
        });
        $('#sort-order').on('change', function () {
            table.ajax.reload();
        });
    });


</script>

<script>
    $(document).on('click', '.viewDetails', function () {

        var orderId = $(this).data('id'); // Get the order ID

        // Send AJAX request to fetch order details
        $.ajax({
            url: '{{ route("admin.sales_details") }}',  // Your route for viewing invoice
            type: 'GET',
            data: {
                invoiceId: orderId  // Send invoiceId as query parameter
            },
            success: function (response) {
                if (response.success) {
                    var order = response.data;
                    var store_name = response.store_name;
                    var stocks = response.stocks;
                    const productImagesMap = {};
                    stocks.forEach(stock => {
                        productImagesMap[stock.product_id] = stock.images;
                    });
                    const taxAmount = order.tax * order.subtotal_price / 100;
                    const discountAmount = order.discount * order.subtotal_price / 100;
                    const shippingAmount = order.shipping * order.subtotal_price / 100;



                    // Populate modal content
                    var modalContent = `
                        <div class="sales-details-items d-flex">
                           <div class="details-item" style="line-height: 1.5;">
                                <h6>Customer Info</h6>

                                <p style="margin: 2px 0;"><b>Customer Name:</b> ${order.customer.customer_name}</p>
                                <p style="margin: 2px 0;"><b>Customer Email:</b> ${order.customer.email || 'N/A'}</p>
                                <p style="margin: 2px 0;"><b>Customer Contact:</b>${order.customer.phone || 'N/A'}</p>
                                <p style="margin: 2px 0;"><b>Customer Address:</b> ${order.customer.address || 'N/A'}</p>
                            </div>
                            <div class="details-item">
                                <h6>Invoice Info</h6>
                                <p>
                                    <strong>Store:</strong> ${store_name}<br />
                                    <strong>Payment Status:</strong> ${order.status == 0 ? 'Unpaid' : 'Paid'}<br />
                                    <strong>Payment Method:</strong> ${order.payment_method}
                                </p>
                            </div>
                           
                        </div>
                        <h5 class="order-text">Order Summary</h5>
                        <div class="table-responsive no-pagination">
                            <table class="table datanew">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Qty</th>
                                        <th>Price($)</th>
                                        <th>Paid($)</th>
                                        <th>Due($)</th>
                                        <th>Payment Type</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${order.order_items.map(item => `
                                        <tr>
                                            <td>
                                                <div class="productimgname">
                                                    <a href="javascript:void(0);" class="product-img stock-img">
                                                        <img src="/storage/${productImagesMap[item.product_id]}" alt="${item.product_name}" />


                                                    </a>
                                                    <a href="javascript:void(0);">${item.product_name}</a>
                                                </div>
                                            </td>
                                            <td>${item.qty}</td>
                                            <td>$${item.price}</td>
                                            <td>$${item.qty * item.price}</td>
                                            <td>$0.00</td>
                                            <td>${order.payment_method}</td>    
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="row">
                            <div class="col-lg-6 ms-auto">
                                <div class="total-order w-100 max-widthauto m-auto mb-4">
                                    <ul>
                                        <li>
                                            <h4>Sub Total</h4>
                                            <h5>$${order.subtotal_price}</h5>
                                        </li>
                                        <li>
                                            <h4>Order Tax( ${order.tax || 0}% )</h4>
                                            <h5>$${taxAmount}</h5>
                                        </li>
                                        <li>
                                            <h4>Discount( ${order.discount || 0}% )</h4>
                                            <h5>-$${discountAmount}</h5>
                                        </li>
                                         <li>
                                            <h4>Shipping( ${order.shipping || 0}% )</h4>
                                            <h5>$${shippingAmount}</h5>
                                        </li>
                                        <li>
                                            <h4>Grand Total</h4>
                                            <h5>$${order.total_price}</h5>
                                        </li>
                                        <li>
                                            <h4>Paid</h4>
                                            <h5>$${order.total_price}</h5>
                                        </li>
                                        <li>
                                            <h4>Due</h4>
                                            <h5>$0.00</h5>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    `;

                    // Inject the content into the modal
                    $('#sales-details-new .card-body').html(modalContent);

                    // Show the modal
                    $('#sales-details-new').modal('show');
                } else {
                    alert('Invoice not found!');
                }
            },
            error: function () {
                alert('Error fetching order details.');
            }
        });


    });
</script>
@endsection