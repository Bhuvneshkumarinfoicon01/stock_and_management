@extends('layouts.app_admin') @section('title') Dashbaord @endsection
@section('styles') @endsection @section('content')

<div class="page-wrapper">
    <div class="content pos-design">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4>Point of Sale (POS)</h4>
                    <h6>Manage Your Point of Sale</h6>
                </div>
            </div>

            <div class="page-btn">
                <a href="{{ route('admin.transaction-manage') }}" class="btn btn-added"><i data-feather="shopping-cart"
                        class="feather-16 me-2"> </i>View Transactions</a>
            </div>
        </div>



        <div class="row align-items-start pos-wrapper">
            <div class="col-md-12 col-lg-8">
                <div class="pos-categories tabs_wrapper">
                    <h5>Categories</h5>
                    <p>Select From Below Categories</p>

                    <!-- <select id="ca" class="select">
                        <option value="">Choose Category</option>
                        @foreach($categories as $category)
                        <option>{{$category->name??''}}</option>
                        @endforeach
                    </select> -->
                    <ul class="tabs owl-carousel pos-category">
                        @foreach($categories as $category)
                        <li id="{{$category->name}}">
                            <a href="javascript:void(0);">
                                <img src="assets/img/categories/category-02.webp" alt="Categories" />
                            </a>
                            <h6>
                                <a href="javascript:void(0);">{{$category->name}}</a>
                            </h6>
                            <span>{{ $categoryProductCounts[$category->id] ?? 0 }} Items</span>

                        </li>
                        @endforeach
                    </ul>
                    <div class="pos-products">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-3">Products</h5>
                        </div>
                        <div class="tabs_container">
                            @foreach ($categories as $category)
                            <div class="tab_content @if($loop->first) active @endif" data-tab="{{ $category->name }}">
                                <div class="row">
                                    @if(isset($categoryProducts[$category->id]) &&
                                    count($categoryProducts[$category->id]) > 0)
                                    @foreach ($categoryProducts[$category->id] as $product)
                                    <div class="col-sm-2 col-md-6 col-lg-3 col-xl-3 pe-2">
                                        <div class="product-info default-cover card">
                                            <a href="javascript:void(0);" class="img-bg add-to-cart"
                                                data-product-id="{{ $product->product_id }}"
                                                data-product-name="{{ $product->product_name }}"
                                                data-product-price="{{ $product->price }}"
                                                data-product-sku="{{ $product->sku }}"
                                                data-product-category_id="{{ $product->category_id }}"
                                                data-product-image="{{ asset($product->images ? 'storage/'.$product->images : 'admin/assets/img/avatar/default-product.png') }}">
                                                <img class="p-1"
                                                    src="{{ asset($product->images ? 'storage/'.$product->images : 'admin/assets/img/avatar/default-product.png') }}"
                                                    alt="Products" />
                                                <span><i data-feather="check" class="feather-16"></i></span>
                                            </a>
                                            <h6 class="cat-name"><a href="javascript:void(0);">{{ $category->name }}</a>
                                            </h6>
                                            <h6 class="product-name"><a href="javascript:void(0);">{{
                                                    $product->product_name }}</a></h6>
                                            <div class="d-flex align-items-center justify-content-between price">
                                                <span>Qty: {{ $product->quantity }}</span>
                                                <p>${{ $product->price }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                    @else
                                    <div class="col-12 text-center text-muted py-4">
                                        No products found for {{ $category->name }}.
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-lg-4 ps-0">
                <aside class="product-order-list">
                    <div class="head d-flex align-items-center justify-content-between w-100">
                        <div class="">
                            <h5>Order List</h5>
                            <span>Transaction ID : #65565</span>
                        </div>
                        <div class="">
                            <a class="confirm-text" href="javascript:void(0);"><i data-feather="trash-2"
                                    class="feather-16 text-danger"></i></a>
                            <a href="javascript:void(0);" class="text-default"><i data-feather="more-vertical"
                                    class="feather-16"></i></a>
                        </div>
                    </div>
                    <div class="customer-info block-section">
                        <h6>Customer Information</h6>
                        <div class="input-block d-flex align-items-center">
                            <div class="flex-grow-1">
                                <select class="select" name="customer_id" id="customer-select">
                                    <option value="">Choose Customer</option>
                                    @foreach($customers as $customer)
                                    <option value="{{$customer->id}}">
                                        {{$customer->customer_name}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <a href="#" class="btn btn-primary btn-icon" data-bs-toggle="modal"
                                data-bs-target="#create"><i data-feather="user-plus" class="feather-16"></i></a>
                        </div>
                    </div>

                    <div class="product-added block-section">
                        <div class="head-text d-flex align-items-center justify-content-between">
                            <h6 class="d-flex align-items-center mb-0">
                                Product Added <span class="count">0</span>
                            </h6>
                            <a href="javascript:void(0);" class="d-flex align-items-center text-danger" id="clear-all">
                                <span class="me-1"><i data-feather="x" class="feather-16"></i></span>Clear all
                            </a>
                        </div>
                        <div class="product-wrap" id="product-list">
                            <!-- Dynamically added products will appear here -->
                        </div>
                    </div>
                    <div class="block-section">
                        <div class="selling-info">
                            <div class="row">
                                <div class="col-12 col-sm-4">
                                    <div class="input-block">
                                        <label>Order Tax</label>
                                        <select id="taxDropdown" class="select">
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
                                <div class="col-12 col-sm-4">
                                    <div class="input-block">
                                        <label>Shipping</label>
                                        <select id="shippingDropdown" class="select">
                                            <option value="0">
                                                Free Shipping
                                            </option>
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
                                <div class="col-12 col-sm-4">
                                    <div class="input-block">
                                        <label>Discount</label>
                                        <select id="discountDropdown" class="select">
                                            <option value="0">
                                                No Discount
                                            </option>
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
                                <div class="col-12 col-sm-4">
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
                                @endif
                            </div>
                        </div>
                        <div class="order-total">
                            <table class="table table-responsive table-borderless">
                                <tr>
                                    <td>Sub Total</td>
                                    <td class="text-end" id="sub-total">
                                        $0.00
                                    </td>
                                </tr>
                                <tr>
                                    <td id="tax_percentage">Tax (GST 0%)</td>
                                    <td class="text-end" id="tax">$0.00</td>
                                </tr>
                                <tr>
                                    <td id="shipping_percentage">Shipping (0%)</td>
                                    <td class="text-end" id="shipping">
                                        $0.00
                                    </td>
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

                    <div class="block-section payment-method">
                        <h6>Payment Method</h6>
                        <div class="row d-flex align-items-center justify-content-center methods">
                            <div class="col-md-6 col-lg-4 item cash-method">
                                <div class="default-cover">
                                    <a href="javascript:void(0);" id="cashPayment">
                                        <img src="https://dreamspos.dreamstechnologies.com/html/template/assets/img/icons/cash-pay.svg"
                                            alt="Payment Method" />
                                        <span>Cash</span>
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4 item">
                                <div class="default-cover">
                                    <a href="javascript:void(0);">
                                        <img src="https://dreamspos.dreamstechnologies.com/html/template/assets/img/icons/credit-card.svg"
                                            alt="Payment Method" />
                                        <span>Debit Card</span>
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4 item">
                                <div class="default-cover">
                                    <a href="javascript:void(0);">
                                        <img src="https://dreamspos.dreamstechnologies.com/html/template/assets/img/icons/qr-scan.svg"
                                            alt="Payment Method" />
                                        <span>Scan</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-grid btn-block">
                        <a id="grandTotalBtn" class="btn btn-secondary" href="javascript:void(0);">
                            Grand Total: $<span id="grandTotalValue">0.00</span>
                        </a>
                    </div>

                </aside>
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

<!-- Hold -->
<div class="modal fade modal-default pos-modal" id="hold-order" aria-labelledby="hold-order">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header p-4">
                <h5>Hold order</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <form>
                    <h2 class="text-center p-4">4500.00</h2>
                    <div class="input-block">
                        <label>Order Reference</label>
                        <input class="form-control" type="text" value="" placeholder="" />
                    </div>
                    <p>
                        The current order will be set on hold. You can retreive
                        this order from the pending order button. Providing a
                        reference to it might help you to identify the order
                        more quickly.
                    </p>
                    <div class="modal-footer d-sm-flex justify-content-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Confirm
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Hold -->

<!-- Edit Product -->
<!-- <div class="modal fade modal-default pos-modal" id="edit-product" aria-labelledby="edit-product">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header p-4">
                <h5>Red Nike Laser</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <form>
                    <div class="row">
                        <div class="col-lg-6 col-sm-12 col-12">
                            <div class="input-blocks add-product">
                                <label>Product Name <span>*</span></label>
                                <input type="text" placeholder="45" />
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12 col-12">
                            <div class="input-blocks add-product">
                                <label>Tax Type <span>*</span></label>
                                <select class="select">
                                    <option>Exclusive</option>
                                    <option>Inclusive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12 col-12">
                            <div class="input-blocks add-product">
                                <label>Tax <span>*</span></label>
                                <input type="text" placeholder="% 15" />
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12 col-12">
                            <div class="input-blocks add-product">
                                <label>Discount Type <span>*</span></label>
                                <select class="select">
                                    <option>Percentage</option>
                                    <option>Early payment discounts</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12 col-12">
                            <div class="input-blocks add-product">
                                <label>Discount <span>*</span></label>
                                <input type="text" placeholder="15" />
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12 col-12">
                            <div class="input-blocks add-product">
                                <label>Sale Unit <span>*</span></label>
                                <select class="select">
                                    <option>Kilogram</option>
                                    <option>Grams</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer d-sm-flex justify-content-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> -->
<!-- /Edit Product -->

<!-- View Order -->
<!-- <div class="modal fade pos-modal" id="orders" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header p-4">
                <h5 class="modal-title">Orders</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="tabs-sets">
                    <ul class="nav nav-tabs" id="myTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="onhold-tab" data-bs-toggle="tab"
                                data-bs-target="#onhold" type="button" aria-controls="onhold" aria-selected="true"
                                role="tab">
                                Onhold
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="unpaid-tab" data-bs-toggle="tab" data-bs-target="#unpaid"
                                type="button" aria-controls="unpaid" aria-selected="false" role="tab">
                                Unpaid
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="paid-tab" data-bs-toggle="tab" data-bs-target="#paid"
                                type="button" aria-controls="paid" aria-selected="false" role="tab">
                                Paid
                            </button>
                        </li>
                    </ul>

                </div>
            </div>
        </div>
    </div>
</div> -->

@endsection @section('scripts')

<script>
    $(document).ready(function () {
        updateProductCount();

        // Fetch cart from session
        $.ajax({
            url: "{{ route('admin.get.cart') }}",
            method: "GET",
            success: function (response) {
                cart = response.cart || {}; // If response.cart exists, use it; otherwise, keep an empty cart

                // sessionStorage.setItem("cart", JSON.stringify(cart)); // Persist cart data
                // console.log(
                //     "Cart stored in sessionStorage:",
                //     sessionStorage.getItem("cart")
                // );
                if (response.cart) {
                    $("#product-list").empty();
                    $.each(response.cart, function (index, product) {
                        appendProductToCart(product);
                    });
                    updateProductCount();
                    calculateTotal();
                }
            },
        });

        // Handle the product selection and add it to the cart
        $(".add-to-cart").click(function () {
            var productId = $(this).data("product-id");
            var productName = $(this).data("product-name");
            var productPrice = $(this).data("product-price");
            var productImage = $(this).data("product-image");
            var productSku = $(this).data("product-sku");
            var productCategory = $(this).data("product-category_id");
            var existingProduct = $("#product-list .product-list").filter(
                function () {
                    return $(this).data("product-id") === productId;
                }
            );
            console.log("product price:", productPrice);

            if (existingProduct.length > 0) {
                Swal.fire({
                    icon: "warning",
                    title: "Product already in the cart!",
                    text: "This product is already added to your cart.",
                });
                return;
            }

            $.ajax({
                url: "{{route('admin.add.to.cart')}}",
                method: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                    product_id: productId,
                    product_name: productName,
                    price: productPrice,
                    image: productImage,
                    sku: productSku,
                    category_id: productCategory,
                },

                success: function (response) {
                    if (response.success) {
                        console.log("response:", response);
                        refreshCart();
                        updateProductCount();
                        calculateTotal();
                    } else {
                        Swal.fire({
                            icon: "warning",
                            title: response.message,
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Error adding product to cart!",
                    });
                },
            });
        });
        // Function to append a product to the cart
        function appendProductToCart(product) {
            var productHtml = `
            <div class="product-list d-flex align-items-center justify-content-between" data-product-id="${product.product_id}" data-product-sku="${product.sku}" data-category-id="${product.category_id}" data-product-price="${product.price}">
                <div class="d-flex align-items-center product-info" data-bs-toggle="modal" data-bs-target="#products">
                    <a href="javascript:void(0);" class="img-bg cart-list">
                        <img src="${product.image}" alt="Products" />
                    </a>
                    <div class="info">
                        <span>${product.product_id}</span>
                        <h6><a href="javascript:void(0);">${product.product_name}</a></h6>
                        <p>$${product.price}</p>
                    </div>
                </div>
                <div class="qty-item text-center">
                    <a href="javascript:void(0);" class="dec">-</a>
                    <input type="text" class="form-control text-center qty-input" value="${product.quantity}" />
                    <a href="javascript:void(0);" class="inc">+</a>
                </div>
                <div class="d-flex align-items-center action">
                    <a class="btn-icon delete-icon" href="javascript:void(0);">
                        <i data-feather="trash-2"></i>
                    </a>
                </div>
            </div>
        `;

            $("#product-list").append(productHtml);
            feather.replace();
        }
        window.refreshCart = function () {
            $.ajax({
                url: "{{ route('admin.get.cart') }}",
                method: "GET",
                success: function (response) {
                    if (response.cart) {
                        console.log("Cart fetched from server:", response.cart);
                        cart = response.cart;
                        sessionStorage.setItem("cart", JSON.stringify(cart));
                        $("#product-list").empty();
                        $.each(cart, function (index, product) {
                            appendProductToCart(product);
                        });

                        updateProductCount();
                        calculateTotal();
                    }
                },
                error: function (xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                },
            });
        };

        // MANUAL PRODUCT REMOVE/DELETE BUTTON
        $("#product-list").on("click", ".delete-icon", function () {
            var productElement = $(this).closest(".product-list");
            var productId = productElement.data("product-id");

            $(".product-info").each(function () {
                var currentProductId = $(this)
                    .find(".add-to-cart")
                    .data("product-id");
                if (currentProductId === productId) {
                    $(this).removeClass("active");
                }
            });
            $.ajax({
                url: "{{route('admin.remove.from.cart')}}",
                method: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                    product_id: productId,
                },
                success: function (response) {
                    refreshCart();
                    updateProductCount();
                    calculateTotal();
                },
                error: function () {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Error removing product from cart!",
                    });
                },
            });
        });

        // Clear all products
        $("#clear-all").click(function () {
            $.ajax({
                url: "{{ route('admin.clear.cart') }}",
                method: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (response) {
                    refreshCart();
                    $(".product-info").removeClass("active");
                    updateProductCount();
                    calculateTotal();
                    Swal.fire({
                        icon: "success",
                        title: "Cart Cleared",
                        text: "All products have been removed from the cart.",
                    });
                },
                error: function () {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Error clearing the cart!",
                    });
                },
            });
        });
        function updateProductCount() {
            var count = $(".product-list").length;
            $(".count").text(count);
        }
    });
</script>

<script>
    $(document).ready(function () {
        $(document).on("click", ".inc, .dec", function () {
            let inputField = $(this).siblings(".qty-input");
            let currentValue = parseInt(inputField.val());
            let isIncrement = $(this).hasClass("inc");
            let productId = $(this).closest(".product-list").data("product-id");

            if (!isNaN(currentValue)) {
                let newValue = isIncrement
                    ? currentValue + 1
                    : Math.max(1, currentValue - 1);
                inputField.val(newValue);
                $.ajax({
                    url: "{{ route('admin.add.to.cart') }}",
                    method: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        product_id: productId,
                        quantity: newValue,
                    },
                    // beforeSend: function () {
                    //     console.log("Sending AJAX request:", {
                    //         product_id: productId,
                    //         quantity: newValue,
                    //     });
                    // },
                    success: function (response) {
                        console.log("Cart updated successfully:", response);
                        if (typeof refreshCart === "function") {
                            refreshCart();
                        } else {
                            console.warn(
                                "refreshCart function is not defined in this scope."
                            );
                        }
                    },

                    error: function (xhr, status, error) {
                        console.error("AJAX Error:", xhr.responseText);
                        Swal.fire({
                            icon: "error",
                            title: "Oops...",
                            text: "Error updating cart quantity!",
                        });
                    },
                });
            }
        });
    });
</script>

<script>
    $("#create-customer-form").on("submit", function (event) {
        event.preventDefault();
        var customerData = $(this).serialize();

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
        });
    });

</script>
<script>
    function calculateTotal() {
        let subTotal = 0;

        $(".product-list").each(function () {
            let price = parseFloat($(this).data("product-price")) || 0;
            let qty = parseInt($(this).find(".qty-input").val()) || 0;
            subTotal += price * qty;
        });

        // Fetch extra values from dropdowns
        let tax = parseFloat($("#taxDropdown").val()) || 0;
        let shipping = parseFloat($("#shippingDropdown").val()) || 0;
        let discount = parseFloat($("#discountDropdown").val()) || 0;

        let taxAmount = (tax / 100) * subTotal;
        let discountAmount = (discount / 100) * subTotal;
        let shippingAmount = (shipping / 100) * subTotal;

        let total = subTotal + taxAmount + shippingAmount - discountAmount;

        $("#sub-total").text(subTotal.toFixed(2));
        $("#tax_percentage").text(`Tax (GST ${tax}%)`);
        $("#tax").text(taxAmount.toFixed(2));
        $("#shipping_percentage").text(`Shipping (${shipping}%)`);
        $("#shipping").text(shippingAmount.toFixed(2));
        $("#discount_percentage").text(`Discount (${discount}%)`);
        $("#discount").text(`-${discountAmount.toFixed(2)}`);
        $("#total").text(total.toFixed(2));

        updateGrandTotal(); // in case there's another level of grand total
    }

    function updateGrandTotal() {
        let totalText = $("#total").text().trim();
        let totalPrice = parseFloat(totalText.replace(/[^\d.]/g, "")) || 0;
        // console.log("Grand Total Updated:", totalPrice);
        $("#grandTotalValue").text(totalPrice.toFixed(2));
    }

    $(document).on("input", ".qty-input", function () {
        calculateTotal();
    });
    $(document).ready(function () {
        calculateTotal();

        let paymentMethod = null;
        $(".methods .item a").click(function () {
            $(".methods .item a").removeClass("selected");
            $(this).addClass("selected");
            paymentMethod = $(this).find("span").text().trim().toLowerCase();
            // console.log("Selected Payment Method:", paymentMethod); // Debugging log
        });
        $(".btn-secondary").click(function () {
            refreshCart();
            setTimeout(() => {
                console.log("Cart before submitting order:", cart);
                submitOrder(paymentMethod);
            }, 200);
        });

        function submitOrder(paymentMethod) {
            calculateTotal();
            let subtotalPrice = parseFloat($("#sub-total").text()) || 0;
            let totalPrice = parseFloat($("#total").text()) || 0;
            let customerId = $("#customer-select").val();
            let customerName = $("#customer-select option:selected")
                .text()
                .trim();
            let items = [];
            $(".product-list").each(function () {
                items.push({
                    product_id: $(this).data("product-id"),
                    product_name: $(this).find(".info h6 a").text(),
                    qty: parseInt($(this).find(".qty-input").val()),
                    price: parseFloat($(this).data("product-price")),
                    sku: $(this).data("product-sku"),
                    category_id: $(this).data("product-category_id"),
                });
            });
            if (!customerId || customerId === "") {
                Swal.fire({
                    title: "Customer Required!",
                    text: "Please select a customer before placing the order.",
                    icon: "warning",
                    confirmButtonText: "OK",
                });
                return;
            }
            if (items.length === 0) {
                Swal.fire({
                    icon: "warning",
                    title: "Cart is Empty!",
                    text: "Please add products to the cart before placing an order.",
                    confirmButtonText: "OK",
                });
                return;
            }
            if (!paymentMethod || paymentMethod === "") {
                Swal.fire({
                    title: "Payment Method Required!",
                    text: "Please select a payment method before placing the order.",
                    icon: "warning",
                    confirmButtonText: "OK",
                });
                return;
            }

            let orderData = {
                store_id: $("#store_id").val() || null,
                customer_id: customerId,
                customer_name: customerName,
                subtotal_price: subtotalPrice,
                total_price: totalPrice,
                tax: parseFloat($("#taxDropdown").val()) || 0,
                shipping: parseFloat($("#shippingDropdown").val()) || 0,
                discount: parseFloat($("#discountDropdown").val()) || 0,
                payment_method: paymentMethod,
                notes: $("#order_notes").val() || "",
                items: Object.values(cart).map((item) => {
                    return {
                        product_id: item.product_id,
                        product_name: item.product_name,
                        qty: parseInt(item.quantity, 10), // Convert to integer
                        price: parseFloat(item.price), // Convert to float
                        sku: item.sku, // Ensure sku is a string
                        category_id: item.category_id,
                    };
                }),
            };
            console.log("Final Order Data:", orderData);

            // $(".product-list").each(function () {
            //     orderData.items.push({
            //         product_id: $(this).data("product-id"),
            //         product_name: $(this).find(".info h6 a").text(),
            //         qty: parseInt($(this).find(".qty-input").val()),
            //         sku: $(this).data("product-sku"),
            //         price: parseFloat($(this).data("product-price")),
            //     });
            // });
            // console.log("sku", items);

            $.ajax({
                url: "{{ route('admin.place-order') }}",
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                data: JSON.stringify(orderData),
                contentType: "application/json",
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: "success",
                            title: "Order Placed!",
                            text:
                                response.message ||
                                "The order was placed successfully.",
                        }).then(() => {
                            window.location.href =
                                "{{ route('admin.transaction-manage') }}";
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Order Failed!",
                            text:
                                response.message ||
                                "Something went wrong while placing the order.",
                        });
                    }
                },
                error: function (xhr) {
                    let errorMessage =
                        xhr.responseJSON?.message ||
                        "Server Error! Please try again.";
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: errorMessage,
                    });
                },
            });
        }
    });

    // Prevent browser from loading old page data when using back button
    window.addEventListener("pageshow", function (event) {
        if (event.persisted) {
            location.reload();
        }
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".store-select").forEach(function (item) {
            item.addEventListener("click", function () {
                const storeId = this.getAttribute("data-id");
                console.log("Store clicked:", storeId);

                fetch("{{ route('admin.select.store') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    },
                    body: JSON.stringify({ store_id: storeId }),
                })
                    .then((response) => {
                        console.log("Raw response:", response);
                        return response.json();
                    })
                    .then((data) => {
                        console.log("Fetch response:", data);
                        location.reload();
                    })
                    .catch((error) => {
                        console.error("Fetch error:", error);
                    });
            });
        });
    });
    $(document).on(
        "change",
        "#taxDropdown, #shippingDropdown, #discountDropdown",
        function () {
            calculateTotal();
        }
    );
</script>
@endsection