@extends('layouts.app_admin')
@section('title') New GRN @endsection
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header d-flex justify-content-between flex-wrap">
            <div class="page-title">
                <h4>New goods receipt note</h4>
                <h6>Populate from a purchase invoice (direct) or a purchase order (requires a linked confirmed invoice). Manual entry is also supported.</h6>
            </div>
            <a href="{{ route('admin.goods-receipt-notes.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
        @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
        <div class="alert alert-info py-2">
            <strong>Note:</strong> When receiving against a purchase order, a confirmed purchase invoice must be assigned. Any differences between the PO and invoice will be highlighted — goods are received per the <strong>purchase invoice</strong>.
        </div>
        <div class="card"><div class="card-body">
            <form method="post" action="{{ route('admin.goods-receipt-notes.store') }}" id="grn-form">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-md-4"><label class="form-label">Supplier *</label>
                        <select name="supplier_id" id="grn_supplier_id" class="form-select" required><option value="">Choose supplier</option>@foreach($suppliers as $s)<option value="{{ $s->id }}" @selected(old('supplier_id')==$s->id)>{{ $s->company_name }}</option>@endforeach</select></div>
                    <div class="col-md-4"><label class="form-label">Store *</label>
                        <select name="store_id" id="purchase_store_id" class="form-select" required><option value="">Choose store</option>@foreach($stores as $st)<option value="{{ $st->id }}" @selected(old('store_id', $selectedStoreId)==$st->id)>{{ $st->name }}</option>@endforeach</select></div>
                    <div class="col-md-4">
                        <label class="form-label">Purchase order</label>
                        <select name="purchase_order_id" id="grn_purchase_order_id" class="form-select">
                            <option value="">—</option>
                            @foreach($purchaseOrders as $po)
                                <option value="{{ $po->id }}" @selected(old('purchase_order_id', $selectedPurchaseOrderId)==$po->id)>{{ $po->po_number }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Optional — requires a linked purchase invoice.</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Purchase invoice</label>
                        <select name="supplier_purchase_invoice_id" id="grn_purchase_invoice_id" class="form-select">
                            <option value="">—</option>
                            @foreach($purchaseInvoices as $inv)
                                <option value="{{ $inv->id }}" @selected(old('supplier_purchase_invoice_id', $selectedPurchaseInvoiceId)==$inv->id)>
                                    {{ $inv->internal_invoice_number ?: ($inv->invoice_number ?: ('#'.$inv->id)) }}
                                    @if($inv->invoice_number && $inv->internal_invoice_number) (Supplier: {{ $inv->invoice_number }}) @endif
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Populate lines from a confirmed invoice (with or without a PO).</small>
                    </div>
                    <div class="col-md-4"><label class="form-label">GRN date *</label><input type="date" name="grn_date" class="form-control" required value="{{ old('grn_date', now()->toDateString()) }}"></div>
                    <div class="col-12"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea></div>
                </div>
                @include('admin.purchases.partials.product-lines-picker', [
                    'pickerPrefix' => 'grn',
                    'priceColumnLabel' => 'Unit cost',
                    'perLineTax' => true,
                    'taxExemptDefault' => (bool) old('tax_exempt', false),
                ])
                <button type="submit" class="btn btn-primary mt-3" id="grn-submit">Save GRN</button>
            </form>
        </div></div>
    </div>
</div>
@endsection
@section('scripts')
<script>
window.PerLineTaxConfig = {
    taxExemptSelector: '#grn_tax_exempt',
    tbodySelector: '#grn_product_tbody',
    subTotalSelector: '#grn_sub_total',
    taxSelector: '#grn_tax',
    taxLabelSelector: '#grn_tax_label',
    totalSelector: '#grn_total',
};
</script>
@include('admin.partials.invoice-per-line-tax-scripts')
@include('admin.purchases.partials.product-lines-picker-scripts', [
    'pickerPrefix' => 'grn',
    'storeSelectId' => 'purchase_store_id',
    'formId' => 'grn-form',
    'submitBtnId' => 'grn-submit',
    'emptyMessage' => 'Add at least one product before saving.',
    'perLineTax' => true,
    'externallyManagedSources' => true,
    // Old: PO-only population via purchaseOrderSelectId.
    // 'purchaseOrderSelectId' => 'grn_purchase_order_id',
    // 'supplierSelectId' => 'grn_supplier_id',
    // 'purchaseOrderItemsUrl' => route('admin.purchase-orders.items', ['purchaseOrder' => '__PO__']),
])
@include('admin.purchases.partials.grn-source-scripts', [
    'pickerPrefix' => 'grn',
    'storeSelectId' => 'purchase_store_id',
    'poSelectId' => 'grn_purchase_order_id',
    'piSelectId' => 'grn_purchase_invoice_id',
    'supplierSelectId' => 'grn_supplier_id',
    'formId' => 'grn-form',
    'submitBtnId' => 'grn-submit',
    'poGrnContextUrl' => route('admin.purchase-orders.grn-context', ['purchaseOrder' => '__PO__']),
    'piItemsUrl' => route('admin.supplier-purchase-invoices.items', ['invoice' => '__INVOICE__']),
    'createInvoiceUrl' => route('admin.supplier-purchase-invoices.create', ['purchase_order_id' => '__PO__']),
    'initialPurchaseOrderId' => old('purchase_order_id', $selectedPurchaseOrderId ?? null),
    'initialPurchaseInvoiceId' => old('supplier_purchase_invoice_id', $selectedPurchaseInvoiceId ?? null),
])
@endsection
