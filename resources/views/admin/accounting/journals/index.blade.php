@extends('layouts.app_admin')
@section('title') Journals @endsection

@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex justify-content-between flex-wrap">
                <div class="page-title">
                    <h4>Journal entries</h4>
                    <h6>Manual journals can be edited by administrators or employees with Journal Update permission. Editing a posted journal updates the general ledger.</h6>
                </div>
                <div>
                    <a href="{{ route('admin.accounting.journals.create') }}" class="btn btn-primary">New journal</a>
                    <a href="{{ route('admin.account-module') }}" class="btn btn-outline-secondary">Account module</a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Reference</th>
                                <th>Description</th>
                                <th>Lines</th>
                                <th>Source</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($entries as $entry)
                                <tr>
                                    <td>{{ $entry->id }}</td>
                                    <td>{{ format_date($entry->entry_date) }}</td>
                                    <td>{{ $entry->reference ?? '—' }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($entry->description ?? '—', 40) }}</td>
                                    <td>{{ $entry->lines_count }}</td>
                                    <td>
                                        @if($entry->source_type)
                                            <span class="badge bg-light text-dark">{{ $entry->source_type }} #{{ $entry->source_id }}</span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>
                                        @if($entry->status === 'posted')
                                            <span class="badge bg-success">Posted</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Draft</span>
                                        @endif
                                    </td>
                                    <td class="text-nowrap">
                                        <a href="{{ route('admin.accounting.journals.show', $entry) }}" class="btn btn-sm btn-outline-primary">View</a>
                                        @if($canEditJournal && ($entry->source_type === null || $canEditGeneratedJournals))
                                            <a href="{{ route('admin.accounting.journals.edit', $entry) }}" class="btn btn-sm btn-primary">Edit</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7">No journals yet. Create one with balanced debits and credits.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $entries->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
