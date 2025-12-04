@extends('layouts.app')

@section('title', 'User Data')

@section('css')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<style>
    /* Force side-by-side display for bottom start section */
    .dt-layout-cell.dt-start {
        display: flex !important;
        align-items: center;
        gap: 10px;
    }

    .dt-layout-cell.dt-start select {
        width: auto !important;
        min-width: 60px;
        padding-right: 30px !important;
        /* Ensure space for arrow */
    }

    /* Custom Search Input Style */
    .dt-search {
        position: relative;
    }

    .dt-search input {
        padding-left: 30px !important;
        /* Space for the icon */
        border-radius: 3px !important;
        /* Rounded corners */
    }

    .dt-search::before {
        content: "\f002";
        /* FontAwesome magnifying glass */
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #aaa;
        pointer-events: none;
        /* Let clicks pass through */
        z-index: 1;
    }
</style>
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
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Add New User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="userDataForm" enctype="multipart/form-data" novalidate>
                @csrf
                <input type="hidden" id="userData_id" name="id">
                <input type="hidden" id="userData_form_method" name="_method" value="POST">
                <div class="modal-body">
                    <ul class="nav nav-tabs" id="userTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="master-tab" data-toggle="tab" href="#master" role="tab"
                                aria-controls="master" aria-selected="true">User Data</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="bank-tab" data-toggle="tab" href="#bank" role="tab"
                                aria-controls="bank" aria-selected="false">Bank Detail</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="assignments-tab" data-toggle="tab" href="#assignments" role="tab"
                                aria-controls="assignments" aria-selected="false">Assignments</a>
                        </li>
                    </ul>
                    <div class="tab-content mt-3" id="userTabsContent">
                        <!-- Master Data Tab -->
                        <div class="tab-pane fade show active" id="master" role="tabpanel" aria-labelledby="master-tab">
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
                                        <input type="text" class="form-control" id="full_name" name="full_name"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <input type="password" class="form-control" id="password" name="password"
                                            required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nip">NIP</label>
                                        <input type="text" class="form-control" id="nip" name="nip">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="employee_status">Status Pegawai</label>
                                        <select class="form-control" id="employee_status" name="employee_status">
                                            <option value="">Select Status</option>
                                            <option value="Tetap">Tetap</option>
                                            <option value="Kontrak">Kontrak</option>
                                            <option value="Borongan">Borongan</option>
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
                                        <label for="phone">Phone</label>
                                        <input type="text" class="form-control" id="phone" name="phone">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <textarea class="form-control" id="address" name="address" rows="1"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="employee_photo">Photo</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="employee_photo"
                                                    name="employee_photo">
                                                <label class="custom-file-label" for="employee_photo">Choose
                                                    file</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status_active">Status Akun</label>
                                        <select class="form-control" id="status_active" name="status_active">
                                            <option value="1">Active</option>
                                            <option value="0">Not Active</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="blacklist_note">Black List Note</label>
                                        <textarea class="form-control" id="blacklist_note" name="blacklist_note"
                                            rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="accessible_tenants">Accessible Tenants (Optional)</label>
                                        <select class="form-control select2" id="accessible_tenants"
                                            name="accessible_tenants[]" multiple="multiple" style="width: 100%;">
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">Tenants assigned in "Assignments" tab are
                                            automatically included.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bank Detail Tab -->
                        <div class="tab-pane fade" id="bank" role="tabpanel" aria-labelledby="bank-tab">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bank_name">Bank Name</label>
                                        <input type="text" class="form-control" id="bank_name" name="bank_name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bank_account_name">Bank Account Name</label>
                                        <input type="text" class="form-control" id="bank_account_name"
                                            name="bank_account_name">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bank_account_number">Bank Account Number</label>
                                        <input type="text" class="form-control" id="bank_account_number"
                                            name="bank_account_number">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Assignments Tab -->
                        <div class="tab-pane fade" id="assignments" role="tabpanel" aria-labelledby="assignments-tab">
                            <table class="table table-bordered" id="assignmentsTable">
                                <thead>
                                    <tr>
                                        <th>Tenant</th>
                                        <th>Department</th>
                                        <th>Section</th>
                                        <th>Position</th>
                                        <th>Role</th>
                                        <th>Plant</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="assignmentsBody">
                                    <!-- Dynamic Rows -->
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-success btn-sm" id="addAssignmentBtn">
                                <i class="fas fa-plus"></i> Add Assignment
                            </button>
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
<script type="text/javascript" src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script src="{{ asset('js/crud-manager.js') }}"></script>
<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        const isInternalUser = @json($isInternal);
        const currentCustomerId = @json($currentCustomerId);
        const plants = @json($plants);
        let rowIdx = 0;

        // Helper functions for dropdowns (Scoped to Row)
        function loadSections(row, deptId, selectedSectionId, selectedPositionId) {
            const sectionSelect = row.find('.section-select');
            const positionSelect = row.find('.position-select');

            if (!deptId) {
                sectionSelect.empty().append('<option value="">Select Section</option>');
                positionSelect.empty().append('<option value="">Select Position</option>');
                return $.Deferred().resolve();
            }

            return $.ajax({
                url: '{{ route("rbac.sections.by-department", ":id") }}'.replace(':id', deptId),
                type: 'GET',
                success: function (data) {
                    sectionSelect.empty().append('<option value="">Select Section</option>');
                    $.each(data, function (key, value) {
                        sectionSelect.append('<option value="' + value.id + '">' + value.section_name + '</option>');
                    });

                    if (selectedSectionId) {
                        sectionSelect.val(selectedSectionId);
                    }

                    positionSelect.empty().append('<option value="">Select Position</option>');

                    var sectionIdToLoad = selectedSectionId || sectionSelect.val();
                    if (sectionIdToLoad) {
                        loadPositions(row, sectionIdToLoad, selectedPositionId);
                    }
                }
            });
        }

        function loadPositions(row, sectionId, selectedPositionId) {
            const positionSelect = row.find('.position-select');

            if (!sectionId) {
                positionSelect.empty().append('<option value="">Select Position</option>');
                return $.Deferred().resolve();
            }

            return $.ajax({
                url: '{{ route("rbac.positions.by-section", ":id") }}'.replace(':id', sectionId),
                type: 'GET',
                success: function (data) {
                    positionSelect.empty().append('<option value="">Select Position</option>');
                    $.each(data, function (key, value) {
                        positionSelect.append('<option value="' + value.id + '">' + value.position_name + '</option>');
                    });

                    if (selectedPositionId) {
                        positionSelect.val(selectedPositionId);
                    }
                }
            });
        }

        function loadDepartments(row, customerId, selectedDeptId, selectedSectionId, selectedPositionId) {
            const deptSelect = row.find('.dept-select');
            const sectionSelect = row.find('.section-select');
            const positionSelect = row.find('.position-select');
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
                success: function (data) {
                    deptSelect.empty().append('<option value="">Select Department</option>');
                    $.each(data, function (key, value) {
                        deptSelect.append('<option value="' + value.id + '">' + value.dept_name + '</option>');
                    });

                    if (selectedDeptId) {
                        deptSelect.val(selectedDeptId);
                        loadSections(row, selectedDeptId, selectedSectionId, selectedPositionId);
                    } else {
                        sectionSelect.empty().append('<option value="">Select Section</option>');
                        positionSelect.empty().append('<option value="">Select Position</option>');
                    }
                }
            });
        }

        function loadRoles(row, customerId, selectedRoleId) {
            const roleSelect = row.find('.role-select');
            const resolvedCustomer = customerId === '' ? 'null' : (customerId ?? 'null');

            return $.ajax({
                url: '{{ route("rbac.roles.by-customer", ":id") }}'.replace(':id', resolvedCustomer),
                type: 'GET',
                success: function (data) {
                    roleSelect.empty().append('<option value="">Select Role</option>');
                    $.each(data, function (key, value) {
                        roleSelect.append('<option value="' + value.id + '">' + value.role_name + '</option>');
                    });
                    if (selectedRoleId) {
                        roleSelect.val(selectedRoleId);
                    }
                }
            });
        }

        function loadPlants(row, customerId, selectedPlantId) {
            const plantSelect = row.find('.plant-select');
            plantSelect.empty().append('<option value="">Select Plant</option>');

            if (!customerId && !isInternalUser) {
                // Should not happen if logic is correct, but safe guard
                return;
            }

            // Filter plants by customerId
            // If internal user and no customer selected (global), show all? Or none?
            // Usually global means no specific customer, so maybe all plants?
            // But plants belong to customers.
            // If internal user selects "Internal (Global)", customerId is empty.
            // Plants for internal? Maybe plants with null customer_id?
            // Let's assume plants are filtered by the selected customer.

            const filteredPlants = plants.filter(plant => {
                if (customerId) {
                    return plant.customer_id == customerId;
                } else {
                    // If no customer selected (Internal Global), show plants with no customer_id?
                    return !plant.customer_id;
                }
            });

            $.each(filteredPlants, function (key, value) {
                plantSelect.append('<option value="' + value.id + '">' + value.plant_name + '</option>');
            });

            if (selectedPlantId) {
                plantSelect.val(selectedPlantId);
            }
        }

        function addAssignmentRow(data = {}) {
            const idx = rowIdx++;
            const customerId = data.customer_id || (isInternalUser ? '' : currentCustomerId);

            let customerOptions = '';
            @foreach($customers as $customer)
                customerOptions += `<option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>`;
            @endforeach

            let customerSelect = '';
            if (isInternalUser) {
                customerSelect = `<select class="form-control customer-select" name="details[${idx}][customer_id]" required>
                                    <option value="">Internal (Global)</option>
                                    ${customerOptions}
                                  </select>`;
            } else {
                customerSelect = `<select class="form-control customer-select" name="details[${idx}][customer_id]" disabled>
                                    <option value="${currentCustomerId}">{{ optional($customers->first())->customer_name ?? 'My Customer' }}</option>
                                  </select>
                                  <input type="hidden" name="details[${idx}][customer_id]" value="${currentCustomerId}">`;
            }

            const rowHtml = `
                <tr id="row-${idx}">
                    <td>
                        ${customerSelect}
                        <input type="hidden" name="details[${idx}][id]" value="${data.id || ''}">
                    </td>
                    <td>
                        <select class="form-control dept-select" name="details[${idx}][dept_id]" required>
                            <option value="">Select Department</option>
                        </select>
                    </td>
                    <td>
                        <select class="form-control section-select" name="details[${idx}][section_id]" required>
                            <option value="">Select Section</option>
                        </select>
                    </td>
                    <td>
                        <select class="form-control position-select" name="details[${idx}][position_id]" required>
                            <option value="">Select Position</option>
                        </select>
                    </td>
                    <td>
                        <select class="form-control role-select" name="details[${idx}][role_id]" required>
                            <option value="">Select Role</option>
                        </select>
                    </td>
                    <td>
                        <select class="form-control plant-select" name="details[${idx}][plant_id]" required>
                            <option value="">Select Plant</option>
                        </select>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;

            $('#assignmentsBody').append(rowHtml);
            const row = $(`#row-${idx}`);

            // Set initial values if provided
            if (data.customer_id) row.find('.customer-select').val(data.customer_id);

            // Load dependencies
            loadDepartments(row, customerId, data.dept_id, data.section_id, data.position_id).then(() => {
                loadRoles(row, customerId, data.role_id);
            });
            loadPlants(row, customerId, data.plant_id);

            // Event listeners
            row.find('.customer-select').on('change', function () {
                const newCustId = $(this).val();
                loadDepartments(row, newCustId).then(() => loadRoles(row, newCustId));
                loadPlants(row, newCustId);
            });
            row.find('.dept-select').on('change', function () {
                loadSections(row, $(this).val());
            });
            row.find('.section-select').on('change', function () {
                loadPositions(row, $(this).val());
            });
            row.find('.remove-row').on('click', function () {
                $(this).closest('tr').remove();
            });
        }

        $('#addAssignmentBtn').on('click', function () {
            addAssignmentRow();
        });

        // Initialize Select2
        $('.select2').select2();

        // Initialize CrudManager
        new CrudManager({
            entity: 'userData',
            routes: {
                index: "{{ route('rbac.user-data') }}",
                store: "{{ route('rbac.user-data.store') }}",
            },
            columns: [
                { data: 'no', name: 'no' },
                { data: 'customer_name', name: 'customer_name' },
                { data: 'username', name: 'username' },
                { data: 'full_name', name: 'full_name' },
                { data: 'email', name: 'email' },
                { data: 'role_name', name: 'role_name' },
                { data: 'dept_name', name: 'dept_name' },
                { data: 'section_name', name: 'section_name' },
                { data: 'position_name', name: 'position_name' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            modalId: '#addModal',
            formId: '#userDataForm',
            tableId: '#userDataTable',
            filterBtnId: '#filterBtn',
            resetBtnId: '#resetBtn',
            addBtnId: '[data-target="#addModal"]',
            dateFilters: true,
            options: {
                layout: {
                    topStart: 'search',
                    topEnd: 'buttons',
                    bottomStart: ['pageLength', 'info'],
                    bottomEnd: 'paging'
                },
                language: {
                    lengthMenu: "_MENU_",
                    search: "",
                    searchPlaceholder: "Search"
                },
                buttons: [
                    {
                        text: '<i class="fas fa-plus"></i> Add New',
                        className: 'btn btn-primary',
                        action: function (e, dt, node, config) {
                            $('#addModal').modal('show');
                        }
                    },
                    {
                        extend: 'csv',
                        text: '<i class="fas fa-file-csv"></i> Export CSV',
                        className: 'btn btn-success'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Print',
                        className: 'btn btn-info'
                    }
                ]
            },
            onAdd: function () {
                $('#addModalLabel').text('Add New User');
                $('#assignmentsBody').empty();
                $('#accessible_tenants').val(null).trigger('change');
                $('#userDataForm')[0].reset();
                $('#userDataForm :input').prop('disabled', false);
                $('button[type="submit"]').show();
                $('.custom-file-label').html('Choose file');

                // Add one empty row by default
                addAssignmentRow();

                // Show password field
                $('#password').closest('.form-group').show();
                $('#password').attr('required', 'required');
                $('#password').removeAttr('placeholder');

                // Reset tab to first one
                $('#master-tab').tab('show');
            },
            onEdit: function (response) {
                $('#addModalLabel').text('Edit User');
                $('#userData_id').val(response.user.id);

                // Populate basic user information
                $('#username').val(response.user.username || '');
                $('#email').val(response.user.email || '');
                $('#full_name').val(response.user.name || ''); // Note: user.name in User model, employee_name in UserDetail

                // Populate Master Data from first detail (assuming consistency)
                if (response.details && response.details.length > 0) {
                    const detail = response.details[0];
                    $('#nip').val(detail.nip || '');
                    $('#employee_status').val(detail.employee_status || '');
                    $('#gender').val(detail.gender || '');
                    $('#phone').val(detail.phone || '');
                    $('#address').val(detail.address || '');
                    $('#status_active').val(detail.status_active ? '1' : '0');
                    $('#blacklist_note').val(detail.blacklist_note || '');
                    $('#bank_name').val(detail.bank_name || '');
                    $('#bank_account_name').val(detail.bank_account_name || '');
                    $('#bank_account_number').val(detail.bank_account_number || '');
                } else {
                    $('#nip').val('');
                    $('#employee_status').val('');
                    $('#gender').val('');
                    $('#phone').val('');
                    $('#address').val('');
                    $('#status_active').val('1');
                    $('#blacklist_note').val('');
                    $('#bank_name').val('');
                    $('#bank_account_name').val('');
                    $('#bank_account_number').val('');
                }

                // Populate Accessible Tenants
                if (response.accessible_tenants) {
                    $('#accessible_tenants').val(response.accessible_tenants).trigger('change');
                } else {
                    $('#accessible_tenants').val(null).trigger('change');
                }

                // Populate Assignments
                $('#assignmentsBody').empty();
                if (response.details && response.details.length > 0) {
                    response.details.forEach(detail => {
                        // We need to reconstruct the data object for addAssignmentRow
                        // detail has position.section.dept_id etc.
                        // But wait, the response.details from controller is just the UserDetail model with relations.
                        // We need to extract IDs.

                        let deptId = null;
                        let sectionId = null;
                        if (detail.position && detail.position.section) {
                            deptId = detail.position.section.dept_id;
                            sectionId = detail.position.section_id;
                        }

                        addAssignmentRow({
                            id: detail.id,
                            customer_id: detail.customer_id,
                            dept_id: deptId,
                            section_id: sectionId,
                            position_id: detail.position_id,
                            role_id: detail.role_id,
                            plant_id: detail.plant_id
                        });
                    });
                } else {
                    addAssignmentRow();
                }

                // Make password field optional when editing
                $('#password').closest('.form-group').show();
                $('#password').removeAttr('required');
                $('#password').attr('placeholder', 'Leave blank to keep current password');

                // Reset tab to first one
                $('#master-tab').tab('show');
            },
            onModalHidden: function () {
                // Reset password field state
                $('#password').attr('required', 'required');
                $('#password').removeAttr('placeholder');
                $('#userDataForm :input').prop('disabled', false);
                $('button[type="submit"]').show();
            }
        });

        // Detail Button Logic
        $(document).on('click', '.userData-detail-btn', function () {
            const id = $(this).data('id');
            // Use CrudManager's edit logic but set to view-only
            // We can trigger the edit button click logic manually or call the edit function if exposed
            // But CrudManager doesn't expose it easily.
            // Let's simulate a click on the edit button but set a flag?
            // Or just fetch data manually and populate.
            // Since we want to reuse the population logic, let's try to leverage CrudManager.

            // We can add a 'view-only' class to the modal or set a global flag
            $('#addModal').data('view-only', true);

            // Trigger edit fetch
            $.ajax({
                url: "{{ route('rbac.user-data') }}/" + id + "/edit",
                type: 'GET',
                success: function (response) {
                    // Manually call onEdit logic (copy-paste or refactor? Refactor is better but risky here)
                    // Let's just use the CrudManager instance if we could access it.
                    // But we can't easily.
                    // So let's just duplicate the population logic for now or trigger the edit button and then disable inputs.

                    // Triggering edit button might be tricky because we are inside the click handler.
                    // Let's just duplicate the population logic for safety and clarity.

                    $('#addModal').modal('show');
                    $('#addModalLabel').text('User Detail');
                    $('#userData_id').val(response.user.id);

                    $('#username').val(response.user.username || '');
                    $('#email').val(response.user.email || '');
                    $('#full_name').val(response.user.name || '');

                    if (response.details && response.details.length > 0) {
                        const detail = response.details[0];
                        $('#nip').val(detail.nip || '');
                        $('#employee_status').val(detail.employee_status || '');
                        $('#gender').val(detail.gender || '');
                        $('#phone').val(detail.phone || '');
                        $('#address').val(detail.address || '');
                        $('#status_active').val(detail.status_active ? '1' : '0');
                        $('#blacklist_note').val(detail.blacklist_note || '');
                        $('#bank_name').val(detail.bank_name || '');
                        $('#bank_account_name').val(detail.bank_account_name || '');
                        $('#bank_account_number').val(detail.bank_account_number || '');
                    }

                    if (response.accessible_tenants) {
                        $('#accessible_tenants').val(response.accessible_tenants).trigger('change');
                    } else {
                        $('#accessible_tenants').val(null).trigger('change');
                    }

                    $('#assignmentsBody').empty();
                    if (response.details && response.details.length > 0) {
                        response.details.forEach(detail => {
                            let deptId = null;
                            let sectionId = null;
                            if (detail.position && detail.position.section) {
                                deptId = detail.position.section.dept_id;
                                sectionId = detail.position.section_id;
                            }

                            addAssignmentRow({
                                id: detail.id,
                                customer_id: detail.customer_id,
                                dept_id: deptId,
                                section_id: sectionId,
                                position_id: detail.position_id,
                                role_id: detail.role_id,
                                plant_id: detail.plant_id
                            });
                        });
                    } else {
                        addAssignmentRow();
                    }

                    // Disable inputs
                    $('#userDataForm :input').prop('disabled', true);
                    // Hide save button
                    $('button[type="submit"]').hide();
                    // Hide add assignment button and remove row buttons
                    $('#addAssignmentBtn').hide();
                    $('.remove-row').hide(); // This might run before rows are added? No, rows added above.
                    // Wait, addAssignmentRow is async for ajax calls?
                    // Yes, loadDepartments etc return promises.
                    // We need to wait for them to finish before disabling?
                    // Actually, disabling the parent fieldset or form is easier.
                    // But select2 might need special handling.
                    $('#userDataForm select').prop('disabled', true);

                    // Hide password field
                    $('#password').closest('.form-group').hide();

                    $('#master-tab').tab('show');
                }
            });
        });

        // Custom file input label
        $(".custom-file-input").on("change", function () {
            var fileName = $(this).val().split("\\").pop();
            $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
        });
    });
</script>
@stop