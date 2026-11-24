@extends('layouts.app_admin')
@section('title') Sales invoices on hold @endsection
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header d-flex justify-content-between flex-wrap">
            <div class="page-title">
                <h4>Sales invoices on hold</h4>
                <h6>Click <strong>Continue</strong> to open a draft, then use Place Order or Place Invoice on Hold on the form.</h6>
            </div>
            <a href="{{ route('admin.generate-invoice') }}" class="btn btn-primary">New sale invoice</a>
        </div>
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        <div class="card table-list-card">
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                    @include('admin.partials.datatable-page-length-toolbar', [
                        'tableKey' => 'sales-invoices-on-hold',
                        'toolbarId' => 'sales-invoices-on-hold-dt-toolbar',
                        'datatableConfig' => $datatableConfig,
                    ])
                </div>
                <div class="table-responsive datatable-scroll-container" id="sales-on-hold-scroll-wrap">
                    <table class="table datanew" id="sales-on-hold-table">
                        <thead>
                            <tr>
                                <th>Draft ref</th>
                                <th>Customer</th>
                                <th>Store</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Credit limit</th>
                                <th>Action</th>
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
<style>
    .datatable-infinite-scroll {
        overflow-y: auto;
    }
</style>
<script>
$(function () {
    const onHoldColumns = [
            { data: 'invoice_no', name: 'invoice_no', orderable: true, searchable: true },
            { data: 'customer_name', name: 'customer_name' },
            { data: 'store_name', name: 'sort_stores.name' },
            { data: 'created_at', name: 'created_at' },
            { data: 'total_price', name: 'total_price', orderable: false, searchable: false },
            { data: 'limit_flag', name: 'credit_limit_blocked', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ];

    DatatablePagination.attach({
        tableKey: 'sales-invoices-on-hold',
        tableSelector: '#sales-on-hold-table',
        toolbarSelector: '#sales-invoices-on-hold-dt-toolbar',
        scrollContainerSelector: '#sales-on-hold-scroll-wrap',
        config: @json($datatableConfig),
        getAjaxUrl: function () {
            return @json(route('admin.sales-invoices-on-hold'));
        },
        buildTableOptions: function (mode, pageLength) {
            const shared = {
                columns: onHoldColumns,
                language: {
                    emptyTable: 'No sale invoices on hold.',
                    search: 'Search:',
                    searchPlaceholder: 'Search drafts...',
                },
                drawCallback: function () {
                    feather.replace();
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
                ajax: @json(route('admin.sales-invoices-on-hold')),
                pageLength: pageLength,
                lengthChange: false,
                searching: true,
                dom: 'frtip',
            });
        },
    });
});
</script>
@endsection
