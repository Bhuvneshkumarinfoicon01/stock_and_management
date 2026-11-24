@extends('layouts.app_admin') @section('title') Dashbaord @endsection
@section('styles') @endsection @section('content')

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4>Stock Transfer</h4>
                    <h6>Manage your stock transfers</h6>
                </div>
            </div>
            <ul class="table-top-head">

                <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Excel"
                        href="{{route('admin.stock-transfers.export')}}"><img
                            src="https://dreamspos.dreamstechnologies.com/html/template/assets/img/icons/excel.svg"
                            alt="img" /></a>
                </li>
                <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Refresh" id="refresh-page"><i
                            data-feather="rotate-ccw" class="feather-rotate-ccw"></i></a>
                </li>
                <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Collapse" id="collapse-header"><i
                            data-feather="chevron-up" class="feather-chevron-up"></i></a>
                </li>
            </ul>
            @if (hasPermission('create', 'stock_transfer'))
            <div class="page-btn">
                <a href="#" class="btn btn-added" data-bs-toggle="modal" data-bs-target="#add-units"><i
                        data-feather="plus-circle" class="me-2"></i>Add New Transfer</a>
            </div>
            @endif
            <!-- <div class="page-btn import">
							<a href="#" class="btn btn-added color" data-bs-toggle="modal" data-bs-target="#view-notes"><i
								data-feather="download" class="me-2"></i>Import Transfer</a>
						</div> -->
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
							<a class="btn btn-filter" id="filter_search">
								<i data-feather="filter" class="filter-icon"></i>
								<span><img src="{{asset('admin/assets/img/icons/closes.svg')}}" alt="img"></span>
							</a>
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
                                    <i data-feather="archive" class="info-img"></i>
                                    <select id="filter-store-from" class="select">
                                        <option value="choose">From Store</option>
                                        @foreach($stores as $store)
                                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="input-blocks">
                                    <i data-feather="archive" class="info-img"></i>
                                    <select id="filter-store-to" class="select">
                                        <option value="choose">To Store</option>
                                        @foreach($stores as $store)
                                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-12">
                                <div class="input-blocks">
                                    <i data-feather="calendar" class="info-img"></i>
                                    <div class="input-groupicon">
                                        <input type="text" id="filter-date-from" class="datetimepicker"
                                            placeholder="Choose From Date" />

                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-12">
                                <div class="input-blocks">
                                    <i data-feather="calendar" class="info-img"></i>
                                    <div class="input-groupicon">
                                        <input type="text" id="filter-date-to" class="datetimepicker"
                                            placeholder="Choose To Date" />

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
                <div class="table-responsive datatable-scroll-container" id="stock-transfer-scroll-wrap">
                    <table class="table datanew" id="stock-transfer-table">
                        <thead>
                            <tr>
                                <!-- <th class="no-sort">
                                    <label class="checkboxs">
                                        <input
                                            type="checkbox"
                                            id="select-all"
                                        />
                                        <span class="checkmarks"></span>
                                    </label>
                                </th> -->
                                <th>From Store</th>
                                <th>To Store</th>
                                <th>Product Name</th>
                                <th>Quantity Transferred</th>
                                <th>Ref Number</th>
                                <th>Created Date</th>
                                <th class="no-sort">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <!-- /product list -->
    </div>
</div>

<!-- Add Stock -->
<div class="modal fade" id="add-units" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered stock-adjust-modal">
        <div class="modal-content">
            <div class="page-wrapper-new p-0">
                <div class="content">
                    <div class="modal-header border-0 custom-modal-header">
                        <div class="page-title">
                            <h4>Add Transfer</h4>
                        </div>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body custom-modal-body">
                        <form method="POST" action="{{ route('admin.stock-transfer.store') }}" id="transferForm">
                            @csrf
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="input-blocks">
                                        <label>Store From</label>
                                        <select name="store_from" id="store_from" class="form-control select">
                                            <option value="">Choose</option>
                                            @if(in_array($user->role_id, [1, 2]))
                                            @foreach ($stores as $store)
                                            <option value="{{ $store->id }}">
                                                {{ $store->name }}
                                            </option>
                                            @endforeach
                                            @else
                                            <option value="{{ $user->selected_store_id }}">
                                                {{ optional($stores->firstWhere('id', $user->selected_store_id))->name
                                                }}
                                            </option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="input-blocks">
                                        <label>Store To</label>
                                        <select name="store_to" id="store_to" class="form-control select">
                                            <option value="">Choose</option>
                                            @foreach ($stores as $store)
                                            <option value="{{ $store->id }}">
                                                {{ $store->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="input-blocks">
                                        <label>Responsible Person From:</label>
                                        <input name="rperson_from" type="text" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="input-blocks">
                                        <label>Responsible Person To:</label>
                                        <input name="rperson_to" type="text" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="input-blocks">
                                        <label>Choose Arrival Date:</label>
                                        <div class="input-blocks">
                                            <i data-feather="calendar" class="info-img"></i>
                                            <div class="input-groupicon">
                                                <input type="text" name="arrival_date" class="datetimepicker"
                                                    placeholder="Choose Date" required />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="input-blocks search-form mb-3">
                                        <label>Product</label>
                                        <input type="text" id="product_search" class="form-control"
                                            placeholder="Search Product by Name or SKU" />
                                        <ul id="product_list" class="list-group mt-2"></ul>

                                        <i data-feather="search" class="feather-search"></i>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="modal-body-table">
                                        <div class="table-responsive">
                                            <table class="table datanew">
                                                <thead>
                                                    <tr>
                                                        <th>Product</th>
                                                        <th>SKU</th>
                                                        <th>Category</th>
                                                        <th>Qty</th>

                                                        <th class="no-sort">
                                                            Action
                                                        </th>
                                                        <!-- <th>
                                                            <div
                                                                class="edit-delete-action"
                                                            >
                                                                <a
                                                                    class="me-2 p-2 add-row-btn"
                                                                    href="javascript:void(0);"
                                                                >
                                                                    <i
                                                                        data-feather="plus-circle"
                                                                        class="plus-circle"
                                                                    ></i>
                                                                </a>
                                                            </div>
                                                        </th> -->
                                                    </tr>
                                                </thead>
                                                <tbody id="product-tbody">
                                                    <!-- Template Row (Hidden by Default) -->
                                                    <tr id="product-row-template" class="d-none">
                                                        <td>
                                                            <input type="hidden" name="product_id[]"
                                                                class="form-control product-id" />
                                                            <input type="text" name="product_name[]"
                                                                class="form-control product-name" readonly />
                                                        </td>
                                                        <td>
                                                            <div class="productimgname">
                                                                <input name="product_name[]" type="text"
                                                                    class="form-control product-name" readonly />
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="input-blocks sku">
                                                                <input name="sku[]" type="text"
                                                                    class="form-control product-sku" readonly />
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <select name="category_id[]" class="select product-category"
                                                                disabled>
                                                                <option>
                                                                    Choose
                                                                </option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <div class="product-quantity">
                                                                <input name="qty[]" type="number"
                                                                    class="form-control product-qty" value="1"
                                                                    min="1" />
                                                            </div>
                                                        </td>

                                                        <td class="action-table-data">
                                                            <div class="edit-delete-action">
                                                                <a class="confirm-text p-2 delete-row"
                                                                    href="javascript:void(0);">
                                                                    <i data-feather="trash-2"
                                                                        class="feather-trash-2"></i>
                                                                </a>
                                                            </div>
                                                        </td>

                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="input-blocks search-form mb-0">
                                        <label>Notes</label>
                                        <textarea name="notes" class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer-btn">
                                <button type="button" class="btn btn-cancel me-2" data-bs-dismiss="modal">
                                    Cancel
                                </button>
                                <button type="submit" class="btn btn-submit">
                                    Create
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Add Stock -->

<!-- Edit Stock -->
<div class="modal fade" id="edit-units">
    <div class="modal-dialog modal-dialog-centered stock-adjust-modal">
        <div class="modal-content">
            <div class="page-wrapper-new p-0">
                <div class="content">
                    <div class="modal-header border-0 custom-modal-header">
                        <div class="page-title">
                            <h4>Edit Transfer</h4>
                        </div>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body custom-modal-body">
                        <form id="edit-transfer-form" method="POST" action="{{ route('admin.stock-transfer.update') }}">
                            @csrf
                            <input type="hidden" id="edit-transfer-id" />
                            <div class="row">
                                <!-- Store From -->
                                <div class="col-lg-6">
                                    <div class="input-blocks">
                                        <label>Store From</label>
                                        <select id="edit-store-from" class="select form-control" name="store_from"
                                            disabled>
                                            <option value="">Choose</option>
                                            @foreach ($stores as $store)
                                            <option value="{{ $store->id }}">
                                                {{ $store->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="store_from" id="edit-store-from-hidden">
                                    </div>
                                </div>

                                <!-- Store To -->
                                <div class="col-lg-6">
                                    <div class="input-blocks">
                                        <label>Store To</label>
                                        <select id="edit-store-to" class="select form-control" name="store_to" disabled>
                                            <option value="">Choose</option>
                                            @foreach ($stores as $store)
                                            <option value="{{ $store->id }}">
                                                {{ $store->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="store_to" id="edit-store-to-hidden">
                                    </div>
                                </div>

                                <!-- Responsible Person From -->
                                <div class="col-lg-6">
                                    <div class="input-blocks">
                                        <label>Responsible Person From:</label>
                                        <input type="text" class="form-control" id="edit-rperson-from"
                                            name="rperson_from" />
                                    </div>
                                </div>

                                <!-- Responsible Person To -->
                                <div class="col-lg-6">
                                    <div class="input-blocks">
                                        <label>Responsible Person To:</label>
                                        <input type="text" class="form-control" id="edit-rperson-to"
                                            name="rperson_to" />
                                    </div>
                                </div>

                                <!-- Arrival Date -->
                                <div class="col-lg-6">
                                    <div class="input-blocks">
                                        <label>Choose Arrival Date:</label>
                                        <div class="input-blocks">
                                            <i data-feather="calendar" class="info-img"></i>
                                            <div class="input-groupicon">
                                                <input type="text" name="arrival_date" id="edit-arrival-date"
                                                    class="datetimepicker" placeholder="Choose Date" required />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Reference No (Disabled) -->
                                <div class="col-lg-12">
                                    <div class="input-blocks">
                                        <label>Reference No</label>
                                        <input type="text" class="form-control" id="edit-reference-no"
                                            name="reference_no" readonly />
                                    </div>
                                </div>

                                <!-- Products Table -->
                                <div class="col-lg-12" style="width:100%;">
                                    <div class="modal-body-table">
                                        <div class="table-responsive">
                                            <table class="table datanew">
                                                <thead>
                                                    <tr>
                                                        <th>Product</th>
                                                        <th>SKU</th>
                                                        <th>Category</th>
                                                        <th>Qty</th>
                                                        <th>Action</th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="edit-products-table">
                                                    <!-- Dynamically load product rows here -->


                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Notes -->
                                <div class="col-lg-12">
                                    <div class="input-blocks search-form mb-0">
                                        <label>Notes</label>
                                        <textarea class="form-control" id="edit-notes" name="notes"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Footer -->
                            <div class="modal-footer-btn">
                                <button type="button" class="btn btn-cancel me-2" data-bs-dismiss="modal">
                                    Cancel
                                </button>
                                <button type="submit" class="btn btn-submit">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Edit Stock -->

@endsection @section('scripts') @if(session('success'))
<script>
    document.addEventListener("DOMContentLoaded", function () {
        Swal.fire({
            title: "Success!",
            text: "{{ session('success') }}",
            icon: "success",
            confirmButtonText: "OK",
        });
    });
</script>
@endif
<script>
    $(document).ready(function () {
        let table = $('#stock-transfer-table').DataTable(
            DatatablePagination.applyFixedHeader({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{ route('admin.stocktransfers.index') }}",
                // dataType: 'json',
                dataType: 'json',
                data: function (d) {
                    d.store_from = $('#filter-store-from').val();
                    d.store_to = $('#filter-store-to').val();
                    d.order_type = $('#sort-order').val();
                    d.date_from = $('#filter-date-from').val();
                    d.date_to = $('#filter-date-to').val();
                },
                error: function (xhr, error, thrown) {
                    console.error('Stock transfer DataTable AJAX error:', {
                        status: xhr.status,
                        responseText: xhr.responseText,
                        error: error,
                        thrown: thrown
                    });
                }
            },
            columns: [
                { data: 'store_from', name: 'store_from' },
                { data: 'store_to', name: 'store_to' },
                { data: 'product_name', name: 'product_name' },
                { data: 'qty_transfered', name: 'qty' },
                { data: 'reference_no', name: 'reference_no' },
                { data: 'created_date', name: 'created_date' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false },
            ],
            pageLength: 10,
            drawCallback: function () {
                feather.replace();
            },
            initComplete: function () {
                let searchWrapper = $('#stock-transfer-table_filter');
                let defaultSearchBox = searchWrapper.find('input');
                $('#custom-search').append(defaultSearchBox);
                defaultSearchBox.attr('placeholder', 'Search...');
                searchWrapper.remove();
            }
        }, {}, '#stock-transfer-scroll-wrap')
        );


        $('#filter-store-to').on('change', function () {
            table.ajax.reload();
        });

        $('#filter-store-from').on('change', function () {
            table.ajax.reload();
        });
        $('#sort-order').on('change', function () {
            table.ajax.reload();
        });

        $('#searchBtn').on('click', function () {
            // Reload DataTable with the current filter values
            table.ajax.reload();
        });



    });


</script>

<script>


    $("#product_search").on("keyup", function () {
        let query = $(this).val();
        let storeId = $("#store_from").val();

        if (storeId === "") {
            Swal.fire({
                icon: "warning",
                title: "Select Store!",
                text: "Please select a store first.",
            });
            return;
        }

        if (query.length > 1) {
            fetchProducts(storeId, query);
        } else {
            $("#product_list").empty();
        }
    });

    function fetchProducts(storeId, query) {
        $.ajax({
            url: "{{ route('admin.get-products') }}",
            method: "GET",
            data: { store_id: storeId, query: query },
            success: function (response) {
                $("#product_list").empty();

                if (response.length === 0) {
                    $("#product_list").append(
                        '<li class="list-group-item">No products found</li>'
                    );
                } else {
                    $.each(response, function (index, product) {
                        $("#product_list").append(`
                        <li class="list-group-item product-item" 
                            data-id="${product.product_id}"  
                            data-name="${product.product_name}" 
                            data-sku="${product.sku}" 
                            data-category="${product.category_name}" 
                            data-qty="${product.total_quantity}">
                            ${product.product_name} - SKU: ${product.sku} - Qty: ${product.total_quantity}
                        </li>
                    `);
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: "error",
                    title: "Error!",
                    text: "Failed to load products.",
                });
            },
        });
    }

    $(document).on("click", ".product-item", function () {
        let productId = $(this).data("id");
        let productName = $(this).data("name");
        let sku = $(this).data("sku");
        let category = $(this).data("category");
        let availableQty = $(this).data("qty");

        addProductToTable(productId, productName, sku, category, availableQty);
        $("#product_list").empty();
        $("#product_search").val("");
    });

    function addProductToTable(
        productId,
        productName,
        sku,
        category,
        availableQty
    ) {
        let isDuplicate = false;
        $("#product-tbody tr").each(function () {
            let existingProductId = $(this)
                .find('input[name="product_id[]"]')
                .val();
            if (existingProductId == productId) {
                isDuplicate = true;
                return false;
            }
        });

        if (isDuplicate) {
            Swal.fire({
                icon: "warning",
                title: "Duplicate Product!",
                text: "This product is already added to the list.",
            });
            return; // Stop further execution if duplicate found
        }

        // Add product row if no duplicate found
        let newRow = `
    <tr>
        <td>
            <input type="hidden" name="product_id[]" value="${productId}" />
            <span>${productName}</span>
        </td>
        <td><span>${sku}</span></td>
        <td><span>${category}</span></td>
        <td>
            <input
                type="number"
                name="qty[]"
                class="form-control product-qty"
                value="${availableQty > 0 ? 1 : 0}"
                min="1"
                max="${availableQty}"  
                data-available="${availableQty}"  
            />
            <small class="available-qty">Available Qty: ${availableQty}</small>
        </td>
        <td>
            <a href="javascript:void(0);" class="btn btn-danger btn-sm delete-row">Delete</a>
        </td>
    </tr>
    `;

        $("#product-tbody").append(newRow); // Append new row only if no duplicate
    }


    $(document).ready(function () {
        $(document).on("input", ".product-qty", function () {
            let enteredQty = parseInt($(this).val());
            let availableQty = parseInt($(this).data("available"));
            if (enteredQty > availableQty) {
                Swal.fire({
                    icon: "error",
                    title: "Quantity Exceeded!",
                    text: `Available quantity is only ${availableQty}.`,
                });

                $(this).val(availableQty);
            }
        });
    });

    $(document).on("click", ".delete-row", function () {
        $(this).closest("tr").remove();
    });
    $("#transferForm").on("submit", function (e) {
        e.preventDefault(); // Prevent default form submission

        // Serialize form data
        let formData = $(this)
            .find(":not(#product-row-template :input)") // Ignore the hidden product row template
            .serializeArray();

        // console.log("Form Data:", formData); // Debugging - Optional
        formData.forEach(function (item) {
            console.log(`Name: ${item.name}, Value: ${item.value}`);
        });


        let referenceNo =
            Date.now().toString().slice(-10) +
            Math.floor(Math.random() * 90 + 10);
        Swal.fire({
            title: "Confirm Stock Transfer",
            html: `
        <strong>Reference No:</strong> ${referenceNo}<br>
        <strong>Store From:</strong> ${$(
                "#store_from option:selected"
            ).text()}<br>
        <strong>Store To:</strong> ${$("#store_to option:selected").text()}<br>
        <strong>Responsible Person (From):</strong> ${formData.filter((item) => item.name === "rperson_from")[0]?.value
                }<br>
        <strong>Responsible Person (To):</strong> ${formData.filter((item) => item.name === "rperson_to")[0]?.value
                }<br>
        <strong>Notes:</strong> ${formData.filter((item) => item.name === "notes")[0]?.value || "N/A"
                }<br><br>
        <strong>Arrival Date:</strong> ${formData.filter((item) => item.name === "arrival_date")[0]?.value || "N/A"
                }<br><br>
        <strong>Products:</strong><br>
        ${formData
                    .filter((item) => item.name === "product_id[]")
                    .map((item, index) => {
                        let qty = formData.filter(
                            (q) =>
                                q.name === "qty[]" &&
                                formData.indexOf(q) === formData.indexOf(item) + 1
                        )[0]?.value;

                        return `Product ID: ${item.value}, Qty: ${qty}<br>`;
                    })
                    .join("")}
    `,
            icon: "info",
            showCancelButton: true,
            confirmButtonText: "Confirm Transfer",
            cancelButtonText: "Cancel",
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    url: "{{ route('admin.stock-transfer.store') }}", // Correct route name
                    method: "POST",
                    data: formData.concat({
                        name: "reference_no",
                        value: referenceNo, // Include reference number
                    }),

                    success: function (response) {
                        console.log(" AJAX Success - Response:", response);
                        if (response.success) {
                            Swal.fire({
                                icon: "success",
                                title: "Transfer Successful!",
                                text: `Reference No: ${response.reference_no}`,
                            }).then(() => {
                                window.location.reload(); // Reload after success
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Error!",
                                text: response.message,
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: "error",
                            title: "Error!",
                            text: "Failed to create stock transfer.",
                        });
                    },
                });
            }
        });
    });

    //SCRIPT FOR EDIT MODAL

    $(document).ready(function () {
        // Open Edit Modal
        $(document).on("click", ".edit-btn", function () {
            // $(".edit-btn").on("click", function () {
            var id = $(this).data("id"); // Get the ID of the transfer
            var url = "{{ route('admin.stock-transfer.edit', ':id') }}";
            url = url.replace(":id", id);


            // Clear previous data before loading new
            $("#edit-transfer-form")[0].reset();
            $("#edit-products-table").html("");

            // Fetch Transfer Data using AJAX
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    if (response.success) {
                        var data = response.data;
                        var availableQuantities = response.availableQuantities; // Save globally
                        window.availableQuantities = availableQuantities;

                        // Populate Store Dropdowns
                        $("#edit-store-from").val(data.store_from).trigger("change");
                        $("#edit-store-to").val(data.store_to).trigger("change");
                        $("#edit-store-from-hidden").val(data.store_from);
                        $("#edit-store-to-hidden").val(data.store_to);
                        // Populate Responsible Persons
                        $("#edit-rperson-from").val(data.rperson_from);
                        $("#edit-rperson-to").val(data.rperson_to);
                        $("#edit-arrival-date").val(data.arrival_date);
                        // Populate Reference No
                        $("#edit-reference-no").val(data.reference_no);
                        $("#edit-notes").val(data.notes);

                        // Populate Products Table

                        var productRows = "";
                        data.items.forEach(function (item) {
                            var availableQty = response.availableQuantities[item.product_id];
                            var category = item.category_name;
                            // Check if product already exists in the table
                            var existingRow = $('#edit-products-table').find('tr[data-product-id="' + item.product_id + '"]');
                            if (existingRow.length === 0) {
                                // Only add product if it doesn't already exist in the table
                                productRows += `
                                        <tr data-product-id="${item.product_id}">
                                            <td>${item.product_name}</td>
                                            <td>${item.sku}</td>
                                            <td>${category}</td>
                                             <td>
                                                <input type="number" class="form-control qty-input" name="qty[]" min="1" max="${availableQty}" value="${item.qty}" />
                                                <small class="available-qty">Available Qty: ${availableQty}</small>  
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);" class="delete-row" data-id="${item.id}">
                                                    <i data-feather="trash-2" class="feather-trash-2"></i>
                                                </a>
                                            </td>
                                            <td><input type="hidden" name="product_id[]" value="${item.product_id}" /></td>
                                            <td><input type="hidden" name="product_name[]" value="${item.product_name}" /></td>
                                            <td><input type="hidden" name="sku[]" value="${item.sku}" /></td>
                                            <td><input type="hidden" name="category_id[]" value="${item.category_id}" /></td>
                                            
                                        </tr>
                                    `;
                            }
                        });
                        $("#edit-products-table").html(productRows);
                        feather.replace(); // Reinitialize feather icons


                    } else {
                        swal.fire({
                            icon: "error",
                            title: "Error!",
                            text: response.message,
                        });
                    }
                },
                error: function () {
                    swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: "Failed to load transfer data.",
                    });
                },
            });
        });
    });

    // Listen for changes in quantity input fields
    $(document).on("change", ".qty-input", function () {
        let $row = $(this).closest('tr');
        let productId = $row.data('product-id');
        let enteredQty = parseInt($(this).val());
        let availableQty = parseInt(window.availableQuantities[productId]);

        if (enteredQty > availableQty) {
            $(this).val(availableQty); // Reset to max
            Swal.fire({
                icon: "warning",
                title: "Limit Exceeded",
                text: "Entered quantity exceeds available stock.",
            });
        }
    });




    //SUBMIT THE EDITTED DATA
    $("#edit-units form").submit(function (e) {
        e.preventDefault();

        let formData = $(this).serializeArray(); // Gather standard form fields
        $.ajax({
            url: "{{ route('admin.stock-transfer.update') }}",
            type: "POST",
            data: formData,
            success: function (response) {
                if (response.success) {
                    Swal.fire({
                        icon: "success",
                        title: "Success!",
                        text: response.message,
                    }).then(() => {
                        window.location.reload(); // Reload the page after success
                    });

                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: response.message,
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: "error",
                    title: "Error!",
                    text: "Failed to update transfer.",
                });
            },
        });
    });
    // Delete Stock Transfer
    $(document).on('click', '.delete-transfer', function () {
        let id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "This will permanently delete the stock transfer!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3E7B27',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('admin.stock-transfer-delete') }}",
                    type: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: id
                    },
                    success: function (res) {
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: res.message,
                            }).then(() => {
                                window.location.reload();
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to delete stock transfer.',
                        });
                    }
                });
            }
        });
    });

</script>












@endsection