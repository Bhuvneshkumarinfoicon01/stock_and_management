{{-- Shared per-line tax calculation. Set window.PerLineTaxConfig before include for purchase forms. --}}
<script>
(function (window) {
    function cfg(key, fallback) {
        const c = window.PerLineTaxConfig || {};
        return Object.prototype.hasOwnProperty.call(c, key) ? c[key] : fallback;
    }

    function sel(name, fallback) {
        return cfg(name, fallback);
    }

    function formatInvoiceCurrency(value) {
        const num = Number(value) || 0;
        return num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function isInvoiceTaxExempt() {
        return $(sel('taxExemptSelector', '#invoice_tax_exempt')).is(':checked');
    }

    function syncInvoiceTaxColumnVisibility() {
        const exempt = isInvoiceTaxExempt();
        $(sel('lineTaxColSelector', '.line-tax-col')).toggleClass('d-none', exempt);
    }

    function lineTaxable($row) {
        if (isInvoiceTaxExempt()) {
            return false;
        }
        return $row.find('.line-taxable-val').val() === '1';
    }

    function parseTaxableFlag(value) {
        return value !== false && value !== 0 && value !== '0';
    }

    /** Store product default tax on the row (used when tax-exempt is unchecked). */
    function setRowDefaultTax($row, isTaxable, taxRate) {
        const taxable = parseTaxableFlag(isTaxable);
        const rate = taxRate != null && taxRate !== '' ? parseFloat(taxRate) : 0;
        $row.attr('data-default-is-taxable', taxable ? '1' : '0');
        $row.attr('data-default-tax-rate', String(taxable ? rate : 0));
    }

    function getRowDefaultTax($row) {
        if ($row.attr('data-default-is-taxable') === undefined) {
            return null;
        }
        const taxable = $row.attr('data-default-is-taxable') === '1';
        const rate = parseFloat($row.attr('data-default-tax-rate')) || 0;
        return { taxable, rate };
    }

    function restoreRowDefaultTax($row) {
        const defaults = getRowDefaultTax($row);
        if (!defaults) {
            return false;
        }
        const $rate = $row.find('.line-tax-rate');
        $row.find('.line-taxable-cb').prop('checked', defaults.taxable);
        $row.find('.line-taxable-val').val(defaults.taxable ? '1' : '0');
        $rate.val(defaults.taxable ? defaults.rate : 0);
        $rate.prop('readonly', !defaults.taxable);
        return true;
    }

    function syncLineTaxUi($row) {
        const templateId = sel('templateRowId', 'product-row-template');
        if ($row.attr('id') === templateId) {
            return;
        }

        const exempt = isInvoiceTaxExempt();
        const $rate = $row.find('.line-tax-rate');

        if (exempt) {
            $row.find('.line-taxable-val').val('0');
            $row.find('.line-taxable-cb').prop('checked', false);
            $rate.val(0).prop('readonly', true);
        } else if (restoreRowDefaultTax($row)) {
            // Restored product defaults after tax-exempt was unchecked.
        } else {
            // Old fallback when no defaults were stored on the row:
            const taxable = $row.find('.line-taxable-cb').is(':checked');
            $row.find('.line-taxable-val').val(taxable ? '1' : '0');
            $rate.prop('readonly', !taxable);
        }

        syncInvoiceTaxColumnVisibility();
    }

    function syncAllLineTaxUi(options) {
        options = options || {};
        const tbodySelector = options.tbodySelector || sel('tbodySelector', '#product-tbody');

        $(tbodySelector + ' tr').each(function () {
            syncLineTaxUi($(this));
        });
        syncInvoiceTaxColumnVisibility();
    }

    function calculatePerLineInvoiceTotals(options) {
        options = options || {};
        const tbodySelector = options.tbodySelector || sel('tbodySelector', '#product-tbody');
        const skipTemplate = options.skipTemplate !== false;
        const templateId = sel('templateRowId', 'product-row-template');

        let subtotal = 0;
        let taxTotal = 0;

        $(tbodySelector + ' tr').each(function () {
            if (skipTemplate && $(this).attr('id') === templateId) {
                return;
            }
            const $row = $(this);
            const qty = parseFloat($row.find('input[name="qty[]"]').val()) || 0;
            const price = parseFloat($row.find('input[name="price[]"]').val()) || 0;
            const lineSubtotal = qty * price;
            subtotal += lineSubtotal;

            let lineTax = 0;
            if (lineTaxable($row)) {
                const rate = parseFloat($row.find('.line-tax-rate').val()) || 0;
                lineTax = (lineSubtotal * rate) / 100;
            }
            taxTotal += lineTax;
            $row.find('.line-tax-amount').text('$' + formatInvoiceCurrency(lineTax));
        });

        const discountRate = parseFloat($(sel('discountSelector', '#discountDropdown')).val()) || 0;
        const discountAmount = (subtotal * discountRate) / 100;
        const total = subtotal + taxTotal - discountAmount;

        $(sel('subTotalSelector', '#sub-total')).text('$' + formatInvoiceCurrency(subtotal));
        $(sel('taxSelector', '#tax')).text('$' + formatInvoiceCurrency(taxTotal));
        $(sel('taxLabelSelector', '#tax_percentage')).text(taxTotal > 0 ? 'Tax (line total)' : 'Tax');
        $(sel('discountAmountSelector', '#discount')).text('$' + formatInvoiceCurrency(discountAmount));
        $(sel('discountLabelSelector', '#discount_percentage')).text('Discount (' + discountRate + '%)');
        $(sel('totalSelector', '#total')).text('$' + formatInvoiceCurrency(total));
        const placeOrderSel = sel('placeOrderValueSelector', '#placeOrderValue');
        if ($(placeOrderSel).length) {
            $(placeOrderSel).text(formatInvoiceCurrency(total));
        }

        return { subtotal, taxTotal, discountAmount, total };
    }

    function bindPerLineTaxEvents(options) {
        options = options || {};
        const tbodySelector = options.tbodySelector || sel('tbodySelector', '#product-tbody');
        const taxExemptSelector = sel('taxExemptSelector', '#invoice_tax_exempt');

        $(document).off('change.perLineTax', taxExemptSelector).on('change.perLineTax', taxExemptSelector, function () {
            $(tbodySelector + ' tr').each(function () {
                syncLineTaxUi($(this));
            });
            syncInvoiceTaxColumnVisibility();
            calculatePerLineInvoiceTotals(options);
        });

        $(document).off('change.perLineTax', '.line-taxable-cb').on('change.perLineTax', '.line-taxable-cb', function () {
            const $row = $(this).closest('tr');
            $row.find('.line-taxable-val').val($(this).is(':checked') ? '1' : '0');
            syncLineTaxUi($row);
            calculatePerLineInvoiceTotals(options);
        });

        $(document).off('input.perLineTax change.perLineTax', '.line-tax-rate, input[name="qty[]"], input[name="price[]"]')
            .on('input.perLineTax change.perLineTax', '.line-tax-rate, input[name="qty[]"], input[name="price[]"]', function () {
                calculatePerLineInvoiceTotals(options);
            });

        const discountSelector = sel('discountSelector', '#discountDropdown');
        $(document).off('change.perLineTax', discountSelector).on('change.perLineTax', discountSelector, function () {
            calculatePerLineInvoiceTotals(options);
        });

        syncInvoiceTaxColumnVisibility();
    }

    window.InvoicePerLineTax = {
        formatCurrency: formatInvoiceCurrency,
        isTaxExempt: isInvoiceTaxExempt,
        setRowDefaultTax: setRowDefaultTax,
        getRowDefaultTax: getRowDefaultTax,
        restoreRowDefaultTax: restoreRowDefaultTax,
        syncLineTaxUi: syncLineTaxUi,
        syncAllLineTaxUi: syncAllLineTaxUi,
        syncColumnVisibility: syncInvoiceTaxColumnVisibility,
        calculateTotals: calculatePerLineInvoiceTotals,
        bindEvents: bindPerLineTaxEvents,
        lineTaxable: lineTaxable,
    };
})(window);
</script>
