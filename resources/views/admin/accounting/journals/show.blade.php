@extends('layouts.app_admin')
@section('title') Journal #{{ $journalEntry->id }} @endsection

@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex justify-content-between flex-wrap">
                <div class="page-title">
                    <h4>Journal #{{ $journalEntry->id }}</h4>
                    <h6>{{ format_date($journalEntry->entry_date) }} — {{ $journalEntry->reference ?? 'No reference' }}</h6>
                </div>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    @include('admin.partials.record-navigator')
                    @if($journalEntry->source_type === null && $canEditJournal)
                        <a href="{{ route('admin.accounting.journals.edit', $journalEntry) }}" class="btn btn-primary">Edit journal</a>
                    @endif
                    <a href="{{ route('admin.accounting.journals.index') }}" class="btn btn-outline-secondary">All journals</a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif

        @if(!empty($saleJournalContext))
            {{-- Sale invoice journal document (revenue + COGS) — matches accounting worksheet layout --}}
            <div class="card mb-3">
                <div class="card-body">
                    <p class="mb-1"><strong>Status:</strong>
                        @if($journalEntry->status === 'posted')
                            <span class="badge bg-success">Posted</span>
                            @if($journalEntry->posted_at)<span class="text-muted ms-2">{{ format_datetime($journalEntry->posted_at) }}</span>@endif
                        @else
                            <span class="badge bg-warning text-dark">Draft</span>
                        @endif
                    </p>
                    @if($journalEntry->description)
                        <p class="mb-0"><strong>Description:</strong> {{ $journalEntry->description }}</p>
                    @endif
                    @if(count($saleJournalContext['related_journal_ids'] ?? []) > 1)
                        <p class="mb-0 text-muted small">Related journals:
                            @foreach($saleJournalContext['related_journal_ids'] as $jid)
                                @if($jid == $journalEntry->id)
                                    <strong>#{{ $jid }}</strong>@if(!$loop->last), @endif
                                @else
                                    <a href="{{ route('admin.accounting.journals.show', $jid) }}">#{{ $jid }}</a>@if(!$loop->last), @endif
                                @endif
                            @endforeach
                        </p>
                    @endif
                </div>
            </div>

            <div class="card mb-3 border">
                <div class="card-body sale-journal-document">
                    <p class="mb-1"><strong>Invoice #:</strong> {{ $saleJournalContext['invoice_number'] }}</p>
                    <p class="mb-3"><strong>Customer:</strong> {{ $saleJournalContext['customer_name'] }}</p>

                    @foreach($saleJournalContext['invoice_lines'] as $line)
                        <p class="mb-1">
                            <strong>{{ $line['product_name'] }}</strong>
                            — qty: {{ $line['qty'] }},
                            Unit Price: {{ number_format($line['unit_price'], 2) }},
                            @if($line['is_taxable'] && $line['tax_rate'] > 0)
                                Tax: {{ rtrim(rtrim(number_format($line['tax_rate'], 2), '0'), '.') }}%,
                            @else
                                Tax: exempt,
                            @endif
                            Total: {{ number_format($line['line_total'], 2) }}
                            @if($line['unit_cost'] > 0)
                                <span class="text-muted">@@unit cost: {{ number_format($line['unit_cost'], 2) }} each</span>
                            @endif
                        </p>
                    @endforeach

                    <hr class="my-3">
                    <div class="row">
                        <div class="col-md-6 offset-md-6">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td>Subtotal</td>
                                    <td class="text-end">{{ number_format($saleJournalContext['subtotal'], 2) }}</td>
                                </tr>
                                @if(($saleJournalContext['discount_amount'] ?? 0) > 0)
                                <tr>
                                    <td>Discount ({{ rtrim(rtrim(number_format($saleJournalContext['discount_percent'], 2), '0'), '.') }}%)</td>
                                    <td class="text-end">-{{ number_format($saleJournalContext['discount_amount'], 2) }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td>Tax</td>
                                    <td class="text-end">{{ number_format($saleJournalContext['tax_amount'], 2) }}</td>
                                </tr>
                                <tr class="fw-bold border-top">
                                    <td>Total</td>
                                    <td class="text-end">{{ number_format($saleJournalContext['total'], 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="table-responsive mt-4">
                        <table class="table table-bordered mb-0 sale-journal-ledger">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 110px;">DATE</th>
                                    <th>PARTICULARS</th>
                                    <th>MEMO</th>
                                    <th class="text-end" style="width: 130px;">DEBIT</th>
                                    <th class="text-end" style="width: 130px;">CREDIT</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $grandDebit = 0; $grandCredit = 0; @endphp
                                @foreach($saleJournalContext['journal_groups'] as $group)
                                    @if(!empty($group['spacer']))
                                        <tr class="sale-journal-spacer"><td colspan="5">&nbsp;</td></tr>
                                        @continue
                                    @endif
                                    @foreach($group['rows'] as $row)
                                        @php
                                            $grandDebit += (float) ($row['debit'] ?? 0);
                                            $grandCredit += (float) ($row['credit'] ?? 0);
                                        @endphp
                                        <tr>
                                            <td>{{ $row['date'] }}</td>
                                            <td>{{ $row['particulars'] }}</td>
                                            <td>{{ $row['memo'] ?? '—' }}</td>
                                            <td class="text-end">{{ $row['debit'] !== null ? number_format($row['debit'], 2) : '' }}</td>
                                            <td class="text-end">{{ $row['credit'] !== null ? number_format($row['credit'], 2) : '' }}</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                                <tr class="fw-bold table-light">
                                    <td colspan="3">Totals</td>
                                    <td class="text-end">{{ number_format($grandDebit, 2) }}</td>
                                    <td class="text-end">{{ number_format($grandCredit, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{--
            Old simple lines table (replaced by invoice + ledger layout above for sale journals):
            <div class="card mb-3">
                <div class="card-header"><h5 class="mb-0">Lines</h5></div>
                ...
            </div>
            --}}
        @else
            <div class="card mb-3">
                <div class="card-body">
                    <p class="mb-1"><strong>Status:</strong>
                        @if($journalEntry->status === 'posted')
                            <span class="badge bg-success">Posted</span>
                            @if($journalEntry->posted_at)<span class="text-muted ms-2">{{ format_datetime($journalEntry->posted_at) }}</span>@endif
                        @else
                            <span class="badge bg-warning text-dark">Draft</span>
                        @endif
                    </p>
                    @if($journalEntry->description)
                        <p class="mb-0"><strong>Description:</strong> {{ $journalEntry->description }}</p>
                    @endif
                    @if($journalEntry->user)
                        <p class="mb-0 text-muted small">Created by {{ $journalEntry->user->name ?? ('User #' . $journalEntry->user_id) }}</p>
                    @endif
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header"><h5 class="mb-0">Lines</h5></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Account</th>
                                    <th>Memo</th>
                                    <th class="text-end">Debit</th>
                                    <th class="text-end">Credit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $td = 0; $tc = 0; @endphp
                                @foreach($journalEntry->lines as $line)
                                    @php $td += (float) $line->debit; $tc += (float) $line->credit; @endphp
                                    <tr>
                                        <td>{{ $line->glAccount->code }} — {{ $line->glAccount->name }}</td>
                                        <td>{{ $line->line_description ?? '—' }}</td>
                                        <td class="text-end">{{ number_format((float) $line->debit, 2) }}</td>
                                        <td class="text-end">{{ number_format((float) $line->credit, 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr class="fw-bold">
                                    <td colspan="2">Totals</td>
                                    <td class="text-end">{{ number_format($td, 2) }}</td>
                                    <td class="text-end">{{ number_format($tc, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        @if($journalEntry->isDraft())
            @php
                $td = $journalEntry->lines->sum('debit');
                $tc = $journalEntry->lines->sum('credit');
            @endphp
            <div class="d-flex flex-wrap gap-2">
                <form action="{{ route('admin.accounting.journals.post', $journalEntry) }}" method="POST" onsubmit="return confirm('Post this journal to the general ledger? This cannot be undone.');">
                    @csrf
                    <button type="submit" class="btn btn-success" @if(round($td,2) !== round($tc,2)) disabled title="Debits must equal credits" @endif>Post journal</button>
                </form>
                <form action="{{ route('admin.accounting.journals.destroy', $journalEntry) }}" method="POST" onsubmit="return confirm('Delete this draft?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">Delete draft</button>
                </form>
            </div>
            @if(round($td, 2) !== round($tc, 2))
                <p class="text-danger mt-2 mb-0">This draft is not balanced and cannot be posted. Delete it and create a new journal with equal total debits and credits.</p>
            @endif
        @endif
    </div>
</div>
@endsection

@section('style')
<style>
    .sale-journal-document { font-size: 0.95rem; }
    .sale-journal-ledger th,
    .sale-journal-ledger td { vertical-align: middle; }
    .sale-journal-spacer td { height: 1.25rem; padding: 0; border-left: none !important; border-right: none !important; background: #fff; }
</style>
@endsection
