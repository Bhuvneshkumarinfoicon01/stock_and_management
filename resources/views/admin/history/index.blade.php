{{-- resources/views/admin/history/index.blade.php --}}
@extends('layouts.app_admin')
@section('title','Audit Trail')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="page-wrapper">
    <div class="content">
        <h4 class="mb-3">Audit Trail</h4>

        <table id="historyTable" class="table table-striped w-100">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Entity</th>
                    <th>Event</th>
                    <th>When</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

{{-- Modal --}}
<div class="modal fade" id="historyModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="historyModalLabel">History Detail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="historyModalBody">
                {{-- content injected by JS --}}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')

<script>
    $(function () {
        // DataTable
        const tbl = $('#historyTable').DataTable({
            ajax: "{{ route('admin.history.data') }}",
            columns: [
                { data: 'user' },
                { data: 'entity' },
                { data: 'event' },
                { data: 'when' },
                { data: 'action', orderable: false, searchable: false }
            ],
            order: [[3, 'desc']]
        });

        // Click handler
        $('#historyTable').on('click', '.view-history', function () {
            const id = $(this).data('id');
            let url = `{{ route('admin.history.show', ':id') }}`.replace(':id', id);
            $.get(url, function (res) {
                buildModal(res);
                $('#historyModal').modal('show');
            });
        });

        // Build modal depending on event
        function buildModal(h) {
            let html = `
            <div style="max-height:60vh;overflow:auto;">
                <p><strong>Entity:</strong> ${h.key ?? h.auditable_id}</p>
                <p><strong>User:</strong> ${h.user}</p>
                <p><strong>Date:</strong> ${h.when}</p>
            `;

            if (h.event === 'created') {
                html += renderCreated(h.new_values);
            } else if (h.event === 'updated') {
                html += renderUpdated(h.new_values, h.old_values);
            } else if (h.event === 'deleted') {
                html += renderDeleted(h.old_values);
            }
            html += '</div>';
            $('#historyModalBody').html(html);
        }

        // Render helpers
        function renderCreated(newVals) {
            return `
            <h6 class="mt-3">New Values</h6>
            ${objectToTable(newVals)}
            `;
        }
        function renderUpdated(newVals, oldVals) {
            let rows = '';
            for (const key in newVals) {
                if (['created_at', 'updated_at'].includes(key)) continue; // skip these fields
                const newVal = formatVal(newVals[key]);
                const oldVal = oldVals[key] !== undefined ? formatVal(oldVals[key]) : '-';
                rows += `<tr><td>${key}</td><td>${oldVal}</td><td>${newVal}</td></tr>`;
            }
            return `
            <h6 class="mt-3">Changes</h6>
            <table class="table table-bordered table-sm">
                <thead><tr><th>Field</th><th>Old</th><th>New</th></tr></thead>
                <tbody>${rows}</tbody>
            </table>`;
        }
        function renderDeleted(oldVals) {
            return `
            <h6 class="mt-3">Deleted Record</h6>
            ${objectToTable(oldVals)}
            `;
        }

        // Improved object/array to table
        function objectToTable(obj) {
            if (Array.isArray(obj)) {
                if (obj.length === 0) return '<em>No data</em>';
                // If array of objects, render as table
                if (typeof obj[0] === 'object') {
                    let headers = Object.keys(obj[0]).filter(h => !['created_at', 'updated_at'].includes(h));
                    let thead = headers.map(h => `<th>${h}</th>`).join('');
                    let tbody = obj.map(row =>
                        '<tr>' + headers.map(h => `<td>${formatVal(row[h])}</td>`).join('') + '</tr>'
                    ).join('');
                    return `<table class="table table-bordered table-sm"><thead><tr>${thead}</tr></thead><tbody>${tbody}</tbody></table>`;
                } else {
                    // Simple array
                    return `<ul>${obj.map(v => `<li>${formatVal(v)}</li>`).join('')}</ul>`;
                }
            } else if (typeof obj === 'object' && obj !== null) {
                let rows = '';
                for (const k in obj) {
                    if (['created_at', 'updated_at'].includes(k)) continue; // skip these fields
                    rows += `<tr><td><strong>${k}</strong></td><td>${formatVal(obj[k])}</td></tr>`;
                }
                return `<table class="table table-bordered table-sm"><tbody>${rows}</tbody></table>`;
            } else {
                return formatVal(obj);
            }
        }

        function formatVal(v) {
            if (Array.isArray(v) || (typeof v === 'object' && v !== null)) {
                return objectToTable(v);
            }
            return v === null || v === undefined ? '<em>null</em>' : v;
        }
    });
</script>
@endsection