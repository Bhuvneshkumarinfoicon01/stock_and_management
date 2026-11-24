@extends('layouts.app_admin')
@section('title') Supplier payments @endsection
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header d-flex justify-content-between flex-wrap">
            <div class="page-title"><h4>Supplier payments</h4></div>
            <a href="{{ route('admin.supplier-payments.create') }}" class="btn btn-primary">Record payment</a>
        </div>
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        <div class="card"><div class="card-body table-responsive">
            <table class="table table-striped">
                <thead><tr><th>Date</th><th>Supplier</th><th>Amount</th><th>Method</th><th>Invoice</th><th>Reference</th></tr></thead>
                <tbody>
                    @forelse($payments as $p)
                        <tr>
                            <td>{{ format_date($p->payment_date) }}</td>
                            <td>{{ $p->supplier->company_name ?? '—' }}</td>
                            <td>{{ number_format((float) $p->amount, 2) }}</td>
                            <td>{{ $p->payment_method ?? '—' }}</td>
                            <td>{{ $p->purchaseInvoice?->internal_invoice_number ?: ($p->purchaseInvoice?->invoice_number ?? '—') }}</td>
                            <td>{{ $p->reference_no ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No payments recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $payments->links() }}
        </div></div>
    </div>
</div>
@endsection
