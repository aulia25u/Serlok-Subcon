@extends('adminlte::page')

@section('title', 'Master Menu')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"/>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Master Menu Management</h3>
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

                        <table class="table table-bordered table-striped" id="masterMenuTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Role</th>
                                    <th>Tenant</th>
                                    <th>Menu</th>
                                    <th>Permissions</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
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
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Master Menu</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                @php
                    $restrictedMenus = ['Tenant List Management', 'Tenant Owner Management'];
                @endphp
                <form id="addForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="customer_id">Tenant:</label>
                            @if($isInternal)
                                <select class="form-control" id="customer_id" name="customer_id">
                                    <option value="">Internal (Global)</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                                    @endforeach
                                </select>
                            @else
                                <select class="form-control" id="customer_id" name="customer_id" disabled>
                                    <option value="{{ $currentCustomerId ?? '' }}">
                                        {{ optional($customers->first())->customer_name ?? 'My Customer' }}
                                    </option>
                                </select>
                                <input type="hidden" name="customer_id" value="{{ $currentCustomerId }}">
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="role_id">Role:</label>
                            <select class="form-control" id="role_id" name="role_id" required>
                                <option value="">Select Role</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="menu_id">Menu:</label>
                            <select class="form-control" id="menu_id" name="menu_id" required>
                                <option value="">Select Menu</option>
                                @foreach($menus as $menu)
                                    @if(!$isInternal && in_array($menu->menu_name, $restrictedMenus))
                                        @continue
                                    @endif
                                    <option value="{{ $menu->id }}">{{ $menu->menu_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Permissions:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_create" name="is_create">
                                <label class="form-check-label" for="is_create">Create</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_read" name="is_read" checked>
                                <label class="form-check-label" for="is_read">Read</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_update" name="is_update">
                                <label class="form-check-label" for="is_update">Update</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_delete" name="is_delete">
                                <label class="form-check-label" for="is_delete">Delete</label>
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
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Master Menu</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="editForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_id" name="id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_customer_id">Customer:</label>
                            @if($isInternal)
                                <select class="form-control" id="edit_customer_id" name="customer_id">
                                    <option value="">Internal (Global)</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                                    @endforeach
                                </select>
                            @else
                                <select class="form-control" id="edit_customer_id" name="customer_id" disabled>
                                    <option value="{{ $currentCustomerId ?? '' }}">
                                        {{ optional($customers->first())->customer_name ?? 'My Customer' }}
                                    </option>
                                </select>
                                <input type="hidden" name="customer_id" value="{{ $currentCustomerId }}">
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="edit_role_id">Role:</label>
                            <select class="form-control" id="edit_role_id" name="role_id" required>
                                <option value="">Select Role</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit_menu_id">Menu:</label>
                            <select class="form-control" id="edit_menu_id" name="menu_id" required>
                                <option value="">Select Menu</option>
                                @foreach($menus as $menu)
                                    @if(!$isInternal && in_array($menu->menu_name, $restrictedMenus))
                                        @continue
                                    @endif
                                    <option value="{{ $menu->id }}">{{ $menu->menu_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Permissions:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="edit_is_create" name="is_create">
                                <label class="form-check-label" for="edit_is_create">Create</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="edit_is_read" name="is_read">
                                <label class="form-check-label" for="edit_is_read">Read</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="edit_is_update" name="is_update">
                                <label class="form-check-label" for="edit_is_update">Update</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="edit_is_delete" name="is_delete">
                                <label class="form-check-label" for="edit_is_delete">Delete</label>
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
@stop

@section('js')
    <script type="text/javascript" src="https://cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var table = $('#masterMenuTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('rbac.master-menu.data') }}",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    }
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'role_name', name: 'role_name'},
                    {data: 'customer_name', name: 'customer_name'},
                    {data: 'menu_name', name: 'menu_name'},
                    {data: 'permissions', name: 'permissions', orderable: false},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'updated_at', name: 'updated_at'},
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

            function loadRoles(selectId, customerId, selectedRoleId) {
                const resolved = (customerId === '' || customerId === null) ? 'null' : customerId;

                return $.ajax({
                    url: '{{ route("rbac.roles.by-customer", [ "customer_id" => ":customer" ]) }}'.replace(':customer', resolved),
                    type: 'GET',
                    success: function(data) {
                        const $select = $(selectId);
                        $select.empty().append('<option value="">Select Role</option>');
                        data.forEach(function(role) {
                            $select.append('<option value="' + role.id + '">' + role.role_name + '</option>');
                        });

                        if (selectedRoleId) {
                            $select.val(selectedRoleId);
                        }
                    }
                });
            }

            $('#customer_id').on('change', function() {
                loadRoles('#role_id', $(this).val());
            });

            $('#edit_customer_id').on('change', function() {
                loadRoles('#edit_role_id', $(this).val());
            });

            loadRoles('#role_id', $('#customer_id').val());
            loadRoles('#edit_role_id', $('#edit_customer_id').val());

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
                    url: "{{ route('rbac.master-menu.store') }}",
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
                    url: "{{ url('rbac/master-menu') }}/" + id + "/edit",
                    type: 'GET',
                    success: function(response) {
                        $('#edit_id').val(response.id);
                        $('#edit_menu_id').val(response.menu_id);
                        $('#edit_is_create').prop('checked', response.is_create);
                        $('#edit_is_read').prop('checked', response.is_read);
                        $('#edit_is_update').prop('checked', response.is_update);
                        $('#edit_is_delete').prop('checked', response.is_delete);
                        const customerId = response.customer_id ?? $('#edit_customer_id').val();
                        $('#edit_customer_id').val(customerId);
                        loadRoles('#edit_role_id', customerId, response.role_id);
                        $('#editModal').modal('show');
                    }
                });
            });

            // Edit form submission
            $('#editForm').on('submit', function(e) {
                e.preventDefault();
                var id = $('#edit_id').val();
                $.ajax({
                    url: "{{ url('rbac/master-menu') }}/" + id,
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

            // Delete button click
            $(document).on('click', '.delete-btn', function() {
                var id = $(this).data('id');
                if (confirm('Are you sure you want to delete this item?')) {
                    $.ajax({
                        url: "{{ url('rbac/master-menu') }}/" + id,
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
