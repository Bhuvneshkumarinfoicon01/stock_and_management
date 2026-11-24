<?php
$title = "index";
include_once('assets/libs/header.php');
?>



			<div class="page-wrapper">
				<div class="content">
					<div class="page-header">
						<div class="add-item d-flex">
							<div class="page-title">
								<h4>Sales Trend Report</h4>
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
					</div>
					
				



					<div class="row mt-5">
                        <div class="col-xl-12 col-sm-12 col-12">
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
                                                    <span><img src="assets/img/icons/closes.svg" alt="img"></span>
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
                                    <div class="card" id="filter_inputs">
                                        <div class="card-body pb-0">
                                            <div class="row">
                                                <div class="col-lg-2">
                                                    <div class="input-blocks">
                                                        <i data-feather="box" class="info-img"></i>
                                                        <select class="select">
                                                            <option>Choose Store</option>
                                                            <option>Store name 1</option>
                                                            <option>Store name 2</option>
                                                            <option>Store name 3</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="input-blocks">
                                                        <i data-feather="zap" class="info-img"></i>
                                                        <select class="select">
                                                            <option>Choose Category</option>
                                                            <option>Category 1</option>
                                                            <option>Category 2</option>
                                                            <option>Category 3</option>
                                                            <option>Category 4</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-sm-6 col-12">
                                                    <div class="input-blocks">
                                                        <i data-feather="calendar" class="info-img"></i>
                                                        <div class="input-groupicon">
                                                            <input type="text" class="datetimepicker" placeholder="Choose Date From" >
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-sm-6 col-12">
                                                    <div class="input-blocks">
                                                        <i data-feather="calendar" class="info-img"></i>
                                                        <div class="input-groupicon">
                                                            <input type="text" class="datetimepicker" placeholder="Choose Date To" >
                                                        </div>
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
                                                    <th>Product Name</th>
                                                    <th>SKU</th>
                                                    <th>Category</th>
                                                    <th>Sold Qty</th>
                                                    <th>Sold Amount</th>
                                                    <th>Instock Qty</th>
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
                                                    <td class="productimgname">
                                                        <a href="javascript:void(0);">Product Name 1</a>
                                                    </td>
                                                    <td>PT001</td>
                                                    <td>Computers</td>
                                                    <td>20</td>
                                                    <td>$1000</td>
                                                    <td>100</td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <label class="checkboxs">
                                                            <input type="checkbox">
                                                            <span class="checkmarks"></span>
                                                        </label>
                                                    </td>
                                                    <td class="productimgname">
                                                        <a href="javascript:void(0);">Product Name 1</a>
                                                    </td>
                                                    <td>PT001</td>
                                                    <td>Computers</td>
                                                    <td>20</td>
                                                    <td>$1000</td>
                                                    <td>100</td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <label class="checkboxs">
                                                            <input type="checkbox">
                                                            <span class="checkmarks"></span>
                                                        </label>
                                                    </td>
                                                    <td class="productimgname">
                                                        <a href="javascript:void(0);">Product Name 1</a>
                                                    </td>
                                                    <td>PT001</td>
                                                    <td>Computers</td>
                                                    <td>20</td>
                                                    <td>$1000</td>
                                                    <td>100</td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <label class="checkboxs">
                                                            <input type="checkbox">
                                                            <span class="checkmarks"></span>
                                                        </label>
                                                    </td>
                                                    <td class="productimgname">
                                                        <a href="javascript:void(0);">Product Name 1</a>
                                                    </td>
                                                    <td>PT001</td>
                                                    <td>Computers</td>
                                                    <td>20</td>
                                                    <td>$1000</td>
                                                    <td>100</td>
                                                </tr>
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
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

                                        <div class="input-blocks">
                                            <i data-feather="calendar" class="info-img"></i>
                                            <div class="input-groupicon">
                                                <input type="text" class="datetimepicker" placeholder="Choose Date From" >
                                            </div>
                                        </div>
                                
                                        <div class="input-blocks">
                                            <i data-feather="calendar" class="info-img"></i>
                                            <div class="input-groupicon">
                                                <input type="text" class="datetimepicker" placeholder="Choose Date To" >
                                            </div>
                                        </div>
                                                
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