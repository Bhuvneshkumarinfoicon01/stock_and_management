@extends('layouts.app_admin') @section('title') Dashbaord @endsection
@section('styles') @endsection @section('content')
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
                    <div class="search-set">
                        <div class="search-input">
                            <a href="#" class="btn btn-searchset"><i data-feather="search"
                                    class="feather-search"></i></a>
                        </div>
                    </div>
                    <div class="search-path">
                        <a class="btn btn-filter" id="filter_search">
                            <i data-feather="filter" class="filter-icon"></i>
                            <span><img src="assets/img/icons/closes.svg" alt="img" /></span>
                        </a>
                    </div>
                    <div class="form-sort">
                        <i data-feather="sliders" class="info-img"></i>
                        <select class="select sort-users">
                            <option value="" selected>Sort by</option>
                            <option value="desc" {{ request('sort')=='desc' ? 'selected' : '' }}>Newest First</option>
                            <option value="asc" {{ request('sort')=='asc' ? 'selected' : '' }}>Oldest First</option>
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
                                    <select class="select user-status">
                                        <option value="" selected>Choose Status</option>
                                        <option value="1" {{ request('status')=='1' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ request('status')=='0' ? 'selected' : '' }}>Inactive
                                        </option>
                                    </select>

                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-12 ms-auto">
                                <div class="input-blocks">
                                    <a class="btn btn-filters ms-auto">
                                        <i data-feather="search" class="feather-search"></i>
                                        Searchsss
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
                                <th>Store name</th>
                                <th>Address</th>
                                <th>City</th>
                                <th>State</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stores as $store)
                            <tr>
                                <td>{{ $store->name }}</td>
                                <td>{{ $store->address }}</td>
                                <td>{{ $store->city }}</td>
                                <td>{{ $store->state }}</td>
                                <td>{{ $store->phone }}</td>
                                <td>{{ $store->email }}</td>
                                <td>
                                    @if ($store->status == 1)
                                    <span class="badge badge-linesuccess">Active</span>
                                    @else
                                    <span class="badge badge-linedanger">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach

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
    <div class="modal-dialog modal-dialog-centered custom-modal-two">
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
                    <div class="modal-body custom-modal-body scroll-modal">
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
                                <input type="text" class="form-control" inputmode="numeric" pattern="[0-9]*" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="text" class="form-control" />
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
<div class="modal fade" id="edit-stores">
    <div class="modal-dialog modal-dialog-centered custom-modal-two">
        <div class="modal-content">
            <div class="page-wrapper-new p-0">
                <div class="content">
                    <div class="modal-header border-0 custom-modal-header">
                        <div class="page-title">
                            <h4>Edit Store w</h4>
                        </div>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body custom-modal-body scroll-modal">
                        <form>
                            <div class="mb-3">
                                <label class="form-label">Store Name 1</label>
                                <input type="text" class="form-control" />
                            </div>
                            <!-- <div class="mb-3">
                                        <label class="form-label">User Name</label>
                                        <input type="text" class="form-control" value="FredJ25">
                                    </div>
                                    <div class="input-blocks mb-3">
                                        <label>Password</label>
                                        <div class="pass-group">
                                            <input type="password" class=" pass-input" value="1234">
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
                                <input type="email" class="form-control" />
                            </div>
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
<script>
    $(document).ready(function () {
        // Sort Dropdown Handler
        $(".sort-users").change(function () {
            var sort = $(this).val();
            var status = $(".user-status").val(); // Get the selected status
            loadStoreData(sort, status);
        });

        // Handle status dropdown change
        $(".user-status").change(function () {
            var status = $(this).val();
            var sort = $(".sort-users").val(); // Get the selected sort
            loadStoreData(sort, status);
        });

        // Function to load store data
        function loadStoreData(sort, status) {
            $.ajax({
                url: "{{ route('admin.store-list-superadmin') }}", // Route to controller method
                method: "GET",
                data: {
                    sort: sort,
                    status: status
                },
                success: function (response) {
                    // Update the table body with new data
                    $("tbody").html(response);
                }
            });
        }

        // Initialize DataTable After Page Load
        if ($(".datanew").length > 0) {
            initDataTable();
        }

        function initDataTable() {
            if ($.fn.DataTable.isDataTable(".datanew")) {
                $(".datanew").DataTable().destroy(); // Destroy existing table instance
            }
            $(".datanew").DataTable({
                bFilter: true,
                sDom: "fBtlpi",
                ordering: true,
                language: {
                    search: " ",
                    sLengthMenu: "_MENU_",
                    searchPlaceholder: "Search",
                    info: "_START_ - _END_ of _TOTAL_ items",
                    paginate: {
                        next: ' <i class=" fa fa-angle-right"></i>',
                        previous: '<i class="fa fa-angle-left"></i> ',
                    },
                },
                initComplete: (settings, json) => {
                    $(".dataTables_filter").appendTo("#tableSearch");
                    $(".dataTables_filter").appendTo(".search-input");
                },
            });
        }
    });
</script>
@endsection