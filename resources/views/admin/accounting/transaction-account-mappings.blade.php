@extends('layouts.app_admin')
@section('title') Transaction account mappings @endsection
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header d-flex justify-content-between flex-wrap">
            <div class="page-title">
                <h4>Transaction account mappings</h4>
                <h6>Configure default debit and credit GL accounts for each document type (point 16).</h6>
            </div>
            <a href="{{ route('admin.account-module') }}" class="btn btn-outline-secondary">Back to accounts</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif

        <div class="card">
            <div class="card-body">
                <form method="post" action="{{ route('admin.accounting.transaction-mappings.update') }}">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Document / source</th>
                                    <th>Debit account</th>
                                    <th>Credit account</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mappings as $mapping)
                                    @php
                                        $label = $sourceLabels[$mapping->source_type] ?? $mapping->label ?? $mapping->source_type;
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $label }}</strong>
                                            <div class="text-muted small">{{ $mapping->source_type }}</div>
                                        </td>
                                        <td>
                                            <select name="mappings[{{ $mapping->source_type }}][debit_account_id]" class="form-select" required>
                                                @foreach($accounts->groupBy('type') as $type => $group)
                                                    <optgroup label="{{ ucfirst($type) }}">
                                                        @foreach($group as $account)
                                                            <option value="{{ $account->id }}" @selected((int) $mapping->debit_account_id === (int) $account->id)>
                                                                {{ $account->code }} — {{ $account->name }}
                                                            </option>
                                                        @endforeach
                                                    </optgroup>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="mappings[{{ $mapping->source_type }}][credit_account_id]" class="form-select" required>
                                                @foreach($accounts->groupBy('type') as $type => $group)
                                                    <optgroup label="{{ ucfirst($type) }}">
                                                        @foreach($group as $account)
                                                            <option value="{{ $account->id }}" @selected((int) $mapping->credit_account_id === (int) $account->id)>
                                                                {{ $account->code }} — {{ $account->name }}
                                                            </option>
                                                        @endforeach
                                                    </optgroup>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <p class="text-muted small">
                        Sale invoices use these accounts for the <strong>revenue</strong> journal (point 17). Product debit/credit accounts on the stock form are used for the separate <strong>cost</strong> journal.
                    </p>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Save mappings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
