@php
    $draftOrderId = $draftOrderId ?? null;
@endphp
<input type="hidden" name="invoice_status" id="invoice_status" value="Confirmed">
@if($draftOrderId)
    <input type="hidden" name="order_id" id="draft_order_id" value="{{ $draftOrderId }}">
@endif
<div class="d-flex justify-content-center mt-4 gap-2">
    <button type="button" id="placeOrder" class="btn btn-secondary btn-lg">
        Place Order: $<span id="placeOrderValue">0.00</span>
    </button>
    <button type="button" id="placeOnHold" class="btn btn-outline-primary btn-lg">Place Invoice on Hold</button>
    @if($draftOrderId)
        <button type="button" id="cancelDraftInvoice" class="btn btn-outline-danger">Cancel Invoice</button>
    @endif
</div>
