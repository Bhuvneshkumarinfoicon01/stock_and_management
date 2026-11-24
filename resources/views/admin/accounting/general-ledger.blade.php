@extends('layouts.app_admin')
@section('title') General ledger @endsection

@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex justify-content-between flex-wrap">
                <div class="page-title">
                    <h4>General ledger</h4>
                    <h6>Posted journal lines for one account, with running balance (debit-normal vs credit-normal).</h6>
                </div>
                <a href="{{ route('admin.account-module') }}" class="btn btn-outline-secondary">Account module</a>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <form method="get" action="{{ route('admin.accounting.general-ledger') }}" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Account</label>
                        <select name="gl_account_id" class="form-select" required>
                            <option value="">— Select —</option>
                            @foreach($accounts as $a)
                                <option value="{{ $a->id }}" @selected(request('gl_account_id') == $a->id)>{{ $a->code }} — {{ $a->name }} ({{ $a->type }})</option>
                            @endforeach
                        </select>
                    </div>
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

        @if($selectedAccount)
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ $selectedAccount->code }} — {{ $selectedAccount->name }}</h5>
                    <small class="text-muted">Opening balance before {{ format_date($dateFrom) }}: {{ number_format($openingBalance, 2) }}</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Journal</th>
                                    <th>Reference</th>
                                    <th>Memo</th>
                                    <th class="text-end">Debit</th>
                                    <th class="text-end">Credit</th>
                                    <th class="text-end">Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lines as $line)
                                    <tr>
                                        <td>{{ format_date($line->journalEntry->entry_date) }}</td>
                                        <td><a href="{{ route('admin.accounting.journals.show', $line->journalEntry) }}">#{{ $line->journal_entry_id }}</a></td>
                                        <td>{{ $line->journalEntry->reference ?? '—' }}</td>
                                        <td>{{ $line->line_description ?? '—' }}</td>
                                        <td class="text-end">{{ number_format((float) $line->debit, 2) }}</td>
                                        <td class="text-end">{{ number_format((float) $line->credit, 2) }}</td>
                                        <td class="text-end">{{ number_format($line->running_balance ?? 0, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center text-muted">No posted activity in this range.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <p class="text-muted">Select an account and date range, then click Run.</p>
        @endif
    </div>
</div>
@endsection
