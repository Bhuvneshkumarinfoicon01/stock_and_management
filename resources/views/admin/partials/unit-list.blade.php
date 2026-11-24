@foreach($units as $unit)
<tr>
    <td>{{ $unit->name }}</td>
    <td>
        <span
            class="badge {{ $unit->status ? 'badge-linesuccess' : 'badge-linedanger' }}"
        >
            {{ $unit->status ? 'Active' : 'Inactive' }}
        </span>
    </td>
    <td>
        <button
            class="btn btn-sm btn-primary edit-category"
            data-id="{{ $unit->id }}"
        >
            Edit
        </button>
        <button
            class="btn btn-sm btn-danger delete-category"
            data-id="{{ $unit->id }}"
        >
            Delete
        </button>
    </td>
</tr>
@endforeach
