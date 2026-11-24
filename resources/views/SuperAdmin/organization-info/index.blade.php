@extends('layouts.app_admin')
@section('title')
    Organisation Info
@endsection
@section('styles')
@endsection
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4>Organisation Info</h4>
                        <h6>Manage organization name, contact details, and logo</h6>
                    </div>
                </div>
                <ul class="table-top-head">
                    <li>
                        <a data-bs-toggle="tooltip" data-bs-placement="top" title="Refresh" id="refresh-page"><i
                                data-feather="rotate-ccw" class="feather-rotate-ccw"></i></a>
                    </li>
                    <li>
                        <a data-bs-toggle="tooltip" data-bs-placement="top" title="Collapse" id="collapse-header"><i
                                data-feather="chevron-up" class="feather-chevron-up"></i></a>
                    </li>
                </ul>
                @if (!$hasOrganization)
                    <div class="page-btn">
                        <a href="{{ route('admin.organization-info.create') }}" class="btn btn-added"><i
                                data-feather="plus-circle" class="me-2"></i>Add Organisation</a>
                    </div>
                @endif
            </div>

            <div class="card table-list-card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table datanew">
                            <thead>
                                <tr>
                                    <th>Logo</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Website</th>
                                    <th class="no-sort">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($organizations as $org)
                                    <tr>
                                        <td>
                                            @if ($org->logo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($org->logo_path))
                                                <img src="{{ asset('storage/' . $org->logo_path) }}" alt=""
                                                    style="max-height: 40px; max-width: 80px; object-fit: contain;">
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>{{ $org->name }}</td>
                                        <td>{{ $org->email ?? '—' }}</td>
                                        <td>{{ $org->phone ?? '—' }}</td>
                                        <td>
                                            @if ($org->website)
                                                <a href="{{ $org->website }}" target="_blank" rel="noopener noreferrer">Link</a>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>
                                            <a class="p-2"
                                                href="{{ route('admin.organization-info.edit', $org) }}"><i
                                                    data-feather="edit" class="feather-edit"></i></a>
                                            <form action="{{ route('admin.organization-info.destroy', $org) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Delete this organisation record?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link text-danger p-2 border-0">
                                                    <i data-feather="trash-2" class="feather-trash-2"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">No organisations yet. Click
                                            “Add Organisation”.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: @json(session('success')),
                timer: 2000,
                showConfirmButton: false
            });
        </script>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.feather) {
                feather.replace();
            }
        });
    </script>
@endsection
