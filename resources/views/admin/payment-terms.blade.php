@extends('layouts.app_admin')
@section('title') Payment Terms @endsection

@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4>Payment Terms</h4>
                    <h6>Create, update, activate and deactivate terms</h6>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">Add Payment Term</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.payment-terms.store') }}" method="POST" class="row g-3" id="create-payment-term-form">
                    @csrf
                    <div class="col-md-4">
                        <label class="form-label">Label</label>
                        <input type="text" name="label" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Due Days</label>
                        <input type="number" name="due_days" class="form-control" min="0">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Due Months</label>
                        <input type="number" name="due_months" class="form-control" min="0" value="0">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" min="0" value="0">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="is_active" class="form-select">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary create-submit-btn">Save</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    @include('admin.purchases.partials.list-pagination', ['paginator' => $paymentTerms, 'showLinks' => false, 'showSummary' => false, 'containerClass' => 'mb-3'])
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Label</th>
                                <th>Due Days</th>
                                <th>Due Months</th>
                                <th>Sort Order</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($paymentTerms as $term)
                                <tr>
                                    <td>{{ $term->id }}</td>
                                    <td>{{ $term->label }}</td>
                                    <td>{{ $term->due_days ?? '-' }}</td>
                                    <td>{{ $term->due_months ?? 0 }}</td>
                                    <td>{{ $term->sort_order ?? 0 }}</td>
                                    <td>
                                        @if($term->is_active)
                                            <span class="badge badge-linesuccess">Active</span>
                                        @else
                                            <span class="badge badge-linedanger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-primary edit-term-btn"
                                                data-id="{{ $term->id }}"
                                                data-label="{{ $term->label }}"
                                                data-due-days="{{ $term->due_days }}"
                                                data-due-months="{{ $term->due_months }}"
                                                data-sort-order="{{ $term->sort_order }}"
                                                data-is-active="{{ $term->is_active }}"
                                            >
                                                Edit
                                            </button>

                                            <form action="{{ route('admin.payment-terms.toggle-status', $term->id) }}" method="POST" class="d-inline toggle-status-form">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ $term->is_active ? 'btn-outline-warning' : 'btn-outline-success' }} toggle-status-btn">
                                                    {{ $term->is_active ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>

                                            <form action="{{ route('admin.payment-terms.destroy', $term->id) }}" method="POST" class="d-inline delete-term-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger delete-term-btn">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No payment terms found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    @include('admin.purchases.partials.list-pagination', ['paginator' => $paymentTerms, 'showPerPage' => false])
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="edit-payment-term-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Payment Term</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="edit-payment-term-form">
                @csrf
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Label</label>
                        <input type="text" name="label" id="edit-label" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Due Days</label>
                        <input type="number" name="due_days" id="edit-due-days" class="form-control" min="0">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Due Months</label>
                        <input type="number" name="due_months" id="edit-due-months" class="form-control" min="0">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" id="edit-sort-order" class="form-control" min="0">
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Status</label>
                        <select name="is_active" id="edit-is-active" class="form-select">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary edit-submit-btn">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(function () {
        $(document).on('click', '.edit-term-btn', function () {
            const id = $(this).data('id');
            const urlTemplate = "{{ route('admin.payment-terms.update', ['paymentTerm' => '__ID__']) }}";
            $('#edit-payment-term-form').attr('action', urlTemplate.replace('__ID__', id));
            $('#edit-label').val($(this).data('label'));
            $('#edit-due-days').val($(this).data('due-days'));
            $('#edit-due-months').val($(this).data('due-months'));
            $('#edit-sort-order').val($(this).data('sort-order'));
            $('#edit-is-active').val(String($(this).data('is-active')));
            $('#edit-payment-term-modal').modal('show');
        });

        $('#create-payment-term-form').on('submit', function () {
            const $btn = $(this).find('.create-submit-btn');
            $btn.prop('disabled', true).text('Saving...');
        });

        $('#edit-payment-term-form').on('submit', function () {
            const $btn = $(this).find('.edit-submit-btn');
            $btn.prop('disabled', true).text('Updating...');
        });

        $(document).on('submit', '.toggle-status-form', function () {
            const $btn = $(this).find('.toggle-status-btn');
            $btn.prop('disabled', true).text('Saving...');
        });

        $(document).on('submit', '.delete-term-form', function (e) {
            const confirmed = confirm('Delete this payment term?');
            if (!confirmed) {
                e.preventDefault();
                return;
            }
            const $btn = $(this).find('.delete-term-btn');
            $btn.prop('disabled', true).text('Deleting...');
        });
    });
</script>
@endsection

