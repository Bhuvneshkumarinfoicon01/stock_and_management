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
					<h4>Daily Operations Overview</h4>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-header">
						<h5 class="card-title">Stock Levels</h5>
					</div>
					<div class="card-body">
						<div id="s-col-stacked-graph" class="chart-set"></div>
					</div>
				</div>
			</div>

			<div class="col-md-12">
				<div class="card table-list-card">
					<div class="card-header">
						<h5 class="card-title">Day Inventory Movement</h5>
					</div>
					<div class="card-body">
						<div class="table-top">
							<div class="search-set">
								<div class="search-input d-flex align-items-center">
									<div id="custom-search" class="me-2"></div>
									<!-- DataTable input will be moved here -->
									<a href="#" class="btn btn-searchset">
										<i data-feather="search" class="feather-search"></i>
									</a>
								</div>
							</div>

							<div class="form-sort">
								<i data-feather="sliders" class="info-img"></i>
								<select class="select" id="sort-order">
									<option value="">Sort by</option>
									<option value="desc">Newest First</option>
									<option value="asc">Oldest First</option>
								</select>
							</div>
						</div>
						<!-- Filter -->
						<div class="card" id="superadmin_daily_filter_inputs" style="display:block !important;">
							<div class="card-body pb-0">
								<div class="row">
									<div class="col-lg-3 col-sm-6 col-12">
										<div class="input-blocks">
											{{-- Old code:
											<i data-feather="archive" class="info-img"></i>
											--}}
											<select id="filter-store-from" class="select">
												<option value="choose">From Store</option>
												@foreach($stores as $store)
												<option value="{{ $store->id }}">{{ $store->name }}</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="col-lg-3 col-sm-6 col-12">
										<div class="input-blocks">
											{{-- Old code:
											<i data-feather="archive" class="info-img"></i>
											--}}
											<select id="filter-store-to" class="select">
												<option value="choose">To Store</option>
												@foreach($stores as $store)
												<option value="{{ $store->id }}">{{ $store->name }}</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="col-lg-2 col-sm-6 col-12">
										<div class="input-blocks">
											<i data-feather="calendar" class="info-img"></i>
											<div class="input-groupicon">
												<input type="text" id="filter-date-from" class="datetimepicker" placeholder="Choose From Date" />
											</div>
										</div>
									</div>
									<div class="col-lg-2 col-sm-6 col-12">
										<div class="input-blocks">
											<i data-feather="calendar" class="info-img"></i>
											<div class="input-groupicon">
												<input type="text" id="filter-date-to" class="datetimepicker" placeholder="Choose To Date" />
											</div>
										</div>
									</div>
									<div class="col-lg-1 col-sm-6 col-12">
										<div class="input-blocks">
											<a class="btn btn-filters ms-auto" id="searchBtn">
												<i data-feather="search" class="feather-search"></i>
												Search
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- /Filter -->
						<div class="table-responsive product-list">
							<table class="table datanew-1" id="stock-transfers-table">
								<thead>
									<tr>
										<th>Store From (Outgoing)</th>
										<th>Store To (Incoming)</th>
										<th>Product</th>
										<th>Qty</th>
									</tr>
								</thead>

							</table>
						</div>
					</div>
				</div>
			</div>


			<div class="col-md-12">
				<div class="card table-list-card">
					<div class="card-header">
						<h5 class="card-title">Day Sales Overview</h5>
					</div>
					<div class="card-body">
						<div class="table-top">
							<div class="search-set">
								<div class="search-input d-flex align-items-center">
									<div id="custom-search-second" class="me-2"></div>
									<!-- DataTable input will be moved here -->
									<a href="#" class="btn btn-searchset">
										<i data-feather="search" class="feather-search"></i>
									</a>
								</div>
							</div>
							<div class="form-sort">
								<i data-feather="sliders" class="info-img"></i>
								<select class="select" id="sort-order-second">
									<option value="">Sort by</option>
									<option value="desc">Newest First</option>
									<option value="asc">Oldest First</option>
								</select>
							</div>
						</div>
						<!-- Sales Date Filter -->
						<div class="card" id="superadmin_sales_filter_inputs" style="display:block !important;">
							<div class="card-body pb-0">
								<div class="row">
									<div class="col-lg-5 col-sm-6 col-12">
										<div class="input-blocks">
											<i data-feather="calendar" class="info-img"></i>
											<div class="input-groupicon">
												<input type="text" id="sales-filter-date-from" class="datetimepicker" placeholder="Choose Start Date" />
											</div>
										</div>
									</div>
									<div class="col-lg-5 col-sm-6 col-12">
										<div class="input-blocks">
											<i data-feather="calendar" class="info-img"></i>
											<div class="input-groupicon">
												<input type="text" id="sales-filter-date-to" class="datetimepicker" placeholder="Choose End Date" />
											</div>
										</div>
									</div>
									<div class="col-lg-2 col-sm-6 col-12">
										<div class="input-blocks">
											<a class="btn btn-filters ms-auto" id="salesSearchBtn">
												<i data-feather="search" class="feather-search"></i>
												Search
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- /Sales Date Filter -->
						<div class="table-responsive">
							<table class="table datanew-2" id="sales-overview-table">
								<thead>
									<tr>
										<th>SKU</th>
										<th>Product Name</th>
										<th>Price</th>
										<th>Qty</th>
										<th>Total</th>
									</tr>
								</thead>

							</table>
						</div>

					</div>
				</div>
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
@section('scripts')
<script>

	let table = $('#stock-transfers-table').DataTable({
		processing: true,
		serverSide: true,
		ajax: {
			url: "{{ route('admin.daily-operations-overview-superadmin') }}",
			data: function (d) {
				d.order_type = $('#sort-order').val();
				d.store_id = '{{ session('selected_store_id') }}'; // Add this line
				d.store_from = $('#filter-store-from').val();
				d.store_to = $('#filter-store-to').val();
				d.date_from = $('#filter-date-from').val();
				d.date_to = $('#filter-date-to').val();
			}
		},
		columns: [
			{ data: 'store_from', name: 'store_from' },
			{ data: 'store_to', name: 'store_to' },
			{ data: 'item_name', name: 'item_name' },
			{ data: 'item_qty', name: 'item_qty' },
		],
		pageLength: 10,
		initComplete: function () {
			let searchWrapper = $('#stock-transfers-table_filter');
			let defaultSearchBox = searchWrapper.find('input');
			$('#custom-search').append(defaultSearchBox);
			defaultSearchBox.attr('placeholder', 'Search...');
			searchWrapper.remove();
		}
	});

	$('#sort-order').on('change', function () {
		table.ajax.reload();
	});
	$('#searchBtn').on('click', function () {
		table.ajax.reload();
	});

	// SECOND TABLE
	let secondTable = $('#sales-overview-table').DataTable({
		processing: true,
		serverSide: true,
		ajax: {
			url: "{{ route('admin.daily-sales-overview-admin') }}",
			data: function (d) {
				d.order_type = $('#sort-order-second').val();
				d.date_from = $('#sales-filter-date-from').val();
				d.date_to = $('#sales-filter-date-to').val();
			}
		},
		columns: [
			{ data: 'sku', name: 'sku' },
			{ data: 'product_name', name: 'order_items.product_name' },
			{ data: 'price', name: 'order_items.price' },
			{ data: 'qty', name: 'qty' },
			{ data: 'totalPrice', name: 'order_items.price' },
		],
		pageLength: 10,
		initComplete: function () {
			let searchWrapper = $('#sales-overview-table_filter');
			let defaultSearchBox = searchWrapper.find('input');
			$('#custom-search-second').append(defaultSearchBox);
			defaultSearchBox.attr('placeholder', 'Search...');
			searchWrapper.remove();
		}
	});

	$('#sort-order-second').on('change', function () {
		secondTable.ajax.reload();
	});
	$('#salesSearchBtn').on('click', function () {
		secondTable.ajax.reload();
	});
</script>
<script>
	// Attach change event to the select dropdown
	$('#sort').on('change', function () {
		// Get the selected value
		var sortOption = $(this).val();

		// Send AJAX request
		$.ajax({
			url: "{{ route('admin.daily-operations-overview-superadmin') }}",
			method: "GET",
			data: { sort: sortOption },
			success: function (response) {
				// Update the table with the new sorted data (partial HTML)
				$('#stockTransfersData').html(response);
			}
		});
	});
</script>

<script>
	document.addEventListener("DOMContentLoaded", function () {
		const categoryChartData = @json($categoryData);

		if (document.querySelector("#s-col-stacked-graph")) {
			// Brighter and vivid colors
			const colors = categoryChartData.map((_, index) => {
				const hue = (index * 97.508) % 360; // golden angle for separation
				return `hsl(${hue}, 85%, 65%)`; // higher saturation & lightness
			});

			const sColStacked = {
				chart: {
					height: 350,
					type: "bar",
					toolbar: { show: false }
				},
				series: [{
					name: "Total Items",
					data: categoryChartData.map(item => item.count)
				}],
				colors: colors,
				xaxis: {
					categories: categoryChartData.map(item => item.name)
				},
				plotOptions: {
					bar: {
						horizontal: false,
						distributed: true
					}
				},
				legend: { show: false },
				fill: { opacity: 1 }
			};

			const chart = new ApexCharts(document.querySelector("#s-col-stacked-graph"), sColStacked);
			chart.render();
		}

	})	
</script>
<script>
	document.addEventListener("DOMContentLoaded", function () {
		// Initialize search for Inventory Movement Table
		$('#searchInput2 input').on('input', function () {
			var searchQuery = $(this).val().toLowerCase(); // Get the search query

			// Filter rows in the "Inventory Movement" table
			$('#stockTransfersData tr').each(function () {
				var rowText = $(this).text().toLowerCase(); // Get the text content of the row
				if (rowText.includes(searchQuery)) {
					$(this).show(); // Show row if it matches the search query
				} else {
					$(this).hide(); // Hide row if it doesn't match the search query
				}
			});
		});

		// Initialize search for Sales Overview Table
		$('#searchInput1 input').on('input', function () {
			var searchQuery = $(this).val().toLowerCase(); // Get the search query

			// Filter rows in the "Sales Overview" table
			$('.sales-list tr').each(function () {
				var rowText = $(this).text().toLowerCase(); // Get the text content of the row
				if (rowText.includes(searchQuery)) {
					$(this).show(); // Show row if it matches the search query
				} else {
					$(this).hide(); // Hide row if it doesn't match the search query
				}
			});
		});
	});
</script>




@endsection