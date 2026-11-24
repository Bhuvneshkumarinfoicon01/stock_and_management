<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Stock Transfer Status Update</title>
</head>
<body>
    <h2>Stock Transfer Status Updated</h2>

    <p><strong>Reference No:</strong> {{ $transfer->reference_no }}</p>
    <p><strong>Status:</strong> {{ $statusText }}</p>
    <p><strong>From Store:</strong> {{ $storeFrom->name ?? 'N/A' }}</p>
    <p><strong>To Store:</strong> {{ $storeTo->name ?? 'N/A' }}</p>
    <p><strong>Arrival Date:</strong> {{ format_date($transfer->arrival_date) }}</p>

    <h3>Transferred Products</h3>
    <ul>
        @foreach ($items as $item)
            <li>{{ $item->product_name }} — Qty: {{ $item->qty }}</li>
        @endforeach
    </ul>

    <p>Thank you.</p>
</body>
</html>
