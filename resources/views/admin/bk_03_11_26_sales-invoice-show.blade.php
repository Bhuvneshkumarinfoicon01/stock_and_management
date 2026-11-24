@extends('layouts.app_admin')
@section('title') Sales invoice @endsection

@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header d-flex justify-content-between flex-wrap gap-2">
            <div class="page-title">
                <h4>Sales invoice {{ $invoice->invoice_number ?: ('INV/' . $invoice->id) }}</h4>
                <h6>{{ format_date($invoice->created_at) }}</h6>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.sales-invoices.index') }}" class="btn btn-outline-secondary">Back</a>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <strong>Customer</strong>
                        <div>{{ $invoice->customer_name ?: ($invoice->customer->customer_name ?? '—') }}</div>
                    </div>
                    <div class="col-md-3">
                        <strong>Store</strong>
                        <div>{{ $invoice->store->name ?? '—' }}</div>
                    </div>
                    <div class="col-md-3">
                        <strong>Invoice Type</strong>
                        <div>
                            @if((int) $invoice->invoice_type === 1)
                                Cash Sales
                            @elseif((int) $invoice->invoice_type === 2)
                                Credit Sales
                            @elseif((int) $invoice->invoice_type === 3)
                                Estimate
                            @else
                                —
                            @endif
                        </div>
                    </div>
                    <div class="col-md-3">
                        <strong>Status</strong>
                        <div>
                            @if((int) $invoice->status === 1)
                                Paid
                            @elseif((int) $invoice->status === 2)
                                Partial
                            @elseif((int) $invoice->status === 3)
                                Estimate
                            @else
                                Unpaid
                            @endif
                        </div>
                    </div>
                    <div class="col-md-3">
                        <strong>Due date</strong>
                        <div>{{ format_date($invoice->due_date) }}</div>
                    </div>
                    <div class="col-md-3">
                        <strong>Subtotal</strong>
                        <div>{{ number_format((float) $invoice->subtotal_price, 2) }}</div>
                    </div>
                    <div class="col-md-3">
                        <strong>Tax</strong>
                        <div>{{ number_format((float) ($invoice->tax_amount ?? 0), 2) }}</div>
                    </div>
                    <div class="col-md-3">
                        <strong>Total</strong>
                        <div>{{ number_format((float) $invoice->total_price, 2) }}</div>
                    </div>
                    <div class="col-md-3">
                        <strong>Paid</strong>
                        <div>{{ number_format((float) ($invoice->paid_amount ?? 0), 2) }}</div>
                    </div>
                    <div class="col-md-3">
                        <strong>Due</strong>
                        <div>{{ number_format((float) ($invoice->due_amount ?? 0), 2) }}</div>
                    </div>
                    @if(!empty($invoice->notes))
                        <div class="col-12">
                            <strong>Notes</strong>
                            <div>{{ $invoice->notes }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">Line items</h5>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Unit price</th>
                            <th class="text-end">Line tax</th>
                            <th class="text-end">Line total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoice->orderItems as $item)
                            @php
                                $qty = (float) $item->qty;
                                $unit = (float) $item->price;
                                $lineTax = (float) ($item->tax_amount ?? 0);
                                $lineTotal = ($qty * $unit) + $lineTax;
                            @endphp
                            <tr>
                                <td>{{ $item->product_name }}</td>
                                <td class="text-end">{{ number_format($qty, 2) }}</td>
                                <td class="text-end">{{ number_format($unit, 2) }}</td>
                                <td class="text-end">{{ number_format($lineTax, 2) }}</td>
                                <td class="text-end">{{ number_format($lineTotal, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No line items found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Payment history</h5>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Method</th>
                            <th>Reference</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoice->payments as $payment)
                            <tr>
                                <td>{{ format_datetime($payment->created_at) }}</td>
                                <td>{{ $payment->payment_method ?: '—' }}</td>
                                <td>{{ $payment->reference_no ?: '—' }}</td>
                                <td class="text-end">{{ number_format((float) $payment->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No payments recorded.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
