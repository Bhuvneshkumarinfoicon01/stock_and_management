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
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4>Store List</h4>
                    <h6>Manage your Store</h6>
                </div>
            </div>
            <ul class="table-top-head">
                <!-- <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Pdf"><img
                            src="https://dreamspos.dreamstechnologies.com/html/template/assets/img/icons/pdf.svg"
                            alt="img" /></a>
                </li>
                <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Excel"><img
                            src="https://dreamspos.dreamstechnologies.com/html/template/assets/img/icons/excel.svg"
                            alt="img" /></a>
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
            @if(hasPermission('create', 'store'))
            <div class="page-btn">
                <a href="#" class="btn btn-added" data-bs-toggle="modal" data-bs-target="#add-stores"><i
                        data-feather="plus-circle" class="me-2"></i> Add
                    Store</a>
            </div>
            @endif
        </div>
        <!-- /product list -->
        <div class="card table-list-card">
            <div class="card-body">
                <div class="table-top">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 w-100 mb-3">
                        @include('admin.partials.datatable-page-length-toolbar', [
                            'tableKey' => 'store-list',
                            'toolbarId' => 'store-list-dt-toolbar',
                            'datatableConfig' => $datatableConfig ?? app(\App\Services\DatatablePaginationService::class)->config('store-list'),
                        ])
                    </div>
                    <div class="search-set">
                        <div class="search-input d-flex align-items-center">
                            <a href="#" class="btn btn-searchset">
                                <i data-feather="search" class="feather-search"></i>
                            </a>
                            <div id="custom-search" class="me-2"></div>

                        </div>
                    </div>
                    <!-- <div class="search-path">
                        <a class="btn btn-filter" id="filter_search">
                            <i data-feather="filter" class="filter-icon"></i>
                            <span><img src="assets/img/icons/closes.svg" alt="img" /></span>
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
                                    <i data-feather="stop-circle" class="info-img"></i>
                                    <select id="status-filter" class="select user-status">
                                        <option value="choose">Choose Status</option>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- /Filter -->
                <div class="table-responsive datatable-scroll-container" id="store-list-scroll-wrap">
                    <table class="table datanew" id="store-table">
                        <thead>
                            <tr>
                                <th>Store name</th>
                                <th>Address</th>
                                <th>City</th>
                                <th>State</th>
                                <th>Zipcode</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Invoice Prefix</th>
                                <th>Invoice Sequence</th>
                                <th>Status</th>
                                <th class="no-sort">Action</th>
                                {{-- <th style="display:none;">Created</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Rows are loaded by DataTables AJAX in serverSide mode --}}
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
        <!-- /product list -->
    </div>
</div>

<!-- Add Store -->
<div class="modal fade" id="add-stores">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable custom-modal-two">
        <div class="modal-content">
            <div class="page-wrapper-new p-0">
                <div class="content">
                    <div class="modal-header border-0 custom-modal-header">
                        <div class="page-title">
                            <h4>Create Store</h4>
                        </div>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body custom-modal-body">
                        <form>
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Store Name</label>
                                <input type="text" class="form-control" />
                            </div>
                            <!-- <div class="mb-3">
                                        <label class="form-label">User Name</label>
                                        <input type="text" class="form-control">
                                    </div>
                                    <div class="input-blocks mb-3">
                                        <label>Password</label>
                                        <div class="pass-group">
                                            <input type="password" class=" pass-input">
                                            <span class="fas toggle-password fa-eye-slash"></span>
                                        </div>
                                    </div> -->
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <input type="text" class="form-control" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">City</label>
                                <input type="text" class="form-control" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">State</label>
                                <input type="text" class="form-control" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Zip Code</label>
                                <input type="text" class="form-control" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="text" class="form-control" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Invoice Prefix</label>
                                <input type="text" class="form-control" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Sale Invoice Number Sequence</label>
                                <input type="number" min="1" class="form-control">
                            </div>
                            <div class="mb-0">
                                <div
                                    class="status-toggle modal-status d-flex justify-content-between align-items-center">
                                    <span class="status-label">Status</span>
                                    <input type="checkbox" id="user2" class="check" checked="" />
                                    <label for="user2" class="checktoggle"></label>
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
<!-- /Add Store -->

<!-- Edit Store -->
<div class="modal fade" id="edit-stores" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable custom-modal-two">
        <div class="modal-content">
            <div class="page-wrapper-new p-0">
                <div class="content">
                    <div class="modal-header border-0 custom-modal-header">
                        <div class="page-title">
                            <h4>Edit Store</h4>
                        </div>
                        {{--<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>--}}
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    {{-- Match create modal: scroll-modal enables vertical scroll for long forms (document sequences). --}}
                    <div class="modal-body custom-modal-body scroll-modal">
                        <form id="edit-form">
                            <div class="mb-3">
                                <label class="form-label">Store Name</label>
                                <input type="text" name="storeName" class="form-control" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <input type="text" name="address" class="form-control" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">City</label>
                                <input type="text" name="city" class="form-control" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">State</label>
                                <input type="text" name="state" class="form-control" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Zip Code</label>
                                <input type="text" name="zipcode" class="form-control" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" />
                            </div>
                            @include('admin.partials.store-document-sequences-fields', ['isEdit' => true])
                            <div class="mb-0">
                                <div
                                    class="status-toggle modal-status d-flex justify-content-between align-items-center">
                                    <span class="status-label">Status</span>
                                    <input type="checkbox" id="user3" class="check" checked="" />
                                    <label for="user3" class="checktoggle"></label>
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
<!-- /Edit Store -->

@endsection @section('scripts')
<style>
    .datatable-infinite-scroll {
        overflow-y: auto;
    }
</style>
<script>
    $(document).ready(function () {
        const storeColumns = [
            { data: 'store_name', name: 'name' },
            { data: 'address', name: 'address' },
            { data: 'city', name: 'city' },
            { data: 'state', name: 'state' },
            { data: 'zipcode', name: 'zipcode' },
            { data: 'phone', name: 'phone' },
            { data: 'email', name: 'email' },
            { data: 'invoice_prefix', name: 'invoice_prefix' },
            { data: 'sale_invoice_start_number', name: 'next_sale_invoice_number' },
            { data: 'status_badge', name: 'status_badge' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false },
        ];

        function getStoreListAjaxData() {
            const data = {};
            const status = $('#status-filter').val();
            if (status && status !== 'choose') {
                data.status = status;
            }
            const orderType = $('#sort-order').val();
            if (orderType) {
                data.order_type = orderType;
            }
            return data;
        }

        const paginationManager = DatatablePagination.attach({
            tableKey: 'store-list',
            tableSelector: '#store-table',
            toolbarSelector: '#store-list-dt-toolbar',
            scrollContainerSelector: '#store-list-scroll-wrap',
            config: @json($datatableConfig ?? app(\App\Services\DatatablePaginationService::class)->config('store-list')),
            getAjaxUrl: function () {
                return "{{ route('admin.store-list-superadmin') }}";
            },
            getAjaxData: getStoreListAjaxData,
            buildTableOptions: function (mode, pageLength) {
                const shared = {
                    columns: storeColumns,
                    order: [[0, 'asc']],
                    language: {
                        emptyTable: 'No stores found',
                        search: 'Search:',
                        searchPlaceholder: 'Search stores...',
                    },
                    drawCallback: function () {
                        feather.replace();
                    },
                    initComplete: function () {
                        const searchWrapper = $('#store-table_filter');
                        const defaultSearchBox = searchWrapper.find('input');
                        $('#custom-search').append(defaultSearchBox);
                        defaultSearchBox.attr('placeholder', 'Search...');
                        searchWrapper.hide();
                    },
                };

                if (mode === 'scroll') {
                    return Object.assign({}, shared, {
                        processing: true,
                        serverSide: false,
                        paging: false,
                        lengthChange: false,
                        info: false,
                        searching: true,
                        dom: 'frt',
                        data: [],
                    });
                }

                return Object.assign({}, shared, {
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('admin.store-list-superadmin') }}",
                        data: function (d) {
                            Object.assign(d, getStoreListAjaxData());
                        },
                    },
                    pageLength: pageLength,
                    lengthChange: false,
                    searching: true,
                    dom: 'frtip',
                });
            },
        });

        window.reloadStoreList = function () {
            paginationManager.reload(false);
        };

        $('#status-filter, #sort-order').on('change', function () {
            paginationManager.reload(false);
        });
    });
</script>

<script>
    // Old DataTable init (replaced by DatatablePagination above).
    /*
    $(document).ready(function () {
        let table = $('#store-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.store-list-superadmin') }}",
                dataType: 'json',
                type: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                data: function (d) {
                    d.status = $('#status-filter').val();
                    d.order_type = $('#sort-order').val();
                }
            },
            columns: [
                { data: 'store_name', name: 'name' },
                { data: 'address', name: 'address' },
                { data: 'city', name: 'city' },
                { data: 'state', name: 'state' },
                { data: 'zipcode', name: 'zipcode' },
                { data: 'phone', name: 'phone' },
                { data: 'email', name: 'email' },
                { data: 'status_badge', name: 'status_badge' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false },
            ],
            pageLength: 10,
            drawCallback: function () {
                feather.replace();
            },
            initComplete: function () {
                let searchWrapper = $('#store-table_filter');
                let defaultSearchBox = searchWrapper.find('input');
                $('#custom-search').append(defaultSearchBox);
                defaultSearchBox.attr('placeholder', 'Search...');
                searchWrapper.remove();
            }
        });
        */
</script>

<script>
    // Use named routes to avoid broken root-relative URLs when app runs in a subdirectory.
    const EDIT_STORE_URL_TEMPLATE = "{{ route('admin.edit-store-superadmin', ['id' => '___ID___']) }}";
    const UPDATE_STORE_URL_TEMPLATE = "{{ route('admin.update-store-superadmin', ['id' => '___ID___']) }}";
    const DELETE_STORE_URL_TEMPLATE = "{{ route('admin.delete-store-superadmin', ['id' => '___ID___']) }}";

    //EDIT STORE MODEL
    $(document).on('click', '.edit-store-btn', function () {
        const storeId = $(this).data('id');

        // Fetch store data and populate the form
        $.ajax({
            //url: `/admin/store-list/edit/${storeId}`,
            url: EDIT_STORE_URL_TEMPLATE.replace('___ID___', storeId),
            method: 'GET',
            success: function (response) {
                //console.log(response);
                if (response.success) {
                    const store = response.data;
                    // Populate form with store data
                    const $form = $('#edit-form');
                    $form.find('input[name="storeName"]').val(store.name);
                    $form.find('input[name="address"]').val(store.address);
                    $form.find('input[name="city"]').val(store.city);
                    $form.find('input[name="state"]').val(store.state);
                    $form.find('input[name="zipcode"]').val(store.zipcode);
                    $form.find('input[name="phone"]').val(store.phone);
                    $form.find('input[name="email"]').val(store.email);
                    $form.find('input[name="invoice_prefix"]').val(store.invoice_prefix || 'INV');
                    $form.find('input[name="sale_invoice_start_number"]').val(store.next_sale_invoice_number || 1);
                    $form.find('input[name="purchase_invoice_prefix"]').val(store.purchase_invoice_prefix || 'PINV');
                    $form.find('input[name="purchase_invoice_start_number"]').val(store.next_purchase_invoice_number || 1);
                    $form.find('input[name="purchase_order_prefix"]').val(store.purchase_order_prefix || 'PO');
                    $form.find('input[name="purchase_order_start_number"]').val(store.next_purchase_order_number || 1);
                    $form.find('input[name="purchase_return_prefix"]').val(store.purchase_return_prefix || 'PR');
                    $form.find('input[name="purchase_return_start_number"]').val(store.next_purchase_return_number || 1);
                    $form.find('input[name="grn_prefix"]').val(store.grn_prefix || 'GRN');
                    $form.find('input[name="grn_start_number"]').val(store.next_grn_number || 1);
                    $form.find('input[name="credit_note_prefix"]').val(store.credit_note_prefix || 'CN');
                    $form.find('input[name="credit_note_start_number"]').val(store.next_credit_note_number || 1);
                    $form.find('input[name="debit_note_prefix"]').val(store.debit_note_prefix || 'DN');
                    $form.find('input[name="debit_note_start_number"]').val(store.next_debit_note_number || 1);
                    $form.find('input[name="receipt_prefix"]').val(store.receipt_prefix || 'RCPT');
                    $form.find('input[name="receipt_start_number"]').val(store.next_receipt_number || 1);
                    $form.find('input[name="batch_purchase_prefix"]').val(store.batch_purchase_prefix || 'BP');
                    $form.find('input[name="batch_purchase_start_number"]').val(store.next_batch_purchase_number || 1);
                    $('#user3').prop('checked', store.status === 1);

                    // Store the storeId dynamically to use when submitting
                    $('#edit-form').data('id', storeId);
                } else {
                    alert('Store not found');
                }
            },
            error: function () {
                alert('Failed to load store data');
            }
        });
    });

    // Handle form submission
    $('#edit-form').on('submit', function (e) {
        e.preventDefault();

        const storeId = $(this).data('id');  // Retrieve storeId from the form data
        const formData = {
            storeName: $('#edit-form input[name="storeName"]').val(),
            address: $('#edit-form input[name="address"]').val(),
            city: $('#edit-form input[name="city"]').val(),
            state: $('#edit-form input[name="state"]').val(),
            zipcode: $('#edit-form input[name="zipcode"]').val(),
            phone: $('#edit-form input[name="phone"]').val(),
            email: $('#edit-form input[name="email"]').val(),
            invoice_prefix: $('#edit-form input[name="invoice_prefix"]').val(),
            sale_invoice_start_number: $('#edit-form input[name="sale_invoice_start_number"]').val(),
            purchase_invoice_prefix: $('#edit-form input[name="purchase_invoice_prefix"]').val(),
            purchase_invoice_start_number: $('#edit-form input[name="purchase_invoice_start_number"]').val(),
            purchase_order_prefix: $('#edit-form input[name="purchase_order_prefix"]').val(),
            purchase_order_start_number: $('#edit-form input[name="purchase_order_start_number"]').val(),
            purchase_return_prefix: $('#edit-form input[name="purchase_return_prefix"]').val(),
            purchase_return_start_number: $('#edit-form input[name="purchase_return_start_number"]').val(),
            grn_prefix: $('#edit-form input[name="grn_prefix"]').val(),
            grn_start_number: $('#edit-form input[name="grn_start_number"]').val(),
            credit_note_prefix: $('#edit-form input[name="credit_note_prefix"]').val(),
            credit_note_start_number: $('#edit-form input[name="credit_note_start_number"]').val(),
            debit_note_prefix: $('#edit-form input[name="debit_note_prefix"]').val(),
            debit_note_start_number: $('#edit-form input[name="debit_note_start_number"]').val(),
            receipt_prefix: $('#edit-form input[name="receipt_prefix"]').val(),
            receipt_start_number: $('#edit-form input[name="receipt_start_number"]').val(),
            batch_purchase_prefix: $('#edit-form input[name="batch_purchase_prefix"]').val(),
            batch_purchase_start_number: $('#edit-form input[name="batch_purchase_start_number"]').val(),
            status: $('#user3').is(':checked') ? 1 : 0,
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        // Send the PUT request to update the store
        $.ajax({
            //url: `/admin/store-list/update/${storeId}`, 
            url: UPDATE_STORE_URL_TEMPLATE.replace('___ID___', storeId),
            method: 'POST',
            data: formData,
            success: function (response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message || 'Store updated successfully.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        $('#edit-stores').modal('hide');
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to update store.'
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Validation failed or server error.'
                });
            }
        });

    });

    $(document).on('click', '.delete-store-btn', function () {
        const storeId = $(this).data('id');
        const token = $('meta[name="csrf-token"]').attr('content');

        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    //url: `/admin/store-list/delete/${storeId}`,
                    url: DELETE_STORE_URL_TEMPLATE.replace('___ID___', storeId),
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: token
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire('Deleted!', response.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Error', 'Deletion failed. Try again.', 'error');
                    }
                });
            }
        });
    });


</script>

@endsection