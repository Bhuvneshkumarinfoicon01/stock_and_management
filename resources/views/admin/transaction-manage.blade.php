@extends('layouts.app_admin') @section('title') Dashbaord @endsection
@section('styles') @endsection @section('content')

<div class="page-wrapper">
    <div class="content pos-design">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4>Transaction Management</h4>
                    <h6>Transaction Management</h6>
                </div>
            </div>
            <div class="page-btn">
                <a href="{{ route('admin.pos-terminal') }}" class="btn btn-added"><i data-feather="arrow-left"
                        class="feather-16 me-2"></i>Back To Pos Terminal</a>
            </div>
        </div>


        <div class="tabs-sets">
            <div class="tab-content">
                <div class="tab-pane fade show active" id="purchase" role="tabpanel" aria-labelledby="purchase-tab">
                    <div class="table-responsive">
                        <table class="table datanew">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Invoice No.</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th class="no-sort">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
                                <tr>
                                    <td>
                                        {{ format_date($transaction->created_at) }}
                                    </td>
                                    <td>INV/{{$transaction->id}}</td>
                                    <td>{{$transaction->customer_name}}</td>
                                    <td>${{$transaction->total_price}}</td>
                                    <td class="action-table-data">
                                        <div class="edit-delete-action">
                                            <a class="me-2 p-2 print-receipt" href="javascript:void(0);"
                                                data-bs-toggle="modal" data-bs-target="#print-receipt"
                                                data-id="{{$transaction->id}}"
                                                data-customer="{{$transaction->customer_name}}"
                                                data-customerid="{{$transaction->customer_id}}"
                                                data-date="{{ format_date($transaction->created_at) }}"
                                                data-total="${{$transaction->total_price}}"
                                                data-invoice="INV/{{$transaction->id}}"><i data-feather="printer"
                                                    class="feather-edit"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="payment" role="tabpanel">
                    <div class="table-top">
                        <div class="search-set">
                            <div class="search-input">
                                <a class="btn btn-searchset d-flex align-items-center h-100"><img
                                        src="assets/img/icons/search-white.svg" alt="img" /></a>
                            </div>
                        </div>
                        <div class="wordset">
                            <ul>
                                <li>
                                    <a class="d-flex align-items-center justify-content-center" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Pdf"><img src="assets/img/icons/pdf.svg"
                                            alt="img" /></a>
                                </li>
                                <li>
                                    <a class="d-flex align-items-center justify-content-center" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Excel"><img src="assets/img/icons/excel.svg"
                                            alt="img" /></a>
                                </li>
                                <li>
                                    <a class="d-flex align-items-center justify-content-center" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Print"><i data-feather="printer"
                                            class="feather-rotate-ccw"></i></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-default" id="print-receipt" aria-labelledby="print-receipt">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="d-flex justify-content-end">
                <button type="button" class="close p-0 no-print" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="icon-head text-center">
                    <a href="javascript:void(0);">
                        <h6>Accounting and Stock Management System</h6>
                    </a>
                </div>
                <div class="text-center info">
                    <h6>Accounting and Stock Management System,</h6>
                    <p class="mb-0">Phone Number: +1 5656665656</p>
                    <p class="mb-0">
                        Email:
                        <a href="mailto:email@gmail.com">email@gmail.com</a>
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
                    <a href="javascript:void(0);">
                        <img src="assets/img/barcode/barcode-03.jpg" alt="Barcode" />
                    </a>
                    <p>Sale 31</p>
                    <p>Thank You For Shopping With Us. Please Come Again</p>
                    <a href="javascript:void(0);" class="btn btn-primary no-print" id="printButton">Print Receipt</a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection @section('scripts')
<script>
    $(document).ready(function () {
        $(".print-receipt").click(function () {
            let transactionId = $(this).data("id");
            let customerName = $(this).data("customer");
            let date = $(this).data("date");
            let total = $(this).data("total");
            let invoice = $(this).data("invoice");
            let custId = $(this).data("customerid");
            let formattedName = customerName.replace(/\b\w/g, (c) =>
                c.toUpperCase()
            );

            // Update modal content
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
                    let taxAmount = (tax / 100) * subTotal;
                    let discountAmount = (discount / 100) * subTotal;
                    let shippingAmount = (shipping / 100) * subTotal;
                    $.each(response.items, function (index, item) {
                        let itemTotal = item.price * item.qty;
                        let row = `
                <tr>
                    <td>${item.product_name}</td>
                    <td>${item.price}</td>
                    <td>${item.qty}</td>
                    <td>${itemTotal.toFixed(2)}</td>
                </tr>`;
                        tbody.append(row);
                    });

                    // Append bill summary
                    let summary = `
                    <tr>
                        <td colspan="4">
                            <table class="table-borderless w-100 table-fit">
                                <tr>
                                    <td>Sub Total :</td>
                                    <td class="text-end">$${subTotal.toFixed(
                        2
                    )}</td>
                                </tr>
                                <tr>
                                    <td>Discount (${discount}%):</td>
                                    <td class="text-end">-$${discountAmount.toFixed(
                        2
                    )}</td>
                                </tr>
                                <tr>
                                    <td>Shipping (${shipping}%):</td>
                                    <td class="text-end">$${shippingAmount.toFixed(
                        2
                    )}</td>
                                </tr>
                                <tr>
                                    <td>Tax (${tax}%):</td>
                                    <td class="text-end">$${taxAmount.toFixed(
                        2
                    )}</td>
                                </tr>
                                <tr>
                                    <td>Total Bill :</td>
                                    <td class="text-end">$${totalBill.toFixed(
                        2
                    )}</td>
                                </tr>
                                <tr>
                                    <td>Due :</td>
                                    <td class="text-end">$0.00</td>
                                </tr>
                                <tr>
                                    <td>Total Payable :</td>
                                    <td class="text-end">$${totalPayable.toFixed(
                        2
                    )}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    `;

                    tbody.append(summary);
                },
            });
        });
    });

    $(document).ready(function () {
        $("#printButton").click(function () {
            let printContents =
                document.getElementById("print-receipt").outerHTML;
            let styles = document.head.innerHTML;

            let printWindow = window.open("", "", "width=800,height=600");
            printWindow.document.write(`
        <html>
        <head>
            <title>Print Receipt</title>
            ${styles}  <!-- This ensures all styles are included -->
        </head>
        <body>
            ${printContents}
            <script>
                document.querySelectorAll(".no-print").forEach(el => el.style.display = "none"); 
                window.print();
                window.close();
            <\/script>
        </body>
        </html>
        `);

            printWindow.document.close();
            printWindow.focus();
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 500);
        });
    });
</script>

@endsection