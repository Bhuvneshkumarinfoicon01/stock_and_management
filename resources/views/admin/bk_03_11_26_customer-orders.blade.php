@extends('layouts.app_admin')
@section('title') Customer Orders @endsection
@section('styles') @endsection

@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4>Customer Orders</h4>
                    <h6>{{ $customer->customer_name }} (ID: {{ $customer->id }})</h6>
                </div>
            </div>
            <div class="page-btn">
                <a href="{{ route('admin.customer') }}" class="btn btn-added">
                    <i data-feather="arrow-left" class="me-2"></i>Back to Customers
                </a>
            </div>
        </div>

        <div class="card table-list-card">
            <div class="card-body">
                <div class="table-top">
                    <div class="search-set">
                        <div class="search-input d-flex align-items-center">
                            <div id="custom-search" class="me-2"></div>
                            <a href="#" class="btn btn-searchset">
                                <i data-feather="search" class="feather-search"></i>
                            </a>
                        </div>
                    </div>
                    <div class="form-sort">
                        <i data-feather="sliders" class="info-img"></i>
                        <select class="select" id="sort-order">
                            <option value="">Sort by</option>
                            <option value="desc">Newest First</option>
                            <option value="asc">Oldest First</option>
                        </select>
                    </div>
                </div>

                <div class="table-responsive product-list">
                    <table class="table datanew" id="customer-orders-table">
                        <thead>
                            <tr>
                                <th>Invoice No</th>
                                <th>Store</th>
                                <th>Date</th>
                                <th>Invoice Type</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Paid</th>
                                <th>Due</th>
                                <th>Due Date</th>
                                <th>Payment</th>
                                <th>Status</th>
                                <th class="no-sort">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="order-details-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order Details (<span id="details-order-no">-</span>)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody id="order-details-body"></tbody>
                    </table>
                </div>
                <hr>
                <h6 class="mb-2">Ledger Details</h6>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Debit</th>
                                <th>Credit</th>
                                <th>Balance</th>
                            </tr>
                        </thead>
                        <tbody id="order-ledger-body"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="record-payment-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Record Payment (<span id="payment-order-no">-</span>)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="recordPaymentForm">
                <div class="modal-body">
                    <input type="hidden" id="payment_order_id">
                    <div class="mb-3">
                        <label class="form-label">Due Amount</label>
                        <input type="text" class="form-control" id="payment_due_amount" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount</label>
                        <input type="number" class="form-control" id="payment_amount" step="0.01" min="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <input type="text" class="form-control" id="payment_method" placeholder="Cash / Bank / UPI">
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Reference No (Optional)</label>
                        <input type="text" class="form-control" id="payment_reference_no">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary save-payment-btn">Save Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="change-invoice-type-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Invoice Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="changeInvoiceTypeForm">
                <div class="modal-body">
                    <input type="hidden" id="change_invoice_order_id">
                    <div class="mb-3">
                        <label class="form-label">Invoice Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="change_invoice_type" required>
                            <option value="">Select invoice type</option>
                            @foreach($invoiceOptions as $id => $name)
                                @if((int) $id !== 3)
                                    <option value="{{ $id }}" data-is-credit="{{ (int) $id === 2 ? 1 : 0 }}">{{ $name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-0 change-credit-only-fields" style="display:none;">
                        <label class="form-label">Payment Terms <span class="text-danger">*</span></label>
                        <select class="form-select" id="change_payment_terms_days">
                            <option value="">Select terms</option>
                            @foreach($paymentTerms as $term)
                                <option value="{{ $term->id }}">{{ $term->label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary change-invoice-type-submit-btn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        const table = $('#customer-orders-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.customer-orders.data', $customer->id) }}",
                data: function (d) {
                    d.order_type = $('#sort-order').val();
                }
            },
            columns: [
                { data: 'order_id', name: 'id' },
                { data: 'store_name', name: 'store.name' },
                { data: 'created_at', name: 'created_at' },
                { data: 'invoice_type', name: 'invoice_type' },
                { data: 'items', name: 'items', orderable: false, searchable: false },
                { data: 'total_price', name: 'total_price' },
                { data: 'paid_amount', name: 'paid_amount' },
                { data: 'due_amount', name: 'due_amount' },
                { data: 'due_date', name: 'due_date' },
                { data: 'payment_method', name: 'payment_method' },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            pageLength: 10,
            drawCallback: function () {
                feather.replace();
            },
            initComplete: function () {
                const searchWrapper = $('#customer-orders-table_filter');
                const defaultSearchBox = searchWrapper.find('input');
                $('#custom-search').append(defaultSearchBox);
                defaultSearchBox.attr('placeholder', 'Search...');
                searchWrapper.remove();
            }
        });

        $('#sort-order').on('change', function () {
            table.ajax.reload();
        });

        $(document).on('click', '.view-order-details', function () {
            const orderId = $(this).data('id');
            $('#details-order-no').text('INV/' + orderId);
            const $tbody = $('#order-details-body');
            const $ledgerBody = $('#order-ledger-body');
            $tbody.html('<tr><td colspan="5" class="text-center">Loading...</td></tr>');
            $ledgerBody.html('<tr><td colspan="6" class="text-center">Loading...</td></tr>');

            $.ajax({
                url: "{{ route('admin.get-orderItems', '') }}/" + orderId,
                type: "GET",
                success: function (response) {
                    $tbody.empty();
                    if (response?.invoice_no) {
                        $('#details-order-no').text(response.invoice_no);
                    }

                    if (!response.items || response.items.length === 0) {
                        $tbody.html('<tr><td colspan="5" class="text-center">No items found.</td></tr>');
                    } else {
                        $.each(response.items, function (_, item) {
                            const qty = Number(item.qty) || 0;
                            const price = Number(item.price) || 0;
                            const total = qty * price;

                            $tbody.append(`
                                <tr>
                                    <td>${item.product_name ?? '-'}</td>
                                    <td>${item.sku ?? '-'}</td>
                                    <td>${qty}</td>
                                    <td>$${price.toFixed(2)}</td>
                                    <td>$${total.toFixed(2)}</td>
                                </tr>
                            `);
                        });
                    }

                    $ledgerBody.empty();
                    if (!response.ledger_entries || response.ledger_entries.length === 0) {
                        $ledgerBody.html('<tr><td colspan="6" class="text-center">No ledger entries found.</td></tr>');
                    } else {
                        $.each(response.ledger_entries, function (_, entry) {
                            $ledgerBody.append(`
                                <tr>
                                    <td>${entry.entry_date ?? '-'}</td>
                                    <td>${entry.entry_type ?? '-'}</td>
                                    <td>${entry.description ?? '-'}</td>
                                    <td>$${(Number(entry.debit) || 0).toFixed(2)}</td>
                                    <td>$${(Number(entry.credit) || 0).toFixed(2)}</td>
                                    <td>$${(Number(entry.balance_after) || 0).toFixed(2)}</td>
                                </tr>
                            `);
                        });
                    }

                    $('#order-details-modal').modal('show');
                },
                error: function () {
                    $tbody.html('<tr><td colspan="5" class="text-center text-danger">Failed to load order details.</td></tr>');
                    $ledgerBody.html('<tr><td colspan="6" class="text-center text-danger">Failed to load ledger details.</td></tr>');
                    $('#order-details-modal').modal('show');
                }
            });
        });

        $(document).on('click', '.add-order-payment', function () {
            const orderId = $(this).data('id');
            const due = Number($(this).data('due')) || 0;
            $('#payment-order-no').text('INV/' + orderId);
            $('#payment_order_id').val(orderId);
            $('#payment_due_amount').val('$' + due.toFixed(2));
            $('#payment_amount').val(due > 0 ? due.toFixed(2) : '');
            $('#payment_method').val('');
            $('#payment_reference_no').val('');
            $('#record-payment-modal').modal('show');
        });

        $('#recordPaymentForm').on('submit', function (e) {
            e.preventDefault();
            const $submitBtn = $(this).find('.save-payment-btn');
            const originalBtnText = $submitBtn.text();
            const orderId = $('#payment_order_id').val();
            const urlTemplate = "{{ route('admin.customer-order-payment.store', ['customer' => $customer->id, 'order' => '__ORDER__']) }}";

            $submitBtn.prop('disabled', true).text('Saving...');

            $.ajax({
                url: urlTemplate.replace('__ORDER__', orderId),
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    amount: $('#payment_amount').val(),
                    payment_method: $('#payment_method').val(),
                    reference_no: $('#payment_reference_no').val(),
                },
                success: function (response) {
                    $('#record-payment-modal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message || 'Payment saved.',
                    });
                    table.ajax.reload(null, false);
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Payment failed',
                        text: xhr.responseJSON?.message || 'Unable to save payment.',
                    });
                },
                complete: function () {
                    $submitBtn.prop('disabled', false).text(originalBtnText);
                }
            });
        });

        $(document).on('click', '.change-order-invoice-type', function () {
            const orderId = $(this).data('id');
            if (!orderId) {
                Swal.fire('Error', 'Invalid conversion request.', 'error');
                return;
            }

            $('#change_invoice_order_id').val(orderId);
            $('#change_invoice_type').val('');
            $('#change_payment_terms_days').val('');
            $('.change-credit-only-fields').hide();
            $('#change_payment_terms_days').prop('required', false);
            $('#change-invoice-type-modal').modal('show');
        });

        $('#change_invoice_type').on('change', function () {
            const isCredit = Number($('#change_invoice_type option:selected').data('is-credit') || 0) === 1;
            if (isCredit) {
                $('.change-credit-only-fields').show();
                $('#change_payment_terms_days').prop('required', true);
            } else {
                $('.change-credit-only-fields').hide();
                $('#change_payment_terms_days').val('');
                $('#change_payment_terms_days').prop('required', false);
            }
        });

        $('#changeInvoiceTypeForm').on('submit', function (e) {
            e.preventDefault();
            const $submitBtn = $(this).find('.change-invoice-type-submit-btn');
            const originalBtnText = $submitBtn.text();

            const orderId = $('#change_invoice_order_id').val();
            const invoiceType = $('#change_invoice_type').val();
            const paymentTermsDays = $('#change_payment_terms_days').val();
            const isCredit = Number($('#change_invoice_type option:selected').data('is-credit') || 0) === 1;

            if (!orderId || !invoiceType) {
                Swal.fire('Error', 'Please select invoice type.', 'error');
                return;
            }

            if (isCredit && !paymentTermsDays) {
                Swal.fire('Error', 'Payment terms are required for Credit Sales.', 'error');
                return;
            }

            $submitBtn.prop('disabled', true).text('Saving...');

            const urlTemplate = "{{ route('admin.credit-invoices.convert-estimate', ['order' => '__ORDER__']) }}";
            const postUrl = urlTemplate.replace('__ORDER__', orderId);

            $.ajax({
                url: postUrl,
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                data: {
                    invoice_type: invoiceType,
                    payment_terms_days: isCredit ? paymentTermsDays : null
                },
                success: function (res) {
                    if (res.success) {
                        Swal.fire('Success', res.message || 'Invoice updated successfully.', 'success');
                        $('#change-invoice-type-modal').modal('hide');
                        table.ajax.reload(null, false);
                    } else {
                        Swal.fire('Error', res.message || 'Conversion failed.', 'error');
                    }
                },
                error: function (xhr) {
                    const msg = xhr.responseJSON?.message || 'Conversion failed.';
                    Swal.fire('Error', msg, 'error');
                },
                complete: function () {
                    $submitBtn.prop('disabled', false).text(originalBtnText);
                }
            });
        });
    });
</script>
@endsection
