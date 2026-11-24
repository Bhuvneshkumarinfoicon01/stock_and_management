@extends('layouts.app_admin')

@section('title')
Dashbaord
@endsection

@section('styles')

@endsection

@section('content')



			<div class="page-wrapper">
				<div class="content">
					<div class="page-header">
						<div class="page-title me-auto">
							<h4>Inventory Dashboard</h4>
						</div>
						<ul class="table-top-head low-stock-top-head">
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
						<div class="page-btn">
							<a href="add-stock.php" class="btn btn-added"><i data-feather="plus-circle" class="me-2"></i>Add New Stock</a>
						</div>
						<div class="page-btn">
							<a href="stock-transfer.php" class="btn btn-added"><i data-feather="plus-circle" class="me-2"></i>Stock Transfer</a>
						</div>
					</div>
					<div class="table-tab">
						<ul class="nav nav-pills" id="pills-tab" role="tablist">
							<li class="nav-item" role="presentation">
							  <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Low Stocks</button>
							</li>
							<li class="nav-item" role="presentation">
							  <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Out of Stocks</button>
							</li>
							
						</ul>
						<div class="tab-content" id="pills-tabContent">
							<div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
								<div class="card table-list-card">
									<div class="card-body">
										<div class="table-top">
											<div class="search-set">
												<div class="search-input">
													<a href="#" class="btn btn-searchset"><i data-feather="search" class="feather-search"></i></a>
												</div>
											</div>
											<div class="search-path">
												<a class="btn btn-filter" id="filter_search">
													<i data-feather="filter" class="filter-icon"></i>
													<span><img src="assets/img/icons/closes.svg" alt="img"></span>
												</a>
											</div>
											<div class="form-sort">
												<i data-feather="sliders" class="info-img"></i>
												<select class="select">
													<option>Sort by</option>
													<option>Newest</option>
													<option>Oldest</option>
												</select>
											</div>
										</div>
										<!-- /Filter -->
										<div class="card" id="filter_inputs">
											<div class="card-body pb-0">
												<div class="row">
													<div class="col-lg-3 col-sm-6 col-12">
														<div class="input-blocks">
															<i data-feather="box" class="info-img"></i>
															<select class="select">
																<option>Choose Product</option>
																<option>productname 1</option>
																<option>productname 2</option>
																<option>productname 3</option>
																<option>productname 4</option>
																<option>productname 5</option>
															</select>
														</div>
													</div>
													<div class="col-lg-3 col-sm-6 col-12">
														<div class="input-blocks">
															<i data-feather="zap" class="info-img"></i>
															<select class="select">
																<option>Choose Category</option>
																<option>Categoryname 1</option>
																<option>Categoryname 2</option>
																<option>Categoryname 3</option>
																<option>Categoryname 4</option>
																<option>Categoryname 5</option>
															</select>
														</div>
													</div>
													<div class="col-lg-3 col-sm-6 col-12">
														<div class="input-blocks">
															<i data-feather="archive" class="info-img"></i>
															<select class="select">
																<option>Choose Store</option>
																<option>Storeaname 1</option>
																<option>Storeaname 2</option>
																<option>Storeaname 3</option>
																<option>Storeaname 4</option>
																<option>Storeaname 5</option>
															</select>
														</div>
													</div>
													<div class="col-lg-3 col-sm-6 col-12 ms-auto">
														<div class="input-blocks">
															<a class="btn btn-filters ms-auto"> <i data-feather="search" class="feather-search"></i> Search </a>
														</div>
													</div>
												</div>
											</div>
										</div>
										<!-- /Filter -->
										<div class="table-responsive">
											<table class="table  datanew">
												<thead>
													<tr>
														<th class="no-sort">
															<label class="checkboxs">
																<input type="checkbox" id="select-all">
																<span class="checkmarks"></span>
															</label>
														</th>
														<th>Store</th>
														<th>Product</th>
														<th>Category</th>
														<th>SKU</th>
														<th>Qty</th>
														<th>Qty Alert</th>
														<th class="no-sort">Action</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td>
															<label class="checkboxs">
																<input type="checkbox">
																<span class="checkmarks"></span>
															</label>
														</td>
														<td>Storename 1</td>
														<td>
															<div class="productimgname">
																<a href="javascript:void(0);" class="product-img stock-img">
																	<img src="assets/img/products/stock-img-01.png" alt="product">
																</a>
																<a href="javascript:void(0);">productname 1</a>
															</div>												
														</td>
														<td>Categoryname 1</td>
														<td>SKU90</td>
														<td>15</td>
														<td>10</td>
														<td class="action-table-data">
															<div class="edit-delete-action">
																<a class="me-2 p-2" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#edit-stock">
																	<i data-feather="edit" class="feather-edit"></i>
																</a>
																<a class="confirm-text p-2" href="javascript:void(0);">
																	<i data-feather="trash-2" class="feather-trash-2"></i>
																</a>
															</div>
														</td>
													</tr>
													<tr>
														<td>
															<label class="checkboxs">
																<input type="checkbox">
																<span class="checkmarks"></span>
															</label>
														</td>
														<td>Storename 1</td>
														<td>
															<div class="productimgname">
																<a href="javascript:void(0);" class="product-img stock-img">
																	<img src="assets/img/products/stock-img-02.png" alt="product">
																</a>
																<a href="javascript:void(0);">productname 1</a>
															</div>												
														</td>
														<td>Categoryname 1</td>
														<td>SKU90</td>
														<td>15</td>
														<td>10</td>
														<td class="action-table-data">
															<div class="edit-delete-action">
																<a class="me-2 p-2" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#edit-stock">
																	<i data-feather="edit" class="feather-edit"></i>
																</a>
																<a class="confirm-text p-2" href="javascript:void(0);">
																	<i data-feather="trash-2" class="feather-trash-2"></i>
																</a>
															</div>
														</td>
													</tr>
													<tr>
														<td>
															<label class="checkboxs">
																<input type="checkbox">
																<span class="checkmarks"></span>
															</label>
														</td>
														<td>Storename 1</td>
														<td>
															<div class="productimgname">
																<a href="javascript:void(0);" class="product-img stock-img">
																	<img src="assets/img/products/stock-img-03.png" alt="product">
																</a>
																<a href="javascript:void(0);">productname 1</a>
															</div>												
														</td>
														<td>Categoryname 1</td>
														<td>SKU90</td>
														<td>15</td>
														<td>10</td>
														<td class="action-table-data">
															<div class="edit-delete-action">
																<a class="me-2 p-2" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#edit-stock">
																	<i data-feather="edit" class="feather-edit"></i>
																</a>
																<a class="confirm-text p-2" href="javascript:void(0);">
																	<i data-feather="trash-2" class="feather-trash-2"></i>
																</a>
															</div>
														</td>
													</tr>
													<tr>
														<td>
															<label class="checkboxs">
																<input type="checkbox">
																<span class="checkmarks"></span>
															</label>
														</td>
														<td>Storename 1</td>
														<td>
															<div class="productimgname">
																<a href="javascript:void(0);" class="product-img stock-img">
																	<img src="assets/img/products/stock-img-01.png" alt="product">
																</a>
																<a href="javascript:void(0);">productname 1</a>
															</div>												
														</td>
														<td>Categoryname 1</td>
														<td>SKU90</td>
														<td>15</td>
														<td>10</td>
														<td class="action-table-data">
															<div class="edit-delete-action">
																<a class="me-2 p-2" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#edit-stock">
																	<i data-feather="edit" class="feather-edit"></i>
																</a>
																<a class="confirm-text p-2" href="javascript:void(0);">
																	<i data-feather="trash-2" class="feather-trash-2"></i>
																</a>
															</div>
														</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
							<div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
								<!-- /product list -->
								<div class="card table-list-card">
									<div class="card-body">
										<div class="table-top">
											<div class="search-set">
												<div class="search-input">
													<a href="#" class="btn btn-searchset"><i data-feather="search" class="feather-search"></i></a>
												</div>
											</div>
											<div class="search-path">
												<a class="btn btn-filter" id="filter_search1">
													<i data-feather="filter" class="filter-icon"></i>
													<span><img src="assets/img/icons/closes.svg" alt="img"></span>
												</a>
											</div>
											<div class="form-sort">
												<i data-feather="sliders" class="info-img"></i>
												<select class="select">
													<option>Sort by Date</option>
													<option>Newest</option>
													<option>Oldest</option>
												</select>
											</div>
										</div>
										<!-- /Filter -->
										<div class="card" id="filter_inputs1">
											<div class="card-body pb-0">
												<div class="row">
													<div class="col-lg-2 col-sm-6 col-12">
														<div class="input-blocks">
															<i class="fas fa-money-bill info-img"></i>
															<select class="select">
																<option>Choose Store</option>
																<option>Storename 1</option>
																<option>Storename 2</option>
																<option>Storename 3</option>
																<option>Storename 4</option>
																<option>Storename 5</option>
															</select>
														</div>
													</div>
													<div class="col-lg-2 col-sm-6 col-12">
														<div class="input-blocks">
															<i data-feather="box" class="info-img"></i>
															<select class="select">
																<option>Choose Product</option>
																<option>Productname 1</option>
																<option>Productname 2</option>
																<option>Productname 3</option>
																<option>Productname 4</option>
																<option>Productname 5</option>
															</select>
														</div>
													</div>
													<div class="col-lg-2 col-sm-6 col-12">
														<div class="input-blocks">
															<i data-feather="stop-circle" class="info-img"></i>
															<select class="select">
																<option>Choose Categroy</option>
																<option>Categoryname 1</option>
																<option>Categoryname 2</option>
																<option>Categoryname 3</option>
																<option>Categoryname 4</option>
																<option>Categoryname 5</option>
															</select>
														</div>
													</div>
													<div class="col-lg-3 col-sm-6 col-12 ms-auto">
														<div class="input-blocks">
															<a class="btn btn-filters ms-auto"> <i data-feather="search" class="feather-search"></i> Search </a>
														</div>
													</div>
												</div>
											</div>
										</div>
										<!-- /Filter -->
										<div class="table-responsive">
											<table class="table  datanew">
												<thead>
													<tr>
														<th class="no-sort">
															<label class="checkboxs">
																<input type="checkbox" id="select-all">
																<span class="checkmarks"></span>
															</label>
														</th>
														<th>Store</th>
														<th>Product</th>
														<th>Category</th>
														<th>SKU</th>
														<th>Qty</th>
														<th>Qty Alert</th>
														<th class="no-sort">Action</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td>
															<label class="checkboxs">
																<input type="checkbox">
																<span class="checkmarks"></span>
															</label>
														</td>
														<td>Storename 1</td>
														<td>
															<div class="productimgname">
																<a href="javascript:void(0);" class="product-img stock-img">
																	<img src="assets/img/products/stock-img-07.png" alt="product">
																</a>
																<a href="javascript:void(0);">productname 1</a>
															</div>												
														</td>
														<td>Categoryname 1</td>
														<td>SKU90</td>
														<td>15</td>
														<td>10</td>
														<td class="action-table-data">
															<div class="edit-delete-action">
																<a class="me-2 p-2" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#edit-stock">
																	<i data-feather="edit" class="feather-edit"></i>
																</a>
																<a class="confirm-text p-2" href="javascript:void(0);">
																	<i data-feather="trash-2" class="feather-trash-2"></i>
																</a>
															</div>
														</td>
													</tr>
													<tr>
														<td>
															<label class="checkboxs">
																<input type="checkbox">
																<span class="checkmarks"></span>
															</label>
														</td>
														<td>Storename 1</td>
														<td>
															<div class="productimgname">
																<a href="javascript:void(0);" class="product-img stock-img">
																	<img src="assets/img/products/stock-img-08.png" alt="product">
																</a>
																<a href="javascript:void(0);">productname 1</a>
															</div>												
														</td>
														<td>Categoryname 1</td>
														<td>SKU90</td>
														<td>15</td>
														<td>10</td>
														<td class="action-table-data">
															<div class="edit-delete-action">
																<a class="me-2 p-2" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#edit-stock">
																	<i data-feather="edit" class="feather-edit"></i>
																</a>
																<a class="confirm-text p-2" href="javascript:void(0);">
																	<i data-feather="trash-2" class="feather-trash-2"></i>
																</a>
															</div>
														</td>
													</tr>
													<tr>
														<td>
															<label class="checkboxs">
																<input type="checkbox">
																<span class="checkmarks"></span>
															</label>
														</td>
														<td>Storename 1</td>
														<td>
															<div class="productimgname">
																<a href="javascript:void(0);" class="product-img stock-img">
																	<img src="assets/img/products/stock-img-06.png" alt="product">
																</a>
																<a href="javascript:void(0);">productname 1</a>
															</div>												
														</td>
														<td>Categoryname 1</td>
														<td>SKU90</td>
														<td>15</td>
														<td>10</td>
														<td class="action-table-data">
															<div class="edit-delete-action">
																<a class="me-2 p-2" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#edit-stock">
																	<i data-feather="edit" class="feather-edit"></i>
																</a>
																<a class="confirm-text p-2" href="javascript:void(0);">
																	<i data-feather="trash-2" class="feather-trash-2"></i>
																</a>
															</div>
														</td>
													</tr>
													<tr>
														<td>
															<label class="checkboxs">
																<input type="checkbox">
																<span class="checkmarks"></span>
															</label>
														</td>
														<td>Storename 1</td>
														<td>
															<div class="productimgname">
																<a href="javascript:void(0);" class="product-img stock-img">
																	<img src="assets/img/products/stock-img-05.png" alt="product">
																</a>
																<a href="javascript:void(0);">productname 1</a>
															</div>												
														</td>
														<td>Categoryname 1</td>
														<td>SKU90</td>
														<td>15</td>
														<td>10</td>
														<td class="action-table-data">
															<div class="edit-delete-action">
																<a class="me-2 p-2" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#edit-stock">
																	<i data-feather="edit" class="feather-edit"></i>
																</a>
																<a class="confirm-text p-2" href="javascript:void(0);">
																	<i data-feather="trash-2" class="feather-trash-2"></i>
																</a>
															</div>
														</td>
													</tr>
													<tr>
														<td>
															<label class="checkboxs">
																<input type="checkbox">
																<span class="checkmarks"></span>
															</label>
														</td>
														<td>Storename 1</td>
														<td>
															<div class="productimgname">
																<a href="javascript:void(0);" class="product-img stock-img">
																	<img src="assets/img/products/stock-img-04.png" alt="product">
																</a>
																<a href="javascript:void(0);">productname 1</a>
															</div>												
														</td>
														<td>Categoryname 1</td>
														<td>SKU90</td>
														<td>15</td>
														<td>10</td>
														<td class="action-table-data">
															<div class="edit-delete-action">
																<a class="me-2 p-2" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#edit-stock">
																	<i data-feather="edit" class="feather-edit"></i>
																</a>
																<a class="confirm-text p-2" href="javascript:void(0);">
																	<i data-feather="trash-2" class="feather-trash-2"></i>
																</a>
															</div>
														</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
								<!-- /product list -->
							</div>
							
						</div>
					</div>


					<div class="row">
						<div class="col-sm-12 col-md-12 col-xl-6 d-flex">
							<div class="card flex-fill">
								<div class="card-header d-flex justify-content-between align-items-center">
									<h5 class="card-title mb-0">Incoming stock</h5>
								</div>
								<div class="card-body">
									<div id="in-stock-level" class="chart-set"></div>
								</div>
							</div>
						</div>
						<div class="col-sm-12 col-md-12 col-xl-6 d-flex">
							<div class="card flex-fill">
								<div class="card-header d-flex justify-content-between align-items-center">
									<h5 class="card-title mb-0">Outgoing stock</h5>
								</div>
								<div class="card-body">
									<div id="out-stock-level" class="chart-set"></div>
								</div>
							</div>
						</div>
						<div class="col-xl-12 col-sm-12 col-12 d-flex">
							<div class="card flex-fill default-cover">
								<div class="card-header d-flex justify-content-between align-items-center">
									<h5 class="card-title mb-0">Current stock levelss</h5>
									<div class="graph-sets">
										<div class="dropdown dropdown-wraper">
											<button class="btn btn-white btn-sm dropdown-toggle d-flex align-items-center" type="button" id="dropdown-sales" data-bs-toggle="dropdown" aria-expanded="false">
												
												Choose Store</button>
											<ul class="dropdown-menu" aria-labelledby="dropdown-sales">
												<li>
													<a href="javascript:void(0);" class="dropdown-item">Storename 1</a>
												</li>
												<li>
													<a href="javascript:void(0);" class="dropdown-item">Storename 2</a>
												</li>
												<li>
													<a href="javascript:void(0);" class="dropdown-item">Storename 3</a>
												</li>												
											</ul>
										</div>
									</div>
								</div>
								<div class="card-body">
									<div id="stock-analysis" class="chart-set"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
       

	
@endsection