<?php
$title = "index";
include_once('assets/libs/header.php');
?>


        <div class="page-wrapper">
			<div class="content">
            <div class="page-header">
						<div class="add-item d-flex">
							<div class="page-title">
								<h4>Invoice Report	</h4>
								<h6>Manage Your Invoice Report</h6>
							</div>
						</div>
						<ul class="table-top-head">
							<li>
								<a data-bs-toggle="tooltip" data-bs-placement="top" title="Pdf"><img src="https://dreamspos.dreamstechnologies.com/html/template/assets/img/icons/pdf.svg" alt="img"></a>
							</li>
							<li>
								<a data-bs-toggle="tooltip" data-bs-placement="top" title="Excel"><img src="https://dreamspos.dreamstechnologies.com/html/template/assets/img/icons/excel.svg" alt="img"></a>
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
					</div>
					
					<div class="card table-list-card">
						<div class="card-body">
							<div class="table-top">
								<div class="search-set">
									<div class="search-input">
										<a href="#" class="btn btn-searchset"><i data-feather="search" class="feather-search"></i></a>
									</div>
								</div>
								<div class="search-path">
									<div class="d-flex align-items-center">
										<a class="btn btn-filter" id="filter_search">
											<i data-feather="filter" class="filter-icon"></i>
											<span><img src="https://dreamspos.dreamstechnologies.com/html/template/assets/img/icons/closes.svg" alt="img"></span>
										</a>
										
									</div>
									
								</div>
								<div class="form-sort">
									<i data-feather="sliders" class="info-img"></i>
									<select class="select">
										<option>Sort by Date</option>
										<option>25 9 23</option>
										<option>12 9 23</option>
									</select>
								</div>
							</div>
							<!-- /Filter -->
							<div class="card" id="filter_inputs">
								<div class="card-body pb-0">
									<div class="row">
										<div class="col-lg-3 col-sm-6 col-12">
											<div class="input-blocks">
												<i data-feather="user" class="info-img"></i>
												<select class="select">
													<option>Choose Store</option>
													<option>Store name 1</option>
													<option>Store name 2</option>
													<option>Store name 3</option>
													<option>Store name 4</option>
													<option>Store name 5</option>
												</select>
											</div>
										</div>
										<div class="col-lg-3 col-sm-6 col-12">
											<div class="input-blocks">
												<i data-feather="stop-circle" class="info-img"></i>
												<select class="select">
													<option>Choose Status</option>
													<option>Paid</option>
													<option>Unpaid</option>
													<option>Overdue</option>
												</select>
											</div>
										</div>
										<div class="col-lg-3 col-sm-6 col-12">
											<div class="input-blocks">
												<div class="position-relative daterange-wraper">
													<input type="text" class="form-control" name="datetimes" placeholder="From Date - To Date" >
													<i data-feather="calendar" class="feather-14 info-img"></i>
												</div>
											</div>
										</div>
										<div class="col-lg-3 col-sm-6 col-12">
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
											<th>Invoice No</th>
											<th>Store Name</th>
											<th>Due Date</th>
											<th>Amount</th>
											<th>Paid</th>
											<th>Amount Due</th>
											<th>Status</th>
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
											<td>INV001</td>
											<td>Store name 1</td>
											<td>19 Jan 2023</td>
											<td>$1000</td>
											<td>$1000</td>
											<td>$0.00</td>
											<td><span class="badge badge-linesuccess">Paid</span></td>
                                            <td class="action-table-data">
												<div class="edit-delete-action">
													<a class="me-2 edit-icon  p-2" href="#" data-bs-toggle="modal" data-bs-target="#edit-invoice">
														<i data-feather="eye" class="feather-eye"></i>
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
											<td>INV001</td>
											<td>Store name 1</td>
											<td>19 Jan 2023</td>
											<td>$1000</td>
											<td>$1000</td>
											<td>$0.00</td>
											<td><span class="badge badge-linedanger">Unpaid</span></td>
                                            <td class="action-table-data">
												<div class="edit-delete-action">
													<a class="me-2 edit-icon  p-2" href="#" data-bs-toggle="modal" data-bs-target="#edit-invoice">
														<i data-feather="eye" class="feather-eye"></i>
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
											<td>INV001</td>
											<td>Store name 1</td>
											<td>19 Jan 2023</td>
											<td>$1000</td>
											<td>$1000</td>
											<td>$0.00</td>
											<td><span class="badge badge-linedanger">Unpaid</span></td>
                                            <td class="action-table-data">
												<div class="edit-delete-action">
													<a class="me-2 edit-icon  p-2" href="#" data-bs-toggle="modal" data-bs-target="#edit-invoice">
														<i data-feather="eye" class="feather-eye"></i>
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
											<td>INV001</td>
											<td>Store name 1</td>
											<td>19 Jan 2023</td>
											<td>$1000</td>
											<td>$1000</td>
											<td>$0.00</td>
											<td><span class="badge badge-linedanger">Unpaid</span></td>
                                            <td class="action-table-data">
												<div class="edit-delete-action">
													<a class="me-2 edit-icon  p-2" href="#" data-bs-toggle="modal" data-bs-target="#edit-invoice">
														<i data-feather="eye" class="feather-eye"></i>
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
		</div>

        <div class="modal fade" id="edit-invoice">
			<div class="modal-dialog modal-dialog-centered stock-adjust-modal">
				<div class="modal-content">
					<div class="page-wrapper-new p-0">
						<div class="content">
							<div class="modal-header border-0 custom-modal-header">
								<div class="page-title">
									<h4>View Invoice</h4>
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
                                                <input type="text" class="form-control" value="Store name 1" disabled>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-6 col-12">
                                            <div class="mb-3 add-product">
                                                <label class="form-label">Invoice No.x</label>
                                                <input type="text" class="form-control" value="P_1234" disabled>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-6 col-12">
                                            <div class="mb-3 add-product">
                                                <label class="form-label">Due Date</label>
                                                <input type="text" class="form-control" value="26-12-2024" disabled>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-6 col-12">
                                            <div class="mb-3 add-product">
                                                <label>Amount</label>
                                                <input type="text" class="form-control list" value="$1200" disabled>
                                            </div>
                                        </div>
                                        
                                        <div class="col-lg-6 col-sm-6 col-12">
                                            <div class="mb-3 add-product">
                                                <label class="form-label">Amount paid</label>
                                                <input type="text" class="form-control list"  value="$1100" disabled>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-6 col-12">
                                            <div class="mb-3 add-product">
                                                <label class="form-label">Amount Due</label>
                                                <input type="text" class="form-control list"  value="$120" disabled>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-6 col-12">
                                            <div class="mb-3 add-product">
                                                <label class="form-label">Status</label>
                                                <input type="text" class="form-control list"  value="Paid" disabled>
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