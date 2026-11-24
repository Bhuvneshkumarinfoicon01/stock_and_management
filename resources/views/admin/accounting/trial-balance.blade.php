@extends('layouts.app_admin')
@section('title') Trial balance @endsection

@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex justify-content-between flex-wrap">
                <div class="page-title">
                    <h4>Trial balance</h4>
                    <h6>Posted debits and credits by account for the period. Totals must agree for a balanced set of books.</h6>
                </div>
                <a href="{{ route('admin.account-module') }}" class="btn btn-outline-secondary">Account module</a>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <form method="get" action="{{ route('admin.accounting.trial-balance') }}" class="row g-3 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label">From</label>
                        <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">To</label>
                        <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">Run</button>
                    </div>
                </form>
            </div>
        </div>

        @if(!$isBalanced)
            <div class="alert alert-warning">Period debits ({{ number_format($totalDebit, 2) }}) and credits ({{ number_format($totalCredit, 2) }}) do not match. Check for unposted or unbalanced journals.</div>
        @else
            <div class="alert alert-success">Trial balance in balance for the selected period.</div>
        @endif

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Account</th>
                                <th>Type</th>
                                <th class="text-end">Debit</th>
                                <th class="text-end">Credit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rows as $r)
                                <tr>
                                    <td>{{ $r->code }}</td>
                                    <td>{{ $r->name }}</td>
                                    <td>{{ $r->type }}</td>
                                    <td class="text-end">{{ number_format((float) $r->period_debit, 2) }}</td>
                                    <td class="text-end">{{ number_format((float) $r->period_credit, 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="fw-bold">
                                <td colspan="3">Totals</td>
                                <td class="text-end">{{ number_format($totalDebit, 2) }}</td>
                                <td class="text-end">{{ number_format($totalCredit, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
