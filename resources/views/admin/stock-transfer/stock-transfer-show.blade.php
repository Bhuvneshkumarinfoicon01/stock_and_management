@extends('layouts.app_admin')
@section('title') Stock transfer {{ $transfer->reference_no }} @endsection

@section('content')
@component('admin.purchases.partials.document-show-layout', [
    'title' => 'Stock transfer ' . $transfer->reference_no,
    'subtitle' => format_datetime($transfer->created_at),
    'backUrl' => route('admin.stock-transfer'),
    'headerActions' => [],
    'recordNav' => $recordNav ?? null,
])
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3"><strong>From store</strong><div>{{ $storeFrom->name ?? '—' }}</div></div>
                <div class="col-md-3"><strong>To store</strong><div>{{ $storeTo->name ?? '—' }}</div></div>
                <div class="col-md-3"><strong>Released by</strong><div>{{ $transfer->rperson_from ?: '—' }}</div></div>
                <div class="col-md-3"><strong>Received by</strong><div>{{ $transfer->rperson_to ?: '—' }}</div></div>
                <div class="col-md-3"><strong>Arrival date</strong><div>{{ format_date($transfer->arrival_date) }}</div></div>
                @if($transfer->notes)
                    <div class="col-12"><strong>Notes</strong><div>{{ $transfer->notes }}</div></div>
                @endif
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5 class="mb-0">Transferred products</h5></div>
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th class="text-end">Qty</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transfer->items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td class="text-end">{{ $item->qty }}</td>
                            <td>
                                @if(is_null($item->status))
                                    Pending
                                @elseif((int) $item->status === 1)
                                    Accepted
                                @else
                                    Rejected
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endcomponent
@endsection
