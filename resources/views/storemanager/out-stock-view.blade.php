@extends('layouts.app_admin') @section('title') Dashbaord @endsection
@section('styles') @endsection @section('content')

<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4>Out Of Stock</h4>
                    <h6>Manage your Stocks</h6>
                </div>
            </div>
        </div>

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
                </div>
                <div class="table-responsive">
                    <table class="table datanew" id="out-of-table">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Instock Qty</th>
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
    let table = $('#out-of-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.outOfStock-view') }}",
            // data: function (d) {
            //     d.date_from = $('#date_from').val();  
            //         d.date_to = $('#date_to').val(); 
            //     d.store_id = $('#storeSelect').val(); 
            //     d.order_type = $('#sort-order').val();
            // }
        },
        columns: [
            { data: 'product_info', name: 'product_info' },
            { data: 'quantity', name: 'quantity' },
            
        ],
        pageLength: 10,
        initComplete: function () {
                let searchWrapper = $('#out-of-table_filter');
                let defaultSearchBox = searchWrapper.find('input');
                $('#custom-search').append(defaultSearchBox);
                defaultSearchBox.attr('placeholder', 'Search Products...');
                searchWrapper.remove();
            }
    });
    // $('#searchBtn').on('click', function () {
    //     table.ajax.reload();
    // });

});
</script>
@endsection
