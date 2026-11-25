@extends('layouts.app_admin')
@section('title') {{ isset($journalEntry) ? 'Edit journal #' . $journalEntry->id : 'New journal' }} @endsection

@section('content')
@php
    $isEditing = isset($journalEntry);
    $existingLines = $isEditing
        ? $journalEntry->lines->map(fn ($line) => [
            'gl_account_id' => $line->gl_account_id,
            'line_description' => $line->line_description,
            'debit' => $line->debit,
            'credit' => $line->credit,
        ])->all()
        : [];
@endphp
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex justify-content-between flex-wrap">
                <div class="page-title">
                    <h4>{{ $isEditing ? 'Edit journal entry' : 'New journal entry' }}</h4>
                    <h6>Each line is either a debit or a credit. Total debits must equal total credits before posting.</h6>
                </div>
                <a href="{{ $isEditing ? route('admin.accounting.journals.show', $journalEntry) : route('admin.accounting.journals.index') }}" class="btn btn-outline-secondary">Back to journals</a>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif
        @if($isEditing && $journalEntry->isPosted())
            <div class="alert alert-warning">This journal is posted. Saving changes updates its general-ledger amounts immediately.</div>
        @endif

        <div class="card">
            <div class="card-body">
                <form action="{{ $isEditing ? route('admin.accounting.journals.update', $journalEntry) : route('admin.accounting.journals.store') }}" method="POST" id="journal-create-form">
                    @csrf
                    @if($isEditing)
                        @method('PUT')
                    @endif
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Entry date</label>
                            <input type="date" name="entry_date" class="form-control" required value="{{ old('entry_date', $isEditing ? $journalEntry->entry_date->toDateString() : now()->toDateString()) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Reference</label>
                            <input type="text" name="reference" class="form-control" value="{{ old('reference', $isEditing ? $journalEntry->reference : '') }}" placeholder="Optional">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Description</label>
                            <input type="text" name="description" class="form-control" value="{{ old('description', $isEditing ? $journalEntry->description : '') }}" placeholder="Optional header note">
                        </div>
                    </div>

                    <div class="table-responsive mb-2">
                        <table class="table table-bordered" id="lines-table">
                            <thead>
                                <tr>
                                    <th style="width:32%">Account</th>
                                    <th>Line memo</th>
                                    <th style="width:12%">Debit</th>
                                    <th style="width:12%">Credit</th>
                                    <th style="width:6%"></th>
                                </tr>
                            </thead>
                            <tbody id="journal-line-rows">
                                @php
                                    $oldLines = old('lines', $existingLines);
                                    $count = max($isEditing ? 2 : 4, count($oldLines));
                                @endphp
                                @for($i = 0; $i < $count; $i++)
                                    @php $L = $oldLines[$i] ?? []; @endphp
                                    <tr class="journal-line-row">
                                        <td>
                                            <select name="lines[{{ $i }}][gl_account_id]" class="form-select" required>
                                                <option value="">Choose account</option>
                                                @foreach($accounts as $acc)
                                                    <option value="{{ $acc->id }}" @selected(($L['gl_account_id'] ?? '') == $acc->id)>{{ $acc->code }} — {{ $acc->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="text" name="lines[{{ $i }}][line_description]" class="form-control" value="{{ $L['line_description'] ?? '' }}"></td>
                                        <td><input type="number" step="0.01" min="0" name="lines[{{ $i }}][debit]" class="form-control line-debit" value="{{ $L['debit'] ?? '' }}" placeholder="0"></td>
                                        <td><input type="number" step="0.01" min="0" name="lines[{{ $i }}][credit]" class="form-control line-credit" value="{{ $L['credit'] ?? '' }}" placeholder="0"></td>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-light remove-line" title="Remove row">&times;</button></td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-outline-primary mb-3" id="add-line">Add line</button>
                    <div>
                        <button type="submit" class="btn btn-primary">{{ $isEditing ? 'Save changes' : 'Save as draft' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var tbody = document.getElementById('journal-line-rows');
    var accounts = @json($accounts->map(fn ($a) => ['id' => $a->id, 'label' => $a->code . ' — ' . $a->name]));
    var idx = tbody.querySelectorAll('tr').length;

    function accountOptions(selected) {
        var html = '<option value="">Choose account</option>';
        accounts.forEach(function (a) {
            html += '<option value="' + a.id + '"' + (String(a.id) === String(selected) ? ' selected' : '') + '>' + a.label + '</option>';
        });
        return html;
    }

    document.getElementById('add-line').addEventListener('click', function () {
        var tr = document.createElement('tr');
        tr.className = 'journal-line-row';
        tr.innerHTML =
            '<td><select name="lines[' + idx + '][gl_account_id]" class="form-select" required>' + accountOptions('') + '</select></td>' +
            '<td><input type="text" name="lines[' + idx + '][line_description]" class="form-control"></td>' +
            '<td><input type="number" step="0.01" min="0" name="lines[' + idx + '][debit]" class="form-control line-debit" placeholder="0"></td>' +
            '<td><input type="number" step="0.01" min="0" name="lines[' + idx + '][credit]" class="form-control line-credit" placeholder="0"></td>' +
            '<td class="text-center"><button type="button" class="btn btn-sm btn-light remove-line">&times;</button></td>';
        tbody.appendChild(tr);
        idx++;
    });

    tbody.addEventListener('click', function (e) {
        if (e.target.closest('.remove-line')) {
            var rows = tbody.querySelectorAll('tr');
            if (rows.length <= 2) return;
            e.target.closest('tr').remove();
        }
    });
})();
</script>
@endsection
