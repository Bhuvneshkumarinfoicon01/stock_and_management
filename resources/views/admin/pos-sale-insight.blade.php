@extends('layouts.app_admin') @section('title') Dashbaord @endsection
@section('styles') @endsection @section('content')

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4>Sales Trend Report</h4>
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
            <div class="col-sm-12 col-md-12 col-xl-12 d-flex">
                <div class="card flex-fill">
                    <div
                        class="card-header d-flex justify-content-between align-items-center"
                    >
                        <h5 class="card-title mb-0">
                            Analyze payment method trends
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="payment-trend-chart" class="chart-set"></div>
                    </div>
                </div>
            </div>

            <div class="col-sm-12 col-md-12 col-xl-12 d-flex">
                <div class="card flex-fill default-cover">
                    <div
                        class="card-header d-flex justify-content-between align-items-center"
                    >
                        <h5 class="card-title mb-0">
                            Point Of Sale - Sales Data
                        </h5>
                        <div class="graph-sets">
                            <div class="dropdown dropdown-wraper">
                                <button
                                    class="btn btn-white btn-sm dropdown-toggle d-flex align-items-center"
                                    type="button"
                                    id="dropdown-sales"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="24"
                                        height="24"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="feather feather-calendar feather-14"
                                    >
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                    <span id="selected-year" class="ms-1">{{ now()->year }}</span>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdown-sales" id="year-dropdown-list">
                                    <!-- JS will populate this -->
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="pos-sales-analysis-chart" class="chart-set"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {

    if ($("#payment-trend-chart").length > 0) {
        $.ajax({
            url: "/admin/payment-trend-data", // update with your route
            method: "GET",
            success: function (response) {
                console.log("DEBUG: Raw response from API:", response);
                var chart = new ApexCharts(
                    document.querySelector("#payment-trend-chart"),
                    {
                        chart: {
                            height: 350,
                            type: "bar",
                            toolbar: { show: false },
                        },
                        plotOptions: {
                            bar: {
                                horizontal: false,
                                columnWidth: "55%",
                                endingShape: "rounded",
                            },
                        },
                        dataLabels: { enabled: false },
                        stroke: {
                            show: true,
                            width: 2,
                            colors: ["transparent"],
                        },
                        series: response.series,
                        xaxis: {
                            categories: response.months,
                        },
                        yaxis: {
                            title: {
                                text: "$ (thousands)",
                            },
                        },
                        fill: { opacity: 1 },
                        tooltip: {
                            y: {
                                formatter: function (val) {
                                    return "$ " + val + " thousands";
                                },
                            },
                        },
                    }
                );

                chart.render();
            },
        });
    }

    let chart = null;
        let selectedYear = new Date().getFullYear(); // default

        function loadSalesData(year = selectedYear) {
            fetch(`/admin/dashboard/pos-sales-data?year=${year}`)
                .then(res => res.json())
                .then(data => {
                    // Populate year dropdown
                    const dropdown = document.getElementById("year-dropdown-list");
                    dropdown.innerHTML = "";
                    data.years.forEach(y => {
                        const li = document.createElement("li");
                        li.innerHTML = `<a href="#" class="dropdown-item year-option" data-year="${y}">${y}</a>`;
                        dropdown.appendChild(li);
                    });

                    // Update selected year label
                    document.getElementById("selected-year").innerText = year;

                    // Chart config
                    const options = {
                        series: [{ name: "Sales Analysis", data: data.sales }],
                        chart: { height: 273, type: "area", zoom: { enabled: false } },
                        colors: ["#FF9F43"],
                        dataLabels: { enabled: false },
                        stroke: { curve: "straight" },
                        xaxis: { categories: data.months },
                        yaxis: { labels: { formatter: val => val + "K" } },
                        legend: { position: "top", horizontalAlign: "left" },
                    };

                    if (chart) {
                        chart.updateOptions(options);
                    } else {
                        chart = new ApexCharts(document.querySelector("#pos-sales-analysis-chart"), options);
                        chart.render();
                    }
                })
                .catch(error => {
                    console.error("Error loading POS Sales Data:", error);
                });
        }

// Initial chart load
        if ($("#pos-sales-analysis-chart").length > 0) {
            loadSalesData();

            // On year click
            $(document).on("click", ".year-option", function (e) {
                e.preventDefault();
                selectedYear = $(this).data("year");
                loadSalesData(selectedYear);
            });
        }


})
</script>

@endsection
