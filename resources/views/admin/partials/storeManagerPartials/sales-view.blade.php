<div class="modal fade" id="sales-details-new">
    <div class="modal-dialog sales-details-modal">
        <div class="modal-content">
            <div class="page-wrapper details-blk">
                <div class="content p-0">
                    <div class="page-header p-4 mb-0">
                        <div class="add-item d-flex">
                            <div class="page-title modal-datail">
                                <h4>Sales Detail : </h4>
                            </div>
                        </div>
                        <ul class="table-top-head">
                            <li>
                                <a data-bs-toggle="tooltip" data-bs-placement="top" title="Pdf"><i data-feather="edit"
                                        class="action-edit sales-action"></i></a>
                            </li>
                            <li>
                                <a data-bs-toggle="tooltip" data-bs-placement="top" title="Pdf"><img
                                        src="assets/img/icons/pdf.svg" alt="img" /></a>
                            </li>
                            <li>
                                <a data-bs-toggle="tooltip" data-bs-placement="top" title="Excel"><img
                                        src="assets/img/icons/excel.svg" alt="img" /></a>
                            </li>
                            <li>
                                <a data-bs-toggle="tooltip" data-bs-placement="top" title="Print"><i
                                        data-feather="printer" class="feather-rotate-ccw"></i></a>
                            </li>
                        </ul>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <form>
                                <div class="invoice-box table-height" style="
                                        max-width: 1600px;
                                        width: 100%;
                                        overflow: auto;
                                        padding: 0;
                                        font-size: 14px;
                                        line-height: 24px;
                                        color: #555;
                                    ">

                                    <h5 class="order-text">Order Summary</h5>
                                    <div class="table-responsive no-pagination">
                                        <table class="table datanew">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Qty</th>
                                                    <th>Price($)</th>
                                                    <th>Paid($)</th>
                                                    <th>Due($)</th>
                                                    <th>payment Type</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                <tr>
                                                    <td>
                                                        <div class="productimgname">
                                                            <a href="javascript:void(0);" class="product-img stock-img">
                                                                <img src="" alt="product" />
                                                            </a>
                                                            <a href="javascript:void(0);">Product name</a>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="product-quantity">
                                                            <span class="quantity-btn">+<i data-feather="plus-circle"
                                                                    class="plus-circle"></i></span>
                                                            <input type="text" class="quntity-input" value="" />
                                                            <span class="quantity-btn"><i data-feather="minus-circle"
                                                                    class="feather-search"></i></span>
                                                        </div>
                                                    </td>
                                                    <td>0.00</td>
                                                    <td>0.00</td>
                                                    <td>0.00</td>
                                                    <td>payment_method</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="row">
                                        <div class="col-lg-6 ms-auto">
                                            <div class="total-order w-100 max-widthauto m-auto mb-4">
                                                <ul>
                                                    <li>
                                                        <h4>Order Tax</h4>
                                                        <h5>$ 0.00</h5>
                                                    </li>
                                                    <li>
                                                        <h4>Discount</h4>
                                                        <h5>$ 0.00</h5>
                                                    </li>
                                                    <li>
                                                        <h4>Shipping</h4>
                                                        <h5>$ 0.00</h5>
                                                    <li>
                                                        <h4>Grand Total</h4>
                                                        <h5>$ 0.00</h5>
                                                    </li>
                                                    <li>
                                                        <h4>Paid</h4>
                                                        <h5>$ 0.00</h5>
                                                    </li>
                                                    <li>
                                                        <h4>Due</h4>
                                                        <h5>$ 0.00</h5>
                                                    </li>
                                                </ul>
                                            </div>
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
</div>
<!-- /details popup -->