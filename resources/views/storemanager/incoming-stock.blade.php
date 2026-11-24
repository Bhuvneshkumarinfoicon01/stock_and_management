@extends('layouts.app_admin') @section('title') Dashbaord @endsection
@section('styles') @endsection @section('content')

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4>Incoming Stock</h4>
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
                            <div class="col-lg-3">
                                <div class="input-blocks">
                                    <i data-feather="box" class="info-img"></i>
                                    <select id="status-filter" class="select">

                                        <option value="choose">Choose status</option>
                                        <option value="">Pending</option>
                                        <option value="1">Accepted</option>
                                        <option value="0">Rejected</option>
                                    </select>
                                </div>
                            </div>
                            <!-- <div class="col-lg-6 col-sm-6 col-12">
                                <div class="input-blocks">
                                    <a class="btn btn-filters ms-auto">
                                        <i data-feather="search" class="feather-search"></i>
                                        Search
                                    </a>
                                </div>
                            </div> -->
                        </div>
                    </div>
                </div>
                <!-- /Filter -->
                <div class="table-responsive">
                    <table class="table datanew" id="incoming-stock-table">
                        <thead>
                            <tr>
                                <th>Reference No.</th>
                                <th>Arrival Date</th>
                                <th>Product Name</th>
                                <th>Supplier Name</th>
                                <th>Incoming Qty</th>
                                <th>Instock Qty</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')


<script>
    $(document).ready(function () {
        let table = $('#incoming-stock-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.incoming-stock') }}",
                data: function (d) {
                    d.status = $('#status-filter').val();
                    d.order_type = $('#sort-order').val();
                }
            },
            columns: [
                { data: 'reference_no', name: 'reference_no' },
                { data: 'arrival_date', name: 'arrival_date' },
                { data: 'product_name', name: 'product_name' },
                { data: 'supplier_name', name: 'supplier_name' },
                { data: 'incoming_qty', name: 'incoming_qty' },
                { data: 'instock_qty', name: 'instock_qty' },
                { data: 'status', name: 'status', orderable: false, searchable: false }
            ],
            pageLength: 10,
            initComplete: function () {
                let searchWrapper = $('#incoming-stock-table_filter');
                let defaultSearchBox = searchWrapper.find('input');
                $('#custom-search').append(defaultSearchBox);
                defaultSearchBox.attr('placeholder', 'Search Stocks...');

                searchWrapper.remove();
            }
        });

        // Reload on status filter change
        $('#status-filter').on('change', function () {
            table.ajax.reload();
        });
        $('#sort-order').on('change', function () {
            table.ajax.reload();
        });
    });

</script>
<script>

    /*document.querySelectorAll('.choose-status').forEach(button => {
        button.addEventListener('click', function () {
            const itemId = this.getAttribute('data-id');
            const productId = this.getAttribute('data-product');
            const referenceNo = this.getAttribute('data-reference');
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: "btn btn-success",
                    denyButton: "btn btn-danger"


                },
                buttonsStyling: true
            });
            swalWithBootstrapButtons.fire({
                title: "Choose Status",
                text: "Do you want to accept or reject the transfer?",
                icon: "warning",
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: "Accept",
                denyButtonText: "Reject",

                reverseButtons: false
            }).then((result) => {
                if (result.isConfirmed) {
                    swalWithBootstrapButtons.fire({
                        title: "Accepted",
                        text: "Transfer accepted successfully!",
                        icon: "success"
                    });
                    updateStatus(itemId, 1);
                    // window.location.reload();
                } else if (result.isDenied) {
                    swalWithBootstrapButtons.fire({
                        title: "Rejected",
                        text: "Transfer rejected successfully!",
                        icon: "error"
                    }).then(() => {
                        updateStatus(itemId, 0);
                        // window.location.reload();
                    });
                }
            });
        });
    });*/

    function updateStatus(id, status, productId, referenceNo) {
        fetch(`/admin/update-transfer-status/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({
                status,
                product_id: productId,  // Add product_id to the body
                reference_no: referenceNo
            }),
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Updated!', data.message, 'success').then(() => {
                        $('#incoming-stock-table').DataTable().ajax.reload(); // or use DataTable reload
                    });
                } else {
                    Swal.fire('Error!', data.message, 'error');
                }
            });
    }

    function showalert(that, rowid) {

        //const itemId = this.getAttribute('data-id');
        const productId = that.getAttribute('data-product');
        const referenceNo = that.getAttribute('data-reference');
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success",
                denyButton: "btn btn-danger"


            },
            buttonsStyling: true
        });
        swalWithBootstrapButtons.fire({
            title: "Choose Status",
            text: "Do you want to accept or reject the transfer?",
            icon: "warning",
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: "Accept",
            denyButtonText: "Reject",

            reverseButtons: false
        }).then((result) => {
            if (result.isConfirmed) {
                swalWithBootstrapButtons.fire({
                    title: "Accepted",
                    text: "Transfer accepted successfully!",
                    icon: "success"
                });
                updateStatus(rowid, 1, productId, referenceNo);
                // $('#incoming-stock-table').DataTable().ajax.reload();
            } else if (result.isDenied) {
                swalWithBootstrapButtons.fire({
                    title: "Rejected",
                    text: "Transfer rejected successfully!",
                    icon: "error"
                }).then(() => {
                    updateStatus(rowid, 0, productId, referenceNo);
                    // $('#incoming-stock-table').DataTable().ajax.reload();
                });
            }
        });

    }



</script>
@endsection