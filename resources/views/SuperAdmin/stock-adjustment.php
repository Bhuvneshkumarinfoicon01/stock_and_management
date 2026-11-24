<?php
$title = "index";
include_once('assets/libs/header.php');
?>


<div class="page-wrapper">
				<div class="content">
					<div class="page-header">
						<div class="add-item d-flex">
							<div class="page-title">
								<h4>Stock Adjustment</h4>
								<h6>Manage your products</h6>
							</div>
						</div>
						<ul class="table-top-head">
							<li>
								<a data-bs-toggle="tooltip" data-bs-placement="top" title="Pdf"><img src="assets/img/icons/pdf.svg" alt="img"></a>
							</li>
							<li>
								<a data-bs-toggle="tooltip" data-bs-placement="top" title="Excel"><img src="assets/img/icons/excel.svg" alt="img"></a>
							</li>
							<li>
								<a data-bs-toggle="tooltip" data-bs-placement="top" title="Print"><i data-feather="printer" class="feather-rotate-ccw"></i></a>
							</li>
							<li>
								<a data-bs-toggle="tooltip" data-bs-placement="top" title="Refresh"><i data-feather="rotate-ccw" class="feather-rotate-ccw"></i></a>
							</li>
							<li>
								<a data-bs-toggle="tooltip" data-bs-placement="top" title="Collapse" id="collapse-header"><i data-feather="chevron-up" class="feather-chevron-up"></i></a>
							</li>
						</ul>
						<div class="page-btn">
							<a href="add-stock.php" class="btn btn-added"><i data-feather="plus-circle" class="me-2"></i>Add New Stock</a>
						</div>	
					</div>
					
					<!-- /product list -->
					<div class="card table-list-card">
						<div class="card-body">
							<div class="table-top">
								<div class="search-set">
									<div class="search-input">
										<a href="javascript:void(0);" class="btn btn-searchset"><i data-feather="search" class="feather-search"></i></a>
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
							<div class="card mb-0" id="filter_inputs">
								<div class="card-body pb-0">
									<div class="row">
										<div class="col-lg-12 col-sm-12">
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
														<a class="btn btn-filters ms-auto"> <i data-feather="search" class="feather-search"></i> Search </a>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<!-- /Filter -->
							<div class="table-responsive product-list">
								<table class="table datanew">
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
											<th>Adjust Qty</th>
											<th>Adjust Reason</th>
											<th>Adjust Date</th>
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
													<a href="javascript:void(0);">Product name 1</a>
												</div>												
											</td>
											<td>12 </td>
											<td>Loss</td>
											<td>23-1-2025</td>
											<td class="action-table-data">
												<div class="edit-delete-action">
													 <a class="me-2 p-2" href="#" data-bs-toggle="modal" data-bs-target="#edit-units">
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
													<a href="javascript:void(0);">Product name 1</a>
												</div>												
											</td>
											<td>12 </td>
											<td>Damage</td>
											<td>23-1-2025</td>
											<td class="action-table-data">
												<div class="edit-delete-action">
													 <a class="me-2 p-2" href="#" data-bs-toggle="modal" data-bs-target="#edit-units">
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
													<a href="javascript:void(0);">Product name 1</a>
												</div>												
											</td>
											<td>12 </td>
											<td>Error Detection</td>
											<td>23-1-2025</td>
											<td class="action-table-data">
												<div class="edit-delete-action">
													 <a class="me-2 p-2" href="#" data-bs-toggle="modal" data-bs-target="#edit-units">
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
													<a href="javascript:void(0);">Product name 1</a>
												</div>												
											</td>
											<td>12 </td>
											<td>Loss</td>
											<td>23-1-2025</td>
											<td class="action-table-data">
												<div class="edit-delete-action">
													 <a class="me-2 p-2" href="#" data-bs-toggle="modal" data-bs-target="#edit-units">
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



		<!-- Edit Stock -->
		<div class="modal fade" id="edit-units">
			<div class="modal-dialog modal-dialog-centered stock-adjust-modal">
				<div class="modal-content">
					<div class="page-wrapper-new p-0">
						<div class="content">
							<div class="modal-header border-0 custom-modal-header">
								<div class="page-title">
									<h4>Edit Stock Adjustment</h4>
								</div>
								<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body custom-modal-body">
								<form>
									<div class="row">
                                        <div class="col-lg-6 col-sm-6 col-12">
                                            <div class="mb-3 add-product">
                                                <label class="form-label">Store</label>
                                                <select class="select">
                                                    <option>Choose</option>
                                                    <option>Storename 1</option>
                                                    <option>Storename 2</option>
                                                    <option>Storename 3</option>
                                                    <option selected>Storename 4</option>
                                                    <option>Storename 5</option>
                                                    <option>Storename 6</option>
                                                </select>
                                            </div>
                                        </div>
										<div class="col-lg-6 col-sm-6 col-12">
                                            <div class="mb-3 add-product">
                                                <label class="form-label">Product</label>
                                                <select class="select">
                                                    <option>Choose</option>
                                                    <option>productname 1</option>
                                                    <option>productname 2</option>
                                                    <option>productname 3</option>
                                                    <option selected>productname 4</option>
                                                    <option>productname 5</option>
                                                    <option>productname 6</option>
                                                </select>
                                            </div>
                                        </div>
										<div class="col-lg-4 col-sm-6 col-12">
											<label  class="form-label">Adjusted Quantity</label>
											<div class="product-quantity">
												<span class="quantity-btn"><i data-feather="minus-circle" class="feather-search"></i></span>
												<input type="text" class="quntity-input" value="2">
												<span class="quantity-btn">+<i data-feather="plus-circle" class="plus-circle"></i></span>
											</div>
										</div>
										<div class="col-lg-4 col-sm-6 col-12">
                                            <div class="mb-3 add-product">
                                                <label class="form-label">Adjustment Reason</label>
                                                <select class="select">
                                                    <option>Choose</option>
                                                    <option>loss</option>
                                                    <option>Damage</option>
                                                    <option selected>Error Correction</option>
                                                </select>
                                            </div>
                                        </div>

										<div class="col-lg-4 col-sm-6 col-12">
											<div class="input-blocks">
												<label>Purchase Date</label>
		
												<div class="input-groupicon calender-input">
													<i data-feather="calendar" class="info-img"></i>
													<input type="text" class="datetimepicker" placeholder="Choose Date">
												</div>
											</div>
										</div>
									</div>
									<div class="modal-footer-btn">
										<button type="button" class="btn btn-cancel me-2" data-bs-dismiss="modal">Cancel</button>
										<button type="submit" class="btn btn-submit">Save Changes</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Edit Stock -->

       

	
		<?php
$title = "index";
include_once('assets/libs/footer.php');
?>