<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="submenu-open">
                    <h6 class="submenu-hdr">Dashboard</h6>
                    <ul>
                        <li>
                            <a href="{{ route('admin.dashboard') }}" class="subdrop"><i
                                    data-feather="grid"></i><span>Dashboard</span></a>
                        </li>
                    </ul>
                </li>
                <li class="submenu-open">
                    <h6 class="submenu-hdr">Store</h6>
                    <ul>
                        <li>
                            <a href="{{ route('admin.stores_communication') }}"><i
                                    data-feather="plus-square"></i><span>Communication</span></a>
                        </li>
                        <li>
                            <a href="{{
                                    route('admin.daily-operations-overview-admin')
                                }}"><i data-feather="plus-square"></i><span>Daily operation overview</span></a>
                        </li>
                    </ul>
                </li>
                <li class="submenu-open">
                    <h6 class="submenu-hdr">Store Operations</h6>
                    <ul>
                        <li>
                            <a href="{{ route('admin.stock.list') }}"><i data-feather="box"></i><span>All
                                    Stock</span></a>
                        </li>
                        <li>
                            <a href="{{ route('admin.stock.create') }}"><i data-feather="plus-square"></i><span>Create Product</span></a>
                        </li>
                        <li>
                            <a href="{{ route('admin.stocktransfers.index') }}"><i data-feather="truck"></i><span>Stock
                                    transfers</span></a>
                        </li>
                        <li>
                            <a href="{{ route('admin.stock-adjustment') }}"><i data-feather="clipboard"></i><span>Stock
                                    Adjustment</span></a>
                        </li>
                        <!-- <li>
                            <a href="{{--route('manage-stocks')--}}"
                                ><i data-feather="package"></i
                                ><span>Discrepencies</span></a
                            >
                        </li>
                        <li>
                            <a href="{{--route('stor2storecomm')--}}"
                                ><i data-feather="plus-square"></i
                                ><span>Communication</span></a
                            >
                        </li> -->
                    </ul>
                </li>
                <li class="submenu-open">
                    {{--<h6 class="submenu-hdr">Financial Oversight</h6>--}}
                    <h6 class="submenu-hdr">Reports</h6>
                    <ul>
                        <li>
                            <a href="{{ route('admin.profit-loss-statement') }}"><i
                                    data-feather="pie-chart"></i><span>Profit & Loss</span></a>
                        </li>
                        <li>
                            <a href="{{ route('admin.balance-sheet') }}"><i data-feather="plus-square"></i><span>Balance
                                    Sheet</span></a>
                        </li>
                        <li>
                            <a href="{{ route('admin.invoice') }}"><i data-feather="file"></i><span>Invoice</span></a>
                        </li>
                    </ul>
                </li>
                <!-- <li class="submenu-open">
							<h6 class="submenu-hdr">Inventory</h6>
							<ul>
								<li><a href="stock-list.php"><i data-feather="box"></i><span>All Stock</span></a></li>
								<li><a href="add-stock.php"><i data-feather="plus-square"></i><span>Add Stock</span></a></li>
								<li><a href="stock-transfer.php"><i data-feather="truck"></i><span>Stock transfers</span></a></li>
								<li><a href="stock-adjustment.php"><i data-feather="clipboard"></i><span>Stock Adjustment</span></a></li>
								<li><a href="manage-stocks.php"><i data-feather="package"></i><span>Discrepencies</span></a></li>
							</ul>
						</li> -->

                <li class="submenu-open">
                    <h6 class="submenu-hdr">Staff Management</h6>
                    <ul>
                        <li>
                            <a href="{{ route('admin.users') }}"><i data-feather="user"></i><span>Employees</span></a>
                        </li>
                        <li><a href="{{ route('admin.store-employee') }}"><i data-feather="user"></i><span>Store
                                    Employees</span></a></li>
                    </ul>
                </li>
                <!-- <li class="submenu-open">
							<h6 class="submenu-hdr">Promotion</h6>
							<ul>
								<li><a href="Promotion.php"><i data-feather="save"></i><span>Add Promotion</span></a></li>
								<li><a href="view-promotion.php"><i data-feather="eye"></i><span>View Promotion</span></a></li>
							</ul>
						</li> -->
                <!-- <li class="submenu-open">
                    <h6 class="submenu-hdr">Ledger</h6>
                    <ul>
                        <li>
                            <a href="{{ route('admin.ledger') }}"
                                ><i data-feather="columns"></i
                                ><span>View / Add Ledger</span></a
                            >
                        </li>
                    </ul>
                </li> -->
                <li class="submenu-open">
                    <h6 class="submenu-hdr">Point of Sale</h6>
                    <ul>
                        <li>
                            <a href="{{ route('admin.pos-terminal') }}"><i data-feather="hard-drive"></i><span>Configure
                                    POS Terminals</span></a>
                        </li>
                        <li><a href="{{ route('admin.invoice') }}"><i data-feather="trending-down"></i><span>Invoice
                                    Management</span></a></li>
                        <li>
                            <a href="{{ route('admin.transaction-manage') }}"><i
                                    data-feather="refresh-ccw"></i><span>Transaction Management</span></a>
                        </li>
                        <li>
                            <a href="{{ route('admin.pos-sale-insight') }}"><i
                                    data-feather="shopping-cart"></i><span>Sales Insights</span></a>
                        </li>
                    </ul>
                </li>
                <li class="submenu-open">
                    <h6 class="submenu-hdr">Reports</h6>
                    <ul>
                        <li>
                            <a href="{{ route('admin.sales-trend') }}"><i data-feather="bar-chart-2"></i><span>Sales
                                    trends</span></a>
                        </li>
                        <li>
                            <a href="{{ route('admin.inventory-flow-admin') }}"><i
                                    data-feather="refresh-cw"></i><span>Inventory flow</span></a>
                        </li>
                        <li>
                            <a href="{{
                                    route('admin.top-performing-product')
                                }}"><i data-feather="bar-chart"></i><span>Top-performing products</span></a>
                        </li>
                    </ul>
                </li>
                <li class="submenu-open">
                    <h6 class="submenu-hdr">Setting</h6>
                    <ul>
                        <li>
                            <a href="{{route('admin.setting')}}"><i data-feather="monitor"></i><span>Settings</span></a>
                        </li>
                        <!-- <li>
                            <a href="#"
                                ><i data-feather="hexagon"></i
                                ><span>Audit Logs</span></a
                            >
                        </li> -->
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- /Sidebar -->
{{--
<!-- Sidebar -->
<div class="sidebar collapsed-sidebar" id="collapsed-sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu-2" class="sidebar-menu sidebar-menu-three">
            <aside id="aside" class="ui-aside">
                <ul class="tab nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="tablinks nav-link active" href="#home" id="home-tab" data-bs-toggle="tab"
                            data-bs-target="#home" role="tab" aria-selected="true">
                            <img src="{{
                                    asset(
                                        'admin/assets/img/icons/menu-icon.svg'
                                    )
                                }}" alt="" />
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="tablinks nav-link" href="#messages" id="messages-tab" data-bs-toggle="tab"
                            data-bs-target="#product" role="tab" aria-selected="false">
                            <img src="{{
                                    asset('admin/assets/img/icons/product.svg')
                                }}" alt="" />
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="tablinks nav-link" href="#profile" id="profile-tab" data-bs-toggle="tab"
                            data-bs-target="#sales" role="tab" aria-selected="false">
                            <img src="{{
                                    asset('admin/assets/img/icons/sales1.svg')
                                }}" alt="" />
                        </a>
                    </li>

                    <li class="nav-item" role="presentation">
                        <a class="tablinks nav-link" href="#report" id="report-tab" data-bs-toggle="tab"
                            data-bs-target="#purchase" role="tab" aria-selected="true">
                            <img src="{{
                                    asset(
                                        'admin/assets/img/icons/purchase1.svg'
                                    )
                                }}" alt="" />
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="tablinks nav-link" href="#set" id="set-tab" data-bs-toggle="tab"
                            data-bs-target="#user" role="tab" aria-selected="true">
                            <img src="{{
                                    asset('admin/assets/img/icons/users1.svg')
                                }}" alt="" />
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="tablinks nav-link" href="#set2" id="set-tab2" data-bs-toggle="tab"
                            data-bs-target="#employee" role="tab" aria-selected="true">
                            <img src="{{
                                    asset(
                                        'admin/assets/img/icons/calendars.svg'
                                    )
                                }}" alt="" />
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="tablinks nav-link" href="#set3" id="set-tab3" data-bs-toggle="tab"
                            data-bs-target="#report" role="tab" aria-selected="true">
                            <img src="{{
                                    asset('admin/assets/img/icons/printer.svg')
                                }}" alt="" />
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="tablinks nav-link" href="#set4" id="set-tab4" data-bs-toggle="tab"
                            data-bs-target="#document" role="tab" aria-selected="true">
                            <i data-feather="file-minus"></i>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="tablinks nav-link" href="#set5" id="set-tab6" data-bs-toggle="tab"
                            data-bs-target="#permission" role="tab" aria-selected="true">
                            <i data-feather="user"></i>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="tablinks nav-link" href="#set6" id="set-tab5" data-bs-toggle="tab"
                            data-bs-target="#settings" role="tab" aria-selected="true">
                            <i data-feather="settings"></i>
                        </a>
                    </li>
                </ul>
            </aside>
        </div>
    </div>
</div>
<!-- /Sidebar -->

--}}