@extends('layouts.app_admin')
@section('title') Edit supplier @endsection
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header"><div class="page-title"><h4>Edit supplier</h4></div></div>
        @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
        <div class="card"><div class="card-body">
            <form method="post" action="{{ route('admin.suppliers.update', $supplier) }}" class="row g-3">
                @csrf
                @method('PUT')
                <div class="col-md-4"><label class="form-label">Company name *</label><input name="company_name" class="form-control" required value="{{ old('company_name', $supplier->company_name) }}"></div>
                <div class="col-md-4"><label class="form-label">Contact name</label><input name="contact_name" class="form-control" value="{{ old('contact_name', $supplier->contact_name) }}"></div>
                <div class="col-md-4"><label class="form-label">Code</label><input name="code" class="form-control" value="{{ old('code', $supplier->code) }}"></div>
                <div class="col-md-4"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email', $supplier->email) }}"></div>
                <div class="col-md-4"><label class="form-label">Phone</label><input name="phone" class="form-control" value="{{ old('phone', $supplier->phone) }}"></div>
                <div class="col-md-4"><label class="form-label">Tax number</label><input name="tax_number" class="form-control" value="{{ old('tax_number', $supplier->tax_number) }}"></div>
                <div class="col-12"><label class="form-label">Address</label><input name="address" class="form-control" value="{{ old('address', $supplier->address) }}"></div>
                <div class="col-md-4"><label class="form-label">City</label><input name="city" class="form-control" value="{{ old('city', $supplier->city) }}"></div>
                <div class="col-md-4"><label class="form-label">Country</label><input name="country" class="form-control" value="{{ old('country', $supplier->country) }}"></div>
                <div class="col-md-4"><label class="form-label">Credit limit</label><input type="number" name="credit_limit" class="form-control" min="0" step="0.01" value="{{ old('credit_limit', $supplier->credit_limit ?? 0) }}"><small class="text-muted">0 = no limit</small></div>
                <div class="col-md-4"><label class="form-label">Status</label>
                    <select name="status" class="form-select"><option value="1" @selected(old('status', $supplier->status)==1)>Active</option><option value="0" @selected(old('status', $supplier->status)==0)>Inactive</option></select>
                </div>
                <div class="col-12"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2">{{ old('notes', $supplier->notes) }}</textarea></div>
                <div class="col-12"><button class="btn btn-primary">Update</button> <a href="{{ route('admin.suppliers.index') }}" class="btn btn-light">Back</a></div>
            </form>
        </div></div>
    </div>
</div>
@endsection
