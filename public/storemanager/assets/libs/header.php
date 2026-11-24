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
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" integrity="sha512-tS3S5qG0BlhnQROyJXvNjeEM4UpMXHrQfTGmbQ1gKmelCxlSEBUaxhRBj/EFTzpbP4RVSrpEikbmdJobCvhE3g==" crossorigin="anonymous" referrerpolicy="no-referrer" />

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
							<!-- <h6 class="submenu-hdr">Dashboard</h6> -->
							<ul>
								<li class="submenu">
									<a href="javascript:void(0);" ><i data-feather="grid"></i><span>Dashboard</span><span class="menu-arrow"></span></a>
									<ul>
										<li><a href="index.php" class="subdrop"><span>Sales Summary</span></a></li>
										<li><a href="inventory-summary.php" class="subdrop"><span>Inventory Summary</span></a></li>
										<li><a href="report-section.php" class="subdrop"><span>Report Section</span></a></li>
										<li><a href="customer-insights.php" class="subdrop"><span>Customer Insight</span></a></li>
									</ul>
								</li>
							</ul>								
						</li>
					
						<li class="submenu-open">
							<h6 class="submenu-hdr">Sales Management</h6>
							<ul>
								<li><a href="sales.php"><i data-feather="shopping-cart"></i><span>Sales Transaction</span></a></li>
								<li><a href="sales-trend.php"><i data-feather="copy"></i><span>Sales Report</span></a></li>
								<li><a href="customer.php"><i data-feather="user"></i><span>Customer Interaction</span></a></li>
							</ul>
						</li>
						<li class="submenu-open">
							<h6 class="submenu-hdr">Inventory Management</h6>
							<ul>
								<li><a href="incoming-stock.php"><i data-feather="box"></i><span>Incoming Stock</span></a></li>
								<li><a href="outgoing-stock.php"><i data-feather="package"></i><span>Outgoing Stock</span></a></li>
								<li><a href="real-time-stock.php"><i data-feather="layers"></i><span>Real-Time Updates</span></a></li>
							</ul>
						</li>
						<li class="submenu-open">
							<h6 class="submenu-hdr">Operational Reporting</h6>
							<ul>
								<li><a href="sales-performance.php"><i data-feather="bar-chart"></i><span>Sales Performance Reports</span></a></li>
								<li><a href="inventory-report.php"><i data-feather="inbox"></i><span>Inventory Reports</span></a></li>
								<li><a href="customer-report.php"><i data-feather="user"></i><span>Customer Reports</span></a></li>
							</ul>
						</li>
						<li class="submenu-open">
							<h6 class="submenu-hdr">Internal Communication</h6>
							<ul>
								<li><a href="stor2storecomm.php"><i data-feather="message-circle"></i><span>Communication</span></a></li>
							</ul>
						</li>
						<li class="submenu-open">
							<h6 class="submenu-hdr">Staff Management</h6>
							<ul>
								<li><a href="employee-list.php"><i data-feather="user"></i><span>Manage Store Staff</span></a></li>
							</ul>
						</li>

						<li class="submenu-open">
							<h6 class="submenu-hdr">System Settings & Preferences</h6>
							<ul>
								<li><a href="User.php"><i data-feather="monitor"></i><span>User Roles & Permissions</span></a></li>
								<li><a href="setting.php"><i data-feather="hexagon"></i><span>System Configurations</span></a></li>
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


		<div class="modal fade" id="add-stores">
            <div class="modal-dialog modal-dialog-centered custom-modal-two">
                <div class="modal-content">
                    <div class="page-wrapper-new p-0">
                        <div class="content">
                            <div class="modal-header border-0 custom-modal-header">
                                <div class="page-title">
                                    <h4>Create Store</h4>
                                </div>
                                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body custom-modal-body">
                                <form>
                                    <div class="mb-3">
                                        <label class="form-label">Store Name</label>
                                        <input type="text" class="form-control">
                                    </div>
                                    <!-- <div class="mb-3">
                                        <label class="form-label">User Name</label>
                                        <input type="text" class="form-control">
                                    </div>
                                    <div class="input-blocks mb-3">
                                        <label>Password</label>
                                        <div class="pass-group">
                                            <input type="password" class=" pass-input">
                                            <span class="fas toggle-password fa-eye-slash"></span>
                                        </div>
                                    </div> -->
                                    <div class="mb-3">
                                        <label class="form-label">Address</label>
                                        <input type="text" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">City</label>
                                        <input type="text" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">State</label>
                                        <input type="text" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Zip Code</label>
                                        <input type="text" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Phone</label>
                                        <input type="text" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="text" class="form-control">
                                    </div>
                                    <div class="mb-0">
                                        <div class="status-toggle modal-status d-flex justify-content-between align-items-center">
                                            <span class="status-label">Status</span>
                                            <input type="checkbox" id="user2" class="check" checked="">
                                            <label for="user2" class="checktoggle"></label>
                                        </div>
                                    </div>
                                    <div class="modal-footer-btn">
                                        <button type="button" class="btn btn-cancel me-2" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-submit">Create</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

