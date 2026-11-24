<script>
(function () {
    const printDataUrlTemplate = @json(route('admin.supplier-purchase-invoices.print-data', ['invoice' => '__INVOICE__']));

    function formatPinvPrintCurrency(value) {
        const num = Number(value) || 0;
        return '$' + num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    window.openPurchaseInvoicePrint = function (invoiceId) {
        const url = printDataUrlTemplate.replace('__INVOICE__', invoiceId);
        $.ajax({
            url: url,
            type: 'GET',
            success: function (data) {
                $('#pinv-print-supplier').text(data.supplier_name || '—');
                $('#pinv-print-internal-no').text(data.invoice_no || '—');
                $('#pinv-print-supplier-no').text(data.supplier_invoice_number || '—');
                $('#pinv-print-store').text(data.store_name || '—');
                $('#pinv-print-date').text(data.invoice_date || '—');
                $('#pinv-print-due-date').text(data.due_date || '—');
                $('#pinv-print-subtotal').text(formatPinvPrintCurrency(data.sub_total));
                $('#pinv-print-tax').text(formatPinvPrintCurrency(data.tax_amount));
                $('#pinv-print-total').text(formatPinvPrintCurrency(data.total));
                $('#pinv-print-due').text(formatPinvPrintCurrency(data.due_amount));

                const $lines = $('#pinv-print-lines').empty();
                (data.items || []).forEach(function (item) {
                    const taxText = item.is_taxable && item.tax_rate > 0
                        ? formatPinvPrintCurrency(item.tax_amount) + ' (' + item.tax_rate + '%)'
                        : '—';
                    $lines.append(`
                        <tr>
                            <td>${item.product_name}</td>
                            <td class="text-end">${item.qty}</td>
                            <td class="text-end">${formatPinvPrintCurrency(item.unit_price)}</td>
                            <td class="text-end">${taxText}</td>
                            <td class="text-end">${formatPinvPrintCurrency(item.line_total)}</td>
                        </tr>`);
                });

                $('#print-purchase-invoice').modal('show');
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Print unavailable',
                    text: 'Invoice was saved but the print preview could not be loaded.',
                });
            }
        });
    };

    $('#print-purchase-invoice').on('hidden.bs.modal', function () {
        if (window._redirectAfterPurchasePrint) {
            const url = window._redirectAfterPurchasePrint;
            window._redirectAfterPurchasePrint = null;
            window.location.href = url;
        }
    });

    $(document).on('click', '#pinvPrintButton', function () {
        const modalBody = document.querySelector('#print-purchase-invoice .modal-body');
        if (!modalBody) return;

        const printContent = modalBody.innerHTML;
        let styles = '';
        document.querySelectorAll('link[rel="stylesheet"], style').forEach(function (el) {
            styles += el.outerHTML;
        });

        let iframe = document.getElementById('pinv-print-iframe');
        if (!iframe) {
            iframe = document.createElement('iframe');
            iframe.id = 'pinv-print-iframe';
            iframe.style.position = 'fixed';
            iframe.style.right = '0';
            iframe.style.bottom = '0';
            iframe.style.width = '0';
            iframe.style.height = '0';
            iframe.style.border = 'none';
            document.body.appendChild(iframe);
        }

        const doc = iframe.contentWindow.document;
        doc.open();
        doc.write('<html><head><title>Print Purchase Invoice</title>' + styles +
            '<style>.no-print{display:none!important} body{padding:20px;font-family:Arial,sans-serif;}</style>' +
            '</head><body>' + printContent + '</body></html>');
        doc.close();

        iframe.onload = function () {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
        };
    });
})();
</script>
