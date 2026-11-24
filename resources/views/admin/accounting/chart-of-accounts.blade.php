@extends('layouts.app_admin')
@section('title') Chart of Accounts @endsection

@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex justify-content-between flex-wrap">
                <div class="page-title">
                    <h4>Chart of Accounts</h4>
                    <h6>General ledger accounts (double-entry). Debits and credits post through journals.</h6>
                </div>
                <a href="{{ route('admin.account-module') }}" class="btn btn-outline-secondary">Account module</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif

        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">Add account</h5></div>
            <div class="card-body">
                <form action="{{ route('admin.accounting.chart-of-accounts.store') }}" method="POST" class="row g-3">
                    @csrf
                    <div class="col-md-2"><label class="form-label">Code</label><input type="text" name="code" class="form-control" required value="{{ old('code') }}"></div>
                    <div class="col-md-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control" required value="{{ old('name') }}"></div>
                    <div class="col-md-2">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select" required>
                            @foreach(\App\Models\GlAccount::types() as $t)
                                <option value="{{ $t }}" @selected(old('type') === $t)>{{ ucfirst($t) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Parent (optional)</label>
                        <select name="parent_id" class="form-select">
                            <option value="">—</option>
                            @foreach($accounts as $p)
                                <option value="{{ $p->id }}" @selected(old('parent_id') == $p->id)>{{ $p->code }} — {{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3"><label class="form-label">Description</label><input type="text" name="description" class="form-control" value="{{ old('description') }}"></div>
                    <div class="col-12"><button type="submit" class="btn btn-primary">Save account</button></div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Parent</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($accounts as $a)
                                <tr>
                                    <td>{{ $a->code }}</td>
                                    <td>{{ $a->name }}</td>
                                    <td><span class="badge bg-secondary">{{ $a->type }}</span></td>
                                    <td>{{ $a->parent?->code ?? '—' }}</td>
                                    <td>
                                        @if($a->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editCoa{{ $a->id }}">Edit</button>
                                        <form action="{{ route('admin.accounting.chart-of-accounts.toggle', $a) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-secondary">Toggle active</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @foreach($accounts as $a)
            <div class="modal fade" id="editCoa{{ $a->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('admin.accounting.chart-of-accounts.update', $a) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-header"><h5 class="modal-title">Edit {{ $a->code }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                            <div class="modal-body row g-2">
                                <div class="col-12"><label class="form-label">Code</label><input type="text" name="code" class="form-control" required value="{{ $a->code }}"></div>
                                <div class="col-12"><label class="form-label">Name</label><input type="text" name="name" class="form-control" required value="{{ $a->name }}"></div>
                                <div class="col-12">
                                    <label class="form-label">Type</label>
                                    <select name="type" class="form-select" required>
                                        @foreach(\App\Models\GlAccount::types() as $t)
                                            <option value="{{ $t }}" @selected($a->type === $t)>{{ ucfirst($t) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Parent</label>
                                    <select name="parent_id" class="form-select">
                                        <option value="">—</option>
                                        @foreach($accounts as $p)
                                            @if($p->id !== $a->id)
                                                <option value="{{ $p->id }}" @selected($a->parent_id == $p->id)>{{ $p->code }} — {{ $p->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12"><label class="form-label">Description</label><input type="text" name="description" class="form-control" value="{{ $a->description }}"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
