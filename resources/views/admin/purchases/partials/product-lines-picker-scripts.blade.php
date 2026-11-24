@php
    $pickerPrefix = $pickerPrefix ?? 'pdoc';
    $storeSelectId = $storeSelectId ?? 'purchase_store_id';
    $formId = $formId ?? 'purchase-document-form';
    $submitBtnId = $submitBtnId ?? 'purchase-document-submit';
    $emptyMessage = $emptyMessage ?? 'Add at least one product before saving.';
    $purchaseOrderSelectId = $purchaseOrderSelectId ?? null;
    $supplierSelectId = $supplierSelectId ?? null;
    $purchaseOrderItemsUrl = $purchaseOrderItemsUrl ?? null;
    $externallyManagedSources = !empty($externallyManagedSources);
    $perLineTax = !empty($perLineTax);
@endphp
<script>
(function ($) {
    const prefix = @json($pickerPrefix);
    const perLineTax = @json($perLineTax);
    const storeSelectId = @json($storeSelectId);
    const productsUrl = @json(route('admin.get-products-invoice'));

    const $search = $('#' + prefix + '_product_search');
    const $list = $('#' + prefix + '_product_list');
    const $tbody = $('#' + prefix + '_product_tbody');
    const $store = $('#' + storeSelectId);

    function recalcTotals() {
        if (perLineTax && window.InvoicePerLineTax) {
            window.InvoicePerLineTax.calculateTotals({
                tbodySelector: '#' + prefix + '_product_tbody',
            });
        }
    }

    function fetchProducts(storeId, query) {
        $.ajax({
            url: productsUrl,
            method: 'GET',
            data: { store_id: storeId, query: query || '' },
            success: function (response) {
                $list.empty().show();
                if (!response || response.length === 0) {
                    $list.append('<li class="list-group-item">No products found</li>');
                    return;
                }
                $.each(response, function (_, product) {
                    const $li = $('<li class="list-group-item ' + prefix + '-product-item" style="cursor:pointer;"></li>');
                    $li.attr('data-id', product.product_id);
                    $li.attr('data-name', product.product_name);
                    $li.attr('data-sku', product.sku || '');
                    $li.attr('data-category', product.category_name || '');
                    $li.attr('data-category_id', product.category_id || '');
                    $li.attr('data-qty', product.total_quantity);
                    $li.attr('data-price', product.price);
                    if (perLineTax) {
                        $li.attr('data-is_taxable', product.is_taxable ? 1 : 0);
                        $li.attr('data-tax_rate', product.tax_rate != null ? product.tax_rate : 0);
                    }
                    $li.text(product.product_name + ' - SKU: ' + (product.sku || '') + ' - Qty: ' + product.total_quantity);
                    $list.append($li);
                });
            },
            error: function () {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load products.' });
                }
            }
        });
    }

    function addProductRow(productId, productName, sku, category, categoryId, price, qty, skipDuplicateCheck, isTaxable, taxRate) {
        if (!skipDuplicateCheck) {
            let duplicate = false;
            $tbody.find('tr').each(function () {
                if ($(this).find('input[name="product_id[]"]').val() == productId) {
                    duplicate = true;
                    return false;
                }
            });
            if (duplicate) {
                Swal.fire({ icon: 'warning', title: 'Duplicate', text: 'This product is already in the list.' });
                return;
            }
        }

        const priceVal = parseFloat(price) || 0;
        const qtyVal = Math.max(1, parseInt(qty, 10) || 1);
        const $row = $('<tr>');
        const $td1 = $('<td>');
        $td1.append($('<input>', { type: 'hidden', name: 'product_id[]', value: String(productId) }));
        $td1.append($('<input>', { type: 'text', name: 'product_name[]', class: 'form-control', value: productName, readonly: true }));
        $row.append($td1);
        $row.append($('<td>').append($('<input>', { type: 'text', name: 'sku[]', class: 'form-control', value: sku || '', readonly: true })));
        const $td3 = $('<td>');
        $td3.append($('<input>', { type: 'hidden', name: 'category_id[]', value: categoryId != null ? String(categoryId) : '' }));
        $td3.append($('<input>', { type: 'text', name: 'category[]', class: 'form-control', value: category || '', readonly: true }));
        $row.append($td3);
        $row.append($('<td>').append($('<input>', { type: 'number', name: 'qty[]', class: 'form-control mt-1', value: qtyVal, min: 1 })));
        $row.append($('<td>').append($('<input>', { type: 'text', name: 'price[]', class: 'form-control mt-1', value: priceVal })));

        if (perLineTax) {
            const taxable = isTaxable !== false && isTaxable !== 0 && isTaxable !== '0';
            const rate = taxRate != null && taxRate !== '' ? parseFloat(taxRate) : 0;
            const $taxTd = $('<td class="line-tax-col">');
            $taxTd.append($('<input>', { type: 'hidden', name: 'line_is_taxable[]', class: 'line-taxable-val', value: taxable ? '1' : '0' }));
            $taxTd.append($('<input>', { type: 'checkbox', class: 'form-check-input line-taxable-cb', checked: taxable }));
            $row.append($taxTd);
            $row.append($('<td class="line-tax-col">').append(
                $('<input>', { type: 'number', step: '0.01', min: 0, max: 100, name: 'line_tax_rate[]', class: 'form-control form-control-sm line-tax-rate', value: rate })
            ));
            $row.append($('<td class="line-tax-col line-tax-amount text-end">').text('$0.00'));
        }

        $row.append($('<td>').append(
            $('<a>', { href: 'javascript:void(0);', class: 'btn btn-danger btn-sm ' + prefix + '-delete-row mt-1' }).text('Delete')
        ));
        $tbody.append($row);

        if (perLineTax && window.InvoicePerLineTax) {
            window.InvoicePerLineTax.setRowDefaultTax($row, isTaxable, taxRate);
            window.InvoicePerLineTax.syncLineTaxUi($row);
            recalcTotals();
        }

        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }

    window[prefix + 'AddProductRow'] = addProductRow;

    function loadProductRows(rows) {
        $tbody.empty();
        if (!rows || !rows.length) {
            return;
        }
        $.each(rows, function (_, item) {
            addProductRow(
                item.product_id,
                item.product_name,
                item.sku || '',
                item.category || '',
                item.category_id || '',
                item.unit_price,
                item.qty,
                true,
                item.is_taxable,
                item.tax_rate
            );
        });
    }

    $search.on('focus keyup', function () {
        const storeId = $store.val();
        if (!storeId) {
            Swal.fire({ icon: 'warning', title: 'Select store', text: 'Please choose a store before searching products.' });
            return;
        }
        fetchProducts(storeId, $(this).val());
    });

    $(document).on('click', '.' + prefix + '-product-item', function () {
        const $el = $(this);
        addProductRow(
            $el.data('id'),
            $el.data('name'),
            $el.data('sku'),
            $el.data('category'),
            $el.data('category_id'),
            $el.data('price'),
            null,
            false,
            perLineTax ? ($el.data('is_taxable') == 1) : undefined,
            perLineTax ? $el.data('tax_rate') : undefined
        );
        $list.empty().hide();
        $search.val('');
    });

    let suppressStoreChange = false;
    const poSelectId = @json($purchaseOrderSelectId);
    const poItemsUrlTemplate = @json($purchaseOrderItemsUrl);
    const externallyManagedSources = @json($externallyManagedSources);
    const $poSelect = poSelectId ? $('#' + poSelectId) : $();
    const $supplier = @json($supplierSelectId) ? $('#' + @json($supplierSelectId)) : $();

    @if($purchaseOrderSelectId && $purchaseOrderItemsUrl)

    function loadPurchaseOrderItems(poId) {
        if (!poId) {
            $tbody.empty();
            recalcTotals();
            return;
        }

        const storeId = $store.val();
        const url = poItemsUrlTemplate.replace('__PO__', poId);

        $.ajax({
            url: url,
            method: 'GET',
            data: { store_id: storeId || '' },
            success: function (response) {
                const po = response.purchase_order || {};
                suppressStoreChange = true;
                if ($supplier.length && po.supplier_id) {
                    $supplier.val(String(po.supplier_id));
                }
                if (po.store_id) {
                    $store.val(String(po.store_id));
                }
                suppressStoreChange = false;

                loadProductRows(response.items || []);

                if (!(response.items || []).length) {
                    Swal.fire({ icon: 'info', title: 'No items', text: 'This purchase order has no line items.' });
                }
                recalcTotals();
            },
            error: function () {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Could not load purchase order items.' });
            }
        });
    }

    $poSelect.on('change', function () {
        loadPurchaseOrderItems($(this).val());
    });

    if ($poSelect.val()) {
        loadPurchaseOrderItems($poSelect.val());
    }
    @endif

    $store.on('change', function () {
        const storeId = $store.val();
        const query = $search.val();
        const listOpen = $list.is(':visible');
        if (storeId && (listOpen || query)) {
            fetchProducts(storeId, query);
        } else {
            $list.empty().hide();
        }
        if (suppressStoreChange || externallyManagedSources) {
            return;
        }
        if ($poSelect.length && $poSelect.val()) {
            loadPurchaseOrderItems($poSelect.val());
            return;
        }
        $tbody.empty();
        recalcTotals();
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('#' + prefix + '_product_search, #' + prefix + '_product_list').length) {
            $list.hide();
        }
    });

    $(document).on('click', '.' + prefix + '-delete-row', function () {
        $(this).closest('tr').remove();
        recalcTotals();
    });

    $('#' + @json($formId)).on('submit', function (e) {
        if ($tbody.find('tr').length === 0) {
            e.preventDefault();
            Swal.fire({ icon: 'warning', title: 'No products', text: @json($emptyMessage) });
            return;
        }
        const $btn = $('#' + @json($submitBtnId));
        if ($btn.length) {
            $btn.prop('disabled', true).text('Saving...');
        }
    });

    if (perLineTax && window.InvoicePerLineTax) {
        window.InvoicePerLineTax.bindEvents({ tbodySelector: '#' + prefix + '_product_tbody' });
        window.InvoicePerLineTax.syncAllLineTaxUi({ tbodySelector: '#' + prefix + '_product_tbody' });
        recalcTotals();
    }
})(jQuery);
</script>
