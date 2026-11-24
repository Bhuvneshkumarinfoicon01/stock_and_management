@extends('layouts.app_admin')
@section('title') Profit & Loss @endsection
@section('styles')
<style>
    .table-scroll-wrapper { overflow-x: auto; white-space: nowrap; }
    .profit-table th, .profit-table td { white-space: nowrap; }
    .profit-table th:first-child,
    .profit-table td:first-child {
        position: sticky;
        left: 0;
        z-index: 2;
        background: #fff;
        min-width: 180px;
        font-weight: 600;
    }
    .profit-table thead th:first-child { z-index: 3; background: #f8f9fa; }
    .profit-table-bg td:first-child,
    .profit-table-bg th:first-child { background: #f3f4f6; }
</style>
@endsection
@section('content')

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4>Profit &amp; Loss</h4>
                    <h6 class="text-muted">Monthly income statement from posted general ledger (revenue, COGS, expenses).</h6>
                </div>
            </div>
            <ul class="table-top-head">
                <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Excel" href="#" id="export-profit-loss">
                        {{--<img src="https://dreamspos.dreamstechnologies.com/html/template/assets/img/icons/excel.svg" alt="img"/>--}}
                        <img src="{{asset('storage/icons/excel01.png')}}" alt="img" width="20" height="20" />
                    </a>
                </li>
                <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Print" href="#" id="print-profit-loss">
                        <i data-feather="printer" class="feather-rotate-ccw"></i>
                    </a>
                </li>
                <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Refresh" id="refresh-page">
                        <i data-feather="rotate-ccw" class="feather-rotate-ccw"></i>
                    </a>
                </li>
                <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Collapse" id="collapse-header">
                        <i data-feather="chevron-up" class="feather-chevron-up"></i>
                    </a>
                </li>
            </ul>
        </div>

        <div class="card table-list-card border-0 mb-0">
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
                                <option value="">All Stores</option>
                                @foreach ($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="date-btn">
                        <a href="javascript:void(0);" class="btn btn-secondary d-flex align-items-center" id="display-profit-loss">
                            <i data-feather="database" class="feather-14 info-img me-2"></i>Display Data
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div id="pl-alert" class="alert alert-info d-none mb-3"></div>

        <div class="table-scroll-wrapper">
            <table class="table profit-table table-bordered">
                <thead class="profit-table-bg">
                    <tr id="pl-header-row">
                        <th>Category</th>
                    </tr>
                </thead>
                <tbody id="pl-tbody">
                    <tr><td colspan="2" class="text-muted">Select dates and click Display Data.</td></tr>
                </tbody>
            </table>
        </div>

        {{--
        Legacy operational rows (not formal P&L):
        Sales (orders.total_price), Adjustments, Incoming/Outgoing transfers, Stocks value,
        Net profit = sales - adjustments only.
        --}}
    </div>
</div>

@endsection
@section('scripts')
<script>
(function () {
    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    function parseDMY(dateStr) {
        const parts = dateStr.split('-');
        if (parts.length !== 3) return null;
        return new Date(parts[2] + '-' + parts[1] + '-' + parts[0]);
    }

    function generateMonthRange(start_date, end_date) {
        const result = [];
        let start = parseDMY(start_date);
        let end = parseDMY(end_date);
        if (!start || !end) return result;
        start.setDate(1);
        while (start <= end) {
            const label = monthNames[start.getMonth()] + ' ' + start.getFullYear();
            result.push({ key: label, label: label });
            start.setMonth(start.getMonth() + 1);
        }
        return result;
    }

    function fmtAmount(amount) {
        const n = parseFloat(amount) || 0;
        const prefix = n < 0 ? '-$' : '$';
        return prefix + Math.abs(n).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function seriesToMap(series) {
        const map = {};
        (series || []).forEach(function (e) { map[e.month] = parseFloat(e.amount) || 0; });
        return map;
    }

    function buildRow(label, months, amountMap, extraClass) {
        let html = '<tr class="' + (extraClass || '') + '"><td>' + label + '</td>';
        months.forEach(function (m) {
            const val = amountMap[m.key] ?? 0;
            const cls = val < 0 ? ' text-danger' : '';
            html += '<td class="text-end' + cls + '" data-month="' + m.key + '">' + fmtAmount(val) + '</td>';
        });
        html += '</tr>';
        return html;
    }

    $('#display-profit-loss').on('click', function () {
        const fromDate = $('#from_date').val();
        const toDate = $('#to_date').val();
        const storeId = $('#store_id').val();

        if (!fromDate || !toDate) {
            Swal.fire({ icon: 'warning', title: 'Missing Dates', text: 'Please select both dates.', confirmButtonText: 'OK' });
            return;
        }

        const months = generateMonthRange(fromDate, toDate);
        if (!months.length) {
            Swal.fire({ icon: 'warning', title: 'Invalid range', text: 'Could not build month range from dates.' });
            return;
        }

        let headerHtml = '<th>Category</th>';
        months.forEach(function (m) { headerHtml += '<th class="text-end">' + m.label + '</th>'; });
        $('#pl-header-row').html(headerHtml);

        $('#pl-tbody').html('<tr><td colspan="' + (months.length + 1) + '">Loading…</td></tr>');

        $.ajax({
            url: "{{ route('admin.profit-loss.sales') }}",
            method: 'GET',
            data: { store_id: storeId, start_date: fromDate, end_date: toDate },
            success: function (res) {
                const $alert = $('#pl-alert');
                $alert.removeClass('d-none alert-warning alert-success alert-info');
                if (!res.has_gl_data) {
                    $alert.addClass('alert-warning').text('No posted GL journals in this period. Sales and purchases with automatic posting will appear here.');
                } else {
                    $alert.addClass('alert-success').text('Profit & loss from general ledger (posted journals).');
                }

                const rev = seriesToMap(res.revenue);
                const cogs = seriesToMap(res.cogs);
                const gross = seriesToMap(res.gross_profit);
                const opex = seriesToMap(res.operating_expenses);
                const net = seriesToMap(res.net_income);

                let body = '';
                body += buildRow('Sales Revenue', months, rev);
                body += buildRow('Cost of Goods Sold', months, cogs);
                body += buildRow('Gross Profit', months, gross, 'profit-table-bg');
                body += buildRow('Operating Expenses', months, opex);
                body += buildRow('Net Income', months, net, 'profit-table-bg');

                $('#pl-tbody').html(body);

                /* Legacy AJAX fill — operational, not GL:
                res.sales.forEach(...);
                res.adjustments.forEach(...);
                net_profit = sales - adjustments;
                */
            },
            error: function (xhr) {
                $('#pl-tbody').html('<tr><td colspan="' + (months.length + 1) + '" class="text-danger">Failed to load profit & loss data.</td></tr>');
                Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Request failed.' });
            }
        });
    });

    $('#export-profit-loss').on('click', function (e) {
        e.preventDefault();
        const fromDate = $('#from_date').val();
        const toDate = $('#to_date').val();
        const storeId = $('#store_id').val();
        if (!fromDate || !toDate) {
            Swal.fire({ icon: 'warning', title: 'Missing Dates', text: 'Please select both dates.', confirmButtonText: 'OK' });
            return;
        }
        const url = "{{ route('admin.export.profit-loss') }}?store_id=" + encodeURIComponent(storeId) + "&start_date=" + encodeURIComponent(fromDate) + "&end_date=" + encodeURIComponent(toDate);
        window.location.href = url;
    });

    $('#print-profit-loss').on('click', function (e) {
        e.preventDefault();

        const fromDate = $('#from_date').val() || '-';
        const toDate = $('#to_date').val() || '-';
        const storeText = $('#store_id option:selected').text() || 'All Stores';
        const tableHtml = document.querySelector('.table-scroll-wrapper')?.innerHTML || '';
        const alertText = $('#pl-alert').hasClass('d-none') ? '' : ($('#pl-alert').text() || '');

        if (!tableHtml.trim()) {
            Swal.fire({ icon: 'warning', title: 'Nothing to print', text: 'Please load a report first.' });
            return;
        }

        const printWindow = window.open('', '_blank');
        if (!printWindow) {
            Swal.fire({ icon: 'warning', title: 'Popup blocked', text: 'Allow popups to print the report.' });
            return;
        }

        printWindow.document.write(`
            <html>
            <head>
                <title>Profit & Loss Report</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; color: #111827; }
                    h2 { margin: 0 0 4px; }
                    .meta { margin: 0 0 14px; color: #374151; font-size: 13px; }
                    .note { margin: 0 0 10px; color: #065f46; font-size: 12px; }
                    table { width: 100%; border-collapse: collapse; font-size: 12px; }
                    th, td { border: 1px solid #d1d5db; padding: 8px; }
                    th { background: #f3f4f6; text-align: right; }
                    th:first-child, td:first-child { text-align: left; font-weight: 600; }
                    .text-end { text-align: right; }
                    .text-danger { color: #b91c1c; }
                    @media print { body { margin: 12px; } }
                </style>
            </head>
            <body>
                <h2>Profit & Loss Report</h2>
                <p class="meta">From: ${fromDate} | To: ${toDate} | Store: ${storeText}</p>
                ${alertText ? `<p class="note">${alertText}</p>` : ''}
                ${tableHtml}
            </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.focus();
        setTimeout(function () {
            printWindow.print();
            printWindow.close();
        }, 200);
    });
})();
</script>
@endsection
