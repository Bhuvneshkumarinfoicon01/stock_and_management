@extends('layouts.app_admin')

@section('title')
Dashbaord
@endsection

@section('styles')

@endsection

@section('content')




<div class="page-wrapper">
	<div class="content">
		<div class="row">
			<div class="col-xl-3 col-sm-6 col-12 d-flex">
				<div class="dash-widget dash3 w-100">
					<div class="dash-widgetimg">
						<span><img src="assets/img/icons/dash4.svg" alt="img"></span>
					</div>
					<div class="dash-widgetcontent">
						<h5>$<span class="counters" data-count="40000.00">$40000.00</span></h5>
						<h6>Total Sales</h6>
					</div>
				</div>
			</div>

			<div class="col-xl-3 col-sm-6 col-12 d-flex">
				<div class="dash-widget dash1 w-100">
					<div class="dash-widgetimg">
						<span><img src="assets/img/icons/dash2.svg" alt="img"></span>
					</div>
					<div class="dash-widgetcontent">
						<h5>$<span class="counters" data-count="4385.00">$4,385.00</span></h5>
						<h6>Total revenue</h6>
					</div>
				</div>
			</div>
			<div class="col-xl-3 col-sm-6 col-12 d-flex">
				<div class="dash-widget w-100">
					<div class="dash-widgetimg">
						<span><img src="assets/img/icons/dash1.svg" alt="img"></span>
					</div>
					<div class="dash-widgetcontent">
						<h5><span class="counters" data-count="3071">3071</span></h5>
						<h6>Total number of stores</h6>
					</div>
				</div>
			</div>
			<div class="col-xl-3 col-sm-6 col-12 d-flex">
				<div class="dash-widget dash2 w-100">
					<div class="dash-widgetimg">
						<span><img src="assets/img/icons/dash3.svg" alt="img"></span>
					</div>
					<div class="dash-widgetcontent">
						<h5><span class="counters" data-count="385">385</span></h5>
						<h6>Low stock</h6>
					</div>
				</div>
			</div>

		</div>

		<div class="row">
			<div class="col-sm-12 col-md-12 col-xl-6 d-flex">
				<div class="card flex-fill">
					<div class="card-header d-flex justify-content-between align-items-center">
						<h5 class="card-title mb-0">Profit & Loss summary</h5>
					</div>
					<div class="card-body">
						<div id="s-col" class="chart-set"></div>
					</div>
				</div>
			</div>
			<div class="col-xl-6 col-sm-12 col-12 d-flex">
				<div class="card flex-fill default-cover">
					<div class="card-header d-flex justify-content-between align-items-center">
						<h5 class="card-title mb-0">Sales trends</h5>
						<div class="graph-sets">
							<div class="dropdown dropdown-wraper">
								<button class="btn btn-white btn-sm dropdown-toggle d-flex align-items-center"
									type="button" id="dropdown-sales" data-bs-toggle="dropdown"
									aria-expanded="false"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
										viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
										stroke-linecap="round" stroke-linejoin="round"
										class="feather feather-calendar feather-14">
										<rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
										<line x1="16" y1="2" x2="16" y2="6"></line>
										<line x1="8" y1="2" x2="8" y2="6"></line>
										<line x1="3" y1="10" x2="21" y2="10"></line>
									</svg>2023</button>
								<ul class="dropdown-menu" aria-labelledby="dropdown-sales">
									<li>
										<a href="javascript:void(0);" class="dropdown-item">2023</a>
									</li>
									<li>
										<a href="javascript:void(0);" class="dropdown-item">2022</a>
									</li>
									<li>
										<a href="javascript:void(0);" class="dropdown-item">2021</a>
									</li>
								</ul>
							</div>
						</div>
					</div>
					<div class="card-body">
						<div id="sales-analysis" class="chart-set"></div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12 col-md-12 col-xl-4 d-flex">
				<div class="card flex-fill default-cover w-100 mb-4">
					<div class="card-header d-flex justify-content-between align-items-center">
						<h4 class="card-title mb-0">Top-5 performing stores</h4>
						<div class="dropdown">
							<!-- <a href="javascript:void(0);" class="view-all d-flex align-items-center">
								View All<span class="ps-2 d-flex align-items-center"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right feather-16"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg></span>
							</a> -->
						</div>
					</div>
					<div class="card-body">
						<div class="table-responsive">
							<table class="table table-borderless recent-transactions">
								<thead>
									<tr>
										<th>#</th>
										<th>Store Name</th>
										<th>Sales(amt)</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>1</td>
										<td>
											<div class="store-info">
												<a href="#" class="store-img">
													<img src="assets/img/store/store-01.png" alt="store">
												</a>
												<div class="info"><a href="#">Store Name 1</a></div>
											</div>
										</td>
										<td>$1,099.00</td>
									</tr>

									<tr>
										<td>2</td>
										<td>
											<div class="store-info">
												<a href="#" class="store-img">
													<img src="assets/img/store/store-02.png" alt="store">
												</a>
												<div class="info"><a href="#">Store Name 2</a></div>
											</div>
										</td>
										<td>$1,099.00</td>
									</tr>

									<tr>
										<td>3</td>
										<td>
											<div class="store-info">
												<a href="#" class="store-img">
													<img src="assets/img/store/store-03.png" alt="store">
												</a>
												<div class="info"><a href="#">Store Name 3</a></div>
											</div>
										</td>
										<td>$1,099.00</td>
									</tr>

									<tr>
										<td>4</td>
										<td>
											<div class="store-info">
												<a href="#" class="store-img">
													<img src="assets/img/store/store-04.png" alt="store">
												</a>
												<div class="info"><a href="#">Store Name 4</a></div>
											</div>
										</td>
										<td>$1,099.00</td>
									</tr>

								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-12 col-lg-4 col-sm-12 col-12">
				<div class="card flex-fill default-cover w-100 mb-4">
					<div class="card-header d-flex justify-content-between align-items-center">
						<h4 class="card-title mb-0">High-demand Products</h4>
						<div class="dropdown">
							<a href="javascript:void(0);" class="view-all d-flex align-items-center">
								View All<span class="ps-2 d-flex align-items-center"><svg
										xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
										fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
										stroke-linejoin="round" class="feather feather-arrow-right feather-16">
										<line x1="5" y1="12" x2="19" y2="12"></line>
										<polyline points="12 5 19 12 12 19"></polyline>
									</svg></span>
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
			<div class="col-md-12 col-lg-4 col-sm-12 col-12">
				<div class="card flex-fill default-cover w-100 mb-4">
					<div class="card-header d-flex justify-content-between align-items-center">
						<h4 class="card-title mb-0">Low-stock Products</h4>
						<div class="dropdown">
							<a href="javascript:void(0);" class="view-all d-flex align-items-center">
								View All<span class="ps-2 d-flex align-items-center"><svg
										xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
										fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
										stroke-linejoin="round" class="feather feather-arrow-right feather-16">
										<line x1="5" y1="12" x2="19" y2="12"></line>
										<polyline points="12 5 19 12 12 19"></polyline>
									</svg></span>
							</a>
						</div>
					</div>
					<div class="card-body">
						<div class="table-responsive">
							<table class="table table-borderless best-seller low-stock">
								<tbody>
									<tr>
										<td>
											<div class="product-info">
												<a href="#" class="product-img">
													<img src="assets/img/products/stock-img-01.png" alt="product">
												</a>
												<div class="info">
													<a href="#">Lenovo 3rd Generation</a>
												</div>
											</div>
										</td>
										<td>
											<p class="head-text">
												<i class="fa-solid fa-arrow-trend-down"></i>11
												<span>units</span>
											</p>
										</td>
									</tr>
									<tr>
										<td>
											<div class="product-info">
												<a href="#" class="product-img">
													<img src="assets/img/products/stock-img-01.png" alt="product">
												</a>
												<div class="info">
													<a href="#">Lenovo 3rd Generation</a>
												</div>
											</div>
										</td>
										<td>
											<p class="head-text">
												<i class="fa-solid fa-arrow-trend-down"></i>09
												<span>units</span>
											</p>
										</td>
									</tr>
									<tr>
										<td>
											<div class="product-info">
												<a href="#" class="product-img">
													<img src="assets/img/products/stock-img-01.png" alt="product">
												</a>
												<div class="info">
													<a href="#">Lenovo 3rd Generation</a>
												</div>
											</div>
										</td>
										<td>
											<p class="head-text">
												<i class="fa-solid fa-arrow-trend-down"></i>60
												<span>units</span>
											</p>
										</td>
									</tr>
									<tr>
										<td>
											<div class="product-info">
												<a href="#" class="product-img">
													<img src="assets/img/products/stock-img-01.png" alt="product">
												</a>
												<div class="info">
													<a href="#">Lenovo 3rd Generation</a>
												</div>
											</div>
										</td>
										<td>
											<p class="head-text">
												<i class="fa-solid fa-arrow-trend-down"></i>10
												<span>units</span>
											</p>
										</td>
									</tr>
									<tr>
										<td>
											<div class="product-info">
												<a href="#" class="product-img">
													<img src="assets/img/products/stock-img-01.png" alt="product">
												</a>
												<div class="info">
													<a href="#">Lenovo 3rd Generation</a>
												</div>
											</div>
										</td>
										<td>
											<p class="head-text">
												<i class="fa-solid fa-arrow-trend-down"></i>01
												<span>units</span>
											</p>
										</td>
									</tr>


								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-xl-12 col-lg-12">
				<div class="card flex-fill">
					<div class="card-header d-flex justify-content-between align-items-center">
						<h5 class="card-title mb-0">Total revenue</h5>

						<div class="search-path profit-head ">
							<div class="input-blocks mb-0">
								<i data-feather="calendar" class="info-img"></i>
								<div class="input-groupicon">
									<input type="text" class="datetimepicker" placeholder="This Financial Year">
								</div>
							</div>
						</div>
					</div>
					<div class="card-body">
						<div id="s-line" class="chart-set"></div>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>


@endsection

@section('scripts')
@if(session('success'))
<script>
	document.addEventListener("DOMContentLoaded", function () {
		Swal.fire({
			title: "Success!",
			text: "{{ session('success') }}",
			icon: "success",
			confirmButtonText: "OK"
		});
	});
</script>
@endif
<script>

</script>

@endsection