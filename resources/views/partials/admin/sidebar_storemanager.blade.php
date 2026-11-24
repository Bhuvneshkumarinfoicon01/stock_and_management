<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="submenu-open">
                    <!-- <h6 class="submenu-hdr">Dashboard</h6> -->
                    <ul>
                        <li class="submenu">
                            <a href="javascript:void(0);"><i data-feather="grid"></i><span>Dashboard</span><span
                                    class="menu-arrow"></span></a>
                            <ul>
                                <li>
                                    <a href="{{
                                            route('admin.storemanager_dashboard')
                                        }}" class="subdrop"><span>Sales Summary</span></a>
                                </li>
                                <li>
                                    <a href="{{
                                            route('admin.inventory-summary')
                                        }}" class="subdrop"><span>Inventory Summary</span></a>
                                </li>
                                <li>
                                    <a href="{{
                                            route('admin.report-section')
                                        }}" class="subdrop"><span>Report Section</span></a>
                                </li>
                                <li>
                                    <a href="{{
                                            route('admin.customer-insights')
                                        }}" class="subdrop"><span>Customer Insight</span></a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>

                <li class="submenu-open">
                    <h6 class="submenu-hdr">Sales Management</h6>
                    <ul>
                        <li>
                            <a href="{{ route('admin.sales') }}"><i data-feather="shopping-cart"></i><span>Sales
                                    Transaction</span></a>
                        </li>
                        <li>
                            <a href="{{ route('admin.salesTrend') }}"><i data-feather="copy"></i><span>Sales
                                    Report</span></a>
                        </li>
                        <li>
                            <a href="{{ route('admin.customer') }}"><i data-feather="user"></i><span>Customer
                                    Interaction</span></a>
                        </li>
                    </ul>
                </li>
                <li class="submenu-open">
                    <h6 class="submenu-hdr">Inventory Management</h6>
                    <ul>
                        <li>
                            <a href="{{ route('admin.stock.list.storemanager') }}"><i data-feather="box"></i><span>All
                                    Stock</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.stock.create') }}"><i data-feather="plus-square"></i><span>Add
                                    Stock</span></a>
                        </li>
                        <li>
                            <a href="{{ route('admin.stocktransfers.index') }}"><i data-feather="truck"></i><span>Stock
                                    transfers</span></a>
                        </li>
                        <li>
                            <a href="{{ route('admin.stock-adjustment') }}"><i data-feather="clipboard"></i><span>Stock
                                    Adjustment</span></a>
                        </li>
                        <li>
                            <a href="{{ route('admin.incoming-stock') }}"><i data-feather="box"></i><span>Incoming
                                    Stock</span></a>
                        </li>
                        <li>
                            <a href="{{ route('admin.outgoing-stock') }}"><i data-feather="package"></i><span>Outgoing
                                    Stock</span></a>
                        </li>
                        <li>
                            <a href="{{ route('admin.real-time-stock') }}"><i data-feather="layers"></i><span>Real-Time
                                    Updates</span></a>
                        </li>
                    </ul>
                </li>
                <li class="submenu-open">
                    <h6 class="submenu-hdr">Point of Sale</h6>
                    <ul>
                        <li>
                            <a href="{{ route('admin.pos-terminal') }}"><i data-feather="hard-drive"></i><span>Configure
                                    POS Terminals</span></a>
                        </li>
                        <li><a href="{{ route('admin.invoice') }}"><i data-feather="trending-down"></i><span>Invoice
                                    Management</span></a>
                        </li>
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
                    <h6 class="submenu-hdr">Operational Reporting</h6>
                    <ul>
                        <li>
                            <a href="{{ route('admin.sales-performance') }}"><i data-feather="bar-chart"></i><span>Sales
                                    Performance Reports</span></a>
                        </li>
                        <li>
                            <a href="{{ route('admin.inventory-report') }}"><i data-feather="inbox"></i><span>Inventory
                                    Reports</span></a>
                        </li>
                        <li>
                            <a href="{{ route('admin.customer-report') }}"><i data-feather="user"></i><span>Customer
                                    Reports</span></a>
                        </li>
                    </ul>
                </li>
                <li class="submenu-open">
                    <h6 class="submenu-hdr">Store Operations</h6>
                    <ul>
                        <li>
                            <a href="{{ route('admin.store-list-superadmin') }}"><i data-feather="box"></i><span>All
                                    Stores</span></a>
                        </li>
                        <li>
                            <a href="{{ route('admin.stores_communication') }}"><i
                                    data-feather="message-circle"></i><span>Communication</span></a>
                        </li>
                        <li>
                            <a href="{{
                                    route('admin.daily-operations-overview-admin')
                                }}"><i data-feather="plus-square"></i><span>Daily operation overview</span></a>
                        </li>
                    </ul>
                </li>
                <li class="submenu-open">
                    <h6 class="submenu-hdr">Staff Management</h6>
                    <ul>
                        <li><a href="{{ route('admin.users') }}"><i data-feather="user"></i><span>Employees</span></a>
                        </li>
                        <li>
                            <a href="{{ route('admin.employee-list') }}"><i data-feather="user"></i><span>Manage Store
                                    Staff</span></a>
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

                {{--<li class="submenu-open">
                    <!-- <h6 class="submenu-hdr">System Settings & Preferences</h6> -->
                    <!-- <ul> -->
                    <!-- <li>
                            <a href="{{ route('admin.User') }}"
                                ><i data-feather="monitor"></i
                                ><span>User Roles & Permissions</span></a
                            >
                        </li> -->
                    <!-- <li> -->
                    <!-- <a href="{{ route('admin.setting') }}"><i data-feather="hexagon"></i><span>System
                                    Configurations</span></a>
                        </li>
                    </ul> -->
                </li>--}}
            </ul>
        </div>
    </div>
</div>
<!-- /Sidebar -->