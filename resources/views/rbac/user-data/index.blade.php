@extends('layouts.app')

@section('title', 'User Data')
@section('page-title', 'User Data')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">User Data Management</h3>
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

                    <table class="table table-bordered table-striped" id="userDataTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Username</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Department</th>
                                <th>Section</th>
                                <th>Position</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Add New User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="userDataForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="full_name">Full Name</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role_id">Role</label>
                                <select class="form-control" id="role_id" name="role_id" required>
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="dept_id">Department</label>
                                <select class="form-control" id="dept_id" name="dept_id" required>
                                    <option value="">Select Department</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->dept_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="section_id">Section</label>
                                <select class="form-control" id="section_id" name="section_id" required>
                                    <option value="">Select Section</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="position_id">Position</label>
                                <select class="form-control" id="position_id" name="position_id" required>
                                    <option value="">Select Position</option>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="gender">Gender</label>
                                <select class="form-control" id="gender" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
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

            var table = $('#userDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('rbac.user-data') }}",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    }
                },
                columns: [
                    {data: 'no', name: 'no'},
                    {data: 'username', name: 'username'},
                    {data: 'full_name', name: 'full_name'},
                    {data: 'email', name: 'email'},
                    {data: 'role_name', name: 'role_name'},
                    {data: 'dept_name', name: 'dept_name'},
                    {data: 'section_name', name: 'section_name'},
                    {data: 'position_name', name: 'position_name'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                pageLength: 10,
                responsive: true
            });

            $('#filterBtn').click(function() {
                table.draw();
            });

            $('#resetBtn').click(function() {
                $('#start_date, #end_date').val('');
                table.draw();
            });

            function loadSections(deptId, selectedSectionId, selectedPositionId) {
                if (!deptId) {
                    $('#section_id').empty().append('<option value="">Select Section</option>');
                    $('#position_id').empty().append('<option value="">Select Position</option>');
                    return $.Deferred().resolve();
                }

                return $.ajax({
                    url: '{{ route("rbac.sections.by-department", ":id") }}'.replace(':id', deptId),
                    type: 'GET',
                    success: function(data) {
                        $('#section_id').empty().append('<option value="">Select Section</option>');
                        $.each(data, function(key, value) {
                            $('#section_id').append('<option value="' + value.id + '">' + value.section_name + '</option>');
                        });

                        if (selectedSectionId) {
                            $('#section_id').val(selectedSectionId);
                        }

                        $('#position_id').empty().append('<option value="">Select Position</option>');

                        var sectionIdToLoad = selectedSectionId || $('#section_id').val();
                        if (sectionIdToLoad) {
                            loadPositions(sectionIdToLoad, selectedPositionId);
                        }
                    }
                });
            }

            function loadPositions(sectionId, selectedPositionId) {
                if (!sectionId) {
                    $('#position_id').empty().append('<option value="">Select Position</option>');
                    return $.Deferred().resolve();
                }

                return $.ajax({
                    url: '{{ route("rbac.positions.by-section", ":id") }}'.replace(':id', sectionId),
                    type: 'GET',
                    success: function(data) {
                        $('#position_id').empty().append('<option value="">Select Position</option>');
                        $.each(data, function(key, value) {
                            $('#position_id').append('<option value="' + value.id + '">' + value.position_name + '</option>');
                        });

                        if (selectedPositionId) {
                            $('#position_id').val(selectedPositionId);
                        }
                    }
                });
            }

            function populateSections(sections, selectedSectionId) {
                $('#section_id').empty().append('<option value="">Select Section</option>');
                sections.forEach(function(section) {
                    $('#section_id').append('<option value="' + section.id + '">' + section.section_name + '</option>');
                });
                if (selectedSectionId) {
                    $('#section_id').val(selectedSectionId);
                }
            }

            function populatePositions(positions, selectedPositionId) {
                $('#position_id').empty().append('<option value="">Select Position</option>');
                positions.forEach(function(position) {
                    $('#position_id').append('<option value="' + position.id + '">' + position.position_name + '</option>');
                });
                if (selectedPositionId) {
                    $('#position_id').val(selectedPositionId);
                }
            }

            $('#dept_id').on('change', function() {
                var deptId = $(this).val();
                loadSections(deptId);
            });

            $('#section_id').on('change', function() {
                var sectionId = $(this).val();
                loadPositions(sectionId);
            });
            
            $('#addModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var modal = $(this);
                if (button.hasClass('edit-btn')) {
                    modal.find('.modal-title').text('Edit User');
                    var userId = button.data('id');

                    $.ajax({
                        url: "{{ route('rbac.user-data.edit', ':id') }}".replace(':id', userId),
                        type: 'GET',
                        success: function(response) {
                            // console.log('Edit response:', response); // Debug log - commented out
                            console.log('Edit response:', response); // Debug log
                            $('#userDataForm').attr('action', "{{ route('rbac.user-data.update', ':id') }}".replace(':id', response.user.id));
                            $('#userDataForm').find('input[name="_method"]').remove();
                            $('#userDataForm').append('<input type="hidden" name="_method" value="PUT">');

                            // Populate basic user information with null checks
                            $('#username').val(response.user.username || '');
                            $('#email').val(response.user.email || '');
                            $('#full_name').val(response.user.user_detail ? response.user.user_detail.employee_name || '' : '');
                            $('#gender').val(response.user.user_detail ? response.user.user_detail.gender || '' : '');
                            $('#role_id').val(response.user.user_detail ? response.user.user_detail.role_id || '' : '');

                            // Handle department/section/position population
                            if (response.user.user_detail && response.user.user_detail.position && response.user.user_detail.position.section) {
                                var deptId = response.user.user_detail.position.section.dept_id;
                                var sectionId = response.user.user_detail.position.section_id;
                                var positionId = response.user.user_detail.position_id;

                                $('#dept_id').val(deptId);
                                if (response.sections) {
                                    populateSections(response.sections, sectionId);
                                } else {
                                    loadSections(deptId, sectionId, positionId);
                                }
                                if (response.positions) {
                                    populatePositions(response.positions, positionId);
                                }
                            } else {
                                // Clear organizational fields if no data
                                $('#dept_id').val('');
                                $('#section_id').empty().append('<option value="">Select Section</option>');
                                $('#position_id').empty().append('<option value="">Select Position</option>');

                                if (response.message) {
                                    toastr.warning(response.message);
                                }
                            }

                            // Make password field optional when editing
                            $('#password').closest('.form-group').show();
                            $('#password').removeAttr('required');
                            $('#password').attr('placeholder', 'Leave blank to keep current password');
                        },
                        error: function(xhr) {
                            console.error('Error loading user data:', xhr.responseText);
                            toastr.error('Failed to load user data for editing.');
                        }
                    });
                } else {
                    modal.find('.modal-title').text('Add New User');
                    $('#userDataForm').trigger('reset');
                    $('#userDataForm').find('input[name="_method"]').remove();
                    $('#userDataForm').attr('action', "{{ route('rbac.user-data.store') }}");

                    // Reset dropdowns
                    $('#section_id').empty().append('<option value="">Select Section</option>');
                    $('#position_id').empty().append('<option value="">Select Position</option>');

                    // Show password field when adding new user
                    $('#password').closest('.form-group').show();
                }
            });

            $('#userDataForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var url = form.attr('action');
                var method = form.find('input[name="_method"]').val() || 'POST';
                var data = form.serialize();

                // If editing, exclude password if it's empty
                if (method === 'PUT') {
                    if (!$('#password').val()) {
                        data = data.replace(/&?password=.*?(?=(&|$))/, '');
                    }
                }

                $.ajax({
                    url: url,
                    type: method,
                    data: data,
                    success: function(response) {
                        $('#addModal').modal('hide');
                        table.ajax.reload(null, false);
                        toastr.success(response.success);
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                toastr.error(value[0]);
                            });
                        } else if (xhr.responseJSON && xhr.responseJSON.error) {
                            toastr.error(xhr.responseJSON.error);
                        } else {
                            toastr.error('An unexpected error occurred.');
                        }
                    }
                });
            });

            $(document).on('click', '.delete-btn', function() {
                var userId = $(this).data('id');
                if (confirm('Are you sure you want to delete this user?')) {
                    $.ajax({
                        url: "{{ route('rbac.user-data.destroy', ':id') }}".replace(':id', userId),
                        type: 'POST',
                        data: {
                            _method: 'DELETE',
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            table.ajax.reload(null, false);
                            toastr.success(response.success);
                        },
                        error: function(xhr) {
                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                toastr.error(xhr.responseJSON.error);
                            } else {
                                toastr.error('Something went wrong.');
                            }
                        }
                    });
                }
            });
        });
    </script>
@stop
