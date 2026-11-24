@if (is_null($status))
<span class="badge bg-outline-warning p-2 choose-status" data-id="{{ $id }}" data-product="{{ $product_id }}"
    data-reference="{{ $reference_no }}">
    Pending
</span>
@elseif ($status === 0)
<span class="badge badge-linedanger choose-status" data-id="{{ $id }}">
    Rejected
</span>
@elseif ($status === 1)
<span class="badge badge-linesuccess choose-status" data-id="{{ $id }}">
    Accepted
</span>
@endif