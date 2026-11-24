@php
    $holdBatchPayload = $holdBatchPayload ?? null;
@endphp
<script>
$(function () {
    const searchUrl = @json(route('admin.batch-purchases.search-invoices'));
    const previewUrl = @json(route('admin.batch-purchases.preview'));
    const holdPayload = @json($holdBatchPayload);
    const excludeBatchId = holdPayload ? holdPayload.batch_id : null;

    const $tbody = $('#batch_product_tbody');
    const $store = $('#batch_store_id');
    const $chargeTotal = $('#batch_charge_invoices_total');
    const $allocatedTotal = $('#batch_allocated_charges_total');
    const $balanceWrap = $('#batch_charges_balance_wrap');

    let productInvoices = [];
    let chargeInvoices = [];
    let chargePoolSubtotal = 0;
    let previewTimer = null;
    let lastPreviewChargeSubtotal = null;

    function sumExistingCharges(map) {
        return Object.keys(map).reduce(function (sum, key) {
            return sum + (Number(map[key]) || 0);
        }, 0);
    }

    function formatMoney(value) {
        const num = Number(value) || 0;
        return '$' + num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function selectedProductIds() {
        return productInvoices.map(function (inv) { return inv.id; });
    }

    function selectedChargeIds() {
        return chargeInvoices.map(function (inv) { return inv.id; });
    }

    function renderInvoiceTags(type) {
        const list = type === 'product' ? productInvoices : chargeInvoices;
        const $wrap = type === 'product' ? $('#batch_product_invoice_selected') : $('#batch_charge_invoice_selected');
        $wrap.empty();
        list.forEach(function (inv) {
            const $tag = $('<span class="badge bg-light text-dark border d-inline-flex align-items-center gap-1 p-2"></span>');
            $tag.append($('<span></span>').text(inv.display || inv.label));
            const $btn = $('<button type="button" class="btn-close btn-close-sm" aria-label="Remove"></button>');
            $btn.on('click', function () {
                if (type === 'product') {
                    productInvoices = productInvoices.filter(function (x) { return x.id !== inv.id; });
                } else {
                    chargeInvoices = chargeInvoices.filter(function (x) { return x.id !== inv.id; });
                }
                renderInvoiceTags(type);
                queuePreview();
            });
            $tag.append($btn);
            $wrap.append($tag);
        });
        updateChargePoolDisplay();
    }

    function updateChargePoolDisplay() {
        chargePoolSubtotal = chargeInvoices.reduce(function (sum, inv) {
            return sum + (Number(inv.subtotal) || 0);
        }, 0);
        $chargeTotal.text(formatMoney(chargePoolSubtotal));
        updateAllocatedChargesDisplay();
    }

    function updateAllocatedChargesDisplay() {
        let allocated = 0;
        $tbody.find('input.batch-additional-charges').each(function () {
            allocated += Number($(this).val()) || 0;
        });
        allocated = Math.round(allocated * 100) / 100;
        $allocatedTotal.text(formatMoney(allocated));

        if (chargeInvoices.length === 0) {
            $balanceWrap.text('');
            return;
        }

        const diff = Math.round((allocated - chargePoolSubtotal) * 100) / 100;
        if (Math.abs(diff) < 0.01) {
            $balanceWrap.html('<span class="text-success">Balanced</span>');
        } else {
            $balanceWrap.html('<span class="text-danger">Difference: ' + formatMoney(diff) + '</span>');
        }
    }

    function recalcBatchRow($row) {
        const qty = parseFloat($row.find('input[name="qty[]"]').val()) || 0;
        const price = parseFloat($row.find('input[name="price[]"]').val()) || 0;
        const extra = parseFloat($row.find('.batch-additional-charges').val()) || 0;
        const amount = Math.round(qty * price * 100) / 100;
        const grandTotal = Math.round((amount + extra) * 100) / 100;
        const finalUnit = qty > 0 ? (grandTotal / qty) : 0;

        $row.find('.batch-line-amount').val(amount.toFixed(2));
        $row.find('.batch-line-grand-total').val(grandTotal.toFixed(2));
        $row.find('.batch-line-final-unit').val(finalUnit.toFixed(4));
        $row.find('input[name="final_unit[]"]').val(finalUnit.toFixed(4));
    }

    function recalcAllRows() {
        $tbody.find('tr').each(function () {
            recalcBatchRow($(this));
        });
        if (window.InvoicePerLineTax) {
            window.InvoicePerLineTax.calculateTotals({ tbodySelector: '#batch_product_tbody' });
        }
        updateAllocatedChargesDisplay();
        updateGrandTotalsSum();
    }

    function updateGrandTotalsSum() {
        let grandSum = 0;
        $tbody.find('.batch-line-grand-total').each(function () {
            grandSum += Number($(this).val()) || 0;
        });
        grandSum = Math.round(grandSum * 100) / 100;
        $('#batch_grand_totals_sum').text(formatMoney(grandSum));

        const taxText = ($('#batch_tax').text() || '').replace(/[^0-9.-]/g, '');
        const tax = Number(taxText) || 0;
        // Overall purchase total includes allocated additional charges plus line tax.
        $('#batch_total').text(formatMoney(Math.round((grandSum + tax) * 100) / 100));
    }

    function addBatchRow(line) {
        const $row = $('<tr></tr>');
        const taxable = line.is_taxable !== false;
        const rate = line.tax_rate != null ? parseFloat(line.tax_rate) : 0;

        function cellInput(name, value, attrs) {
            const $td = $('<td></td>');
            const $input = $('<input>', Object.assign({
                type: 'text',
                name: name,
                class: 'form-control form-control-sm',
                value: value != null ? value : '',
            }, attrs || {}));
            $td.append($input);
            return $td;
        }

        const $td1 = $('<td></td>');
        $td1.append($('<input>', { type: 'hidden', name: 'product_id[]', value: line.product_id || '' }));
        $td1.append($('<input>', { type: 'text', name: 'product_name[]', class: 'form-control form-control-sm', value: line.product_name || '', readonly: true }));
        $row.append($td1);
        $row.append(cellInput('sku[]', line.sku || '', { readonly: true }));
        const $tdCat = $('<td></td>');
        $tdCat.append($('<input>', { type: 'hidden', name: 'category_id[]', value: line.category_id || '' }));
        $tdCat.append($('<input>', { type: 'text', name: 'category[]', class: 'form-control form-control-sm', value: line.category || line.category_name || '', readonly: true }));
        $row.append($tdCat);
        $row.append(cellInput('qty[]', line.qty || 1, { type: 'number', min: 1, class: 'form-control form-control-sm batch-qty' }));
        $row.append(cellInput('price[]', line.unit_price || 0, { class: 'form-control form-control-sm batch-price' }));
        $row.append(cellInput(null, line.amount || 0, { readonly: true, class: 'form-control form-control-sm batch-line-amount bg-light' }));
        $row.append(cellInput('additional_charges[]', line.additional_charges || 0, { type: 'number', step: '0.01', min: 0, class: 'form-control form-control-sm batch-additional-charges' }));
        $row.append(cellInput(null, line.grand_total || line.amount || 0, { readonly: true, class: 'form-control form-control-sm batch-line-grand-total bg-light' }));
        $row.append(cellInput('final_unit[]', line.final_unit || 0, { readonly: true, class: 'form-control form-control-sm batch-line-final-unit bg-light' }));

        const $taxTd = $('<td class="line-tax-col"></td>');
        $taxTd.append($('<input>', { type: 'hidden', name: 'line_is_taxable[]', class: 'line-taxable-val', value: taxable ? '1' : '0' }));
        $taxTd.append($('<input>', { type: 'checkbox', class: 'form-check-input line-taxable-cb', checked: taxable }));
        $row.append($taxTd);
        $row.append(cellInput('line_tax_rate[]', rate, { type: 'number', step: '0.01', min: 0, max: 100, class: 'form-control form-control-sm line-tax-rate' }));
        $row.append($('<td class="line-tax-col line-tax-amount text-end">').text('$0.00'));
        $row.append($('<td></td>').append(
            $('<button type="button" class="btn btn-danger btn-sm batch-delete-row">Delete</button>')
        ));

        $tbody.append($row);
        if (window.InvoicePerLineTax) {
            window.InvoicePerLineTax.setRowDefaultTax($row, taxable, rate);
            window.InvoicePerLineTax.syncLineTaxUi($row);
        }
        recalcBatchRow($row);
    }

    function loadLines(lines) {
        $tbody.empty();
        (lines || []).forEach(function (line) {
            addBatchRow(line);
        });
        recalcAllRows();
    }

    function appendHiddenInvoiceIds() {
        $('#batch_product_invoice_selected input[type="hidden"][name="product_invoice_ids[]"]').remove();
        $('#batch_charge_invoice_selected input[type="hidden"][name="charge_invoice_ids[]"]').remove();
        productInvoices.forEach(function (inv) {
            $('#batch_product_invoice_selected').append(
                $('<input>', { type: 'hidden', name: 'product_invoice_ids[]', value: inv.id })
            );
        });
        chargeInvoices.forEach(function (inv) {
            $('#batch_charge_invoice_selected').append(
                $('<input>', { type: 'hidden', name: 'charge_invoice_ids[]', value: inv.id })
            );
        });
    }

    function queuePreview() {
        clearTimeout(previewTimer);
        previewTimer = setTimeout(runPreview, 350);
    }

    function runPreview() {
        appendHiddenInvoiceIds();
        if (!selectedProductIds().length) {
            $tbody.empty();
            recalcAllRows();
            return;
        }
        if (!$store.val()) {
            return;
        }

        $.ajax({
            url: previewUrl,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
            data: {
                store_id: $store.val(),
                product_invoice_ids: selectedProductIds(),
                charge_invoice_ids: selectedChargeIds(),
                exclude_batch_id: excludeBatchId || '',
            },
            success: function (res) {
                const existingCharges = {};
                $tbody.find('tr').each(function () {
                    const pid = $(this).find('input[name="product_id[]"]').val();
                    if (pid) {
                        existingCharges[pid] = $(this).find('.batch-additional-charges').val();
                    }
                });

                const newPool = Number(res.charge_invoices_subtotal) || 0;
                const chargePoolChanged = lastPreviewChargeSubtotal !== null
                    && Math.abs(lastPreviewChargeSubtotal - newPool) > 0.01;
                const manualAllocation = sumExistingCharges(existingCharges) > 0.01;
                lastPreviewChargeSubtotal = newPool;

                loadLines(res.lines || []);

                // Keep manual allocations only when charge pool is unchanged; otherwise use server split.
                if (!chargePoolChanged && manualAllocation) {
                    $tbody.find('tr').each(function () {
                        const pid = $(this).find('input[name="product_id[]"]').val();
                        if (pid && existingCharges[pid] !== undefined) {
                            $(this).find('.batch-additional-charges').val(existingCharges[pid]);
                        }
                    });
                }

                chargePoolSubtotal = newPool;
                $chargeTotal.text(formatMoney(chargePoolSubtotal));
                recalcAllRows();
            },
            error: function (xhr) {
                const msg = (xhr.responseJSON || {}).message || 'Could not build batch preview.';
                Swal.fire({ icon: 'error', title: 'Preview failed', text: msg });
            },
        });
    }

    function bindInvoiceSearch(inputId, resultsId, type) {
        const $input = $('#' + inputId);
        const $results = $('#' + resultsId);

        $input.on('keyup focus', function () {
            const q = $(this).val();
            if (!$store.val()) {
                Swal.fire({ icon: 'warning', title: 'Select store', text: 'Choose a store before searching invoices.' });
                return;
            }
            $.get(searchUrl, {
                q: q,
                store_id: $store.val(),
                exclude_batch_id: excludeBatchId || '',
            }).done(function (items) {
                $results.empty().show();
                const selectedIds = type === 'product' ? selectedProductIds() : selectedChargeIds();
                if (!items.length) {
                    $results.append('<li class="list-group-item">No invoices found</li>');
                    return;
                }
                items.forEach(function (item) {
                    if (selectedIds.indexOf(item.id) !== -1) {
                        return;
                    }
                    const $li = $('<li class="list-group-item" style="cursor:pointer;"></li>').text(item.display);
                    $li.on('click', function () {
                        const entry = { id: item.id, display: item.display, label: item.label, subtotal: item.subtotal };
                        if (type === 'product') {
                            productInvoices.push(entry);
                            renderInvoiceTags('product');
                        } else {
                            chargeInvoices.push(entry);
                            renderInvoiceTags('charge');
                        }
                        $input.val('');
                        $results.hide();
                        queuePreview();
                    });
                    $results.append($li);
                });
            });
        });
    }

    bindInvoiceSearch('batch_product_invoice_search', 'batch_product_invoice_results', 'product');
    bindInvoiceSearch('batch_charge_invoice_search', 'batch_charge_invoice_results', 'charge');

    $store.on('change', function () {
        productInvoices = [];
        chargeInvoices = [];
        renderInvoiceTags('product');
        renderInvoiceTags('charge');
        queuePreview();
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('#batch_product_invoice_search, #batch_product_invoice_results, #batch_charge_invoice_search, #batch_charge_invoice_results').length) {
            $('#batch_product_invoice_results, #batch_charge_invoice_results').hide();
        }
    });

    $(document).on('input change', '.batch-qty, .batch-price, .batch-additional-charges', function () {
        recalcBatchRow($(this).closest('tr'));
        if (window.InvoicePerLineTax) {
            window.InvoicePerLineTax.calculateTotals({ tbodySelector: '#batch_product_tbody' });
        }
        updateAllocatedChargesDisplay();
    });

    $(document).on('click', '.batch-delete-row', function () {
        $(this).closest('tr').remove();
        recalcAllRows();
    });

    if (window.InvoicePerLineTax) {
        window.InvoicePerLineTax.bindEvents({ tbodySelector: '#batch_product_tbody' });
    }

    function validateBeforeSubmit(onHold) {
        if (!$store.val()) {
            Swal.fire({ icon: 'warning', title: 'Store required', text: 'Choose a store.' });
            return false;
        }
        if (!selectedProductIds().length) {
            Swal.fire({ icon: 'warning', title: 'Product invoices required', text: 'Select at least one product invoice.' });
            return false;
        }
        if (!$tbody.find('tr').length) {
            Swal.fire({ icon: 'warning', title: 'No lines', text: 'Add at least one product line.' });
            return false;
        }
        if (!onHold && chargeInvoices.length) {
            let allocated = 0;
            $tbody.find('.batch-additional-charges').each(function () {
                allocated += Number($(this).val()) || 0;
            });
            allocated = Math.round(allocated * 100) / 100;
            const pool = Math.round(chargePoolSubtotal * 100) / 100;
            if (Math.abs(allocated - pool) > 0.01) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Additional charges mismatch',
                    text: 'Allocated additional charges (' + formatMoney(allocated) + ') must equal additional charge invoices total (' + formatMoney(pool) + ').',
                });
                return false;
            }
        }
        return true;
    }

    function submitBatch(onHold) {
        if (!validateBeforeSubmit(onHold)) {
            return;
        }
        $('#batch_status').val(onHold ? 'draft' : 'confirmed');
        appendHiddenInvoiceIds();
        $('#batch-confirm-btn, #batch-hold-btn').prop('disabled', true);
        const $form = $('#batch-purchase-form');
        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: $form.serialize(),
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            success: function (res) {
                Swal.fire({ icon: 'success', title: 'Saved', text: res.message || '' }).then(function () {
                    window.location.href = res.redirect_url || @json(route('admin.batch-purchases.index'));
                });
            },
            error: function (xhr) {
                let text = (xhr.responseJSON || {}).message || 'Could not save batch purchase.';
                const errors = (xhr.responseJSON || {}).errors;
                if (errors) {
                    const key = Object.keys(errors)[0];
                    if (key && errors[key][0]) text = errors[key][0];
                }
                Swal.fire({ icon: 'error', title: 'Error', text: text });
            },
            complete: function () {
                $('#batch-confirm-btn, #batch-hold-btn').prop('disabled', false);
            },
        });
    }

    $('#batch-confirm-btn').on('click', function () { submitBatch(false); });
    $('#batch-hold-btn').on('click', function () { submitBatch(true); });
    $('#batch-purchase-form').on('submit', function (e) { e.preventDefault(); });

    if (holdPayload) {
        productInvoices = holdPayload.product_invoices || [];
        chargeInvoices = (holdPayload.charge_invoices || []).map(function (inv) {
            return {
                id: inv.id,
                display: inv.display,
                subtotal: Number(inv.subtotal) || 0,
            };
        });
        renderInvoiceTags('product');
        renderInvoiceTags('charge');
        loadLines(holdPayload.lines || []);
        chargePoolSubtotal = Number(holdPayload.charge_invoices_subtotal) || 0;
        $chargeTotal.text(formatMoney(chargePoolSubtotal));
        updateAllocatedChargesDisplay();
    }
});
</script>
