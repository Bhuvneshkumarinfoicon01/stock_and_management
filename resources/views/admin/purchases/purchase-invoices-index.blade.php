@extends('layouts.app_admin')
@section('title') Purchase invoices @endsection
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header d-flex justify-content-between flex-wrap">
            <div class="page-title"><h4>Supplier purchase invoices</h4></div>
            <a href="{{ route('admin.supplier-purchase-invoices.create') }}" class="btn btn-primary">New invoice</a>
        </div>
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        <div class="card"><div class="card-body table-responsive">
            @include('admin.purchases.partials.list-pagination', ['paginator' => $invoices, 'showLinks' => false, 'showSummary' => false, 'containerClass' => 'mb-3'])
            <table class="table table-striped">
                <thead><tr>{{--<th>Internal #</th>--}}<th>Invoice #</th><th>Date</th><th>Supplier</th><th>Total</th><th>Due</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse($invoices as $inv)
                        <tr>
                            {{--<td>{{ $inv->internal_invoice_number ?: '—' }}</td>--}}
                            <td>
                                <a href="{{ route('admin.supplier-purchase-invoices.show', $inv) }}">
                                    {{ $inv->invoice_number ?: ($inv->internal_invoice_number ?: '—') }}
                                </a>
                            </td>
                            <td>{{ format_date($inv->invoice_date) }}</td>
                            <td>{{ $inv->supplier->company_name ?? '—' }}</td>
                            <td>{{ number_format((float)$inv->total, 2) }}</td>
                            <td>{{ number_format((float)$inv->due_amount, 2) }}</td>
                            <td>{{ $inv->status }}</td>
                            <td>
                                <a href="{{ route('admin.supplier-purchase-invoices.show', $inv) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8">No purchase invoices.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @include('admin.purchases.partials.list-pagination', ['paginator' => $invoices, 'showPerPage' => false])
        </div></div>
    </div>
</div>
@endsection
