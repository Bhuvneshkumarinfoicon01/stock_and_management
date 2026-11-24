@extends('layouts.app_admin')
@section('title') Record supplier payment @endsection
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header d-flex justify-content-between"><div class="page-title"><h4>Record supplier payment</h4></div>
            <a href="{{ route('admin.supplier-payments.index') }}" class="btn btn-light">Back</a></div>
        @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
        <div class="card"><div class="card-body">
            <form method="post" action="{{ route('admin.supplier-payments.store') }}" class="row g-3">
                @csrf
                <div class="col-md-6"><label class="form-label">Supplier *</label>
                    <select name="supplier_id" class="form-select" required>
                        <option value="">—</option>
                        @foreach($suppliers as $s)<option value="{{ $s->id }}" @selected(old('supplier_id')==$s->id)>{{ $s->company_name }}</option>@endforeach
                    </select>
                </div>
                <div class="col-md-6"><label class="form-label">Allocate to invoice (optional)</label>
                    <select name="supplier_purchase_invoice_id" class="form-select">
                        <option value="">— Unallocated —</option>
                        @foreach($openInvoices as $inv)
                            <option value="{{ $inv->id }}" @selected(old('supplier_purchase_invoice_id')==$inv->id)>{{ $inv->internal_invoice_number ?: ($inv->invoice_number ?: ('#' . $inv->id)) }} — due {{ number_format((float)$inv->due_amount,2) }} ({{ $inv->supplier->company_name ?? '' }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3"><label class="form-label">Payment date *</label><input type="date" name="payment_date" class="form-control" required value="{{ old('payment_date', now()->toDateString()) }}"></div>
                <div class="col-md-3"><label class="form-label">Amount *</label><input type="number" step="0.01" min="0.01" name="amount" class="form-control" required value="{{ old('amount') }}"></div>
                <div class="col-md-3"><label class="form-label">Method</label><input name="payment_method" class="form-control" value="{{ old('payment_method') }}"></div>
                <div class="col-md-3"><label class="form-label">Reference</label><input name="reference_no" class="form-control" value="{{ old('reference_no') }}"></div>
                <div class="col-12"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea></div>
                <div class="col-12"><button class="btn btn-primary">Save payment</button></div>
            </form>
        </div></div>
    </div>
</div>
@endsection
