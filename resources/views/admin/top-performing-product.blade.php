@extends('layouts.app_admin') @section('title') Dashbaord @endsection
@section('styles') @endsection @section('content')

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title me-auto">
                <h4>Product Performance Report</h4>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 col-lg-6 col-sm-12 col-12">
                <div class="card flex-fill default-cover w-100 mb-4">
                    <div
                        class="card-header d-flex justify-content-between align-items-center"
                    >
                        <h4 class="card-title mb-0">Top Performing Products</h4>
                        <div class="dropdown">
                            <a
                                href="{{route('admin.top-view-all')}}"
                                class="view-all d-flex align-items-center"
                            >
                           
                                View All<i data-feather="arrow-right"
                            class="feather-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless best-seller">
                                <tbody>
                                    @foreach($topPerformingProducts as $topProduct)
                                    <tr>
                                        <td>
                                            <div class="product-info">
                                                <a href="#" class="product-img">
                                                    <img
                                                        
                                                        src="{{asset($topProduct->stock->images ? 'storage/'.$topProduct->stock->images : 'admin\assets\img\avatar\default-product.png')}}"
                                                        alt="product"
                                                    />
                                                </a>
                                                <div class="info">
                                                    <a href="#"
                                                        >{{$topProduct->product_name}}</a
                                                    >
                                                    <p class="dull-text">
                                                        ${{ number_format($topProduct->sold_amount,2) }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="head-text">Sales</p>
                                            {{$topProduct->sold_qty}}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 col-md-12">
                <div class="card flex-fill">
                    <div
                        class="card-header d-flex justify-content-between align-items-center"
                    >
                        <h5 class="card-title mb-0">Top Performing Products</h5>

                        <div class="search-path profit-head">
                            <div class="input-blocks mb-0">
                                <i data-feather="calendar" class="info-img"></i>
                                <div class="input-groupicon">
                                    <div class="p-4">
                                        <select id="year-filter-top" class="form-control">
                                            <option value="">Select Year</option>
                                            @foreach($availableYears as $year)
                                                <option value="{{ $year }}">{{ $year }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="donut-chart-topProduct" class="chart-set"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 col-lg-6 col-sm-12 col-12">
                <div class="card flex-fill default-cover w-100 mb-4">
                    <div
                        class="card-header d-flex justify-content-between align-items-center"
                    >
                        <h4 class="card-title mb-0">
                            Underperforming Performing Products
                        </h4>
                        <div class="dropdown">
                            <a
                                href="{{route('admin.under-view-all')}}"
                                class="view-all d-flex align-items-center"
                            >
                           
                                View All<i data-feather="arrow-right"
                            class="feather-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless best-seller">
                                <tbody>
                                    @foreach($underPerformingProducts as $underProduct)
                                    <tr>
                                        <td>
                                            <div class="product-info">
                                                <a href="#" class="product-img">
                                                    <img
                                                        
                                                        src="{{asset($underProduct->stock->images ? 'storage/'.$underProduct->stock->images : 'admin\assets\img\avatar\default-product.png')}}"
                                                        alt="product"
                                                    />
                                                </a>
                                                <div class="info">
                                                    <a href="#"
                                                        >{{$underProduct->product_name}}</a
                                                    >
                                                    <p class="dull-text">
                                                        ${{ number_format($underProduct->sold_amount ,2) }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="head-text">Sales</p>
                                            {{$underProduct->sold_qty}}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 col-md-12">
                <div class="card flex-fill">
                    <div
                        class="card-header d-flex justify-content-between align-items-center"
                    >
                        <h5 class="card-title mb-0">
                            Underperforming Performing Products
                        </h5>

                        <div class="search-path profit-head">
                            <div class="input-blocks mb-0">
                                <i data-feather="calendar" class="info-img"></i>
                                <div class="input-groupicon">
                                    <div class="p-4">
                                        <select id="year-filter-under" class="form-control">
                                            <option value="">Select Year</option>
                                            @foreach($availableYears as $year)
                                                <option value="{{ $year }}">{{ $year }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="donut-chart-underProduct" class="chart-set"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script>
    // Function to update Top Performing Products chart
function updateTopProductChart(seriesData, labelData) {
    console.log('Top Product Chart Data:', seriesData, labelData);

    var donutChartTopProduct = {
        chart: {
            height: 350,
            type: 'donut',
            toolbar: {
                show: false
            }
        },
        colors: ['#4361ee', '#888ea8', '#e3e4eb', '#d3d3d3'],
        series: seriesData, // Use the response data for series
        labels: labelData,
        tooltip: {
            y: {
                formatter: function (value) {
                    // Format the tooltip value to "k" format
                    return (value / 1000).toFixed(1) + 'k';
                }
            }
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    };

    var donut = new ApexCharts(document.querySelector("#donut-chart-topProduct"), donutChartTopProduct);
    donut.render();
}

// Function to update Underperforming Products chart
function updateUnderProductChart(seriesData, labelData) {

    var donutChartUnderProduct = {
        chart: {
            height: 350,
            type: 'donut',
            toolbar: {
                show: false
            }
        },
        colors: ['#4361ee', '#888ea8', '#e3e4eb', '#d3d3d3'],
        series: seriesData, // Use the response data for series
        labels: labelData, // Use the response data for labels
        tooltip: {
            y: {
                formatter: function (value) {
                    // Format the tooltip value to "k" format
                    return (value / 1000).toFixed(1) + 'k';
                }
            }
        },
        responsive: [{
            breakpoint: 480,    
            options: {
                chart: {
                    width: 200
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    };

    var donut = new ApexCharts(document.querySelector("#donut-chart-underProduct"), donutChartUnderProduct);
    donut.render();
}


$(document).ready(function () {
    // Function to fetch and update the Top Performing Products chart
    function fetchTopProducts(year) {
        $.ajax({
            url: "{{ route('admin.top-products-data') }}",
            type: 'GET',
            data: { year: year },
            success: function (response) {
                updateTopProductChart(response.series, response.labels);
            },
            error: function () {
                console.log('Error fetching top products data');
            }
        });
    }

    // Function to fetch and update the Underperforming Products chart
    function fetchUnderProducts(year) {
        $.ajax({
            url: "{{ route('admin.under-products-data') }}",
            type: 'GET',
            data: { year: year },
            success: function (response) {
                updateUnderProductChart(response.series, response.labels);
            },
            error: function () {
                console.log('Error fetching under-performing products data');
            }
        });
    }

    // Get the latest year for Top Performing Products and set it as selected
    let latestYearTop = $('#year-filter-top option:first').val();
    if (latestYearTop) {
        $('#year-filter-top').val(latestYearTop); // Set the latest year as selected
        fetchTopProducts(latestYearTop); // Fetch and display the chart for the latest year
    }

    // Handle change event for the Top Performing Products dropdown
    $('#year-filter-top').on('change', function () {
        let selectedYear = $(this).val();
        if (selectedYear) {
            fetchTopProducts(selectedYear); // Fetch and update the chart for the selected year
        }
    });

    // Get the latest year for Underperforming Products and set it as selected
    let latestYearUnder = $('#year-filter-under option:first').val();
    if (latestYearUnder) {
        $('#year-filter-under').val(latestYearUnder); // Set the latest year as selected
        fetchUnderProducts(latestYearUnder); // Fetch and display the chart for the latest year
    }

    // Handle change event for the Underperforming Products dropdown
    $('#year-filter-under').on('change', function () {
        let selectedYear = $(this).val();
        if (selectedYear) {
            fetchUnderProducts(selectedYear); // Fetch and update the chart for the selected year
        }
    });
});
</script>

    

@endsection
