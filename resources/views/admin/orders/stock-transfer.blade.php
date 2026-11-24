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
                <li>
                    <a
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="Pdf"
                        ><img
                            src="https://dreamspos.dreamstechnologies.com/html/template/assets/img/icons/pdf.svg"
                            alt="img"
                    /></a>
                </li>
                <li>
                    <a
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="Excel"
                        ><img
                            src="https://dreamspos.dreamstechnologies.com/html/template/assets/img/icons/excel.svg"
                            alt="img"
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
                </li>
                <li>
                    <a
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="Refresh"
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
            <div class="page-btn">
                <a
                    href="#"
                    class="btn btn-added"
                    data-bs-toggle="modal"
                    data-bs-target="#add-units"
                    ><i data-feather="plus-circle" class="me-2"></i>Add New</a
                >
            </div>
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
                        <div class="search-input">
                            <a href="#" class="btn btn-searchset"
                                ><i
                                    data-feather="search"
                                    class="feather-search"
                                ></i
                            ></a>
                        </div>
                    </div>
                    <div class="search-path">
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
                    </div>
                    <div class="form-sort">
                        <i data-feather="sliders" class="info-img"></i>
                        <select class="select">
                            <option>Sort by Date</option>
                            <option>Newest</option>
                            <option>Oldest</option>
                        </select>
                    </div>
                </div>
                <!-- /Filter -->
                <div class="card" id="filter_inputs">
                    <div class="card-body pb-0">
                        <div class="row">
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="input-blocks">
                                    <i
                                        data-feather="archive"
                                        class="info-img"
                                    ></i>
                                    <select class="select">
                                        <option>Store From</option>
                                        <option>Storename 1</option>
                                        <option>Storename 2</option>
                                        <option>Storename 3</option>
                                        <option>Storename 4</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-12">
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
                            </div>
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="input-blocks">
                                    <i
                                        data-feather="calendar"
                                        class="info-img"
                                    ></i>
                                    <div class="input-groupicon">
                                        <input
                                            type="text"
                                            class="datetimepicker"
                                            placeholder="Choose Date"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-12 ms-auto">
                                <div class="input-blocks">
                                    <a class="btn btn-filters ms-auto">
                                        <i
                                            data-feather="search"
                                            class="feather-search"
                                        ></i>
                                        Search
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Filter -->
                <div class="table-responsive">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th class="no-sort">
                                    <label class="checkboxs">
                                        <input
                                            type="checkbox"
                                            id="select-all"
                                        />
                                        <span class="checkmarks"></span>
                                    </label>
                                </th>
                                <th>From Store</th>
                                <th>To Store</th>
                                <th>No of Products</th>
                                <th>Quantity transfered</th>
                                <th>Ref Number</th>
                                <th>Date</th>
                                <th class="no-sort">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <label class="checkboxs">
                                        <input type="checkbox" />
                                        <span class="checkmarks"></span>
                                    </label>
                                </td>
                                <td>Storename 1</td>
                                <td>Storename 2</td>
                                <td>04</td>
                                <td>14</td>
                                <td>#145445</td>
                                <td>25 Jul 2023</td>
                                <td class="action-table-data">
                                    <div class="edit-delete-action">
                                        <a
                                            class="me-2 p-2"
                                            href="#"
                                            data-bs-toggle="modal"
                                            data-bs-target="#edit-units"
                                        >
                                            <i
                                                data-feather="edit"
                                                class="feather-edit"
                                            ></i>
                                        </a>
                                        <a
                                            class="confirm-text p-2"
                                            href="javascript:void(0);"
                                        >
                                            <i
                                                data-feather="trash-2"
                                                class="feather-trash-2"
                                            ></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            
                        </tbody>
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
                        <button
                            type="button"
                            class="close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        >
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body custom-modal-body">
                        <form id="transferForm" method="POST">
                            @csrf
                            <div class="row">
                                <!-- Store From -->
                                <div class="col-lg-6">
                                    <div class="input-blocks">
                                        <label>Store From</label>
                                        <select
                                            id="store_from"
                                            name="store_from"
                                            class="form-control store-dropdown"
                                        >
                                            <option value="">Choose</option>
                                            @foreach ($stores as $store)
                                            <option value="{{ $store->id }}">
                                                {{ $store->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Store To -->
                                <div class="col-lg-6">
                                    <div class="input-blocks">
                                        <label>Store To</label>
                                        <select
                                            id="store_to"
                                            name="store_to"
                                            class="form-control store-dropdown"
                                        >
                                            <option value="">Choose</option>
                                            @foreach ($stores as $store)
                                            <option value="{{ $store->id }}">
                                                {{ $store->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Responsible Person From -->
                                <div class="col-lg-6">
                                    <div class="input-blocks">
                                        <label>Responsible Person From:</label>
                                        <input
                                            type="text"
                                            name="rperson_from"
                                            class="form-control"
                                            required
                                        />
                                    </div>
                                </div>

                                <!-- Responsible Person To -->
                                <div class="col-lg-6">
                                    <div class="input-blocks">
                                        <label>Responsible Person To:</label>
                                        <input
                                            type="text"
                                            name="rperson_to"
                                            class="form-control"
                                            required
                                        />
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6 col-12">
                                    <label>Choose Arrival Date:</label>
                                    <div class="input-blocks">
                                        <i
                                            data-feather="calendar"
                                            class="info-img"
                                        ></i>
                                        <div class="input-groupicon">
                                            <input
                                                type="text"
                                                name="arrival_date"
                                                class="datetimepicker"
                                                placeholder="Choose Date"
                                                required
                                            />
                                        </div>
                                    </div>
                                </div>

                                <!-- Product Search -->
                                <div class="col-lg-12">
                                    <div class="input-blocks search-form mb-3">
                                        <label>Product</label>
                                        <input
                                            type="text"
                                            id="product_search"
                                            class="form-control"
                                            placeholder="Search Product by Name or SKU"
                                        />
                                        <ul
                                            id="product_list"
                                            class="list-group mt-2"
                                        ></ul>
                                        <i
                                            data-feather="search"
                                            class="feather-search"
                                        ></i>
                                    </div>
                                </div>

                                <!-- Products Table -->
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
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="product-tbody">
                                                    <!-- Product rows will be dynamically added here -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Notes -->
                                <div class="col-lg-12">
                                    <div class="input-blocks search-form mb-0">
                                        <label>Notes</label>
                                        <textarea
                                            name="notes"
                                            class="form-control"
                                        ></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Footer Buttons -->
                            <div class="modal-footer-btn">
                                <button
                                    type="button"
                                    class="btn btn-cancel me-2"
                                    data-bs-dismiss="modal"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    id="createTransferBtn"
                                    class="btn btn-submit"
                                >
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
                        <button
                            type="button"
                            class="close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        >
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body custom-modal-body">
                        <form>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="input-blocks">
                                        <label>Store From</label>
                                        <select class="select">
                                            <option>Choose</option>
                                            <option>Storename 1</option>
                                            <option>Storename 2</option>
                                            <option selected>
                                                Storename 3
                                            </option>
                                            <option>Storename 4</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="input-blocks">
                                        <label>Store To</label>
                                        <select class="select">
                                            <option>Choose</option>
                                            <option>Storename 5</option>
                                            <option selected>
                                                Storename 6
                                            </option>
                                            <option>Storename 7</option>
                                            <option>Storename 8</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="input-blocks">
                                        <label>Responsible Person From:</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            value="Responsible person name"
                                        />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="input-blocks">
                                        <label>Responsible Person To:</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            value="Responsible person name"
                                        />
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="input-blocks">
                                        <label>Reference No</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            value="32434545"
                                            disabled
                                        />
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="input-blocks search-form mb-3">
                                        <label>Product</label>
                                        <input
                                            type="text"
                                            id="product_search"
                                            class="form-control"
                                            placeholder="Select Product"
                                        />
                                        <ul
                                            id="product_list"
                                            class="list-group mt-2"
                                        ></ul>
                                        <i
                                            data-feather="search"
                                            class="feather-search"
                                        ></i>
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
                                                        <th>
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
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <div
                                                                class="productimgname"
                                                            >
                                                                <input
                                                                    type="text"
                                                                    class="form-control"
                                                                    value="Dell laptop"
                                                                />
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div
                                                                class="input-blocks sku"
                                                            >
                                                                <!-- <label>SKU</label> -->
                                                                <input
                                                                    type="text"
                                                                    class="form-control"
                                                                    value="SKU1890"
                                                                />
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <!-- <label>Category</label> -->
                                                            <select
                                                                class="select"
                                                            >
                                                                <option>
                                                                    Choose
                                                                </option>
                                                                <option>
                                                                    Category
                                                                    Name 5
                                                                </option>
                                                                <option>
                                                                    Category
                                                                    Name 6
                                                                </option>
                                                                <option
                                                                    selected
                                                                >
                                                                    Category
                                                                    Name 7
                                                                </option>
                                                                <option>
                                                                    Category
                                                                    Name 8
                                                                </option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <div
                                                                class="product-quantity"
                                                            >
                                                                <span
                                                                    class="quantity-btn"
                                                                    ><i
                                                                        data-feather="minus-circle"
                                                                        class="feather-search"
                                                                    ></i
                                                                ></span>
                                                                <input
                                                                    type="text"
                                                                    class="quntity-input"
                                                                    value="2"
                                                                />
                                                                <span
                                                                    class="quantity-btn"
                                                                    >+<i
                                                                        data-feather="plus-circle"
                                                                        class="plus-circle"
                                                                    ></i
                                                                ></span>
                                                            </div>
                                                        </td>
                                                        <td
                                                            class="action-table-data"
                                                            colspan="2"
                                                        >
                                                            <div
                                                                class="edit-delete-action"
                                                            >
                                                                <a
                                                                    class="confirm-text p-2"
                                                                    href="javascript:void(0);"
                                                                >
                                                                    <i
                                                                        data-feather="trash-2"
                                                                        class="feather-trash-2"
                                                                    ></i>
                                                                </a>
                                                            </div>
                                                        </td>

                                                        <td></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="input-blocks search-form mb-0">
                                        <label>Notes</label>
                                        <textarea class="form-control">
It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal </textarea
                                        >
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer-btn">
                                <button
                                    type="button"
                                    class="btn btn-cancel me-2"
                                    data-bs-dismiss="modal"
                                >
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

<!-- <<<<<<< HEAD:resources/views/admin/stock-transfer.blade.php =======-->
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
<!-- <script>
    $(document).ready(function () {
        let selectedProducts = {}; // Prevent duplicate products

        // Fetch stores when modal opens
        $("#add-units").on("shown.bs.modal", function () {
            fetchStores();
        });

        // Function to fetch stores and populate dropdown
        function fetchStores() {
            $.ajax({
                url: "{{ route('admin.get-stores') }}",
                type: "GET",
                success: function (response) {
                    $(".store-dropdown")
                        .empty()
                        .append('<option value="">Choose</option>');
                    $.each(response, function (key, store) {
                        $(".store-dropdown").append(
                            `<option value="${store.id}">${store.name}</option>`
                        );
                    });
                },
                error: function (error) {
                    console.error("Error fetching stores:", error);
                },
            });
        }

        // Clear product list when store changes
        $("#store_from").on("change", function () {
            $("#product_search").val("");
            $("#product_list").empty();
        });

        // Product search with AJAX
        $("#product_search").on("keyup", function () {
            let query = $(this).val();
            let storeId = $("#store_from").val();

            if (query.length > 1 && storeId !== "") {
                $.ajax({
                    url: "{{ route('admin.get-products') }}",
                    type: "GET",
                    data: {
                        store_id: storeId,
                        query: query,
                    },
                    success: function (response) {
                        $("#product_list").empty(); // Clear previous results

                        if (response.length > 0) {
                            $.each(response, function (key, product) {
                                $("#product_list").append(
                                    `<li class="list-group-item product-item" 
            data-id="${product.id}" 
            data-name="${product.product_name}" 
            data-sku="${product.sku}" 
            data-category="${product.category_name}"  Correct this -->
<!-- data-qty="${product.total_quantity}">
            ${product.product_name} - ${product.sku} (Qty: ${product.total_quantity})
        </li>`
                                );
                            });
                        } else {
                            $("#product_list").append(
                                '<li class="list-group-item text-danger">No products found</li>'
                            );
                        }
                    },
                });
            } else {
                $("#product_list").empty();
            }
        });

        // Add product to table on click
        $(document).on("click", ".product-item", function () {
            let productId = $(this).data("id");

            // Prevent adding the same product multiple times
            if (selectedProducts[productId]) {
                Swal.fire({
                    icon: "warning",
                    title: "Product Already Added!",
                    text: "You have already added this product.",
                });
                return;
            }

            // Mark product as selected to prevent duplicates
            selectedProducts[productId] = true;

            let productName = $(this).data("name");
            let productSku = $(this).data("sku");
            let productCategory = $(this).data("category");
            let productQty = $(this).data("qty");

            console.log("Product Category:", productCategory); // Debugging

            addProductRow(
                productId,
                productName, -->
<!-- productSku,
                productCategory,
                productQty
            );

            // Clear search and product list after selection
            $("#product_search").val("");
            $("#product_list").empty();
        });

        // Add product row to table
        function addProductRow(id, name, sku, categoryName, qty) {
            let newRow = `
    <tr data-id="${id}"> -->
<!-- <td><input type="hidden" name="product_id[]" value="${id}" />${name}</td>
        <td>${sku}</td>
        <td>${categoryName}</td> Corrected this line to show category name
        <td><input type="number" name="qty[]" class="form-control" value="1" min="1" max="${qty}" /></td>
        <td>
            <button type="button" class="btn btn-danger btn-sm remove-product">Remove</button>
        </td>
    </tr>`;
            $("#product-tbody").append(newRow);
        } 

        // Remove product row
        $(document).on("click", ".remove-product", function () {
            let row = $(this).closest("tr");
            let productId = row.data("id");
            row.remove();
            delete selectedProducts[productId]; // Remove from selected list
        });

        // Close dropdown if clicking outside
        $(document).on("click", function (e) {
            if (
                !$(e.target).closest(".product-dropdown, #product_search")
                    .length
            ) {
                $(".product-dropdown").remove();
            }
        });
    });
</script> -->

<!-- >>>>>>> -->
<!-- 305be3f2d461c6d843fa6dfd1c2df61d08a672d6:resources/views/admin/orders/stock-transfer.blade.php -->
@endsection
