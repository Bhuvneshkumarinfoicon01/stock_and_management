@extends('layouts.app_admin') @section('title') Dashbaord @endsection
@section('styles') @endsection @section('content')

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4>Unit List</h4>
                    <h6>Manage your Units</h6>
                </div>
            </div>

            <div class="page-btn">
                <button
                    class="btn btn-added"
                    data-bs-toggle="modal"
                    data-bs-target="#categoryModal"
                    id="addCategoryBtn"
                >
                    <i data-feather="plus-circle" class="me-2"></i> Add Unit
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                 <div class="table-top">
                    <div class="search-set">
                        <div class="search-input d-flex align-items-center">
                            <div id="custom-search" class="me-2"></div>
                            <a href="#" class="btn btn-searchset">
                                <i data-feather="search" class="feather-search"></i>
                            </a>
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
                                        <option value="1" >Active</option>
                                        <option value="0">Inactive</option>
                                    </select>

                                </div>
                            </div>
                           
                        </div>
                    </div>
                </div>
                <!-- /Filter -->
                <div class="table-responsive">
                    <table class="table" id="units-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Unit Modal -->
<div
    class="modal fade"
    id="categoryModal"
    tabindex="-1"
    aria-labelledby="categoryModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalLabel">Add Unit</h5>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
            </div>
            <div class="modal-body">
                <form id="categoryForm">
                    <input type="hidden" name="id" id="categoryId" />
                    <div class="mb-3">
                        <label for="categoryName" class="form-label"
                            >Unit Name</label
                        >
                        <input
                            type="text"
                            class="form-control"
                            id="categoryName"
                            name="name"
                            required
                        />
                    </div>
                    <div class="mb-3 form-check form-switch">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="categoryStatus"
                            name="status"
                            checked
                        />
                        <label class="form-check-label" for="categoryStatus"
                            >Active</label
                        >
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="btn btn-primary"
                            id="saveCategoryBtn"
                        >
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection @section('scripts')
<script>
    $(document).ready(function () {
     let table = $('#units-table').DataTable({
         processing: true,
         serverSide: true,
         ajax: {
             url: "{{ route('admin.units.index') }}",
             data: function (d) {
				d.status = $('#status-filter').val();  
                d.order_type = $('#sort-order').val();
                
             }
         },
         columns: [
			 { data: 'unit_name', name: 'name' },
             { data: 'status_badge', name: 'status_badge'},
             { data: 'actions', name: 'actions', orderable: false, searchable: false },
         ],
         pageLength: 10,
         drawCallback: function () {
             feather.replace();
         },
         initComplete: function () {
                let searchWrapper = $('#units-table_filter');
                let defaultSearchBox = searchWrapper.find('input');
                $('#custom-search').append(defaultSearchBox);
                defaultSearchBox.attr('placeholder', 'Search...');
                searchWrapper.remove();
            }
     });
 
     
	  $('#status-filter').on('change', function () {
            table.ajax.reload();
        });
     $('#sort-order').on('change', function () {
         table.ajax.reload();
     });
 });
 
 
 </script>
<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        // Load units dynamically
        function loadCategories() {
            $.ajax({
                url: "{{ route('admin.units.index') }}",
                type: "GET",
                success: function (data) {
                    $("#categoryTableBody").html(data);
                },
            });
        }

        // Open modal for adding unit
        $("#addCategoryBtn").click(function () {
            $("#categoryForm")[0].reset();
            $("#categoryId").val("");
            $("#categoryModalLabel").text("Add Unit");
            $("#saveCategoryBtn").text("Create");
            $("#categoryModal").modal("show");
        });

        // Save unit (Create or Update)
        $("#categoryForm").submit(function (e) {
            e.preventDefault();

            let id = $("#categoryId").val();
            let formData = {
                name: $("#categoryName").val(),
                status: $("#categoryStatus").is(":checked") ? 1 : 0,
            };

            let url = id
                ? "/admin/units/" + id
                : "{{ route('admin.units.store') }}";
            let type = id ? "PUT" : "POST";

            $.ajax({
                type: type,
                url: url,
                data: formData,
                success: function (response) {
                    $("#categoryModal").modal("hide");
                    // loadCategories();
                    if ($.fn.DataTable.isDataTable("#units-table")) {
                        $("#units-table").DataTable().ajax.reload(null, false);
                    }
                    showAlert("success", response.message);
                },
                error: function (xhr) {
                    showAlert("error", xhr.responseJSON.message);
                },
            });
        });

        // Edit unit
        $(document).on("click", ".edit-category", function () {
            let id = $(this).data("id");
            $.get("/admin/units/" + id + "/edit", function (data) {
                $("#categoryId").val(data.id);
                $("#categoryName").val(data.name);
                $("#categoryStatus").prop("checked", data.status == 1);
                $("#categoryModalLabel").text("Edit Unit");
                $("#saveCategoryBtn").text("Update");
                $("#categoryModal").modal("show");
            });
        });

        // Delete unit
        $(document).on("click", ".delete-category", function () {
            let id = $(this).data("id");
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "DELETE",
                        url: "/admin/units/" + id,
                        success: function (response) {
                            // loadCategories();
                            if ($.fn.DataTable.isDataTable("#units-table")) {
                                $("#units-table").DataTable().ajax.reload(null, false);
                            }
                            showAlert("success", response.message);
                        },
                    });
                }
            });
        });

        // Search Units

        // Function to show alerts
        function showAlert(type, message) {
            Swal.fire({
                icon: type,
                title: message,
                showConfirmButton: false,
                timer: 2000,
            });
        }

        // loadCategories();
    });
</script>
@endsection
