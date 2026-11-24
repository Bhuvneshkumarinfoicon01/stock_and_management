<?php
$title = "index";
include_once('assets/libs/header.php');
?>



			<div class="page-wrapper">
				<div class="content">
					<div class="page-header">
						<div class="add-item d-flex">
							<div class="page-title">
								<h4>Sales Analysis</h4>
								<h6>Manage your Profit & Loss</h6>
							</div>
						</div>
						<ul class="table-top-head">
							<!-- <li>
								<a data-bs-toggle="tooltip" data-bs-placement="top" title="Pdf"><img src="assets/img/icons/pdf.svg" alt="img"></a>
							</li>
							<li>
								<a data-bs-toggle="tooltip" data-bs-placement="top" title="Excel"><img src="assets/img/icons/excel.svg" alt="img"></a>
							</li>
							<li>
								<a data-bs-toggle="tooltip" data-bs-placement="top" title="Print"><i data-feather="printer" class="feather-rotate-ccw"></i></a>
							</li> -->
							<li>
								<a data-bs-toggle="tooltip" data-bs-placement="top" title="Refresh" id="refresh-page"><i data-feather="rotate-ccw" class="feather-rotate-ccw"></i></a>
							</li>
							<li>
								<a data-bs-toggle="tooltip" data-bs-placement="top" title="Collapse" id="collapse-header"><i data-feather="chevron-up" class="feather-chevron-up"></i></a>
							</li>
						</ul>
					</div>
					
				



					<div class="row mt-5">
						<div class="col-xl-12 col-sm-12 col-12 d-flex">
							<div class="card flex-fill default-cover">
								<div class="card-header d-flex justify-content-between align-items-center">
									<div class="finance-charts d-flex gap-3">
										<select class="select">
											<option>Choose Store</option>
											<option>Storename1</option>
											<option>Storename2</option>
											<option>Storename3</option>
											<option>Storename4</option>
										</select>

										<select class="select">
											<option>Choose Product</option>
											<option>Productname1</option>
											<option>Productname1</option>
											<option>Productname1</option>
											<option>Productname1</option>
										</select>

										<select class="select">
											<option>Choose Category</option>
											<option>Categoryname1</option>
											<option>Categoryname1</option>
											<option>Categoryname1</option>
											<option>Categoryname1</option>
										</select>
									</div>
								</div>
								<div class="card-body">
									<div id="sales-analysis" class="chart-set"></div>
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