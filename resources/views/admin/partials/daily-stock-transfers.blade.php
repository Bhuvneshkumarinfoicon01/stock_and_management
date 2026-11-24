<!-- resources/views/partials/stock-transfers.blade.php -->
@foreach ($stockTransfers as $stockTransfer)
    @foreach ($stockTransfer->items as $item)
        <tr>
            <td>{{ $stockTransfer->storeFrom->name }}</td>
            <td>{{ $stockTransfer->storeTo->name }}</td>
            <td>{{ $item->product_name }}</td>
            <td>{{ $item->qty }}</td>
        </tr>
    @endforeach
@endforeach
