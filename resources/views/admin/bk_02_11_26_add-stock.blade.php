@extends('layouts.app_admin')

@section('title')
Dashbaord
@endsection

@section('styles')

@endsection

@section('content')
@php
    $storeMarkups = $storeMarkups ?? [];
@endphp


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
								<a href="{{route('admin.stock.list')}}" class="btn btn-secondary"><i data-feather="arrow-left" class="me-2"></i>Back to Product</a>
							</div>
						</li>
						<li>
							<a data-bs-toggle="tooltip" data-bs-placement="top" title="Collapse" id="collapse-header"><i data-feather="chevron-up" class="feather-chevron-up"></i></a>
						</li>
					</ul>
				</div>
				{{--<form action="{{route('admin.stock.store')}}" method="post" enctype="multipart/form-data">--}}
				{{--@if(session('success'))
					<div class="alert alert-success alert-dismissible fade show" role="alert">
						{{ session('success') }}
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
				@endif--}}
				{{--@if(session('success'))
					<div class="alert alert-success alert-dismissible fade show" role="alert">
						{{ session('success') }}

						<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
					</div>
				@endif--}}
				<form action="{{ isset($stock) ? route('admin.stock.update', $stock->id) : route('admin.stock.store') }}" 
					method="post" 
					enctype="multipart/form-data">
					@csrf
					@if(isset($stock))
						@method('PUT')
					@endif
					
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
									@php
										// Store comes from header selection (session), not a form dropdown.
										// $user = auth()->user();
										// $selectedStore = $user->selected_store_id;
										$headerStoreId = session('selected_store_id');
									@endphp
									<div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <div class="row">
                                                {{-- Store dropdown removed — use header selected store --}}
                                                {{--
                                                <div class="col-lg-3 col-sm-6 col-12">
                                                    <div class="mb-3 add-product">
                                                        <label class="form-label">Store</label>
                                                        <select class="select select1" name="store_id">
															@foreach($stores as $store)
																<option value="{{$store->id}}" {{ $store->id==$selectedStore ? 'selected':''}}>{{$store->name}}</option>
															@endforeach
                                                        </select>
														@error('store_id')
															<div class="text-danger">{{ $message }}</div>
														@enderror
                                                    </div>
                                                </div>
                                                --}}
                                                <input type="hidden" name="store_id" value="{{ old('store_id', $headerStoreId) }}">
                                                @error('store_id')
                                                    <div class="col-12 mb-2">
                                                        <div class="text-danger">{{ $message }}</div>
                                                    </div>
                                                @enderror

                                                <div class="col-lg-3 col-sm-6 col-12">
                                                    <div class="mb-3 add-product">
                                                        <label class="form-label">Product ID</label>
                                                        <input type="text" name="product_id" class="form-control" 
       													value="{{ old('product_id', isset($stock) ? $stock->product_id : 'P_' . str_pad(\App\Models\Stock::count() + 1, 3, '0', STR_PAD_LEFT)) }}" readonly>
                           	                         </div>
                                                </div>

                                                <div class="col-lg-3 col-sm-6 col-12">
                                                    <div class="mb-3 add-product">
                                                        <label class="form-label">Product Name</label>
                                                        <input type="text" name="product_name" class="form-control" value="{{ old('product_name', isset($stock) ? $stock->product_name : '') }}">
														@error('product_name')
															<div class="text-danger">{{ $message }}</div>
														@enderror
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-sm-6 col-12">
                                                    <div class="input-blocks add-product list">
                                                        <label>SKU</label>
                                                        <input type="text" name="sku" class="form-control list" placeholder="Enter SKU" value="{{ old('sku', isset($stock) ? $stock->sku : '') }}">
														@error('sku')
															<div class="text-danger">{{ $message }}</div>
														@enderror
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
															
                                                            <select class="select1 select_category" name="category_id" id="category_id">
                                                                <option>Choose</option>
																<!-- @foreach($categories as $category)
                                                                <option value="{{$category->id}}" 																
																{{ old('category_id', isset($stock) ? $stock->category_id : '') == $category->id ? 'selected' : '' }}
																>{{$category->name}}</option>
																@endforeach                                                                 -->
																
																@foreach($categories->where('status', 1)->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE) as $category)
																	<option value="{{ $category->id }}"
																		{{ old('category_id', isset($stock) ? $stock->category_id : '') == $category->id ? 'selected' : '' }}>
																		{{ $category->name }}
																	</option>
																@endforeach
                                                            </select>

															

															@error('category_id')
																<div class="text-danger">{{ $message }}</div>
															@enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3 col-sm-6 col-12">
														<div class="mb-3 add-product">
															<div class="add-newplus">
																<label class="form-label">Unit</label>
																<a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#add-unit">
																	<i data-feather="plus-circle" class="plus-down-add"></i><span>Add New</span>
																</a>
															</div>
															<select class="select unit_id" name="unit_id" id="unit_id">
																<option value="">Choose</option>
																@foreach($units as $unit)
																	<option value="{{ $unit->id }}" {{ old('unit_id', isset($stock) ? $stock->unit_id : '') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
																@endforeach
															</select>
													
															@error('unit_id')
																<div class="text-danger">{{ $message }}</div>
															@enderror
														</div>
													</div>

                                                    <div class="col-lg-3 col-sm-6 col-12">
                                                        <div class="mb-3 add-product">
                                                            <label class="form-label">Product Type <span class="text-danger">*</span></label>
                                                            <select class="select product-type-select" name="product_type" id="product_type" required>
                                                                <option value="">Choose Product Type</option>
                                                                <option value="service" {{ old('product_type', isset($stock) ? $stock->product_type : '') === 'service' ? 'selected' : '' }}>Service</option>
                                                                <option value="inventory_stock" {{ old('product_type', isset($stock) ? $stock->product_type : 'inventory_stock') === 'inventory_stock' ? 'selected' : '' }}>Inventory Stock</option>
                                                                <option value="non_inventory_stock" {{ old('product_type', isset($stock) ? $stock->product_type : '') === 'non_inventory_stock' ? 'selected' : '' }}>Non-Inventory Stock</option>
                                                            </select>
                                                            <small class="text-muted">Choose how this product should behave in stock and accounting flows.</small>
                                                            @error('product_type')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3 col-sm-6 col-12">
                                                        <div class="mb-3 add-product">
                                                            <label class="form-label">Taxable</label>
                                                            <select class="select" name="is_taxable" id="is_taxable">
                                                                <option value="1" {{ old('is_taxable', isset($stock) ? (int) $stock->is_taxable : 1) == 1 ? 'selected' : '' }}>Taxable</option>
                                                                <option value="0" {{ old('is_taxable', isset($stock) ? (int) $stock->is_taxable : 1) == 0 ? 'selected' : '' }}>Non-taxable</option>
                                                            </select>
                                                            @error('is_taxable')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3 col-sm-6 col-12" id="product_tax_rate_wrap">
                                                        <div class="mb-3 add-product">
                                                            <label class="form-label">Tax rate (%)</label>
                                                            <input type="number" step="0.01" min="0" max="100" name="tax_rate" id="tax_rate"
                                                                value="{{ old('tax_rate', isset($stock) ? $stock->tax_rate : '') }}"
                                                                class="form-control" placeholder="e.g. 15">
                                                            <small class="text-muted">Only applies when the product is taxable.</small>
                                                            @error('tax_rate')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3 col-sm-6 col-12">
                                                        <div class="mb-3 add-product">
                                                            <label class="form-label">Debit Account <span class="text-danger">*</span></label>
                                                            <select class="select account-select" name="debit_account_id" id="debit_account_id" required>
                                                                <option value="">Choose Debit Account</option>
                                                                @php
                                                                    $accountGroups = collect($glAccounts ?? [])->groupBy('type');
                                                                @endphp
                                                                @foreach($accountGroups as $type => $accounts)
                                                                    <optgroup label="{{ ucfirst($type) }}">
                                                                        @foreach($accounts as $account)
                                                                            <option value="{{ $account->id }}" {{ (string) old('debit_account_id', isset($stock) ? $stock->debit_account_id : '') === (string) $account->id ? 'selected' : '' }}>
                                                                                {{ $account->code }} - {{ $account->name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </optgroup>
                                                                @endforeach
                                                            </select>
                                                            <small class="text-muted">Used when stock value is debited into the ledger.</small>
                                                            @error('debit_account_id')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3 col-sm-6 col-12">
                                                        <div class="mb-3 add-product">
                                                            <label class="form-label">Credit Account <span class="text-danger">*</span></label>
                                                            <select class="select account-select" name="credit_account_id" id="credit_account_id" required>
                                                                <option value="">Choose Credit Account</option>
                                                                @foreach($accountGroups as $type => $accounts)
                                                                    <optgroup label="{{ ucfirst($type) }}">
                                                                        @foreach($accounts as $account)
                                                                            <option value="{{ $account->id }}" {{ (string) old('credit_account_id', isset($stock) ? $stock->credit_account_id : '') === (string) $account->id ? 'selected' : '' }}>
                                                                                {{ $account->code }} - {{ $account->name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </optgroup>
                                                                @endforeach
                                                            </select>
                                                            <small class="text-muted">Used when stock transactions credit the ledger.</small>
                                                            @error('credit_account_id')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
													
                                                    {{--<div class="col-lg-3 col-sm-6 col-12">
                                                        <div class="mb-3 add-product">
                                                            <label class="form-label">Supplier Name</label>
                                                            <input type="text" name="supplier_name" value="{{ old('supplier_name', isset($stock) ? $stock->supplier_name : '') }}" class="form-control list" placeholder="Supplier Name">
															@error('supplier_name')
																<div class="text-danger">{{ $message }}</div>
															@enderror
														</div>
                                                    </div>
                                                    <div class="col-lg-3 col-sm-6 col-12">
                                                        <div class="input-blocks">
                                                            <label>Purchase Date</label>
                    
                                                            <div class="input-groupicon calender-input">
                                                                <i data-feather="calendar" class="info-img"></i>
                                                                <input type="text" name="purchase_date" value="{{ old('purchase_date', isset($stock) ? format_date($stock->purchase_date, '') : '') }}" class="datetimepicker" placeholder="Choose Date">
																@error('purchase_date')
																	<div class="text-danger">{{ $message }}</div>
																@enderror
                                                            </div>
                                                        </div>
                                                    </div>--}}
                                                    
                                                </div>
                                            </div>

                                            <div class="col-lg-12">
                                                <div class="input-blocks summer-description-box transfer mb-3">
                                                    <label>Description</label>
                                                    <textarea class="form-control h-100" name="description" rows="5">{!! old('description', isset($stock) ? $stock->description : '') !!}</textarea>
                                                    <p class="mt-1">Maximum 60 Characters</p>
													@error('description')
														<div class="text-danger">{{ $message }}</div>
													@enderror
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
												<div class="single-pill-product">
													<div class="row">
                                                        {{-- Legacy manual quantity entry removed per new stock rules.
                                                        <div class="col-lg-3 col-sm-6 col-12">
                                                            <div class="input-blocks add-product">
                                                                <label>Quantity</label>
                                                                <input type="text" name="qty" value="{{ old('qty', isset($stock) ? $stock->quantity : '') }}" class="form-control">
                                                                @error('qty')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        --}}
                                                        <div class="col-lg-6 col-sm-12 col-12">
                                                            <div class="input-blocks add-product">
                                                                <label>Quantity</label>
                                                                <div class="form-control bg-light d-flex align-items-center" style="min-height: 38px;">
                                                                    Computed automatically from purchases minus sales. No manual quantity entry is required.
                                                                </div>
                                                            </div>
                                                        </div>
														<div class="col-lg-3 col-sm-6 col-12">
															<div class="input-blocks add-product">
																<label>Cost</label>
																{{-- Old manual cost entry (Point 18 — cost is transaction-derived).
																<input type="number" step="0.01" min="0" name="cost" id="product_cost"
																	value="{{ old('cost', isset($stock) ? $stock->cost : '') }}" class="form-control">
																--}}
																<div class="form-control bg-light d-flex align-items-center" style="min-height: 38px;">
																	@if(isset($stock))
																		{{ number_format((float) ($stock->cost ?? 0), 2, '.', '') }}
																	@else
																		0.00
																	@endif
																</div>
																<input type="hidden" id="product_cost"
																	value="{{ old('cost', isset($stock) ? number_format((float) ($stock->cost ?? 0), 2, '.', '') : '0.00') }}">
																<small class="text-muted">Computed from purchases, receipts, adjustments, and sales using the inventory valuation method.</small>
																@error('cost')
																	<div class="text-danger">{{ $message }}</div>
																@enderror
															</div>
														</div>
														<div class="col-lg-3 col-sm-6 col-12">
															<div class="input-blocks add-product">
																<label>Selling Price</label>
																<input type="text" id="primary_selling_price" class="form-control bg-light" readonly
																	value="{{ old('price', isset($stock) ? number_format((float) $stock->price, 2, '.', '') : '0.00') }}">
																<input type="hidden" name="price" id="product_price_hidden"
																	value="{{ old('price', isset($stock) ? $stock->price : '') }}">
																<small class="text-muted">Calculated from cost and store markup for the selected store.</small>
																@error('price')
																	<div class="text-danger">{{ $message }}</div>
																@enderror
															</div>
														</div>
														{{-- Old manual price entry (Point 20 — price is now calculated from cost + markup).
														<div class="col-lg-3 col-sm-6 col-12">
															<div class="input-blocks add-product">
																<label>Price</label>
																<input type="text" name="price" value="{{ old('price', isset($stock) ? $stock->price : '') }}" class="form-control">
																@error('price')
																	<div class="text-danger">{{ $message }}</div>
																@enderror
															</div>
														</div>
														--}}
														{{--<div class="col-lg-3 col-sm-6 col-12">
															<div class="input-blocks add-product">
																<label>Discount (%)</label>
																<input type="text" name="discount" value="{{ old('discount', isset($stock) ? $stock->discount : '') }}" class="form-control">
																@error('discount')
																	<div class="text-danger">{{ $message }}</div>
																@enderror
															</div>
														</div>
														<div class="col-lg-3 col-sm-6 col-12">
															<div class="input-blocks add-product">
																<label>Tax (%)</label>
																<input type="text" name="tax" value="{{ old('tax', isset($stock) ? $stock->tax : '') }}" class="form-control">
																@error('tax')
																	<div class="text-danger">{{ $message }}</div>
																@enderror
															</div>
														</div>--}}
													</div>

														@include('admin.partials.product-store-markup-fields', [
															'stores' => $stores ?? collect(),
															'storeMarkups' => $storeMarkups,
														])
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
														<div class="image-uploads" id="image-upload-label" style="cursor:pointer;">
															<i data-feather="plus-circle" class="plus-down-add me-0"></i>
															<h4>Add Image</h4>
														</div>
														<input type="file" name="image" class="form-control d-none" id="imgInp" accept="image/*" style="width: 100%;">
													</div>
													@error('image')
														<div class="text-danger">{{ $message }}</div>
													@enderror
												</div>

												<!-- Image Preview Div -->
												<div id="image-preview" class="">
													@if(isset($stock) && $stock->images)
													<div class="phone-img position-relative me-2 mb-2">
														<img src="{{ asset('storage/' . $stock->images) }}" alt="image" class="img-thumbnail" width="150" height="150">
														<a href="javascript:void(0);" class="remove-product">
															<i data-feather="x" class="x-square-add"></i>
														</a>
													</div>
													@endif
												</div>

												<input type="hidden" name="remove_image" id="remove_image" value="0">
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
												<div class="addproduct-icon list icon">
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
																<input type="text" name="manufactured_date"
																	value="{{ old('manufactured_date', isset($stock) && $stock->manufactured_date ? format_date($stock->manufactured_date, '') : '') }}"
																	class="datetimepicker" placeholder="Choose Date">
																@error('manufactured_date')
																	<div class="text-danger">{{ $message }}</div>
																@enderror
															</div>
														</div>
													</div>
													<div class="col-lg-4 col-sm-6 col-12">
														<div class="input-blocks">
															<label>Expiry On</label>
					
															<div class="input-groupicon calender-input">
																<i data-feather="calendar" class="info-img"></i>
																<input type="text" name="expiry_on"
																	value="{{ old('expiry_on', isset($stock) && $stock->expiry_date ? format_date($stock->expiry_date, '') : '') }}"
																	class="datetimepicker" placeholder="Choose Date">
																@error('expiry_on')
																	<div class="text-danger">{{ $message }}</div>
																@enderror
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
					</div>
					<div class="col-lg-12">
						<div class="btn-addproduct mb-4">
							<a type="button" class="btn btn-cancel me-2" href="{{route('admin.stock.list')}}">Cancel</a>
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
							<form id="category-form">
								@csrf
								<div class="mb-3">
									<label class="form-label">Name</label>
									<input type="text" class="form-control" id="category-name" name="name" required>
								</div>
								<div class="modal-footer-btn">
									<a href="javascript:void(0);" class="btn btn-cancel me-2" data-bs-dismiss="modal">Cancel</a>
									<button type="submit" class="btn btn-submit">Submit</button>
								</div>
							</form>

							
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
							<form id="unit-form">
								@csrf
								<div class="mb-3">
									<label class="form-label">Unit Name</label>
									<input
										type="text"
										class="form-control"
										name="name"
										id="unit-name"
										required
									/>
								</div>
								<div class="modal-footer-btn">
									<a
										href="javascript:void(0);"
										class="btn btn-cancel me-2"
										data-bs-dismiss="modal"
										>Cancel</a
									>
									<button type="submit" class="btn btn-submit">
										Submit
									</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- /Add Unit -->



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
$(document).ready(function () {
    $("#category-form").on("submit", function (e) {
        e.preventDefault();

        $.ajax({
            url: "{{ route('admin.categories.store') }}",
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function (response) {
				console.log(response);
				console.log(response.category);
				console.log(response.category.id);
                if (response.status === "success") {
                    
					$("#category-form")[0].reset();
                    $("#add-units-category").modal("hide");
					
					let newOption = new Option(response.category.name, response.category.id, true, true);
					let $select = $(".select_category");
					
					$select.append(newOption).trigger("change");
					if ($select.hasClass("select_category")) {
						$select.select2(); 
					}
					$('.select_category').val(response.category.name).trigger('change');					
					alert(response.message);
                }
            },
            error: function (xhr) {
                alert("Error adding category");
            },
        });
    });
	$("#unit-form").on("submit", function (e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('admin.units.store') }}",
                type: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function (response) {
                    if (response.status === "success") {
                        $("#unit-form")[0].reset();
                        $("#add-unit").modal("hide");
                        let newOption = new Option(response.unit.name, response.unit.id, true, true);
                        $("select[name='unit']").append(newOption).trigger("change");

						let $select = $("#unit_id");
					
						$select.append(newOption).trigger("change");
						if ($select.hasClass("unit_id")) {
							$select.select2(); 
						}
						$('#unit_id').val(response.unit.name).trigger('change');
                        alert(response.message);
                    }
                },
                error: function (xhr) {
                    alert("Error adding unit");
                },
            });
        });
});

$(document).ready(function() {
    // Store select removed — store_id comes from header session.
    // $('.select1').select2({
    //     minimumResultsForSearch: -1,
    //     width: '100%'
    // });
    // let selectedValue = "{{ old('store_id', isset($stock) ? $stock->store_id : '') }}";
    // if (selectedValue) {
    //     $('.select1').val(selectedValue).trigger('change');
    // }
	$('.select_category').select2({
        minimumResultsForSearch: -1,
        width: '100%'
    });
    let selectedValue2 = "{{ old('category_id', isset($stock) ? $stock->category_id : '') }}";
    if (selectedValue2) {
        $('.select_category').val(selectedValue2).trigger('change');
    }

    $('.account-select').select2({
        width: '100%'
    });

    $('.product-type-select').select2({
        minimumResultsForSearch: -1,
        width: '100%'
    });

    const selectedDebitAccount = "{{ old('debit_account_id', isset($stock) ? $stock->debit_account_id : '') }}";
    if (selectedDebitAccount) {
        $('#debit_account_id').val(selectedDebitAccount).trigger('change');
    }

    const selectedCreditAccount = "{{ old('credit_account_id', isset($stock) ? $stock->credit_account_id : '') }}";
    if (selectedCreditAccount) {
        $('#credit_account_id').val(selectedCreditAccount).trigger('change');
    }

    const selectedProductType = "{{ old('product_type', isset($stock) ? $stock->product_type : 'inventory_stock') }}";
    if (selectedProductType) {
        $('#product_type').val(selectedProductType).trigger('change');
    }

    function syncProductTaxRateField() {
        const taxable = $('#is_taxable').val() === '1';
        $('#product_tax_rate_wrap').toggle(taxable);
        $('#tax_rate').prop('required', taxable);
        if (!taxable) {
            $('#tax_rate').val('');
        }
    }
    $('#is_taxable').on('change', syncProductTaxRateField);
    syncProductTaxRateField();
});
</script>
@include('admin.partials.product-store-markup-scripts')
<script>
    // Function to update the product ID field
    function updateProductId(input) {
        let value = input.value;
        if (!value.startsWith('P_')) {
            input.value = 'P_'; // Ensure the value starts with 'P_'
        }
    }
</script>
<script>
$(document).ready(function () {
    // Trigger file input on clicking "Add Image"
    $('#image-upload-label').on('click', function () {
        $('#imgInp').trigger('click');
    });

    // Show preview for selected image
    $("#imgInp").on("change", function (e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                $("#image-preview").css("display", "block").html(`
                    <div class="phone-img position-relative me-2 mb-2">
                        <img src="${e.target.result}" alt="preview" class="img-thumbnail" width="150" height="150">
                        <a href="javascript:void(0);" class="remove-product">
                            <i data-feather="x" class="x-square-add"></i>
                        </a>
                    </div>
                `);
                feather.replace();
                $('#remove_image').val('0');
            };
            reader.readAsDataURL(file);
        }
    });

    // Remove image preview and reset file input
    $(document).on("click", ".remove-product", function () {
        $("#image-preview").html('').css("display", "none"); // Clear and hide
        $('#imgInp').val(null);       // Reset input
        $('#remove_image').val('1');  // Mark image as removed
    });
});


</script>


@endsection
