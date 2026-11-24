@extends('layouts.app_admin')

@section('title')
Finance
@endsection

@section('styles')
<style>
    .table-scroll-wrapper {
        overflow-x: auto;
        white-space: nowrap;
    }

    .table-scroll-wrapper table {
        width: auto;
        min-width: 100%;
    }

    .profit-table th,
    .profit-table td {
        white-space: nowrap;
    }
</style>
@endsection

@section('content')

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4>Profit & Loss</h4>
                    <h6>Manage your Profit & Loss</h6>
                </div>
            </div>
            <ul class="table-top-head">
                <!-- <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Pdf"><img
                            src="https://dreamspos.dreamstechnologies.com/html/template/assets/img/icons/pdf.svg"
                            alt="img"></a>
                </li>
                <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Excel"><img
                            src="https://dreamspos.dreamstechnologies.com/html/template/assets/img/icons/excel.svg"
                            alt="img"></a>
                </li>
                <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Print"><i data-feather="printer"
                            class="feather-rotate-ccw"></i></a>
                </li> -->
                <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Refresh" id="refresh-page"><i data-feather="rotate-ccw"
                            class="feather-rotate-ccw"></i></a>
                </li>
                <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Collapse" id="collapse-header"><i
                            data-feather="chevron-up" class="feather-chevron-up"></i></a>
                </li>
            </ul>
        </div>

        <div class="card table-list-card border-0">
            <div class="card-body mb-3">
                <div class="table-top mb-0 profit-table-top">
                    <div class="search-path profit-head">
                        <label class="form-label mb-0">From Date</label>
                        <div class="input-blocks mb-0">
                            <i data-feather="calendar" class="info-img"></i>
                            <div class="input-groupicon">
                                <input type="text" id="from_date" class="datetimepicker" placeholder="From Date" />
                            </div>
                        </div>
                    </div>
                    <div class="position-relative daterange-wraper input-blocks mb-0">
                        <label class="form-label mb-0">To Date</label>
                        <div class="input-blocks mb-0">
                            <i data-feather="calendar" class="info-img"></i>
                            <div class="input-groupicon">
                                <input type="text" id="to_date" class="datetimepicker" placeholder="To Date" />
                            </div>
                        </div>
                    </div>
                    <div class="position-relative daterange-wraper input-blocks mb-0">
                        <label class="form-label mb-0">Select Store</label>
                        <div class="input-blocks mb-0 me-2">
                            <select id="store_id" class="select">
                                <option value="">Choose Store</option>
                                @foreach ($stores as $store)
                                <option value="{{ $store->id }}">{{ $store->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="date-btn">
                        <a href="javascript:void(0);" class="btn btn-secondary d-flex align-items-center"
                            id="display-profit-loss">
                            <i data-feather="database" class="feather-14 info-img me-2"></i>Display Data</a>
                    </div>
                </div>
            </div>
        </div>
        <div id="pnl-gl-notice" class="alert alert-warning d-none mb-2" role="alert">
            No posted journal entries were found in the system. Profit &amp; Loss amounts from the general ledger will stay at zero until journals are posted.
        </div>
        <div class="table-responsive">
            <table class="table profit-table">
                <thead class="profit-table-bg">
                    <tr id="dynamic-header">
                        <th class="no-sort">Category</th>
                        <!-- Dynamic month/year headers will be inserted here -->
                    </tr>
                </thead>
                <tbody>
                    {{-- GL P&L rows (same row ids as before; labels updated in JS). --}}
                    <tr class="profit-table-bg" id="dynamic-sales-row">
                        <!-- Sales Revenue — filled via JS -->
                    </tr>
                    <tr id="dynamic-adjustments-row">
                        <!-- Cost of Goods Sold — filled via JS -->
                    </tr>
                    <tr id="dynamic-incomingTransfer-row">
                        <!-- Gross Profit — filled via JS -->
                    </tr>
                    <tr id="dynamic-outgoingTransfer-row">
                        <!-- Operating Expenses — filled via JS -->
                    </tr>
                    {{-- Legacy operational row "Stocks Value" — not returned by GL P&L API; keep markup commented per request.
                    <tr id="dynamic-stocks-row"></tr>
                    --}}
                    <tr class="profit-table-bg" id="dynamic-netProfit-row">
                        <!-- Net Income — filled via JS -->
                    </tr>

                </tbody>
            </table>
        </div>



        <div class="row mt-5">
            <div class="col-xl-12 col-sm-12 col-12 d-flex">
                <div class="card flex-fill default-cover">
                    <div class="card-header d-flex justify-content-between align-items-center mt-10">
                        <h5 class="card-title mb-0 mt-10">Sales Analysis</h5>
                        <div class="finance-charts d-flex gap-3">
                            <select id="filter-store" class="select">
                                <option value="">Choose Store</option>
                                @foreach($stores as $store)
                                <option value="{{ $store->id }}">{{ $store->name }}</option>
                                @endforeach
                            </select>

                            <select id="filter-category" class="select">
                                <option value="">Choose Category</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @php $currentYear = (int) date('Y'); @endphp
                            <select id="filter-year" class="select" title="Calendar year for order totals">
                                @for ($y = $currentYear + 1; $y >= $currentYear - 5; $y--)
                                    <option value="{{ $y }}" @selected($y === $currentYear)>{{ $y }}</option>
                                @endfor
                            </select>

                            <button id="search-btn" class="btn btn-primary">Search</button>
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
<script>
    const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

    function generateMonthRange(start_date, end_date) {
        const result = [];
        function parseDMY(dateStr) {
            const [day, month, year] = dateStr.split("-");
            return new Date(`${year}-${month}-${day}`);
        }

        let start = parseDMY(start_date);
        let end = parseDMY(end_date);
        start.setDate(1);

        while (start <= end) {
            const month = monthNames[start.getMonth()];
            const year = start.getFullYear();
            const label = `${month} ${year}`;
            result.push({ key: label, label: label });

            start.setMonth(start.getMonth() + 1);
        }

        return result;
    }

    /** Format currency for display (non-signed). */
    function formatMoneyAmount(val) {
        const n = parseFloat(val);
        if (isNaN(n)) {
            return '-';
        }
        return '$' + n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    /** Format currency with sign styling hook (negatives show as - $). */
    function formatSignedMoney(val) {
        const n = parseFloat(val);
        if (isNaN(n)) {
            return '-';
        }
        const formatted = Math.abs(n).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        return (n < 0 ? '- $' : '$') + formatted;
    }

    function fillGlSeries(rowSelector, series, signed) {
        if (!Array.isArray(series)) {
            return;
        }
        series.forEach((entry) => {
            const cell = $(rowSelector + ' td[data-month="' + entry.month + '"]');
            const amt = parseFloat(entry.amount);
            if (signed) {
                cell.text(formatSignedMoney(amt));
                cell.toggleClass('text-danger', amt < 0);
            } else {
                cell.text(formatMoneyAmount(amt));
                cell.removeClass('text-danger');
            }
        });
    }

    $("#display-profit-loss").on("click", function () {
        const fromDate = $("#from_date").val();
        const toDate = $("#to_date").val();
        const storeId = $("#store_id").val();

        if (!fromDate || !toDate) {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Dates',
                text: 'Please select both the Dates.',
                confirmButtonText: 'OK'
            });
            return;
        }

        const months = generateMonthRange(fromDate, toDate);
        const colspan = months.length + 1;

        // Build table headers (must align with server month labels "M Y", e.g. May 2026)
        let headerHtml = '<th class="no-sort">Line item</th>';
        months.forEach((m) => {
            headerHtml += `<th>${m.label}</th>`;
        });
        $(".profit-table thead tr").html(headerHtml);

        // GL-based P&L row labels (reuses same row ids as the legacy UI)
        let salesHtml = '<td class="fw-semibold">Sales Revenue</td>';
        let cogsHtml = '<td class="fw-semibold">Cost of Goods Sold</td>';
        let grossHtml = '<td class="fw-semibold">Gross Profit</td>';
        let opexHtml = '<td class="fw-semibold">Operating Expenses</td>';
        let netHtml = '<td class="fw-semibold">Net Income</td>';
        months.forEach((m) => {
            const cell = `<td data-month="${m.key}">-</td>`;
            salesHtml += cell;
            cogsHtml += cell;
            grossHtml += cell;
            opexHtml += cell;
            netHtml += cell;
        });
        $("#dynamic-sales-row").html(salesHtml);
        $("#dynamic-adjustments-row").html(cogsHtml);
        $("#dynamic-incomingTransfer-row").html(grossHtml);
        $("#dynamic-outgoingTransfer-row").html(opexHtml);
        $("#dynamic-netProfit-row").html(netHtml);

        $("#pnl-gl-notice").addClass('d-none');

        $.ajax({
            url: "{{ route('admin.profit-loss.sales') }}",
            method: "GET",
            data: {
                store_id: storeId,
                start_date: fromDate,
                end_date: toDate,
            },
            success: function (res) {
                if (res.has_gl_data === false) {
                    $("#pnl-gl-notice").removeClass('d-none');
                } else {
                    $("#pnl-gl-notice").addClass('d-none');
                }

                /* If API returns month keys, ensure columns still match (optional sync).
                   Primary path: same date range as PHP monthLabelsBetween + JS generateMonthRange. */
                if (res.months && Array.isArray(res.months) && res.months.length &&
                    (res.months.length !== months.length ||
                     res.months.some((m, i) => months[i] && m !== months[i].key))) {
                    console.warn('P&L: client month headers and server res.months differ; verify date range / locale.', res.months);
                }

                fillGlSeries('#dynamic-sales-row', res.revenue, false);
                fillGlSeries('#dynamic-adjustments-row', res.cogs, false);
                fillGlSeries('#dynamic-incomingTransfer-row', res.gross_profit, true);
                fillGlSeries('#dynamic-outgoingTransfer-row', res.operating_expenses, false);
                fillGlSeries('#dynamic-netProfit-row', res.net_income, true);

                /*
                |--------------------------------------------------------------------------
                | Legacy response shape (operational P&L) — kept for reference only.
                | ProfitLossController@getMonthlySales now returns GL JSON:
                | revenue, cogs, gross_profit, operating_expenses, net_income, months, has_gl_data.
                |--------------------------------------------------------------------------
                |
                | if (res.sales && Array.isArray(res.sales)) {
                |     res.sales.forEach((entry) => {
                |         $('#dynamic-sales-row td[data-month="' + entry.month + '"]').text(
                |             '$' + parseFloat(entry.total_sales).toLocaleString()
                |         );
                |     });
                | }
                | ... adjustments, incoming, outgoing, stocks, net_profit ...
                */
            },

            error: function () {
                $("#dynamic-sales-row").html('<td colspan="' + colspan + '">Failed to load profit &amp; loss data</td>');
                $("#dynamic-adjustments-row").html('');
                $("#dynamic-incomingTransfer-row").html('');
                $("#dynamic-outgoingTransfer-row").html('');
                $("#dynamic-netProfit-row").html('');
            }
        });
    });
</script>
<script>
    function loadSalesAnalysisChart() {
        const storeId = document.getElementById("filter-store").value;
        const categoryId = document.getElementById("filter-category").value;
        const year = document.getElementById("filter-year").value;

        fetch(`{{ route('admin.sales-analysis-superadmin') }}?store_id=${encodeURIComponent(storeId)}&category_id=${encodeURIComponent(categoryId)}&year=${encodeURIComponent(year)}`, {
            method: "GET",
            headers: {
                "X-Requested-With": "XMLHttpRequest"
            }
        })
            .then((response) => response.json())
            .then((data) => {
                const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                let monthlyTotals = {};
                months.forEach((m) => {
                    monthlyTotals[m] = 0;
                });

                (Array.isArray(data) ? data : []).forEach((row) => {
                    if (row.month != null && Object.prototype.hasOwnProperty.call(monthlyTotals, row.month)) {
                        monthlyTotals[row.month] = parseFloat(row.total) || 0;
                    }
                });

                const chartData = months.map((m) => monthlyTotals[m]);
                const maxVal = Math.max(...chartData, 0);
                const yMax = maxVal > 0 ? maxVal * 1.2 : 1;

                const options = {
                    series: [{
                        name: "Sales Analysis (" + year + ")",
                        data: chartData
                    }],
                    chart: {
                        height: 273,
                        type: "area",
                        zoom: { enabled: false }
                    },
                    colors: ["#FF9F43"],
                    dataLabels: { enabled: false },
                    stroke: { curve: "straight" },
                    xaxis: {
                        categories: months,
                    },
                    yaxis: {
                        min: 0,
                        max: yMax,
                        tickAmount: 5,
                        labels: {
                            formatter: (val) => {
                                if (val >= 1000) {
                                    return (val / 1000).toFixed(2) + "K";
                                }
                                return val.toFixed(0);
                            }
                        }
                    },
                    legend: {
                        position: "top",
                        horizontalAlign: "left",
                    }
                };

                document.querySelector("#sales-analysis-chart").innerHTML = "";
                new ApexCharts(document.querySelector("#sales-analysis-chart"), options).render();
            })
            .catch(() => {
                document.querySelector("#sales-analysis-chart").innerHTML = '<p class="text-muted mb-0">Could not load sales analysis.</p>';
            });
    }

    document.getElementById("search-btn").addEventListener("click", loadSalesAnalysisChart);

    $(function () {
        if (typeof ApexCharts !== "undefined") {
            loadSalesAnalysisChart();
        }
    });
</script>

@endsection