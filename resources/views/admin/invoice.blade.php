@extends('layouts.app_admin') @section('title') Dashbaord @endsection
@section('styles') @endsection @section('content')


<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    {{--<h4>Invoice Report</h4>
                    <h6>Manage Your Invoice Report</h6>--}}
                    <h4>Invoice</h4>
                    <h6>Manage Your Invoice</h6>
                </div>
            </div>
            <ul class="table-top-head">
                <!-- <li>
                    <a
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="Pdf"
                        ><img
                            src="https://dreamspos.dreamstechnologies.com/html/template/assets/img/icons/pdf.svg"
                            alt="img"
                    /></a>
                </li> -->
                <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Excel"
                        href="{{ route('admin.invoices.export', request()->all()) }}"><img
                            src="{{asset('admin/assets/img/icons/excel.svg')}}" alt="img" /></a>
                </li>
                <!-- <li>
                    <a
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="Print"
                        ><i
                            data-feather="printer"
                            class="feather-rotate-ccw"
                        ></i
                    ></a>
                </li> -->
                <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Refresh" id="refresh-page"><i
                            data-feather="rotate-ccw" class="feather-rotate-ccw"></i></a>
                </li>
                <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Collapse" id="collapse-header"><i
                            data-feather="chevron-up" class="feather-chevron-up"></i></a>
                </li>
            </ul>
            @php
            $user=auth()->user();
            @endphp
            @if($user->role_id != 5)
            @if(hasPermission('create', 'order'))
            <div class="page-btn">
                <button href="#" class="btn btn-added" data-bs-toggle="modal" data-bs-target="#add-units">
                    <i data-feather="plus-circle" class="me-2"></i>Generate Invoice
                </button>
            </div>
            @endif
            @else
            <div class="page-btn">
                <button href="#" class="btn btn-added" data-bs-toggle="modal" data-bs-target="#add-units">
                    <i data-feather="plus-circle" class="me-2"></i>Generate Invoice
                </button>
            </div>
            @endif
        </div>

        <div class="card table-list-card">
            <div class="card-body">
                <div class="table-top">
                    <div class="search-set">
                        <div class="search-input d-flex align-items-center">
                            <div id="custom-search" class="me-2"></div> <!-- DataTable input will be moved here -->
                            <a href="#" class="btn btn-searchset">
                                <i data-feather="search" class="feather-search"></i>
                            </a>
                        </div>
                    </div>
                    <!-- <div class="search-path">
                        <div class="d-flex align-items-center">
                            <a class="btn btn-filter" id="filter_search">
                                <i
                                    data-feather="filter"
                                    class="filter-icon"
                                ></i>
                                <span
                                    ><img
                                        src="https://dreamspos.dreamstechnologies.com/html/template/assets/img/icons/closes.svg"
                                        alt="img"
                                /></span>
                            </a>
                        </div>
                    </div> -->
                    <div class="form-sort">
                        <i data-feather="sliders" class="info-img"></i>
                        <select class="select" id="sort-order">
                            <option value="">Sort by</option>
                            <option value="desc">Newest First</option>
                            <option value="asc">Oldest First</option>
                        </select>
                    </div>
                </div>
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                    @include('admin.partials.datatable-page-length-toolbar', [
                        'tableKey' => 'invoice-management',
                        'toolbarId' => 'invoice-management-dt-toolbar',
                        'datatableConfig' => $datatableConfig,
                    ])
                </div>
                <!-- /Filter -->
                @php
                $selectedStore = $user->selected_store_id;
                @endphp
                <div class="card" id="filter_inputs">
                    <div class="card-body pb-0">
                        <div class="row">
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="input-blocks">
                                    <i data-feather="user" class="info-img"></i>
                                    <select class="select" id="filter_store">
                                        @foreach($stores as $store)
                                        @if($store->id == $selectedStore)
                                        <option value="{{ $store->id }}" selected>{{ $store->name }}</option>
                                        @endif
                                        @endforeach
                                        <option value="choose">All Stores</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-2 col-sm-6 col-12">
                                <div class="input-blocks">
                                    <i data-feather="calendar" class="info-img"></i>
                                    <div class="input-groupicon">
                                        <input type="text" name="date_from" id="date-from" class="datetimepicker"
                                            placeholder="Choose Date From" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-12">
                                <div class="input-blocks">
                                    <i data-feather="calendar" class="info-img"></i>
                                    <div class="input-groupicon">
                                        <input type="text" name="date_to" id="date-to" class="datetimepicker"
                                            placeholder="Choose Date To" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-1 col-sm-6 col-12">
                                <div class="input-blocks">
                                    <a class="btn btn-filters" id="searchBtn">
                                        <i data-feather="search" class="feather-search"></i>
                                        Search
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- /Filter -->
                <div class="table-responsive datatable-scroll-container" id="invoice-management-scroll-wrap">
                    <table class="table datanew" id="order-table">
                        <thead>
                            <tr>
                                <!-- <th class="no-sort">
                                    <label class="checkboxs">
                                        <input
                                            type="checkbox"
                                            id="select-all"
                                        />
                                        <span class="checkmarks"></span>
                                    </label>
                                </th> -->
                                <th>Order Id</th>
                                <th>Invoice Number</th>
                                <th>Customer Name</th>
                                <th>Store Name</th>
                                <th>Order Date</th>
                                <th>Invoice Type</th>
                                <th>Amount</th>
                                <th>Paid</th>
                                <th>Amount Due</th>
                                <th>Due Date</th>
                                <th>Overdue</th>
                                <th>Status</th>
                                @if($user->role_id != 5)
                                <th class="no-sort">
                                    Action
                                </th>
                                @endif
                            </tr>
                        </thead>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Order -->
<div class="modal fade" id="add-units" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered stock-adjust-modal">
        <div class="modal-content">
            <div class="page-wrapper-new p-0">
                <div class="content">
                    <div class="modal-header border-0 custom-modal-header">
                        <div class="page-title">
                            {{--<h4>Add Transfer</h4>--}}
                            <h4>Invoicing Options</h4>
                        </div>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body custom-modal-body">
                        <form method="POST" action="{{ route('admin.invoice-order') }}" id="transferForm">
                            @csrf
                            @php
                            $selectedStore = $user->selected_store_id;
                            @endphp
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="input-blocks">
                                        <label>Select Store</label>
                                        <select name="store_from" id="store_from" class="form-control select">
                                            @foreach ($stores as $store)
                                            @if($store->id==$selectedStore)
                                            <option value="{{ $store->id }}" selected>
                                                {{ $store->name }}
                                            </option>
                                            @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="input-blocks">
                                        <label>Customer</label>
                                        <div class="input-block d-flex align-items-center mt-2">
                                            <div class="flex-grow-1 me-2 ">
                                                <select class="select" name="customer_id" id="customer-select">
                                                    <option value="">Choose Customer</option>
                                                    @foreach($customers as $customer)
                                                    <option value="{{$customer->id}}">
                                                        {{$customer->customer_name}}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <a href="#" class="btn btn-primary btn-icon open-create-modal">
                                                <i data-feather="user-plus" class="feather-16"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="input-blocks">
                                        {{--<label>Where and How do you generate an invoice for a customer taking goods</label>--}}
                                        <label>Invoice Type *</label>
                                        <select name="invoice_type" id="invoice_type" class="form-select" required>
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
                                    <small class="text-muted d-block mb-3">When checked, no line tax is applied regardless of product settings. Unchecking restores each line to the product's default tax status.</small>
                                </div>
                                <div class="col-lg-12 credit-only-fields" style="display:none;">
                                    <div class="input-blocks">
                                        <label>Payment Terms (Days)</label>
                                        {{-- Old code:
                                        <input type="number" min="0" max="3650" name="payment_terms_days" id="payment_terms_days" class="form-control" placeholder="e.g. 30">
                                        --}}
                                        <select name="payment_terms_days" id="payment_terms_days" class="form-select">
                                            <option value="">Select Payment Terms</option>
                                            {{-- Old hardcoded options:
                                            <option value="1">0-30 days</option>
                                            <option value="2">30-60 days</option>
                                            <option value="3">60-90 days</option>
                                            <option value="4">90+ days</option>
                                            --}}
                                            @foreach($paymentTerms as $term)
                                            <option value="{{ $term->id }}">{{ $term->label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                {{-- Old code: due date input removed from create form; due date is auto-calculated from selected payment term bucket.
                                <div class="col-lg-6 credit-only-fields" style="display:none;">
                                    <div class="input-blocks">
                                        <label>Due Date</label>
                                        <input type="date" name="due_date" id="due_date" class="form-control">
                                    </div>
                                </div>
                                --}}
                                <div class="col-lg-12">
                                    <div class="input-blocks search-form mb-3">
                                        <label>Product</label>
                                        <input type="text" id="product_search" class="form-control"
                                            placeholder="Search Product by Name or SKU" />
                                        <!-- <ul id="product_list" class="list-group mt-2"></ul> -->
                                        <ul id="product_list" class="list-group"
                                            style="max-height: 200px; overflow-y: auto; position: absolute; z-index: 1000; width: 100%; display: none;">
                                        </ul>


                                        <i data-feather="search" class="feather-search"></i>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="modal-body-table">
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
                                                <tbody id="product-tbody">
                                                    <!-- Template Row (Hidden by Default) -->
                                                    <tr id="product-row-template" class="d-none">
                                                        <td>
                                                            <input type="hidden" name="product_id[]"
                                                                class="form-control product-id" />
                                                            <input type="text" name="product_name[]"
                                                                class="form-control product-name" readonly />
                                                        </td>
                                                        <td>
                                                            <div class="productimgname">
                                                                <input name="product_name[]" type="text"
                                                                    class="form-control product-name" readonly />
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="input-blocks sku">
                                                                <input name="sku[]" type="text"
                                                                    class="form-control product-sku" readonly />
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <select name="category_id[]" class="select product-category"
                                                                disabled>
                                                                <option>
                                                                    Choose
                                                                </option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <div class="product-quantity">
                                                                <input name="qty[]" type="number"
                                                                    class="form-control product-qty" value="1"
                                                                    min="1" />
                                                            </div>
                                                        </td>
                                                        <td class="action-table-data">
                                                            <div class="edit-delete-action">
                                                                <a class="confirm-text p-2 delete-row"
                                                                    href="javascript:void(0);">
                                                                    <i data-feather="trash-2"
                                                                        class="feather-trash-2"></i>
                                                                </a>
                                                            </div>
                                                        </td>

                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
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
                                @php
                                $user = auth()->user();
                                @endphp

                                @if($user && in_array($user->role_id, [1,2,3]))
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
                                            @for ($i = 5; $i <= $discount_percentage; $i +=5) <option value="{{ $i }}">
                                                {{ $i }}%</option>
                                                @endfor
                                        </select>
                                    </div>
                                </div>
                                @endif--}}

                                <input type="hidden" name="discount" id="discountDropdown" value="0">

                                <div class="row">
                                    <div class="col-lg-6 mt-5">
                                        <div class="order-total">
                                            <table class="table table-responsive table-borderless">
                                                <tr>
                                                    <td>Sub Total</td>
                                                    <td class="text-end" id="sub-total">
                                                        $0.00
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td id="tax_percentage">Tax</td>
                                                    <td class="text-end" id="tax">$0.00</td>
                                                </tr>
                                                <tr>
                                                    <td id="discount_percentage">Discount (0%)</td>
                                                    <td class="danger text-end" id="discount">
                                                        $0.00
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Total</td>
                                                    <td class="text-end" id="total">$0.00</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mt-5">
                                        <div class="block-section payment-method">
                                            <h6>Payment Method</h6>
                                            <div class="row d-flex align-items-center justify-content-center methods">
                                                <input type="hidden" name="payment_method" id="payment_method" />
                                                <div class="col-md-6 col-lg-4 item cash-method">
                                                    <div class="default-cover">
                                                        <a href="javascript:void(0);" class="payment-option"
                                                            data-method="Cash" id="cashPayment">


                                                            <span><i data-feather="dollar-sign" class="me-1" width="16" height="16"></i>Cash</span>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-lg-4 item">
                                                    <div class="default-cover">
                                                        <a href="javascript:void(0);" class="payment-option"
                                                            data-method="Card" id="cashPayment">

                                                            <span><i data-feather="credit-card" class="me-1" width="16" height="16"></i>Debit Card</span></a>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-lg-4 item">
                                                    <div class="default-cover">
                                                        <a href="javascript:void(0);" class="payment-option"
                                                            data-method="Scan" id="cashPayment">

                                                            <span>
                                                                <svg class="me-1" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                                                    xmlns="http://www.w3.org/2000/svg" style="vertical-align:-2px;">
                                                                    <rect x="3" y="3" width="7" height="7" stroke="currentColor" stroke-width="2"/>
                                                                    <rect x="14" y="3" width="7" height="7" stroke="currentColor" stroke-width="2"/>
                                                                    <rect x="3" y="14" width="7" height="7" stroke="currentColor" stroke-width="2"/>
                                                                    <path d="M14 14H17V17H14V14Z" fill="currentColor"/>
                                                                    <path d="M17 17H21V21H17V17Z" fill="currentColor"/>
                                                                    <path d="M14 19H16V21H14V19Z" fill="currentColor"/>
                                                                    <path d="M19 14H21V16H19V14Z" fill="currentColor"/>
                                                                </svg>Scan
                                                            </span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @include('admin.partials.invoice-placement-buttons', ['draftOrderId' => null])
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer-btn">
                                <button type="button" class="btn btn-cancel me-2" data-bs-dismiss="modal">
                                    Cancel
                                </button>
                                <!-- <button type="submit" class="btn btn-submit">
                                    Create
                                </button> -->
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /New Order -->
<!-- Unit Price Update Modal -->
<div class="modal fade" id="unit-price-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 custom-modal-header">
                <div class="page-title">
                    <h4>Change Unit Price</h4>
                </div>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body custom-modal-body">
                <form id="unit-price-modal-form">
                    @csrf
                    <input type="hidden" id="unit-price-modal-store-id" value="">
                    <input type="hidden" id="unit-price-modal-product-id" value="">
                    <input type="hidden" id="unit-price-modal-target" value="generate">
                    <div class="mb-3">
                        <label class="form-label">New Unit Price</label>
                        <input type="text" id="unit-price-modal-input" class="form-control"
                            inputmode="decimal" autocomplete="off" />
                    </div>
                </form>
            </div>
            <div class="modal-footer-btn">
                <button type="button" class="btn btn-cancel me-2" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-submit" id="unit-price-modal-save">Save</button>
            </div>
        </div>
    </div>
</div>
<!-- Edit Order -->
<div class="modal fade" id="edit-order" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered stock-adjust-modal">
        <div class="modal-content">
            <div class="page-wrapper-new p-0">
                <div class="content">
                    <div class="modal-header border-0 custom-modal-header">
                        <div class="page-title">
                            <h4>Add Transfer</h4>
                        </div>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body custom-modal-body">
                        <form method="POST" action="{{ route('admin.invoice-update') }}" id="edit_invoice_form">
                            @csrf
                            <input type="hidden" name="order_id" id="edit-order-id" value="">

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="input-blocks">
                                        <label>Select Store</label>
                                        <select name="edit_store_from" id="edit_store_from" class="form-control select">
                                            @foreach ($stores as $store)
                                            @if($store->id == $selectedStore)
                                            <option value="{{ $store->id }}" selected>{{ $store->name }}</option>
                                            @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="input-blocks">
                                        <label>Customer</label>
                                        <div class="input-block d-flex align-items-center mt-2">
                                            <div class="flex-grow-1 me-2">
                                                <select class="select" name="edit_customer_id"
                                                    id="edit_customer_select">
                                                    <option value="">Choose Customer</option>
                                                    @foreach($customers as $customer)
                                                    <option value="{{ $customer->id }}">{{ $customer->customer_name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <a href="#" class="btn btn-primary btn-icon open-create-modal">
                                                <i data-feather="user-plus" class="feather-16"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="input-blocks search-form mb-3">
                                        <label>Product</label>
                                        <input type="text" id="edit_product_search" class="form-control"
                                            placeholder="Search Product by Name or SKU" />
                                        <ul id="edit_product_list" class="list-group"
                                            style="max-height: 200px; overflow-y: auto; position: absolute; z-index: 1000; width: 100%; display: none;">
                                        </ul>
                                        <i data-feather="search" class="feather-search"></i>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="modal-body-table">
                                        <div class="table-responsive">
                                            <table class="table datanew">
                                                <thead>
                                                    <tr>
                                                        <th>Product</th>
                                                        <th>SKU</th>
                                                        <th>Category</th>
                                                        <th>Qty</th>
                                                        <th>Price</th>
                                                        <th class="no-sort">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="edit_product_tbody">
                                                    <!-- JS will populate rows -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="input-blocks">
                                        <label>Order Tax</label>
                                        <select name="edit_tax" id="edit_taxDropdown" class="select">
                                            <option value="0">No Tax</option>
                                            <option value="5">GST 5%</option>
                                            <option value="10">GST 10%</option>
                                            <option value="15">GST 15%</option>
                                            <option value="20">GST 20%</option>
                                            <option value="25">GST 25%</option>
                                            <option value="30">GST 30%</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="input-blocks">
                                        <label>Discount</label>
                                        <select name="edit_discount" id="edit_discountDropdown" class="select">
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

                                <div class="row">
                                    <div class="col-lg-6 mt-5">
                                        <div class="order-total">
                                            <table class="table table-responsive table-borderless">
                                                <tr>
                                                    <td>Sub Total</td>
                                                    <td class="text-end" id="edit_sub_total">$0.00</td>
                                                </tr>
                                                <tr>
                                                    <td id="edit_tax_percentage">Tax (GST 0%)</td>
                                                    <td class="text-end" id="edit_tax">$0.00</td>
                                                </tr>
                                                <tr>
                                                    <td id="edit_discount_percentage">Discount (0%)</td>
                                                    <td class="danger text-end" id="edit_discount">$0.00</td>
                                                </tr>
                                                <tr>
                                                    <td>Total</td>
                                                    <td class="text-end" id="edit_total">$0.00</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mt-5">
                                        <div class="block-section payment-method">
                                            <h6>Payment Method</h6>
                                            <div class="row d-flex align-items-center justify-content-center methods">
                                                <input type="hidden" name="edit_payment_method"
                                                    id="edit_payment_method" />
                                                <div class="col-md-6 col-lg-4 item cash-method">
                                                    <div class="default-cover">
                                                        <a href="javascript:void(0);" class="edit-payment-option"
                                                            data-method="Cash"><span><i data-feather="dollar-sign" class="me-1" width="16" height="16"></i>Cash</span></a>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-lg-4 item">
                                                    <div class="default-cover">
                                                        <a href="javascript:void(0);" class="edit-payment-option"
                                                            data-method="Card"><span><i data-feather="credit-card" class="me-1" width="16" height="16"></i>Debit
                                                                Card</span></a>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-lg-4 item">
                                                    <div class="default-cover">
                                                        <a href="javascript:void(0);" class="edit-payment-option"
                                                            data-method="Scan"><span>
                                                                <svg class="me-1" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                                                    xmlns="http://www.w3.org/2000/svg" style="vertical-align:-2px;">
                                                                    <rect x="3" y="3" width="7" height="7" stroke="currentColor" stroke-width="2"/>
                                                                    <rect x="14" y="3" width="7" height="7" stroke="currentColor" stroke-width="2"/>
                                                                    <rect x="3" y="14" width="7" height="7" stroke="currentColor" stroke-width="2"/>
                                                                    <path d="M14 14H17V17H14V14Z" fill="currentColor"/>
                                                                    <path d="M17 17H21V21H17V17Z" fill="currentColor"/>
                                                                    <path d="M14 19H16V21H14V19Z" fill="currentColor"/>
                                                                    <path d="M19 14H21V16H19V14Z" fill="currentColor"/>
                                                                </svg>Scan
                                                            </span></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-grid btn-block mt-5">
                                            <button type="submit" id="submit_edit_invoice" class="btn btn-secondary">
                                                Update Order: $<span id="edit_placeOrderValue">0.00</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer-btn">
                                <button type="button" class="btn btn-cancel me-2"
                                    data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Edit Order -->
<!-- Eye button view modal -->
<div class="modal fade" id="edit-invoice">
    <div class="modal-dialog modal-dialog-centered stock-adjust-modal">
        <div class="modal-content">
            <div class="page-wrapper-new p-0">
                <div class="content">
                    <div class="modal-header border-0 custom-modal-header">
                        <div class="page-title">
                            <h4>View Invoice</h4>
                        </div>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body custom-modal-body">
                        <form>

                            <div class="row">
                                <div class="col-lg-6 col-sm-6 col-12">
                                    <div class="mb-3 add-product">
                                        <label class="form-label">Store</label>
                                        <input type="text" class="form-control" value="" name="store_name" disabled />
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-6 col-12">
                                    <div class="mb-3 add-product">
                                        <label class="form-label">Invoice No.</label>
                                        <input type="text" class="form-control" value="" name="invoice_no" disabled />
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-6 col-12">
                                    <div class="mb-3 add-product">
                                        <label class="form-label">Due Date</label>
                                        <input type="text" class="form-control" value="" name="due_date" disabled />
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-6 col-12">
                                    <div class="mb-3 add-product">
                                        <label>Amount</label>
                                        <input type="text" class="form-control list" value="" name="amount" disabled />
                                    </div>
                                </div>

                                <div class="col-lg-6 col-sm-6 col-12">
                                    <div class="mb-3 add-product">
                                        <label class="form-label">Amount paid</label>
                                        <input type="text" class="form-control list" value="" name="amount_paid"
                                            disabled />
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-6 col-12">
                                    <div class="mb-3 add-product">
                                        <label class="form-label">Amount Due</label>
                                        <input type="text" class="form-control list" value="$0.00" disabled />
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-6 col-12">
                                    <div class="mb-3 add-product">
                                        <label class="form-label">Status</label>
                                        <input type="text" class="form-control list" value="" name="status" disabled />
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Eye button view modal -->

<!-- Create customer -->
<div class="modal fade" id="customer-payment-info-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Customer & Payment Info (<span id="cp-invoice-no">-</span>)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2 mb-3">
                    <div class="col-md-6"><strong>Customer:</strong> <span id="cp-customer-name">-</span></div>
                    <div class="col-md-6"><strong>Phone:</strong> <span id="cp-customer-phone">-</span></div>
                    <div class="col-md-6"><strong>Email:</strong> <span id="cp-customer-email">-</span></div>
                    <div class="col-md-6"><strong>Due Date:</strong> <span id="cp-due-date">-</span></div>
                    <div class="col-md-4"><strong>Total:</strong> $<span id="cp-total-amount">0.00</span></div>
                    <div class="col-md-4"><strong>Paid:</strong> $<span id="cp-paid-amount">0.00</span></div>
                    <div class="col-md-4"><strong>Due:</strong> $<span id="cp-due-amount">0.00</span></div>
                </div>

                <h6 class="mb-2">Payment History</h6>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Reference</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody id="cp-payments-body"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create customer -->
<div class="modal fade" id="create" tabindex="-1" aria-labelledby="create" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="create-customer-form">
                    <div class="row">
                        <div class="col-lg-6 col-sm-12 col-12">
                            <div class="input-blocks">
                                <label>Customer Name</label>
                                <input name="customer_name" type="text" class="form-control" />
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12 col-12">
                            <div class="input-blocks">
                                <label>Email</label>
                                <input name="email" type="email" class="form-control" />
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12 col-12">
                            <div class="input-blocks">
                                <label>Phone</label>
                                <input name="phone" type="text" class="form-control" />
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12 col-12">
                            <div class="input-blocks">
                                <label>Country</label>
                                <input name="country" type="text" class="form-control" />
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12 col-12">
                            <div class="input-blocks">
                                <label>City</label>
                                <input name="city" type="text" />
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12 col-12">
                            <div class="input-blocks">
                                <label>Address</label>
                                <input name="address" type="text" />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-sm-flex justify-content-end">
                        <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-submit me-2">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Receipt Print -->
<div class="modal fade modal-default" id="print-receipt" aria-labelledby="print-receipt">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="d-flex justify-content-end">
                <button type="button" class="close p-0 no-print" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                {{--<div class="icon-head text-center">
                    <a href="javascript:void(0);">
                        <h6>Accounting and Stock Management System</h6>
                    </a>
                </div>--}}
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
                                <span>Invoice No: </span><span>CS132453</span>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <div class="invoice-user-name">
                                <span>Customer Id: </span><span></span>
                            </div>
                            <div class="invoice-user-name">
                                <span>Date: </span><span>01.07.2022</span>
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

                    <tbody>
                        <tr>
                            <td>product1</td>
                            <td>$00</td>
                            <td>0</td>
                            <td>$0</td>
                        </tr>

                        <tr>
                            <td colspan="4">
                                <table class="table-borderless w-100 table-fit">
                                    <tr>
                                        <td>Sub Total :</td>
                                        <td class="text-end">$0.00</td>
                                    </tr>
                                    <tr>
                                        <td>Discount (0%) :</td>
                                        <td class="text-end">-$0.00</td>
                                    </tr>
                                    <tr>
                                        <td>Shipping (0%) :</td>
                                        <td class="text-end">0.00</td>
                                    </tr>
                                    <tr>
                                        <td>Tax (0%) :</td>
                                        <td class="text-end">$0.00</td>
                                    </tr>
                                    <tr>
                                        <td>Total Bill :</td>
                                        <td class="text-end">$0.00</td>
                                    </tr>
                                    <tr>
                                        <td>Due :</td>
                                        <td class="text-end">$0.00</td>
                                    </tr>
                                    <tr>
                                        <td>Total Payable :</td>
                                        <td class="text-end">$0.00</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="text-center invoice-bar">
                    <p>
                        **VAT against this challan is payable through central
                        registration. Thank you for your business!
                    </p>
                    {{--<a href="javascript:void(0);">
                        <img src="assets/img/barcode/barcode-03.jpg" alt="Barcode" />
                    </a>
                    <p>Sale 31</p>--}}
                    <p>Thank You For Shopping With Us. Please Come Again</p>
                    <a href="javascript:void(0);" class="btn btn-primary no-print" id="printButton">Print Receipt</a>
                </div>
            </div>
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
                            @foreach($invoice_options as $id => $name)
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

<!-- Receipt Print -->
@endsection
@push('scripts')
@include('admin.partials.invoice-per-line-tax-scripts')
<script>
    // Replace feather icons for elements inside modals as well.
    document.addEventListener('DOMContentLoaded', function () {
        if (window.feather) {
            feather.replace();
        }
    });

    $(document).ready(function () {
        $('#add-units .select').each(function () {
            const $select = $(this);
            if ($select.data('select2')) {
                $select.select2('destroy');
            }
            $select.select2({
                minimumResultsForSearch: -1,
                width: '100%',
                dropdownParent: $('#add-units')
            });
        });

        let userRoleId = {{ auth()->user()->role_id }};

    let columns = [
        { data: 'order_id', name: 'id' },
        { data: 'invoice_no', name: 'invoice_no' },
        { data: 'customer_name', name: 'customer_name' },
        { data: 'store_name', name: 'sort_stores.name' },
        { data: 'created_at', name: 'created_at' },
        { data: 'invoice_type', name: 'invoice_type' },
        { data: 'total_price', name: 'total_price', orderable: false, searchable: false },
        { data: 'paid_price', name: 'paid_price', orderable: false, searchable: false },
        { data: 'due', name: 'due', orderable: false, searchable: false },
        { data: 'due_date', name: 'due_date', orderable: false, searchable: false },
        { data: 'overdue', name: 'overdue', orderable: false, searchable: false },
        { data: 'payment_status', name: 'payment_status', orderable: false, searchable: false }
    ];

    if (userRoleId !== 5) {
        columns.push({ data: 'actions', name: 'actions', orderable: false, searchable: false });
    }

    let paginationManager = DatatablePagination.attach({
        tableKey: 'invoice-management',
        tableSelector: '#order-table',
        toolbarSelector: '#invoice-management-dt-toolbar',
        scrollContainerSelector: '#invoice-management-scroll-wrap',
        config: @json($datatableConfig),
        getAjaxUrl: function () {
            return "{{ route('admin.invoice') }}";
        },
        buildTableOptions: function (mode, pageLength) {
            const shared = {
                columns: columns,
                order: [[0, 'desc']],
                drawCallback: function () {
                    feather.replace();
                },
                initComplete: function () {
                    let searchWrapper = $('#order-table_filter');
                    let defaultSearchBox = searchWrapper.find('input');
                    if ($('#custom-search').find(defaultSearchBox).length === 0) {
                        $('#custom-search').append(defaultSearchBox);
                    }
                    defaultSearchBox.attr('placeholder', 'Search...');
                    searchWrapper.remove();
                }
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
                ajax: {
                    url: "{{ route('admin.invoice') }}",
                    data: function (d) {
                        d.store = $('#filter_store').val();
                        d.order_type = $('#sort-order').val();
                        d.date_to = $('#date-to').val();
                        d.date_from = $('#date-from').val();
                    }
                },
                pageLength: pageLength,
                lengthChange: false,
                searching: true,
                dom: 'frtip',
            });
        },
    });
    window.invoiceReportTable = $('#order-table').DataTable();

    $('#filter_store').on('change', function () {
        paginationManager.reload(false);
    });
    $('#sort-order').on('change', function () {
        paginationManager.reload(false);
    });
    $('#searchBtn').on('click', function () {
        paginationManager.reload(false);
    });
});

    $(document).ready(function () {
        // Old global tax: $("#taxDropdown, #discountDropdown").on("change", calculateTotals);
        $("#discountDropdown").on("change", calculateTotals);
        $(document).on("input", 'input[name="qty[]"], input[name="price[]"]', calculateTotals);
        $(document).on("click", ".delete-row", calculateTotals);
        if (window.InvoicePerLineTax) {
            window.InvoicePerLineTax.bindEvents();
            window.InvoicePerLineTax.syncAllLineTaxUi();
        }

        // $(".viewInvoice").on("click", function () {
        $(document).on("click", ".viewInvoice", function () {
            var invoiceId = $(this).data("id");


            $.ajax({
                url: "{{ route('admin.view-invoice') }}",
                type: "GET",
                data: {
                    invoiceId: invoiceId,
                },
                success: function (response) {
                    const date = new Date(response.data.created_at);
                    const formatted = date.toISOString().substring(0, 10);

                    if (response.success) {
                        //console.log(response);

                        $('#edit-invoice input[name="store_name"]').val(
                            response.store_name
                        );
                        $('#edit-invoice input[name="invoice_no"]').val(
                            response.data.invoice_number || ('INV/' + response.data.id)
                        );
                        $('#edit-invoice input[name="due_date"]').val(
                            formatted
                        );
                        $('#edit-invoice input[name="amount"]').val(
                            response.data.total_price
                        );

                        $('#edit-invoice input[name="amount_paid"]').val(
                            response.data.total_price
                        );
                        $('#edit-invoice input[name="address"]').val(
                            response.data.address
                        );
                        $('#edit-invoice input[name="status"]').val(
                            response.data.status == 0
                                ? "Unpaid"
                                : "Paid"
                        );

                        $("#edit-invoice").modal("show");
                    } else {
                        alert("Invoice details not found!");
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error:", xhr.responseText);
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Failed to View/Find Invoice. Please try again.",
                        confirmButtonColor: "#d33",
                    });
                },
            });
        });

        $(document).on('click', '.view-customer-payments', function () {
            const orderId = $(this).data('id');
            const $tbody = $('#cp-payments-body');
            $('#cp-invoice-no').text('INV/' + orderId);
            $tbody.html('<tr><td colspan="5" class="text-center">Loading...</td></tr>');

            $.ajax({
                url: "{{ route('admin.invoice-payment-info', ['order' => '__ORDER__']) }}".replace('__ORDER__', orderId),
                type: 'GET',
                success: function (response) {
                    if (!response.success) {
                        Swal.fire('Error', response.message || 'Failed to load details', 'error');
                        return;
                    }

                    const d = response.data;
                    $('#cp-invoice-no').text(d.invoice_no || d.invoice_number || ('INV/' + orderId));
                    $('#cp-customer-name').text(d.customer_name || '-');
                    $('#cp-customer-phone').text(d.customer_phone || '-');
                    $('#cp-customer-email').text(d.customer_email || '-');
                    $('#cp-due-date').text(d.due_date || '-');
                    $('#cp-total-amount').text((Number(d.total_amount) || 0).toFixed(2));
                    $('#cp-paid-amount').text((Number(d.paid_amount) || 0).toFixed(2));
                    $('#cp-due-amount').text((Number(d.due_amount) || 0).toFixed(2));

                    $tbody.empty();
                    if (!d.payments || d.payments.length === 0) {
                        $tbody.html('<tr><td colspan="5" class="text-center">No payments recorded for this invoice.</td></tr>');
                    } else {
                        d.payments.forEach(function (payment) {
                            $tbody.append(`
                                <tr>
                                    <td>${payment.payment_date ?? '-'}</td>
                                    <td>$${(Number(payment.amount) || 0).toFixed(2)}</td>
                                    <td>${payment.payment_method ?? '-'}</td>
                                    <td>${payment.reference_no ?? '-'}</td>
                                    <td>${payment.notes ?? '-'}</td>
                                </tr>
                            `);
                        });
                    }

                    $('#customer-payment-info-modal').modal('show');
                },
                error: function () {
                    Swal.fire('Error', 'Failed to load customer payment details.', 'error');
                }
            });
        });

        $(document).on('click', '.convert-estimate-invoice-main', function () {
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
                        if (window.invoiceReportTable) {
                            window.invoiceReportTable.ajax.reload(null, false);
                        }
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

    })
    $("#product_search").on("focus", function () {
        let storeId = $("#store_from").val();
        if (storeId === "") {
            Swal.fire({
                icon: "warning",
                title: "Select Store!",
                text: "Please select a store first.",
            });
            return;
        }
        fetchProducts(storeId, $(this).val());
    });

    $("#product_search").on("keyup", function () {
        let query = $(this).val();
        let storeId = $("#store_from").val();

        if (storeId === "") {
            Swal.fire({
                icon: "warning",
                title: "Select Store!",
                text: "Please select a store first.",
            });
            return;
        }

        fetchProducts(storeId, query);
    });

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
                    $.each(response, function (index, product) {
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
                            </li>
                        `);
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: "error",
                    title: "Error!",
                    text: "Failed to load products.",
                });
            },
        });
    }

    $(document).on("click", ".product-item", function () {
        let productId = $(this).data("id");
        let productName = $(this).data("name");
        let sku = $(this).data("sku");
        let category = $(this).data("category");
        let category_id = $(this).data("category_id")
        let availableQty = $(this).data("qty");
        let price = $(this).data("price");

        addProductToTable(
            productId,
            productName,
            sku,
            category,
            availableQty,
            price,
            category_id,
            null,
            $(this).data("is_taxable") == 1,
            $(this).data("tax_rate")
        );
        $("#product_list").empty().hide();
        $("#product_search").val("");
    });

    // Allow admin to update unit price for a selected product before generating invoice.
    let unitPriceModalRow = null;
    let unitPriceModalTarget = 'generate';
    $(document).on("click", ".edit-unit-price", function () {
        const $row = $(this).closest("tr");
        const productId = $(this).data("product-id");
        const storeId = $("#store_from").val();

        const currentPrice = parseFloat($row.find('input[name="price[]"]').val()) || 0;

        unitPriceModalRow = $row;
        unitPriceModalTarget = 'generate';

        $("#unit-price-modal-store-id").val(storeId);
        $("#unit-price-modal-product-id").val(productId);
        $("#unit-price-modal-target").val(unitPriceModalTarget);
        $("#unit-price-modal-input").val(currentPrice);

        const modalEl = document.getElementById('unit-price-modal');
        if (modalEl) {
            $('#unit-price-modal').modal('show');
        }
    });

    function addProductToTable(productId, productName, sku, category, availableQty, price, category_id, lineQty, isTaxable, taxRate) {
        let isDuplicate = false;
        $("#product-tbody tr").not("#product-row-template").each(function () {
            let existingProductId = $(this).find('input[name="product_id[]"]').val();
            if (existingProductId == productId) {
                isDuplicate = true;
                return false;
            }
        });

        if (isDuplicate) {
            Swal.fire({
                icon: "warning",
                title: "Duplicate Product!",
                text: "This product is already added to the list.",
            });
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

        let newRow = `
            <tr>
                <td><input type="hidden" name="product_id[]" value="${productId}" />
                    <input type="text" name="product_name[]" class="form-control" value="${safeName}" readonly /></td>
                <td><input type="text" name="sku[]" class="form-control" value="${safeSku}" readonly /></td>
                <td>
                    <input type="hidden" name="category_id[]" value="${category_id}" />
                    <input type="text" name="category[]" class="form-control" value="${safeCategory}" readonly />
                </td>
                <td>
                    <input type="number" name="qty[]" class="form-control product-qty mt-2"
                        value="${qtyVal}" min="1" max="${maxQty}" data-available="${maxQty}" />
                    <small class="available-qty">Available Qty: ${maxQty}</small>
                </td>
                <td>
                    <input type="text" name="price[]" class="form-control mt-2" value="${price}"/>
                    <a href="javascript:void(0);" class="edit-unit-price mt-2 d-block text-decoration-none small"
                        data-product-id="${productId}">
                        Change Unit Price
                    </a>
                </td>
                <td class="line-tax-col">
                    <input type="hidden" name="line_is_taxable[]" class="line-taxable-val" value="${taxable ? 1 : 0}">
                    <input type="checkbox" class="form-check-input line-taxable-cb" ${checked}>
                </td>
                <td class="line-tax-col">
                    <input type="number" step="0.01" min="0" max="100" name="line_tax_rate[]" class="form-control form-control-sm line-tax-rate" value="${rate}">
                </td>
                <td class="line-tax-col line-tax-amount text-end">$0.00</td>
                <td><a href="javascript:void(0);" class="btn btn-danger btn-sm delete-row mt-2">Delete</a></td>
            </tr>
        `;
        const $row = $(newRow);
        $("#product-tbody").append($row);
        if (window.InvoicePerLineTax) {
            window.InvoicePerLineTax.setRowDefaultTax($row, isTaxable, taxRate);
            window.InvoicePerLineTax.syncLineTaxUi($row);
        }
        calculateTotals();
    }

    $(document).on("input", ".product-qty", function () {
        let enteredQty = parseInt($(this).val());
        let availableQty = parseInt($(this).data("available"));
        if (enteredQty > availableQty) {
            Swal.fire({
                icon: "error",
                title: "Quantity Exceeded!",
                text: `Available quantity is only ${availableQty}.`,
            });
            $(this).val(availableQty);
        }
    });

    function syncInvoiceLineQoh(storeId) {
        if (!storeId) {
            return;
        }
        $.ajax({
            url: "{{ route('admin.get-products-invoice') }}",
            method: "GET",
            data: { store_id: storeId, query: "" },
            success: function (products) {
                const byId = {};
                $.each(products || [], function (_, product) {
                    byId[String(product.product_id)] = product;
                });
                $("#product-tbody tr").not("#product-row-template").each(function () {
                    const productId = String($(this).find('input[name="product_id[]"]').val() || "");
                    const product = byId[productId];
                    if (!product) {
                        return;
                    }
                    const qoh = parseInt(product.total_quantity, 10) || 0;
                    const $qty = $(this).find('input[name="qty[]"]');
                    $qty.attr("max", Math.max(qoh, 1)).attr("data-available", qoh);
                    $(this).find(".available-qty").text("Available Qty: " + qoh);
                });
            }
        });
    }

    $("#store_from").on("change", function () {
        const storeId = $(this).val();
        const query = $("#product_search").val();
        if (storeId && ($("#product_list").is(":visible") || query)) {
            fetchProducts(storeId, query);
        } else {
            $("#product_list").empty().hide();
        }
        syncInvoiceLineQoh(storeId);
    });

    $(document).on("click", ".delete-row", function () {
        $(this).closest("tr").remove();
    });

    $(document).on("click", function (e) {
        if (!$(e.target).closest("#product_search, #product_list").length) {
            $("#product_list").hide();
        }
    });

    $('#invoice_type').on('change', function () {
        const invoiceTypeId = Number($('#invoice_type').val() || 0);
        const isCreditSale = invoiceTypeId === 2;

        if (isCreditSale) {
            $('.credit-only-fields').show();
        } else {
            $('.credit-only-fields').hide();
            // Old behavior: keep values even after switching type.
            // $('#payment_terms_days').val('');
        }
    });
    // EDIT INVOICE
    $(document).on('click', '.edit-order', function () {
        const orderId = $(this).data('id');

        $.ajax({
            url: "{{Route('admin.invoice-edit')}}",
            type: 'GET',
            data: { id: orderId },
            success: function (response) {
                if (response.success) {
                    const order = response.data;
                    const items = response.data.order_items;
                    $('#edit-order-id').val(order.id);
                    $('#edit_store_from').val(order.store_id).trigger('change');
                    $('#edit_customer_select').val(order.customer_id).trigger('change');
                    $('#edit_taxDropdown').val(order.tax).trigger('change');
                    $('#edit_discountDropdown').val(order.discount).trigger('change');
                    $('#edit_payment_method').val(order.payment_method);
                    $('#edit_product_tbody').empty();

                    items.forEach(function (item) {
                        let availableQty = item.available_qty || 0;
                        const row = `
                            <tr>
                                <td>
                                    <input type="hidden" name="edit_product_id[]" class="form-control" value="${item.product_id}" />
                                    <input type="text" name="edit_product_name[]" class="form-control" value="${item.product_name}" readonly />
                                </td>
                                <td>
                                    <input type="text" name="edit_sku[]" class="form-control" value="${item.sku}" readonly />
                                </td>
                                <td>
                                    <input type="hidden" name="edit_category_id[]" value="${item.category_id}" />
                                    <input type="text" class="form-control" value="${item.category_name || 'N/A'}" readonly />
                                </td>
                                <td>
                                    <input type="number" name="edit_qty[]" class="form-control edit_qty" style="margin:20px 0 0 0;"
                                        value="${availableQty > 0 ? item.qty : 0}" min="1" max="${availableQty}" data-available="${availableQty}" />
                                    <small class="available-qty">Available Qty: ${availableQty}</small>
                                </td>
                                <td>
                                    {{-- Old: name="edit_price[]" sent client price (forgeable). Server now sets price from order line or stock. --}}
                                    {{-- <input type="text" name="edit_price[]" class="form-control" value="${item.price}" readonly /> --}}
                                    <input type="text" class="form-control edit-line-unit-price" readonly
                                        value="${item.price}" data-unit-price="${parseFloat(item.price) || 0}" title="Price is fixed from this invoice (existing lines) or from stock (new lines)." />
                                </td>
                                <td >
                                    <a class="btn btn-danger btn-sm delete-row" href="javascript:void(0);">
                                        <i data-feather="trash-2" class="feather-trash-2"></i>
                                    </a>
                                </td>
                            </tr>`;
                        $('#edit_product_tbody').append(row);
                        feather.replace();
                    });


                    $('#edit-order').modal('show');
                    setTimeout(() => {
                        $('.edit-payment-option').removeClass('selected');
                        $('.edit-payment-option[data-method="' + order.payment_method + '"]').addClass('selected');

                    }, 300); // Delay to ensure DOM is fully loaded
                    updateEditOrderTotals();
                    $(document).on('input change', 'input[name="edit_qty[]"],  #edit_taxDropdown, #edit_discountDropdown', function () {
                        updateEditOrderTotals();
                    });
                } else {
                    Swal.fire('Error', 'Failed to load invoice data', 'error');
                }
            },
            error: function () {
                Swal.fire('Error', 'Server error while loading invoice', 'error');
            }
        });
    });

    // Product Search for Edit Modal
    $("#edit_product_search").on("keyup focus", function () {
        let query = $(this).val();
        let storeId = $("#edit_store_from").val();

        if (!storeId) {
            Swal.fire({
                icon: "warning",
                title: "Select Store First",
                text: "Please select a store before searching for products.",
            });
            return;
        }

        fetchEditModalProducts(storeId, query);
    });

    function fetchEditModalProducts(storeId, query) {
        $.ajax({
            url: "{{ route('admin.get-products-invoice') }}",
            method: "GET",
            data: { store_id: storeId, query: query },
            success: function (response) {
                $("#edit_product_list").empty().show();

                if (response.length === 0) {
                    $("#edit_product_list").append('<li class="list-group-item">No products found</li>');
                } else {
                    $.each(response, function (index, product) {
                        $("#edit_product_list").append(`
                            <li class="list-group-item edit-product-item"
                                data-id="${product.product_id}"
                                data-name="${product.product_name}"
                                data-sku="${product.sku}"
                                data-category="${product.category_name}"
                                data-category_id="${product.category_id}"
                                data-qty="${product.total_quantity}"
                                data-price="${product.price}">
                                ${product.product_name} - SKU: ${product.sku} - Qty: ${product.total_quantity}
                            </li>`);
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: "error",
                    title: "Error!",
                    text: "Failed to fetch products.",
                });
            }
        });
    }

    $(document).on("click", ".edit-product-item", function () {
        let productId = $(this).data("id");
        let productName = $(this).data("name");
        let sku = $(this).data("sku");
        let category = $(this).data("category");
        let category_id = $(this).data("category_id");
        let availableQty = $(this).data("qty");
        let price = $(this).data("price");

        let isDuplicate = false;
        $("#edit_product_tbody tr").each(function () {
            if ($(this).find('input[name="edit_product_id[]"]').val() == productId) {
                isDuplicate = true;
                return false;
            }
        });

        if (isDuplicate) {
            Swal.fire({
                icon: "warning",
                title: "Duplicate Product",
                text: "This product is already added.",
            });
            return;
        }

        const newRow = `
        <tr>
            <td>
                <input type="hidden" name="edit_product_id[]" class="form-control" value="${productId}" />
                <input type="text" name="edit_product_name[]" class="form-control" value="${productName}" readonly />
            </td>
            <td>
                <input type="text" name="edit_sku[]" class="form-control" value="${sku}" readonly />
            </td>
            <td>
                <input type="hidden" name="edit_category_id[]" value="${category_id}" />
                <input type="text" class="form-control" value="${category}" readonly />
            </td>
            <td>
                <input type="number" name="edit_qty[]" class="form-control edit-qty mt-2"
                    value="${availableQty > 0 ? 1 : 0}" min="1" max="${availableQty}" data-available="${availableQty}" />
                <small class="available-qty">Available Qty: ${availableQty}</small>
            </td>
            <td>
                {{-- Old: name="edit_price[]" --}}
                {{-- <input type="text" name="edit_price[]" class="form-control mt-2" value="${price}" readonly /> --}}
                <input type="text" class="form-control edit-line-unit-price mt-2" readonly
                    value="${price}" data-unit-price="${parseFloat(price) || 0}" title="Price comes from stock for newly added lines; you cannot edit it here." />
                {{-- Old (edit invoice): "Change Unit Price" link — disabled.
                <a href="javascript:void(0);" class="edit-unit-price-edit mt-2 d-block text-decoration-none small"
                    data-product-id="${productId}">
                    Change Unit Price
                </a>
                --}}
            </td>
            <td >
                <a class="btn btn-danger btn-sm delete-row mt-2" href="javascript:void(0);">
                    <i data-feather="trash-2" class="feather-trash-2"></i>
                </a>
            </td>
        </tr>`;

        $("#edit_product_tbody").append(newRow);
        $("#edit_product_list").empty().hide();
        $("#edit_product_search").val("");
        feather.replace();
        updateEditOrderTotals();
        $(document).on('input change', 'input[name="edit_qty[]"], #edit_taxDropdown, #edit_discountDropdown', function () {
            updateEditOrderTotals();
        });
    });

    // Old: unit price modal for EDIT invoice rows (disabled — price may only be changed when adding lines on new invoice via .edit-unit-price).
    /*
    $(document).on("click", ".edit-unit-price-edit", function () {
        const $row = $(this).closest("tr");
        const productId = $(this).data("product-id");
        const storeId = $("#edit_store_from").val() || $("#store_from").val();

        const currentPrice = parseFloat($row.find('input[name="edit_price[]"]').val()) || 0;

        unitPriceModalRow = $row;
        unitPriceModalTarget = 'edit';

        $("#unit-price-modal-store-id").val(storeId);
        $("#unit-price-modal-product-id").val(productId);
        $("#unit-price-modal-target").val(unitPriceModalTarget);
        $("#unit-price-modal-input").val(currentPrice);

        const modalEl = document.getElementById('unit-price-modal');
        if (modalEl) {
            $('#unit-price-modal').modal('show');
        }
    });
    */

    // Save unit price from the modal
    $(document).on("click", "#unit-price-modal-save", function () {
        const $submitBtn = $(this);
        const originalBtnText = $submitBtn.text();
        if (!unitPriceModalRow) {
            Swal.fire({ icon: "error", title: "Error", text: "Select a product first." });
            return;
        }

        const storeId = $("#unit-price-modal-store-id").val();
        const productId = $("#unit-price-modal-product-id").val();
        const rawValue = $("#unit-price-modal-input").val();
        const newPrice = parseFloat(rawValue);

        if (!storeId || !productId || Number.isNaN(newPrice) || newPrice < 0) {
            Swal.fire({ icon: "error", title: "Invalid price", text: "Enter a valid unit price." });
            return;
        }

        $submitBtn.prop('disabled', true).text('Saving...');

        $.ajax({
            url: "{{ route('admin.invoice-item-price.update') }}",
            method: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
                store_id: storeId,
                product_id: productId,
                price: newPrice
            },
            success: function (response) {
                if (response.success) {
                    if (unitPriceModalTarget === 'generate') {
                        unitPriceModalRow.find('input[name="price[]"]').val(response.price);
                        calculateTotals();
                    }
                    // Old: updating edit-invoice row price from modal (edit flow disabled).
                    // else {
                    //     unitPriceModalRow.find('input[name="edit_price[]"]').val(response.price);
                    //     updateEditOrderTotals();
                    // }

                    $('#unit-price-modal').modal('hide');
                    unitPriceModalRow = null;
                    Swal.fire({ icon: "success", title: "Updated", text: "Unit price updated successfully." });
                } else {
                    Swal.fire({ icon: "error", title: "Failed", text: response.message || "Could not update price." });
                }
            },
            error: function (xhr) {
                Swal.fire({
                    icon: "error",
                    title: "Failed",
                    text: xhr.responseJSON?.message || "Could not update price."
                });
            },
            complete: function () {
                $submitBtn.prop('disabled', false).text(originalBtnText);
            }
        });
    });

    $(document).on("click", function (e) {
        if (!$(e.target).closest("#edit_product_search, #edit_product_list").length) {
            $("#edit_product_list").hide();
        }
    });

    $(document).on('click', '#submit_edit_invoice', function (e) {
        e.preventDefault();
        const $submitBtn = $(this);
        const originalBtnText = $submitBtn.text();
        let formData = {
            order_id: $('#edit-order-id').val(), // ensure this hidden field exists in modal
            store_id: $('#edit_store_from').val(),
            customer_id: $('#edit_customer_select').val(),
            tax: $('#edit_taxDropdown').val(),
            discount: $('#edit_discountDropdown').val(),
            payment_method: $('#edit_payment_method').val(),
            product_id: $("input[name='edit_product_id[]']").map(function () { return $(this).val(); }).get(),
            product_name: $("input[name='edit_product_name[]']").map(function () { return $(this).val(); }).get(),
            sku: $("input[name='edit_sku[]']").map(function () { return $(this).val(); }).get(),
            qty: $("input[name='edit_qty[]']").map(function () { return $(this).val(); }).get(),
            // Old: client-submitted prices (ignored server-side; server uses order line + stock).
            // price: $("input[name='edit_price[]']").map(function () { return $(this).val(); }).get(),
            category_id: $("input[name='edit_category_id[]']").map(function () { return $(this).val(); }).get(),
            _token: "{{ csrf_token() }}"
        };

        $submitBtn.prop('disabled', true).text('Saving...');

        $.ajax({
            url: "{{ route('admin.invoice-update') }}",
            method: "POST",
            data: formData,
            success: function (response) {
                if (response.success) {
                    $('#edit-order').modal('hide');
                    Swal.fire('Success', response.message, 'success');
                    // Optionally reload table or page
                    location.reload();
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function () {
                Swal.fire('Error', 'Server error while updating invoice.', 'error');
            },
            complete: function () {
                $submitBtn.prop('disabled', false).text(originalBtnText);
            }
        });
    });

    //OPEN THE CUSTOMER MODAL OVER THE INVOICE GENERATION MODAL
    $(document).on('click', '.open-create-modal', function (e) {
        e.preventDefault();
        $('#create').modal({
            backdrop: false, // Prevents closing both modals when clicking outside
            keyboard: false  // Optional: disables ESC close for the second modal
        });
        $('#create').modal('show');
        // Optionally, adjust z-index if needed:
        $('#create').css('z-index', 1061); // Bootstrap default is 1050 for modals
        $('.modal-backdrop').last().css('z-index', 1060); // Ensure backdrop is below the top modal
    });
    $(document).on('click', '.payment-option', function () {
        $('.payment-option').removeClass('selected');
        $(this).addClass('selected');
        let method = $(this).data('method');
        $('#payment_method').val(method);
    });

    $(document).on('click', '.edit-payment-option', function () {
        $('.edit-payment-option').removeClass('selected');
        $(this).addClass('selected');
        let method = $(this).data('method');
        $('#edit_payment_method').val(method);
    });
    $("#create-customer-form").on("submit", function (event) {
        event.preventDefault();
        const $submitBtn = $(this).find('button[type="submit"]').first();
        const originalBtnText = $submitBtn.text();
        var customerData = $(this).serialize();

        $submitBtn.prop('disabled', true).text('Saving...');

        $.ajax({
            url: "{{ route('admin.create-customer') }}",
            method: "POST",
            data: customerData,
            success: function (response) {
                if (response.success && response.data) {
                    let newOption = `<option value="${response.data.id}" selected>
                    ${response.data.customer_name}
                </option>`;
                    $("#customer-select")
                        .append(newOption)
                        .val(response.data.id)
                        .trigger("change");

                    Swal.fire({
                        icon: "success",
                        title: "Customer Created",
                        text: response.message,
                    });
                    $("#create").modal("hide");
                    $(".modal-backdrop").remove();
                    $("body").removeClass("modal-open");
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Failed to create customer.",
                    });
                }
            },
            error: function (error) {
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: error.responseJSON?.message || "Something went wrong.",
                });
            },
            complete: function () {
                $submitBtn.prop('disabled', false).text(originalBtnText);
            },
        });
    });

    function formatCurrency(value) {
        const num = Number(value) || 0;
        return num.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function calculateTotals() {
        if (window.InvoicePerLineTax) {
            window.InvoicePerLineTax.calculateTotals();
            return;
        }
        // Old global tax fallback:
        let subtotal = 0;
        $("#product-tbody tr").not("#product-row-template").each(function () {
            const qty = parseFloat($(this).find('input[name="qty[]"]').val()) || 0;
            const price = parseFloat($(this).find('input[name="price[]"]').val()) || 0;
            subtotal += qty * price;
        });
        const discountRate = parseFloat($("#discountDropdown").val()) || 0;
        const discountAmount = (subtotal * discountRate) / 100;
        const total = subtotal - discountAmount;
        $("#sub-total").text(`$${formatCurrency(subtotal)}`);
        $("#tax").text(`$${formatCurrency(0)}`);
        $("#discount").text(`$${formatCurrency(discountAmount)}`);
        $("#total").text(`$${formatCurrency(total)}`);
        $("#placeOrderValue").text(formatCurrency(total));
        $("#tax_percentage").text('Tax');
        $("#discount_percentage").text(`Discount (${discountRate}%)`);
    }
    function updateEditOrderTotals() {
        let subtotal = 0;

        $('#edit_product_tbody tr').each(function () {
            let qty = parseFloat($(this).find('input[name="edit_qty[]"]').val()) || 0;
            let price = parseFloat($(this).find('.edit-line-unit-price').attr('data-unit-price')) || 0;
            subtotal += qty * price;
        });

        let taxPercent = parseFloat($('#edit_taxDropdown').val()) || 0;
        let discountPercent = parseFloat($('#edit_discountDropdown').val()) || 0;

        let tax = subtotal * (taxPercent / 100);
        let discount = subtotal * (discountPercent / 100);
        let total = subtotal + tax - discount;

        // Old code:
        // $('#edit_sub_total').text('$' + subtotal.toFixed(2));
        // $('#edit_tax').text('$' + tax.toFixed(2));
        // $('#edit_discount').text('$' + discount.toFixed(2));
        // $('#edit_total').text('$' + total.toFixed(2));
        // $('#edit_placeOrderValue').text(total.toFixed(2));
        $('#edit_sub_total').text('$' + formatCurrency(subtotal));
        $('#edit_tax').text('$' + formatCurrency(tax));
        $('#edit_discount').text('$' + formatCurrency(discount));
        $('#edit_total').text('$' + formatCurrency(total));
        $('#edit_placeOrderValue').text(formatCurrency(total));

        $('#edit_tax_percentage').text('Tax (GST ' + taxPercent + '%)');
        $('#edit_discount_percentage').text('Discount (' + discountPercent + '%)');

    }

    window.openInvoicePrintReceipt = function (transactionId, options) {
        options = options || {};
        const customerName = options.customerName || '-';
        const date = options.date || '-';
        const total = options.total || '-';
        const invoice = options.invoice || '-';
        const custId = options.customerId || '-';
        const dueDate = options.dueDate || '-';
        const formattedName = String(customerName).replace(/\b\w/g, (c) => c.toUpperCase());

        $("#print-receipt .invoice-user-name span:contains('Name:')")
            .next()
            .text(formattedName);
        $("#print-receipt .invoice-user-name span:contains('Customer Id:')")
            .next()
            .text(custId);
        $("#print-receipt .invoice-user-name span:contains('Invoice No:')")
            .next()
            .text(invoice);
        $("#print-receipt .invoice-user-name span:contains('Date:')")
            .next()
            .text(date);
        $("#print-due-date-row span:last").text(dueDate);
        $("#print-receipt .text-end:contains('Total Bill')")
            .next()
            .text(total);
        $("#print-receipt .text-end:contains('Total Payable')")
            .next()
            .text(total);

        $.ajax({
            url: "{{ route('admin.get-orderItems', '') }}/" + transactionId,
            type: "GET",
            success: function (response) {
                let tbody = $("#print-receipt tbody");
                tbody.empty();
                let subTotal = response.sub_total;
                let discount = response.discount;
                let shipping = response.shipping;
                let tax = response.tax;
                let totalBill = response.total_bill;
                let totalPayable = response.total_payable;
                let dueAmount = response.due_amount || 0;
                let paidAmount = response.paid_amount || 0;
                let responseDueDate = response.due_date || '-';
                let taxAmount = response.tax_amount != null
                    ? parseFloat(response.tax_amount) || 0
                    : (tax / 100) * subTotal;
                let discountAmount = response.discount_amount != null
                    ? parseFloat(response.discount_amount) || 0
                    : (discount / 100) * subTotal;
                let shippingAmount = response.shipping_amount != null
                    ? parseFloat(response.shipping_amount) || 0
                    : (shipping / 100) * subTotal;
                $("#print-due-date-row span:last").text(responseDueDate);
                $.each(response.items, function (index, item) {
                    let itemTotal = item.price * item.qty;
                    let row = `
                <tr>
                    <td>${item.product_name}</td>
                    <td>${formatCurrency(item.price)}</td>
                    <td>${item.qty}</td>
                    <td>${formatCurrency(itemTotal)}</td>
                </tr>`;
                    tbody.append(row);
                });

                const taxLabel = tax > 0 ? `Tax (${tax}%)` : 'Tax';
                const shippingLabel = shipping > 0 && shipping <= 100 ? `Shipping (${shipping}%)` : 'Shipping';

                let summary = `
                    <tr>
                        <td colspan="4">
                            <table class="table-borderless w-100 table-fit">
                                <tr>
                                    <td>Sub Total :</td>
                                    <td class="text-end">$${formatCurrency(subTotal)}</td>
                                </tr>
                                <tr>
                                    <td>Discount (${discount}%):</td>
                                    <td class="text-end">-$${formatCurrency(discountAmount)}</td>
                                </tr>
                                <tr>
                                    <td>${shippingLabel} :</td>
                                    <td class="text-end">$${formatCurrency(shippingAmount)}</td>
                                </tr>
                                <tr>
                                    <td>${taxLabel} :</td>
                                    <td class="text-end">$${formatCurrency(taxAmount)}</td>
                                </tr>
                                <tr>
                                    <td>Total Bill :</td>
                                    <td class="text-end">$${formatCurrency(totalBill)}</td>
                                </tr>
                                <tr>
                                    <td>Paid :</td>
                                    <td class="text-end">$${formatCurrency(paidAmount)}</td>
                                </tr>
                                <tr>
                                    <td>Due :</td>
                                    <td class="text-end">$${formatCurrency(dueAmount)}</td>
                                </tr>
                                <tr>
                                    <td>Total Payable :</td>
                                    <td class="text-end">$${formatCurrency(totalPayable)}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    `;

                tbody.append(summary);
                $("#print-receipt").modal("show");
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Print unavailable',
                    text: 'Invoice was saved but the print preview could not be loaded.',
                });
            }
        });
    };

    $(document).ready(function () {
        $(document).on('click', '.print-receipt', function () {
            window.openInvoicePrintReceipt($(this).data("id"), {
                customerName: $(this).data("customer"),
                date: $(this).data("date"),
                total: $(this).data("total"),
                invoice: $(this).data("invoice"),
                customerId: $(this).data("customerid"),
                dueDate: $(this).data("duedate") || '-',
            });
        });

        $('#print-receipt').on('hidden.bs.modal', function () {
            if (window._reloadAfterInvoicePrint) {
                window._reloadAfterInvoicePrint = false;
                window.location.reload();
                return;
            }
            if (window._redirectAfterInvoicePrint) {
                const url = window._redirectAfterInvoicePrint;
                window._redirectAfterInvoicePrint = null;
                window.location.href = url;
            }
        });
    });

    /*
    // Old inline print-receipt click handler (replaced by window.openInvoicePrintReceipt above):
    $(document).ready(function () {
        $(document).on('click', '.print-receipt', function () {
            ...
        });
    });
    */

    $(document).ready(function () {
        $("#printButton").click(function () {
            var modalBody = document.querySelector('#print-receipt .modal-body');
            if (!modalBody) return;

            var printContent = modalBody.innerHTML;
            var styles = '';
            document.querySelectorAll('link[rel="stylesheet"], style').forEach(function(el) {
                styles += el.outerHTML;
            });

            var iframe = document.getElementById('print-iframe');
            if (!iframe) {
                iframe = document.createElement('iframe');
                iframe.id = 'print-iframe';
                iframe.style.position = 'fixed';
                iframe.style.right = '0';
                iframe.style.bottom = '0';
                iframe.style.width = '0';
                iframe.style.height = '0';
                iframe.style.border = 'none';
                document.body.appendChild(iframe);
            }

            var doc = iframe.contentWindow.document;
            doc.open();
            doc.write('<html><head><title>Print Receipt</title>' + styles +
                '<style>.no-print{display:none!important} body{padding:20px;font-family:Arial,sans-serif;}</style>' +
                '</head><body>' + printContent + '</body></html>');
            doc.close();

            iframe.onload = function() {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            };
        });
    });


  

</script>
@include('admin.partials.invoice-placement-scripts', ['draftOrderId' => null])
@endpush
@push('style')
<style>
    .modal-backdrop+.modal-backdrop {
        z-index: 1060 !important;
    }

    .modal.show {
        z-index: 1061 !important;
        backdrop-filter: blur(40px);
    }

    #product_list {
        max-height: 200px;
        overflow-y: auto;
        position: absolute;
        z-index: 1000;
        width: 100%;
        display: none;
    }

    .modal-dialog.stock-adjust-modal {
        max-width: 1099px;
    }

    .payment-method .methods .default-cover a {
        box-shadow: rgba(190, 190, 190, 0.27) 0px 4px 60px 0px;
        text-align: center;
        display: block;
        font-size: 15px;
        color: rgb(9, 44, 76);
        padding: 10px 15px;
    }

    .payment-method .methods .default-cover span {
        padding: 13px 15px 12px 6px
    }

    .payment-method .methods .default-cover span img {
        margin: 5px 7px -4px 0;

    }

    .swal2-container {
        z-index: 2000 !important;
    }

    .btn-icon {
        width: 6.313rem;
        height: 2.313rem;
        font-size: 0.95rem;
        flex-shrink: 0;
    }
</style>
@endpush