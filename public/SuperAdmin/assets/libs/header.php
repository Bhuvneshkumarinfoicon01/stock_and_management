<!DOCTYPE html>
<html lang="en" data-layout-mode="light_mode">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<meta name="description" content="">
	<meta name="keywords" content="">
	<meta name="author" content="">
	<meta name="robots" content="noindex, nofollow">
	<title>Accounting and Stock Management System</title>

	<script src="assets/js/theme-script.js"></script>	

	<!-- Favicon -->
	<link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">

	<!-- Datetimepicker CSS -->
	<link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css">

	<!-- DataTables CSS -->
	<link rel="stylesheet" href="assets/css/dataTables.bootstrap5.min.css">

	<!-- animation CSS -->
	<link rel="stylesheet" href="assets/css/animate.css">

	<!-- animation CSS -->
	<link rel="stylesheet" href="assets/css/boxicons.min.css">

	<!-- Select2 CSS -->
	<link rel="stylesheet" href="assets/css/select2.min.css">

	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">


	<!-- Main CSS -->
	<link rel="stylesheet" href="assets/css/style.css">

</head>

<body>
	<div id="global-loader">
		<div class="whirly-loader"> </div>
	</div>
	<!-- Main Wrapper -->
	<div class="main-wrapper">

		<!-- Header -->
		<div class="header">

			<!-- Logo -->
			<div class="header-left active">
				<a href="index.php" class="logo logo-normal">
					<h6>Accounting and Stock Management System</h6>
				</a>
				<a href="index.php" class="logo logo-white">
					<h6>Accounting and Stock Management System</h6>
				</a>
				<a href="index.php" class="logo-small">
					<h6>A & S</h6>
				</a>
				<a id="toggle_btn" href="javascript:void(0);">
					<i data-feather="chevrons-left" class="feather-16"></i>
				</a>
			</div>
			<!-- /Logo -->

			<a id="mobile_btn" class="mobile_btn" href="#sidebar">
				<span class="bar-icon">
					<span></span>
					<span></span>
					<span></span>
				</span>
			</a>

			<!-- Header Menu -->
			<ul class="nav user-menu">

				<!-- Search -->
				<li class="nav-item nav-searchinputs">
					<div class="top-nav-search">
						<a href="javascript:void(0);" class="responsive-search">
							<i class="fa fa-search"></i>
						</a>
						<form action="#" class="dropdown">
							<div class="searchinputs dropdown-toggle" id="dropdownMenuClickable"
								data-bs-toggle="dropdown" data-bs-auto-close="false">
								<input type="text" placeholder="Search">
								<div class="search-addon">
									<span><i data-feather="x-circle" class="feather-14"></i></span>
								</div>
							</div>
						</form>
					</div>
				</li>
				<!-- /Search -->


				<!-- Select Store -->
				<li class="nav-item dropdown has-arrow main-drop select-store-dropdown">
					<a href="javascript:void(0);" class="dropdown-toggle nav-link select-store"
						data-bs-toggle="dropdown">
						<span class="user-info">
							<span class="user-letter">
								<img src="assets/img/store/store-01.png" alt="Store Logo" class="img-fluid">
							</span>
							<span class="user-detail">
								<span class="user-name">Select Store</span>
							</span>
						</span>
					</a>
					<div class="dropdown-menu dropdown-menu-right">
						<a href="javascript:void(0);" class="dropdown-item">
							<img src="assets/img/store/store-01.png" alt="Store Logo" class="img-fluid"> Storname 1
						</a>
						<a href="javascript:void(0);" class="dropdown-item">
							<img src="assets/img/store/store-02.png" alt="Store Logo" class="img-fluid"> Storname 2
						</a>
						<a href="javascript:void(0);" class="dropdown-item">
							<img src="assets/img/store/store-03.png" alt="Store Logo" class="img-fluid"> Storname 3
						</a>
						<a href="javascript:void(0);" class="dropdown-item">
							<img src="assets/img/store/store-04.png" alt="Store Logo" class="img-fluid"> Storname 4
						</a>
					</div>
				</li>
				<!-- /Select Store -->



				<li class="nav-item dropdown nav-item-box">
					<a href="javascript:void(0);" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
						<i data-feather="bell"></i><span class="badge rounded-pill">2</span>
					</a>
					<div class="dropdown-menu notifications">
						<div class="topnav-dropdown-header">
							<span class="notification-title">Notifications</span>
							<a href="javascript:void(0)" class="clear-noti"> Clear All </a>
						</div>
						<div class="noti-content">
							<ul class="notification-list">
								<li class="notification-message">
									<a href="#">
										<div class="media d-flex">
											<span class="avatar flex-shrink-0">
												<img alt="" src="assets/img/profiles/avatar-02.jpg">
											</span>
											<div class="media-body flex-grow-1">
												<p class="noti-details"><span class="noti-title">John Doe</span> added
													new products <span class="noti-title"></span>
												</p>
												<p class="noti-time"><span class="notification-time">4 mins ago</span>
												</p>
											</div>
										</div>
									</a>
								</li>
								<li class="notification-message">
									<a href="#">
										<div class="media d-flex">
											<span class="avatar flex-shrink-0">
												<img alt="" src="assets/img/profiles/avatar-02.jpg">
											</span>
											<div class="media-body flex-grow-1">
												<p class="noti-details"><span class="noti-title">John Doe</span> added
													new products <span class="noti-title"></span>
												</p>
												<p class="noti-time"><span class="notification-time">4 mins ago</span>
												</p>
											</div>
										</div>
									</a>
								</li>
							</ul>
						</div>
						<div class="topnav-dropdown-footer">
							<a href="#">View all Notifications</a>
						</div>
					</div>
				</li>
				<!-- /Notifications -->

				<li class="nav-item nav-item-box">
					<a href="#"><i data-feather="settings"></i></a>
				</li>

				<li class="nav-item dropdown has-arrow main-drop">
					<a href="javascript:void(0);" class="dropdown-toggle nav-link userset" data-bs-toggle="dropdown">
						<span class="user-info">
							<span class="user-letter">
								<img src="assets/img/profiles/avator1.jpg" alt="" class="img-fluid">
							</span>
							<span class="user-detail">
								<span class="user-name">Lorem Ipsum</span>
								<span class="user-role">Super Admin</span>
							</span>
						</span>
					</a>
					<div class="dropdown-menu menu-drop-user">
						<div class="profilename">
							<div class="profileset">
								<span class="user-img"><img src="assets/img/profiles/avator1.jpg" alt="">
									<span class="status online"></span></span>
								<div class="profilesets">
									<h6>Lorem Ipsum</h6>
									<h5>Super Admin</h5>
								</div>
							</div>
							<hr class="m-0">
							<a class="dropdown-item" href="profile.php"> <i class="me-2" data-feather="user"></i> My Profile</a>
							<a class="dropdown-item" href="setting.php"><i class="me-2" data-feather="settings"></i>Settings</a>
							<hr class="m-0">
							<a class="dropdown-item logout pb-0" href="login.php"><img src="assets/img/icons/log-out.svg" class="me-2" alt="img">Logout</a>
						</div>
					</div>
				</li>
			</ul>
			<!-- /Header Menu -->

			<!-- Mobile Menu -->
			<div class="dropdown mobile-user-menu">
				<a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
				<div class="dropdown-menu dropdown-menu-right">
					<a class="dropdown-item" href="profile.php">My Profile</a>
					<a class="dropdown-item" href="setting.php">Settings</a>
					<a class="dropdown-item" href="login.php">Logout</a>
				</div>
			</div>
			<!-- /Mobile Menu -->
		</div>
		<!-- /Header -->

		<!-- Sidebar -->
		<div class="sidebar" id="sidebar">
			<div class="sidebar-inner slimscroll">
				<div id="sidebar-menu" class="sidebar-menu">
					<ul>
						<li class="submenu-open">
							<h6 class="submenu-hdr">Main</h6>
							<ul>
								<li class="submenu">
									<a href="javascript:void(0);" class="subdrop active"><i data-feather="grid"></i><span>Dashboard</span><span class="menu-arrow"></span></a>
									<ul>
										<li><a href="index.php" class="active">Admin Dashboard</a></li>
										<li><a href="inventory.php">Inventory Dashboard</a></li>
										<li><a href="finance.php">Financial Oversight</a></li>
									</ul>
								</li>
							</ul>
						</li>
						<li class="submenu-open">
							<h6 class="submenu-hdr">Store</h6>
							<ul>
								<li><a href="store-list.php"><i data-feather="box"></i><span>All Stores</span></a></li>
								<li><a href="stor2storecomm.php"><i data-feather="plus-square"></i><span>Communication</span></a></li>
								<li><a href="daily-operations-overview.php"><i data-feather="plus-square"></i><span>Daily operations overview</span></a></li>
							</ul>
						</li>
						<li class="submenu-open">
							<h6 class="submenu-hdr">Stock</h6>
							<ul>
								<li><a href="stock-list.php"><i data-feather="box"></i><span>All Stock</span></a></li>
								<li><a href="add-stock.php"><i data-feather="plus-square"></i><span>Add Stock</span></a></li>
								<li><a href="stock-transfer.php"><i data-feather="truck"></i><span>Stock transfers</span></a></li>
								<li><a href="stock-adjustment.php"><i data-feather="clipboard"></i><span>Stock Adjustment</span></a></li>
								<li><a href="manage-stocks.php"><i data-feather="package"></i><span>Manage Stock</span></a></li>
							</ul>
						</li>
						<li class="submenu-open">
							<h6 class="submenu-hdr">Financial Oversight</h6>
							<ul>
								<li><a href="profit-loss-statement.php"><i data-feather="box"></i><span>Profit & Loss Statements</span></a></li>
								<li><a href="balance-sheet.php"><i data-feather="plus-square"></i><span>View Balance Sheets</span></a></li>
								<li><a href="invoice.php"><i data-feather="trending-down"></i><span>Invoice Management</span></a></li>
								<li><a href="sales-analysis.php"><i data-feather="codepen"></i><span>Sales Analysis</span></a></li>
							</ul>
						</li>
						<li class="submenu-open">
							<h6 class="submenu-hdr">Staff Management</h6>
							<ul>
								<li><a href="employee-list.php"><i data-feather="user"></i><span>Employees</span></a></li>
								<!-- <li><a href="#"><i data-feather="git-merge"></i><span>permi</span></a></li> -->
							</ul>
						</li>
						<li class="submenu-open">
							<h6 class="submenu-hdr">Customizable Reports</h6>
							<ul>
								<li><a href="sales-trend.php"><i data-feather="bar-chart-2"></i><span>Sales trends</span></a></li>
								<li><a href="inventory-flow.php"><i data-feather="inbox"></i><span>Inventory flow</span></a></li>
								<li><a href="top-performing-product.php"><i data-feather="bar-chart"></i><span>Top-performing products</span></a></li>
							</ul>
						</li>

						<li class="submenu-open">
							<h6 class="submenu-hdr">Settings</h6>
							<ul>
								<li><a href="setting.php"><i data-feather="monitor"></i><span>System Settings</span></a></li>
							</ul>
						</li>
						
					</ul>
				</div>
			</div>
		</div>
		<!-- /Sidebar -->

		<!-- Sidebar -->
		<div class="sidebar collapsed-sidebar" id="collapsed-sidebar">
			<div class="sidebar-inner slimscroll">
				<div id="sidebar-menu-2" class="sidebar-menu sidebar-menu-three">
					<aside id="aside" class="ui-aside">
						<ul class="tab nav nav-tabs" id="myTab" role="tablist">
						<li class="nav-item" role="presentation">
							<a class="tablinks nav-link active" href="#home" id="home-tab" data-bs-toggle="tab" data-bs-target="#home"  role="tab" aria-selected="true">
								<img src="assets/img/icons/menu-icon.svg" alt="">
							</a>
						</li>
						<li class="nav-item" role="presentation">
							<a class="tablinks nav-link" href="#messages" id="messages-tab" data-bs-toggle="tab" data-bs-target="#product"  role="tab" aria-selected="false">
								<img src="assets/img/icons/product.svg" alt="">
							</a>
						</li>
						<li class="nav-item" role="presentation"> 
							<a class="tablinks nav-link"  href="#profile" id="profile-tab" data-bs-toggle="tab" data-bs-target="#sales"  role="tab" aria-selected="false">
								<img src="assets/img/icons/sales1.svg" alt="">
							</a>
						</li>
						
						<li class="nav-item" role="presentation">
							<a class="tablinks nav-link" href="#report" id="report-tab" data-bs-toggle="tab" data-bs-target="#purchase"  role="tab" aria-selected="true">
								<img src="assets/img/icons/purchase1.svg" alt="">
							</a>
						</li>
						<li class="nav-item" role="presentation">
							<a class="tablinks nav-link" href="#set" id="set-tab" data-bs-toggle="tab" data-bs-target="#user"  role="tab" aria-selected="true">
								<img src="assets/img/icons/users1.svg" alt="">
							</a>
						</li>
						<li class="nav-item" role="presentation">
							<a class="tablinks nav-link" href="#set2" id="set-tab2" data-bs-toggle="tab" data-bs-target="#employee"  role="tab" aria-selected="true">
								<img src="assets/img/icons/calendars.svg" alt="">
							</a>
						</li>
						<li class="nav-item" role="presentation">
							<a class="tablinks nav-link" href="#set3" id="set-tab3" data-bs-toggle="tab" data-bs-target="#report"  role="tab" aria-selected="true">
								<img src="assets/img/icons/printer.svg" alt="">
							</a>
						</li>
						<li class="nav-item" role="presentation">
							<a class="tablinks nav-link" href="#set4" id="set-tab4" data-bs-toggle="tab" data-bs-target="#document"  role="tab" aria-selected="true">
								<i data-feather="file-minus"></i>
							</a>
						</li>
						<li class="nav-item" role="presentation">
							<a class="tablinks nav-link" href="#set5" id="set-tab6" data-bs-toggle="tab" data-bs-target="#permission"  role="tab" aria-selected="true">
								<i data-feather="user"></i>
							</a>
						</li>
						<li class="nav-item" role="presentation">
							<a class="tablinks nav-link" href="#set6" id="set-tab5" data-bs-toggle="tab" data-bs-target="#settings"  role="tab" aria-selected="true">
								<i data-feather="settings"></i>
							</a>
						</li> 
						</ul>
					</aside>
				
				</div>
			</div>
		</div>
		<!-- /Sidebar -->

