@foreach ($stores as $store)
<tr>
    <td>{{ $store->name }}</td>
    <td>{{ $store->address }}</td>
    <td>{{ $store->city }}</td>
    <td>{{ $store->state }}</td>
    <td>{{ $store->phone }}</td>
    <td>{{ $store->email }}</td>
    <td>
        @if ($store->status == 1)
        <span class="badge badge-linesuccess">Active</span>
        @else
        <span class="badge badge-linedanger">Inactive</span>
        @endif
    </td>
</tr>
@endforeach
