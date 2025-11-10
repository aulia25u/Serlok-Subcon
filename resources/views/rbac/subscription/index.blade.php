@extends('adminlte::page')

@section('title', 'Subscription Management')

@section('page-title', 'Subscription Management')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css"/>
    <style>
        .pdf-viewer {
            width: 100%;
            height: 500px;
            border: 1px solid #ccc;
        }
    </style>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Subscription Management</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addModal">
                                <i class="fas fa-plus"></i> Add New
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped" id="subscriptionTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Customer</th>
                                    <th>Invoice Date</th>
                                    <th>Valid Until</th>
                                    <th>Income</th>
                                    <th>Status</th>
                                    <th>Invoice PDF</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Modal --}}
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Subscription</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="addForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="customer_id">Customer:</label>
                            <select class="form-control" id="customer_id" name="customer_id" required>
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="invoice_date">Invoice Date:</label>
                            <input type="date" class="form-control" id="invoice_date" name="invoice_date" required>
                        </div>
                        <div class="form-group">
                            <label for="income">Income (Rp):</label>
                            <input type="number" class="form-control" id="income" name="income" min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="status">Status Subscription:</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="active">Active</option>
                                <option value="non-active">Non-Active</option>
                                <option value="penagihan">Penagihan</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="invoice_file">Invoice PDF:</label>
                            <input type="file" class="form-control-file" id="invoice_file" name="invoice_file" accept="application/pdf" required>
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

    {{-- Edit Modal --}}
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Subscription</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="editForm" enctype="multipart/form-data">
                    @csrf
                    @method('POST') {{-- Use POST for file upload, _method will handle PUT --}}
                    <input type="hidden" id="edit_id" name="id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_customer_id">Customer:</label>
                            <select class="form-control" id="edit_customer_id" name="customer_id" required>
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit_invoice_date">Invoice Date:</label>
                            <input type="date" class="form-control" id="edit_invoice_date" name="invoice_date" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_income">Income (Rp):</label>
                            <input type="number" class="form-control" id="edit_income" name="income" min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_status">Status Subscription:</label>
                            <select class="form-control" id="edit_status" name="status" required>
                                <option value="active">Active</option>
                                <option value="non-active">Non-Active</option>
                                <option value="penagihan">Penagihan</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit_invoice_file">Invoice PDF (optional):</label>
                            <input type="file" class="form-control-file" id="edit_invoice_file" name="invoice_file" accept="application/pdf">
                            <small class="form-text text-muted">Leave blank to keep current PDF.</small>
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

    {{-- View Modal --}}
    <div class="modal fade" id="viewModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">View Subscription</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Customer:</strong> <span id="view_customer_name"></span></p>
                    <p><strong>Invoice Date:</strong> <span id="view_invoice_date"></span></p>
                    <p><strong>Valid Until:</strong> <span id="view_valid_until"></span></p>
                    <p><strong>Income:</strong> <span id="view_income"></span></p>
                    <p><strong>Status:</strong> <span id="view_status"></span></p>
                    <p><strong>Invoice:</strong> <a id="view_invoice_link" href="#" target="_blank" class="btn btn-sm btn-info"><i class="fas fa-file-pdf"></i> View PDF</a></p>
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
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var table = $('#subscriptionTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('rbac.subscription.data') }}",
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'customer_name', name: 'customer.name'},
                    {data: 'invoice_date_formatted', name: 'invoice_date'},
                    {data: 'valid_until_formatted', name: 'valid_until'},
                    {data: 'income_formatted', name: 'income'},
                    {data: 'status_badge', name: 'status', orderable: false, searchable: false},
                    {data: 'invoice_pdf', name: 'invoice_path', orderable: false, searchable: false},
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

            // Add form submission
            $('#addForm').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                    url: "{{ route('rbac.subscription.store') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
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
                    url: "{{ url('rbac/subscription') }}/" + id + "/edit",
                    type: 'GET',
                    success: function(response) {
                        $('#edit_id').val(response.id);
                        $('#edit_customer_id').val(response.customer_id);
                        $('#edit_invoice_date').val(response.invoice_date);
                        $('#edit_income').val(response.income);
                        $('#edit_status').val(response.status);
                        $('#editModal').modal('show');
                    }
                });
            });

            // Edit form submission
            $('#editForm').on('submit', function(e) {
                e.preventDefault();
                var id = $('#edit_id').val();
                var formData = new FormData(this);
                formData.append('_method', 'POST'); // Laravel expects POST for file uploads with PUT/PATCH

                $.ajax({
                    url: "{{ url('rbac/subscription') }}/" + id,
                    type: 'POST', // Use POST for file uploads
                    data: formData,
                    processData: false,
                    contentType: false,
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
                    url: "{{ url('rbac/subscription') }}/" + id,
                    type: 'GET',
                    success: function(response) {
                        $('#view_customer_name').text(response.customer.name);
                        $('#view_invoice_date').text(response.invoice_date);
                        $('#view_valid_until').text(response.valid_until);
                        $('#view_income').text(response.income ? 'Rp ' + new Intl.NumberFormat('id-ID').format(response.income) : '-');
                        let badgeClass = 'badge-secondary';
                        if (response.status == 'active') {
                            badgeClass = 'badge-success';
                        } else if (response.status == 'penagihan') {
                            badgeClass = 'badge-warning';
                        }
                        $('#view_status').html('<span class="badge ' + badgeClass + '">' + response.status.charAt(0).toUpperCase() + response.status.slice(1) + '</span>');
                        if (response.invoice_path) {
                            $('#view_invoice_link').attr('href', '/storage/' + response.invoice_path.replace('public/', ''));
                            $('#view_invoice_link').show();
                        } else {
                            $('#view_invoice_link').hide();
                        }
                        $('#viewModal').modal('show');
                    }
                });
            });

            // Delete button click
            $(document).on('click', '.delete-btn', function() {
                var id = $(this).data('id');
                if (confirm('Are you sure you want to delete this item?')) {
                    $.ajax({
                        url: "{{ url('rbac/subscription') }}/" + id,
                        type: 'POST',
                        data: {
                            _method: 'DELETE'
                        },
                        success: function(response) {
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
                }
            });
        });
    </script>
@stop