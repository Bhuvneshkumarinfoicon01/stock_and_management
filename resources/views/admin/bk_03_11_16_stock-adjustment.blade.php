@extends('layouts.app_admin') @section('title') Dashbaord @endsection
@section('styles') @endsection @section('content')

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4>Stock Adjustment</h4>
                    <h6>Manage your products</h6>
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
                </li> -->
                <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Excel"
                        href="{{route('admin.stock-adjustments.export')}}"><img src="assets/img/icons/excel.svg"
                            alt="img" /></a>
                </li>
                <!-- <li>
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
            @if (hasPermission('create', 'stock_adjustment'))
            <div class="page-btn">
                <button type="button" class="btn btn-added" data-bs-toggle="modal" data-bs-target="#add-new-stock">
                    <i data-feather="plus-circle" class="me-2"></i>Add New
                    Adjustment
                </button>
            </div>
            @endif
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
                @php
                $user=auth()->user();
                @endphp
                @if($user && in_array($user->role_id,[1,2]))
                <div class="card mb-0" id="filter_inputs">
                    <div class="card-body pb-0">
                        <div class="row">
                            <div class="col-lg-12 col-sm-12">
                                <div class="row">
                                    <div class="col-lg-2 col-sm-6 col-12">
                                        <div class="input-blocks">
                                            <i class="fas fa-money-bill info-img"></i>
                                            @php
                                            $user=auth()->user();
                                            $selected_store_id=$user->selected_store_id;
                                            @endphp

                                            <select class="select" id="store_filter">
                                                @if($user && in_array($user->role_id,[1,2]) )
                                                <option value="all">All Stores</option>
                                                @endif
                                                @foreach($stores as $store)
                                                @if($store->id==$selected_store_id)
                                                <option value="{{ $store->id }}" selected>{{ $store->name }}</option>
                                                @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                <!-- /Filter -->
                <div class="table-responsive datatable-scroll-container" id="stock-adjustment-scroll-wrap">
                    <table class="table datanew" id="adjustment-table">
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
                                <th>Store</th>
                                <th>Product</th>
                                <th>Adjustment Type</th>
                                <th>Adjust Qty</th>
                                <th>Adjust Reason</th>
                                <th>Adjust Date</th>
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

<!-- Edit Stock -->
<div class="modal fade" id="edit-units">
    <div class="modal-dialog modal-dialog-centered stock-adjust-modal">
        <div class="modal-content">
            <div class="page-wrapper-new p-0">
                <div class="content">
                    <div class="modal-header border-0 custom-modal-header">
                        <div class="page-title">
                            <h4>Edit Stock Adjustment</h4>
                        </div>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body custom-modal-body">
                        <form method="post" id="editForm" action="">
                            @csrf @method('PUT')
                            <input type="hidden" id="edit_id" name="id" />
                            <div class="row">
                                <div class="col-lg-6 col-sm-6 col-12">
                                    <div class="mb-3 add-product">
                                        <label class="form-label">Store</label>
                                        <select class="select form-control" id="edit_store_id" name="store_id">
                                            <option value="">Choose</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-6 col-12">
                                    <div class="mb-3 add-product">
                                        <label class="form-label">Product</label>
                                        <select class="select form-control" id="edit_product_id" name="product_id">
                                            <option value="">Choose</option>
                                        </select>
                                        <small class="text-muted available-stock"></small>

                                    </div>

                                </div>
                                <div class="col-lg-4 col-sm-6 col-12">
                                    <label class="form-label">Adjustment Type</label>
                                    <div class="btn-group w-100" role="group" aria-label="Adjustment Type">
                                        <button type="button" class="btn btn-sm btn-outline-primary adjustment-type-btn" data-type="add">
                                            Add to Stock
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-primary adjustment-type-btn active" data-type="remove">
                                            Reduce Stock
                                        </button>
                                    </div>
                                    <input type="hidden" name="adjustment_type" id="edit_adjustment_type" value="remove" />
                                </div>
                                <div class="col-lg-4 col-sm-6 col-12">
                                    <label class="form-label">Adjusted Quantity</label>
                                    <div class="product-quantity">
                                        <span class="quantity-btn"><i data-feather="minus-circle"
                                                class="feather-search"></i></span>
                                        <input type="text" class="quntity-input" id="edit_quantity"
                                            name="adjust_quantity" value="1" />
                                        <span class="quantity-btn">+<i data-feather="plus-circle"
                                                class="plus-circle"></i></span>
                                    </div>
                                    <small class="text-muted">
                                        Use "Add to Stock" when the quantity is being received or increased. Use "Reduce Stock" when the quantity is being deducted.
                                    </small>
                                </div>
                                <div class="col-lg-4 col-sm-6 col-12">
                                    <div class="mb-3 add-product">
                                        <label class="form-label">Adjustment Reason</label>
                                        <select class="select form-control" id="edit_reason" name="reason">
                                            <option value="">Choose</option>
                                            <option value="Loss">Loss</option>
                                            <option value="Damage">
                                                Damage
                                            </option>
                                            <option value="Error Correction">
                                                Error Correction
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-sm-6 col-12">
                                    <div class="input-blocks">
                                        <label>Adjustment Date</label>

                                        <div class="input-groupicon calender-input">
                                            <i data-feather="calendar" class="info-img"></i>
                                            <input type="text" class="form-control datetimepicker"
                                                id="edit_adjustment_date" name="adjust_date" placeholder="Choose Date"
                                                value="" />
                                        </div>
                                    </div>
                                </div>
                            </div>
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
<!--Add new Adjustment-->
<div class="modal fade" id="add-new-stock">
    <div class="modal-dialog modal-dialog-centered stock-adjust-modal">
        <div class="modal-content">
            <div class="page-wrapper-new p-0">
                <div class="content">
                    <div class="modal-header border-0 custom-modal-header">

                        <div class="page-title">
                            <h4>Add New Stock Adjustment</h4>
                        </div>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body custom-modal-body">
                        <form method="post" action="{{ route('admin.create-adjustment') }}">
                            @csrf
                            <!-- <div class="row">
                                <div class="col-lg-6 col-sm-6 col-12">
                                    <div class="mb-3 add-product">
                                        <label class="form-label">Store</label>
                                        <select class="select form-control" id="store_id" name="store_id">
                                            <option value="">Choose</option>

                                            @if(in_array($user->role_id, [1, 2]))
                                            @foreach($stores as $store)
                                            <option value="{{ $store->id }}">{{ $store->name }}</option>
                                            @endforeach
                                            @else
                                            <option value="{{ $user->selected_store_id }}">
                                                {{ optional($stores->firstWhere('id', $user->selected_store_id))->name
                                                }}
                                            </option>
                                            @endif
                                        </select>
                                        @error('store_id')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-6 col-12">
                                    <div class="mb-3 add-product">
                                        <label class="form-label">Product</label>
                                        <select class="select" id="product_id" name="product_id">
                                            <option value="">Choose</option>
                                        </select>
                                        <small class="text-muted available-stock"></small>
                                        @error('product_id')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-6 col-12">
                                    <label class="form-label">Adjustment Type</label>
                                    <div class="btn-group w-100" role="group" aria-label="Adjustment Type">
                                        <button type="button" class="btn btn-sm btn-outline-primary adjustment-type-btn" data-type="add">
                                            Add to Stock
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-primary adjustment-type-btn active" data-type="remove">
                                            Reduce Stock
                                        </button>
                                    </div>
                                    <input type="hidden" name="adjustment_type" id="add_adjustment_type" value="remove" />
                                </div>
                                <div class="col-lg-4 col-sm-6 col-12">
                                    <label class="form-label">Adjusted Quantity</label>
                                    <div class="product-quantity">
                                        <span class="quantity-btn"><i data-feather="minus-circle"
                                                class="feather-search"></i></span>
                                        <input type="text" class="quntity-input" value="1" name="quantity" />
                                        <span class="quantity-btn">+<i data-feather="plus-circle"
                                                class="plus-circle"></i></span>

                                    </div>
                                    <small class="text-muted">
                                        Use "Add to Stock" when the quantity is being received or increased. Use "Reduce Stock" when the quantity is being deducted.
                                    </small>
                                    @error('adjustment_type')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                    @error('quantity')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-lg-4 col-sm-6 col-12">
                                    <div class="mb-3 add-product">
                                        <label class="form-label">Adjustment Reason</label>
                                        <select class="select" name="reason">
                                            <option value="">Choose</option>
                                            <option value="Loss">Loss</option>
                                            <option value="Damage">
                                                Damage
                                            </option>
                                            <option value="Error Correction">
                                                Error Correction
                                            </option>
                                        </select>
                                        @error('reason')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-4 col-sm-6 col-12">
                                    <div class="input-blocks">
                                        <label>Adjustment Date</label>
                                        <div class="input-groupicon calender-input">
                                            <i data-feather="calendar" class="info-img"></i>
                                            <input type="text" class="form-control datetimepicker"
                                                placeholder="Choose Date" name="adjustment_date" value="" />
                                            @error('adjustment_date')
                                            <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                            
                            <div class="row">

                                <!-- Store -->
                                <div class="col-lg-4 col-sm-6 col-12">
                                    <div class="mb-3 add-product">
                                        <label class="form-label">Store</label>

                                        <select class="select form-control" id="store_id" name="store_id">
                                            <option value="">Choose</option>

                                            @if(in_array($user->role_id, [1, 2]))
                                                @foreach($stores as $store)
                                                    <option value="{{ $store->id }}">
                                                        {{ $store->name }}
                                                    </option>
                                                @endforeach
                                            @else
                                                <option value="{{ $user->selected_store_id }}">
                                                    {{ optional($stores->firstWhere('id', $user->selected_store_id))->name }}
                                                </option>
                                            @endif
                                        </select>

                                        @error('store_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Product -->
                                <div class="col-lg-4 col-sm-6 col-12">
                                    <div class="mb-3 add-product">
                                        <label class="form-label">Product</label>

                                        <select class="select" id="product_id" name="product_id">
                                            <option value="">Choose</option>
                                        </select>

                                        <small class="text-muted available-stock"></small>

                                        @error('product_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Adjustment Type -->
                                <div class="col-lg-4 col-sm-6 col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Adjustment Type</label>

                                        <div class="btn-group w-100" role="group">

                                            <button type="button"
                                                class="btn btn-sm btn-outline-primary adjustment-type-btn"
                                                data-type="add">
                                                Add to Stock
                                            </button>

                                            <button type="button"
                                                class="btn btn-sm btn-outline-primary adjustment-type-btn active"
                                                data-type="remove">
                                                Reduce Stock
                                            </button>

                                        </div>

                                        <input type="hidden"
                                            name="adjustment_type"
                                            id="add_adjustment_type"
                                            value="remove" />

                                        @error('adjustment_type')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Quantity -->
                                <div class="col-lg-4 col-sm-6 col-12">
                                    <div class="mb-3">

                                        <label class="form-label">
                                            Adjusted Quantity
                                            <span class="required-star text-danger">*</span>
                                        </label>

                                        <div class="product-quantity">

                                            <span class="quantity-btn">
                                                <i data-feather="minus-circle"></i>
                                            </span>

                                            <input type="text"
                                                class="quntity-input"
                                                value="1"
                                                name="quantity" />

                                            <span class="quantity-btn">
                                                + <i data-feather="plus-circle"></i>
                                            </span>

                                        </div>

                                        <small class="text-muted">
                                            Use Add to Stock to increase quantity and Reduce Stock to deduct quantity.
                                        </small>

                                        @error('quantity')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror

                                    </div>
                                </div>

                                <!-- Adjustment Reason -->
                                <div class="col-lg-4 col-sm-6 col-12">
                                    <div class="mb-3 add-product">

                                        <label class="form-label">
                                            Adjustment Reason
                                            <span class="required-star text-danger">*</span>
                                        </label>

                                        <select class="select" name="reason" id="reason">
                                            <option value="">Choose</option>
                                            <option value="Loss">Loss</option>
                                            <option value="Damage">Damage</option>
                                            <option value="Error Correction">Error Correction</option>
                                        </select>

                                        @error('reason')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror

                                    </div>
                                </div>

                                <!-- Adjustment Date -->
                                <div class="col-lg-4 col-sm-6 col-12">
                                    <div class="mb-3 input-blocks">

                                        <label class="form-label">Adjustment Date</label>

                                        <div class="input-groupicon calender-input">

                                            <i data-feather="calendar" class="info-img"></i>

                                            <input type="text"
                                                class="form-control datetimepicker"
                                                placeholder="Choose Date"
                                                name="adjustment_date"
                                                value="" />

                                        </div>

                                        @error('adjustment_date')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror

                                    </div>
                                </div>

                            </div>


                            <!-- Adjustment Type JavaScript -->
                            <script>
                                $(document).ready(function () {

                                    function updateAdjustmentType() {

                                        let type = $('#add_adjustment_type').val();

                                        if (type === 'remove') {
                                            $('.required-star').show();
                                        } else {
                                            $('.required-star').hide();
                                        }
                                    }

                                    // Adjustment button click
                                    $('.adjustment-type-btn').on('click', function () {

                                        let type = $(this).data('type');

                                        // Update hidden input
                                        $('#add_adjustment_type').val(type);

                                        // Update active button
                                        $('.adjustment-type-btn').removeClass('active');
                                        $(this).addClass('active');

                                        // Show / Hide mandatory stars
                                        updateAdjustmentType();

                                    });

                                    // Initial load
                                    updateAdjustmentType();

                                });
                            </script>

                            <style>
                                .required-star {
                                    color: red;
                                    font-weight: bold;
                                }

                                .adjustment-type-btn.active {
                                    background-color: #ff9f43;
                                    color: #fff;
                                    border-color: #ff9f43;
                                }
                            </style>

                            <div class="modal-footer-btn">
                                <button type="button" class="btn btn-cancel me-2" data-bs-dismiss="modal">
                                    Cancel
                                </button>
                                <button type="submit" class="btn btn-submit">
                                    Add Adjustment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@if ($errors->any())
<script>
    $(document).ready(function () {
        $('#add-new-stock').modal('show');
    });
</script>
@endif
<!--/Add new Adjustment-->

@endsection @section('scripts') >
<script>
    $(document).ready(function () {
        let table = $('#adjustment-table').DataTable(
            DatatablePagination.applyFixedHeader({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.stock-adjustment') }}",
                data: function (d) {
                    d.store = $('#store_filter').val();
                    d.order_type = $('#sort-order').val();
                }
            },
            columns: [
                { data: 'store_name', name: 'store.name' }, // if you join stores table
                { data: 'product_details', name: 'stock.product_name', orderable: false }, // if you join stocks table
                { data: 'adjustment_type', name: 'stock_adjustments.adjustment_type', orderable: true },
                { data: 'adj_qty', name: 'adjust_quantity' },
                { data: 'reason', name: 'reason' },
                { data: 'adjust_date', name: 'stock_adjustments.adjust_date', orderable: false },
                { data: 'actions', name: 'actions', orderable: false, searchable: false },
            ],
            pageLength: 10,
            drawCallback: function () {
                feather.replace();
            },
            initComplete: function () {
                let searchWrapper = $('#adjustment-table_filter');
                let defaultSearchBox = searchWrapper.find('input');
                $('#custom-search').append(defaultSearchBox);
                defaultSearchBox.attr('placeholder', 'Search...');
                searchWrapper.remove();
            }
        }, {}, '#stock-adjustment-scroll-wrap')
        );

        $('#store_filter').on('change', function () {
            table.ajax.reload();
        });
        $('#sort-order').on('change', function () {
            table.ajax.reload();
        });
    });


</script>

<script>
    let productCache = [];
    $(document).ready(function () {
        $("#add-new-stock, #edit-units").on("shown.bs.modal", function (e) {
            var modalId = $(this).attr("id");
            var storeDropdown = $("#" + modalId + " #store_id");
            var productDropdown = $("#" + modalId + " #product_id");
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
            });

            // Reason is shown & mandatory only when reducing stock
            var $reason = $(this).find('select[name="reason"]');
            var isReduce = $(this).find('input[name="adjustment_type"]').val() === "remove";
            $reason.prop("hidden", !isReduce);
            $reason.prop("required", isReduce);

        });

        $(document).on("click", ".adjustment-type-btn", function () {
            var modal = $(this).closest(".modal");
            var type = $(this).data("type");
            modal.find(".adjustment-type-btn").removeClass("active");
            $(this).addClass("active");
            modal.find('input[name="adjustment_type"]').val(type);

            // Reason is shown & mandatory only when reducing stock
            var $reason = modal.find('select[name="reason"]');
            if (type === "remove") {
                $reason.prop("hidden", false);
                $reason.prop("required", true);
            } else {
                $reason.prop("hidden", true);
                $reason.prop("required", false);
            }
        });

        $(document).on("change", "#store_id, #edit_store_id", function () {
            var modalId = $(this).closest(".modal").attr("id");
            var storeId = $(this).val();
            var productDropdown =
                modalId === "edit-units"
                    ? $("#" + modalId + " #edit_product_id")
                    : $("#" + modalId + " #product_id");

            if (storeId) {
                $.ajax({
                    url: "{{ route('admin.get-products') }}",
                    type: "GET",
                    data: { store_id: storeId },
                    success: function (response) {
                        productCache = response;

                        productDropdown.empty().append('<option value="">Choose</option>');

                        if (response.length) {
                            $.each(response, function (key, product) {
                                productDropdown.append(
                                    '<option value="' +
                                    product.product_id +
                                    '">' +
                                    product.product_name +
                                    " (SKU: " +
                                    product.sku +
                                    ")</option>"
                                );
                            });
                        } else {
                            productDropdown.append('<option value="">No products found</option>');
                        }
                        $("#" + modalId + " .available-stock").text("");
                    },
                    error: function () {
                        alert("Failed to load products.");
                    }
                });
            } else {
                productDropdown.empty().append('<option value="">Choose</option>');
                $("#" + modalId + " .available-stock").text("");
            }
        });
        $(document).on("change", "#product_id, #edit_product_id", function () {
            var modalId = $(this).closest(".modal").attr("id");
            var productId = $(this).val();

            if (productId && productCache.length > 0) {
                var product = productCache.find(p => p.product_id == productId);
                if (product) {
                    $("#" + modalId + " .available-stock").text("Available Quantity: " + product.total_quantity);
                } else {
                    $("#" + modalId + " .available-stock").text("Stock not found");
                }
            } else {
                $("#" + modalId + " .available-stock").text("");
            }
        });

    });
    $(document).ready(function () {
        $(document).on("click", ".edit-btn", function () {
            var id = $(this).data("id");
            console.log("Edit button clicked, ID:", id); // Debug
            var url = "/admin/get-adjustment-data/" + id;

            console.log("Generated URL:", url);
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    if (response.success) {
                        console.log(
                            "Data fetched successfully:",
                            response.data
                        );
                        populateEditModal(response.data);
                        $("#edit-units").modal("show");
                    } else {
                        alert(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching adjustment data:", error);
                },
            });
        });

        function populateEditModal(data) {
            $("#editForm").attr("action", "/admin/update/" + data.id);
            $("#edit_id").val(data.id);
            $.ajax({
                url: "{{ route('admin.get-stores') }}",
                type: "GET",
                success: function (response) {
                    $("#edit_store_id")
                        .empty()
                        .append('<option value="">Choose</option>');
                    $.each(response, function (key, store) {
                        var selected =
                            store.id == data.store_id ? "selected" : "";
                        $("#edit_store_id").append(
                            '<option value="' +
                            store.id +
                            '" ' +
                            selected +
                            ">" +
                            store.name +
                            "</option>"
                        );
                    });
                    loadProductsForEdit(data.store_id, data.product_id);
                },
            });
            $("#edit_quantity").val(data.adjust_quantity);
            $("#edit_reason").val(data.reason).trigger("change");
            var adjustmentType = data.adjustment_type === "add" ? "add" : "remove";
            $("#edit_adjustment_type").val(adjustmentType);
            $("#edit-units .adjustment-type-btn").removeClass("active");
            $("#edit-units .adjustment-type-btn[data-type='" + adjustmentType + "']").addClass("active");
            $("#edit_reason").prop("hidden", adjustmentType !== "remove");
            $("#edit_reason").prop("required", adjustmentType === "remove");
            if (data.adjust_date) {
                var formattedDate = moment(
                    data.adjust_date,
                    "YYYY-MM-DD"
                ).format(window.appDatePickerFormat || "DD-MMM-YYYY");
                $("#edit_adjustment_date").val(formattedDate);
                console.log("Date loaded into input field:", formattedDate);
            }
            console.log(
                "Date loaded into edit modal:",
                $("#edit_adjustment_date").val()
            );
        }
        function loadProductsForEdit(storeId, selectedProductId) {
            if (!storeId) return;

            $.ajax({
                url: "{{ route('admin.get-products') }}",
                type: "GET",
                data: { store_id: storeId },
                success: function (response) {
                    var productDropdown = $("#edit_product_id");
                    productDropdown.empty().append('<option value="">Choose</option>');
                    productCache = response;

                    $.each(response, function (key, product) {
                        var selected = product.product_id == selectedProductId ? "selected" : "";
                        productDropdown.append(
                            '<option value="' +
                            product.product_id +
                            '" ' +
                            selected +
                            ">" +
                            product.product_name +
                            " (SKU: " +
                            product.sku +
                            ")</option>"
                        );
                    });

                    // Trigger change to load available stock
                    productDropdown.val(selectedProductId).trigger("change");
                },
                error: function () {
                    alert("Failed to load products.");
                },
            });
        }


    });

    // Datepicker initialization
    $(document).ready(function () {
        $(".datetimepicker").datepicker({
            dateFormat: "yy-mm-dd", // Correct date format
        });

        $(document).on("click", ".edit-btn", function () {
            var id = $(this).data("id");
            var url = "/admin/get-adjustment-data/" + id;

            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    if (response.success) {
                        populateEditModal(response.data);
                        $("#edit-units").modal("show");
                    } else {
                        alert(response.message);
                    }
                },
            });
        });

        function populateEditModal(data) {
            $("#editForm").attr("action", "/admin/update/" + data.id);
            $("#edit_id").val(data.id);
            $("#edit_quantity").val(data.adjust_quantity);
            $("#edit_reason").val(data.reason).trigger("change");
            var adjustmentType = data.adjustment_type === "add" ? "add" : "remove";
            $("#edit_adjustment_type").val(adjustmentType);
            $("#edit-units .adjustment-type-btn").removeClass("active");
            $("#edit-units .adjustment-type-btn[data-type='" + adjustmentType + "']").addClass("active");
            $("#edit_reason").prop("hidden", adjustmentType !== "remove");
            $("#edit_reason").prop("required", adjustmentType === "remove");

            if (data.adjust_date) {
                var formattedDate = moment(
                    data.adjust_date,
                    "YYYY-MM-DD"
                ).format(window.appDatePickerFormat || "DD-MMM-YYYY");
                $("#edit_adjustment_date").val(formattedDate);
            }
        }
    });
</script>
<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).on('click', '.delete-adjustment', function () {
            let id = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: 'This adjustment will be permanently deleted.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/admin/adjustment/' + id + '/delete',
                        type: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.success,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            location.reload();
                        },
                        error: function () {
                            Swal.fire('Error', 'Delete failed. Try again.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>


<script>
    $(document).ready(function () {
        $(".datetimepicker").datepicker({
            dateFormat: "yy-mm-dd",
            changeMonth: true,
            changeYear: true,
        });
    });
</script>
@if(session('success'))
<script>
    document.addEventListener("DOMContentLoaded", function () {
        Swal.fire({
            icon: "success",
            title: "Success!",
            text: "{{ session('success') }}",
            timer: 3000,
            showConfirmButton: false,
        });
    });
</script>
@endif @if(session('error'))
<script>
    document.addEventListener("DOMContentLoaded", function () {
        Swal.fire({
            icon: "error",
            title: "Oops!",
            text: "{{ session('error') }}",
            timer: 3000,
            showConfirmButton: false,
        });
    });
</script>


@endif @endsection