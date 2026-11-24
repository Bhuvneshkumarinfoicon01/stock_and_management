@extends('layouts.app_admin')
@section('title') Batch purchases on hold @endsection
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header d-flex justify-content-between flex-wrap">
            <div class="page-title"><h4>Batch purchases on hold</h4></div>
            <a href="{{ route('admin.batch-purchases.create') }}" class="btn btn-primary">New batch purchase</a>
        </div>
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        <div class="card table-list-card">
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                    @include('admin.partials.datatable-page-length-toolbar', [
                        'tableKey' => 'batch-purchases-on-hold',
                        'toolbarId' => 'batch-purchases-on-hold-dt-toolbar',
                        'datatableConfig' => $datatableConfig,
                    ])
                </div>
                <div class="table-responsive datatable-scroll-container" id="batch-purchases-on-hold-scroll-wrap">
                    <table class="table datanew" id="batch-purchases-on-hold-table">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Date</th>
                                <th>Store</th>
                                <th>Total</th>
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
            { data: 'reference', name: 'draft_reference' },
            { data: 'batch_date', name: 'batch_date', orderable: false, searchable: false },
            { data: 'store_name', name: 'store.name', orderable: false, searchable: false },
            { data: 'total_formatted', name: 'total', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ];

        DatatablePagination.attach({
            tableKey: 'batch-purchases-on-hold',
            tableSelector: '#batch-purchases-on-hold-table',
            toolbarSelector: '#batch-purchases-on-hold-dt-toolbar',
            scrollContainerSelector: '#batch-purchases-on-hold-scroll-wrap',
            config: @json($datatableConfig),
            getAjaxUrl: function () {
                return @json(route('admin.batch-purchases-on-hold'));
            },
            buildTableOptions: function (mode, pageLength) {
                const shared = {
                    columns: onHoldColumns,
                    order: [[0, 'desc']],
                    language: {
                        emptyTable: 'No batch purchases on hold.',
                        search: 'Search:',
                        searchPlaceholder: 'Search reference or store...',
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
                    ajax: @json(route('admin.batch-purchases-on-hold')),
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
