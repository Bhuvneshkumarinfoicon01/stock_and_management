@foreach ($orders as $order)
<tr>
    <td>
        <label class="checkboxs">
            <input type="checkbox" />
            <span class="checkmarks"></span>
        </label>
    </td>
    <td>INV/{{ $order->id }}</td>
    <td>{{ $order->store->name }}</td>
    <td>{{ format_date($order->created_at) }}</td>
    <td>${{ $order->total_price }}</td>
    <td>${{ $order->total_price }}</td>
    <td>$0.00</td>
    <td>
        @if($order->status == 0)
        <span class="badge badge-linedanger">Unpaid</span>
        @else
        <span class="badge badge-linesuccess">Paid</span>
        @endif
    </td>
    <td class="action-table-data">
        <div class="edit-delete-action">
            <a
                class="me-2 edit-icon p-2 viewInvoice"
                href="#"
                data-id="{{ $order->id }}"
                data-bs-toggle="modal"
                data-bs-target="#edit-invoice"
            >
                <i data-feather="eye" class="feather-eye"></i>
            </a>
            <!-- <a class="confirm-text p-2" href="javascript:void(0);">
                <i data-feather="trash-2" class="feather-trash-2"></i>
            </a> -->
        </div>
    </td>
</tr>
@endforeach

