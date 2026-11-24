@extends('layouts.app_admin') @section('title') Dashbaord @endsection
@section('styles') @endsection @section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4>Outgoining Stock</h4>
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
                            <div class="col-lg-3">
                                <div class="input-blocks">
                                    <i data-feather="box" class="info-img"></i>
                                    <select id="status-filter" class="select">

                                        <option value="choose">choose status</option>
                                        <option value="">Pending</option>
                                        <option value="1">Accepted</option>
                                        <option value="0">Rejected</option>
                                    </select>
                                </div>
                            </div>
                            <!-- <div class="col-lg-6 col-sm-6 col-12">
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
                <div class="table-responsive">
                    <table class="table datanew" id="outgoing-stock-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Transfer Product Name</th>
                                <th>Destination Storee</th>
                                <th>Qty</th>
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
        let table = $('#outgoing-stock-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.outgoing-stock') }}",
                data: function (d) {
                    d.status = $('#status-filter').val();
                    d.order_type = $('#sort-order').val();
                }
            },
            columns: [
                { data: 'date', name: 'date' },
                { data: 'transfer_product_name', name: 'transfer_product_name' },
                { data: 'destination_store', name: 'destination_store' },
                { data: 'qty', name: 'qty' },
                { data: 'status', name: 'status', orderable: false, searchable: false }
            ],
            pageLength: 10,
            initComplete: function () {
                let searchWrapper = $('#outgoing-stock-table_filter');
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

@endsection