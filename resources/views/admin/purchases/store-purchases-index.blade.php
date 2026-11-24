@extends('layouts.app_admin')
@section('title') Store purchases @endsection
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header d-flex justify-content-between flex-wrap">
            <div class="page-title"><h4>Add purchase (list)</h4><h6>Recorded store purchases</h6></div>
            <a href="{{ route('admin.store-purchases.create') }}" class="btn btn-primary">Add purchase</a>
        </div>
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        <div class="card"><div class="card-body table-responsive">
            @include('admin.purchases.partials.list-pagination', ['paginator' => $purchases, 'showLinks' => false, 'showSummary' => false, 'containerClass' => 'mb-3'])
            <table class="table table-striped">
                <thead><tr><th>#</th><th>Date</th><th>Supplier</th><th>Total</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse($purchases as $p)
                        <tr>
                            <td><a href="{{ route('admin.store-purchases.show', $p) }}">{{ $p->purchase_number }}</a></td>
                            <td>{{ format_date($p->purchase_date) }}</td>
                            <td>{{ $p->supplier->company_name ?? '—' }}</td>
                            <td>{{ number_format((float)$p->total, 2) }}</td>
                            <td>{{ $p->status }}</td>
                            <td>
                                <a href="{{ route('admin.store-purchases.show', $p) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No purchases recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @include('admin.purchases.partials.list-pagination', ['paginator' => $purchases, 'showPerPage' => false])
        </div></div>
    </div>
</div>
@endsection
