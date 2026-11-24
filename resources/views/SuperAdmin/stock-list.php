<?php
$title = "index";
include_once('assets/libs/header.php');
?>


<div class="page-wrapper">
				<div class="content">
					<div class="page-header">
						<div class="add-item d-flex">
							<div class="page-title">
								<h4>Product List</h4>
								<h6>Manage your products</h6>
							</div>
						</div>
						<ul class="table-top-head">
							<li>
								<a data-bs-toggle="tooltip" data-bs-placement="top" title="Pdf"><img src="https://dreamspos.dreamstechnologies.com/html/template/assets/img/icons/pdf.svg" alt="img"></a>
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
												
												<div class="col-lg-2 col-sm-6 col-12">
													<div class="input-blocks">
														<i data-feather="stop-circle" class="info-img"></i>
														<select class="select">
															<option>All Unit</option>
															<option>Kg</option>
															<option>Pc</option>
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
											<th>SKU</th>
											<th>Category</th>
											<th>Price</th>
											<th>Unit</th>
											<th>Qty</th>
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
											<td>SKU89 </td>
											<td>Categoryname</td>
											<td>$12500.00</td>
											<td>Pc</td>
											<td>100</td>
											<td class="action-table-data">
												<div class="edit-delete-action">
													<a class="me-2 edit-icon  p-2" href="javascript:void(0);">
														<i data-feather="eye" class="feather-eye"></i>
													</a>
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
														<img src="assets/img/products/stock-img-02.png" alt="product">
													</a>
													<a href="javascript:void(0);">Product name 1</a>
												</div>												
											</td>
											<td>SKU89 </td>
											<td>Categoryname</td>
											<td>$12500.00</td>
											<td>Pc</td>
											<td>100</td>
											<td class="action-table-data">
												<div class="edit-delete-action">
													<a class="me-2 edit-icon  p-2" href="javascript:void(0);">
														<i data-feather="eye" class="feather-eye"></i>
													</a>
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
														<img src="assets/img/products/stock-img-03.png" alt="product">
													</a>
													<a href="javascript:void(0);">Product name 1</a>
												</div>												
											</td>
											<td>SKU89 </td>
											<td>Categoryname</td>
											<td>$12500.00</td>
											<td>Pc</td>
											<td>100</td>
											<td class="action-table-data">
												<div class="edit-delete-action">
													<a class="me-2 edit-icon  p-2" href="javascript:void(0);">
														<i data-feather="eye" class="feather-eye"></i>
													</a>
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
														<img src="assets/img/products/stock-img-04.png" alt="product">
													</a>
													<a href="javascript:void(0);">Product name 1</a>
												</div>												
											</td>
											<td>SKU89 </td>
											<td>Categoryname</td>
											<td>$12500.00</td>
											<td>Pc</td>
											<td>100</td>
											<td class="action-table-data">
												<div class="edit-delete-action">
													<a class="me-2 edit-icon  p-2" href="javascript:void(0);">
														<i data-feather="eye" class="feather-eye"></i>
													</a>
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
														<img src="assets/img/products/stock-img-05.png" alt="product">
													</a>
													<a href="javascript:void(0);">Product name 1</a>
												</div>												
											</td>
											<td>SKU89 </td>
											<td>Categoryname</td>
											<td>$12500.00</td>
											<td>Pc</td>
											<td>100</td>
											<td class="action-table-data">
												<div class="edit-delete-action">
													<a class="me-2 edit-icon  p-2" href="javascript:void(0);">
														<i data-feather="eye" class="feather-eye"></i>
													</a>
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
														<img src="assets/img/products/stock-img-06.png" alt="product">
													</a>
													<a href="javascript:void(0);">Product name 1</a>
												</div>												
											</td>
											<td>SKU89 </td>
											<td>Categoryname</td>
											<td>$12500.00</td>
											<td>Pc</td>
											<td>100</td>
											<td class="action-table-data">
												<div class="edit-delete-action">
													<a class="me-2 edit-icon  p-2" href="javascript:void(0);">
														<i data-feather="eye" class="feather-eye"></i>
													</a>
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
														<img src="assets/img/products/stock-img-07.png" alt="product">
													</a>
													<a href="javascript:void(0);">Product name 1</a>
												</div>												
											</td>
											<td>SKU89 </td>
											<td>Categoryname</td>
											<td>$12500.00</td>
											<td>Pc</td>
											<td>100</td>
											<td class="action-table-data">
												<div class="edit-delete-action">
													<a class="me-2 edit-icon  p-2" href="javascript:void(0);">
														<i data-feather="eye" class="feather-eye"></i>
													</a>
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
														<img src="assets/img/products/stock-img-08.png" alt="product">
													</a>
													<a href="javascript:void(0);">Product name 1</a>
												</div>												
											</td>
											<td>SKU89 </td>
											<td>Categoryname</td>
											<td>$12500.00</td>
											<td>Pc</td>
											<td>100</td>
											<td class="action-table-data">
												<div class="edit-delete-action">
													<a class="me-2 edit-icon  p-2" href="javascript:void(0);">
														<i data-feather="eye" class="feather-eye"></i>
													</a>
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


		<!-- Add Stock Transfer-->
		<div class="modal fade" id="add-units"  aria-modal="true" role="dialog">
			<div class="modal-dialog modal-dialog-centered stock-adjust-modal">
				<div class="modal-content">
					<div class="page-wrapper-new p-0">
						<div class="content">
							<div class="modal-header border-0 custom-modal-header">
								<div class="page-title">
									<h4>Add Stock Transfer</h4>
								</div>
								<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body custom-modal-body">
								<form>
									<div class="row">
										<div class="col-lg-6">
											<div class="input-blocks">
												<label>Store From</label>
												<select class="select">
													<option>Choose</option>
													<option>Storename 1</option>
													<option>Storename 2</option>
													<option>Storename 3</option>
													<option>Storename 4</option>
												</select>
											</div>
										</div>
										<div class="col-lg-6">
											<div class="input-blocks">
												<label>Store To</label>
												<select class="select">
													<option>Choose</option>
													<option>Storename 5</option>
													<option>Storename 6</option>
													<option>Storename 7</option>
													<option>Storename 8</option>
												</select>
											</div>
										</div>
										<div class="col-lg-6">
											<div class="input-blocks">
												<label>Responsible Person From:</label>
												<input type="text" class="form-control">
											</div>
										</div>
										<div class="col-lg-6">
											<div class="input-blocks">
												<label>Responsible Person To:</label>
												<input type="text" class="form-control">
											</div>
										</div>
										<div class="col-lg-12">
											<div class="input-blocks search-form mb-3">
												<label>Product</label>
												<input type="text" class="form-control" placeholder="Select Product">
												<i data-feather="search" class="feather-search"></i>
											</div>
										</div>
										<div class="col-lg-12">
											<div class="modal-body-table">
												<div class="table-responsive">
													<table class="table  datanew">
														<thead>
															<tr>
																<th>Product</th>
																<th>SKU</th>
																<th>Category</th>
																<th>Qty</th>
																<th class="no-sort">Action</th>
																<th>
																	<div class="edit-delete-action">
																		<a class="me-2 p-2 add-row-btn" href="javascript:void(0);">
																			<i data-feather="plus-circle" class="plus-circle"></i>
																		</a>
																	</div>
																</th>
															</tr>
														</thead>
														<tbody>
															<tr>
																<td>
																	<div class="productimgname">
																		<input type="text" class="form-control">
																	</div>												
																</td>
																<td>
																	<div class="input-blocks sku">
																		<!-- <label>SKU</label> -->
																		<input type="text" class="form-control">
																	</div>
																</td>
																<td>
																	<!-- <label>Category</label> -->
																	<select class="select">
																		<option>Choose</option>
																		<option>Category Name 5</option>
																		<option>Category Name 6</option>
																		<option>Category Name 7</option>
																		<option>Category Name 8</option>
																	</select>
																</td>
																<td>
																	<div class="product-quantity">
																		<span class="quantity-btn"><i data-feather="minus-circle" class="feather-search"></i></span>
																		<input type="text" class="quntity-input" value="2">
																		<span class="quantity-btn">+<i data-feather="plus-circle" class="plus-circle"></i></span>
																	</div>
																</td>
																<td class="action-table-data" colspan="2">
																	<div class="edit-delete-action">
																		<a class="confirm-text p-2" href="javascript:void(0);">
																			<i data-feather="trash-2" class="feather-trash-2"></i>
																		</a>
																	</div>
																</td>

																<td></td>
															</tr>
														</tbody>
													</table>
												</div>
											</div>
										</div>
										<div class="col-lg-12">
											<div class="input-blocks search-form mb-0">
												<label>Notes</label>
												<textarea class="form-control"></textarea>
											</div>
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
		<!-- /Add Stock -->

		<!-- Edit Stock -->
		<div class="modal fade" id="edit-units">
			<div class="modal-dialog modal-dialog-centered stock-adjust-modal">
				<div class="modal-content">
					<div class="page-wrapper-new p-0">
						<div class="content">
							<div class="modal-header border-0 custom-modal-header">
								<div class="page-title">
									<h4>Edit Stock</h4>
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
                                                <label class="form-label">Product ID</label>
                                                <input type="text" class="form-control" value="P_1234" disabled>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-6 col-12">
                                            <div class="mb-3 add-product">
                                                <label class="form-label">Product Name</label>
                                                <input type="text" class="form-control" placeholder="Enter Product Name" value="Product name 12">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-6 col-12">
                                            <div class="input-blocks add-product list">
                                                <label>SKU</label>
                                                <input type="text" class="form-control list" placeholder="Enter SKU" value="SKU890">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-6 col-12">
                                            <div class="mb-3 add-product">
                                                <div class="add-newplus">
                                                    <label class="form-label">Category</label>
                                                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#add-units-category"><i data-feather="plus-circle" class="plus-down-add"></i><span>Add
                                                            New</span></a>
                                                </div>
                                                <select class="select">
                                                    <option>Choose</option>
                                                    <option>Category Name 1</option>
                                                    <option selected>Category Name 2</option>
                                                    <option>Category Name 3</option>
                                                    <option>Category Name 4</option>
                                                    <option>Category Name 5</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-6 col-12">
                                            <div class="mb-3 add-product">
                                                <div class="add-newplus">
                                                    <label class="form-label">Unit</label>
                                                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#add-unit"><i data-feather="plus-circle" class="plus-down-add"></i><span>Add New</span></a>
                                                </div>
                                                <select class="select">
                                                    <option>Choose</option>
                                                    <option>Kg</option>
                                                    <option selected>Pc</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-6 col-12">
                                            <div class="mb-3 add-product">
                                                <label class="form-label">Supplier Name</label>
                                                <input type="text" class="form-control list" placeholder="Supplier Name" value="Lorem Ipsum">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-6 col-12">
                                            <div class="input-blocks">
                                                <label>Purchase Date</label>
        
                                                <div class="input-groupicon calender-input">
                                                    <i data-feather="calendar" class="info-img"></i>
                                                    <input type="text" class="datetimepicker" placeholder="Choose Date">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-12">
                                            <div class="input-blocks summer-description-box transfer mb-3">
                                                <label>Description</label>
                                                <textarea class="form-control h-100" rows="5">It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal <</textarea>
                                                <p class="mt-1">Maximum 60 Characters</p>
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

        <!-- Add Category -->
        <div class="modal fade" id="add-units-category">
            <div class="modal-dialog modal-dialog-centered custom-modal-two">
                <div class="modal-content">
                    <div class="page-wrapper-new p-0">
                        <div class="content">
                            <div class="modal-header border-0 custom-modal-header">
                                <div class="page-title">
                                    <h4>Add New Category</h4>
                                </div>
                                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body custom-modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control">
                                </div>
                                <div class="modal-footer-btn">
                                    <a href="javascript:void(0);" class="btn btn-cancel me-2" data-bs-dismiss="modal">Cancel</a>
                                    <a href="javascript:void(0);" class="btn btn-submit">Submit</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Add Category -->


        <!-- Add Unit -->
        <div class="modal fade" id="add-unit">
            <div class="modal-dialog modal-dialog-centered custom-modal-two">
                <div class="modal-content">
                    <div class="page-wrapper-new p-0">
                        <div class="content">
                            <div class="modal-header border-0 custom-modal-header">
                                <div class="page-title">
                                    <h4>Add Unit</h4>
                                </div>
                                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body custom-modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Unit</label>
                                    <input type="text" class="form-control">
                                </div>
                                <div class="modal-footer-btn">
                                    <a href="javascript:void(0);" class="btn btn-cancel me-2" data-bs-dismiss="modal">Cancel</a>
                                    <a href="#" class="btn btn-submit">Submit</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Add Unit -->

       

	
		<?php
$title = "index";
include_once('assets/libs/footer.php');
?>