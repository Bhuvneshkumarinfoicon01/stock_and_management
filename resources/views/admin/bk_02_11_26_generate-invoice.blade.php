@extends('layouts.app_admin')
@section('title') Generate Invoice @endsection
{{-- Layout yields @section('style') — not 'styles' (old name kept commented). --}}
@section('style')
<style>
    #product_list {
        max-height: 200px;
        overflow-y: auto;
        position: absolute;
        z-index: 1000;
        width: 100%;
        display: none;
        background: #fff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .payment-option.selected {
        border: 1px solid #ff9f43;
        border-radius: 6px;
        background: #fff7ed;
    }
</style>
@endsection

@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4>Generate Invoice</h4>
                    <h6>Create a new invoice using the same flow as Invoice Management.</h6>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.invoice-order') }}" id="transferForm">
                    @csrf
                    @php
                        $selectedStore = optional($holdOrder)->store_id ?? session('selected_store_id') ?? ($user->selected_store_id ?? null);
                    @endphp
                    @if(!empty($holdOrder))
                        <div class="alert alert-warning mb-3">
                            Editing draft <strong>{{ $holdOrder->draft_reference ?? ('#' . $holdOrder->id) }}</strong>
                            @if($holdOrder->credit_limit_blocked)
                                — held because customer credit limit was exceeded.
                            @endif
                        </div>
                        <input type="hidden" id="credit_limit_blocked_flag" value="{{ $holdOrder->credit_limit_blocked ? '1' : '0' }}">
                    @endif
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="input-blocks">
                                <label>Select Store</label>
                                <select name="store_from" id="store_from" class="form-control select">
                                    @foreach ($stores as $store)
                                        <option value="{{ $store->id }}" @selected($store->id == $selectedStore)>{{ $store->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-blocks">
                                <label>Customer</label>
                                <div class="input-block d-flex align-items-center mt-2">
                                    <div class="flex-grow-1 me-2">
                                        <select class="select" name="customer_id" id="customer-select">
                                            <option value="">Choose Customer</option>
                                            @foreach($customers as $customer)
                                                <option value="{{$customer->id}}">{{$customer->customer_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    {{-- Old behavior from invoice page opened create-customer modal. --}}
                                    {{--<a href="#" class="btn btn-primary btn-icon open-create-modal">
                                        <i data-feather="user-plus" class="feather-16"></i>
                                    </a>--}}
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="input-blocks">
                                <label>Invoice Type</label>
                                <select name="invoice_type" id="invoice_type" class="select">
                                    <option value="">Select Invoice Type</option>
                                    @foreach($invoice_options as $key => $option)
                                        <option value="{{ $key }}">{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="input-blocks">
                                <label>Order Date *</label>
                                <input type="text" name="order_date" id="order_date" class="form-control datetimepicker" placeholder="Choose Date" value="{{ format_date(now(), '') }}" required>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="tax_exempt" id="invoice_tax_exempt" value="1">
                                <label class="form-check-label" for="invoice_tax_exempt">
                                    Entire invoice is non-taxable (tax-exempt customer)
                                </label>
                            </div>
                            <small class="text-muted d-block mb-3">When checked, no line tax is applied regardless of product settings.</small>
                            {{--<small class="text-muted d-block mb-3">When checked, no line tax is applied regardless of product settings. Unchecking restores each line to the product's default tax status.</small>--}}
                        </div>

                        <div class="col-lg-12 credit-only-fields" style="display:none;">
                            <div class="input-blocks">
                                <label>Payment Terms (Days)</label>
                                {{-- Old hardcoded options kept replaced by DB terms --}}
                                <select name="payment_terms_days" id="payment_terms_days" class="select">
                                    <option value="">Select Payment Terms</option>
                                    @foreach($paymentTerms as $term)
                                        <option value="{{ $term->id }}">{{ $term->label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="input-blocks search-form mb-3 position-relative">
                                <label>Product</label>
                                <input type="text" id="product_search" class="form-control" placeholder="Search Product by Name or SKU" />
                                {{-- Match invoice.blade.php: fixed-height dropdown with scroll (not full-page list). --}}
                                <ul id="product_list" class="list-group"
                                    style="max-height: 200px; overflow-y: auto; position: absolute; z-index: 1000; width: 100%; display: none;"></ul>
                                <i data-feather="search" class="feather-search"></i>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <table class="table datanew">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>SKU</th>
                                            <th>Category</th>
                                            <th>Qty</th>
                                            <th>Price</th>
                                            <th class="line-tax-col">Taxable</th>
                                            <th class="line-tax-col">Rate %</th>
                                            <th class="line-tax-col">Line tax</th>
                                            <th class="no-sort">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="product-tbody"></tbody>
                                </table>
                            </div>
                        </div>

                        {{--<div class="col-lg-6">
                            <div class="input-blocks">
                                <label>Order Tax</label>
                                <select name="tax" id="taxDropdown" class="select">
                                    <option value="0">No Tax</option>
                                    <option value="5">5%</option>
                                    <option value="10">10%</option>
                                    <option value="15">15%</option>
                                    <option value="20">20%</option>
                                    <option value="25">25%</option>
                                    <option value="30">30%</option>
                                </select>
                            </div>
                        </div>

                        @php $currentUser = auth()->user(); @endphp
                        @if($currentUser && in_array($currentUser->role_id, [1,2,3]))
                            <div class="col-lg-6">
                                <div class="input-blocks">
                                    <label>Discount</label>
                                    <select name="discount" id="discountDropdown" class="select">
                                        <option value="0">No Discount</option>
                                        <option value="5">5%</option>
                                        <option value="10">10%</option>
                                        <option value="15">15%</option>
                                        <option value="20">20%</option>
                                        <option value="25">25%</option>
                                        <option value="30">30%</option>
                                    </select>
                                </div>
                            </div>
                        @else
                            <div class="col-lg-6">
                                <div class="input-blocks">
                                    <label>Discount</label>
                                    <select name="discount" id="discountDropdown" class="select">
                                        <option value="0">No Discount</option>
                                        @for ($i = 5; $i <= (int) ($discount_percentage ?? 0); $i += 5)
                                            <option value="{{ $i }}">{{ $i }}%</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        @endif--}}

                        <input type="hidden" name="discount" id="discountDropdown" value="0">

                        <div class="col-lg-6 mt-4">
                            <table class="table table-borderless">
                                <tr><td>Sub Total</td><td class="text-end" id="sub-total">$0.00</td></tr>
                                <tr><td id="tax_percentage">Tax (GST 0%)</td><td class="text-end" id="tax">$0.00</td></tr>
                                <tr><td id="discount_percentage">Discount (0%)</td><td class="text-end" id="discount">$0.00</td></tr>
                                <tr><td>Total</td><td class="text-end" id="total">$0.00</td></tr>
                            </table>
                        </div>

                        <div class="col-lg-6 mt-4">
                            <div class="block-section payment-method">
                                <h6>Payment Method</h6>
                                <div class="row d-flex align-items-center justify-content-center methods">
                                    <input type="hidden" name="payment_method" id="payment_method" />
                                    <div class="col-md-4 item"><a href="javascript:void(0);" class="payment-option" data-method="Cash"><span><i data-feather="dollar-sign" class="me-1"></i>Cash</span></a></div>
                                    <div class="col-md-4 item"><a href="javascript:void(0);" class="payment-option" data-method="Card"><span><i data-feather="credit-card" class="me-1"></i>Debit Card</span></a></div>
                                    <div class="col-md-4 item"><a href="javascript:void(0);" class="payment-option" data-method="Scan"><span>Scan</span></a></div>
                                </div>
                            </div>
                            @include('admin.partials.invoice-placement-buttons', ['draftOrderId' => $holdOrder->id ?? null])
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@include('admin.partials.sale-invoice-print-modal')
@endsection

@section('scripts')
@include('admin.partials.invoice-per-line-tax-scripts')
@include('admin.partials.sale-invoice-print-scripts')
<script>
    function calculateTotals() {
        if (window.InvoicePerLineTax) {
            window.InvoicePerLineTax.calculateTotals();
        }
    }

    function addProductToTable(productId, productName, sku, category, availableQty, price, categoryId, lineQty, isTaxable, taxRate) {
        let isDuplicate = false;
        $("#product-tbody tr").each(function () {
            if ($(this).find('input[name="product_id[]"]').val() == productId) {
                isDuplicate = true;
                return false;
            }
        });
        if (isDuplicate) {
            Swal.fire({ icon: "warning", title: "Duplicate Product!", text: "This product is already added to the list." });
            return;
        }
        const stockMax = parseInt(availableQty, 10) || 99999;
        const qtyVal = lineQty != null && lineQty !== '' ? parseFloat(lineQty) : (stockMax > 0 ? 1 : 1);
        const maxQty = Math.max(stockMax, Math.ceil(qtyVal));
        const safeName = $('<div>').text(productName).html();
        const safeSku = $('<div>').text(sku).html();
        const safeCategory = $('<div>').text(category).html();
        const taxable = isTaxable !== false && isTaxable !== 0 && isTaxable !== '0';
        const rate = taxRate != null && taxRate !== '' ? parseFloat(taxRate) : 0;
        const checked = taxable ? 'checked' : '';
        const newRow = `
            <tr>
                <td><input type="hidden" name="product_id[]" value="${productId}" />
                    <input type="text" name="product_name[]" class="form-control" value="${safeName}" readonly /></td>
                <td><input type="text" name="sku[]" class="form-control" value="${safeSku}" readonly /></td>
                <td><input type="hidden" name="category_id[]" value="${categoryId}" />
                    <input type="text" name="category[]" class="form-control" value="${safeCategory}" readonly /></td>
                <td>
                    <input type="number" name="qty[]" class="form-control product-qty mt-2" value="${qtyVal}" min="1" max="${maxQty}" data-available="${maxQty}" />
                    <small class="available-qty">Available Qty: ${maxQty}</small>
                </td>
                <td><input type="text" name="price[]" class="form-control mt-2" value="${price}"/></td>
                <td class="line-tax-col">
                    <input type="hidden" name="line_is_taxable[]" class="line-taxable-val" value="${taxable ? 1 : 0}">
                    <input type="checkbox" class="form-check-input line-taxable-cb" ${checked}>
                </td>
                <td class="line-tax-col">
                    <input type="number" step="0.01" min="0" max="100" name="line_tax_rate[]" class="form-control form-control-sm line-tax-rate" value="${rate}">
                </td>
                <td class="line-tax-col line-tax-amount text-end">$0.00</td>
                <td><a href="javascript:void(0);" class="btn btn-danger btn-sm delete-row mt-2">Delete</a></td>
            </tr>`;
        const $row = $(newRow);
        $("#product-tbody").append($row);
        if (window.InvoicePerLineTax) {
            window.InvoicePerLineTax.setRowDefaultTax($row, isTaxable, taxRate);
            window.InvoicePerLineTax.syncLineTaxUi($row);
        }
        calculateTotals();
    }

    function fetchProducts(storeId, query) {
        $.ajax({
            url: "{{ route('admin.get-products-invoice') }}",
            method: "GET",
            data: { store_id: storeId, query: query },
            success: function (response) {
                $("#product_list").empty().show();
                if (response.length === 0) {
                    $("#product_list").append('<li class="list-group-item">No products found</li>');
                } else {
                    $.each(response, function (_, product) {
                        $("#product_list").append(`
                            <li class="list-group-item product-item"
                                data-id="${product.product_id}"
                                data-name="${product.product_name}"
                                data-sku="${product.sku}"
                                data-category="${product.category_name}"
                                data-category_id="${product.category_id}"
                                data-qty="${product.total_quantity}"
                                data-price="${product.price}"
                                data-is_taxable="${product.is_taxable ? 1 : 0}"
                                data-tax_rate="${product.tax_rate != null ? product.tax_rate : 0}">
                                ${product.product_name} - SKU: ${product.sku} - Qty: ${product.total_quantity}
                            </li>`);
                    });
                }
            },
            error: function () {
                Swal.fire({ icon: "error", title: "Error!", text: "Failed to load products." });
            }
        });
    }

    $(function () {
        $("#product_search").on("focus keyup", function () {
            const storeId = $("#store_from").val();
            if (storeId === "") {
                Swal.fire({ icon: "warning", title: "Select Store!", text: "Please select a store first." });
                return;
            }
            fetchProducts(storeId, $(this).val());
        });

        $(document).on("click", ".product-item", function () {
            addProductToTable(
                $(this).data("id"),
                $(this).data("name"),
                $(this).data("sku"),
                $(this).data("category"),
                $(this).data("qty"),
                $(this).data("price"),
                $(this).data("category_id"),
                null,
                $(this).data("is_taxable") == 1,
                $(this).data("tax_rate")
            );
            $("#product_list").empty().hide();
            $("#product_search").val("");
        });

        $(document).on("input", ".product-qty", function () {
            const enteredQty = parseInt($(this).val());
            const availableQty = parseInt($(this).data("available"));
            if (enteredQty > availableQty) {
                Swal.fire({ icon: "error", title: "Quantity Exceeded!", text: `Available quantity is only ${availableQty}.` });
                $(this).val(availableQty);
            }
            calculateTotals();
        });

        $(document).on("input", 'input[name="price[]"]', calculateTotals);
        // Old global tax dropdown:
        // $("#taxDropdown, #discountDropdown").on("change", calculateTotals);
        $("#discountDropdown").on("change", calculateTotals);
        if (window.InvoicePerLineTax) {
            window.InvoicePerLineTax.bindEvents();
            window.InvoicePerLineTax.syncAllLineTaxUi();
        }
        $("#store_from").on("change", function () { $("#product_list").empty().hide(); $("#product_search").val(""); });
        $(document).on("click", ".delete-row", function () { $(this).closest("tr").remove(); calculateTotals(); });
        $(document).on("click", function (e) { if (!$(e.target).closest("#product_search, #product_list").length) { $("#product_list").hide(); } });

        $('#invoice_type').on('change', function () {
            const invoiceTypeId = Number($('#invoice_type').val() || 0);
            $('.credit-only-fields').toggle(invoiceTypeId === 2);
        });

        $(document).on('click', '.payment-option', function () {
            $('.payment-option').removeClass('selected');
            $(this).addClass('selected');
            $('#payment_method').val($(this).data('method'));
        });

        const holdOrderPayload = @json($holdOrderPayload);
        if (holdOrderPayload) {
            function applyHoldOrderPayload() {
                $('#store_from').val(String(holdOrderPayload.store_id)).trigger('change');
                $('#customer-select').val(String(holdOrderPayload.customer_id)).trigger('change');
                $('#invoice_type').val(String(holdOrderPayload.invoice_type)).trigger('change');
                if (holdOrderPayload.payment_terms_days) {
                    $('#payment_terms_days').val(String(holdOrderPayload.payment_terms_days)).trigger('change');
                }
                // Old global tax: $('#taxDropdown').val(...).trigger('change');
                $('#invoice_tax_exempt').prop('checked', !!holdOrderPayload.tax_exempt);
                $('#discountDropdown').val(String(Math.round(parseFloat(holdOrderPayload.discount) || 0))).trigger('change');
                if (holdOrderPayload.payment_method) {
                    $('#payment_method').val(holdOrderPayload.payment_method);
                    $('.payment-option').removeClass('selected');
                    $('.payment-option[data-method="' + holdOrderPayload.payment_method + '"]').addClass('selected');
                }
                const invoiceTypeId = Number(holdOrderPayload.invoice_type || 0);
                $('.credit-only-fields').toggle(invoiceTypeId === 2);
                $('#product-tbody').empty();
                (holdOrderPayload.lines || []).forEach(function (line) {
                    addProductToTable(
                        line.product_id,
                        line.product_name,
                        line.sku,
                        line.category_name,
                        line.available_qty,
                        line.price,
                        line.category_id,
                        line.qty,
                        line.is_taxable,
                        line.tax_rate
                    );
                    const $lastRow = $('#product-tbody tr').last();
                    if (window.InvoicePerLineTax && $lastRow.length) {
                        window.InvoicePerLineTax.setRowDefaultTax(
                            $lastRow,
                            line.default_is_taxable != null ? line.default_is_taxable : line.is_taxable,
                            line.default_tax_rate != null ? line.default_tax_rate : line.tax_rate
                        );
                    }
                });
                if (window.InvoicePerLineTax) {
                    window.InvoicePerLineTax.syncAllLineTaxUi();
                }
                calculateTotals();
            }
            applyHoldOrderPayload();
            setTimeout(applyHoldOrderPayload, 300);
        }
    });
</script>
@include('admin.partials.invoice-placement-scripts', ['draftOrderId' => $holdOrder->id ?? null])
@endsection
