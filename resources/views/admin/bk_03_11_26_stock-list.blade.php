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
					<!-- Download Dummy CSV -->
					<a data-bs-toggle="tooltip" data-bs-placement="top" title="Download Dummy CSV"
						href="{{ route('admin.stocks-dummy-download') }}">
						<i data-feather="download" class="feather-download"></i>
					</a>
				</li>
				<li class="dropdown">
					<a href="#" class="btn btn-icon" data-bs-toggle="dropdown" title="Import / Upload">
						<i class="fa-solid fa-upload" style="color:#030303;"></i>
					</a>

					<div class="dropdown-menu p-3 shadow border-0" style="min-width:300px;z-index:9999;">


						<h6 class="dropdown-header px-0">Import products</h6>
						<form action="{{ route('admin.stocks-import') }}" method="POST" enctype="multipart/form-data"
							class="mb-3">
							@csrf
							<div class="mb-2">
								<label class="form-label">CSV file <span class="text-danger">*</span></label>
								<input type="file" name="csv_file" accept=".csv" class="form-control form-control-sm"
									required>
							</div>
							<div class="mb-3">
								<label class="form-label">Image folder <small
										class="text-muted">(optional)</small></label>
								<input type="file" name="images[]" webkitdirectory directory multiple
									class="form-control form-control-sm">
								<small class="text-muted">Filenames must match the “Image” column.</small>
							</div>
							<button type="submit" class="btn btn-sm btn-primary w-100">
								<i class="fa fa-upload me-1"></i> Import
							</button>
						</form>

						<hr class="my-2">

						<h6 class="dropdown-header px-0">Upload images only</h6>
						<form action="{{ route('admin.stocks-upload-images') }}" method="POST"
							enctype="multipart/form-data">
							@csrf
							<div class="mb-3">
								<label class="form-label">Image folder</label>
								<input type="file" name="images[]" webkitdirectory directory multiple
									class="form-control form-control-sm" required>
								<small class="text-muted">Images must be named exactly as in DB (e.g.
									<em>airpods.jpg</em>).</small>
							</div>
							<button type="submit" class="btn btn-sm btn-success w-100">
								<i class="fa fa-image me-1"></i> Upload images
							</button>
						</form>
					</div>
				</li>




				<li>
					<a data-bs-toggle="tooltip" data-bs-placement="top" title="Excel"
						href="{{route('admin.stocks-export')}}"><img src="{{asset('admin/assets/img/icons/excel.svg')}}"
							alt="img"></a>
				</li>
				<!-- <li>
						<a data-bs-toggle="tooltip" data-bs-placement="top" title="Print"><i data-feather="printer" class="feather-rotate-ccw"></i></a>
					</li>-->
				<li>
					<a data-bs-toggle="tooltip" data-bs-placement="top" title="Refresh" id="refresh-page"><i
							data-feather="rotate-ccw" class="feather-rotate-ccw"></i></a>
				</li>
				<li>
					<a data-bs-toggle="tooltip" data-bs-placement="top" title="Collapse" id="collapse-header"><i
							data-feather="chevron-up" class="feather-chevron-up"></i></a>
				</li>
			</ul>
			@if(hasPermission('create', 'stock'))
			<div class="page-btn">
				{{--<a href="{{route('admin.stock.create')}}" class="btn btn-added"><i data-feather="plus-circle" class="me-2"></i>Add New Stock</a>--}}
				<a href="{{route('admin.stock.create')}}" class="btn btn-added"><i data-feather="plus-circle" class="me-2"></i>Add New Product</a>
			</div>
			@endif
		</div>

		<!-- /product list -->
		<div class="card table-list-card">
			<div class="card-body">
				<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
					@include('admin.partials.datatable-page-length-toolbar', [
						'tableKey' => 'stock-list',
						'toolbarId' => 'stock-list-dt-toolbar',
						'datatableConfig' => $datatableConfig,
					])
				</div>
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
											<i class="fas fa-money-bill info-img"></i>
											@php
											$user=auth()->user();
											$selected_store_id=$user->selected_store_id;
											@endphp

											<select class="select" id="filter_store">
												<option value="choose">Choose Store</option>
												@if($user && in_array($user->role_id,[1,2]) )
												<option value="all">All Stores</option>
												@endif
												@foreach($stores as $store)
												@if($store->id==$selected_store_id)
												<option value="{{ $store->id }}">{{ $store->name }}</option>
												@endif
												@endforeach
											</select>
										</div>
									</div>
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
				<div class="table-responsive product-list datatable-scroll-container" id="stock-list-scroll-wrap">
					<table class="table datanew" id="stock-list-table">
						<thead>
							<tr>
								<!-- <th class="no-sort">
										<label class="checkboxs">
											<input type="checkbox" id="select-all">
											<span class="checkmarks"></span>
										</label>
									</th> -->
								<!-- @php
										$user=auth()->user();
									@endphp -->
								<!-- <th>Store</th> -->
								<th>Product</th>
								<th>SKU</th>
								<th>Category</th>
								<th>Price</th>
								<th>Unit</th>
								<th>Qty</th>
								@if($user && in_array($user->role_id,[1,2,3,5]))
								<th class="no-sort">Action</th>
								@endif
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
		function formatCurrency(value) {
			const num = Number(value) || 0;
			return num.toLocaleString('en-US', {
				minimumFractionDigits: 2,
				maximumFractionDigits: 2
			});
		}

		function getStockListAjaxData() {
			return {
				category: $('#filter_category').val(),
				store: $('#filter_store').val(),
				order_type: $('#sort-order').val(),
			};
		}

		const stockColumns = [
			//{ data: 'store_name', name: 'store_name' },
			{ data: 'product', name: 'product' },
			{ data: 'sku', name: 'sku' },
			{ data: 'category', name: 'category' },
			{
				data: 'price',
				name: 'price',
				render: function (data) {
					return formatCurrency(data);
				}
			},
			{ data: 'unit', name: 'unit' },
			{ data: 'quantity', name: 'quantity' },
			{ data: 'actions', name: 'actions', orderable: false, searchable: false },
		];

		const paginationManager = DatatablePagination.attach({
			tableKey: 'stock-list',
			tableSelector: '#stock-list-table',
			toolbarSelector: '#stock-list-dt-toolbar',
			scrollContainerSelector: '#stock-list-scroll-wrap',
			config: @json($datatableConfig),
			getAjaxUrl: function () {
				return "{{ route('admin.stock.list') }}";
			},
			getAjaxData: getStockListAjaxData,
			buildTableOptions: function (mode, pageLength) {
				const shared = {
					columns: stockColumns,
					order: [[1, 'asc']],
					language: {
						emptyTable: 'No products found',
						search: 'Search:',
						searchPlaceholder: 'Search...',
					},
					drawCallback: function () {
						feather.replace();
					},
					initComplete: function () {
						const searchWrapper = $('#stock-list-table_filter');
						const defaultSearchBox = searchWrapper.find('input');
						$('#custom-search').append(defaultSearchBox);
						defaultSearchBox.attr('placeholder', 'Search...');
						searchWrapper.hide();
					},
				};

				if (mode === 'scroll') {
					return Object.assign({}, shared, {
						processing: true,
						serverSide: false,
						paging: false,
						lengthChange: false,
						info: false,
						searching: true,
						dom: 'frt',
						data: [],
					});
				}

				return Object.assign({}, shared, {
					processing: true,
					serverSide: true,
					ajax: {
						url: "{{ route('admin.stock.list') }}",
						data: function (d) {
							Object.assign(d, getStockListAjaxData());
						},
					},
					pageLength: pageLength,
					lengthChange: false,
					searching: true,
					dom: 'frtip',
				});
			},
		});

		$('#filter_category, #filter_store, #sort-order').on('change', function () {
			paginationManager.reload(false);
		});
	});
</script>

{{-- Old DataTable init (replaced by DatatablePagination above).
<script>
	$(document).ready(function () {
		function formatCurrency(value) {
			const num = Number(value) || 0;
			return num.toLocaleString('en-US', {
				minimumFractionDigits: 2,
				maximumFractionDigits: 2
			});
		}

		let table = $('#stock-list-table').DataTable({
			destroy: true,
			processing: true,
			serverSide: true,
			ajax: {
				url: "{{ route('admin.stock.list') }}",
				data: function (d) {
					d.category = $('#filter_category').val();
					d.store = $('#filter_store').val();
					d.order_type = $('#sort-order').val();
				}
			},
			columns: [
				// { data: 'store_name', name: 'store_name' },
				{ data: 'product', name: 'product' },
				{ data: 'sku', name: 'sku' },
				{ data: 'category', name: 'category' },
				{
					data: 'price',
					name: 'price',
					render: function (data) {
						return formatCurrency(data);
					}
				},
				{ data: 'unit', name: 'unit' },
				{ data: 'quantity', name: 'quantity' },
				{ data: 'actions', name: 'actions', orderable: false, searchable: false },
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
				searchWrapper.hide();
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
--}}

<script>
	// DELETE Stock Item
	$(document).on('click', '.delete-stock', function () {
		let stockId = $(this).data('id');
		let $row = $(this).closest('tr');

		Swal.fire({
			title: "Are you sure?",
			text: "You won't be able to revert this!",
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: "#d33",
			cancelButtonColor: "#3085d6",
			confirmButtonText: "Yes, delete it!"
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: "{{ url('admin/stock/delete') }}/" + stockId,
					type: "DELETE",
					data: {
						_token: "{{ csrf_token() }}"
					},
					success: function (response) {
						if (response.success) {
							$row.fadeOut(300, function () {
								$(this).remove();
							});
							Swal.fire("Deleted!", response.message, "success");
						} else {
							Swal.fire("Error!", response.message, "error");
						}
					},
					error: function () {
						Swal.fire("Error!", "Something went wrong.", "error");
					}
				});
			}
		});
	});





</script>
@if(filled(session('success')))
<script>
	document.addEventListener('DOMContentLoaded', function () {
		Swal.fire({
			icon: 'success',
			title: 'Success!',
			text: @json(session('success')),
			confirmButtonText: 'OK',
		});
	});
</script>
@endif
@if(session('conflicts'))
<script>
	document.addEventListener("DOMContentLoaded", function () {
		Swal.fire({
			title: "Some entries were skipped",
			html: `{!! implode('<br>', session('conflicts')) !!}`,
			icon: "warning",
			confirmButtonText: "OK",
			customClass: {
				popup: 'text-start',
			}
		});
	});
</script>
@endif

@if(session('missing'))
<script>
	document.addEventListener("DOMContentLoaded", () => {
		Swal.fire({
			title: "Some images were missing",
			html: `{!! implode('<br>', session('missing')) !!}`,
			icon: "info",
			confirmButtonText: "OK",
			customClass: { popup: 'text-start' }
		});
	});
</script>
@endif
@if(session('storeErrors'))
<script>
	document.addEventListener("DOMContentLoaded", () => {
		Swal.fire({
			title: "Import aborted",
			html: `{!! implode('<br>', session('storeErrors')) !!}`,
			icon: "error",
			confirmButtonText: "OK",
			customClass: { popup: 'text-start' }
		});
	});
</script>
@endif
@if(session('unmatched'))
<script>
	document.addEventListener("DOMContentLoaded", () => {
		Swal.fire({
			title: "Some images were not matched",
			html: `{!! implode('<br>', session('unmatched')) !!}`,
			icon: "info",
			confirmButtonText: "OK",
			customClass: { popup: 'text-start' }
		});
	});
</script>
@endif

<style>
	.dropdown-menu.p-3 {
		z-index: 9999 !important;
		position: absolute !important;
	}
</style>
@endsection