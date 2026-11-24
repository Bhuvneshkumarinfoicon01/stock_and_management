@extends('layouts.app_admin')
@section('title') {{ $holdInvoice ? 'Continue purchase invoice' : 'New purchase invoice' }} @endsection
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header d-flex justify-content-between flex-wrap">
            <div class="page-title">
                <h4>{{ $holdInvoice ? 'Continue purchase invoice on hold' : 'New supplier purchase invoice' }}</h4>
                {{-- <h6>Use <strong>Place Order</strong> to confirm (internal number is auto-generated; supplier invoice number is optional), or <strong>Place Invoice on Hold</strong> to save as draft.</h6> --}}
                <h6>Use <strong>Confirm Invoice</strong> to confirm (internal number is auto-generated; supplier invoice number is optional), or <strong>Place Invoice on Hold</strong> to save as draft.</h6>
            </div>
            <a href="{{ $holdInvoice ? route('admin.purchase-invoices-on-hold') : route('admin.supplier-purchase-invoices.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
        @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif
        @if($holdInvoice)
        <div class="alert alert-warning">
            Editing draft <strong>{{ $holdInvoice->draft_reference ?? ('#' . $holdInvoice->id) }}</strong>
            @if($holdInvoice->credit_limit_blocked) — held because supplier credit limit was exceeded. @endif
        </div>
        @endif
        <div class="card"><div class="card-body">
            <form method="post"
                action="{{ $holdInvoice ? route('admin.supplier-purchase-invoices.update', $holdInvoice) : route('admin.supplier-purchase-invoices.store') }}"
                id="pinv-form">
                @csrf
                @if($holdInvoice) @method('PUT') @endif
                <input type="hidden" name="invoice_status" id="pinv_invoice_status" value="Confirmed">
                @if($holdInvoice && $holdInvoice->credit_limit_blocked)
                <input type="hidden" name="override_credit_limit" id="pinv_override_credit" value="0">
                @endif
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Purchase Invoice #</label>
                        <input type="text" id="pinv_internal_invoice_number" class="form-control bg-light"
                            value="{{ old('internal_invoice_number', $holdInvoice->internal_invoice_number ?? '') }}"
                            placeholder="Assigned automatically on confirm" readonly>
                        <small class="text-muted">Sequential number from the store purchase-invoice sequence.</small>
                    </div>
                    <div class="col-md-4" id="pinv-supplier-number-wrap">
                        <label class="form-label">Supplier invoice number <span class="text-muted small">(optional)</span></label>
                        <input name="supplier_invoice_number" id="pinv_supplier_invoice_number" class="form-control"
                            value="{{ old('supplier_invoice_number', $holdInvoice->invoice_number ?? '') }}">
                        <small class="text-muted">The supplier's own invoice reference — can be any value.</small>
                    </div>
                    <div class="col-md-4"><label class="form-label">Supplier *</label>
                        <select name="supplier_id" id="pinv_supplier_id" class="form-select" required><option value="">Choose supplier</option>@foreach($suppliers as $s)<option value="{{ $s->id }}" @selected(old('supplier_id', $holdInvoice->supplier_id ?? null)==$s->id)>{{ $s->company_name }}</option>@endforeach</select></div>
                    <div class="col-md-4"><label class="form-label">Store *</label>
                        <select name="store_id" id="purchase_store_id" class="form-select" required><option value="">Choose store</option>@foreach($stores as $st)<option value="{{ $st->id }}" @selected(old('store_id', $holdInvoice->store_id ?? $selectedStoreId)==$st->id)>{{ $st->name }}</option>@endforeach</select></div>
                    <div class="col-md-4">
                        <label class="form-label">Purchase order</label>
                        <select name="purchase_order_id" id="pinv_purchase_order_id" class="form-select">
                            <option value="">—</option>
                            @foreach($purchaseOrders as $po)
                                <option value="{{ $po->id }}" @selected(old('purchase_order_id', $selectedPurchaseOrderId ?? $holdInvoice->purchase_order_id ?? null)==$po->id)>
                                    {{ $po->po_number }} — ({{ $po->supplier->company_name ?? '' }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Optional — populate lines from an existing purchase order.</small>
                    </div>
                    <div class="col-md-4"><label class="form-label">Invoice date *</label><input type="date" name="invoice_date" class="form-control" required value="{{ old('invoice_date', optional($holdInvoice?->invoice_date)->toDateString() ?? now()->toDateString()) }}"></div>
                    <div class="col-md-4"><label class="form-label">Due date</label><input type="date" name="due_date" class="form-control" value="{{ old('due_date', optional($holdInvoice?->due_date)->toDateString()) }}"></div>
                    <div class="col-12"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2">{{ old('notes', $holdInvoice->notes ?? '') }}</textarea></div>
                </div>
                @include('admin.purchases.partials.product-lines-picker', [
                    'pickerPrefix' => 'pinv',
                    'perLineTax' => true,
                    'taxExemptDefault' => (bool) old('tax_exempt', $holdInvoice->tax_exempt ?? false),
                ])
                <div class="d-flex justify-content-center gap-2 mt-4" style="max-width: 480px;">
                    {{-- <button type="button" class="btn btn-secondary btn-lg" id="pinv-place-order">Place Order</button> --}}
                    <button type="button" class="btn btn-secondary btn-lg" id="pinv-place-order">Confirm Invoice</button>
                    <button type="button" class="btn btn-outline-primary btn-lg" id="pinv-place-hold">Place Invoice on Hold</button>
                    @if($holdInvoice)
                    <button type="button" class="btn btn-outline-danger" id="pinv-cancel-draft">Cancel Invoice</button>
                    @endif
                </div>
            </form>
        </div></div>
    </div>
</div>

@include('admin.purchases.partials.purchase-invoice-print-modal')
@endsection
@section('scripts')
<script>
window.PerLineTaxConfig = {
    taxExemptSelector: '#pinv_tax_exempt',
    tbodySelector: '#pinv_product_tbody',
    subTotalSelector: '#pinv_sub_total',
    taxSelector: '#pinv_tax',
    taxLabelSelector: '#pinv_tax_label',
    totalSelector: '#pinv_total',
    discountSelector: '#pinv_discount_hidden',
    discountAmountSelector: '#pinv_discount',
    discountLabelSelector: '#pinv_discount_label',
};
</script>
@include('admin.partials.invoice-per-line-tax-scripts')
@include('admin.purchases.partials.product-lines-picker-scripts', [
    'pickerPrefix' => 'pinv',
    'storeSelectId' => 'purchase_store_id',
    'formId' => 'pinv-form',
    'submitBtnId' => 'pinv-place-order',
    'emptyMessage' => 'Add at least one product before saving.',
    'perLineTax' => true,
    'purchaseOrderSelectId' => 'pinv_purchase_order_id',
    'supplierSelectId' => 'pinv_supplier_id',
    'purchaseOrderItemsUrl' => route('admin.purchase-orders.items', ['purchaseOrder' => '__PO__']),
])
@include('admin.purchases.partials.purchase-invoice-print-scripts')
<script>
$(function () {
    const holdInvoiceId = @json($holdInvoice->id ?? null);
    const cancelUrl = holdInvoiceId ? @json($holdInvoice ? route('admin.purchase-invoices.cancel', $holdInvoice->id) : null) : null;
    const onHoldListUrl = @json(route('admin.purchase-invoices-on-hold'));
    const supplierCreditCheckUrl = @json(route('admin.check-supplier-credit-limit'));
    const canOverrideCredit = @json(app(\App\Services\CreditLimitService::class)->canOverrideCreditLimit());

    function syncPinvMode(onHold) {
        $('#pinv_invoice_status').val(onHold ? 'Draft' : 'Confirmed');
        const $supplierNum = $('#pinv_supplier_invoice_number');
        if (onHold) {
            $('#pinv_internal_invoice_number').val('').attr('placeholder', 'Assigned when confirmed');
            $supplierNum.prop('required', false);
            $('#pinv-supplier-number-wrap').removeClass('opacity-50');
        } else {
            $('#pinv_internal_invoice_number').attr('placeholder', 'Assigned automatically on confirm');
            $supplierNum.prop('required', false);
            $('#pinv-supplier-number-wrap').removeClass('opacity-50');
        }
    }

    function computePurchaseTotal() {
        if (window.InvoicePerLineTax) {
            return window.InvoicePerLineTax.calculateTotals({ tbodySelector: '#pinv_product_tbody' }).total;
        }
        let subtotal = 0;
        $('#pinv_product_tbody tr').each(function () {
            const qty = parseFloat($(this).find('input[name="qty[]"]').val()) || 0;
            const price = parseFloat($(this).find('input[name="price[]"]').val()) || 0;
            subtotal += qty * price;
        });
        return subtotal;
    }

    function validatePurchaseForm(requireInvoiceNumber) {
        const supplierId = $('select[name="supplier_id"]').val();
        const storeId = $('#purchase_store_id').val();
        if (!supplierId) {
            Swal.fire({ icon: 'warning', title: 'Supplier required', text: 'Choose a supplier.' });
            return false;
        }
        if (!storeId) {
            Swal.fire({ icon: 'warning', title: 'Store required', text: 'Choose a store.' });
            return false;
        }
        if ($('#pinv_product_tbody tr').length === 0) {
            Swal.fire({ icon: 'warning', title: 'No products', text: 'Add at least one product.' });
            return false;
        }
        return true;
    }

    const purchaseInvoiceListUrl = @json(route('admin.supplier-purchase-invoices.index'));

    function resetPinvSubmitButtons() {
        // $('#pinv-place-order').prop('disabled', false).text('Place Order');
        $('#pinv-place-order').prop('disabled', false).text('Confirm Invoice');
        $('#pinv-place-hold').prop('disabled', false).text('Place Invoice on Hold');
    }

    function submitPurchaseFormAjax() {
        const $form = $('#pinv-form');
        const onHold = $('#pinv_invoice_status').val() === 'Draft';
        const method = ($form.find('input[name="_method"]').val() || 'POST').toUpperCase();

        $('#pinv-place-order, #pinv-place-hold').prop('disabled', true);

        $.ajax({
            url: $form.attr('action'),
            method: method === 'PUT' ? 'POST' : 'POST',
            data: $form.serialize(),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            success: function (response) {
                if (!response.success) {
                    Swal.fire({ icon: 'error', title: 'Error', text: response.message || 'Could not save invoice.' });
                    return;
                }

                const title = onHold ? 'Saved on hold' : 'Purchase invoice saved';
                Swal.fire({ icon: 'success', title: title, text: response.message || '' }).then(function () {
                    if (!onHold && response.open_print && response.invoice_id && typeof window.openPurchaseInvoicePrint === 'function') {
                        window._redirectAfterPurchasePrint = response.redirect_url || purchaseInvoiceListUrl;
                        window.openPurchaseInvoicePrint(response.invoice_id);
                        return;
                    }
                    window.location.href = response.redirect_url || (onHold ? onHoldListUrl : purchaseInvoiceListUrl);
                });
            },
            error: function (xhr) {
                const data = xhr.responseJSON || {};
                let text = data.message || 'Could not save purchase invoice.';
                if (data.errors) {
                    const firstKey = Object.keys(data.errors)[0];
                    if (firstKey && data.errors[firstKey][0]) {
                        text = data.errors[firstKey][0];
                    }
                }
                Swal.fire({ icon: 'error', title: 'Error', text: text });
            },
            complete: function () {
                resetPinvSubmitButtons();
            }
        });
    }

    function submitPurchaseForm() {
        submitPurchaseFormAjax();
    }

    $('#pinv-place-hold').on('click', function () {
        if (!validatePurchaseForm(false)) return;
        syncPinvMode(true);
        submitPurchaseForm();
    });

    $('#pinv-place-order').on('click', function () {
        if (!validatePurchaseForm(true)) return;
        syncPinvMode(false);

        const supplierId = $('select[name="supplier_id"]').val();
        const totalAmount = computePurchaseTotal();
        const blockedFlag = $('#pinv_override_credit').length && holdInvoiceId;

        if (blockedFlag && canOverrideCredit) {
            $('#pinv_override_credit').val('1');
            submitPurchaseForm();
            return;
        }

        const $btn = $(this).prop('disabled', true);
        $.ajax({
            url: supplierCreditCheckUrl,
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                supplier_id: supplierId,
                total_amount: totalAmount,
                invoice_id: holdInvoiceId || ''
            }
        }).done(function (res) {
            if (res.exceeded) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Credit limit exceeded',
                    text: res.message || 'Outstanding due plus this invoice exceeds the supplier credit limit. Use Place Invoice on Hold only.',
                    confirmButtonText: 'OK'
                });
                return;
            }
            submitPurchaseForm();
        }).fail(function () {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Could not verify credit limit.' });
        }).always(function () {
            $btn.prop('disabled', false);
        });
    });

    // Old: native form POST (full page redirect).
  // $('#pinv-form').on('submit', function (e) {
  //     if ($('#pinv_product_tbody tr').length === 0) {
  //         e.preventDefault();
  //         return;
  //     }
  // });

    $('#pinv-form').on('submit', function (e) {
        e.preventDefault();
    });

    const holdInvoicePayload = @json($holdInvoicePayload);
    if (holdInvoicePayload && typeof window.pinvAddProductRow === 'function') {
        function applyHoldPurchasePayload() {
            $('select[name="supplier_id"]').val(String(holdInvoicePayload.supplier_id)).trigger('change');
            $('#purchase_store_id').val(String(holdInvoicePayload.store_id)).trigger('change');
            if (holdInvoicePayload.invoice_date) {
                $('input[name="invoice_date"]').val(holdInvoicePayload.invoice_date);
            }
            if (holdInvoicePayload.due_date) {
                $('input[name="due_date"]').val(holdInvoicePayload.due_date);
            }
            $('#pinv_tax_exempt').prop('checked', !!holdInvoicePayload.tax_exempt);
            $('textarea[name="notes"]').val(holdInvoicePayload.notes || '');
            if (holdInvoicePayload.internal_invoice_number) {
                $('#pinv_internal_invoice_number').val(holdInvoicePayload.internal_invoice_number);
            }
            if (holdInvoicePayload.supplier_invoice_number) {
                $('#pinv_supplier_invoice_number').val(holdInvoicePayload.supplier_invoice_number);
            }
            $('#pinv_product_tbody').empty();
            (holdInvoicePayload.lines || []).forEach(function (line) {
                window.pinvAddProductRow(
                    line.product_id,
                    line.product_name,
                    line.sku || '',
                    line.category_name || '',
                    line.category_id || '',
                    line.unit_price,
                    line.qty,
                    true,
                    line.is_taxable,
                    line.tax_rate
                );
                const $lastRow = $('#pinv_product_tbody tr').last();
                if (window.InvoicePerLineTax && $lastRow.length) {
                    window.InvoicePerLineTax.setRowDefaultTax(
                        $lastRow,
                        line.default_is_taxable != null ? line.default_is_taxable : line.is_taxable,
                        line.default_tax_rate != null ? line.default_tax_rate : line.tax_rate
                    );
                }
            });
            if (window.InvoicePerLineTax) {
                window.InvoicePerLineTax.syncAllLineTaxUi({ tbodySelector: '#pinv_product_tbody' });
                window.InvoicePerLineTax.calculateTotals({ tbodySelector: '#pinv_product_tbody' });
            }
        }
        applyHoldPurchasePayload();
        setTimeout(applyHoldPurchasePayload, 300);
    }

    $('#pinv-cancel-draft').on('click', function () {
        if (!cancelUrl) return;
        Swal.fire({
            title: 'Cancel invoice?',
            text: 'This draft will be cancelled.',
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
});
</script>
@endsection
