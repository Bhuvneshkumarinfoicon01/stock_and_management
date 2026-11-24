@extends('layouts.app_admin') @section('title') Dashbaord @endsection
@section('styles') @endsection @section('content')

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4>Ledger</h4>
                    <h6>Manage Your Ledger</h6>
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
                    data-bs-target="#add-ledger"
                    ><i data-feather="plus-circle" class="me-2"></i> Add New
                    Ledger</a
                >
            </div>
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
                            <option>11 09 23</option>
                            <option>20 09 23</option>
                        </select>
                    </div>
                </div>
                <!-- /Filter -->
                <div class="card" id="filter_inputs">
                    <div class="card-body pb-0">
                        <div class="row">
                            <div class="col-lg-2 col-sm-6 col-12">
                                <div class="input-blocks">
                                    <i data-feather="user" class="info-img"></i>
                                    <select class="select">
                                        <option>Select Store</option>
                                        <option>Storename 1</option>
                                        <option>Storename 2</option>
                                        <option>Storename 3</option>
                                        <option>Storename 4</option>
                                        <option>Storename 5</option>
                                        <option>Storename 6</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-12">
                                <div class="input-blocks">
                                    <i
                                        data-feather="stop-circle"
                                        class="info-img"
                                    ></i>
                                    <select class="select">
                                        <option>Entry Type</option>
                                        <option>Debit</option>
                                        <option>Credit</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-12">
                                <div class="input-blocks">
                                    <i
                                        data-feather="calendar"
                                        class="info-img"
                                    ></i>
                                    <div class="input-groupicon">
                                        <input
                                            type="text"
                                            class="datetimepicker"
                                            placeholder="From Date - To Date"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6 col-12">
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
                                <th>Date</th>
                                <th>store</th>

                                <th class="no-sort">Action</th>
                            </tr>
                        </thead>
                        <tbody class="Expense-list-blk">
                            @foreach($ledgers as $ledger)
                            <tr>
                                <td>
                                    <label class="checkboxs">
                                        <input type="checkbox" />
                                        <span class="checkmarks"></span>
                                    </label>
                                </td>
                                <td>{{ format_date($ledger->ledger_date) }}</td>
                                <td>{{$ledger->store->name}}</td>
                                <td class="action-table-data">
                                    <div class="edit-delete-action">
                                        <a
                                            class="me-2 p-2 mb-0"
                                            href="{{
                                                route('admin.ledgerEntries',['id'=>$ledger->id])
                                            }}"
                                        >
                                            <i
                                                data-feather="eye"
                                                class="action-eye"
                                            ></i>
                                        </a>
                                        <a
                                            class="me-2 p-2 mb-0 edit-ledger-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#edit-ledger"
                                            data-id="{{ $ledger->id }}"
                                            data-ledger-date="{{$ledger->ledger_date}}"
                                            data-store="{{$ledger->store->name}}"
                                        >
                                            <i
                                                data-feather="edit"
                                                class="feather-edit"
                                            ></i>
                                        </a>
                                        <a
                                            class="me-3 confirm-text p-2 mb-0 delete-ledger-btn"
                                            href="javascript:void(0);"
                                            data-id="{{ $ledger->id }}"
                                        >
                                            <i
                                                data-feather="trash-2"
                                                class="feather-trash-2"
                                            ></i>
                                        </a>
                                    </div>
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
<!-- ADD LEDGER -->
<div class="modal fade" id="add-ledger">
    <div class="modal-dialog modal-dialog-centered custom-modal-two">
        <div class="modal-content">
            <div class="page-wrapper-new p-0">
                <div class="content">
                    <div class="modal-header border-0 custom-modal-header">
                        <div class="page-title">
                            <h4>Add Ledger</h4>
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
                        <form id="addLedgerForm">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label class="form-label"
                                            >Store Name</label
                                        >
                                        <select
                                            class="select form-control"
                                            id="store_id"
                                            name="store_id"
                                        >
                                            <option value="">Choose</option>
                                        </select>
                                        <span
                                            class="text-danger error-store_id"
                                        ></span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="input-blocks">
                                        <i
                                            data-feather="calendar"
                                            class="info-img"
                                        ></i>
                                        <div class="input-groupicon">
                                            <input
                                                type="text"
                                                class="datetimepicker"
                                                placeholder="Select Date"
                                                name="ledger_date"
                                            />
                                        </div>
                                        <span
                                            class="text-danger error-ledger_date"
                                        ></span>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table
                                            class="table datanew"
                                            id="ledgerTable"
                                        >
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Description</th>
                                                    <th>Debit</th>
                                                    <th>Credit</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <input
                                                            type="date"
                                                            class="form-control"
                                                            name="date[]"
                                                        />
                                                        <span
                                                            class="text-danger error-date"
                                                        ></span>
                                                    </td>
                                                    <td>
                                                        <input
                                                            type="text"
                                                            class="form-control border-black border-1"
                                                            name="description[]"
                                                        />
                                                        <span
                                                            class="text-danger error-description"
                                                        ></span>
                                                    </td>
                                                    <td>
                                                        <input
                                                            type="text"
                                                            class="form-control border-black border-1"
                                                            name="debit[]"
                                                        />
                                                    </td>
                                                    <td>
                                                        <input
                                                            type="text"
                                                            class="form-control border-black border-1"
                                                            name="credit[]"
                                                        />
                                                    </td>
                                                    <td>
                                                        <a
                                                            class="me-3 remove-row text-lg p-2 mb-0"
                                                            href="javascript:void(0);"
                                                        >
                                                            <i
                                                                data-feather="trash-2"
                                                                class="feather-trash-2"
                                                            ></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="text-end mt-2">
                                    <button
                                        type="button"
                                        class="btn btn-primary"
                                        id="addRowBtn"
                                    >
                                        <i data-feather="plus"></i> Add Row
                                    </button>
                                </div>
                            </div>
                            <div class="modal-footer-btn">
                                <a
                                    href="javascript:void(0);"
                                    class="btn btn-cancel me-2"
                                    data-bs-dismiss="modal"
                                    >Cancel</a
                                >
                                <button type="submit" class="btn btn-submit">
                                    Save
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END ADD LEDGER -->
<!-- EDIT LEDGER -->
<div class="modal fade" id="edit-ledger">
    <div class="modal-dialog modal-dialog-centered custom-modal-two">
        <div class="modal-content">
            <div class="page-wrapper-new p-0">
                <div class="content">
                    <div class="modal-header border-0 custom-modal-header">
                        <div class="page-title">
                            <h4>Edit Ledger</h4>
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
                        <form id="editLedgerForm">
                            <input type="hidden" id="edit-ledger-id" />
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label class="form-label"
                                            >Store Name</label
                                        >
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="edit-ledger-store"
                                            name="store_name"
                                            readonly
                                            disabled
                                        />
                                        <span
                                            class="text-danger error-store_name"
                                        ></span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="input-blocks">
                                        <i
                                            data-feather="calendar"
                                            class="info-img"
                                        ></i>
                                        <div class="input-groupicon">
                                            <input
                                                type="text"
                                                class="datetimepicker"
                                                id="edit-ledger-date"
                                                placeholder="Select Date"
                                                name="ledger_date"
                                            />
                                            <span
                                                class="text-danger error-ledger_date"
                                            ></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table
                                            class="table datanew"
                                            id="ledgerTable"
                                        >
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Description</th>
                                                    <th>Debit</th>
                                                    <th>Credit</th>
                                                </tr>
                                            </thead>
                                            <tbody
                                                class="Expense-list-blk"
                                                id="edit-table"
                                            >
                                                <tr>
                                                    <td>
                                                        <input
                                                            type="date"
                                                            class="form-control"
                                                            name="date[]"
                                                        />
                                                        <span
                                                            class="text-danger error-date"
                                                        ></span>
                                                    </td>
                                                    <td>
                                                        <input
                                                            type="text"
                                                            class="form-control border-black border-1"
                                                            name="description[]"
                                                        />
                                                        <span
                                                            class="text-danger error-description"
                                                        ></span>
                                                    </td>
                                                    <td>
                                                        <input
                                                            type="text"
                                                            class="form-control border-black border-1"
                                                            name="debit[]"
                                                        />
                                                    </td>
                                                    <td>
                                                        <input
                                                            type="text"
                                                            class="form-control border-black border-1"
                                                            name="credit[]"
                                                        />
                                                    </td>
                                                    <td>
                                                        <a
                                                            class="me-3 remove-row text-lg p-2 mb-0"
                                                            href="javascript:void(0);"
                                                        >
                                                            <i
                                                                data-feather="trash-2"
                                                                class="feather-trash-2"
                                                            ></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end mt-2">
                                <button
                                    type="button"
                                    class="btn btn-primary"
                                    id="addRowBtn"
                                >
                                    <i data-feather="plus"></i> Add Row
                                </button>
                            </div>
                            <div class="modal-footer-btn">
                                <a
                                    href="javascript:void(0);"
                                    class="btn btn-cancel me-2"
                                    data-bs-dismiss="modal"
                                    >Cancel</a
                                >
                                <a
                                    href="javascript:void(0);"
                                    class="btn btn-submit"
                                    id="updateLedgerBtn"
                                    >Update</a
                                >
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END EDIT LEDGER -->

@endsection @section('scripts')
<script>
    $(document).ready(function () {
        $("#add-ledger, #edit-ledger").on("shown.bs.modal", function () {
            const storeDropdown = $(this).find("#store_id");

            $.ajax({
                url: "{{ route('admin.get-stores') }}",
                type: "GET",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (response) {
                    if (response.error) {
                        Swal.fire({
                            icon: "success",
                            title: "Success!",
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false,
                        });
                    } else {
                        storeDropdown
                            .empty()
                            .append('<option value="">Choose</option>');
                        $.each(response, function (_, store) {
                            storeDropdown.append(
                                `<option value="${store.id}">${store.name}</option>`
                            );
                        });
                    }
                },
                error: function () {
                    Swal.fire("Error!", "Failed to load stores.", "error");
                },
            });
        });

        // Add Ledger Form Submission
        $("#addLedgerForm").submit(function (e) {
            e.preventDefault();

            let formData = {
                store_id: $("#store_id").val(),
                ledger_date: $("input[name='ledger_date']").val(),
                date: [],
                description: [],
                debit: [],
                credit: [],
            };

            $(".datanew tbody tr").each(function () {
                let entryDate = $(this)
                    .find("td:eq(0) input[type='date']")
                    .val();
                if (entryDate) {
                    formData.date.push(entryDate);
                    formData.description.push(
                        $(this).find("td:eq(1) input").val() || null
                    );
                    formData.debit.push(
                        $(this).find("td:eq(2) input").val() || null
                    );
                    formData.credit.push(
                        $(this).find("td:eq(3) input").val() || null
                    );
                }
            });

            $.ajax({
                url: "{{ route('admin.store-ledger') }}",
                type: "POST",

                data: formData,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (response) {
                    Swal.fire({
                        icon: "success",
                        title: "Success!",
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false,
                    });
                    $("#add-ledger").modal("hide");
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $(".error-date").text("");
                        $(".error-description").text("");
                        $.each(errors, function (field, messages) {
                            if (field === "date" || field === "description") {
                                $(".datanew tbody tr").each(function (i, row) {
                                    const $row = $(row);
                                    const errorSpan = $row.find(
                                        ".error-" + field
                                    );

                                    if (
                                        $row
                                            .find(`[name="${field}[]"]`)
                                            .val() === ""
                                    ) {
                                        errorSpan.text(messages[0]);
                                        return false;
                                    }
                                });
                            } else {
                                $(".error-" + field).text(messages[0]);
                            }
                        });
                    }
                },
            });
            $("#add-ledger").on("hidden.bs.modal", function () {
                $(this).find("form")[0].reset();
                $(this).find("select").val("").trigger("change");
                $(this).find(".text-danger").text("");
            });
        });

        // Add Row in table
        $(document).on("click", "#addRowBtn", function () {
            let newRow = `
                <tr>
                    <td><input type="date" class="form-control" name="date[]"><span class="text-danger error-date"></span></td>
                    <td><input type="text" class="form-control border-black border-1" name="description[]"><span class="text-danger error-description"></span></td>
                    <td><input type="text" class="form-control border-black border-1" name="debit[]"></td>
                    <td><input type="text" class="form-control border-black border-1" name="credit[]"></td>
                    <td>
                        <a class="me-3 confirm-text p-2 mb-0 remove-row" href="javascript:void(0);">
                            <i data-feather="trash-2" class="feather-trash-2"></i>
                        </a>
                    </td>
                </tr>
            `;
            $("#ledgerTable tbody").append(newRow);
            feather.replace();
        });

        // Remove row
        $(document).on("click", ".remove-row", function () {
            $(this).closest("tr").remove();
        });

        // Open Edit Modal and Populate Data
        $(document).on("click", ".edit-ledger-btn", function () {
            const ledgerId = $(this).data("id");

            // Store the ID in a hidden field
            $("#edit-ledger-id").val(ledgerId);

            $.get(`/admin/ledger/${ledgerId}/entries`, function (response) {
                const { store_name, ledger_date, entries, store_id } = response;

                $("#edit-store_id").val(store_id);
                $("#edit-ledger-store").val(store_name);
                if (ledger_date) {
                    var formattedDate = moment(
                        ledger_date,
                        "YYYY-MM-DD"
                    ).format(window.appDatePickerFormat || "DD-MMM-YYYY");
                    $("#edit-ledger-date").val(formattedDate);
                    console.log("Formatted ledger date:", formattedDate);
                }
                $("#edit-table").html("");

                entries.forEach((entry) => {
                    $("#edit-table").append(`
                        <tr>
                            <td>
                                <input type="date" class="form-control" name="date[]" value="${
                                    entry.entry_date ?? ""
                                }">
                                <span class="text-danger error-date"></span>
                            </td>
                            <td>
                            <input type="text" class="form-control border-black border-1" name="description[]" value="${
                                entry.description ?? ""
                            }">
                            <span class="text-danger error-description"></span>
                        </td>

                            <td><input type="text" class="form-control border-black border-1" name="debit[]" value="${
                                entry.debit ?? ""
                            }"></td>
                            <td><input type="text" class="form-control border-black border-1" name="credit[]" value="${
                                entry.credit ?? ""
                            }"></td>
                            <td>
                                <a class="me-3 remove-row text-lg p-2 mb-0" href="javascript:void(0);">
                                    <i data-feather="trash-2" class="feather-trash-2"></i>
                                </a>
                            </td>
                        </tr>
                    `);
                });

                feather.replace();
            });
        });

        // Update Ledger
        $(document).on("click", "#updateLedgerBtn", function () {
            const ledgerId = $("#edit-ledger-id").val();
            const form = $("#editLedgerForm");

            $.ajax({
                url: `/admin/ledger/${ledgerId}/update`,
                method: "POST",
                data: form.serialize(),
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (response) {
                    Swal.fire({
                        icon: "success",
                        title: "Success!",
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false,
                    });
                    $("#edit-ledger").modal("hide");
                },
                error: function (xhr) {
                    $(".error-date").text("");
                    $(".error-description").text("");

                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;

                        $.each(errors, function (key, messages) {
                            if (
                                key.startsWith("date.") ||
                                key.startsWith("description.")
                            ) {
                                const parts = key.split(".");
                                const field = parts[0];
                                const index = parts[1];

                                const cleanMessage = messages[0].replace(
                                    /\.\d+/,
                                    ""
                                );
                                const row = $("#edit-table tr").eq(index);
                                row.find(`.error-${field}`).text(cleanMessage);
                            }
                        });
                    }
                },
            });
        });
    });

    $(document).on("click", ".delete-ledger-btn", function () {
        const ledgerId = $(this).data("id");

        Swal.fire({
            title: "Are you sure?",
            text: "This action cannot be undone!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/ledger/${ledgerId}`,
                    type: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (response) {
                        Swal.fire("Deleted!", response.message, "success");
                    },
                    error: function (xhr) {
                        console.log(xhr.responseText);
                        Swal.fire(
                            "Error!",
                            "Failed to delete ledger.",
                            "error"
                        );
                    },
                });
            }
        });
    });
</script>
@endsection
