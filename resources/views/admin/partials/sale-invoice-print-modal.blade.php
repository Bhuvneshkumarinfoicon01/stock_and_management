{{-- Shared sale invoice print preview modal (used by invoice list, generate invoice, etc.) --}}
<div class="modal fade modal-default" id="print-receipt" aria-labelledby="print-receipt">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="d-flex justify-content-end">
                <button type="button" class="close p-0 no-print" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                @php
                    $organizationInfo = \App\Models\OrganizationInfo::first();
                @endphp
                <div class="text-center info">
                    @if($organizationInfo && $organizationInfo->logo_path)
                        <img
                            src="{{ asset('storage/' . $organizationInfo->logo_path) }}"
                            alt="{{ $organizationInfo->name }} logo"
                            style="max-height: 50px; max-width: 120px; object-fit: contain; margin-bottom: 6px;"
                        />
                    @endif
                    <h6>{{ $organizationInfo->name ?? 'Accounting and Stock Management System' }},</h6>
                    <p class="mb-0">Phone Number: {{ $organizationInfo->phone ?? '—' }}</p>
                    <p class="mb-0">
                        Email:
                        @if(!empty($organizationInfo->email))
                            <a href="mailto:{{ $organizationInfo->email }}">{{ $organizationInfo->email }}</a>
                        @else
                            <span>—</span>
                        @endif
                    </p>
                </div>

                <div class="tax-invoice">
                    <h6 class="text-center">Tax Invoice</h6>
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <div class="invoice-user-name">
                                <span>Name: </span><span></span>
                            </div>
                            <div class="invoice-user-name">
                                <span>Invoice No: </span><span></span>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <div class="invoice-user-name">
                                <span>Customer Id: </span><span></span>
                            </div>
                            <div class="invoice-user-name">
                                <span>Date: </span><span></span>
                            </div>
                            <div class="invoice-user-name" id="print-due-date-row">
                                <span>Due Date: </span><span>-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <table class="table-borderless w-100 table-fit">
                    <thead>
                        <tr>
                            <th># Item</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

                <div class="text-center invoice-bar">
                    @include('admin.partials.invoice-footer-message', ['organizationInfo' => $organizationInfo])
                    <a href="javascript:void(0);" class="btn btn-primary no-print" id="printButton">Print Receipt</a>
                </div>
            </div>
        </div>
    </div>
</div>
