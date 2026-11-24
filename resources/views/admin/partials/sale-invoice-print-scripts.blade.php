<script>
(function () {
    function formatPrintCurrency(value) {
        if (window.InvoicePerLineTax && window.InvoicePerLineTax.formatCurrency) {
            return window.InvoicePerLineTax.formatCurrency(value);
        }
        const num = Number(value) || 0;
        return num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    window.openInvoicePrintReceipt = function (transactionId, options) {
        options = options || {};
        const customerName = options.customerName || '-';
        const date = options.date || '-';
        const total = options.total || '-';
        const invoice = options.invoice || '-';
        const custId = options.customerId || '-';
        const dueDate = options.dueDate || '-';
        const formattedName = String(customerName).replace(/\b\w/g, (c) => c.toUpperCase());

        $("#print-receipt .invoice-user-name span:contains('Name:')").next().text(formattedName);
        $("#print-receipt .invoice-user-name span:contains('Customer Id:')").next().text(custId);
        $("#print-receipt .invoice-user-name span:contains('Invoice No:')").next().text(invoice);
        $("#print-receipt .invoice-user-name span:contains('Date:')").next().text(date);
        $("#print-due-date-row span:last").text(dueDate);
        $("#print-receipt .text-end:contains('Total Bill')").next().text(total);
        $("#print-receipt .text-end:contains('Total Payable')").next().text(total);

        $.ajax({
            url: "{{ route('admin.get-orderItems', '') }}/" + transactionId,
            type: "GET",
            success: function (response) {
                const tbody = $("#print-receipt tbody");
                tbody.empty();
                const subTotal = response.sub_total;
                const discount = response.discount;
                const shipping = response.shipping;
                const tax = response.tax;
                const totalBill = response.total_bill;
                const totalPayable = response.total_payable;
                const dueAmount = response.due_amount || 0;
                const paidAmount = response.paid_amount || 0;
                const responseDueDate = response.due_date || '-';
                const taxAmount = response.tax_amount != null
                    ? parseFloat(response.tax_amount) || 0
                    : (tax / 100) * subTotal;
                const discountAmount = response.discount_amount != null
                    ? parseFloat(response.discount_amount) || 0
                    : (discount / 100) * subTotal;
                const shippingAmount = response.shipping_amount != null
                    ? parseFloat(response.shipping_amount) || 0
                    : (shipping / 100) * subTotal;
                $("#print-due-date-row span:last").text(responseDueDate);

                $.each(response.items, function (_, item) {
                    const itemTotal = item.price * item.qty;
                    tbody.append(`
                        <tr>
                            <td>${item.product_name}</td>
                            <td>${formatPrintCurrency(item.price)}</td>
                            <td>${item.qty}</td>
                            <td class="text-end">${formatPrintCurrency(itemTotal)}</td>
                        </tr>`);
                });

                const taxLabel = tax > 0 ? `Tax (${tax}%)` : 'Tax';
                const shippingLabel = shipping > 0 && shipping <= 100 ? `Shipping (${shipping}%)` : 'Shipping';

                tbody.append(`
                    <tr>
                        <td colspan="4">
                            <table class="table-borderless w-100 table-fit">
                                <tr><td>Sub Total :</td><td class="text-end">$${formatPrintCurrency(subTotal)}</td></tr>
                                <tr><td>Discount (${discount}%):</td><td class="text-end">-$${formatPrintCurrency(discountAmount)}</td></tr>
                                <tr><td>${shippingLabel} :</td><td class="text-end">$${formatPrintCurrency(shippingAmount)}</td></tr>
                                <tr><td>${taxLabel} :</td><td class="text-end">$${formatPrintCurrency(taxAmount)}</td></tr>
                                <tr><td>Total Bill :</td><td class="text-end">$${formatPrintCurrency(totalBill)}</td></tr>
                                <tr><td>Paid :</td><td class="text-end">$${formatPrintCurrency(paidAmount)}</td></tr>
                                <tr><td>Due :</td><td class="text-end">$${formatPrintCurrency(dueAmount)}</td></tr>
                                <tr><td>Total Payable :</td><td class="text-end">$${formatPrintCurrency(totalPayable)}</td></tr>
                            </table>
                        </td>
                    </tr>`);

                $("#print-receipt").modal("show");
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

    $(document).on('click', '.print-receipt', function () {
        window.openInvoicePrintReceipt($(this).data("id"), {
            customerName: $(this).data("customer"),
            date: $(this).data("date"),
            total: $(this).data("total"),
            invoice: $(this).data("invoice"),
            customerId: $(this).data("customerid"),
            dueDate: $(this).data("duedate") || '-',
        });
    });

    $('#print-receipt').on('hidden.bs.modal', function () {
        if (window._reloadAfterInvoicePrint) {
            window._reloadAfterInvoicePrint = false;
            window.location.reload();
            return;
        }
        if (window._redirectAfterInvoicePrint) {
            const url = window._redirectAfterInvoicePrint;
            window._redirectAfterInvoicePrint = null;
            window.location.href = url;
        }
    });

    $(document).on('click', '#printButton', function () {
        const modalBody = document.querySelector('#print-receipt .modal-body');
        if (!modalBody) return;

        const printContent = modalBody.innerHTML;
        let styles = '';
        document.querySelectorAll('link[rel="stylesheet"], style').forEach(function (el) {
            styles += el.outerHTML;
        });

        let iframe = document.getElementById('print-iframe');
        if (!iframe) {
            iframe = document.createElement('iframe');
            iframe.id = 'print-iframe';
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
        doc.write('<html><head><title>Print Receipt</title>' + styles +
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
