<!DOCTYPE html>
<html>
<head>
    <title>Stock Transfer Notification</title>
</head>
<body>
    <h2>
        {{ $direction === 'incoming' ? 'Incoming Stock Transfer Alert' : 'Outgoing Stock Transfer Confirmation' }}
    </h2>

    <p>
        This is to inform you that a stock transfer has been
        {{ $direction === 'incoming' ? 'initiated to your store' : 'sent from your store' }}.
    </p>

    <p><strong>Reference No:</strong> {{ $transfer->reference_no }}</p>
    <p><strong>From:</strong> {{ $storeFrom->name }}</p>
    <p><strong>To:</strong> {{ $storeTo->name }}</p>
    <p><strong>Arrival Date:</strong> {{ format_date($transfer->arrival_date) }}</p>

    <h4>Transferred Items:</h4>
    <ul>
        @foreach($items as $item)
            <li>{{ $item->product_name }} - Qty: {{ $item->qty }}</li>
        @endforeach
    </ul>
</body>
</html>
