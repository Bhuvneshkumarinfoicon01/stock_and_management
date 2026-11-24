@extends('layouts.app_admin')
@section('title') Supplier ledger @endsection
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header"><div class="page-title"><h4>Supplier ledger</h4><h6>Invoices increase balance owed; payments reduce it.</h6></div></div>
        <div class="card mb-3"><div class="card-body">
            <form method="get" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label class="form-label">Supplier</label>
                    <select name="supplier_id" class="form-select" onchange="this.form.submit()">
                        <option value="">— Select supplier —</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}" @selected($supplierId == $s->id)>{{ $s->code }} — {{ $s->company_name }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div></div>
        @if($supplier)
            <div class="card"><div class="card-body table-responsive p-0">
                <table class="table table-striped mb-0">
                    <thead><tr><th>Date</th><th>Type</th><th>Reference</th><th class="text-end">Debit</th><th class="text-end">Credit</th><th class="text-end">Balance</th></tr></thead>
                    <tbody>
                        @forelse($transactions as $t)
                            <tr>
                                <td>{{ format_date($t['date']) }}</td>
                                <td>{{ $t['type'] }}</td>
                                <td>{{ $t['reference'] }}</td>
                                <td class="text-end">{{ number_format($t['debit'], 2) }}</td>
                                <td class="text-end">{{ number_format($t['credit'], 2) }}</td>
                                <td class="text-end">{{ number_format($t['balance'], 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">No transactions.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div></div>
        @endif
    </div>
</div>
@endsection
