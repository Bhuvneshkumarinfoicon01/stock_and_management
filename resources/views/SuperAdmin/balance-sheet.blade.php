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
					<div class="add-item d-flex">
						<div class="page-title">
							<h4>Balance Sheet</h4>
						</div>
					</div>
				</div>


                <div class="card table-list-card border-0 mb-0">
						<div class="card-body mb-3">
							<div class="table-top mb-0 profit-table-top">
								
								<div class="search-path profit-head ">
									<div class="input-blocks mb-0">
										<i data-feather="calendar" class="info-img"></i>
										<div class="input-groupicon">
											<input type="text" class="datetimepicker" placeholder="This Financial Year" >
										</div>
									</div>
								</div>
								<div class="position-relative daterange-wraper input-blocks mb-0">
									<input type="text" name="datetimes" placeholder="From Month -  To Month " class="form-control">
									<i data-feather="calendar" class="feather-14 info-img"></i>
								</div>
								<div class="input-blocks mb-0 me-2">
									<select class="select">
										<option>Choose Store</option>
										<option>Storename 1</option>
										<option>Storename 2</option>
										<option>Storename 3</option>
										<option>Storename 4</option>
										<option>Storename 5</option>
									</select>
								</div>
								<div class="date-btn">
									<a href="javascript:void(0);" class="btn btn-secondary d-flex align-items-center">
										<i data-feather="database" class="feather-14 info-img me-2"></i>Display Data</a>
								</div>
							</div>
						</div>
					</div>


                <div class="balance-sheet">
                    <div class="table-responsive" >
                        <table class="table  balance-sheet datanew">
                            <thead>
                                <tr>
                                    <th>Asset Description</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Cash</td>
                                    <td>$50,000</td>
                                </tr>
                                <tr>
                                    <td>Accounts Receivable</td>
                                    <td>$30,000</td>
                                </tr>
                                <tr>
                                    <td>Inventory</td>
                                    <td>$20,000</td>
                                </tr>
                                <tr>
                                    <td>Property, Plant, and Equipment</td>
                                    <td>$100,000</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Assets</strong></td>
                                    <td><strong>$200,000</strong></td>
                                </tr>
                            </tbody>
                        </table>
                        <table class="table  balance-sheet datanew">
                            <thead>
                                <tr>
                                    <th>Liability/Equity Description</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Accounts Payable</td>
                                    <td>$25,000</td>
                                </tr>
                                <tr>
                                    <td>Long-Term Debt</td>
                                    <td>$50,000</td>
                                </tr>
                                <tr>
                                    <td>Common Stock</td>
                                    <td>$75,000</td>
                                </tr>
                                <tr>
                                    <td>Retained Earnings</td>
                                    <td>$50,000</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Liabilities & Equity</strong></td>
                                    <td><strong>$200,000</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
				

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



	
@endsection