@php
    $isEdit = $isEdit ?? false;
    $sequenceFields = [
        ['label' => 'Sale Invoice', 'prefix' => 'invoice_prefix', 'start' => 'sale_invoice_start_number', 'default_prefix' => 'INV', 'default_start' => 1],
        ['label' => 'Purchase Invoice', 'prefix' => 'purchase_invoice_prefix', 'start' => 'purchase_invoice_start_number', 'default_prefix' => 'PINV', 'default_start' => 1],
        ['label' => 'Purchase Order', 'prefix' => 'purchase_order_prefix', 'start' => 'purchase_order_start_number', 'default_prefix' => 'PO', 'default_start' => 1],
        ['label' => 'Purchase Return', 'prefix' => 'purchase_return_prefix', 'start' => 'purchase_return_start_number', 'default_prefix' => 'PR', 'default_start' => 1],
        ['label' => 'Sale Return', 'prefix' => 'sale_return_prefix', 'start' => 'sale_return_start_number', 'default_prefix' => 'SR', 'default_start' => 1],
        ['label' => 'GRN', 'prefix' => 'grn_prefix', 'start' => 'grn_start_number', 'default_prefix' => 'GRN', 'default_start' => 1],
        ['label' => 'Credit Note', 'prefix' => 'credit_note_prefix', 'start' => 'credit_note_start_number', 'default_prefix' => 'CN', 'default_start' => 1],
        ['label' => 'Debit Note', 'prefix' => 'debit_note_prefix', 'start' => 'debit_note_start_number', 'default_prefix' => 'DN', 'default_start' => 1],
        ['label' => 'Receipt', 'prefix' => 'receipt_prefix', 'start' => 'receipt_start_number', 'default_prefix' => 'RCPT', 'default_start' => 1],
        ['label' => 'Batch Processing', 'prefix' => 'batch_purchase_prefix', 'start' => 'batch_purchase_start_number', 'default_prefix' => 'BP', 'default_start' => 1],
    ];
@endphp

<div class="border rounded p-3 mb-3 bg-light">
    <h6 class="mb-2">Document number sequences</h6>
    <p class="text-muted small mb-3">
        Set a prefix and the next number for each document type. Example: prefix <strong>AB</strong> and next <strong>2348</strong> produces <strong>AB002348</strong>, then auto-increments.
    </p>

    @foreach($sequenceFields as $field)
        <div class="row g-2 mb-2">
            <div class="col-md-4">
                <label class="form-label mb-1">{{ $field['label'] }} prefix</label>
                <input type="text"
                    name="{{ $field['prefix'] }}"
                    class="form-control"
                    placeholder="e.g. {{ $field['default_prefix'] }}"
                    value="{{ $isEdit ? '' : $field['default_prefix'] }}"
                    maxlength="32">
            </div>
            <div class="col-md-4">
                <label class="form-label mb-1">Next {{ $field['label'] }} number</label>
                <input type="number"
                    min="1"
                    name="{{ $field['start'] }}"
                    class="form-control"
                    placeholder="e.g. 2348"
                    value="{{ $isEdit ? '' : $field['default_start'] }}">
            </div>
        </div>
    @endforeach
</div>
