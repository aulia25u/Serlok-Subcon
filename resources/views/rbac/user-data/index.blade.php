@extends('layouts.app')

@section('title', 'User Data')

@section('css')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@stop

@section('content')
@php
    $isInternal = is_null($currentCustomerId ?? null);
@endphp
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h1 class="card-title">User Data Management</h1>
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
                            <input type="date" class="form-control" id="userData_start_date" name="start_date">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date">End Date:</label>
                            <input type="date" class="form-control" id="userData_end_date" name="end_date">
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
                                <th>Tenant</th>
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
                <input type="hidden" id="userData_id" name="id">
                <input type="hidden" id="userData_form_method" name="_method" value="POST">
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
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="customer_id">Tenant</label>
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
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="dept_id">Department</label>
                                <select class="form-control" id="dept_id" name="dept_id" required>
                                    <option value="">Select Department</option>
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role_id">Role</label>
                                <select class="form-control" id="role_id" name="role_id" required>
                                    <option value="">Select Role</option>
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

<script src="{{ asset('js/crud-manager.js') }}"></script>
<script>
    $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

        const isInternalUser = @json($isInternal);
        const currentCustomerId = @json($currentCustomerId);

        // Helper functions for dropdowns
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

        function loadDepartments(customerId, selectedDeptId, selectedSectionId, selectedPositionId) {
                const deptSelect = $('#dept_id');
        const sectionSelect = $('#section_id');
        const positionSelect = $('#position_id');
        const resolvedCustomer = customerId === '' ? 'null' : (customerId ?? 'null');

        if (customerId === undefined) {
            deptSelect.empty().append('<option value="">Select Department</option>');
        sectionSelect.empty().append('<option value="">Select Section</option>');
        positionSelect.empty().append('<option value="">Select Position</option>');
        return $.Deferred().resolve();
                }

        return $.ajax({
            url: '{{ route("rbac.departments.by-customer", ":id") }}'.replace(':id', resolvedCustomer),
        type: 'GET',
        success: function(data) {
            deptSelect.empty().append('<option value="">Select Department</option>');
        $.each(data, function(key, value) {
            deptSelect.append('<option value="' + value.id + '">' + value.dept_name + '</option>');
                        });

        if (selectedDeptId) {
            deptSelect.val(selectedDeptId);
        loadSections(selectedDeptId, selectedSectionId, selectedPositionId);
                        } else {
            sectionSelect.empty().append('<option value="">Select Section</option>');
        positionSelect.empty().append('<option value="">Select Position</option>');
                        }
                    }
                });
            }

        function loadRoles(customerId, selectedRoleId) {
                const resolvedCustomer = customerId === '' ? 'null' : (customerId ?? 'null');

        return $.ajax({
            url: '{{ route("rbac.roles.by-customer", ":id") }}'.replace(':id', resolvedCustomer),
        type: 'GET',
        success: function(data) {
            $('#role_id').empty().append('<option value="">Select Role</option>');
        $.each(data, function(key, value) {
            $('#role_id').append('<option value="' + value.id + '">' + value.role_name + '</option>');
                        });
        if (selectedRoleId) {
            $('#role_id').val(selectedRoleId);
                        }
                    }
                });
            }

        // Event Listeners for Dropdowns
        $('#customer_id').on('change', function() {
                const customerId = $(this).val();
        loadDepartments(customerId).then(function() {
            loadRoles(customerId);
                });
            });

        $('#dept_id').on('change', function() {
                var deptId = $(this).val();
        loadSections(deptId);
            });

        $('#section_id').on('change', function() {
                var sectionId = $(this).val();
        loadPositions(sectionId);
            });

        // Initial Load
        const initialCustomerId = $('#customer_id').val();
        loadDepartments(initialCustomerId).then(function() {
            loadRoles(initialCustomerId);
            });

        // Initialize CrudManager
        new CrudManager({
            entity: 'userData', // Matches IDs: userDataTable, userDataForm, addModal (special handling needed)
        routes: {
            index: "{{ route('rbac.user-data') }}",
        store: "{{ route('rbac.user-data.store') }}",
                },
        columns: [
        {data: 'no', name: 'no'},
        {data: 'customer_name', name: 'customer_name'},
        {data: 'username', name: 'username'},
        {data: 'full_name', name: 'full_name'},
        {data: 'email', name: 'email'},
        {data: 'role_name', name: 'role_name'},
        {data: 'dept_name', name: 'dept_name'},
        {data: 'section_name', name: 'section_name'},
        {data: 'position_name', name: 'position_name'},
        {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        modalId: '#addModal',
        formId: '#userDataForm',
        tableId: '#userDataTable',
        filterBtnId: '#filterBtn',
        resetBtnId: '#resetBtn',
        addBtnId: '[data-target="#addModal"]', // Select by attribute since it doesn't have a unique ID in original code
        dateFilters: true,
        onAdd: function() {
            $('#addModalLabel').text('Add New User');

        // Reset dropdowns
        $('#section_id').empty().append('<option value="">Select Section</option>');
        $('#position_id').empty().append('<option value="">Select Position</option>');

        if (isInternalUser) {
            $('#customer_id').val('');
                    } else {
            $('#customer_id').val(currentCustomerId || '');
                    }
        loadDepartments($('#customer_id').val()).then(function() {
            loadRoles($('#customer_id').val());
                    });

        // Show password field
        $('#password').closest('.form-group').show();
        $('#password').attr('required', 'required');
        $('#password').removeAttr('placeholder');
                },
        onEdit: function(response) {
            $('#addModalLabel').text('Edit User');

        // Populate basic user information
        $('#username').val(response.user.username || '');
        $('#email').val(response.user.email || '');
        $('#full_name').val(response.user.user_detail ? response.user.user_detail.employee_name || '' : '');
        $('#gender').val(response.user.user_detail ? response.user.user_detail.gender || '' : '');
        $('#role_id').val(response.user.user_detail ? response.user.user_detail.role_id || '' : '');

        // Handle department/section/position population
        var customerId = response.customer_id ?? '';
        if (response.user.user_detail && response.user.user_detail.position && response.user.user_detail.position.section) {
                        var deptId = response.user.user_detail.position.section.dept_id;
        var sectionId = response.user.user_detail.position.section_id;
        var positionId = response.user.user_detail.position_id;

        $('#customer_id').val(customerId);
        loadDepartments(customerId, deptId, sectionId, positionId).then(function() {
            loadRoles(customerId, response.user.user_detail.role_id);
                        });
                    } else {
            // Clear organizational fields if no data
            $('#customer_id').val(customerId);
        loadDepartments(customerId);
        $('#dept_id').val('');
        $('#section_id').empty().append('<option value="">Select Section</option>');
        $('#position_id').empty().append('<option value="">Select Position</option>');

        if (response.message) {
            toastr.warning(response.message);
                        }
        loadRoles(customerId);
                    }

        // Make password field optional when editing
        $('#password').closest('.form-group').show();
        $('#password').removeAttr('required');
        $('#password').attr('placeholder', 'Leave blank to keep current password');
                },
        onModalHidden: function() {
            // Reset password field state
            $('#password').attr('required', 'required');
        $('#password').removeAttr('placeholder');
                }
            });

            // Override the default edit button listener from CrudManager because the button class is 'edit-btn' not 'userData-edit-btn'
            // and the ID is 'userDataTable' but the entity is 'userData' so CrudManager expects 'userData-edit-btn'
            // We need to manually handle the edit click or change the class in the HTML.
            // Changing the class in the HTML is cleaner but requires modifying the controller or the view where the button is generated.
            // The controller generates the button HTML.

            // Actually, CrudManager uses `.${self.entity}-edit-btn`.
        // If entity is 'userData', it looks for `.userData-edit-btn`.
        // The controller generates `.edit-btn`.
        // So we need to either change the controller or add a delegated listener that triggers the CrudManager logic.
        // Or, simpler: Just add the class 'userData-edit-btn' to the buttons via JS after draw, OR change the entity name in CrudManager config to match?
        // No, 'edit-btn' is generic.

        // Let's manually bind the edit button to the CrudManager logic since we can't easily change the controller output right now without another file edit.
        // Wait, I AM editing the controller later. I can change the class there!
        // But for now, I will add a compatibility layer here.

        $(document).on('click', '.edit-btn', function() {
            // Trigger the logic that CrudManager would have triggered
            // But CrudManager instance is local.
            // I'll attach the instance to the DOM element or make it accessible?
            // Or just copy the logic? No, that defeats the purpose.

            // Better approach: Update the controller to output `userData-edit-btn` and `userData-delete-btn`.
        });
        });
</script>
@stop