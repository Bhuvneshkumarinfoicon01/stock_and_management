@extends('layouts.app_admin')
@section('title') Purchase invoices on hold @endsection
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header d-flex justify-content-between flex-wrap">
            <div class="page-title">
                <h4>Purchase invoices on hold</h4>
                {{-- <h6>Click <strong>Continue</strong> to open a draft, then use Place Order or Place Invoice on Hold on the form.</h6> --}}
                <h6>Click <strong>Continue</strong> to open a draft, then use Confirm Invoice or Place Invoice on Hold on the form.</h6>
            </div>
            <a href="{{ route('admin.supplier-purchase-invoices.create') }}" class="btn btn-primary">New purchase invoice</a>
        </div>
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        <div class="card"><div class="card-body table-responsive">
            @include('admin.purchases.partials.list-pagination', ['paginator' => $invoices, 'showLinks' => false, 'showSummary' => false, 'containerClass' => 'mb-3'])
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Draft ref</th>
                        <th>Date</th>
                        <th>Supplier</th>
                        <th>Total</th>
                        <th>Due</th>
                        <th>Credit limit</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $inv)
                        <tr>
                            <td>{{ $inv->draft_reference ?: ('#' . $inv->id) }}</td>
                            <td>{{ format_date($inv->invoice_date) }}</td>
                            <td>{{ $inv->supplier->company_name ?? '—' }}</td>
                            <td>{{ number_format((float) $inv->total, 2) }}</td>
                            <td>{{ number_format((float) $inv->due_amount, 2) }}</td>
                            <td>
                                @if((bool) $inv->credit_limit_blocked)
                                    <span class="badge bg-warning text-dark">Over credit limit</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.supplier-purchase-invoices.show', $inv) }}" class="btn btn-sm btn-outline-secondary me-1">View</a>
                                <a href="{{ route('admin.supplier-purchase-invoices.create', ['hold' => $inv->id]) }}" class="btn btn-sm btn-primary">Continue</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7">No purchase invoices on hold.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @include('admin.purchases.partials.list-pagination', ['paginator' => $invoices, 'showPerPage' => false])
        </div></div>
    </div>
</div>
@endsection
