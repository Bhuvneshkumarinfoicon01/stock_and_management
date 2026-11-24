@extends('layouts.app_admin')
@section('title') {{ $holdBatch ? 'Continue batch purchase' : 'New batch purchase' }} @endsection
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header d-flex justify-content-between flex-wrap">
            <div class="page-title">
                <h4>{{ $holdBatch ? 'Continue batch purchase on hold' : 'New batch purchase' }}</h4>
                <h6>Group product invoices and allocate additional charge invoices into one batch. Stock is updated on confirm using the <strong>Final Unit</strong> cost.</h6>
            </div>
            <a href="{{ $holdBatch ? route('admin.batch-purchases-on-hold') : route('admin.batch-purchases.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
        @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif
        @if($holdBatch)
        <div class="alert alert-warning">Editing draft <strong>{{ $holdBatch->draft_reference ?? $holdBatch->batch_number }}</strong></div>
        @endif

        <div class="card"><div class="card-body">
            <form method="post"
                action="{{ $holdBatch ? route('admin.batch-purchases.update', $holdBatch) : route('admin.batch-purchases.store') }}"
                id="batch-purchase-form">
                @csrf
                @if($holdBatch) @method('PUT') @endif
                <input type="hidden" name="batch_status" id="batch_status" value="confirmed">

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Batch purchase #</label>
                        <input type="text" class="form-control bg-light" readonly
                            value="{{ $holdBatch->batch_number ?? '' }}" placeholder="Assigned automatically on confirm">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Store *</label>
                        <select name="store_id" id="batch_store_id" class="form-select" required>
                            <option value="">Choose store</option>
                            @foreach($stores as $st)
                                <option value="{{ $st->id }}" @selected(old('store_id', $holdBatch->store_id ?? $selectedStoreId)==$st->id)>{{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date *</label>
                        <input type="date" name="batch_date" class="form-control" required
                            value="{{ old('batch_date', optional($holdBatch?->batch_date)->toDateString() ?? now()->toDateString()) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes', $holdBatch->notes ?? '') }}</textarea>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-lg-6">
                        <label class="form-label">Product invoices</label>
                        <input type="text" id="batch_product_invoice_search" class="form-control mb-2" placeholder="Search by supplier name or invoice number" autocomplete="off">
                        <ul id="batch_product_invoice_results" class="list-group mb-2" style="max-height: 160px; overflow-y: auto; display: none;"></ul>
                        <div id="batch_product_invoice_selected" class="d-flex flex-wrap gap-2"></div>
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label">Additional charge invoices</label>
                        <input type="text" id="batch_charge_invoice_search" class="form-control mb-2" placeholder="Search by supplier name or invoice number" autocomplete="off">
                        <ul id="batch_charge_invoice_results" class="list-group mb-2" style="max-height: 160px; overflow-y: auto; display: none;"></ul>
                        <div id="batch_charge_invoice_selected" class="d-flex flex-wrap gap-2"></div>
                        <div class="mt-2 small text-muted">
                            Additional charge invoices total (excl. tax):
                            <strong id="batch_charge_invoices_total">$0.00</strong>
                        </div>
                        <div class="small text-muted">
                            Allocated in table:
                            <strong id="batch_allocated_charges_total">$0.00</strong>
                            <span id="batch_charges_balance_wrap" class="ms-2"></span>
                        </div>
                    </div>
                </div>

                <div class="col-12 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="tax_exempt" id="batch_tax_exempt" value="1"
                            @checked(old('tax_exempt', $holdBatch->tax_exempt ?? false))>
                        <label class="form-check-label" for="batch_tax_exempt">Entire batch is non-taxable (tax-exempt)</label>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Category</th>
                                <th>Qty</th>
                                <th>Unit price</th>
                                <th>Amount</th>
                                <th>Additional charges</th>
                                <th>Grand total</th>
                                <th>Final unit</th>
                                <th>Taxable</th>
                                <th>Rate %</th>
                                <th>Line tax</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="batch_product_tbody"></tbody>
                    </table>
                </div>

                <div class="row mt-3">
                    <div class="col-lg-6 offset-lg-6">
                        <table class="table table-borderless mb-0">
                            <tr><td>Sub total</td><td class="text-end" id="batch_sub_total">$0.00</td></tr>
                            <tr><td id="batch_tax_label">Tax</td><td class="text-end" id="batch_tax">$0.00</td></tr>
                            {{-- <tr><td><strong>Total</strong></td><td class="text-end"><strong id="batch_total">$0.00</strong></td></tr> --}}
                            <tr><td><strong>Sum of grand totals</strong></td><td class="text-end"><strong id="batch_grand_totals_sum">$0.00</strong></td></tr>
                            <tr><td><strong>Total</strong></td><td class="text-end"><strong id="batch_total">$0.00</strong></td></tr>
                        </table>
                    </div>
                </div>

                <div class="d-flex justify-content-center gap-2 mt-4">
                    <button type="button" class="btn btn-primary btn-lg" id="batch-confirm-btn">Confirm Batch Purchase</button>
                    <button type="button" class="btn btn-outline-secondary btn-lg" id="batch-hold-btn">Place Batch Purchase on Hold</button>
                </div>
            </form>
        </div></div>
    </div>
</div>
@endsection

@section('scripts')
<script>
window.PerLineTaxConfig = {
    taxExemptSelector: '#batch_tax_exempt',
    tbodySelector: '#batch_product_tbody',
    subTotalSelector: '#batch_sub_total',
    taxSelector: '#batch_tax',
    taxLabelSelector: '#batch_tax_label',
    totalSelector: '#batch_total',
    amountBaseSelector: '.batch-line-amount',
};
</script>
@include('admin.partials.invoice-per-line-tax-scripts')
@include('admin.purchases.partials.batch-purchase-scripts', [
    'holdBatchPayload' => $holdBatchPayload ?? null,
])
@endsection
