@foreach($categories as $category)
<tr>
    <td>{{ $category->name }}</td>
    <td>
        <span class="badge {{ $category->status ? 'badge-linesuccess' : 'badge-linedanger' }}">
            {{ $category->status ? 'Active' : 'Inactive' }}
        </span>
    </td>
    <td>
        <button class="btn btn-sm btn-primary edit-category" data-id="{{ $category->id }}">
            Edit
        </button>
        <button class="btn btn-sm btn-danger delete-category" data-id="{{ $category->id }}">
            Delete
        </button>
    </td>
</tr>
@endforeach