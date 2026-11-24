@php
    $pickerPrefix = $pickerPrefix ?? 'grn';
    $storeSelectId = $storeSelectId ?? 'purchase_store_id';
    $poSelectId = $poSelectId ?? 'grn_purchase_order_id';
    $piSelectId = $piSelectId ?? 'grn_purchase_invoice_id';
    $supplierSelectId = $supplierSelectId ?? 'grn_supplier_id';
    $poGrnContextUrl = $poGrnContextUrl ?? null;
    $piItemsUrl = $piItemsUrl ?? null;
    $createInvoiceUrl = $createInvoiceUrl ?? null;
@endphp
<style>
    #grn-discrepancy-review-panel .table th,
    #grn-discrepancy-review-panel .table td {
        font-size: 0.875rem;
    }
    #{{ $pickerPrefix }}_product_tbody tr.grn-discrepancy-row {
        background-color: #fff3cd !important;
    }
    #{{ $pickerPrefix }}_product_tbody tr.grn-discrepancy-row td {
        border-left-color: #ffc107;
    }
</style>
<script>
(function ($) {
    const prefix = @json($pickerPrefix);
    const $poSelect = $('#' + @json($poSelectId));
    const $piSelect = $('#' + @json($piSelectId));
    const $supplier = $('#' + @json($supplierSelectId));
    const $store = $('#' + @json($storeSelectId));
    const $tbody = $('#' + prefix + '_product_tbody');
    const poGrnContextUrlTemplate = @json($poGrnContextUrl);
    const piItemsUrlTemplate = @json($piItemsUrl);
    const createInvoiceUrl = @json($createInvoiceUrl);

    let suppressLinkedChange = false;
    let pendingReceiveItems = null;
    let pendingDiscrepancies = [];

    function recalcTotals() {
        if (window.InvoicePerLineTax) {
            window.InvoicePerLineTax.calculateTotals({ tbodySelector: '#' + prefix + '_product_tbody' });
        }
    }

    function loadProductRows(rows) {
        $tbody.empty();
        if (!rows || !rows.length) {
            recalcTotals();
            return;
        }
        $.each(rows, function (_, item) {
            if (typeof window[prefix + 'AddProductRow'] === 'function') {
                window[prefix + 'AddProductRow'](
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
            }
        });
        recalcTotals();
    }

    function setHeaderFields(header) {
        if (!header) return;
        suppressLinkedChange = true;
        if ($supplier.length && header.supplier_id) {
            $supplier.val(String(header.supplier_id));
        }
        if (header.store_id) {
            $store.val(String(header.store_id));
        }
        suppressLinkedChange = false;
    }

    function populateInvoiceOptions(invoices, selectedId) {
        const current = selectedId ? String(selectedId) : ($piSelect.val() || '');
        $piSelect.find('option[data-linked-po="1"]').remove();
        (invoices || []).forEach(function (inv) {
            const $opt = $('<option>', {
                value: String(inv.id),
                text: inv.label,
            }).attr('data-linked-po', '1');
            $piSelect.append($opt);
        });
        if (selectedId) {
            $piSelect.val(String(selectedId));
        } else if (current && $piSelect.find('option[value="' + current + '"]').length) {
            $piSelect.val(current);
        } else {
            $piSelect.val('');
        }
    }

    function showNoInvoiceAlert(poNumber) {
        const createLink = createInvoiceUrl
            ? '<a href="' + createInvoiceUrl.replace('__PO__', $poSelect.val()) + '" class="alert-link">Create purchase invoice</a>'
            : 'Create a purchase invoice';
        Swal.fire({
            icon: 'warning',
            title: 'Purchase invoice required',
            html: 'Purchase order <strong>' + (poNumber || '') + '</strong> has no confirmed purchase invoice yet. '
                + 'Goods cannot be received against a PO until a purchase invoice is linked.<br><br>' + createLink,
        });
        loadProductRows([]);
    }

    function buildDiscrepancyHtml(discrepancies) {
        if (!discrepancies || !discrepancies.length) {
            return '';
        }
        let rows = '';
        discrepancies.forEach(function (d) {
            rows += '<tr>'
                + '<td>' + (d.product_name || '—') + '</td>'
                + '<td>' + (d.po_qty ?? '—') + '</td>'
                + '<td>' + (d.pi_qty ?? '—') + '</td>'
                + '<td>' + (d.po_unit_price != null ? Number(d.po_unit_price).toFixed(2) : '—') + '</td>'
                + '<td>' + (d.pi_unit_price != null ? Number(d.pi_unit_price).toFixed(2) : '—') + '</td>'
                + '<td class="small">' + (d.message || '') + '</td>'
                + '</tr>';
        });
        return '<div class="table-responsive"><table class="table table-sm table-bordered mb-0">'
            + '<thead><tr><th>Product</th><th>PO Qty</th><th>Invoice Qty</th><th>PO Price</th><th>Invoice Price</th><th>Note</th></tr></thead>'
            + '<tbody>' + rows + '</tbody></table></div>'
            + '<p class="small text-muted mt-2 mb-0">If you continue, goods will be received per the <strong>purchase invoice</strong> quantities and prices.</p>';
    }

    function clearDiscrepancyHighlights() {
        $tbody.find('tr.grn-discrepancy-row').removeClass('grn-discrepancy-row');
    }

    function highlightDiscrepancyRows(discrepancies) {
        clearDiscrepancyHighlights();
        if (!discrepancies || !discrepancies.length) {
            return;
        }

        const names = discrepancies.map(function (d) {
            return String(d.product_name || '').trim().toLowerCase();
        }).filter(Boolean);

        $tbody.find('tr').each(function () {
            const $row = $(this);
            const productName = String($row.find('input[name="product_name[]"]').val() || '').trim().toLowerCase();
            if (productName && names.indexOf(productName) !== -1) {
                $row.addClass('grn-discrepancy-row');
            }
        });
    }

    function hideReviewPanel() {
        $('#grn-discrepancy-review-panel').addClass('d-none');
    }

    function showReviewPanel(discrepancies) {
        let $panel = $('#grn-discrepancy-review-panel');
        if (!$panel.length) {
            $panel = $('<div id="grn-discrepancy-review-panel" class="card border-warning mb-3"></div>');
            $('#' + @json($formId ?? 'grn-form')).closest('.card-body').prepend($panel);
        }

        $panel.removeClass('d-none').html(
            '<div class="card-header bg-warning bg-opacity-10 d-flex flex-wrap justify-content-between align-items-center gap-2">'
            + '<div><strong>Review: PO vs purchase invoice differences</strong>'
            + '<div class="small text-muted">Compare PO and invoice below. Highlighted product lines match the purchase invoice (what will be received).</div></div>'
            + '<button type="button" class="btn btn-sm btn-outline-secondary" id="grn-dismiss-review-panel">Dismiss</button>'
            + '</div>'
            + '<div class="card-body">' + buildDiscrepancyHtml(discrepancies) + '</div>'
        );

        $panel.off('click', '#grn-dismiss-review-panel').on('click', '#grn-dismiss-review-panel', function () {
            hideReviewPanel();
        });
    }

    function reviewDiscrepancies(discrepancies, items) {
        showReviewPanel(discrepancies);
        applyReceiveItems(items || []);
        highlightDiscrepancyRows(discrepancies);

        const $panel = $('#grn-discrepancy-review-panel');
        if ($panel.length) {
            $('html, body').animate({ scrollTop: $panel.offset().top - 80 }, 300);
        }
    }

    // Old: Review (cancel) did nothing — only Receive per invoice loaded lines.
    function confirmReceiveWithDiscrepancies(discrepancies, onConfirm, onReview) {
        if (!discrepancies || !discrepancies.length) {
            hideReviewPanel();
            onConfirm();
            return;
        }
        Swal.fire({
            icon: 'warning',
            title: 'PO vs purchase invoice differences',
            html: buildDiscrepancyHtml(discrepancies),
            width: 900,
            showCancelButton: true,
            confirmButtonText: 'Receive per invoice',
            cancelButtonText: 'Review',
        }).then(function (result) {
            if (result.isConfirmed) {
                hideReviewPanel();
                clearDiscrepancyHighlights();
                onConfirm();
                return;
            }
            if (result.dismiss === Swal.DismissReason.cancel && typeof onReview === 'function') {
                onReview();
            }
        });
    }

    function applyReceiveItems(items) {
        loadProductRows(items || []);
    }

    function loadPurchaseOrderGrnContext(poId, invoiceId) {
        if (!poId) {
            $piSelect.find('option[data-linked-po="1"]').remove();
            loadProductRows([]);
            return;
        }

        const url = poGrnContextUrlTemplate.replace('__PO__', poId);
        $.ajax({
            url: url,
            method: 'GET',
            data: {
                store_id: $store.val() || '',
                invoice_id: invoiceId || '',
            },
            success: function (response) {
                const po = response.purchase_order || {};
                setHeaderFields(po);

                if (response.already_received) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Already received',
                        text: response.message || 'This purchase order has already been fully received.',
                    });
                    populateInvoiceOptions([], null);
                    loadProductRows([]);
                    return;
                }

                if (response.batch_locked) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Batch purchase',
                        text: response.message || 'This purchase invoice is part of a confirmed batch purchase.',
                    });
                    populateInvoiceOptions([], null);
                    loadProductRows([]);
                    return;
                }

                if (!response.has_confirmed_invoice) {
                    showNoInvoiceAlert(po.po_number);
                    populateInvoiceOptions([], null);
                    return;
                }

                populateInvoiceOptions(response.purchase_invoices || [], response.selected_invoice_id);

                if (!response.selected_invoice_id) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Select purchase invoice',
                        text: 'This purchase order has multiple confirmed invoices. Choose which invoice to receive against.',
                    });
                    loadProductRows([]);
                    return;
                }

                pendingReceiveItems = response.items_to_receive || [];
                pendingDiscrepancies = response.discrepancies || [];

                confirmReceiveWithDiscrepancies(pendingDiscrepancies, function () {
                    applyReceiveItems(pendingReceiveItems);
                }, function () {
                    reviewDiscrepancies(pendingDiscrepancies, pendingReceiveItems);
                });
            },
            error: function (xhr) {
                const msg = (xhr.responseJSON || {}).message || 'Could not load purchase order context.';
                Swal.fire({ icon: 'error', title: 'Error', text: msg });
            },
        });
    }

    function loadPurchaseInvoiceItems(invoiceId) {
        if (!invoiceId) {
            if (!$poSelect.val()) {
                loadProductRows([]);
            }
            return;
        }

        const url = piItemsUrlTemplate.replace('__INVOICE__', invoiceId);
        $.ajax({
            url: url,
            method: 'GET',
            data: { store_id: $store.val() || '' },
            success: function (response) {
                const inv = response.purchase_invoice || {};
                setHeaderFields(inv);
                suppressLinkedChange = true;
                if (inv.purchase_order_id) {
                    $poSelect.val(String(inv.purchase_order_id));
                }
                suppressLinkedChange = false;
                loadProductRows(response.items || []);
                if (!(response.items || []).length) {
                    Swal.fire({ icon: 'info', title: 'No items', text: 'This purchase invoice has no line items.' });
                }
            },
            error: function (xhr) {
                const msg = (xhr.responseJSON || {}).message || 'Could not load purchase invoice items.';
                Swal.fire({ icon: 'error', title: 'Error', text: msg });
            },
        });
    }

    $poSelect.on('change', function () {
        if (suppressLinkedChange) return;
        hideReviewPanel();
        clearDiscrepancyHighlights();
        const poId = $(this).val();
        if (poId) {
            suppressLinkedChange = true;
            $piSelect.val('');
            suppressLinkedChange = false;
            loadPurchaseOrderGrnContext(poId, '');
            return;
        }
        $piSelect.find('option[data-linked-po="1"]').remove();
        if (!$piSelect.val()) {
            loadProductRows([]);
        }
    });

    $piSelect.on('change', function () {
        if (suppressLinkedChange) return;
        hideReviewPanel();
        clearDiscrepancyHighlights();
        const invoiceId = $(this).val();
        const poId = $poSelect.val();
        if (poId && $(this).find('option:selected').attr('data-linked-po') === '1') {
            loadPurchaseOrderGrnContext(poId, invoiceId);
            return;
        }
        if (invoiceId && !poId) {
            loadPurchaseInvoiceItems(invoiceId);
        }
    });

    $store.on('change', function () {
        if (suppressLinkedChange) return;
        const poId = $poSelect.val();
        const invoiceId = $piSelect.val();
        if (poId) {
            loadPurchaseOrderGrnContext(poId, invoiceId);
            return;
        }
        if (invoiceId) {
            loadPurchaseInvoiceItems(invoiceId);
        }
    });

    $('#' + @json($formId ?? 'grn-form')).on('submit', function (e) {
        const poId = $poSelect.val();
        const invoiceId = $piSelect.val();
        if (poId && !invoiceId) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Purchase invoice required',
                text: 'Select a confirmed purchase invoice linked to this purchase order before saving the GRN.',
            });
            const $btn = $('#' + @json($submitBtnId ?? 'grn-submit'));
            if ($btn.length) {
                $btn.prop('disabled', false).text('Save GRN');
            }
        }
    });

    @if(!empty($initialPurchaseOrderId))
    if ($poSelect.length) {
        loadPurchaseOrderGrnContext(@json($initialPurchaseOrderId), @json($initialPurchaseInvoiceId ?? ''));
    }
    @elseif(!empty($initialPurchaseInvoiceId))
    if ($piSelect.length) {
        $piSelect.val(String(@json($initialPurchaseInvoiceId)));
        loadPurchaseInvoiceItems(@json($initialPurchaseInvoiceId));
    }
    @endif
})(jQuery);
</script>
