{{-- Purchase invoice print preview modal --}}
<div class="modal fade modal-default" id="print-purchase-invoice" aria-labelledby="print-purchase-invoice">
    <div class="modal-dialog modal-dialog-centered modal-lg">
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
                <div class="text-center info mb-3">
                    @if($organizationInfo && $organizationInfo->logo_path)
                        <img
                            src="{{ asset('storage/' . $organizationInfo->logo_path) }}"
                            alt="{{ $organizationInfo->name }} logo"
                            style="max-height: 50px; max-width: 120px; object-fit: contain; margin-bottom: 6px;"
                        />
                    @endif
                    <h6>{{ $organizationInfo->name ?? 'Accounting and Stock Management System' }}</h6>
                </div>

                <div class="tax-invoice">
                    <h6 class="text-center">Purchase Invoice</h6>
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <div class="invoice-user-name"><span>Supplier: </span><span id="pinv-print-supplier">—</span></div>
                            <div class="invoice-user-name"><span>Purchase Invoice #: </span><span id="pinv-print-internal-no">—</span></div>
                            <div class="invoice-user-name"><span>Supplier Invoice #: </span><span id="pinv-print-supplier-no">—</span></div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <div class="invoice-user-name"><span>Store: </span><span id="pinv-print-store">—</span></div>
                            <div class="invoice-user-name"><span>Invoice Date: </span><span id="pinv-print-date">—</span></div>
                            <div class="invoice-user-name"><span>Due Date: </span><span id="pinv-print-due-date">—</span></div>
                        </div>
                    </div>
                </div>

                <table class="table table-bordered w-100 table-fit mt-3">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Tax</th>
                            <th class="text-end">Line Total</th>
                        </tr>
                    </thead>
                    <tbody id="pinv-print-lines"></tbody>
                </table>

                <table class="table table-borderless w-100 mt-2">
                    <tr><td class="text-end">Sub Total:</td><td class="text-end" style="width:140px;" id="pinv-print-subtotal">$0.00</td></tr>
                    <tr><td class="text-end">Tax:</td><td class="text-end" id="pinv-print-tax">$0.00</td></tr>
                    <tr class="fw-bold"><td class="text-end">Total:</td><td class="text-end" id="pinv-print-total">$0.00</td></tr>
                    <tr><td class="text-end">Due:</td><td class="text-end" id="pinv-print-due">$0.00</td></tr>
                </table>

                <div class="text-center invoice-bar mt-3">
                    <a href="javascript:void(0);" class="btn btn-primary no-print" id="pinvPrintButton">Print Invoice</a>
                </div>
            </div>
        </div>
    </div>
</div>
