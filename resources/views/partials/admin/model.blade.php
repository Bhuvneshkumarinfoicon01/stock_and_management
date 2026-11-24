<div class="modal fade" id="add-stores">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable custom-modal-two">
                <div class="modal-content">
                    <div class="page-wrapper-new p-0">
                        <div class="content">
                            <div class="modal-header border-0 custom-modal-header">
                                <div class="page-title">
                                    <h4>Create Store</h4>
                                </div>
                                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body custom-modal-body scroll-modal">
                               
                                <form id="createStoreForm">
                                    @csrf
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Store Name</label>
                                        <input type="text" name="name" class="form-control">
                                        <span class="error-message text-danger name-error"></span> <!-- Error message -->
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Address</label>
                                        <input type="text" name="address" class="form-control">
                                        <span class="error-message text-danger address-error"></span>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">City</label>
                                        <input type="text" name="city" class="form-control">
                                        <span class="error-message text-danger city-error"></span>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">State</label>
                                        <input type="text" name="state" class="form-control">
                                        <span class="error-message text-danger state-error"></span>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Zip Code</label>
                                        <input type="text" name="zipcode" class="form-control" inputmode="numeric" pattern="[0-9]*">
                                        <span class="error-message text-danger zipcode-error"></span>
                                    </div>
                                    

                                    <div class="mb-3">
                                        <label class="form-label">Phone</label>
                                        <input type="text" name="phone" class="form-control">
                                        <span class="error-message text-danger phone-error"></span>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control">
                                        <span class="error-message text-danger email-error"></span>
                                    </div>

                                    @include('admin.partials.store-document-sequences-fields')

                                    <div class="mb-0">
                                        <div class="status-toggle modal-status d-flex justify-content-between align-items-center">
                                            <span class="status-label">Status</span>
                                            <input type="checkbox" id="user2" class="check" checked>
                                            <label for="user2" class="checktoggle"></label>
                                        </div>
                                    </div>

                                    <div class="modal-footer-btn">
                                        <button type="button" class="btn btn-cancel me-2" data-bs-dismiss="modal">Cancel</button>
                                        <button type="button" id="createStoreBtn" class="btn btn-submit">Create</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <script>
            $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#createStoreBtn').click(function () {
                let formData = {
                    name: $('input[name="name"]').val(),
                    address: $('input[name="address"]').val(),
                    city: $('input[name="city"]').val(),
                    state: $('input[name="state"]').val(),
                    zipcode: $('input[name="zipcode"]').val(),
                    phone: $('input[name="phone"]').val(),
                    email: $('input[name="email"]').val(),
                    invoice_prefix: $('input[name="invoice_prefix"]').val(),
                    sale_invoice_start_number: $('input[name="sale_invoice_start_number"]').val(),
                    purchase_invoice_prefix: $('input[name="purchase_invoice_prefix"]').val(),
                    purchase_invoice_start_number: $('input[name="purchase_invoice_start_number"]').val(),
                    purchase_order_prefix: $('input[name="purchase_order_prefix"]').val(),
                    purchase_order_start_number: $('input[name="purchase_order_start_number"]').val(),
                    purchase_return_prefix: $('input[name="purchase_return_prefix"]').val(),
                    purchase_return_start_number: $('input[name="purchase_return_start_number"]').val(),
                    grn_prefix: $('input[name="grn_prefix"]').val(),
                    grn_start_number: $('input[name="grn_start_number"]').val(),
                    credit_note_prefix: $('input[name="credit_note_prefix"]').val(),
                    credit_note_start_number: $('input[name="credit_note_start_number"]').val(),
                    debit_note_prefix: $('input[name="debit_note_prefix"]').val(),
                    debit_note_start_number: $('input[name="debit_note_start_number"]').val(),
                    receipt_prefix: $('input[name="receipt_prefix"]').val(),
                    receipt_start_number: $('input[name="receipt_start_number"]').val(),
                    batch_purchase_prefix: $('input[name="batch_purchase_prefix"]').val(),
                    batch_purchase_start_number: $('input[name="batch_purchase_start_number"]').val(),
                    status: $('#user2').is(':checked') ? 1 : 0,
                    _token: $('input[name="_token"]').val()
                };
                
                $.ajax({
                    type: 'POST',
                    url: "{{ route('admin.stores_store') }}",
                    data: formData,
                    beforeSend: function () {
                        $('.btn-submit').prop('disabled', true).text('Saving...');
                        $('.error-message').text('');
                    },
                    success: function (response) {
                        if (response.status === "success") {
                            if (response.store && typeof window.appendStoreToHeaderDropdown === 'function') {
                                window.appendStoreToHeaderDropdown(response.store);
                            }
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then(() => {
                                $('#createStoreForm')[0].reset();
                                $('#add-stores').modal('hide');
                                // Refresh the All Stores table without a full page reload.
                                if (typeof window.reloadStoreList === 'function') {
                                    window.reloadStoreList();
                                }
                            });
                        }
                        $('.btn-submit').prop('disabled', false).text('Create');
                    },
                    error: function (xhr) {
                        $('.btn-submit').prop('disabled', false).text('Create');
                        let errors = xhr.responseJSON.errors;
                        if (errors) {
                            $('.error-message').text('');
                            $.each(errors, function (key, messages) {
                                $('.' + key + '-error').text(messages[0]);
                            });
                        }
                    }
                });
            });

        });
        </script>
