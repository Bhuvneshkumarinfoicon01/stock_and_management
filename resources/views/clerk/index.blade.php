@extends('layouts.app_admin')

@section('title')
Dashbaord
@endsection

@section('styles')

@endsection

@section('content')


<div class="page-wrapper">
    <div class="content">
        <div class="welcome d-lg-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center welcome-text">
                <h3 class="d-flex align-items-center">
                    {{--<!-- <img src="\storemanager\assets\img\hi.svg" alt="img"> -->--}}
                    &nbsp;Hi {{$users->name}},</h3>&nbsp;<h6>here's what's happening with your store today.</h6>
            </div>

        </div>
        <div class="row sales-cards">
            <div class="col-xl-8 col-sm-12 col-12">
                <div class="card d-flex align-items-center justify-content-between default-cover mb-4">
                    <div>
                        <h6>Monthly Earning</h6>
                        <h3>$<span class="counters" data-count="{{$monthlyEarning}}">${{$monthlyEarning}}</span></h3>
                        <p class="sales-range">
                            @if($percentageChange !== null)
                            <span class="text-{{ $percentageChange >= 0 ? 'success' : 'danger' }}">
                                <i data-feather="{{ $percentageChange >= 0 ? 'chevron-up' : 'chevron-down' }}"
                                    class="feather-16"></i>
                                {{ abs($percentageChange) }}%&nbsp;
                            </span>
                            {{ $percentageChange >= 0 ? 'increase' : 'decrease' }} compared to last month
                            @else
                            <span class="text-muted">No data to compare</span>
                            @endif
                        </p>

                    </div>
                    {{--<!-- <img src="\storemanager\assets/img/icons/weekly-earning.svg" alt="img"> -->--}}
                </div>
            </div>
            <div class="col-xl-4 col-sm-6 col-12">
                <div class="card color-info bg-primary mb-4">
                    {{--<!-- <img src="\storemanager\assets/img/icons/total-sales.svg" alt="img"> -->--}}
                    <h3 class="counters" data-count="{{$countOrder}}">{{$countOrder}}k</h3>
                    <p>No of Total Sales</p>
                    <i data-feather="rotate-ccw" class="feather-16" data-bs-toggle="tooltip" data-bs-placement="top"
                        title="Refresh"></i>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-12 col-xl-4 d-flex">
                <div class="card flex-fill default-cover w-100 mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Best Seller</h4>
                        <div class="dropdown">
                            <a href="{{route('admin.top-view-all')}}" class="view-all d-flex align-items-center">
                                View All<span class="ps-2 d-flex align-items-center">
                                    <i data-feather="arrow-right" class="feather-16"></i></span>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless best-seller">
                                <tbody>
                                    @foreach($bestSellers as $product)
                                    <tr>
                                        <td>
                                            <div class="product-info">
                                                <a href="#" class="product-img">
                                                    <img src="{{asset($product->images ? 'storage/'.$product->images : 'admin\assets\img\avatar\default-product.png')}}"
                                                        alt="product">
                                                </a>
                                                <div class="info">
                                                    <a href="#">{{$product->product_name}}</a>
                                                    <p class="dull-text">${{$product->price}}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="head-text">Sales</p>
                                            {{$product->total_sold}}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-12 col-xl-8 d-flex">
                <div class="card flex-fill default-cover w-100 mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Recent Transactions</h4>
                        <div class="dropdown">
                            <a href="{{ route('admin.sales') }}" class="view-all d-flex align-items-center">
                                View All<span class="ps-2 d-flex align-items-center"><i data-feather="arrow-right"
                                        class="feather-16"></i></span>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless recent-transactions">
                                <thead>
                                    <tr>
                                        <th># OrderId</th>
                                        <th>Order Details</th>
                                        <th>Payment</th>
                                        <th>Status</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->order_id }}</td>
                                        <td>
                                            <div class="product-info">
                                                <a href="#" class="product-img">
                                                    <img src="{{asset($transaction->images ? 'storage/'.$transaction->images : 'admin\assets\img\avatar\default-product.png')}}"
                                                        alt="product">
                                                </a>
                                                <div class="info">
                                                    <a href="#">{{ $transaction->product_name }}</a>
                                                    <span class="dull-text d-flex align-items-center">
                                                        <i data-feather="clock" class="feather-14"></i>{{
                                                        \Carbon\Carbon::parse($transaction->created_at)->diffForHumans()
                                                        }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="d-block head-text">{{ $transaction->payment_method }}</span>

                                        </td>
                                        <td>
                                            @if($transaction->status == 0)
                                            <span class="badge badge-linedanger">Unpaid</span>
                                            @else
                                            <span class="badge badge-linesuccess">Paid</span>
                                            @endif
                                        </td>
                                        <td>${{ number_format($transaction->total_price, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Button trigger modal -->

        <div class="row sales-board">
            <div class="col-md-12 col-lg-12 col-sm-12 col-12">
                <div class="card flex-fill default-cover">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Sales Analytics</h5>
                        <div class="graph-sets">
                            <div class="dropdown dropdown-wraper">
                                <button class="btn btn-white btn-sm dropdown-toggle d-flex align-items-center"
                                    type="button" id="dropdown-sales" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i data-feather="calendar" class="feather-14"></i>{{ $selectedYear }}
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdown-sales">
                                    @foreach($availableYears as $year)
                                    <li>
                                        <a href="{{ url()->current() . '?year=' . $year }}" class="dropdown-item">
                                            {{ $year }}
                                        </a>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="sales-analysis-chart" class="chart-set"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




@endsection

@section('scripts')
@if(session('success'))
<script>
    document.addEventListener("DOMContentLoaded", function () {
        Swal.fire({
            title: "Success!",
            text: "{{ session('success') }}",
            icon: "success",
            confirmButtonText: "OK"
        });
    });
</script>
@endif
<script>
    const monthlyRevenueData = @json($monthlyRevenueData);
    const selectedYear = @json($selectedYear);

    const options = {
        chart: {
            type: 'line',
            height: 350,
            toolbar: { show: false }
        },
        series: [{
            name: 'Revenue (K)',
            data: monthlyRevenueData
        }],
        xaxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
        },
        title: {
            text: `Monthly Revenue - ${selectedYear}`,
            align: 'left',
            style: {
                fontSize: '16px',
                fontWeight: 'bold'
            }
        },
        yaxis: {
            labels: {
                formatter: val => `${val}K`
            },
            title: {
                text: 'Revenue (in Thousands)'
            }
        },
        colors: ['#00b894']
    };

    const chart = new ApexCharts(document.querySelector("#sales-analysis-chart"), options);
    chart.render();
</script>

@endsection