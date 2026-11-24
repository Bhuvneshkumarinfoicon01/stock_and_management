<h2>Stock Transfer Completed</h2>

<p><strong>From Store:</strong> {{ $store_from->name }}</p>
<p><strong>To Store:</strong> {{ $store_to->name }}</p>
<p><strong>Reference No:</strong> {{ $transfer->reference_no }}</p>
<p><strong>Arrival Date:</strong> {{ format_date($transfer->arrival_date) }}</p>

<h4>Products Transferred:</h4>
<ul>
@foreach ($items as $item)
    <li>{{ $item->product_name }} (SKU: {{ $item->sku }}) - Qty: {{ $item->qty }}</li>
@endforeach
</ul>

<p><strong>Notes:</strong> {{ $transfer->notes }}</p>
