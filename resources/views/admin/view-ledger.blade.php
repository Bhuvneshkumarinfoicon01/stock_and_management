@extends('layouts.app_admin') @section('title') Dashbaord @endsection
@section('styles') @endsection @section('content')

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4>Ledger</h4>
                    <h6>View Your Ledger</h6>
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
            <div class="page-btn d-flex flex-wrap gap-2 align-items-center">
                @include('admin.partials.record-navigator')
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
                <div class="page-title m-3">
                    <h2 class="mb-2">Store: {{$ledger->store->name}}</h2>
                    <h4>
                        Date:
                        {{ format_date($ledger->ledger_date) }}
                    </h4>
                </div>
                <div class="table-responsive">
                    <table class="table datanew">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Debit</th>
                                <th>Credit</th>
                            </tr>
                        </thead>
                        <tbody class="Expense-list-blk">
                            @foreach($ledgerEntries as $ledgerEntry)
                            <tr>
                                <td>{{ format_date($ledgerEntry->entry_date) }}</td>
                                <td>{{$ledgerEntry->description}}</td>
                                <td>{{$ledgerEntry->debit}}</td>
                                <td>{{$ledgerEntry->credit}}</td>
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
                                                    </td>
                                                    <td>
                                                        <input
                                                            type="text"
                                                            class="form-control border-black border-1"
                                                            name="description[]"
                                                        />
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

<div class="modal fade" id="edit-ledger">
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
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label">Store Name</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        value="Storname 1....."
                                    />
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table datanew">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Description</th>
                                                <th>Debit</th>
                                                <th>Credit</th>
                                            </tr>
                                        </thead>
                                        <tbody class="Expense-list-blk">
                                            <tr>
                                                <td>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        value="12-04-24"
                                                    />
                                                </td>
                                                <td>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        value="Expenses related to employee compensation"
                                                    />
                                                </td>
                                                <td>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        value="550.00"
                                                    />
                                                </td>
                                                <td>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        value=""
                                                    />
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        value="19-04-24"
                                                    />
                                                </td>
                                                <td>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        value="Payments for using office space or facilities"
                                                    />
                                                </td>
                                                <td>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        value=""
                                                    />
                                                </td>
                                                <td>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        value="6008.36"
                                                    />
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer-btn">
                            <a
                                href="javascript:void(0);"
                                class="btn btn-cancel me-2"
                                data-bs-dismiss="modal"
                                >Cancel</a
                            >
                            <a href="javascript:void(0);" class="btn btn-submit"
                                >Create</a
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection @section('scripts')
<script>
    $(document).ready(function () {
        // Fetch Stores
        $("#add-ledger").on("shown.bs.modal", function (e) {
            var modalId = $(this).attr("id");
            var storeDropdown = $("#" + modalId + " #store_id");

            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
            });
            // Fetch Stores
            $.ajax({
                url: "{{ route('admin.get-stores') }}",
                type: "GET",
                success: function (response) {
                    if (response.error) {
                        alert(response.error);
                    } else {
                        storeDropdown
                            .empty()
                            .append('<option value="">Choose</option>');
                        $.each(response, function (key, store) {
                            storeDropdown.append(
                                '<option value="' +
                                    store.id +
                                    '">' +
                                    store.name +
                                    "</option>"
                            );
                        });
                        storeDropdown.on("change", function () {
                            console.log("Selected Store ID:", $(this).val());
                        });
                    }
                },
                error: function () {
                    alert("Failed to load stores.");
                },
            });
        });
        $(document).ready(function () {
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
                        // Ensure only valid dates are added
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
                    url: "{{route('admin.store-ledger')}}",
                    type: "POST",
                    data: formData,
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (response) {
                        alert(response.message);
                        $("#add-ledger").modal("hide");
                        location.reload();
                    },
                    error: function (xhr) {
                        console.log(xhr.responseText);
                        alert("Error adding ledger");
                    },
                });
            });
        });
    });

    $(document).ready(function () {
        // Add Row Function
        $("#addRowBtn").click(function () {
            console.log("Add Row Button Clicked"); // Debugging line

            var newRow = `
            <tr>
                <td>
                    <input type="date" class="form-control" name="date[]">
                </td>
                <td>
                    <input type="text" class="form-control border-black border-1" name="description[]">
                </td>
                <td>
                    <input type="text" class="form-control border-black border-1" name="debit[]">
                </td>
                <td>
                    <input type="text" class="form-control border-black border-1" name="credit[]">
                </td>
                <td>
                    <a class="me-3 confirm-text p-2 mb-0 remove-row" href="javascript:void(0);">
                    <i data-feather="trash-2" class="feather-trash-2"></i>                     
                    </a>                                      
                </td>
            </tr>`;

            $("#ledgerTable tbody").append(newRow);
            feather.replace();
        });

        // Remove Row Function
        $(document).on("click", ".remove-row", function () {
            $(this).closest("tr").remove();
        });
    });
</script>
@endsection
