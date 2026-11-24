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
					<h4>Product List</h4>
					<h6>Manage your products</h6>
				</div>
			</div>
			<ul class="table-top-head">
				<!-- <li>
						<a data-bs-toggle="tooltip" data-bs-placement="top" title="Pdf"><img src="https://dreamspos.dreamstechnologies.com/html/template/assets/img/icons/pdf.svg" alt="img"></a>
					</li> -->
				<li>
					<a data-bs-toggle="tooltip" data-bs-placement="top" title="Excel"
						href="{{route('admin.stocks-export')}}"><img src="{{asset('admin/assets/img/icons/excel.svg')}}"
							alt="img"></a>
				</li>
				<!-- <li>
						<a data-bs-toggle="tooltip" data-bs-placement="top" title="Print"><i data-feather="printer" class="feather-rotate-ccw"></i></a>
					</li>
					<li> -->
				<a data-bs-toggle="tooltip" data-bs-placement="top" title="Refresh" id="refresh-page"><i
						data-feather="rotate-ccw" class="feather-rotate-ccw"></i></a>
				</li>
				<li>
					<a data-bs-toggle="tooltip" data-bs-placement="top" title="Collapse" id="collapse-header"><i
							data-feather="chevron-up" class="feather-chevron-up"></i></a>
				</li>
			</ul>
		</div>

		<!-- /product list -->
		<div class="card table-list-card">
			<div class="card-body">
				<div class="table-top">
					<div class="search-set">
						<div class="search-input d-flex align-items-center">
							<div id="custom-search" class="me-2"></div> <!-- DataTable input will be moved here -->
							<a href="#" class="btn btn-searchset">
								<i data-feather="search" class="feather-search"></i>
							</a>
						</div>
					</div>
					<!-- <div class="search-path">   
							<a class="btn btn-filter" id="filter_search">
								<i data-feather="filter" class="filter-icon"></i>
								<span><img src="{{asset('admin/assets/img/icons/closes.svg')}}" alt="img"></span>
							</a>
						</div> -->
					<div class="form-sort">
						<i data-feather="sliders" class="info-img"></i>
						<select class="select" id="sort-order">
							<option value="">Sort by</option>
							<option value="desc">Newest First</option>
							<option value="asc">Oldest First</option>
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
											<i data-feather="stop-circle" class="info-img"></i>
											<select class="select" id="filter_category">
												<option value="choose">Choose Category</option>
												@foreach($categories as $category)
												<option value="{{ $category->id }}">{{ $category->name }}</option>
												@endforeach
											</select>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- /Filter -->
				<div class="table-responsive product-list">
					<table class="table datanew" id="stock-list-table">
						<thead>
							<tr>
								<th>Store</th>
								<th>Product</th>
								<th>SKU</th>
								<th>Category</th>
								<th>Price</th>
								<th>Unit</th>
								<th>Qty</th>
								<!-- <th>Actions</th> -->

							</tr>
						</thead>

					</table>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection

@section('scripts')
<script>
	$(document).ready(function () {
		let table = $('#stock-list-table').DataTable({
			processing: true,
			serverSide: true,
			ajax: {
				url: "{{ route('admin.stock.list.storemanager') }}",
				data: function (d) {
					d.category = $('#filter_category').val();
					d.store = $('#filter_store').val();
					d.order_type = $('#sort-order').val();
				}
			},
			columns: [
				{ data: 'store_name', name: 'store_name' },
				{ data: 'product', name: 'product' },
				{ data: 'sku', name: 'sku' },
				{ data: 'category', name: 'category' },
				{ data: 'price', name: 'price' },
				{ data: 'unit', name: 'unit' },
				{ data: 'quantity', name: 'quantity' },
				// { data: 'actions', name: 'actions', orderable: false, searchable: false },

			],
			pageLength: 10,
			drawCallback: function () {
				feather.replace();
			},
			initComplete: function () {
				let searchWrapper = $('#stock-list-table_filter');
				let defaultSearchBox = searchWrapper.find('input');
				$('#custom-search').append(defaultSearchBox);
				defaultSearchBox.attr('placeholder', 'Search...');
				searchWrapper.remove();
			}
		});
		$('#filter_category').on('change', function () {
			table.ajax.reload();
		});
		$('#filter_store').on('change', function () {
			table.ajax.reload();
		});
		$('#sort-order').on('change', function () {
			table.ajax.reload();
		});
	});


</script>


@endsection