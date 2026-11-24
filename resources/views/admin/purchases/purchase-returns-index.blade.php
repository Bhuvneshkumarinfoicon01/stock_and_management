@extends('layouts.app_admin')
@section('title') Purchase returns @endsection
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header d-flex justify-content-between flex-wrap">
            <div class="page-title"><h4>Purchase returns</h4></div>
            <a href="{{ route('admin.purchase-returns.create') }}" class="btn btn-primary">Purchase return</a>
        </div>
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        <div class="card"><div class="card-body table-responsive">
            @include('admin.purchases.partials.list-pagination', ['paginator' => $returns, 'showLinks' => false, 'showSummary' => false, 'containerClass' => 'mb-3'])
            <table class="table table-striped">
                <thead><tr><th>Return #</th><th>Date</th><th>Supplier</th><th>Amount</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($returns as $r)
                        <tr>
                            <td>{{ $r->return_number }}</td>
                            <td>{{ format_date($r->return_date) }}</td>
                            <td>{{ $r->supplier->company_name ?? '—' }}</td>
                            <td>{{ number_format((float)$r->total_amount, 2) }}</td>
                            <td>{{ $r->status }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5">No purchase returns.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @include('admin.purchases.partials.list-pagination', ['paginator' => $returns, 'showPerPage' => false])
        </div></div>
    </div>
</div>
@endsection
