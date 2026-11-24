<tbody>
    @foreach($stockTransfers as $transfer)
    <tr>
        <td>
            <label class="checkboxs">
                <input type="checkbox" />
                <span class="checkmarks"></span>
            </label>
        </td>
        <td>{{ $transfer->storeFrom->name }}</td>
        <td>{{ $transfer->storeTo->name }}</td>
        <td>{{ $transfer->items->count() }}</td>
        <td>{{ $transfer->items->sum('qty') }}</td>
        <td>{{ $transfer->reference_no }}</td>
        <td>{{ format_date($transfer->created_at) }}</td>
        @php $user = auth()->user(); @endphp @if($user && $user->role_id === 1)
        <td class="action-table-data">
            <div class="edit-delete-action">
                <a
                    class="me-2 p-2 edit-btn"
                    href="javascript:void(0);"
                    data-id="{{ $transfer->id }}"
                    data-bs-toggle="modal"
                    data-bs-target="#edit-units"
                >
                    <i data-feather="edit" class="feather-edit"></i>
                </a>
                <a
                    href="javascript:void(0);"
                    class="confirm-text p-2 delete-transfer"
                    data-id="{{ $transfer->id }}"
                >
                    <i data-feather="trash-2" class="feather-trash-2"></i>
                </a>
            </div>
        </td>
        @endif
    </tr>
    @endforeach
</tbody>
