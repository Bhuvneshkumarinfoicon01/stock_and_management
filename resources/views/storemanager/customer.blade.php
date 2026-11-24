@extends('layouts.app_admin') @section('title') Dashbaord @endsection
@section('styles') @endsection @section('content')

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4>Customer List</h4>
                    <h6>Manage your customers</h6>
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
            <div class="page-btn">
                <a
                    href="#"
                    class="btn btn-added"
                    data-bs-toggle="modal"
                    data-bs-target="#add-units"
                    ><i data-feather="plus-circle" class="me-2"></i>Add New
                    Customer</a
                >
            </div>
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
                                    <i
                                        data-feather="globe"
                                        class="info-img"
                                    ></i>
                                    <select id="country-filter" class="select">
                                        <option value="choose">Choose Country</option>
                                        @foreach ($countryList as $country)
                                        <option value="{{ $country }}">{{ $country }}</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- <div class="col-lg-3 col-sm-6 col-12 ms-auto">
                                <div class="input-blocks">
                                    <a class="btn btn-filters ms-auto">
                                        <i
                                            data-feather="search"
                                            class="feather-search"
                                        ></i>
                                        Search
                                    </a>
                                </div>
                            </div> -->
                        </div>
                    </div>
                </div>
                <!-- /Filter -->
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                    @include('admin.partials.datatable-page-length-toolbar', [
                        'tableKey' => 'all-customers',
                        'toolbarId' => 'all-customers-dt-toolbar',
                        'datatableConfig' => $datatableConfig,
                    ])
                </div>
                <div class="table-responsive datatable-scroll-container" id="customer-scroll-wrap">
                    <table class="table datanew" id="customer-data-table">
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
                                <th>Customer Name</th>
                                <th>Code</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>City</th>
                                <th>Country</th>
                                <th>Status</th>
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

<!-- Add Customer -->
<div class="modal fade" id="add-units">
    <div class="modal-dialog modal-dialog-centered custom-modal-two">
        <div class="modal-content">
            <div class="page-wrapper-new p-0">
                <div class="content">
                    <div class="modal-header border-0 custom-modal-header">
                        <div class="page-title">
                            <h4>Add Customer</h4>
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
                        <form id="addCustomerForm">
                            @csrf
                            <!-- <div class="modal-title-head people-cust-avatar">
                                <h6>Avatar</h6>
                            </div> -->
                            <!-- <div class="new-employee-field">
                                <div class="profile-pic-upload">
                                    <div class="profile-pic">
                                        <span
                                            ><i
                                                data-feather="plus-circle"
                                                class="plus-down-add"
                                            ></i>
                                            Add Image</span
                                        >
                                    </div>
                                    <div class="mb-3">
                                        <div class="image-upload mb-0">
                                            <input type="file" />
                                            <div class="image-uploads">
                                                <h4>Change Image</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                            <div class="row">
                                <div class="col-lg-4 pe-0">
                                    <div class="mb-3">
                                        <label class="form-label"
                                            >Customer Name</label
                                        >
                                        <input
                                            type="text"
                                            class="form-control"
                                            name="customer_name"
                                        />
                                    </div>
                                </div>
                                <div class="col-lg-4 pe-0">
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input
                                            type="email"
                                            class="form-control"
                                            name="email"
                                        />
                                    </div>
                                </div>
                                <div class="col-lg-4 pe-0">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone</label>
                                        {{-- pattern="\d{10,15}" maxlength="15" minlength="10" inputmode="numeric" title="Phone number must be 10 to 15 digits" --}}
                                        <input
                                            type="text"
                                            class="form-control"
                                            name="phone"
                                        />
                                    </div>
                                </div>
                                <div class="col-lg-12 pe-0">
                                    <div class="mb-3">
                                        <label class="form-label"
                                            >Address</label
                                        >
                                        <input
                                            type="text"
                                            class="form-control"
                                            name="address"
                                        />
                                    </div>
                                </div>
                                <div class="col-lg-6 pe-0">
                                    <div class="mb-3">
                                        <label class="form-label">City</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            name="city"
                                        />
                                    </div>
                                </div>
                                <div class="col-lg-6 pe-0">
                                    <div class="mb-3">
                                        <label class="form-label"
                                            >Country</label
                                        >
                                        <!-- <select class="select" name="country">
                                            <option>Choose</option>
                                            <option>United Kingdom</option>
                                            <option>United State</option>
                                        </select> -->
                                        <input
                                            type="text"
                                            class="form-control"
                                            name="country"
                                        />
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-3 input-blocks">
                                        <label class="form-label"
                                            >Descriptions (Optional)</label
                                        >
                                        <textarea
                                            class="form-control mb-1"
                                            name="description"
                                        ></textarea>
                                        <p>Maximum 60 Characters</p>
                                    </div>
                                </div>
                                <div class="col-lg-6 pe-0">
                                    <div class="mb-3">
                                        <label class="form-label">Credit limit</label>
                                        <input type="number" class="form-control" name="credit_limit" min="0" step="0.01" value="0" />
                                        <p class="small text-muted mb-0">0 = no limit</p>
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
                                    Submit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Add Customer -->

<!-- Edit Customer -->
<div class="modal fade" id="edit-customer">
    <div class="modal-dialog modal-dialog-centered custom-modal-two">
        <div class="modal-content">
            <div class="page-wrapper-new p-0">
                <div class="content">
                    <div class="modal-header border-0 custom-modal-header">
                        <div class="page-title">
                            <h4>Edit Customer</h4>
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
                        <form id="customer-edit-form">
                            @csrf
                            <input
                                type="hidden"
                                name="id"
                                id="edit-customer-id"
                            />
                            <!-- <div class="modal-title-head people-cust-avatar">
                                <h6>Avatar</h6>
                            </div> -->
                            <!-- <div class="new-employee-field">
                                <div class="profile-pic-upload">
                                    <div class="profile-pic people-profile-pic">
                                        <img
                                            src="assets/img/profiles/avatar-02.jpg"
                                            alt="Img"
                                        />
                                        <a href="#"
                                            ><i
                                                data-feather="x-square"
                                                class="x-square-add"
                                            ></i
                                        ></a>
                                    </div>
                                    <div class="mb-3">
                                        <div class="image-upload mb-0">
                                            <input type="file" />
                                            <div class="image-uploads">
                                                <h4>Change Image</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                            <div class="row">
                                <div class="col-lg-12 pe-0">
                                    <div class="mb-3">
                                        <label class="form-label"
                                            >Customer Name</label
                                        >
                                        <input
                                            type="text"
                                            class="form-control"
                                            name="customer_name"
                                        />
                                    </div>
                                </div>
                                {{-- <div class="col-lg-4 pe-0">
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input
                                            type="email"
                                            class="form-control"
                                            name="email"
                                        />
                                    </div>
                                </div>
                                 <div class="col-lg-4 pe-0">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            name="phone"
                                            pattern="\d{10,15}"
                                            maxlength="15"
                                            minlength="10"
                                            inputmode="numeric"
                                            title="Phone number must be 10 digits"
                                        />
                                    </div>
                                </div> --}}
                                <div class="col-lg-12 pe-0">
                                    <div class="mb-3">
                                        <label class="form-label"
                                            >Address</label
                                        >
                                        <input
                                            type="text"
                                            class="form-control"
                                            name="address"
                                        />
                                    </div>
                                </div>
                                <div class="col-lg-6 pe-0">
                                    <div class="mb-3">
                                        <label class="form-label">City</label>
                                        <!-- <select class="select">
                                            <option>City</option>
                                            <option>City</option>
                                        </select> -->
                                        <input
                                            type="text"
                                            class="form-control"
                                            name="city"
                                        />
                                    </div>
                                </div>
                                <div class="col-lg-6 pe-0">
                                    <div class="mb-3">
                                        <label class="form-label"
                                            >Country</label
                                        >
                                        <input
                                            type="text"
                                            class="form-control"
                                            name="country"
                                        />
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-0 input-blocks">
                                        <label class="form-label"
                                            >Descriptions</label
                                        >
                                        <textarea
                                            class="form-control mb-1"
                                            name="description"
                                        ></textarea>
                                        <p>Maximum 60 Characters</p>
                                    </div>
                                </div>
                                <div class="col-lg-6 pe-0">
                                    <div class="mb-3">
                                        <label class="form-label">Credit limit</label>
                                        <input type="number" class="form-control" name="credit_limit" id="edit-credit-limit" min="0" step="0.01" value="0" />
                                        <p class="small text-muted mb-0">0 = no limit</p>
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
<!-- /Edit Customer -->

<!-- View Customer -->
<div class="modal fade" id="view-customer">
    <div class="modal-dialog modal-dialog-centered custom-modal-two">
        <div class="modal-content">
            <div class="page-wrapper-new p-0">
                <div class="content">
                    <div class="modal-header border-0 custom-modal-header">
                        <div class="page-title">
                            <h4>View Customer</h4>
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
                            <!-- <div class="new-employee-field">
                                <div class="profile-pic-upload">
                                    <div class="profile-pic people-profile-pic">
                                        <img
                                            src="assets/img/profiles/avatar-02.jpg"
                                            alt="Img"
                                        />
                                    </div>
                                </div>
                            </div> -->
                            <div class="row">
                                <div class="col-lg-4 pe-0">
                                    <div class="mb-3">
                                        <label class="form-label"
                                            >Customer Name</label
                                        >
                                        <input
                                            type="text"
                                            class="form-control"
                                            value=""
                                            disabled
                                            name="customer_name"
                                        />
                                    </div>
                                </div>
                                <div class="col-lg-4 pe-0">
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input
                                            type="email"
                                            class="form-control"
                                            name="email"
                                            value=""
                                            disabled
                                        />
                                    </div>
                                </div>
                                <div class="col-lg-4 pe-0">
                                    <div class="mb-3">
                                        <label class="form-label">Phone</label>
                                        <input
                                            class="form-control"
                                            value=""
                                            type="text"
                                            name="phone"
                                            disabled
                                        />
                                    </div>
                                </div>
                                <div class="col-lg-12 pe-0">
                                    <div class="mb-3">
                                        <label class="form-label"
                                            >Address</label
                                        >
                                        <input
                                            type="text"
                                            class="form-control"
                                            name="address"
                                            value=""
                                            disabled
                                        />
                                    </div>
                                </div>
                                <div class="col-lg-6 pe-0">
                                    <div class="mb-3">
                                        <label class="form-label">City</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            name="city"
                                            value=""
                                            disabled
                                        />
                                    </div>
                                </div>
                                <div class="col-lg-6 pe-0">
                                    <div class="mb-3">
                                        <label class="form-label"
                                            >Country</label
                                        >
                                        <input
                                            type="text"
                                            class="form-control"
                                            name="country"
                                            value=""
                                            disabled
                                        />
                                        <!-- <select class="select" disabled>
                                            <option selected>Germany</option>
                                            <option>United State</option>
                                        </select> -->
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-0 input-blocks">
                                        <label class="form-label"
                                            >Descriptions</label
                                        >
                                        <textarea
                                            class="form-control mb-1"
                                            value=""
                                            disabled
                                            name="description"
                                        ></textarea>
                                        <p>Maximum 60 Characters</p>
                                    </div>
                                </div>
                                <div class="col-lg-6 pe-0">
                                    <div class="mb-3">
                                        <label class="form-label">Credit limit</label>
                                        <input type="text" class="form-control" id="view-credit-limit" disabled />
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /View Customer -->

@endsection @section('scripts')
<style>
    .datatable-infinite-scroll {
        overflow-y: auto;
    }
</style>
<script>
    $(document).ready(function () {
     const columns = [
         { data: 'customer_name', name: 'customer_name' },
         { data: 'code', name: 'id' },
         { data: 'email', name: 'email' },
         { data: 'phone', name: 'phone' },
         { data: 'city', name: 'city' },
         { data: 'country', name: 'country' },
         { data: 'status', name: 'status', orderable: false, searchable: false },
         { data: 'action', name: 'action', orderable: false, searchable: false },
     ];

     let paginationManager = DatatablePagination.attach({
         tableKey: 'all-customers',
         tableSelector: '#customer-data-table',
         toolbarSelector: '#all-customers-dt-toolbar',
         scrollContainerSelector: '#customer-scroll-wrap',
         config: @json($datatableConfig),
         getAjaxUrl: function () {
             return "{{ route('admin.customer') }}";
         },
         buildTableOptions: function (mode, pageLength) {
             const shared = {
                 columns: columns,
                 language: {
                     search: '',
                     searchPlaceholder: 'Search...',
                 },
                 drawCallback: function () {
                     feather.replace();
                 },
                 initComplete: function () {
                     let searchWrapper = $('#customer-data-table_filter');
                     let defaultSearchBox = searchWrapper.find('input');
                     if ($('#custom-search').find(defaultSearchBox).length === 0) {
                         $('#custom-search').append(defaultSearchBox);
                     }
                     defaultSearchBox.attr('placeholder', 'Search...');
                     searchWrapper.remove();
                 }
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
                     url: "{{ route('admin.customer') }}",
                     data: function (d) {
                         d.country = $('#country-filter').val();
                         d.order_type = $('#sort-order').val();
                     }
                 },
                 pageLength: pageLength,
                 lengthChange: false,
                 searching: true,
                 dom: 'frtip',
             });
         },
     });
 
     $('#country-filter').on('change', function () {
        paginationManager.reload(false);
    });
     $('#sort-order').on('change', function () {
         paginationManager.reload(false);
     });
 });
 
 
 </script>
<script>
    $(document).ready(function () {
        $("#addCustomerForm").on("submit", function (e) {
            e.preventDefault();

            let formData = $(this).serialize();

            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
            });

            $.ajax({
                url: "{{ route('admin.create-customer') }}",
                method: "POST",
                data: formData,
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: "success",
                            title: "Customer Added!",
                            text: response.message,
                            confirmButtonClass: "btn btn-success",
                        }).then(()=>{
                            location.reload();
                             $("#add-units").modal("hide");
                             $("#addCustomerForm")[0].reset();
                        });

                       
                        
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Oops!",
                            text: response.message || "Something went wrong.",
                            confirmButtonClass: "btn btn-danger",
                        });
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Failed to add customer!",
                        confirmButtonClass: "btn btn-danger",
                    });
                    console.log(xhr.responseText);
                },
            });
        });
    });

    $(document).ready(function () {

        // View Employee with AJAX
        $(document).on("click", ".viewCustomer", function () {
            var empId = $(this).data("id");

            $.ajax({
                url: "{{ route('admin.view-customer') }}",
                type: "GET",
                data: {
                    empId: empId,
                },
                success: function (response) {
                    console.log(response);

                    if (response.success) {
                        console.log(response);

                        $('#view-customer input[name="customer_name"]').val(
                            response.data.customer_name
                        );
                        $('#view-customer input[name="email"]').val(
                            response.data.email
                        );
                        $('#view-customer input[name="phone"]').val(
                            response.data.phone
                        );
                        $('#view-customer input[name="country"]').val(
                            response.data.country
                        );

                        $('#view-customer input[name="city"]').val(
                            response.data.city
                        );
                        $('#view-customer input[name="address"]').val(
                            response.data.address
                        );
                        $('#view-customer textarea[name="description"]').val(
                            response.data.description
                        );
                        const cl = parseFloat(response.data.credit_limit || 0);
                        $('#view-credit-limit').val(cl > 0 ? cl.toFixed(2) : 'No limit (0)');

                        $("#view-customer").modal("show");
                    } else {
                        alert("Employee details not found!");
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error:", xhr.responseText);
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Failed to View/Find customer. Please try again.",
                        confirmButtonColor: "#d33",
                    });
                },
            });
        });

        //EDIT EMPLOYEE
        $(document).on("click", ".editCustomer", function (e) {
            e.preventDefault();

            var empId = $(this).data("id");
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
            });
            $.ajax({
                url: "{{ route('admin.edit-customer') }}",
                type: "GET",
                data: {
                    empId: empId,
                },
                success: function (response) {
                    if (response.success) {
                        // console.log(response);
                        $("#edit-customer").on("shown.bs.modal", function () {
                            $("#edit-customer")
                                .find('input[name="id"]')
                                .val(response.data.id || "");
                            $("#edit-customer")
                                .find('input[name="customer_name"]')
                                .val(response.data.customer_name || "");
                            $("#edit-customer")
                                .find('input[name="email"]')
                                .val(response.data.email || "");
                            $("#edit-customer")
                                .find('input[name="phone"]')
                                .val(response.data.phone || "");
                            $("#edit-customer")
                                .find('textarea[name="description"]')
                                .val(response.data.description || "");
                            $("#edit-customer")
                                .find('input[name="address"]')
                                .val(response.data.address || "");
                            $("#edit-customer")
                                .find('input[name="city"]')
                                .val(response.data.city || "");
                            $("#edit-customer")
                                .find('input[name="country"]')
                                .val(response.data.country || "");
                            $("#edit-credit-limit").val(response.data.credit_limit ?? 0);
                            $("#edit-customer").modal("show");
                        });

                        $("#edit-customer").modal("show");
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "Error loading Customer data!",
                            confirmButtonColor: "#d33",
                        });
                    }
                },

                error: function (xhr) {
                    console.log(xhr.responseText);
                },
            });
        });

        // Update employee(users-table) with AJAX after submitting the editted data
        $("#customer-edit-form").on("submit", function (e) {
            e.preventDefault();

            var formData = new FormData(this);

            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
            });

            $.ajax({
                url: "{{ route('admin.update-customer') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: "success",
                            title: "Success",
                            text: "Customer updated successfully!",
                            confirmButtonColor: "#3085d6",
                        }).then(() => {
                            $("#edit-customer").modal("hide");
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "Error! Customer Not Found",
                            confirmButtonColor: "#d33",
                        });
                    }
                },
                error: function (xhr) {
                    console.log(xhr.responseText);
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Error updating customer!",
                        confirmButtonColor: "#d33",
                    });
                },
            });
        });
    });

    $(document).on("click", ".deleteCustomer", function (e) {
        e.preventDefault();
        const empId = $(this).data("id");
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "Cancel",
            confirmButtonClass: "btn btn-primary",
            cancelButtonClass: "btn btn-danger ml-1",
            buttonsStyling: false,
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajaxSetup({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                });

                $.ajax({
                    url: "{{ route('admin.delete-customer') }}",
                    type: "DELETE",
                    data: {
                        empId: empId,
                        _token: "{{ csrf_token() }}",
                    },
                    success: function (response) {
                        if (response.success) {
                            console.log(response);
                            Swal.fire({
                                icon: "success",
                                title: "Deleted!",
                                text: "customer has been deleted successfully.",
                                confirmButtonClass: "btn btn-success",
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Error!",
                                text: "Error deleting customer!",
                                confirmButtonClass: "btn btn-danger",
                            });
                        }
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: "error",
                            title: "Error!",
                            text: "Error deleting customer!",
                            confirmButtonClass: "btn btn-danger",
                        });
                        console.log(xhr.responseText);
                    },
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire({
                    title: "Cancelled",
                    text: "The customer record is safe :)",
                    icon: "error",
                    confirmButtonClass: "btn btn-success",
                });
            }
        });
    });

    $(document).on('change', '.customer-status-toggle', function () {
        const checkbox = $(this);
        const customerId = checkbox.data('id');
        const newStatus = checkbox.is(':checked') ? 1 : 0;
        const actionText = newStatus ? 'activate' : 'deactivate';

        checkbox.prop('checked', !newStatus);

        Swal.fire({
            title: 'Are you sure?',
            text: `Do you want to ${actionText} this customer?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, ' + actionText,
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }

            $.ajax({
                url: "{{ route('admin.change-status-customer') }}",
                method: 'POST',
                data: {
                    id: customerId,
                    status: newStatus,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    if (res.success) {
                        Swal.fire('Success!', res.message, 'success');
                        checkbox.prop('checked', newStatus);
                        const activeTable = $.fn.DataTable.isDataTable('#customer-data-table') ? $('#customer-data-table').DataTable() : null;
                        if (activeTable) {
                            activeTable.ajax.reload(null, false);
                        }
                    } else {
                        Swal.fire('Error!', res.message || 'Could not update status.', 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error!', 'Something went wrong.', 'error');
                }
            });
        });
    });
</script>
@endsection
