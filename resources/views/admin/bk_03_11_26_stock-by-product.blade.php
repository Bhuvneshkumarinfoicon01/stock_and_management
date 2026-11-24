@extends('layouts.app_admin')

@section('title')
Stock by Product
@endsection

@section('styles')
<style>
    .table-scroll-wrapper {
        display: block;
        width: 100%;
        max-width: 100%;
        overflow-x: auto;
        overflow-y: visible;
        -webkit-overflow-scrolling: touch;
    }
    #stock-by-product-scroll-wrap .dataTables_wrapper {
        width: max-content;
        min-width: 100%;
    }
    #stock-by-product-dt-footer {
        width: 100%;
        max-width: 100%;
    }
    .card.table-list-card .card-body {
        overflow-x: hidden;
    }
    .stock-by-product-table th,
    .stock-by-product-table td {
        white-space: nowrap;
        vertical-align: middle;
    }
    .stock-by-product-table thead tr:first-child th {
        background: #f8f9fa;
        text-align: center;
    }
    .stock-by-product-table thead tr:nth-child(2) th {
        background: #f3f4f6;
    }
    .stock-by-product-table th.sticky-col,
    .stock-by-product-table td.sticky-col {
        position: sticky;
        left: 0;
        z-index: 2;
        background: #fff;
    }
    .stock-by-product-table thead th.sticky-col {
        z-index: 3;
        background: #f8f9fa;
    }
    .store-group-divider {
        border-left: 2px solid #dee2e6 !important;
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
                    <h4>Stock by Product</h4>
                    <h6>One row per product with quantity and price per store</h6>
                </div>
            </div>
            <ul class="table-top-head">
                <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Refresh" id="refresh-page">
                        <i data-feather="rotate-ccw" class="feather-rotate-ccw"></i>
                    </a>
                </li>
                <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Collapse" id="collapse-header">
                        <i data-feather="chevron-up" class="feather-chevron-up"></i>
                    </a>
                </li>
            </ul>
        </div>

        <div class="card table-list-card">
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                    @include('admin.partials.datatable-page-length-toolbar', [
                        'tableKey' => 'stock-by-product',
                        'toolbarId' => 'stock-by-product-dt-toolbar',
                        'datatableConfig' => $datatableConfig,
                    ])
                </div>
                <div class="table-top">
                    <div class="search-set">
                        <div class="search-input d-flex align-items-center">
                            <div id="custom-search" class="me-2"></div>
                            <a href="#" class="btn btn-searchset">
                                <i data-feather="search" class="feather-search"></i>
                            </a>
                        </div>
                    </div>
                    <div class="form-sort">
                        <i data-feather="sliders" class="info-img"></i>
                        <select class="select" id="sort-order">
                            <option value="">Sort by</option>
                            <option value="desc">Newest First</option>
                            <option value="asc">Oldest First</option>
                        </select>
                    </div>
                </div>

                <div class="card mb-0" id="filter_inputs">
                    <div class="card-body pb-0">
                        <div class="row">
                            <div class="col-lg-12 col-sm-12">
                                <div class="row">
                                    <div class="col-lg-2 col-sm-6 col-12">
                                        <div class="input-blocks">
                                            <i class="fas fa-money-bill info-img"></i>
                                            <select class="select" id="filter_store">
                                                <option value="choose">Choose Store</option>
                                                @if($user && in_array($user->role_id, [1, 2]))
                                                <option value="all">All Stores</option>
                                                @endif
                                                @foreach($stores as $store)
                                                <option value="{{ $store->id }}"
                                                    {{ (string) $store->id === (string) $selected_store_id ? 'selected' : '' }}>
                                                    {{ $store->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-sm-6 col-12">
                                        <div class="input-blocks">
                                            <i data-feather="stop-circle" class="info-img"></i>
                                            <select class="select" id="filter_category">
                                                <option value="choose">Choose Category</option>
                                                @foreach($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-scroll-wrapper datatable-scroll-container mt-3" id="stock-by-product-scroll-wrap">
                    <table class="table stock-by-product-table table-bordered datanew" id="stock-by-product-table">
                        <thead id="stock-by-product-thead"></thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div id="stock-by-product-dt-footer" class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-2"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
    .datatable-infinite-scroll {
        overflow-x: auto;
        overflow-y: auto;
        max-width: 100%;
    }
</style>
<script>
(function () {
    const allStores = @json($stores->map(fn ($s) => ['id' => $s->id, 'name' => $s->name])->values());
    const sessionStoreId = @json(session('selected_store_id'));
    let visibleStores = [];
    let paginationManager = null;

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
            '<th rowspan="2" class="text-end">Combined Qty</th>';

        stores.forEach(function (store, index) {
            const divider = index > 0 ? ' store-group-divider' : '';
            row1 += '<th colspan="2" class="text-center' + divider + '">' + escapeHtml(store.name) + '</th>';
        });
        row1 += '</tr>';

        let row2 = '<tr>';
        stores.forEach(function (store, index) {
            const divider = index > 0 ? ' store-group-divider' : '';
            row2 += '<th class="text-end' + divider + '">Qty</th>';
            row2 += '<th class="text-end">Price</th>';
        });
        row2 += '</tr>';

        $('#stock-by-product-thead').html(row1 + row2);
    }

    function buildColumns(stores) {
        const cols = [
            { data: 'product_name', name: 'product_name', className: 'sticky-col' },
            { data: 'sku', name: 'sku' },
            { data: 'category', name: 'category' },
            { data: 'unit', name: 'unit' },
            { data: 'combined_qty', name: 'combined_qty', className: 'text-end fw-semibold' }
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
                    return cell ? formatCurrency(cell.price) : '';
                }
            });
        });

        return cols;
    }

    function getFilterAjaxData() {
        return {
            category: $('#filter_category').val(),
            store: $('#filter_store').val(),
            order_type: $('#sort-order').val(),
        };
    }

    function pinTableLayout() {
        const $wrap = $('#stock-by-product-scroll-wrap');
        const $table = $('#stock-by-product-table');
        const $wrapper = $table.closest('.dataTables_wrapper');
        const $footer = $('#stock-by-product-dt-footer');

        if (!$wrapper.length) {
            return;
        }

        // Remove stale wrappers left from previous rebuilds.
        $wrap.find('.dataTables_wrapper').not($wrapper).remove();

        if (!$wrap[0].contains($wrapper[0])) {
            $wrap.append($wrapper);
        }

        // DataTables renders fresh info/pagination inside the wrapper on each draw.
        const $info = $wrapper.find('.dataTables_info').first();
        const $paginate = $wrapper.find('.dataTables_paginate').first();

        if ($info.length || $paginate.length) {
            // New controls in wrapper — replace any previously relocated footer copies.
            $footer.empty();
        }

        if ($info.length) {
            $footer.append($info);
        }
        if ($paginate.length) {
            $footer.append($paginate);
        }

        $wrap.css({
            width: '100%',
            maxWidth: '100%',
            overflowX: 'auto',
            overflowY: 'visible',
        });
    }

    function clearTableFooter() {
        $('#stock-by-product-dt-footer').empty();
    }

    function attachPagination() {
        visibleStores = resolveVisibleStores();
        buildTableHeader(visibleStores);

        paginationManager = DatatablePagination.attach({
            tableKey: 'stock-by-product',
            tableSelector: '#stock-by-product-table',
            toolbarSelector: '#stock-by-product-dt-toolbar',
            scrollContainerSelector: '#stock-by-product-scroll-wrap',
            footerSelector: '#stock-by-product-dt-footer',
            config: @json($datatableConfig),
            getAjaxUrl: function () {
                return @json(route('admin.stock.by-product'));
            },
            getAjaxData: getFilterAjaxData,
            buildTableOptions: function (mode, pageLength) {
                buildTableHeader(visibleStores);
                const columns = buildColumns(visibleStores);
                const shared = {
                    columns: columns,
                    order: [[0, 'asc']],
                    autoWidth: false,
                    language: {
                        emptyTable: 'No stock records found.',
                        search: 'Search:',
                        searchPlaceholder: 'Search product, SKU, category...',
                    },
                    drawCallback: function () {
                        feather.replace();
                        pinTableLayout();
                    },
                    initComplete: function () {
                        const searchWrapper = $('#stock-by-product-table_filter');
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
                        url: @json(route('admin.stock.by-product')),
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

    function reloadWithStores() {
        if (!paginationManager) {
            return;
        }

        const stores = resolveVisibleStores();
        const nextKey = storeColumnKey(stores);

        if (nextKey !== storeColumnKey(visibleStores)) {
            visibleStores = stores;
            buildTableHeader(visibleStores);
            clearTableFooter();
            paginationManager.rebuild();
            return;
        }

        paginationManager.reload(false);
    }

    $(document).ready(function () {
        attachPagination();

        $('#filter_category, #filter_store, #sort-order').on('change', function () {
            reloadWithStores();
        });

        $('#refresh-page').on('click', function (e) {
            e.preventDefault();
            reloadWithStores();
        });
    });
})();
</script>
@endsection
