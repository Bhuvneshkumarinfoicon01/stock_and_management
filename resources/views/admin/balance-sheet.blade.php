@extends('layouts.app_admin')
@section('title') Balance Sheet @endsection
@section('styles') @endsection
@section('content')

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4>Balance Sheet</h4>
                    <h6 class="text-muted">Assets, liabilities, and equity from posted general ledger journals (as of the “To” date).</h6>
                </div>
            </div>
            <ul class="table-top-head">
                <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Excel" href="#" id="export-balance-sheet">
                        {{--<img src="https://dreamspos.dreamstechnologies.com/html/template/assets/img/icons/excel.svg" alt="img"/>--}}
                        <img src="{{asset('storage/icons/excel01.png')}}" alt="img" width="20" height="20" />
                    </a>
                </li>
                <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Print" href="#" id="print-balance-sheet">
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
                        <label class="form-label mb-0">To Date <span class="text-muted">(as of)</span></label>
                        <div class="input-blocks mb-0">
                            <i data-feather="calendar" class="info-img"></i>
                            <div class="input-groupicon">
                                <input type="text" id="to_date" class="datetimepicker" placeholder="As of Date" />
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
                        <a href="javascript:void(0);" class="btn btn-secondary d-flex align-items-center" id="display-balance-sheet">
                            <i data-feather="database" class="feather-14 info-img me-2"></i>Display Data
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div id="bs-alert" class="alert alert-info d-none mb-3"></div>

        <div class="row">
            <div class="col-lg-6 mb-3">
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">Assets</h5></div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table balance-sheet mb-0">
                                <thead>
                                    <tr><th>Account</th><th class="text-end">Amount</th></tr>
                                </thead>
                                <tbody id="bs-assets-body">
                                    <tr><td colspan="2" class="text-muted">Select dates and click Display Data.</td></tr>
                                </tbody>
                                <tfoot>
                                    <tr class="table-light">
                                        <td><strong>Total Assets</strong></td>
                                        <td class="text-end"><strong id="bs-total-assets">—</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-3">
                <div class="card mb-3">
                    <div class="card-header"><h5 class="mb-0">Liabilities</h5></div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table balance-sheet mb-0">
                                <thead>
                                    <tr><th>Account</th><th class="text-end">Amount</th></tr>
                                </thead>
                                <tbody id="bs-liabilities-body"></tbody>
                                <tfoot>
                                    <tr class="table-light">
                                        <td><strong>Total Liabilities</strong></td>
                                        <td class="text-end"><strong id="bs-total-liabilities">—</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><h5 class="mb-0">Equity</h5></div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table balance-sheet mb-0">
                                <thead>
                                    <tr><th>Account</th><th class="text-end">Amount</th></tr>
                                </thead>
                                <tbody id="bs-equity-body"></tbody>
                                <tbody id="bs-net-income-row"></tbody>
                                <tfoot>
                                    <tr class="table-light">
                                        <td><strong>Total Equity</strong></td>
                                        <td class="text-end"><strong id="bs-total-equity">—</strong></td>
                                    </tr>
                                    <tr class="table-primary">
                                        <td><strong>Total Liabilities + Equity</strong></td>
                                        <td class="text-end"><strong id="bs-total-liab-equity">—</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{--
        Legacy layout (operational — treated cash/card sales as assets; not a formal balance sheet):
        <div class="balance-sheet">
            <table>
                <tr><td>Cash</td><td>sum of cash orders in period</td></tr>
                <tr><td>Digital</td><td>sum of card orders in period</td></tr>
                <tr><td>Inventory (Stocks)</td><td>stock value filtered by updated_at</td></tr>
                <tr><td>Total Sales</td><td>revenue — belongs on P&amp;L, not BS</td></tr>
            </table>
        </div>
        --}}
    </div>
</div>

@endsection
@section('scripts')
<script>
(function () {
    function fmt(n) {
        return '$' + Number(n || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function renderRows($tbody, rows, emptyLabel) {
        $tbody.empty();
        if (!rows || !rows.length) {
            $tbody.append('<tr><td colspan="2" class="text-muted">' + emptyLabel + '</td></tr>');
            return;
        }
        rows.forEach(function (r) {
            $tbody.append(
                '<tr><td>' + r.code + ' — ' + r.name + '</td><td class="text-end">' + fmt(r.balance) + '</td></tr>'
            );
        });
    }

    $('#display-balance-sheet, #display-profit-loss').on('click', function () {
        const fromDate = $('#from_date').val();
        const toDate = $('#to_date').val();
        const storeId = $('#store_id').val();

        if (!fromDate || !toDate) {
            Swal.fire({ icon: 'warning', title: 'Missing Dates', text: 'Please select both dates.', confirmButtonText: 'OK' });
            return;
        }

        $.ajax({
            url: "{{ route('admin.balance-sheet-data') }}",
            method: 'GET',
            data: { store_id: storeId, start_date: fromDate, end_date: toDate },
            success: function (res) {
                const $alert = $('#bs-alert');
                $alert.removeClass('d-none alert-warning alert-success alert-info');
                if (!res.has_gl_data) {
                    $alert.addClass('alert-warning').text('No posted journal entries yet. Create sales or purchases so automatic GL postings appear, or post manual journals.');
                } else if (res.is_balanced) {
                    $alert.addClass('alert-success').text('Balance sheet from general ledger as of ' + res.as_of_date + '. Assets equal liabilities + equity.');
                } else {
                    $alert.addClass('alert-warning').text('GL totals as of ' + res.as_of_date + ': Assets ' + fmt(res.total_assets) + ' vs Liabilities + Equity ' + fmt(res.total_liabilities_and_equity) + '. Review journals.');
                }

                renderRows($('#bs-assets-body'), res.assets, 'No asset balances.');
                renderRows($('#bs-liabilities-body'), res.liabilities, 'No liability balances.');
                renderRows($('#bs-equity-body'), res.equity, 'No equity account balances.');

                const $ni = $('#bs-net-income-row');
                $ni.empty();
                if (Math.abs(res.net_income || 0) >= 0.005) {
                    $ni.append('<tr><td>Net income (unclosed P&amp;L)</td><td class="text-end">' + fmt(res.net_income) + '</td></tr>');
                }

                $('#bs-total-assets').text(fmt(res.total_assets));
                $('#bs-total-liabilities').text(fmt(res.total_liabilities));
                $('#bs-total-equity').text(fmt(res.total_equity));
                $('#bs-total-liab-equity').text(fmt(res.total_liabilities_and_equity));
            },
            error: function (xhr) {
                Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Could not load balance sheet.' });
            }
        });
    });

    $('#export-balance-sheet').on('click', function (e) {
        e.preventDefault();
        const fromDate = $('#from_date').val();
        const toDate = $('#to_date').val();
        const storeId = $('#store_id').val();
        if (!fromDate || !toDate) {
            Swal.fire({ icon: 'warning', title: 'Missing Dates', text: 'Please select both dates.', confirmButtonText: 'OK' });
            return;
        }
        const url = "{{ route('admin.balance-sheet.export') }}?store_id=" + encodeURIComponent(storeId) + "&start_date=" + encodeURIComponent(fromDate) + "&end_date=" + encodeURIComponent(toDate);
        window.location.href = url;
    });

    $('#print-balance-sheet').on('click', function (e) {
        e.preventDefault();

        const fromDate = $('#from_date').val() || '-';
        const toDate = $('#to_date').val() || '-';
        const storeText = $('#store_id option:selected').text() || 'All Stores';
        const reportHtml = document.querySelector('.row')?.innerHTML || '';
        const alertText = $('#bs-alert').hasClass('d-none') ? '' : ($('#bs-alert').text() || '');

        if (!reportHtml.trim()) {
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
                <title>Balance Sheet Report</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; color: #111827; }
                    h2 { margin: 0 0 4px; }
                    .meta { margin: 0 0 14px; color: #374151; font-size: 13px; }
                    .note { margin: 0 0 10px; color: #065f46; font-size: 12px; }
                    .row { display: flex; gap: 16px; }
                    .col-lg-6 { width: 50%; }
                    .card { border: 1px solid #d1d5db; margin-bottom: 12px; }
                    .card-header { padding: 8px 10px; background: #f3f4f6; border-bottom: 1px solid #d1d5db; }
                    .card-body { padding: 0; }
                    table { width: 100%; border-collapse: collapse; font-size: 12px; }
                    th, td { border: 1px solid #d1d5db; padding: 8px; }
                    th { background: #f9fafb; text-align: left; }
                    .text-end { text-align: right; }
                    .table-light td { background: #f3f4f6; font-weight: 700; }
                    .table-primary td { background: #e5eefb; font-weight: 700; }
                    @media print {
                        body { margin: 12px; }
                        .row { display: flex; }
                    }
                </style>
            </head>
            <body>
                <h2>Balance Sheet Report</h2>
                <p class="meta">From: ${fromDate} | As of: ${toDate} | Store: ${storeText}</p>
                ${alertText ? `<p class="note">${alertText}</p>` : ''}
                <div class="row">${reportHtml}</div>
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
