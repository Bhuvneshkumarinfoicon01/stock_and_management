<?php
$title = "index";
include_once('assets/libs/header.php');
?>


        <div class="page-wrapper">
			<div class="content">
				<div class="page-header">
					<div class="add-item d-flex">
						<div class="page-title">
							<h4>New Product</h4>
							<h6>Create new product</h6>
						</div>
					</div>
					<ul class="table-top-head">
						<li>
							<div class="page-btn">
								<a href="stock-list.php" class="btn btn-secondary"><i data-feather="arrow-left" class="me-2"></i>Back to Product</a>
							</div>
						</li>
						<li>
							<a data-bs-toggle="tooltip" data-bs-placement="top" title="Collapse" id="collapse-header"><i data-feather="chevron-up" class="feather-chevron-up"></i></a>
						</li>
					</ul>
				</div>
				<form>
					<div class="card">
						<div class="card-body add-product pb-0">
							<div class="accordion-card-one accordion" id="accordionExample">
								<div class="accordion-item">
									<div class="accordion-header" id="headingOne">
										<div class="accordion-button" data-bs-toggle="collapse" data-bs-target="#collapseOne"  aria-controls="collapseOne">
											<div class="addproduct-icon">
												<h5><i data-feather="info" class="add-info"></i><span>Product Information</span></h5>
												<a href="javascript:void(0);"><i data-feather="chevron-down" class="chevron-down-add"></i></a>
											</div>
										</div>
									</div>
									<div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <div class="row">
                                                <div class="col-lg-3 col-sm-6 col-12">
                                                    <div class="mb-3 add-product">
                                                        <label class="form-label">Store</label>
                                                        <select class="select">
                                                            <option>Choose</option>
                                                            <option>Storename 1</option>
                                                            <option>Storename 2</option>
                                                            <option>Storename 3</option>
                                                            <option>Storename 4</option>
                                                            <option>Storename 5</option>
                                                            <option>Storename 6</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-sm-6 col-12">
                                                    <div class="mb-3 add-product">
                                                        <label class="form-label">Product ID</label>
                                                        <input type="text" class="form-control" value="P_1234" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-sm-6 col-12">
                                                    <div class="mb-3 add-product">
                                                        <label class="form-label">Product Name</label>
                                                        <input type="text" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-sm-6 col-12">
                                                    <div class="input-blocks add-product list">
                                                        <label>SKU</label>
                                                        <input type="text" class="form-control list" placeholder="Enter SKU">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="addservice-info">
                                                <div class="row">
                                                    <div class="col-lg-3 col-sm-6 col-12">
                                                        <div class="mb-3 add-product">
                                                            <div class="add-newplus">
                                                                <label class="form-label">Category</label>
                                                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#add-units-category"><i data-feather="plus-circle" class="plus-down-add"></i><span>Add
                                                                        New</span></a>
                                                            </div>
                                                            <select class="select">
                                                                <option>Choose</option>
                                                                <option>Category Name 1</option>
                                                                <option>Category Name 2</option>
                                                                <option>Category Name 3</option>
                                                                <option>Category Name 4</option>
                                                                <option>Category Name 5</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3 col-sm-6 col-12">
                                                        <div class="mb-3 add-product">
                                                            <div class="add-newplus">
                                                                <label class="form-label">Unit</label>
                                                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#add-unit"><i data-feather="plus-circle" class="plus-down-add"></i><span>Add New</span></a>
                                                            </div>
                                                            <select class="select">
                                                                <option>Choose</option>
                                                                <option>Kg</option>
                                                                <option>Pc</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3 col-sm-6 col-12">
                                                        <div class="mb-3 add-product">
                                                            <label class="form-label">Supplier Name</label>
                                                            <input type="text" class="form-control list" placeholder="Supplier Name">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3 col-sm-6 col-12">
                                                        <div class="input-blocks">
                                                            <label>Purchase Date</label>
                    
                                                            <div class="input-groupicon calender-input">
                                                                <i data-feather="calendar" class="info-img"></i>
                                                                <input type="text" class="datetimepicker" placeholder="Choose Date">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                </div>
                                            </div>

                                            <div class="col-lg-12">
                                                <div class="input-blocks summer-description-box transfer mb-3">
                                                    <label>Description</label>
                                                    <textarea class="form-control h-100" rows="5"></textarea>
                                                    <p class="mt-1">Maximum 60 Characters</p>
                                                </div>
                                            </div>
                                        </div>
									</div>
								</div>
							</div>
							<div class="accordion-card-one accordion" id="accordionExample2">
								<div class="accordion-item">
									<div class="accordion-header" id="headingTwo">
                                        <div class="accordion-button"  data-bs-toggle="collapse" data-bs-target="#collapseTwo"  aria-controls="collapseTwo">
                                            <div class="text-editor add-list">
                                                <div class="addproduct-icon list icon">
                                                    <h5><i data-feather="life-buoy" class="add-info"></i><span>Pricing & Stocks</span></h5>
                                                    <a href="javascript:void(0);"><i data-feather="chevron-down" class="chevron-down-add"></i></a>
                                                </div>
                                            </div>
                                        </div>
									</div>
									<div id="collapseTwo" class="accordion-collapse collapse show" aria-labelledby="headingTwo" data-bs-parent="#accordionExample2">
										<div class="accordion-body">
											<div class="input-blocks add-products">
												<label class="d-block">Product Type</label>
												<div class="single-pill-product">
                                                    <div class="row">
														<div class="col-lg-4 col-sm-6 col-12">
															<div class="input-blocks add-product">
																<label>Quantity</label>
																<input type="text" class="form-control">
															</div>
														</div>
														<div class="col-lg-4 col-sm-6 col-12">
															<div class="input-blocks add-product">
																<label>Price</label>
																<input type="text" class="form-control">
															</div>
														</div>
														
													</div>
													
												</div>
											</div>
											
										</div>
									</div>
								</div>
							</div>
                            <div class="accordion-card-one accordion" id="accordionExample3">
                                <div class="accordion-item">
                                    <div class="accordion-header" id="headingThree">
                                        <div class="accordion-button"  data-bs-toggle="collapse" data-bs-target="#collapseThree"  aria-controls="collapseThree">
                                            <div class="addproduct-icon list">
                                                <h5><i data-feather="image" class="add-info"></i><span>Images</span></h5>
                                                <a href="javascript:void(0);"><i data-feather="chevron-down" class="chevron-down-add"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="collapseThree" class="accordion-collapse collapse show" aria-labelledby="headingThree" data-bs-parent="#accordionExample3">
                                    <div class="accordion-body">
                                        <div class="text-editor add-list add">
                                            <div class="col-lg-12">
                                                <div class="add-choosen">
                                                    <div class="input-blocks">
                                                        <div class="image-upload">
                                                            <input type="file">
                                                            <div class="image-uploads">
                                                                <i data-feather="plus-circle" class="plus-down-add me-0"></i>
                                                                <h4>Add Images</h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="phone-img">
                                                        <img src="assets/img/products/stock-img-04.png" alt="image">
                                                        <a href="javascript:void(0);"><i data-feather="x" class="x-square-add remove-product"></i></a>
                                                    </div>
        
                                                    <div class="phone-img">
                                                        <img src="assets/img/products/stock-img-03.png" alt="image">
                                                        <a href="javascript:void(0);"><i data-feather="x" class="x-square-add remove-product"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
							<div class="accordion-card-one accordion" id="accordionExample4">
								<div class="accordion-item">
									<div class="accordion-header" id="headingFour">
										<div class="accordion-button"  data-bs-toggle="collapse" data-bs-target="#collapseFour"  aria-controls="collapseFour">
										<div class="text-editor add-list">
											<div class="addproduct-icon list">
												<h5><i data-feather="list" class="add-info"></i><span>Custom Fields</span></h5>
												<a href="javascript:void(0);"><i data-feather="chevron-down" class="chevron-down-add"></i></a>
											</div>
										</div>
										</div>
									</div>
									<div id="collapseFour" class="accordion-collapse collapse show" aria-labelledby="headingFour" data-bs-parent="#accordionExample4">
										<div class="accordion-body">
											<div class="text-editor add-list add">
												<div class="row">
													<div class="col-lg-4 col-sm-6 col-12">
														<div class="input-blocks">
															<label>Manufactured Date</label>
					
															<div class="input-groupicon calender-input">
																<i data-feather="calendar" class="info-img"></i>
																<input type="text" class="datetimepicker" placeholder="Choose Date">
															</div>
														</div>
													</div>
													<div class="col-lg-4 col-sm-6 col-12">
														<div class="input-blocks">
															<label>Expiry On</label>
					
															<div class="input-groupicon calender-input">
																<i data-feather="calendar" class="info-img"></i>
																<input type="text" class="datetimepicker" placeholder="Choose Date">
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-12">
						<div class="btn-addproduct mb-4">
							<button type="button" class="btn btn-cancel me-2">Cancel</button>
							<button type="submit" class="btn btn-submit">Save Product</button>
						</div>
					</div>
				</form>

			</div>
		</div>

        
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