@php
	$sb = fn (...$patterns) => request()->routeIs(...$patterns) ? 'active' : '';
	$isDashboardSection = request()->routeIs(
		'admin.superadmin_dashboard',
		'admin.inventory-summary',
		'admin.finance-dashboard'
	);
@endphp
<!-- Sidebar -->
<div class="sidebar" id="sidebar">
	<div class="sidebar-inner slimscroll">
		<div id="sidebar-menu" class="sidebar-menu">
			<ul>
				<li class="submenu-open">
					<h6 class="submenu-hdr">Main</h6>
					<ul>
						<li class="submenu">
							<a href="javascript:void(0);" class="subdrop {{ $isDashboardSection ? 'active' : '' }}"><i
									data-feather="grid"></i><span>Dashboard</span><span class="menu-arrow"></span></a>
							<ul>
								<li><a href="{{route('admin.superadmin_dashboard')}}" class="{{ $sb('admin.superadmin_dashboard') }}">Admin Dashboard</a>
								</li>
								<li><a href="{{route('admin.inventory-summary')}}" class="{{ $sb('admin.inventory-summary') }}">Inventory Dashboard</a></li>
								<li><a href="{{route('admin.finance-dashboard')}}" class="{{ $sb('admin.finance-dashboard') }}">Financial Oversight</a></li>
							</ul>
						</li>
					</ul>
				</li>
				<li class="submenu-open">
					<h6 class="submenu-hdr">Store</h6>
					<ul>
						<li><a href="{{ route('admin.store-list-superadmin') }}" class="{{ $sb('admin.store-list-superadmin') }}"><i data-feather="box"></i><span>All
									Stores</span></a></li>
						<li><a href="{{route('admin.stores_communication')}}" class="{{ $sb('admin.stores_communication') }}"><i
									data-feather="plus-square"></i><span>Communication</span></a></li>
						<li><a href="{{route('admin.daily-operations-overview-superadmin')}}" class="{{ $sb('admin.daily-operations-overview-superadmin') }}"><i
									data-feather="plus-square"></i><span>Daily operations overview</span></a></li>
					</ul>
				</li>

				<li class="submenu-open">
					<h6 class="submenu-hdr">Inventory</h6>
					<ul>
						<li><a href="{{ route('admin.stock.list') }}" class="{{ $sb('admin.stock.list') }}"><i data-feather="box"></i><span>All Stock</span></a></li>
						<li><a href="{{ route('admin.stock.by-product') }}" class="{{ $sb('admin.stock.by-product') }}"><i data-feather="box"></i><span>Stock by Product</span></a></li>
						<li><a href="{{ route('admin.inventory-valuation') }}" class="{{ $sb('admin.inventory-valuation') }}"><i data-feather="bar-chart-2"></i><span>Inventory Valuation</span></a></li>
						<li><a href="{{ route('admin.stock.create') }}" class="{{ $sb('admin.stock.create') }}"><i data-feather="plus-square"></i><span>Create Product</span></a></li>
						<li><a href="{{ route('admin.stocktransfers.index') }}" class="{{ $sb('admin.stocktransfers.index', 'admin.stock-transfer', 'admin.stock-transfer.*') }}"><i data-feather="truck"></i><span>Stock transfers</span></a></li>
						<li><a href="{{ route('admin.stock-adjustment') }}" class="{{ $sb('admin.stock-adjustment', 'admin.create-adjustment', 'admin.update-adjustment', 'admin.delete-adjustment', 'admin.get-adjustment-data') }}"><i data-feather="clipboard"></i><span>Stock Adjustment</span></a></li>
						<li><a href="{{ route('admin.categories.index') }}" class="{{ $sb('admin.categories.*') }}"><i data-feather="box"></i><span>All Category</span></a></li>
						<li><a href="{{ route('admin.units.index') }}" class="{{ $sb('admin.units.*') }}"><i data-feather="box"></i><span>All Units</span></a></li>
						<!-- <li><a href="{{ route('admin.stock.store-markups') }}" class="{{ $sb('admin.stock.store-markups') }}"><i data-feather="box"></i><span>Store markup & selling prices</span></a></li> -->

						{{--
						<li><a href="{{ route('admin.payment-terms.index') }}" class="{{ $sb('admin.payment-terms.*') }}"><i data-feather="box"></i><span>Payment Terms</span></a></li>
					@php
						$isSupplierSection = request()->routeIs(
							'admin.suppliers.*',
							'admin.supplier-ledger.*',
							'admin.supplier-payments.*',
							'admin.supplier-due-report.*'
						);
						$isPurchasesSection = request()->routeIs(
							'admin.purchase-orders.*',
							'admin.store-purchases.*',
							'admin.purchase-returns.*',
							'admin.goods-receipt-notes.*',
							'admin.supplier-purchase-invoices.*',
							'admin.purchase-invoices-on-hold',
							'admin.purchase-invoices.resume',
							'admin.batch-purchases.*',
							'admin.batch-purchases-on-hold'
						);
					@endphp

					<li class="submenu">
						<a href="javascript:void(0);" class="subdrop {{ $isSupplierSection ? 'active' : '' }}">
							<i data-feather="truck"></i><span>Suppliers</span><span class="menu-arrow"></span>
						</a>
						<ul>
							<li><a href="{{ route('admin.suppliers.create') }}" class="{{ $sb('admin.suppliers.create') }}">Add Supplier</a></li>
							<li><a href="{{ route('admin.suppliers.index') }}" class="{{ $sb('admin.suppliers.index', 'admin.suppliers.edit') }}">Supplier List</a></li>
							<li><a href="{{ route('admin.supplier-ledger.index') }}" class="{{ $sb('admin.supplier-ledger.*') }}">Supplier Ledger</a></li>
							<li><a href="{{ route('admin.supplier-payments.index') }}" class="{{ $sb('admin.supplier-payments.index', 'admin.supplier-payments.create') }}">Supplier Payments</a></li>
							<li><a href="{{ route('admin.supplier-due-report.index') }}" class="{{ $sb('admin.supplier-due-report.*') }}">Supplier Due Report</a></li>
						</ul>
					</li>

					<li class="submenu">
						<a href="javascript:void(0);" class="subdrop {{ $isPurchasesSection ? 'active' : '' }}">
							<i data-feather="shopping-bag"></i><span>Purchases</span><span class="menu-arrow"></span>
						</a>
						<ul>
							<li><a href="{{ route('admin.purchase-orders.index') }}" class="{{ $sb('admin.purchase-orders.*') }}">Purchase Order</a></li>
							<li><a href="{{ route('admin.purchase-returns.index') }}" class="{{ $sb('admin.purchase-returns.*') }}">Purchase Return</a></li>
							<li><a href="{{ route('admin.goods-receipt-notes.index') }}" class="{{ $sb('admin.goods-receipt-notes.*') }}">GRN/Add Stock</a></li>
							<li><a href="{{ route('admin.supplier-purchase-invoices.index') }}" class="{{ $sb('admin.supplier-purchase-invoices.*') }}">Purchase Invoice</a></li>
							<li><a href="{{ route('admin.purchase-invoices-on-hold') }}" class="{{ $sb('admin.purchase-invoices-on-hold', 'admin.purchase-invoices.resume') }}">Invoices On Hold</a></li>
						</ul>
					</li>--}}
					</ul>
				</li>



				<li class="submenu-open">
					{{--<h6 class="submenu-hdr">Stock</h6>--}}
					<h6 class="submenu-hdr">Purchases</h6>
					<ul>
						{{--<li><a href="{{ route('admin.stock.list') }}" class="{{ $sb('admin.stock.list') }}"><i data-feather="box"></i><span>All
									Stock</span></a></li>
						<li><a href="{{ route('admin.stock.create') }}" class="{{ $sb('admin.stock.create') }}"><i data-feather="plus-square"></i><span>Create Product</span></a></li>
						<!-- <li><a href="javascript:void(0);" class="{{ $sb('admin.stock.create') }}"><i data-feather="plus-square"></i><span>Add Stock</span></a></li> -->
						<li><a href="{{ route('admin.stocktransfers.index') }}" class="{{ $sb('admin.stocktransfers.index', 'admin.stock-transfer', 'admin.stock-transfer.*') }}"><i data-feather="truck"></i><span>Stock
									transfers</span></a></li>
						<li><a href="{{ route('admin.stock-adjustment') }}" class="{{ $sb('admin.stock-adjustment', 'admin.create-adjustment', 'admin.update-adjustment', 'admin.delete-adjustment', 'admin.get-adjustment-data') }}"><i data-feather="clipboard"></i><span>Stock
									Adjustment</span></a></li>
						<!-- <li><a href="{{route('admin.manage-stocks-superadmin')}}"><i data-feather="package"></i><span>Manage Stock</span></a></li> -->
						<li>
							<a href="{{ route('admin.categories.index') }}" class="{{ $sb('admin.categories.*') }}"><i data-feather="box"></i><span>All
									Category</span></a>
						</li>
						<li>
							<a href="{{ route('admin.units.index') }}" class="{{ $sb('admin.units.*') }}"><i data-feather="box"></i><span>All
									Units</span></a>
						</li>--}}

						<li>
							<a href="{{ route('admin.payment-terms.index') }}" class="{{ $sb('admin.payment-terms.*') }}"><i data-feather="box"></i><span>Payment Terms</span></a>
						</li>


					{{-- Old placeholder Supplier / Purchases menus (kept for reference):
					<li class="submenu">
						<a href="javascript:void(0);" class="subdrop ">
							<i data-feather="box"></i><span>Supplier</span><span class="menu-arrow"></span>
						</a>
						<ul>
							<li><a href="" class=""></a></li>
						</ul>
					</li>

					<li class="submenu">
						<a href="javascript:void(0);" class="subdrop ">
							<i data-feather="box"></i><span>Purchases</span><span class="menu-arrow"></span>
						</a>
						<ul>
							<li><a href="" class=""></a></li>
						</ul>
					</li>
					--}}

					@php
						$isSupplierSection = request()->routeIs(
							'admin.suppliers.*',
							'admin.supplier-ledger.*',
							'admin.supplier-payments.*',
							'admin.supplier-due-report.*'
						);
						$isPurchasesSection = request()->routeIs(
							'admin.purchase-orders.*',
							'admin.store-purchases.*',
							'admin.purchase-returns.*',
							'admin.goods-receipt-notes.*',
							'admin.supplier-purchase-invoices.*',
							'admin.purchase-invoices-on-hold',
							'admin.purchase-invoices.resume',
							'admin.batch-purchases.*',
							'admin.batch-purchases-on-hold'
						);
					@endphp

					<li class="submenu">
						<a href="javascript:void(0);" class="subdrop {{ $isSupplierSection ? 'active' : '' }}">
							<i data-feather="truck"></i><span>Suppliers</span><span class="menu-arrow"></span>
						</a>
						<ul>
							<li><a href="{{ route('admin.suppliers.create') }}" class="{{ $sb('admin.suppliers.create') }}">Add Supplier</a></li>
							<li><a href="{{ route('admin.suppliers.index') }}" class="{{ $sb('admin.suppliers.index', 'admin.suppliers.edit') }}">Supplier List</a></li>
							<li><a href="{{ route('admin.supplier-ledger.index') }}" class="{{ $sb('admin.supplier-ledger.*') }}">Supplier Ledger</a></li>
							<li><a href="{{ route('admin.supplier-payments.index') }}" class="{{ $sb('admin.supplier-payments.index', 'admin.supplier-payments.create') }}">Supplier Payments</a></li>
							<li><a href="{{ route('admin.supplier-due-report.index') }}" class="{{ $sb('admin.supplier-due-report.*') }}">Supplier Due Report</a></li>
						</ul>
					</li>

					{{--<li class="submenu">
						<a href="javascript:void(0);" class="subdrop {{ $isPurchasesSection ? 'active' : '' }}">
							<i data-feather="shopping-bag"></i><span>Purchases</span><span class="menu-arrow"></span>
						</a>
						<ul>--}}
							<li><a href="{{ route('admin.purchase-orders.index') }}" class="{{ $sb('admin.purchase-orders.*') }}"><i data-feather="box"></i>Purchase Order</a></li>
							{{--<li><a href="{{ route('admin.store-purchases.index') }}" class="{{ $sb('admin.store-purchases.*') }}"><i data-feather="box"></i>Add Purchase</a></li>--}}
							<li><a href="{{ route('admin.purchase-returns.index') }}" class="{{ $sb('admin.purchase-returns.*') }}"><i data-feather="box"></i>Purchase Return</a></li>
							<li><a href="{{ route('admin.goods-receipt-notes.index') }}" class="{{ $sb('admin.goods-receipt-notes.*') }}"><i data-feather="box"></i>GRN/Add Stock</a></li>
							<li><a href="{{ route('admin.supplier-purchase-invoices.index') }}" class="{{ $sb('admin.supplier-purchase-invoices.*') }}"><i data-feather="box"></i>Purchase Invoice</a></li>
							<li><a href="{{ route('admin.purchase-invoices-on-hold') }}" class="{{ $sb('admin.purchase-invoices-on-hold', 'admin.purchase-invoices.resume') }}"><i data-feather="box"></i>Invoices On Hold</a></li>
							<li><a href="{{ route('admin.batch-purchases.index') }}" class="{{ $sb('admin.batch-purchases.*', 'admin.batch-purchases-on-hold') }}"><i data-feather="box"></i>Batch Purchasing</a></li>
						{{--</ul>
					</li>--}}


					</ul>
				</li>

				{{--<li class="submenu-open">
					<h6 class="submenu-hdr">Receivables</h6>
					<ul>
						<li><a href="{{ route('admin.customer') }}" class="{{ $sb('admin.customer', 'admin.customer-orders', 'admin.customer-orders.*') }}"><i data-feather="box"></i><span>All Customers</span></a></li>
						<li><a href="{{ route('admin.credit-invoices.all') }}" class="{{ $sb('admin.credit-invoices.all') }}"><i data-feather="box"></i><span>All Credit Invoices</span></a></li>
						<li><a href="{{ route('admin.credit-invoices.paid') }}" class="{{ $sb('admin.credit-invoices.paid') }}"><i data-feather="plus-square"></i><span>All Paid Credit Invoices</span></a></li>
						<li><a href="{{ route('admin.credit-invoices.unpaid') }}" class="{{ $sb('admin.credit-invoices.unpaid') }}"><i data-feather="plus-square"></i><span>All Unpaid Credit Invoices</span></a></li>
						<li><a href="{{ route('admin.credit-invoices.partial') }}" class="{{ $sb('admin.credit-invoices.partial') }}"><i data-feather="plus-square"></i><span>All Partial Credit Invoices</span></a></li>
						<li><a href="{{ route('admin.credit-invoices.overdue') }}" class="{{ $sb('admin.credit-invoices.overdue') }}"><i data-feather="plus-square"></i><span>All Overdue Credit Invoices</span></a></li>
					</ul>
				</li>--}}

				<li class="submenu-open">
					{{--<h6 class="submenu-hdr">Receivables</h6>--}}
					<h6 class="submenu-hdr">Sales</h6>
					@php
						$isCustomerSection = request()->routeIs(
							'admin.customer',
							'admin.customer.*',
							'admin.customer-orders*',
							'admin.customer-order-payment.*',
							'admin.customer-ledger.*',
							'admin.get-orderItems'
						);
						$isCreditInvoiceSection = request()->routeIs(
							'admin.credit-invoices.all',
							'admin.credit-invoices.paid',
							'admin.credit-invoices.unpaid',
							'admin.credit-invoices.partial',
							'admin.credit-invoices.overdue',
							'admin.credit-invoices.data',
							'admin.credit-invoices.convert-estimate'
						);
						$isEstimateInvoiceSection = request()->routeIs('admin.credit-invoices.estimates');
						$isCashSalesInvoiceSection = request()->routeIs('admin.credit-invoices.cash-sales');
						$isAllSalesInvoicesSection = request()->routeIs(
							'admin.sales-invoices.index',
							'admin.sales-invoices.data',
							'admin.sales-invoices.show'
						);
						$isInvoiceManagementSection = request()->routeIs(
							'admin.invoice*',
							'admin.view-invoice',
							'admin.invoice.filter',
							'admin.invoice-order',
							'admin.get-products-invoice',
							'admin.invoice-item-price.update',
							'admin.invoices.export',
							'admin.generate-invoice'
						);
						$isGenerateInvoiceSection = request()->routeIs('admin.generate-invoice');
						$isSalesOnHoldSection = request()->routeIs('admin.sales-invoices-on-hold', 'admin.sales-invoices.resume');
					@endphp
					<ul>
						<li><a href="{{ route('admin.customer') }}" class="{{ $isCustomerSection ? 'active' : '' }}"><i data-feather="box"></i><span>All Customers</span></a></li>
						{{--
						<li class="submenu">
							<a href="javascript:void(0);" class="subdrop {{ $isCreditInvoiceSection ? 'active' : '' }}">
								<i data-feather="box"></i><span>Credit Invoices</span><span class="menu-arrow"></span>
							</a>
							<ul>
								<li><a href="{{route('admin.credit-invoices.all')}}" class="{{ $sb('admin.credit-invoices.all') }}">All Credit Invoices</a></li>
								<li><a href="{{route('admin.credit-invoices.paid')}}" class="{{ $sb('admin.credit-invoices.paid') }}">All Paid Credit Invoices</a></li>
								<li><a href="{{route('admin.credit-invoices.unpaid')}}" class="{{ $sb('admin.credit-invoices.unpaid') }}">All Unpaid Credit Invoices</a></li>
								<li><a href="{{route('admin.credit-invoices.partial')}}" class="{{ $sb('admin.credit-invoices.partial') }}">All Partial Credit Invoices</a></li>
								<li><a href="{{route('admin.credit-invoices.overdue')}}" class="{{ $sb('admin.credit-invoices.overdue') }}">All Over Due Credit Invoices</a></li>
							</ul>
						</li>
						<li><a href="{{route('admin.credit-invoices.cash-sales')}}" class="{{ $isCashSalesInvoiceSection ? 'active' : '' }}"><i data-feather="plus-square"></i><span>Cash Sales Invoices</span></a></li>
						--}}
						<li><a href="{{ route('admin.sales-invoices.index') }}" class="{{ $isAllSalesInvoicesSection ? 'active' : '' }}"><i data-feather="plus-square"></i><span>All Sales Invoices</span></a></li>
						<li><a href="{{route('admin.credit-invoices.estimates')}}" class="{{ $isEstimateInvoiceSection ? 'active' : '' }}"><i data-feather="plus-square"></i><span>All Estimates</span></a></li>
						<li><a href="{{ route('admin.generate-invoice') }}" class="{{ $isGenerateInvoiceSection ? 'active' : '' }}"><i data-feather="trending-down"></i><span>Generate Invoice</span></a></li>
						<li><a href="{{ route('admin.sales-invoices-on-hold') }}" class="{{ $isSalesOnHoldSection ? 'active' : '' }}"><i data-feather="pause-circle"></i><span>Invoices On Hold</span></a></li>
						<li><a href="{{ route('admin.invoice') }}" class="{{ $isInvoiceManagementSection ? 'active' : '' }}"><i data-feather="trending-down"></i><span>Invoice	Management</span></a></li> {{-- this is for invoice management --}}
						{{--<li><a href="{{ route('admin.invoice') }}" class="{{ $isInvoiceManagementSection ? 'active' : '' }}"><i data-feather="trending-down"></i><span>Invoice Management</span></a></li>--}}
						{{--<li class="submenu">
							<a href="javascript:void(0);" class="subdrop {{ $sb('admin.account-module', 'admin.accounting.*') ? 'active' : '' }}">
								<i data-feather="book-open"></i><span>Account Module</span><span class="menu-arrow"></span>
							</a>
							<ul>
								<li><a href="{{ route('admin.account-module') }}" class="{{ $sb('admin.account-module') }}">Overview</a></li>
								<li><a href="{{ route('admin.accounting.chart-of-accounts') }}" class="{{ $sb('admin.accounting.chart-of-accounts', 'admin.accounting.chart-of-accounts.*') }}">Chart of accounts</a></li>
								<li><a href="{{ route('admin.accounting.journals.index') }}" class="{{ $sb('admin.accounting.journals.*') }}">Journals</a></li>
								<li><a href="{{ route('admin.accounting.general-ledger') }}" class="{{ $sb('admin.accounting.general-ledger') }}">General ledger</a></li>
								<li><a href="{{ route('admin.accounting.trial-balance') }}" class="{{ $sb('admin.accounting.trial-balance') }}">Trial balance</a></li>
								<li><a href="{{ route('admin.accounting.transaction-mappings') }}" class="{{ $sb('admin.accounting.transaction-mappings') }}">Transaction mappings</a></li>
								<li><a href="{{ route('admin.suppliers.index') }}" class="{{ $sb('admin.suppliers.*', 'admin.supplier-ledger.*', 'admin.supplier-payments.*') }}">Suppliers</a></li>
								<li><a href="{{ route('admin.purchase-orders.index') }}" class="{{ $sb('admin.purchase-orders.*', 'admin.goods-receipt-notes.*', 'admin.supplier-purchase-invoices.*') }}">Purchases (GL)</a></li>
							</ul>
						</li>--}}
					</ul>
				</li>



				<li class="submenu-open">
					<h6 class="submenu-hdr">Account<h6>
					<ul>
						<li class="submenu">
							<a href="javascript:void(0);" class="subdrop {{ $sb('admin.account-module', 'admin.accounting.*') ? 'active' : '' }}">
								<i data-feather="book-open"></i><span>Account Module</span><span class="menu-arrow"></span>
							</a>
							<ul>
								<li><a href="{{ route('admin.account-module') }}" class="{{ $sb('admin.account-module') }}">Overview</a></li>
								<li><a href="{{ route('admin.accounting.chart-of-accounts') }}" class="{{ $sb('admin.accounting.chart-of-accounts', 'admin.accounting.chart-of-accounts.*') }}">Chart of accounts</a></li>
								<li><a href="{{ route('admin.accounting.journals.index') }}" class="{{ $sb('admin.accounting.journals.*') }}">Journals</a></li>
								<li><a href="{{ route('admin.accounting.general-ledger') }}" class="{{ $sb('admin.accounting.general-ledger') }}">General ledger</a></li>
								<li><a href="{{ route('admin.accounting.trial-balance') }}" class="{{ $sb('admin.accounting.trial-balance') }}">Trial balance</a></li>
								<li><a href="{{ route('admin.accounting.transaction-mappings') }}" class="{{ $sb('admin.accounting.transaction-mappings') }}">Transaction mappings</a></li>
								{{--<li><a href="{{ route('admin.suppliers.index') }}" class="{{ $sb('admin.suppliers.*', 'admin.supplier-ledger.*', 'admin.supplier-payments.*') }}">Suppliers</a></li>
								<li><a href="{{ route('admin.purchase-orders.index') }}" class="{{ $sb('admin.purchase-orders.*', 'admin.goods-receipt-notes.*', 'admin.supplier-purchase-invoices.*') }}">Purchases (GL)</a></li>--}}
							</ul>
						</li>


					<li class="submenu">
						{{--<a href="javascript:void(0);" class="subdrop {{ $sb('admin.profit-loss-statement', 'admin.profit-loss.*', 'admin.balance-sheet', 'admin.balance-sheet.*', 'admin.invoice', 'admin.view-invoice', 'admin.invoice.filter', 'admin.invoice-order', 'admin.get-products-invoice', 'admin.invoice-edit', 'admin.invoice-update', 'admin.invoice-item-price.update', 'admin.invoices.export', 'admin.sales-analysis-superadmin', 'admin.organization-info.*') ? 'active' : '' }}">--}}
					    <a href="javascript:void(0);" class="subdrop {{ $sb('admin.profit-loss-statement', 'admin.balance-sheet', 'admin.sales-analysis-superadmin') ? 'active' : '' }}">
							<i data-feather="box"></i><span>Reports</span><span class="menu-arrow"></span>
						</a>
						<ul>
							<li><a href="{{route('admin.profit-loss-statement')}}" class="{{ $sb('admin.profit-loss-statement', 'admin.profit-loss.*') }}"><i data-feather="box"></i><span>Profit &
										Loss Statements</span></a></li>
							<li><a href="{{route('admin.balance-sheet')}}" class="{{ $sb('admin.balance-sheet', 'admin.balance-sheet.*') }}"><i data-feather="plus-square"></i><span>View
										Balance Sheets</span></a></li>
							{{--<li><a href="{{ route('admin.invoice') }}" class="{{ $sb('admin.invoice', 'admin.view-invoice', 'admin.invoice.filter', 'admin.invoice-order', 'admin.get-products-invoice', 'admin.invoice-edit', 'admin.invoice-update', 'admin.invoice-item-price.update', 'admin.invoices.export') }}"><i data-feather="trending-down"></i><span>Invoice
										Management</span></a></li>--}}
							<li><a href="{{route('admin.sales-analysis-superadmin')}}" class="{{ $sb('admin.sales-analysis-superadmin') }}"><i
										data-feather="codepen"></i><span>Sales Analysis</span></a></li>
							{{--<li><a href="{{ route('admin.organization-info.index') }}" class="{{ $sb('admin.organization-info.*') }}"><i data-feather="home"></i><span>Oraganisation Info</span></a></li>--}}
						</ul>
				   </li>






					</ul>
				</li>


				<?php
				/*
				<li class="submenu-open">
					{{--<h6 class="submenu-hdr">Financial Oversight</h6>--}}
					<h6 class="submenu-hdr">Ledgers</h6>
					<ul>
						<li><a href="{{route('admin.profit-loss-statement')}}" class="{{ $sb('admin.profit-loss-statement', 'admin.profit-loss.*') }}"><i data-feather="box"></i><span>Profit &
									Loss Statements</span></a></li>
						<li><a href="{{route('admin.balance-sheet')}}" class="{{ $sb('admin.balance-sheet', 'admin.balance-sheet.*') }}"><i data-feather="plus-square"></i><span>View
									Balance Sheets</span></a></li>
						<li><a href="{{ route('admin.invoice') }}" class="{{ $sb('admin.invoice', 'admin.view-invoice', 'admin.invoice.filter', 'admin.invoice-order', 'admin.get-products-invoice', 'admin.invoice-edit', 'admin.invoice-update', 'admin.invoice-item-price.update', 'admin.invoices.export') }}"><i data-feather="trending-down"></i><span>Invoice
									Management</span></a></li>
						<li><a href="{{route('admin.sales-analysis-superadmin')}}" class="{{ $sb('admin.sales-analysis-superadmin') }}"><i
									data-feather="codepen"></i><span>Sales Analysis</span></a></li>

						{{-- <li><a href="javascript:void(0);"><i data-feather="home"></i><span>Oraganisation Info</span></a></li> --}}
						<li><a href="{{ route('admin.organization-info.index') }}" class="{{ $sb('admin.organization-info.*') }}"><i data-feather="home"></i><span>Oraganisation Info</span></a></li>
					</ul>
				</li>
				*/ ?>
				<li class="submenu-open">
					<h6 class="submenu-hdr">Staff Management</h6>
					<ul>
						<li><a href="{{ route('admin.users') }}" class="{{ $sb('admin.users', 'admin.view-employee', 'admin.edit-employee', 'admin.add-employee', 'admin.delete-employee', 'admin.update-employee', 'admin.change-status', 'admin.save-permission') }}"><i data-feather="user"></i><span>Employees</span></a>
						</li>
						<!-- <li><a href="#"><i data-feather="git-merge"></i><span>permi</span></a></li> -->
						<li><a href="{{ route('admin.store-employee') }}" class="{{ $sb('admin.store-employee', 'admin.add-store-staff-admin', 'admin.view-staff-admin', 'admin.edit-staff-admin', 'admin.update-staff-admin', 'admin.delete-staff-admin', 'admin.change-status-storeEmployee') }}"><i data-feather="user"></i><span>Store
									Employees</span></a></li>
					</ul>
				</li>
				<li class="submenu-open">
					<h6 class="submenu-hdr">Customizable Reports</h6>
					<ul>
						<li><a href="{{ route('admin.sales-trend') }}" class="{{ $sb('admin.sales-trend', 'admin.sales-trend-filter', 'admin.sales-trend-report.fetch') }}"><i data-feather="bar-chart-2"></i><span>Sales
									trends</span></a></li>
						<li><a href="{{ route('admin.inventory-flow-superAdmin') }}" class="{{ $sb('admin.inventory-flow-superAdmin') }}"><i
									data-feather="inbox"></i><span>Inventory flow</span></a></li>
						<li><a href="{{route('admin.top-performing-product')}}" class="{{ $sb('admin.top-performing-product', 'admin.top-products-data', 'admin.under-products-data', 'admin.top-view-all', 'admin.under-view-all') }}"><i
									data-feather="bar-chart"></i><span>Top-performing products</span></a></li>
					</ul>
				</li>
				<li class="submenu-open">
					<h6 class="submenu-hdr">Setting</h6>
					<ul>
						<li>
							<a href="{{route('admin.history.index')}}" class="{{ $sb('admin.history.*') }}"><i data-feather="monitor"></i><span>Audit
									Trail</span></a>
						</li>
						<li>
							<a href="{{route('admin.setting')}}" class="{{ $sb('admin.setting', 'admin.setting-store') }}"><i data-feather="monitor"></i><span>Settings</span></a>
						</li>
						<li><a href="{{ route('admin.organization-info.index') }}" class="{{ $sb('admin.organization-info.*') }}"><i data-feather="home"></i><span>Oraganisation Info</span></a></li>
						{{--<li><a href="javascript:void(0);" class=""><i data-feather="home"></i><span>Inventory Valuation setting</span></a></li>--}}
						<!-- <li>
                            <a href="#"
                                ><i data-feather="hexagon"></i
                                ><span>Audit Logs</span></a
                            >
                        </li> -->
					</ul>
				</li>
				<!-- <li class="submenu-open">
							<h6 class="submenu-hdr">Settings</h6>
							<ul>
								<li><a href="{{ route('admin.setting') }}"><i data-feather="monitor"></i><span>System Settings</span></a></li>
							</ul>
						</li> -->

			</ul>
		</div>
	</div>
</div>
<!-- /Sidebar -->
