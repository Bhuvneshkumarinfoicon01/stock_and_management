@php
    $draftOrderId = $draftOrderId ?? null;
    $cancelUrl = $draftOrderId ? url('admin/sales-invoices-on-hold/' . $draftOrderId . '/cancel') : null;
    $onHoldListUrl = route('admin.sales-invoices-on-hold');
    $creditCheckUrl = route('admin.check-customer-credit-limit');
@endphp
<script>
(function () {
    const draftOrderId = @json($draftOrderId);
    const cancelUrl = @json($cancelUrl);
    const onHoldListUrl = @json($onHoldListUrl);
    const creditCheckUrl = @json($creditCheckUrl);
    const canOverrideCredit = @json(app(\App\Services\CreditLimitService::class)->canOverrideCreditLimit());
    const CREDIT_SALE_TYPE_ID = 2;

    function invoiceFormInputs() {
        return $('#transferForm').find(':input').not('#product-row-template :input');
    }

    function invoiceProductRowCount() {
        return $('#product-tbody tr').not('#product-row-template').length;
    }

    function computeInvoiceTotal() {
        if (window.InvoicePerLineTax) {
            return window.InvoicePerLineTax.calculateTotals().total;
        }
        // Old global tax fallback:
        let subtotal = 0;
        $('#product-tbody tr').not('#product-row-template').each(function () {
            const qty = parseFloat($(this).find('input[name="qty[]"]').val()) || 0;
            const price = parseFloat($(this).find('input[name="price[]"]').val()) || 0;
            subtotal += qty * price;
        });
        const discountRate = parseFloat($('#discountDropdown').val()) || 0;
        const discountAmount = (subtotal * discountRate) / 100;
        return subtotal - discountAmount;
    }

    function validateInvoiceForm(isCreditSale, isCashSale) {
        const paymentMethod = $('#payment_method').val();
        const customerId = $('#customer-select').val();
        const invoiceType = $('#invoice_type').val();
        const paymentTermsDays = $('#payment_terms_days').val();

        if (invoiceProductRowCount() === 0) {
            Swal.fire({ icon: 'warning', title: 'Empty Cart', text: 'Please add at least one product.' });
            return false;
        }
        if (!customerId) {
            Swal.fire({ icon: 'warning', title: 'Customer Required', text: 'Please select a customer.' });
            return false;
        }
        if (!invoiceType) {
            Swal.fire({ icon: 'warning', title: 'Invoice Type Required', text: 'Please select invoice type.' });
            return false;
        }
        if (isCashSale && !paymentMethod) {
            Swal.fire({ icon: 'warning', title: 'Payment Required', text: 'Please select a payment method for Cash Sales.' });
            return false;
        }
        if (isCreditSale && !paymentTermsDays) {
            Swal.fire({ icon: 'warning', title: 'Credit Terms Required', text: 'For Credit Sales, select Payment Terms.' });
            return false;
        }
        return true;
    }

    function submitInvoiceWithStatus(status, $submitBtn, originalBtnHtml) {
        $('#invoice_status').val(status === 'draft' ? 'Draft' : 'Confirmed');

        const fd = invoiceFormInputs().serializeArray().filter(function (f) {
            return f.name !== 'invoice_status';
        });
        fd.push({ name: 'invoice_status', value: status === 'draft' ? 'Draft' : 'Confirmed' });

        if (status === 'confirmed' && draftOrderId && $('#credit_limit_blocked_flag').length && $('#credit_limit_blocked_flag').val() === '1' && canOverrideCredit) {
            fd.push({ name: 'override_credit_limit', value: '1' });
        }

        $submitBtn.prop('disabled', true).html('Saving...');
        $.ajax({
            url: "{{ route('admin.invoice-order') }}",
            method: 'POST',
            data: $.param(fd),
            success: function (response) {
                if (response.success) {
                    const title = status === 'draft' ? 'Saved on hold' : 'Order Successful!';

                    const finishPlacement = function () {
                        if (status === 'confirmed' && response.open_print && response.order_id && typeof window.openInvoicePrintReceipt === 'function') {
                            const $invoiceModal = $('#add-units');
                            if ($invoiceModal.length) {
                                $invoiceModal.modal('hide');
                            }
                            window._reloadAfterInvoicePrint = !draftOrderId;
                            window._redirectAfterInvoicePrint = draftOrderId ? onHoldListUrl : null;
                            window.openInvoicePrintReceipt(response.order_id, {
                                customerName: response.customer_name,
                                customerId: response.customer_id,
                                invoice: response.invoice_number,
                                date: response.order_date,
                                dueDate: response.due_date,
                                total: response.total,
                            });
                            return;
                        }

                        // Old: reload or redirect immediately after success alert only.
                        if (draftOrderId && status === 'confirmed') {
                            window.location.href = onHoldListUrl;
                        } else {
                            window.location.reload();
                        }
                    };

                    Swal.fire({ icon: 'success', title: title, text: response.message || '' }).then(finishPlacement);
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: response.message });
                }
            },
            error: function (xhr) {
                const data = xhr.responseJSON || {};
                Swal.fire({ icon: 'error', title: 'Error', text: data.message || data.error || 'Failed to save invoice.' });
            },
            complete: function () {
                $submitBtn.prop('disabled', false).html(originalBtnHtml);
            }
        });
    }

    function checkCustomerCreditBeforeConfirm() {
        const customerId = $('#customer-select').val();
        const dueAmount = computeInvoiceTotal();
        const blockedFlag = $('#credit_limit_blocked_flag').length && $('#credit_limit_blocked_flag').val() === '1';

        if (blockedFlag && canOverrideCredit) {
            return $.Deferred().resolve({ exceeded: false }).promise();
        }

        return $.ajax({
            url: creditCheckUrl,
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                customer_id: customerId,
                due_amount: dueAmount,
                order_id: draftOrderId || ''
            }
        });
    }

    $('#placeOrder').on('click', function () {
        const $submitBtn = $(this);
        const originalBtnHtml = $submitBtn.html();
        const invoiceTypeId = Number($('#invoice_type').val() || 0);
        const isCreditSale = invoiceTypeId === CREDIT_SALE_TYPE_ID;
        const isCashSale = invoiceTypeId === 1;

        if (!validateInvoiceForm(isCreditSale, isCashSale)) {
            return;
        }

        const proceed = function () {
            submitInvoiceWithStatus('confirmed', $submitBtn, originalBtnHtml);
        };

        if (!isCreditSale) {
            proceed();
            return;
        }

        $submitBtn.prop('disabled', true);
        checkCustomerCreditBeforeConfirm()
            .done(function (res) {
                if (res.exceeded) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Credit limit exceeded',
                        text: res.message || 'Outstanding due plus this invoice exceeds the credit limit. Use Place Invoice on Hold only.',
                        confirmButtonText: 'OK'
                    });
                    return;
                }
                proceed();
            })
            .fail(function () {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Could not verify credit limit. Please try again.' });
            })
            .always(function () {
                $submitBtn.prop('disabled', false).html(originalBtnHtml);
            });
    });

    $('#placeOnHold').on('click', function () {
        const $submitBtn = $('#placeOrder');
        const originalBtnHtml = $submitBtn.html();
        const invoiceTypeId = Number($('#invoice_type').val() || 0);
        const isCreditSale = invoiceTypeId === CREDIT_SALE_TYPE_ID;
        const isCashSale = invoiceTypeId === 1;

        if (!validateInvoiceForm(isCreditSale, isCashSale)) {
            return;
        }

        submitInvoiceWithStatus('draft', $submitBtn, originalBtnHtml);
    });

    $('#transferForm').on('submit', function (e) {
        e.preventDefault();
    });

    $('#cancelDraftInvoice').on('click', function () {
        if (!draftOrderId || !cancelUrl) return;
        Swal.fire({
            title: 'Cancel invoice?',
            text: 'This draft will be cancelled and cannot be reopened.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Cancel invoice',
            confirmButtonColor: '#d33'
        }).then(function (result) {
            if (!result.isConfirmed) return;
            $.post(cancelUrl, { _token: "{{ csrf_token() }}" })
                .done(function (res) {
                    if (res.success) {
                        Swal.fire({ icon: 'success', title: 'Cancelled', text: res.message }).then(function () {
                            window.location.href = onHoldListUrl;
                        });
                    }
                })
                .fail(function (xhr) {
                    Swal.fire({ icon: 'error', title: 'Error', text: (xhr.responseJSON || {}).message || 'Could not cancel.' });
                });
        });
    });
})();
</script>
