@extends('layouts.app_admin')

@section('title')
Inventory Valuation
@endsection

@section('styles')
<style>
    .table-scroll-wrapper {
        overflow-x: auto;
        white-space: nowrap;
    }
    .inventory-valuation-table th,
    .inventory-valuation-table td {
        white-space: nowrap;
        vertical-align: middle;
    }
    .inventory-valuation-table thead tr:first-child th {
        background: #f8f9fa;
        text-align: center;
    }
    .inventory-valuation-table thead tr:nth-child(2) th {
        background: #f3f4f6;
    }
    .inventory-valuation-table th.sticky-col,
    .inventory-valuation-table td.sticky-col {
        position: sticky;
        left: 0;
        z-index: 2;
        background: #fff;
    }
    .inventory-valuation-table thead th.sticky-col {
        z-index: 3;
        background: #f8f9fa;
    }
    .store-group-divider {
        border-left: 2px solid #dee2e6 !important;
    }
    .datatable-infinite-scroll {
        overflow-y: auto;
    }
</style>
@endsection

@section('content')
@php
    $user = auth()->user();
    $selected_store_id = $user->selected_store_id ?? session('selected_store_id');
@endphp

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4>Inventory Valuation</h4>
                    <h6>Value at cost by product and store — <span class="text-primary">{{ $valuationMethodLabel }}</span></h6>
                </div>
            </div>
            <ul class="table-top-head">
                <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Refresh" id="refresh-valuation">
                        <i data-feather="rotate-ccw" class="feather-rotate-ccw"></i>
                    </a>
                </li>
            </ul>
        </div>

        <div class="card table-list-card mb-3">
            <div class="card-body">
                <div class="row align-items-end g-3">
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label mb-0" for="valuation_as_at">Valuation as at</label>
                        <div class="input-blocks mb-0">
                            <i data-feather="calendar" class="info-img"></i>
                            <div class="input-groupicon">
                                <input type="text" id="valuation_as_at" class="datetimepicker" value="{{ format_date(now(), '') }}" placeholder="As at date" />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label mb-0" for="filter_store">Store</label>
                        <select class="select" id="filter_store">
                            <option value="choose">Choose Store</option>
                            @if($user && in_array($user->role_id, [1, 2]))
                            <option value="all">All Stores</option>
                            @endif
                            @foreach($stores as $store)
                            @if($store->id == $selected_store_id)
                            <option value="{{ $store->id }}">{{ $store->name }}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label mb-0" for="filter_category">Category</label>
                        <select class="select" id="filter_category">
                            <option value="choose">Choose Category</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <a href="javascript:void(0);" class="btn btn-secondary d-flex align-items-center" id="display-valuation">
                            <i data-feather="database" class="feather-14 info-img me-2"></i>Display Data
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card table-list-card">
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                    @include('admin.partials.datatable-page-length-toolbar', [
                        'tableKey' => 'inventory-valuation',
                        'toolbarId' => 'inventory-valuation-dt-toolbar',
                        'datatableConfig' => $datatableConfig,
                    ])
                </div>
                <div class="table-top mb-3">
                    <div class="search-set">
                        <div class="search-input d-flex align-items-center">
                            <div id="custom-search" class="me-2"></div>
                            <a href="#" class="btn btn-searchset">
                                <i data-feather="search" class="feather-search"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div id="valuation-empty-hint" class="alert alert-info">Select a date and click Display Data to load the valuation report.</div>

                <div class="table-scroll-wrapper datatable-scroll-container d-none" id="inventory-valuation-scroll-wrap">
                    <table class="table inventory-valuation-table table-bordered datanew" id="inventory-valuation-table">
                        <thead id="inventory-valuation-thead"></thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    const allStores = @json($stores->map(fn ($s) => ['id' => $s->id, 'name' => $s->name])->values());
    const sessionStoreId = @json(session('selected_store_id'));
    let visibleStores = [];
    let paginationManager = null;
    let reportReady = false;

    function formatCurrency(value) {
        const num = Number(value) || 0;
        return num.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function escapeHtml(text) {
        return $('<div>').text(text ?? '').html();
    }

    function resolveVisibleStores() {
        const filter = $('#filter_store').val();

        if (filter === 'all') {
            return allStores;
        }
        if (filter && filter !== 'choose') {
            return allStores.filter(function (s) {
                return String(s.id) === String(filter);
            });
        }
        if (sessionStoreId) {
            return allStores.filter(function (s) {
                return String(s.id) === String(sessionStoreId);
            });
        }

        return allStores;
    }

    function storeColumnKey(stores) {
        return stores.map(function (s) { return s.id; }).join(',');
    }

    function buildTableHeader(stores) {
        let row1 = '<tr>' +
            '<th rowspan="2" class="sticky-col">Product</th>' +
            '<th rowspan="2">SKU</th>' +
            '<th rowspan="2">Category</th>' +
            '<th rowspan="2">Unit</th>' +
            '<th rowspan="2" class="text-end">Combined Qty</th>' +
            '<th rowspan="2" class="text-end">Combined Value at Cost</th>';

        stores.forEach(function (store, index) {
            const divider = index > 0 ? ' store-group-divider' : '';
            row1 += '<th colspan="2" class="text-center' + divider + '">' + escapeHtml(store.name) + '</th>';
        });
        row1 += '</tr>';

        let row2 = '<tr>';
        stores.forEach(function (store, index) {
            const divider = index > 0 ? ' store-group-divider' : '';
            row2 += '<th class="text-end' + divider + '">Qty</th>';
            row2 += '<th class="text-end">Value at Cost</th>';
        });
        row2 += '</tr>';

        $('#inventory-valuation-thead').html(row1 + row2);
    }

    function buildColumns(stores) {
        const cols = [
            { data: 'product_name', name: 'product_name', className: 'sticky-col' },
            { data: 'sku', name: 'sku' },
            { data: 'category', name: 'category' },
            { data: 'unit', name: 'unit' },
            { data: 'combined_qty', name: 'combined_qty', className: 'text-end fw-semibold' },
            {
                data: 'combined_value',
                name: 'combined_value',
                className: 'text-end fw-semibold',
                render: function (data) {
                    return formatCurrency(data);
                }
            }
        ];

        stores.forEach(function (store, index) {
            const storeId = String(store.id);
            const divider = index > 0 ? ' store-group-divider' : '';

            cols.push({
                data: null,
                orderable: false,
                searchable: false,
                className: 'text-end' + divider,
                render: function (data, type, row) {
                    const cell = row.stores && row.stores[storeId];
                    return cell ? cell.qty : '';
                }
            });
            cols.push({
                data: null,
                orderable: false,
                searchable: false,
                className: 'text-end',
                render: function (data, type, row) {
                    const cell = row.stores && row.stores[storeId];
                    return cell ? formatCurrency(cell.value) : '';
                }
            });
        });

        return cols;
    }

    function getFilterAjaxData() {
        return {
            as_at: $('#valuation_as_at').val(),
            category: $('#filter_category').val(),
            store: $('#filter_store').val(),
        };
    }

    function attachPagination() {
        visibleStores = resolveVisibleStores();
        buildTableHeader(visibleStores);

        paginationManager = DatatablePagination.attach({
            tableKey: 'inventory-valuation',
            tableSelector: '#inventory-valuation-table',
            toolbarSelector: '#inventory-valuation-dt-toolbar',
            scrollContainerSelector: '#inventory-valuation-scroll-wrap',
            config: @json($datatableConfig),
            getAjaxUrl: function () {
                return @json(route('admin.inventory-valuation'));
            },
            getAjaxData: getFilterAjaxData,
            buildTableOptions: function (mode, pageLength) {
                buildTableHeader(visibleStores);
                const columns = buildColumns(visibleStores);
                const shared = {
                    columns: columns,
                    order: [[0, 'asc']],
                    language: {
                        emptyTable: 'No inventory valuation records found.',
                        search: 'Search:',
                        searchPlaceholder: 'Search product, SKU, category...',
                    },
                    drawCallback: function () {
                        feather.replace();
                    },
                    initComplete: function () {
                        const searchWrapper = $('#inventory-valuation-table_filter');
                        const defaultSearchBox = searchWrapper.find('input');
                        $('#custom-search').empty().append(defaultSearchBox);
                        defaultSearchBox.attr('placeholder', 'Search product, SKU, category...');
                        searchWrapper.hide();
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
                    ajax: {
                        url: @json(route('admin.inventory-valuation')),
                        data: function (d) {
                            Object.assign(d, getFilterAjaxData());
                        },
                    },
                    pageLength: pageLength,
                    lengthChange: false,
                    searching: true,
                    dom: 'frtip',
                });
            },
        });
    }

    function loadValuationReport() {
        const asAt = $('#valuation_as_at').val();
        if (!asAt) {
            Swal.fire({ icon: 'warning', title: 'Missing date', text: 'Please select a valuation date.' });
            return;
        }

        $('#valuation-empty-hint').addClass('d-none');
        $('#inventory-valuation-scroll-wrap').removeClass('d-none');

        const stores = resolveVisibleStores();
        const nextKey = storeColumnKey(stores);

        if (!reportReady || !paginationManager) {
            reportReady = true;
            attachPagination();
            return;
        }

        if (nextKey !== storeColumnKey(visibleStores)) {
            visibleStores = stores;
            paginationManager.setPageLength(paginationManager.pageLength);
            return;
        }

        paginationManager.reload(false);
    }

    $(document).ready(function () {
        $('#display-valuation').on('click', loadValuationReport);

        $('#filter_store, #filter_category').on('change', function () {
            if (reportReady) {
                loadValuationReport();
            }
        });

        $('#refresh-valuation').on('click', function (e) {
            e.preventDefault();
            if (reportReady) {
                loadValuationReport();
            }
        });
    });
})();
</script>
@endsection
