@extends('layouts.app_admin')
@section('title')
    Edit Organisation
@endsection
@section('styles')
@endsection
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="page-title">
                    <h4>Edit Organisation</h4>
                    <h6>Update contact details or logo</h6>
                </div>
                <div class="page-btn">
                    <a href="{{ route('admin.organization-info.index') }}" class="btn btn-secondary">Back to list</a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.organization-info.update', $organization) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-lg-6 col-12 mb-3">
                                <label class="form-label">Organisation name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $organization->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-6 col-12 mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    value="{{ old('phone', $organization->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-6 col-12 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $organization->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-6 col-12 mb-3">
                                <label class="form-label">Website</label>
                                <input type="text" name="website"
                                    class="form-control @error('website') is-invalid @enderror"
                                    value="{{ old('website', $organization->website) }}" placeholder="https://example.com">
                                @error('website')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Address</label>
                                <textarea name="address" rows="3"
                                    class="form-control @error('address') is-invalid @enderror">{{ old('address', $organization->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Invoice footer wording</label>
                                <textarea name="invoice_footer_text" rows="3"
                                    class="form-control @error('invoice_footer_text') is-invalid @enderror" required>{{ old('invoice_footer_text', $organization->invoice_footer_text ?? \App\Models\OrganizationInfo::DEFAULT_INVOICE_FOOTER_TEXT) }}</textarea>
                                <small class="text-muted">Enter the wording to print at the bottom of sales invoices. Use a new line to separate messages.</small>
                                @error('invoice_footer_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-6 col-12 mb-3">
                                <label class="form-label">Company logo</label>
                                @if ($organization->logo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($organization->logo_path))
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $organization->logo_path) }}" alt="Logo"
                                            style="max-height: 60px;">
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="remove_logo" value="1"
                                            id="remove_logo">
                                        <label class="form-check-label" for="remove_logo">Remove current logo</label>
                                    </div>
                                @endif
                                <input type="file" name="logo" accept="image/*"
                                    class="form-control @error('logo') is-invalid @enderror">
                                @error('logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Leave empty to keep current image.</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'info',
                title: 'Notice',
                text: @json(session('error')),
                confirmButtonText: 'OK'
            });
        </script>
    @endif
@endsection
