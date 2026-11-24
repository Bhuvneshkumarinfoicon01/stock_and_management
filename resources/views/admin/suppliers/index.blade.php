@extends('layouts.app_admin')
@section('title') Suppliers @endsection
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header d-flex justify-content-between flex-wrap">
            <div class="page-title"><h4>Supplier list</h4><h6>Active suppliers for purchases and payables</h6></div>
            <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary">Add supplier</a>
        </div>
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        <div class="card">
            <div class="card-body table-responsive">
                @include('admin.purchases.partials.list-pagination', ['paginator' => $suppliers, 'showLinks' => false, 'showSummary' => false, 'containerClass' => 'mb-3'])
                <table class="table table-striped">
                    <thead><tr><th>Code</th><th>Company</th><th>Contact</th><th>Phone</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @forelse($suppliers as $s)
                            <tr>
                                <td>{{ $s->code ?? '—' }}</td>
                                <td>{{ $s->company_name }}</td>
                                <td>{{ $s->contact_name ?? '—' }}</td>
                                <td>{{ $s->phone ?? '—' }}</td>
                                <td>{{ $s->status ? 'Active' : 'Inactive' }}</td>
                                <td><a href="{{ route('admin.suppliers.edit', $s) }}" class="btn btn-sm btn-outline-primary">Edit</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="6">No suppliers yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                @include('admin.purchases.partials.list-pagination', ['paginator' => $suppliers, 'showPerPage' => false])
            </div>
        </div>
    </div>
</div>
@endsection
