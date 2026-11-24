@extends('layouts.app_admin') @section('title') Dashbaord @endsection
@section('styles') @endsection @section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4>Report Section</h4>
                </div>
            </div>
            <ul class="table-top-head">
                <!-- <li>
                    <a
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="Pdf"
                        ><img src="assets/img/icons/pdf.svg" alt="img"
                    /></a>
                </li>
                <li>
                    <a
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="Excel"
                        ><img src="assets/img/icons/excel.svg" alt="img"
                    /></a>
                </li>
                <li>
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
                    <a
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="Refresh"
                        id="refresh-page"
                        ><i
                            data-feather="rotate-ccw"
                            class="feather-rotate-ccw"
                        ></i
                    ></a>
                </li>
                <li>
                    <a
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="Collapse"
                        id="collapse-header"
                        ><i
                            data-feather="chevron-up"
                            class="feather-chevron-up"
                        ></i
                    ></a>
                </li>
            </ul>
        </div>

        <div class="row mt-5">
            <div class="col-xl-12 col-sm-12 col-12">
                <div class="card table-list-card">
                    <div class="add-item ps-4 pt-4 pb-4">
                        <div class="page-title">
                            <h4>Sales Report</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table datanew">
                                <thead>
                                    <tr>
                                        <th>Customer Name</th>
                                        <th>Reference</th>
                                        <th>Date</th>
                                        <th>Payment Status</th>
                                        <th>Grand Total</th>
                                        <th>Paid</th>
                                        <th>Due</th>
                                        <th>Payment Method</th>
                                        <th>Biller</th>
                                    </tr>
                                </thead>
                                <tbody class="sales-list">
                                    @foreach($orders as $order)
                                    <tr>
                                        <td>{{$order->customer_name}}</td>
                                        <td>INV/{{$order->id}}</td>
                                        <td>{{ format_date($order->created_at) }}</td>
                                        <td>
                                            @if($order->status==0)
                                            <span class="badge badge-linedanger"
                                                >Unpaid</span>
                                            @else
                                            <span class="badge badge-linesuccess"
                                                >Paid</span>
                                            @endif    
                                        </td>
                                        <td>${{$order->total_price}}</td>
                                        <td>${{$order->total_price}}</td>
                                        <td>$0.00</td>
                                        <td>{{$order->payment_method}}</td>
                                        <td>Admin</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-12 col-sm-12 col-12">
                <div class="card table-list-card">
                    <div class="add-item ps-4 pt-4 pb-4">
                        <div class="page-title">
                            <h4>Customer Report</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table datanew">
                                <thead>
                                    <tr>
                                        <th>Customer ID</th>
                                        <th>Customer Name</th>
                                        <th>Amount</th>
                                        <th>Paid</th>
                                        <th>Due Amount</th>
                                        <th>Status</th>
                                        <th>Payment Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                    <tr>
                                        <td>{{$order->customer_id}}</td>
                                        <td>{{$order->customer_name}}</td>
                                        <td>${{$order->total_price}}</td>
                                        <td>${{$order->total_price}}</td>
                                        <td>$0.00</td>
                                        <td>
                                            <span class="badges status-badge"
                                                >Completed</span
                                            >
                                        </td>
                                        <td>
                                            @if($order->status==0)
                                            <span class="badge badge-linedanger"
                                                >Unpaid</span>
                                            @else
                                            <span class="badge badge-linesuccess"
                                                >Paid</span>
                                            @endif    
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
