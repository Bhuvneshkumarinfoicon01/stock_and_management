<?php
$title = "index";
include_once('assets/libs/header.php');
?>



    <div class="page-wrapper">  
		<div class="content">
            <div class="page-header">
                <div class="page-title me-auto">
                    <h4>Product Performance Report</h4>
                </div>
            </div>
			

			<div class="row">
				<div class="col-md-12 col-lg-6 col-sm-12 col-12">
					<div class="card flex-fill default-cover w-100 mb-4">
						<div class="card-header d-flex justify-content-between align-items-center">
							<h4 class="card-title mb-0">Top Performing Products</h4>
							<div class="dropdown">
								<a href="javascript:void(0);" class="view-all d-flex align-items-center">
									View All<span class="ps-2 d-flex align-items-center"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right feather-16"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg></span>
								</a>
							</div>
						</div>
						<div class="card-body">
							<div class="table-responsive">
								<table class="table table-borderless best-seller">
									<tbody>
										<tr>
											<td>
												<div class="product-info">
													<a href="#" class="product-img">
														<img src="assets/img/products/stock-img-01.png" alt="product">
													</a>
													<div class="info">
														<a href="#">Lenovo 3rd Generation</a>
														<p class="dull-text">$4420</p>
													</div>
												</div>
											</td>
											<td>
												<p class="head-text">Sales</p>
												6547
											</td>
										</tr>
										<tr>
											<td>
												<div class="product-info">
													<a href="#" class="product-img">
														<img src="assets/img/products/stock-img-06.png" alt="product">
													</a>
													<div class="info">
														<a href="#">Bold V3.2</a>
														<p class="dull-text">$1474</p>
													</div>
												</div>
											</td>
											<td>
												<p class="head-text">Sales</p>
												3474
											</td>
										</tr>
										<tr>
											<td>
												<div class="product-info">
													<a href="#" class="product-img">
														<img src="assets/img/products/stock-img-02.png" alt="product">
													</a>
													<div class="info">
														<a href="#">Nike Jordan</a>
														<p class="dull-text">$8784</p>
													</div>
												</div>
											</td>
											<td>
												<p class="head-text">Sales</p>
												1478
											</td>
										</tr>
										<tr>
											<td>
												<div class="product-info">
													<a href="#" class="product-img">
														<img src="assets/img/products/stock-img-03.png" alt="product">
													</a>
													<div class="info">
														<a href="#">Apple Series 5 Watch</a>
														<p class="dull-text">$3240</p>
													</div>
												</div>
											</td>
											<td>
												<p class="head-text">Sales</p>
												987
											</td>
										</tr>
										<tr>
											<td>
												<div class="product-info">
													<a href="#" class="product-img">
														<img src="assets/img/products/stock-img-04.png" alt="product">
													</a>
													<div class="info">
														<a href="#">Amazon Echo Dot</a>
														<p class="dull-text">$597</p>
													</div>
												</div>
											</td>
											<td>
												<p class="head-text">Sales</p>
												784
											</td>
										</tr>
										
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
                <div class="col-xl-6 col-lg-6 col-md-12">
					<div class="card flex-fill">
						<div class="card-header d-flex justify-content-between align-items-center">
							<h5 class="card-title mb-0">Top Performing Products</h5>

							<div class="search-path profit-head ">
								<div class="input-blocks mb-0">
									<i data-feather="calendar" class="info-img"></i>
									<div class="input-groupicon">
										<input type="text" class="datetimepicker" placeholder="This Financial Year" >
									</div>
								</div>
							</div>
						</div>
						<div class="card-body">
							<div id="donut-chart" class="chart-set"></div>
						</div>
					</div>
				</div>
			</div>


            <div class="row">
				<div class="col-md-12 col-lg-6 col-sm-12 col-12">
					<div class="card flex-fill default-cover w-100 mb-4">
						<div class="card-header d-flex justify-content-between align-items-center">
							<h4 class="card-title mb-0">Underperforming Performing Products</h4>
							<div class="dropdown">
								<a href="javascript:void(0);" class="view-all d-flex align-items-center">
									View All<span class="ps-2 d-flex align-items-center"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right feather-16"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg></span>
								</a>
							</div>
						</div>
						<div class="card-body">
							<div class="table-responsive">
								<table class="table table-borderless best-seller">
									<tbody>
										<tr>
											<td>
												<div class="product-info">
													<a href="#" class="product-img">
														<img src="assets/img/products/stock-img-01.png" alt="product">
													</a>
													<div class="info">
														<a href="#">Lenovo 3rd Generation</a>
														<p class="dull-text">$4420</p>
													</div>
												</div>
											</td>
											<td>
												<p class="head-text">Sales</p>
												6547
											</td>
										</tr>
										<tr>
											<td>
												<div class="product-info">
													<a href="#" class="product-img">
														<img src="assets/img/products/stock-img-06.png" alt="product">
													</a>
													<div class="info">
														<a href="#">Bold V3.2</a>
														<p class="dull-text">$1474</p>
													</div>
												</div>
											</td>
											<td>
												<p class="head-text">Sales</p>
												3474
											</td>
										</tr>
										<tr>
											<td>
												<div class="product-info">
													<a href="#" class="product-img">
														<img src="assets/img/products/stock-img-02.png" alt="product">
													</a>
													<div class="info">
														<a href="#">Nike Jordan</a>
														<p class="dull-text">$8784</p>
													</div>
												</div>
											</td>
											<td>
												<p class="head-text">Sales</p>
												1478
											</td>
										</tr>
										<tr>
											<td>
												<div class="product-info">
													<a href="#" class="product-img">
														<img src="assets/img/products/stock-img-03.png" alt="product">
													</a>
													<div class="info">
														<a href="#">Apple Series 5 Watch</a>
														<p class="dull-text">$3240</p>
													</div>
												</div>
											</td>
											<td>
												<p class="head-text">Sales</p>
												987
											</td>
										</tr>
										<tr>
											<td>
												<div class="product-info">
													<a href="#" class="product-img">
														<img src="assets/img/products/stock-img-04.png" alt="product">
													</a>
													<div class="info">
														<a href="#">Amazon Echo Dot</a>
														<p class="dull-text">$597</p>
													</div>
												</div>
											</td>
											<td>
												<p class="head-text">Sales</p>
												784
											</td>
										</tr>
										
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
                <div class="col-xl-6 col-lg-6 col-md-12">
					<div class="card flex-fill">
						<div class="card-header d-flex justify-content-between align-items-center">
							<h5 class="card-title mb-0">Underperforming Performing Products</h5>

							<div class="search-path profit-head ">
								<div class="input-blocks mb-0">
									<i data-feather="calendar" class="info-img"></i>
									<div class="input-groupicon">
										<input type="text" class="datetimepicker" placeholder="This Financial Year" >
									</div>
								</div>
							</div>
						</div>
						<div class="card-body">
							<div id="donut-chart-2" class="chart-set"></div>
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