<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Stock Adjustment Notification</title>
</head>
<body>
    <h2>Stock Adjustment Made</h2>

    <p><strong>Performed By:</strong> {{ $performedBy }}</p>
    <p><strong>Store:</strong> {{ $store->name }}</p>
    <p><strong>Product:</strong> {{ $stock->product_name ?? 'N/A' }} (SKU: {{ $stock->sku ?? 'N/A' }})</p>
    <p><strong>Adjusted Quantity:</strong> {{ $adjustment->adjust_quantity }}</p>
    <p><strong>Reason:</strong> {{ $adjustment->reason }}</p>
    <p><strong>Adjustment Date:</strong> {{ format_date($adjustment->adjust_date) }}</p>
    <p><strong>Remaining Stock:</strong> {{ $stock->quantity }}</p>

    <p>Thank you.</p>
</body>
</html>
