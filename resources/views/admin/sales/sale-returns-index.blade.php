@extends('layouts.app_admin')
@section('title') Sale returns @endsection
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header d-flex justify-content-between flex-wrap">
            <div class="page-title"><h4>Sale returns</h4></div>
            <a href="{{ route('admin.sale-returns.create') }}" class="btn btn-primary">New sale return</a>
        </div>
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        <div class="card"><div class="card-body table-responsive">
            @include('admin.purchases.partials.list-pagination', ['paginator' => $returns, 'showLinks' => false, 'showSummary' => false, 'containerClass' => 'mb-3'])
            <table class="table table-striped">
                <thead>
                    <tr><th>Return #</th><th>Date</th><th>Customer</th><th>Store</th><th>Invoice</th><th>Amount</th><th>Settlement</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @forelse($returns as $return)
                        <tr>
                            <td>{{ $return->return_number }}</td>
                            <td>{{ format_date($return->return_date) }}</td>
                            <td>{{ $return->customer->customer_name ?? '—' }}</td>
                            <td>{{ $return->store->name ?? '—' }}</td>
                            <td>{{ $return->order?->invoice_number ?: ($return->order_id ? 'INV/' . $return->order_id : '—') }}</td>
                            <td>{{ number_format((float) $return->total_amount, 2) }}</td>
                            <td>{{ $return->settlement_method === 'cash_refund' ? 'Cash refund' : 'Customer balance' }}</td>
                            <td>{{ ucfirst($return->status) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8">No sale returns recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @include('admin.purchases.partials.list-pagination', ['paginator' => $returns, 'showPerPage' => false])
        </div></div>
    </div>
</div>
@endsection
