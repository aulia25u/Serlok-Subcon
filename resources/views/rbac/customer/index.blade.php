@extends('adminlte::page')

@section('title', 'Customer Management')

@section('page-title', 'Customer Management')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container .select2-selection--single {
            height: calc(2.25rem + 2px);
        }
    </style>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Customer Management</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addModal">
                                <i class="fas fa-plus"></i> Add New
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="start_date">Start Date:</label>
                                <input type="date" class="form-control" id="start_date" name="start_date">
                            </div>
                            <div class="col-md-3">
                                <label for="end_date">End Date:</label>
                                <input type="date" class="form-control" id="end_date" name="end_date">
                            </div>
                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="button" class="btn btn-info" id="filterBtn">
                                        <i class="fas fa-filter"></i> Filter
                                    </button>
                                    <button type="button" class="btn btn-secondary" id="resetBtn">
                                        <i class="fas fa-undo"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </div>

                        <table class="table table-bordered table-striped" id="customerTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Customer</th>
                                    <th>Owner</th>
                                    <th>Kategory</th>
                                    <th>Sub-Category</th>
                                    <th>Join Date</th>
                                    <th>ChatID</th>
                                    <th>Marketing</th>
                                    <th>Status</th>
                                    <th>POS Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Customer</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="addForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Customer:</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="owner">Owner:</label>
                                    <input type="text" class="form-control" id="owner" name="owner">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="business_category">Kategory:</label>
                                    <input type="text" class="form-control" id="business_category" name="business_category" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sub_business_category">Sub-Category:</label>
                                    <input type="text" class="form-control" id="sub_business_category" name="sub_business_category" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="join_date">Join Date:</label>
                                    <input type="date" class="form-control" id="join_date" name="join_date" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="telegram_chat_id">Chat ID:</label>
                                    <input type="text" class="form-control" id="telegram_chat_id" name="telegram_chat_id">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user_marketing_id">Marketing:</label>
                                    <select class="form-control select2" id="user_marketing_id" name="user_marketing_id" style="width: 100%;">
                                        <option value="">Select User Marketing</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status:</label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="active">Active</option>
                                        <option value="non-active">Non-Active</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pos_status">POS Status:</label>
                                    <select class="form-control" id="pos_status" name="pos_status" required>
                                        <option value="ready">Ready</option>
                                        <option value="not_ready">Not Ready</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Customer</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="editForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_id" name="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_name">Customer:</label>
                                    <input type="text" class="form-control" id="edit_name" name="name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_owner">Owner:</label>
                                    <input type="text" class="form-control" id="edit_owner" name="owner">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_business_category">Kategory:</label>
                                    <input type="text" class="form-control" id="edit_business_category" name="business_category" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_sub_business_category">Sub-Category:</label>
                                    <input type="text" class="form-control" id="edit_sub_business_category" name="sub_business_category" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_join_date">Join Date:</label>
                                    <input type="date" class="form-control" id="edit_join_date" name="join_date" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_telegram_chat_id">Chat ID:</label>
                                    <input type="text" class="form-control" id="edit_telegram_chat_id" name="telegram_chat_id">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_user_marketing_id">Marketing:</label>
                                    <select class="form-control select2" id="edit_user_marketing_id" name="user_marketing_id" style="width: 100%;">
                                        <option value="">Select User Marketing</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_status">Status:</label>
                                    <select class="form-control" id="edit_status" name="status" required>
                                        <option value="active">Active</option>
                                        <option value="non-active">Non-Active</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_pos_status">POS Status:</label>
                                    <select class="form-control" id="edit_pos_status" name="pos_status" required>
                                        <option value="ready">Ready</option>
                                        <option value="not_ready">Not Ready</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Modal -->
    <div class="modal fade" id="viewModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Customer Details</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>ID:</strong></label>
                                <p id="view_id"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Customer:</strong></label>
                                <p id="view_name"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Kategory:</strong></label>
                                <p id="view_business_category"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Sub-Category:</strong></label>
                                <p id="view_sub_business_category"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Join Date:</strong></label>
                                <p id="view_join_date"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>ChatID:</strong></label>
                                <p id="view_telegram_chat_id"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Created At:</strong></label>
                                <p id="view_created_at"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Updated At:</strong></label>
                                <p id="view_updated_at"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Status:</strong></label>
                                <p id="view_status"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>POS Status:</strong></label>
                                <p id="view_pos_status"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Marketing:</strong></label>
                                <p id="view_user_marketing_name"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Owner:</strong></label>
                                <p id="view_owner"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script type="text/javascript" src="https://cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Initialize Select2 with AJAX
            $('#user_marketing_id').select2({
                placeholder: "Select User Marketing",
                allowClear: true,
                dropdownParent: $('#addModal .modal-content'),
                ajax: {
                    url: "{{ route('rbac.customer.getUsers') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            search: params.term // search term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.results
                        };
                    },
                    cache: true
                },
                minimumInputLength: 0
            });

            $('#edit_user_marketing_id').select2({
                placeholder: "Select User Marketing",
                allowClear: true,
                dropdownParent: $('#editModal .modal-content'),
                ajax: {
                    url: "{{ route('rbac.customer.getUsers') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            search: params.term // search term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.results
                        };
                    },
                    cache: true
                },
                minimumInputLength: 0
            });

            var table = $('#customerTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('rbac.customer') }}",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'owner', name: 'owner'},
                    {data: 'business_category', name: 'business_category'},
                    {data: 'sub_business_category', name: 'sub_business_category'},
                    {data: 'join_date_formatted', name: 'join_date'},
                    {data: 'telegram_chat_id', name: 'telegram_chat_id'},
                    {data: 'user_marketing_name', name: 'user_marketing_name'},
                    {data: 'status_badge', name: 'status', orderable: false, searchable: false},
                    {data: 'pos_status_badge', name: 'pos_status', orderable: false, searchable: false},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                responsive: true,
                autoWidth: false,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                dom: 'Bfrtip',
                buttons: [
                    'excelHtml5',
                    'print'
                ],
                language: {
                    processing: "Loading...",
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                }
            });

            // Filter functionality
            $('#filterBtn').click(function() {
                table.draw();
            });

            $('#resetBtn').click(function() {
                $('#start_date').val('');
                $('#end_date').val('');
                table.draw();
            });

            // Add form submission
            $('#addForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('rbac.customer.store') }}",
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#addModal').modal('hide');
                        table.draw();
                        toastr.success(response.success);
                        $('#addForm')[0].reset();
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            toastr.error(value[0]);
                        });
                    }
                });
            });

            // Edit button click
            $(document).on('click', '.edit-btn', function() {
                var id = $(this).data('id');
                $.ajax({
                    url: "{{ url('rbac/customer') }}/" + id + "/edit",
                    type: 'GET',
                    success: function(response) {
                        $('#edit_id').val(response.id);
                        $('#edit_name').val(response.name);
                        $('#edit_business_category').val(response.business_category);
                        $('#edit_sub_business_category').val(response.sub_business_category);
                        $('#edit_join_date').val(response.join_date.substring(0, 10)); // Format date for input type="date"
                        $('#edit_telegram_chat_id').val(response.telegram_chat_id);
                        $('#edit_status').val(response.status); // Populate status
                        $('#edit_pos_status').val(response.pos_status); // Populate POS status
                        $('#edit_user_marketing_id').val(response.user_marketing_id);
                        $('#edit_owner').val(response.owner);
                        $('#editModal').modal('show');
                    }
                });
            });

            // Edit form submission
            $('#editForm').on('submit', function(e) {
                e.preventDefault();
                var id = $('#edit_id').val();
                $.ajax({
                    url: "{{ url('rbac/customer') }}/" + id,
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#editModal').modal('hide');
                        table.draw();
                        toastr.success(response.success);
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            toastr.error(value[0]);
                        });
                    }
                });
            });

            // View button click
            $(document).on('click', '.view-btn', function() {
                var id = $(this).data('id');
                $.ajax({
                    url: "{{ url('rbac/customer') }}/" + id,
                    type: 'GET',
                    success: function(response) {
                        $('#view_id').text(response.id);
                        $('#view_name').text(response.name);
                        $('#view_business_category').text(response.business_category);
                        $('#view_sub_business_category').text(response.sub_business_category);
                        $('#view_join_date').text(response.join_date_formatted || response.join_date);
                        $('#view_telegram_chat_id').text(response.telegram_chat_id || '-');
                        $('#view_created_at').text(response.created_at_formatted || response.created_at);
                        $('#view_updated_at').text(response.updated_at_formatted || response.updated_at);
                        $('#view_status').html(response.status_badge || response.status);
                        $('#view_pos_status').html(response.pos_status_badge || response.pos_status);
                        $('#view_user_marketing_name').text(response.user_marketing_name || '-');
                        $('#view_owner').text(response.owner || '-');
                        $('#viewModal').modal('show');
                    },
                    error: function(xhr) {
                        toastr.error('Failed to load customer data.');
                    }
                });
            });

            // Delete button click
            $(document).on('click', '.delete-btn', function() {
                var id = $(this).data('id');
                if (confirm('Are you sure you want to delete this item?')) {
                    $.ajax({
                        url: "{{ url('rbac/customer') }}/" + id,
                        type: 'POST',
                        data: {
                            _method: 'DELETE'
                        },
                        success: function(response) {
                            table.draw();
                            toastr.success(response.success);
                        }
                    });
                }
            });
        });
    </script>
@stop
