@extends('layouts.app_admin')
@section('title') Supplier due report @endsection
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header"><div class="page-title"><h4>Supplier due report</h4><h6>Suppliers with open purchase invoice balances</h6></div></div>
        <div class="card"><div class="card-body table-responsive">
            <table class="table table-striped">
                <thead><tr><th>Code</th><th>Supplier</th><th class="text-end">Balance due</th></tr></thead>
                <tbody>
                    @forelse($rows as $r)
                        <tr>
                            <td>{{ $r->code ?? '—' }}</td>
                            <td>{{ $r->company_name }}</td>
                            <td class="text-end">{{ number_format((float) $r->balance_due, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted">No outstanding supplier balances.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div></div>
    </div>
</div>
@endsection
