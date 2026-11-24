@extends('layouts.app_admin')
@section('title') All Sales Invoices @endsection

@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4>All Sales Invoices</h4>
                    <h6>{{ $scopeLabels[$scope] ?? 'All generated sales invoices' }}</h6>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-md-4 col-lg-3">
                        <label class="form-label" for="sales-invoice-filter">Filter by</label>
                        <select class="form-select" id="sales-invoice-filter">
                            @foreach($scopeLabels as $filterScope => $label)
                                <option value="{{ $filterScope }}" @selected($scope === $filterScope)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card table-list-card">
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                    @include('admin.partials.datatable-page-length-toolbar', [
                        'tableKey' => 'sales-invoices',
                        'toolbarId' => 'sales-invoices-dt-toolbar',
                        'datatableConfig' => $datatableConfig,
                    ])
                </div>
                <div class="table-responsive datatable-scroll-container" id="sales-invoices-scroll-wrap">
                    <table class="table datanew" id="sales-invoices-table">
                        <thead>
                            <tr>
                                <th>Invoice No</th>
                                <th>Invoice Type</th>
                                <th>Customer Name</th>
                                <th>Store Name</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Paid</th>
                                <th>Due</th>
                                <th>Due Date</th>
                                <th>Overdue</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="convert-credit-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Invoice Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="convertCreditForm">
                <div class="modal-body">
                    <input type="hidden" id="convert_credit_order_id">
                    <div class="mb-3">
                        <label class="form-label">Invoice Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="convert_invoice_type" required>
                            <option value="">Select invoice type</option>
                            @foreach($invoiceOptions as $option)
                                @if((int) $option->id !== 3)
                                    <option value="{{ $option->id }}" data-is-credit="{{ (int) $option->id === 2 ? 1 : 0 }}">{{ $option->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-0 convert-credit-only-fields" style="display:none;">
                        <label class="form-label">Payment Terms <span class="text-danger">*</span></label>
                        <select class="form-select" id="convert_payment_terms_days">
                            <option value="">Select terms</option>
                            @foreach($paymentTerms as $term)
                                <option value="{{ $term->id }}">{{ $term->label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary save-convert-btn">Save</button>
                </div>
            </form>
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
                    <input type="hidden" id="payment_customer_id">
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
@endsection

@section('scripts')
<script>
    $(function () {
        const dataUrlTemplate = @json(route('admin.sales-invoices.data', ['scope' => '__SCOPE__']));
        let currentScope = @json($scope);

        function salesInvoicesDataUrl(scope) {
            return dataUrlTemplate.replace('__SCOPE__', scope);
        }

        const salesInvoiceColumns = [
            { data: 'invoice_no', name: 'id' },
            { data: 'invoice_type_label', name: 'invoice_type', orderable: false, searchable: false },
            { data: 'customer_name', name: 'customer_name' },
            { data: 'store_name', name: 'sort_stores.name' },
            { data: 'created_at', name: 'created_at' },
            { data: 'total_price', name: 'total_price', orderable: false, searchable: false },
            { data: 'paid_amount', name: 'paid_amount', orderable: false, searchable: false },
            { data: 'due_amount', name: 'due_amount', orderable: false, searchable: false },
            { data: 'due_date', name: 'due_date' },
            { data: 'overdue', name: 'overdue', orderable: false, searchable: false },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ];

        let paginationManager = DatatablePagination.attach({
            tableKey: 'sales-invoices',
            tableSelector: '#sales-invoices-table',
            toolbarSelector: '#sales-invoices-dt-toolbar',
            scrollContainerSelector: '#sales-invoices-scroll-wrap',
            config: @json($datatableConfig),
            getAjaxUrl: function () {
                return salesInvoicesDataUrl(currentScope);
            },
            buildTableOptions: function (mode, pageLength) {
                const shared = {
                    columns: salesInvoiceColumns,
                    order: [[0, 'desc']],
                    language: {
                        emptyTable: 'No sales invoices found',
                        search: 'Search:',
                        searchPlaceholder: 'Search invoices...',
                    },
                    drawCallback: function () {
                        feather.replace();
                    },
                };

                if (mode === 'scroll') {
                    return Object.assign({}, shared, {
                        processing: true,
                        serverSide: false,
                        paging: false,
                        lengthChange: false,
                        info: false,
                        searching: true,
                        dom: 'frt',
                        data: [],
                    });
                }

                return Object.assign({}, shared, {
                    processing: true,
                    serverSide: true,
                    ajax: salesInvoicesDataUrl(currentScope),
                    pageLength: pageLength,
                    lengthChange: false,
                    searching: true,
                    dom: 'frtip',
                });
            },
        });

        $('#sales-invoice-filter').on('change', function () {
            currentScope = $(this).val();
            paginationManager.setAjaxUrl();
        });

        $(document).on('click', '.add-order-payment', function () {
            const orderId = $(this).data('id');
            const customerId = $(this).data('customer-id');
            const due = Number($(this).data('due')) || 0;

            $('#payment-order-no').text('INV/' + orderId);
            $('#payment_order_id').val(orderId);
            $('#payment_customer_id').val(customerId);
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
            const customerId = $('#payment_customer_id').val();
            const amount = Number($('#payment_amount').val() || 0);
            const due = Number(($('#payment_due_amount').val() || '0').replace(/[^0-9.]/g, '')) || 0;

            if (!orderId || !customerId) {
                Swal.fire('Error', 'Order or customer is missing.', 'error');
                return;
            }

            if (amount <= 0 || amount > due) {
                Swal.fire('Invalid amount', 'Please enter amount between 0.01 and due amount.', 'warning');
                return;
            }

            $submitBtn.prop('disabled', true).text('Saving...');

            const urlTemplate = "{{ route('admin.customer-order-payment.store', ['customer' => '__CUSTOMER__', 'order' => '__ORDER__']) }}";
            const postUrl = urlTemplate.replace('__CUSTOMER__', customerId).replace('__ORDER__', orderId);

            $.ajax({
                url: postUrl,
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                data: {
                    amount: amount,
                    payment_method: $('#payment_method').val(),
                    reference_no: $('#payment_reference_no').val()
                },
                success: function (res) {
                    if (res.success) {
                        Swal.fire('Success', res.message || 'Payment recorded successfully.', 'success');
                        $('#record-payment-modal').modal('hide');
                        paginationManager.reload(false);
                    } else {
                        Swal.fire('Error', res.message || 'Failed to record payment.', 'error');
                    }
                },
                error: function (xhr) {
                    const msg = xhr.responseJSON?.message || 'Failed to record payment.';
                    Swal.fire('Error', msg, 'error');
                },
                complete: function () {
                    $submitBtn.prop('disabled', false).text(originalBtnText);
                }
            });
        });

        $(document).on('click', '.convert-estimate-invoice', function () {
            const orderId = $(this).data('id');
            if (!orderId) {
                Swal.fire('Error', 'Invalid conversion request.', 'error');
                return;
            }

            $('#convert_credit_order_id').val(orderId);
            $('#convert_invoice_type').val('');
            $('#convert_payment_terms_days').val('');
            $('.convert-credit-only-fields').hide();
            $('#convert_payment_terms_days').prop('required', false);
            $('#convert-credit-modal').modal('show');
        });

        $('#convert_invoice_type').on('change', function () {
            const isCredit = Number($('#convert_invoice_type option:selected').data('is-credit') || 0) === 1;
            if (isCredit) {
                $('.convert-credit-only-fields').show();
                $('#convert_payment_terms_days').prop('required', true);
            } else {
                $('.convert-credit-only-fields').hide();
                $('#convert_payment_terms_days').val('');
                $('#convert_payment_terms_days').prop('required', false);
            }
        });

        function submitEstimateConversion(orderId, invoiceType, paymentTermsDays, $submitBtn, originalBtnText) {
            const urlTemplate = "{{ route('admin.credit-invoices.convert-estimate', ['order' => '__ORDER__']) }}";
            const postUrl = urlTemplate.replace('__ORDER__', orderId);

            $.ajax({
                url: postUrl,
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                data: {
                    invoice_type: invoiceType,
                    payment_terms_days: paymentTermsDays
                },
                success: function (res) {
                    if (res.success) {
                        Swal.fire('Success', res.message, 'success');
                        $('#convert-credit-modal').modal('hide');
                        paginationManager.reload(false);
                    } else {
                        Swal.fire('Error', res.message || 'Conversion failed.', 'error');
                    }
                },
                error: function (xhr) {
                    const msg = xhr.responseJSON?.message || 'Conversion failed.';
                    Swal.fire('Error', msg, 'error');
                },
                complete: function () {
                    if ($submitBtn && $submitBtn.length) {
                        $submitBtn.prop('disabled', false).text(originalBtnText);
                    }
                }
            });
        }

        $('#convertCreditForm').on('submit', function (e) {
            e.preventDefault();
            const $submitBtn = $(this).find('.save-convert-btn');
            const originalBtnText = $submitBtn.text();

            const orderId = $('#convert_credit_order_id').val();
            const invoiceType = $('#convert_invoice_type').val();
            const paymentTermsDays = $('#convert_payment_terms_days').val();
            const isCredit = Number($('#convert_invoice_type option:selected').data('is-credit') || 0) === 1;

            if (!orderId || !invoiceType) {
                Swal.fire('Error', 'Please select invoice type.', 'error');
                return;
            }

            if (isCredit && !paymentTermsDays) {
                Swal.fire('Error', 'Payment terms are required for Credit Sales.', 'error');
                return;
            }

            $submitBtn.prop('disabled', true).text('Saving...');
            submitEstimateConversion(orderId, invoiceType, isCredit ? paymentTermsDays : null, $submitBtn, originalBtnText);
        });
    });
</script>
@endsection
