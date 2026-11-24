@extends('layouts.app_admin')
@section('title') Batch purchases @endsection
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header d-flex justify-content-between flex-wrap">
            <div class="page-title"><h4>Batch purchases</h4></div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.batch-purchases-on-hold') }}" class="btn btn-outline-secondary">On hold</a>
                <a href="{{ route('admin.batch-purchases.create') }}" class="btn btn-primary">New batch purchase</a>
            </div>
        </div>
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        <div class="card table-list-card">
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                    @include('admin.partials.datatable-page-length-toolbar', [
                        'tableKey' => 'batch-purchases',
                        'toolbarId' => 'batch-purchases-dt-toolbar',
                        'datatableConfig' => $datatableConfig,
                    ])
                </div>
                <div class="table-responsive datatable-scroll-container" id="batch-purchases-scroll-wrap">
                    <table class="table datanew" id="batch-purchases-table">
                        <thead>
                            <tr>
                                <th>Batch #</th>
                                <th>Date</th>
                                <th>Store</th>
                                <th>Total</th>
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
@endsection

@section('scripts')
<style>
    .datatable-infinite-scroll {
        overflow-y: auto;
    }
</style>
<script>
    $(function () {
        const batchColumns = [
            { data: 'batch_number', name: 'batch_number' },
            { data: 'batch_date', name: 'batch_date', orderable: false, searchable: false },
            { data: 'store_name', name: 'store.name', orderable: false, searchable: false },
            { data: 'total_formatted', name: 'total', orderable: false, searchable: false },
            { data: 'status', name: 'status' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false },
        ];

        DatatablePagination.attach({
            tableKey: 'batch-purchases',
            tableSelector: '#batch-purchases-table',
            toolbarSelector: '#batch-purchases-dt-toolbar',
            scrollContainerSelector: '#batch-purchases-scroll-wrap',
            config: @json($datatableConfig),
            getAjaxUrl: function () {
                return @json(route('admin.batch-purchases.index'));
            },
            buildTableOptions: function (mode, pageLength) {
                const shared = {
                    columns: batchColumns,
                    order: [[0, 'desc']],
                    language: {
                        emptyTable: 'No confirmed batch purchases.',
                        search: 'Search:',
                        searchPlaceholder: 'Search batch # or store...',
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
                    ajax: @json(route('admin.batch-purchases.index')),
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
