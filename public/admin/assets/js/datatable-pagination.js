/**
 * Reusable DataTables pagination: preset sizes, custom row count, and infinite scroll for "All".
 *
 * Usage:
 *   const manager = DatatablePagination.attach({
 *     tableKey: 'sales-invoices',
 *     tableSelector: '#sales-invoices-table',
 *     toolbarSelector: '#sales-invoices-dt-toolbar',
 *     scrollContainerSelector: '#sales-invoices-scroll-wrap',
 *     config: { lengthOptions: [10,25,50], defaultLength: 25, scrollChunkSize: 50 },
 *     buildTableOptions: (mode, pageLength) => ({ ... }),
 *     getAjaxUrl: () => '/admin/sales-invoices/data/sales-all',
 *     onTableReady: (table, manager) => {},
 *   });
 */
(function (window, $) {
    'use strict';

    if (!$ || !$.fn.DataTable) {
        return;
    }

    const STORAGE_PREFIX = 'dt_page_length_';

    function readPreference(tableKey, config) {
        try {
            const raw = localStorage.getItem(STORAGE_PREFIX + tableKey);
            if (!raw) {
                return { mode: 'paginated', length: config.defaultLength || 25 };
            }

            const parsed = JSON.parse(raw);
            if (parsed.mode === 'scroll') {
                return { mode: 'scroll', length: config.scrollChunkSize || 50 };
            }

            const length = parseInt(parsed.length, 10);
            if (length > 0) {
                return { mode: 'paginated', length: length };
            }
        } catch (e) {
            // ignore invalid storage
        }

        return { mode: 'paginated', length: config.defaultLength || 25 };
    }

    function savePreference(tableKey, preference) {
        localStorage.setItem(STORAGE_PREFIX + tableKey, JSON.stringify(preference));
    }

    function applyFixedHeaderOptions(options, mode, config, $scrollContainer) {
        const fixedHeader = config.fixedHeader !== false;
        const useScrollY = fixedHeader && (
            mode !== 'scroll' || config.fixedHeaderInScrollMode === true
        );

        if ($scrollContainer && $scrollContainer.length) {
            $scrollContainer.toggleClass('datatable-fixed-header', useScrollY);
        }

        if (!useScrollY) {
            return options;
        }

        const scrollY = config.scrollY || 'calc(100vh - 300px)';
        const merged = Object.assign({}, options, {
            scrollY: scrollY,
            scrollCollapse: true,
        });

        const originalDraw = merged.drawCallback;
        merged.drawCallback = function (settings) {
            if (typeof originalDraw === 'function') {
                originalDraw.call(this, settings);
            }

            if (settings && settings.oScroll && settings.oScroll.sY) {
                new $.fn.dataTable.Api(settings).columns.adjust();
            }
        };

        return merged;
    }

    function buildServerParams(table, start, length) {
        const settings = table ? table.settings()[0] : null;
        const params = {
            draw: 1,
            start: start,
            length: length,
            search: { value: '', regex: false },
            order: [],
            columns: [],
        };

        if (!settings) {
            return params;
        }

        params.search.value = settings.oPreviousSearch.sSearch || '';
        params.order = settings.aaSorting.map(function (sort) {
            return { column: sort[0], dir: sort[1] };
        });

        settings.aoColumns.forEach(function (column, index) {
            params.columns.push({
                data: column.mData,
                name: column.sName || column.mData || '',
                searchable: column.bSearchable,
                orderable: column.bSortable,
                search: { value: '', regex: false },
            });
        });

        return params;
    }

    class DatatablePaginationManager {
        constructor(options) {
            this.options = options;
            this.tableKey = options.tableKey;
            this.$table = $(options.tableSelector);
            this.$toolbar = $(options.toolbarSelector);
            this.$scrollContainer = $(options.scrollContainerSelector);
            this.config = options.config || {};
            this.table = null;
            this.mode = 'paginated';
            this.pageLength = this.config.defaultLength || 25;
            this.scrollChunkSize = this.config.scrollChunkSize || 50;
            this.scrollState = {
                offset: 0,
                total: 0,
                loading: false,
                finished: false,
            };
            this._scrollHandler = null;
            this._searchHandler = null;
            this._scrollElement = null;
        }

        init() {
            const preference = readPreference(this.tableKey, this.config);
            this.mode = preference.mode;
            this.pageLength = preference.mode === 'scroll'
                ? this.scrollChunkSize
                : preference.length;

            this.bindToolbar();
            this.syncToolbarUi();
            this.createTable();

            if (typeof this.options.onTableReady === 'function') {
                this.options.onTableReady(this.table, this);
            }

            return this;
        }

        getAjaxUrl() {
            if (typeof this.options.getAjaxUrl === 'function') {
                return this.options.getAjaxUrl();
            }

            const baseOptions = this.options.buildTableOptions('paginated', this.pageLength);
            return typeof baseOptions.ajax === 'string' ? baseOptions.ajax : '';
        }

        bindToolbar() {
            const self = this;
            const $select = this.$toolbar.find('.dt-page-length-select');
            const $custom = this.$toolbar.find('.dt-page-length-custom');
            const $apply = this.$toolbar.find('.dt-page-length-apply');

            $select.on('change', function () {
                const value = $(this).val();
                if (value === 'custom') {
                    $custom.show().focus();
                    $apply.show();
                    return;
                }

                $custom.hide();
                $apply.hide();

                if (value === 'all') {
                    self.setMode('scroll');
                    return;
                }

                self.setPageLength(parseInt(value, 10));
            });

            $apply.on('click', function () {
                const customValue = parseInt($custom.val(), 10);
                if (!customValue || customValue < 1) {
                    window.Swal && Swal.fire('Invalid value', 'Enter a number greater than 0.', 'warning');
                    return;
                }

                self.setPageLength(customValue);
            });

            $custom.on('keydown', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    $apply.trigger('click');
                }
            });
        }

        syncToolbarUi() {
            const $select = this.$toolbar.find('.dt-page-length-select');
            const $custom = this.$toolbar.find('.dt-page-length-custom');
            const $apply = this.$toolbar.find('.dt-page-length-apply');
            const options = (this.config.lengthOptions || []).map(String);

            if (this.mode === 'scroll') {
                $select.val('all');
                $custom.hide();
                $apply.hide();
                return;
            }

            if (options.includes(String(this.pageLength))) {
                $select.val(String(this.pageLength));
                $custom.hide();
                $apply.hide();
                return;
            }

            $select.val('custom');
            $custom.val(this.pageLength).show();
            $apply.show();
        }

        destroyTable() {
            this.unbindScrollMode();

            if (this.table && $.fn.DataTable.isDataTable(this.$table)) {
                this.table.destroy();
                this.table = null;
            }

            const $wrapper = this.$table.parent();
            if ($wrapper.hasClass('dataTables_wrapper')) {
                $wrapper.before(this.$table);
                $wrapper.remove();
            }

            this.$table.find('tbody').empty();

            if (this.$scrollContainer.length && !this.$scrollContainer.find(this.$table).length) {
                this.$scrollContainer.append(this.$table);
            }

            if (this.options.footerSelector) {
                $(this.options.footerSelector).empty();
            }
        }

        ensureScrollContainerStyles() {
            if (!this.$scrollContainer.length) {
                return;
            }

            this.$scrollContainer.css({
                width: '100%',
                maxWidth: '100%',
                overflowX: 'auto',
            });
        }

        rebuild() {
            this.createTable();
            this.ensureScrollContainerStyles();
        }

        unbindScrollMode() {
            if (this._scrollHandler && this._scrollElement && this._scrollElement.length) {
                this._scrollElement.off('scroll', this._scrollHandler);
                this._scrollHandler = null;
                this._scrollElement = null;
            }

            if (this._searchHandler) {
                const $filter = this.$table.closest('.dataTables_wrapper').find('.dataTables_filter input');
                $filter.off('keyup scrollMode', this._searchHandler);
                this._searchHandler = null;
            }

            this.$scrollContainer.removeClass('datatable-infinite-scroll datatable-fixed-header datatable-sticky-thead');
            this.$scrollContainer.css('max-height', '');
            this.updateScrollStatus('');
        }

        resolveScrollElement() {
            if (this.config.fixedHeaderInScrollMode === true) {
                const $body = this.$scrollContainer.find('.dataTables_scrollBody');
                if ($body.length) {
                    return $body;
                }
            }

            return this.$scrollContainer;
        }

        createTable() {
            this.destroyTable();

            let options = this.options.buildTableOptions(this.mode, this.pageLength);
            options = applyFixedHeaderOptions(
                options,
                this.mode,
                this.config,
                this.$scrollContainer
            );
            this.table = this.$table.DataTable(options);

            if (this.mode === 'scroll') {
                this.enableScrollMode();
            } else {
                savePreference(this.tableKey, { mode: 'paginated', length: this.pageLength });
            }

            this.ensureScrollContainerStyles();
        }

        setPageLength(length) {
            this.mode = 'paginated';
            this.pageLength = length;
            savePreference(this.tableKey, { mode: 'paginated', length: length });
            this.syncToolbarUi();
            this.createTable();
        }

        setMode(mode) {
            this.mode = mode;
            if (mode === 'scroll') {
                savePreference(this.tableKey, { mode: 'scroll', length: this.scrollChunkSize });
            }
            this.syncToolbarUi();
            this.createTable();
        }

        reload(resetPaging) {
            if (this.mode === 'scroll') {
                this.resetScrollLoad();
                return;
            }

            if (this.table) {
                this.table.ajax.reload(null, resetPaging !== false);
            }
        }

        setAjaxUrl() {
            if (this.mode === 'scroll') {
                this.resetScrollLoad();
                return;
            }

            if (!this.table) {
                return;
            }

            const url = this.getAjaxUrl();
            this.table.ajax.url(url).load();
        }

        resetScrollLoad() {
            this.scrollState = {
                offset: 0,
                total: 0,
                loading: false,
                finished: false,
            };

            if (this.table) {
                this.table.clear().draw();
            }

            this.loadScrollChunk(true);
        }

        enableScrollMode() {
            const self = this;
            const useScrollYHeader = this.config.fixedHeaderInScrollMode === true;

            if (!useScrollYHeader) {
                this.$scrollContainer.addClass('datatable-infinite-scroll');
                this.$scrollContainer.css('max-height', this.config.scrollY || 'calc(100vh - 300px)');
            }

            this._searchValue = '';

            if (this.table) {
                this.table.settings()[0].oFeatures.bFilter = false;
            }

            this.resetScrollLoad();

            this._scrollHandler = function () {
                const element = self._scrollElement ? self._scrollElement.get(0) : null;
                if (!element || self.scrollState.loading || self.scrollState.finished) {
                    return;
                }

                const nearBottom = element.scrollTop + element.clientHeight >= element.scrollHeight - 120;
                if (nearBottom) {
                    self.loadScrollChunk(false);
                }
            };

            this._scrollElement = this.resolveScrollElement();
            this._scrollElement.on('scroll', this._scrollHandler);

            const $filter = $(this.table.table().container()).find('.dataTables_filter input');
            if ($filter.length) {
                this._searchHandler = function () {
                    clearTimeout(self._searchDebounce);
                    self._searchDebounce = setTimeout(function () {
                        self._searchValue = $filter.val() || '';
                        self.resetScrollLoad();
                    }, 400);
                };
                $filter.on('keyup scrollMode', this._searchHandler);
            }
        }

        loadScrollChunk(reset) {
            const self = this;

            if (this.scrollState.loading) {
                return;
            }

            if (!reset && this.scrollState.finished) {
                return;
            }

            if (reset) {
                this.scrollState.offset = 0;
                this.scrollState.total = 0;
                this.scrollState.finished = false;
            }

            const start = this.scrollState.offset;
            const length = this.scrollChunkSize;
            const ajaxUrl = this.getAjaxUrl();
            const params = buildServerParams(this.table, start, length);
            const $filter = this.$table.closest('.dataTables_wrapper').find('.dataTables_filter input');
            params.search.value = this._searchValue || ($filter.length ? $filter.val() : '') || '';

            if (typeof this.options.getAjaxData === 'function') {
                Object.assign(params, this.options.getAjaxData() || {});
            }

            this.scrollState.loading = true;
            this.updateScrollStatus('Loading...');

            $.ajax({
                url: ajaxUrl,
                type: 'GET',
                dataType: 'json',
                data: params,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            })
                .done(function (json) {
                    const rows = json.data || [];
                    self.scrollState.total = json.recordsFiltered || 0;

                    rows.forEach(function (row) {
                        self.table.row.add(row);
                    });

                    self.table.draw(false);
                    self.scrollState.offset += rows.length;

                    if (self.scrollState.offset >= self.scrollState.total || rows.length === 0) {
                        self.scrollState.finished = true;
                    }

                    const loaded = self.scrollState.offset;
                    const total = self.scrollState.total;
                    self.updateScrollStatus(
                        total > 0
                            ? 'Showing ' + loaded + ' of ' + total + (self.scrollState.finished ? '' : ' — scroll for more')
                            : 'No records found'
                    );

                    if (typeof self.options.onDraw === 'function') {
                        self.options.onDraw(self.table);
                    }
                })
                .fail(function () {
                    self.updateScrollStatus('Failed to load data');
                })
                .always(function () {
                    self.scrollState.loading = false;
                });
        }

        updateScrollStatus(message) {
            this.$toolbar.find('.dt-scroll-status').text(message || '');
        }
    }

    window.DatatablePagination = {
        attach: function (options) {
            const manager = new DatatablePaginationManager(options);
            manager.init();
            return manager;
        },
        applyFixedHeader: function (options, config, scrollContainerSelector) {
            const $scrollContainer = scrollContainerSelector ? $(scrollContainerSelector) : null;
            const mergedConfig = Object.assign(
                { fixedHeader: true, scrollY: 'calc(100vh - 300px)' },
                config || {}
            );

            return applyFixedHeaderOptions(options, 'paginated', mergedConfig, $scrollContainer);
        },
    };
})(window, window.jQuery);
