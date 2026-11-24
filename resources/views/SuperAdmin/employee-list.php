<?php
$title = "index";
include_once('assets/libs/header.php');
?>


        <div class="page-wrapper">
			<div class="content">
         
                <div class="page-header">
                    <div class="add-item d-flex">
                        <div class="page-title">
                            <h4>Employees</h4>
                            <h6>Manage your employees</h6>
                        </div>							
                    </div>
                    <ul class="table-top-head">
                        <li>
                            <a data-bs-toggle="tooltip" data-bs-placement="top" title="Collapse" id="collapse-header"><i data-feather="chevron-up" class="feather-chevron-up"></i></a>
                        </li>
                    </ul>
                    <div class="page-btn">
                        <a href="#" class="btn btn-added"  data-bs-toggle="modal" data-bs-target="#add-employee"><i data-feather="plus-circle" class="me-2"></i>Add New Employee</a>
                    </div>
                </div>
        
                <div class="card table-list-card">
                    <div class="card-body pb-0">
                        <div class="table-top table-top-two table-top-new">
                            
                            <div class="search-set mb-0">
                                <div class="total-employees">
                                    <h6><i data-feather="users" class="feather-user"></i>Total Employees <span>21</span></h6>
                                </div>
                                <div class="search-input">
                                    <a href="#" class="btn btn-searchset"><i data-feather="search" class="feather-search"></i></a>
                                </div>
                                
                            </div>
                            <div class="search-path d-flex align-items-center search-path-new">
                                <div class="d-flex">
                                    <a class="btn btn-filter" id="filter_search">
                                        <i data-feather="filter" class="filter-icon"></i>
                                        <span><img src="assets/img/icons/closes.svg" alt="img"></span>
                                    </a>
                                </div>
                                <div class="form-sort">
                                    <i data-feather="sliders" class="info-img"></i>
                                    <select class="select">
                                        <option>Sort</option>
                                        <option>Newest</option>
                                        <option>Oldest</option>
                                    </select>
                                </div>
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
                                                <option>Choose Name</option>
                                                <option>Employee Name 1</option>
                                                <option>Employee Name 2</option>
                                                <option>Employee Name 3</option>
                                                <option>Employee Name 4</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6 col-12">
                                        <div class="input-blocks">
                                            <i data-feather="stop-circle" class="info-img"></i>
                                            <select class="select">
                                                <option>Choose Status</option>
                                                <option>Active</option>
                                                <option>Inactive</option>
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
                                        <th>Employee</th>
                                        <th>Employee ID</th>
                                        <th>Email</th>
                                        <th>Phone Number</th>
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
                                        <td>
                                            <div class="userimgname">
                                                <a href="javascript:void(0);" class="product-img">
                                                    <img src="assets/img/user/user-01.webp" alt="product">
                                                </a>
                                                <div>
                                                    <a href="javascript:void(0);">Employee Name</a>
                                                    <span class="emp-team">Employee Designation</span>
                                                </div>
                                                
                                            </div>
                                        </td>
                                        <td>
                                            POS001
                                        </td>
                                        <td>
                                            <a href="#">email.com</a>									
                                        </td>
                                        <td>+1 54554 54788</td>
                                        <td><span class="badge badge-linesuccess">Active</span></td>
                                        <td class="action-table-data">
                                            <div class="edit-delete-action">
                                                <a class="me-2 p-2" href="#" data-bs-toggle="modal" data-bs-target="#employee-view">
                                                    <i data-feather="eye" class="feather-eye"></i>
                                                </a>
                                                <a class="me-2 p-2" href="#" data-bs-toggle="modal" data-bs-target="#employee-edit">
                                                    <i data-feather="edit" class="feather-edit"></i>
                                                </a>
                                                
                                                <a class="p-2 me-2" href="#" data-bs-toggle="modal" data-bs-target="#permission">
                                                    <i data-feather="shield" class="shield"></i>
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
                                        <td>
                                            <div class="userimgname">
                                                <a href="javascript:void(0);" class="product-img">
                                                    <img src="assets/img/user/user-01.webp" alt="product">
                                                </a>
                                                <div>
                                                    <a href="javascript:void(0);">Employee Name</a>
                                                    <span class="emp-team">Employee Designation</span>
                                                </div>
                                                
                                            </div>
                                        </td>
                                        <td>
                                            POS001
                                        </td>
                                        <td>
                                            <a href="#">email.com</a>									
                                        </td>
                                        <td>+1 54554 54788</td>
                                        <td><span class="badge badge-linesuccess">Active</span></td>
                                        <td class="action-table-data">
                                            <div class="edit-delete-action">
                                                <a class="me-2 p-2" href="#" data-bs-toggle="modal" data-bs-target="#employee-view">
                                                    <i data-feather="eye" class="feather-eye"></i>
                                                </a>
                                                <a class="me-2 p-2" href="#" data-bs-toggle="modal" data-bs-target="#employee-edit">
                                                    <i data-feather="edit" class="feather-edit"></i>
                                                </a>
                                                <a class="p-2 me-2" href="#" data-bs-toggle="modal" data-bs-target="#permission">
														<i data-feather="shield" class="shield"></i>
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
                                        <td>
                                            <div class="userimgname">
                                                <a href="javascript:void(0);" class="product-img">
                                                    <img src="assets/img/user/user-01.webp" alt="product">
                                                </a>
                                                <div>
                                                    <a href="javascript:void(0);">Employee Name</a>
                                                    <span class="emp-team">Employee Designation</span>
                                                </div>
                                                
                                            </div>
                                        </td>
                                        <td>
                                            POS001
                                        </td>
                                        <td>
                                            <a href="#">email.com</a>									
                                        </td>
                                        <td>+1 54554 54788</td>
                                        <td><span class="badge badge-linesuccess">Active</span></td>
                                        <td class="action-table-data">
                                            <div class="edit-delete-action">
                                                <a class="me-2 p-2" href="#" data-bs-toggle="modal" data-bs-target="#employee-view">
                                                    <i data-feather="eye" class="feather-eye"></i>
                                                </a>
                                                <a class="me-2 p-2" href="#" data-bs-toggle="modal" data-bs-target="#employee-edit">
                                                    <i data-feather="edit" class="feather-edit"></i>
                                                </a>
                                                
                                                <a class="p-2 me-2" href="#" data-bs-toggle="modal" data-bs-target="#permission">
                                                    <i data-feather="shield" class="shield"></i>
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
                                        <td>
                                            <div class="userimgname">
                                                <a href="javascript:void(0);" class="product-img">
                                                    <img src="assets/img/user/user-01.webp" alt="product">
                                                </a>
                                                <div>
                                                    <a href="javascript:void(0);">Employee Name</a>
                                                    <span class="emp-team">Employee Designation</span>
                                                </div>
                                                
                                            </div>
                                        </td>
                                        <td>
                                            POS001
                                        </td>
                                        <td>
                                            <a href="#">email.com</a>									
                                        </td>
                                        <td>+1 54554 54788</td>
                                        <td><span class="badge badge-linesuccess">Active</span></td>
                                        <td class="action-table-data">
                                            <div class="edit-delete-action">
                                                <a class="me-2 p-2" href="#" data-bs-toggle="modal" data-bs-target="#employee-view">
                                                    <i data-feather="eye" class="feather-eye"></i>
                                                </a>
                                                <a class="me-2 p-2" href="#" data-bs-toggle="modal" data-bs-target="#employee-edit">
                                                    <i data-feather="edit" class="feather-edit"></i>
                                                </a>
                                                
                                                <a class="p-2 me-2" href="#" data-bs-toggle="modal" data-bs-target="#permission">
                                                    <i data-feather="shield" class="shield"></i>
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
                                        <td>
                                            <div class="userimgname">
                                                <a href="javascript:void(0);" class="product-img">
                                                    <img src="assets/img/user/user-01.webp" alt="product">
                                                </a>
                                                <div>
                                                    <a href="javascript:void(0);">Employee Name</a>
                                                    <span class="emp-team">Employee Designation</span>
                                                </div>
                                                
                                            </div>
                                        </td>
                                        <td>
                                            POS001
                                        </td>
                                        <td>
                                            <a href="#">email.com</a>									
                                        </td>
                                        <td>+1 54554 54788</td>
                                        <td><span class="badge badge-linedanger">Inactive</span></td>
                                        <td class="action-table-data">
                                            <div class="edit-delete-action">
                                                <a class="me-2 p-2" href="#" data-bs-toggle="modal" data-bs-target="#employee-view">
                                                    <i data-feather="eye" class="feather-eye"></i>
                                                </a>
                                                <a class="me-2 p-2" href="#" data-bs-toggle="modal" data-bs-target="#employee-edit">
                                                    <i data-feather="edit" class="feather-edit"></i>
                                                </a>
                                                
                                                <a class="p-2 me-2" href="#" data-bs-toggle="modal" data-bs-target="#permission">
                                                    <i data-feather="shield" class="shield"></i>
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


        <!-- employee-edit -->
		<div class="modal fade" id="employee-edit"  aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered stock-adjust-modal">
				<div class="modal-content">
					<div class="page-wrapper-new p-0">
						<div class="content">
							<div class="modal-header border-0 custom-modal-header">
								<div class="page-title">
									<h4>Add New Employee</h4>
								</div>
								<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body custom-modal-body">
								<form>
									<div class="row">
                                        <div class="col-lg-12">
                                            <div class="profile-pic-upload edit-pic new-employee-field">
                                                <div class="profile-pic">
                                                    <span><img src="assets/img/user/user-01.webp" alt=""></span>
                                                </div>
                                                <div class="me-3 mb-0">
                                                    <div class="image-upload mb-0">
                                                        <input type="file">
                                                        <div class="image-uploads">
                                                            <h4>Change Image</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
											<div class="mb-3">
												<label class="form-label">First Name</label>
												<input type="text" class="form-control" value="Mitchum">
											</div>
										</div>
										<div class="col-lg-4 col-md-6">
											<div class="mb-3">
												<label class="form-label">Last Name</label>
												<input type="text" class="form-control" value="Daniel">
											</div>
										</div>
										<div class="col-lg-4 col-md-6">
											<div class="mb-3">
												<label class="form-label">Email</label>
												<input type="email" class="form-control" value="mir34345@example.com">
											</div>
										</div>
										<div class="col-lg-4 col-md-6">
											<div class="mb-3">
												<label class="form-label">Contact Number</label>
												<input type="text" class="form-control" value="+1 54554 54788">
											</div>
										</div>
										<div class="col-lg-4 col-md-6">
											<div class="mb-3">
												<label class="form-label">Emp Code</label>
												<input type="text" class="form-control" value="POS001">
											</div>
										</div>
										<div class="col-lg-4 col-md-6">
											<div class="input-blocks">
												<label class="form-label">Date of Birth</label>
												
												<div class="input-groupicon calender-input">
													<i data-feather="calendar" class="info-img"></i>
													<input type="text" class="datetimepicker form-control" placeholder="Select Date" value="13 Aug 1992">
												</div>
											</div>
										</div>
										<div class="col-lg-4 col-md-6">
											<div class="mb-3">
												<label class="form-label">Gender</label>
												<select class="select">
													<option>Male</option>
													<option>Female</option>
												</select>
											</div>
										</div>
										<div class="col-lg-4 col-md-6">
											<div class="mb-3">
												<label class="form-label">Nationality</label>
												<select class="select">
													<option>United Kingdom</option>
													<option>India</option>
												</select>
											</div>
										</div>
										<div class="col-lg-4 col-md-6">
											<div class="input-blocks">
												<label class="form-label">Joining Date</label>
												<div class="input-groupicon calender-input">
													<i data-feather="calendar" class="info-img"></i>
													<input type="text" class="datetimepicker form-control" placeholder="Select Date" >
												</div>
											</div>
										</div>
                                        <div class="col-lg-4 col-md-6">
											<div class="mb-3">
												<label class="form-label">Designation</label>
												<select class="select">
													<option>Designer</option>
													<option>Developer</option>
													<option>Tester</option>
												</select>
											</div>
										</div>
									</div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="other-info">
                                                <div class="card-title-head">
                                                    <h6><span><i data-feather="info" class="feather-edit"></i></span>Other Information</h6>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-4 col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Emergency No 1</label>
                                                            <input type="text" class="form-control" value="+1 72368 89153">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Emergency No 2</label>
                                                            <input type="text" class="form-control" value="+1 90563 60916">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-md-6">
                                                    </div>
                                                    <div class="col-lg-4 col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Address</label>
                                                            <input type="text" class="form-control" value="9S Quay Street">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Country</label>
                                                            <select class="select">
                                                                <option>United Kingdom</option>
                                                                <option>USA</option>			
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">State</label>
                                                            <input type="text" class="form-control" value="East London">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">City</label>
                                                            <input type="text" class="form-control" value="Leeds">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Zipcode</label>
                                                            <input type="text" class="form-control" value="LS1">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
									<div class="modal-footer-btn">
										<button type="button" class="btn btn-cancel me-2" data-bs-dismiss="modal">Cancel</button>
										<button type="submit" class="btn btn-submit">Save</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

        <!-- employee-view -->
		<div class="modal fade" id="employee-view"  aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered stock-adjust-modal">
				<div class="modal-content">
					<div class="page-wrapper-new p-0">
						<div class="content">
							<div class="modal-header border-0 custom-modal-header">
								<div class="page-title">
									<h4>Add New Employee</h4>
								</div>
								<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body custom-modal-body">
								<form>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="profile-pic-upload edit-pic new-employee-field">
                                                <div class="profile-pic">
                                                    <span><img src="assets/img/user/user-01.webp" alt=""></span>
                                                </div>
                                                <div class="me-3 mb-0">
                                                    <div class="image-upload mb-0">
                                                        <input type="file" disabled>
                                                        <div class="image-uploads">
                                                            <h4>Change Image</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
											<div class="mb-3">
												<label class="form-label">First Name</label>
												<input type="text" disabled class="form-control" value="Mitchum">
											</div>
										</div>
										<div class="col-lg-4 col-md-6">
											<div class="mb-3">
												<label class="form-label">Last Name</label>
												<input type="text" disabled class="form-control" value="Daniel">
											</div>
										</div>
										<div class="col-lg-4 col-md-6">
											<div class="mb-3">
												<label class="form-label">Email</label>
												<input type="email" class="form-control" value="mir34345@example.com">
											</div>
										</div>
										<div class="col-lg-4 col-md-6">
											<div class="mb-3">
												<label class="form-label">Contact Number</label>
												<input type="text" disabled class="form-control" value="+1 54554 54788">
											</div>
										</div>
										<div class="col-lg-4 col-md-6">
											<div class="mb-3">
												<label class="form-label">Emp Code</label>
												<input type="text" disabled class="form-control" value="POS001">
											</div>
										</div>
										<div class="col-lg-4 col-md-6">
											<div class="input-blocks">
												<label class="form-label">Date of Birth</label>
												
												<div class="input-groupicon calender-input">
													<i data-feather="calendar" class="info-img"></i>
													<input type="text" disabled class="datetimepicker form-control" placeholder="Select Date" value="13 Aug 1992">
												</div>
											</div>
										</div>
										<div class="col-lg-4 col-md-6">
											<div class="mb-3">
												<label class="form-label">Gender</label>
												<select class="select">
													<option>Male</option>
													<option>Female</option>
												</select>
											</div>
										</div>
										<div class="col-lg-4 col-md-6">
											<div class="mb-3">
												<label class="form-label">Nationality</label>
												<select class="select">
													<option>United Kingdom</option>
													<option>India</option>
												</select>
											</div>
										</div>
										<div class="col-lg-4 col-md-6">
											<div class="input-blocks">
												<label class="form-label">Joining Date</label>
												<div class="input-groupicon calender-input">
													<i data-feather="calendar" class="info-img"></i>
													<input type="text" disabled class="datetimepicker form-control" placeholder="Select Date" >
												</div>
											</div>
										</div>
                                        <div class="col-lg-4 col-md-6">
											<div class="mb-3">
												<label class="form-label">Designation</label>
												<select class="select">
													<option>Designer</option>
													<option selected diabled>Developer</option>
													<option>Tester</option>
												</select>
											</div>
										</div>
									</div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="other-info">
                                                <div class="card-title-head">
                                                    <h6><span><i data-feather="info" class="feather-edit"></i></span>Other Information</h6>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-6 col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Emergency No 1</label>
                                                            <input type="text" disabled class="form-control" value="+1 72368 89153">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Emergency No 2</label>
                                                            <input type="text" disabled class="form-control" value="+1 90563 60916">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 col-md-12">
                                                        <div class="mb-3">
                                                            <label class="form-label">Address</label>
                                                            <input type="text" disabled class="form-control" value="9S Quay Street">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Country</label>
                                                            <select class="select">
                                                                <option>United Kingdom</option>
                                                                <option>USA</option>			
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">State</label>
                                                            <input type="text" disabled class="form-control" value="East London">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">City</label>
                                                            <input type="text" disabled class="form-control" value="Leeds">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Zipcode</label>
                                                            <input type="text" disabled class="form-control" value="LS1">
                                                        </div>
                                                    </div>
                                                </div>
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

        <!-- add-employee -->
		<div class="modal fade" id="add-employee"  aria-modal="true" role="dialog">
			<div class="modal-dialog modal-dialog-centered stock-adjust-modal">
				<div class="modal-content">
					<div class="page-wrapper-new p-0">
						<div class="content">
							<div class="modal-header border-0 custom-modal-header">
								<div class="page-title">
									<h4>Add New Employee</h4>
								</div>
								<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body custom-modal-body new-employee-field">
								<form>
									<div class="row">
                                        <div class="col-lg-12">
                                            <div class="profile-pic-upload edit-pic">
                                                <div class="profile-pic">
                                                    <span><img src="assets/img/user/user-01.webp" alt=""></span>
                                                </div>
                                                <div class="me-3 mb-0">
                                                    <div class="image-upload mb-0">
                                                        <input type="file">
                                                        <div class="image-uploads">
                                                            <h4>Change Image</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
											<div class="mb-3">
												<label class="form-label">First Name</label>
												<input type="text" class="form-control" placeholder="First Name">
											</div>
										</div>
										<div class="col-lg-4 col-md-6">
											<div class="mb-3">
												<label class="form-label">Last Name</label>
												<input type="text" class="form-control" placeholder="Last Name">
											</div>
										</div>
										<div class="col-lg-4 col-md-6">
											<div class="mb-3">
												<label class="form-label">Email</label>
												<input type="email" class="form-control" placeholder="Email">
											</div>
										</div>
										<div class="col-lg-4 col-md-6">
											<div class="mb-3">
												<label class="form-label">Contact Number</label>
												<input type="text" class="form-control" placeholder="Contact No.">
											</div>
										</div>
										<div class="col-lg-4 col-md-6">
											<div class="mb-3">
												<label class="form-label">Emp Code</label>
												<input type="text" class="form-control" placeholder="Employee Code">
											</div>
										</div>
										<div class="col-lg-4 col-md-6">
											<div class="input-blocks">
												<label class="form-label">Date of Birth</label>
												
												<div class="input-groupicon calender-input">
													<i data-feather="calendar" class="info-img"></i>
													<input type="text" class="datetimepicker form-control" placeholder="Select Date">
												</div>
											</div>
										</div>
										<div class="col-lg-4 col-md-6">
											<div class="mb-3">
												<label class="form-label">Gender</label>
												<select class="select">
													<option>Male</option>
													<option>Female</option>
												</select>
											</div>
										</div>
										<div class="col-lg-4 col-md-6">
											<div class="mb-3">
												<label class="form-label">Nationality</label>
												<select class="select">
													<option>United Kingdom</option>
													<option>India</option>
												</select>
											</div>
										</div>
                                        <div class="col-lg-4 col-md-6">
											<div class="mb-3">
												<label class="form-label">Designation</label>
												<select class="select">
													<option>Designer</option>
													<option selected diabled>Developer</option>
													<option>Tester</option>
												</select>
											</div>
										</div>
										<div class="col-lg-4 col-md-6">
											<div class="input-blocks">
												<label class="form-label">Joining Date</label>
												<div class="input-groupicon calender-input">
													<i data-feather="calendar" class="info-img"></i>
													<input type="text" class="datetimepicker form-control" placeholder="Select Date" >
												</div>
											</div>
										</div>
									</div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="other-info">
                                                <div class="card-title-head">
                                                    <h6><span><i data-feather="info" class="feather-edit"></i></span>Other Information</h6>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-6 col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Emergency No 1</label>
                                                            <input type="text" class="form-control" placeholder="Emergency No 1">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Emergency No 2</label>
                                                            <input type="text" class="form-control"  placeholder="Emergency No 2">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 col-md-12">
                                                        <div class="mb-3">
                                                            <label class="form-label">Address</label>
                                                            <input type="text" class="form-control"  placeholder="Address">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Country</label>
                                                            <select class="select">
                                                                <option>United Kingdom</option>
                                                                <option>USA</option>			
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">State</label>
                                                            <input type="text" class="form-control" placeholder="State">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">City</label>
                                                            <input type="text" class="form-control" placeholder="City">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Zipcode</label>
                                                            <input type="text" class="form-control" placeholder="Zipcode">
                                                        </div>
                                                    </div>
                                                </div>
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


         <!-- permission-to-employee -->
		<div class="modal fade" id="permission"  aria-modal="true" role="dialog">
			<div class="modal-dialog modal-dialog-centered stock-adjust-modal">
				<div class="modal-content">
					<div class="page-wrapper-new p-0">
						<div class="content">
							<div class="modal-header border-0 custom-modal-header">
								<div class="page-title">
									<h4>Permission</h4>
								</div>
								<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body custom-modal-body new-employee-field">
								<form>
                                <div class="table-responsive">
								<table class="table  datanew">
									<thead>
										<tr>
											<th>Inventory</th>
											<th>Product</th>
											<th>Store</th>
											<th>Settings</th>
											<th class="no-sort">Allow all</th>
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
                                            <td>
												<label class="checkboxs">
													<input type="checkbox">
													<span class="checkmarks"></span>
												</label>
											</td>
                                            <td>
												<label class="checkboxs">
													<input type="checkbox">
													<span class="checkmarks"></span>
												</label>
											</td>
                                            <td>
												<label class="checkboxs">
													<input type="checkbox">
													<span class="checkmarks"></span>
												</label>
											</td>
                                            <td>
												<label class="checkboxs">
													<input type="checkbox">
													<span class="checkmarks"></span>
												</label>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
									<div class="modal-footer-btn">
										<button type="button" class="btn btn-cancel me-2" data-bs-dismiss="modal">Cancel</button>
										<button type="submit" class="btn btn-submit">Save</button>
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