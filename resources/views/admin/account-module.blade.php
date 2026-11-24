@extends('layouts.app_admin')

@section('title')
    Account Module
@endsection

@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4>Account Module</h4>
                        <h6>Customers, suppliers, purchases, and double-entry general ledger — aligned with your chart of accounts.</h6>
                    </div>
                </div>
            </div>

            <div class="alert alert-success border mb-4">
                <strong>Automatic GL posting is enabled.</strong>
                Sales (customer invoices / orders), customer payments, purchase orders, store purchases, GRNs, supplier purchase invoices, purchase returns, and supplier payments create <strong>posted</strong> journal entries linked by source document. View them under Journals or General ledger.
            </div>

            <div class="row">
                <div class="col-12 mb-3">
                    <div class="card h-100 border-primary border-2 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">General ledger accounting</h5>
                            <p class="text-muted small mb-3">Chart of accounts (gl_accounts), journals, journal_lines, general ledger register, and trial balance.</p>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('admin.accounting.chart-of-accounts') }}" class="btn btn-primary">Chart of accounts</a>
                               <a href="{{ route('admin.accounting.journals.index') }}" class="btn btn-outline-primary">Journals</a>
                                <a href="{{ route('admin.accounting.journals.create') }}" class="btn btn-outline-primary">Manual journal</a>
                                <a href="{{ route('admin.accounting.general-ledger') }}" class="btn btn-outline-primary">General ledger</a>
                                <a href="{{ route('admin.accounting.trial-balance') }}" class="btn btn-outline-primary">Trial balance</a>
                                <a href="{{ route('admin.accounting.transaction-mappings') }}" class="btn btn-outline-primary">Transaction mappings</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Customers &amp; sales</h5>
                            <p class="text-muted small mb-3">customers, orders (invoices), customer_ledger_entries, customer_payments.</p>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2"><a href="{{ route('admin.customer') }}">All customers</a></li>
                                <li class="mb-2"><a href="{{ route('admin.invoice') }}">Invoice management</a></li>
                                <li class="mb-2"><a href="{{ route('admin.credit-invoices.all') }}">Credit invoices</a></li>
                                <li class="mb-2"><a href="{{ route('admin.credit-invoices.cash-sales') }}">Cash sales</a></li>
                                <li class="mb-2"><a href="{{ route('admin.credit-invoices.estimates') }}">Estimates</a></li>
                            </ul>
                            <p class="text-muted small mt-3 mb-0">
                                <strong>GL on sale:</strong> two journals — revenue (mapping accounts) and cost (product debit/credit accounts).
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Suppliers &amp; purchases</h5>
                            <p class="text-muted small mb-3">suppliers, purchase_orders, goods_receipt_notes, supplier_purchase_invoices, supplier_payments.</p>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2"><a href="{{ route('admin.suppliers.index') }}">Supplier list</a></li>
                                <li class="mb-2"><a href="{{ route('admin.purchase-orders.index') }}">Purchase orders</a></li>
                                <li class="mb-2"><a href="{{ route('admin.goods-receipt-notes.index') }}">GRN</a></li>
                                <li class="mb-2"><a href="{{ route('admin.supplier-purchase-invoices.index') }}">Purchase invoices</a></li>
                                <li class="mb-2"><a href="{{ route('admin.supplier-payments.index') }}">Supplier payments</a></li>
                                <li class="mb-2"><a href="{{ route('admin.supplier-ledger.index') }}">Supplier ledger</a></li>
                            </ul>
                            <p class="text-muted small mt-3 mb-0">
                                <strong>GL on purchase:</strong> Dr Inventory, Cr A/P. Payments: Dr A/P, Cr Cash.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Chart of accounts</h5>
                            {{--<p class="text-muted small mb-2">From <code>gl_accounts</code> table:</p>--}}
                            <div class="table-responsive mb-3" style="max-height: 220px;">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Code</th>
                                            <th>Name</th>
                                            <th>Type</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($glAccounts as $account)
                                            <tr class="{{ $account->is_active ? '' : 'text-muted' }}">
                                                <td>{{ $account->code }}</td>
                                                <td>{{ $account->name }}</td>
                                                <td>
                                                    <span class="badge bg-light text-dark text-capitalize">{{ $account->type }}</span>
                                                    @unless($account->is_active)
                                                        <span class="badge bg-secondary">inactive</span>
                                                    @endunless
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-muted">No accounts in gl_accounts. Run migrations or add accounts under Chart of accounts.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <p class="mb-2">
                                <a href="{{ route('admin.accounting.chart-of-accounts') }}" class="small">Manage chart of accounts →</a>
                            </p>
                            {{--
                            Legacy static list (replaced by gl_accounts query above):
                            <table>... 1000 Cash, 1100 A/R, etc. ...</table>
                            --}}
                            {{-- Legacy operational reports (orders/stock summaries, not GL-based): --}}
                            {{--<p class="text-muted small mb-2">Legacy reports (not from GL):</p>--}}
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2"><a href="{{ route('admin.profit-loss-statement') }}">Profit &amp; Loss (operational)</a></li>
                                <li class="mb-2"><a href="{{ route('admin.balance-sheet') }}">Balance sheet (operational)</a></li>
                                <li class="mb-2"><a href="{{ route('admin.finance-dashboard') }}">Financial oversight</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
