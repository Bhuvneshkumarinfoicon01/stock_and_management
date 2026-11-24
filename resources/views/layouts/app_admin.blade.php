<!DOCTYPE html>
<html lang="en" data-layout-mode="light_mode">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<meta name="description" content="">
	<meta name="keywords" content="">
	<meta name="author" content="">
	<meta name="robots" content="noindex, nofollow">
	<title>Accounting and Stock Management System</title>
	<meta name="csrf-token" content="{{ csrf_token() }}">
	@include('partials.admin.style')
	<!-- jQuery -->
	<script src="{{asset('admin/assets/js/jquery-3.7.1.min.js')}}"></script>
	<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
	<!-- <script src="{{asset('admin/assets/js/jquery.dataTables.min.js')}}"></script> -->
	{{-- @stack: @push('style'). @yield: @section('style') — most pages use yield; both stay active. --}}
	@stack('style')
	@yield('style')
</head>

<body>
	<div id="global-loader">
		<div class="whirly-loader"> </div>
	</div>
	<script>
		// Hide loader as soon as DOM is ready (do not wait for images/CDN — that felt very slow).
		(function () {
			function hideLoader() {
				var el = document.getElementById('global-loader');
				if (el) {
					el.style.display = 'none';
				}
			}
			if (document.readyState === 'loading') {
				document.addEventListener('DOMContentLoaded', hideLoader);
			} else {
				hideLoader();
			}
			setTimeout(hideLoader, 1200);
		})();
	</script>
	<!-- Main Wrapper -->
	<div class="main-wrapper">

		<!-- Header -->
		@include('partials.admin.header')
		<!-- /Header -->
		@php //print_r(Auth::user()->role_id);die;@endphp
		<!-- Sidebar -->
		@if(Auth::check())
			@if(Auth::user()->role_id == 1)
				@include('partials.admin.sidebar_superadmin')
			@elseif(Auth::user()->role_id == 2)
				@include('partials.admin.sidebar_admin')
			@elseif(Auth::user()->role_id == 3)
				@include('partials.admin.sidebar_storemanager')
			@elseif(Auth::user()->role_id == 5)
				@include('partials.admin.sidebar_clerk')
			@endif
		@endif

		<!-- /Sidebar -->


		@include('partials.admin.model')



		@yield('content')

	</div>
	<!-- /Main Wrapper -->

	@include('partials.admin.footer')
	{{-- @stack: @push('scripts'). @yield: @section('scripts') — do not comment @yield or other pages lose JS. --}}
	@stack('scripts')
	@yield('scripts')
	<script>
		$(document).on('click', '#refresh-page', function (e) {
			e.preventDefault();
			location.reload();
		});
	</script>

</html>