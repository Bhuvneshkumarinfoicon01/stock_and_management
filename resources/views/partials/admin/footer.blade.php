

	

	<!-- Bootstrap Core JS -->
	<script src="{{asset('admin/assets/js/bootstrap.bundle.min.js')}}"></script>

	<!-- owl carousel -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js" integrity="sha512-bPs7Ae6pVvhOSiIcyUClR7/q2OAsRiovw4vAkX+zJbw3ShAeeqezq50RIIcIURq7Oa20rW2n2q+fyXBNcU9lrw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

	<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>  -->
	
	<!-- Nice select -->
	<script src="{{asset('admin/assets/js/select2.min.js')}}"></script>

	<!-- Feather Icon JS -->
	<script src="{{asset('admin/assets/js/feather.min.js')}}"></script>

	<!-- Slimscroll JS -->
	<script src="{{asset('admin/assets/js/jquery.slimscroll.min.js')}}"></script>

	{{-- DataTables: core MUST load before dataTables.bootstrap5 (integration reads $.fn.dataTable.defaults). --}}
	{{-- Use same-origin asset() for both so versions always match dataTables.bootstrap5.min.css in head. --}}
	<script src="{{ asset('admin/assets/js/jquery.dataTables.min.js') }}"></script>
	<script src="{{ asset('admin/assets/js/dataTables.bootstrap5.min.js') }}"></script>

	

	<script src="{{asset('admin/assets/js/moment.min.js')}}"></script>
	<script>
		window.appDatePickerFormat = @json($dateFormatConfig['momentFormat'] ?? 'DD-MMM-YYYY');
	</script>
	<script src="{{asset('admin/assets/js/bootstrap-datetimepicker.min.js')}}"></script>
	<script src="{{asset('admin/assets/js/daterangepicker.js')}}"></script>

	<!-- Chart JS -->
	<script src="{{asset('admin/assets/js/apexcharts.min.js')}}"></script>
	<script src="{{asset('admin/assets/js/chart-data.js')}}"></script>

	<!-- Sweetalert 2 -->
	<script src="{{asset('admin/assets/js/sweetalert2.all.min.js')}}"></script>
	<script src="{{asset('admin/assets/js/sweetalerts.min.js')}}"></script>

	<!-- Custom JS -->
	<script src="{{asset('admin/assets/js/script.js')}}"></script>
	<script src="{{ asset('admin/assets/js/datatable-pagination.js') }}"></script>
<script>
	// Function to display SweetAlert messages
    function showAlert(type, message) {
        Swal.fire({
            icon: type,
            title: message,
            timer: 2000,
            showConfirmButton: false
        });
    }
</script>