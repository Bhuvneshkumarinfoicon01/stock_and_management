<?php
$title = "index";
include_once('assets/libs/header.php');
?>



<div class="page-wrapper">
	<div class="content settings-content">
		<div class="page-header settings-pg-header">
			<div class="add-item d-flex">
				<div class="page-title">
					<h4>System Configurations</h4>
					<h6>Manage your settings on portal</h6>
				</div>
			</div>
			<ul class="table-top-head">
				<li>
					<a data-bs-toggle="tooltip" data-bs-placement="top" title="Refresh"><i data-feather="rotate-ccw" class="feather-rotate-ccw"></i></a>
				</li>
				<li>
					<a data-bs-toggle="tooltip" data-bs-placement="top" title="Collapse" id="collapse-header"><i data-feather="chevron-up" class="feather-chevron-up"></i></a>
				</li>
			</ul>
		</div>
		<div class="row">
			<div class="col-xl-12">
					<div class="settings-wrapper d-flex">
					<div class="sidebars settings-sidebar theiaStickySidebar" id="sidebar2">
						<div class="sidebar-inner slimscroll">
							<div id="sidebar-menu5" class="sidebar-menu">
								<ul>
									<li class="submenu-open">
										<ul>
											<li class="submenu">
												<a href="setting.php" class="active subdrop"><i data-feather="settings"></i><span>General Settings</span><span class="menu-arrow"></span></a>
												<ul>
													<li ><a href="setting.php" >Reports</a></li>
													<li><a href="notification.php">Notifications</a></li>
													<li><a href="stock-alert.php" >Stock Alerts</a></li>
													<li><a href="preference.php"  class="active">Reorder Preference</a></li>
												</ul>
											</li>
										</ul>								
									</li>
									
								</ul>
							</div>
						</div>
					</div>
					<div class="settings-page-wrap">
						<form>
							<div class="setting-title">
								<h4>Reorder Preference</h4>
							</div>
							
							<div class="row">
								<div class="col-xl-4 col-lg-6 col-md-4 col-sm-6">
									<div class="connected-app-card">
										<ul>
											<li>
												<div class="security-type">
													<div class="security-title"><h5>Enable Reordering</h5></div>
												</div>
												<div class="status-toggle modal-status d-flex justify-content-between align-items-center ms-2">
													<input type="checkbox" id="user1" class="check" checked>
													<label for="user1" class="checktoggle">	</label>
												</div>
											</li>
										</ul>
									</div>
								</div>
                                <div class="col-xl-4 col-lg-6 col-md-4 col-sm-6">
									<div class="connected-app-card">
										<ul>
											<li>
												<div class="security-type">
													<div class="security-title"><h5>Auto-save Changes</h5></div>
												</div>
												<div class="status-toggle modal-status d-flex justify-content-between align-items-center ms-2">
													<input type="checkbox" id="user1" class="check" checked>
													<label for="user1" class="checktoggle">	</label>
												</div>
											</li>
										</ul>
									</div>
								</div>
                                <div class="col-xl-4 col-lg-6 col-md-4 col-sm-6">
									<div class="connected-app-card">
										<ul>
											<li>
												<div class="security-type">
													<div class="security-title"><h5>Enable Reordering</h5></div>
												</div>
												<div class="status-toggle modal-status d-flex justify-content-between align-items-center ms-2">
													<input type="checkbox" id="user1" class="check" checked>
													<label for="user1" class="checktoggle">	</label>
												</div>
											</li>
										</ul>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
				
			</div>
		</div>
	</div>
</div>


       

	
<?php
$title = "index";
include_once('assets/libs/footer.php');
?>