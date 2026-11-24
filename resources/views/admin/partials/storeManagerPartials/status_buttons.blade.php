@if (is_null($status))
    <button class="btn btn-primary choose-status" data-id="{{ $id }}" data-product="{{ $product_id }}"
        onclick="showalert(this,'{{$id}}')" data-reference="{{ $reference_no }}">
        Choose Status
    </button>
@elseif ($status == 0)
    <button class="btn btn-danger choose-status" data-id="{{ $id }}">
        Rejected
    </button>
@elseif ($status == 1)
    <button class="btn btn-success choose-status" data-id="{{ $id }}">
        Accepted
    </button>
@endif
