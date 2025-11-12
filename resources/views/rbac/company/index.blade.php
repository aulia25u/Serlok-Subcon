@extends('layouts.app')

@section('title', 'Company Management')

@section('css')
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        .dataTables_length select {
            min-width: 50px;
        }
    </style>
@stop

@section('content')
@php
    $isInternal = is_null($currentCustomerId ?? null);
@endphp
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Company Management</h3>
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs" id="companyTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="tab-department-link" data-toggle="tab" href="#tab-department" role="tab" aria-controls="tab-department" aria-selected="true">Department</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="tab-section-link" data-toggle="tab" href="#tab-section" role="tab" aria-controls="tab-section" aria-selected="false">Section</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="tab-position-link" data-toggle="tab" href="#tab-position" role="tab" aria-controls="tab-position" aria-selected="false">Position</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="tab-plant-link" data-toggle="tab" href="#tab-plant" role="tab" aria-controls="tab-plant" aria-selected="false">Plant</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="tab-role-link" data-toggle="tab" href="#tab-role" role="tab" aria-controls="tab-role" aria-selected="false">Role</a>
                </li>
            </ul>
            <div class="tab-content pt-3" id="companyTabContent">
                <div class="tab-pane fade show active" id="tab-department" role="tabpanel" aria-labelledby="tab-department-link">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Department</h5>
                        <button type="button" class="btn btn-primary" id="deptAddBtn" data-toggle="modal" data-target="#deptAddModal">
                            <i class="fas fa-plus"></i> Add New
                        </button>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="dept_start_date">Start Date</label>
                            <input type="date" id="dept_start_date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="dept_end_date">End Date</label>
                            <input type="date" id="dept_end_date" class="form-control">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" class="btn btn-info mr-2" id="deptFilterBtn">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <button type="button" class="btn btn-secondary" id="deptResetBtn">
                                <i class="fas fa-undo"></i> Reset
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="deptTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Department Name</th>
                                    <th>Tenant</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="tab-section" role="tabpanel" aria-labelledby="tab-section-link">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Section</h5>
                        <button type="button" class="btn btn-primary" id="sectionAddBtn" data-toggle="modal" data-target="#sectionModal">
                            <i class="fas fa-plus"></i> Add New
                        </button>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="section_start_date">Start Date</label>
                            <input type="date" id="section_start_date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="section_end_date">End Date</label>
                            <input type="date" id="section_end_date" class="form-control">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" class="btn btn-info mr-2" id="sectionFilterBtn">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <button type="button" class="btn btn-secondary" id="sectionResetBtn">
                                <i class="fas fa-undo"></i> Reset
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="sectionTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Section Name</th>
                                    <th>Department</th>
                                    <th>Tenant</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="tab-position" role="tabpanel" aria-labelledby="tab-position-link">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Position</h5>
                        <button type="button" class="btn btn-primary" id="positionAddBtn" data-toggle="modal" data-target="#positionModal">
                            <i class="fas fa-plus"></i> Add New
                        </button>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="position_start_date">Start Date</label>
                            <input type="date" id="position_start_date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="position_end_date">End Date</label>
                            <input type="date" id="position_end_date" class="form-control">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" class="btn btn-info mr-2" id="positionFilterBtn">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <button type="button" class="btn btn-secondary" id="positionResetBtn">
                                <i class="fas fa-undo"></i> Reset
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="positionTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Position Name</th>
                                    <th>Section</th>
                                    <th>Tenant</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="tab-plant" role="tabpanel" aria-labelledby="tab-plant-link">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Plant</h5>
                        <button type="button" class="btn btn-primary" id="plantAddBtn" data-toggle="modal" data-target="#plantModal">
                            <i class="fas fa-plus"></i> Add New
                        </button>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="plant_start_date">Start Date</label>
                            <input type="date" id="plant_start_date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="plant_end_date">End Date</label>
                            <input type="date" id="plant_end_date" class="form-control">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" class="btn btn-info mr-2" id="plantFilterBtn">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <button type="button" class="btn btn-secondary" id="plantResetBtn">
                                <i class="fas fa-undo"></i> Reset
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="plantTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Plant Name</th>
                                    <th>Tenant</th>
                                    <th>Plant Code</th>
                                    <th>Location</th>
                                    <th>Description</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="tab-role" role="tabpanel" aria-labelledby="tab-role-link">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Role</h5>
                        <button type="button" class="btn btn-primary" id="roleAddBtn" data-toggle="modal" data-target="#roleModal">
                            <i class="fas fa-plus"></i> Add New
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="roleTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Role Name</th>
                                    <th>Tenant</th>
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
</div>

<!-- Department Modals -->
<div class="modal fade" id="deptAddModal" tabindex="-1" role="dialog" aria-labelledby="deptAddModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deptAddModalLabel">Add Department</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="deptAddForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="dept_name">Department Name</label>
                        <input type="text" class="form-control" id="dept_name" name="dept_name" required>
                    </div>
                    @if($isInternal)
                        <div class="form-group">
                            <label for="dept_customer_id">Customer</label>
                            <select class="form-control" id="dept_customer_id" name="customer_id">
                                <option value="">Internal (Global)</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <input type="hidden" id="dept_customer_id" name="customer_id" value="{{ $currentCustomerId }}">
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deptEditModal" tabindex="-1" role="dialog" aria-labelledby="deptEditModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deptEditModalLabel">Edit Department</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="deptEditForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="dept_id" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="dept_edit_name">Department Name</label>
                        <input type="text" class="form-control" id="dept_edit_name" name="dept_name" required>
                    </div>
                    @if($isInternal)
                        <div class="form-group">
                            <label for="dept_edit_customer_id">Customer</label>
                            <select class="form-control" id="dept_edit_customer_id" name="customer_id">
                                <option value="">Internal (Global)</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <input type="hidden" id="dept_edit_customer_id" name="customer_id" value="{{ $currentCustomerId }}">
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Section Modal -->
<div class="modal fade" id="sectionModal" tabindex="-1" role="dialog" aria-labelledby="sectionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sectionModalLabel">Add Section</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="sectionForm">
                @csrf
                <input type="hidden" id="section_id" name="id">
                <input type="hidden" id="section_form_method" name="_method" value="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="section_name">Section Name</label>
                        <input type="text" class="form-control" id="section_name" name="section_name" required>
                    </div>
                    <div class="form-group">
                        <label for="section_customer_id">Tenant</label>
                        @if($isInternal)
                            <select class="form-control" id="section_customer_id" name="customer_id">
                                <option value="">Select Customer</option>
                                <option value="null">Internal (Global)</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                                @endforeach
                            </select>
                        @else
                            <select class="form-control" id="section_customer_id" disabled>
                                <option value="{{ $currentCustomerId }}">{{ optional($customers->first())->customer_name ?? 'My Customer' }}</option>
                            </select>
                            <input type="hidden" name="customer_id" value="{{ $currentCustomerId }}">
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="section_dept_id">Department</label>
                        <select class="form-control" id="section_dept_id" name="dept_id" required>
                            <option value="">Select Department</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="section_description">Description</label>
                        <textarea class="form-control" id="section_description" name="description" rows="3"></textarea>
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

<!-- Position Modal -->
<div class="modal fade" id="positionModal" tabindex="-1" role="dialog" aria-labelledby="positionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="positionModalLabel">Add Position</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="positionForm">
                @csrf
                <input type="hidden" id="position_id" name="id">
                <input type="hidden" id="position_form_method" name="_method" value="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="position_name">Position Name</label>
                        <input type="text" class="form-control" id="position_name" name="position_name" required>
                    </div>
                    <div class="form-group">
                        <label for="position_customer_id">Tenant</label>
                        @if($isInternal)
                            <select class="form-control" id="position_customer_id" name="customer_id">
                                <option value="">Select Customer</option>
                                <option value="null">Internal (Global)</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                                @endforeach
                            </select>
                        @else
                            <select class="form-control" id="position_customer_id" disabled>
                                <option value="{{ $currentCustomerId }}">{{ optional($customers->first())->customer_name ?? 'My Customer' }}</option>
                            </select>
                            <input type="hidden" name="customer_id" value="{{ $currentCustomerId }}">
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="position_section_id">Section</label>
                        <select class="form-control" id="position_section_id" name="section_id" required>
                            <option value="">Select Section</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="position_description">Description</label>
                        <textarea class="form-control" id="position_description" name="description" rows="3"></textarea>
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

<!-- Role Modal -->
<div class="modal fade" id="roleModal" tabindex="-1" role="dialog" aria-labelledby="roleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roleModalLabel">Add Role</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="roleForm" action="{{ route('rbac.role.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="role_name">Role Name</label>
                        <input type="text" class="form-control" id="role_name" name="role_name" required>
                    </div>
                    @if($isInternal)
                        <div class="form-group">
                            <label for="role_customer_id">Customer</label>
                            <select class="form-control" id="role_customer_id" name="customer_id">
                                <option value="">Internal (Global)</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <input type="hidden" id="role_customer_id" name="customer_id" value="{{ $currentCustomerId }}">
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Plant Modal -->
<div class="modal fade" id="plantModal" tabindex="-1" role="dialog" aria-labelledby="plantModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="plantModalLabel">Add Plant</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="plantForm">
                @csrf
                <input type="hidden" id="plant_id" name="id">
                <input type="hidden" id="plant_form_method" name="_method" value="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="plant_name">Plant Name</label>
                        <input type="text" class="form-control" id="plant_name" name="plant_name" required>
                    </div>
                    <div class="form-group">
                        <label for="plant_customer_id">Tenant</label>
                        @if($isInternal)
                            <select class="form-control" id="plant_customer_id" name="customer_id">
                                <option value="">Internal (Global)</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                                @endforeach
                            </select>
                        @else
                            <select class="form-control" id="plant_customer_id" disabled>
                                <option value="{{ $currentCustomerId }}">{{ optional($customers->first())->customer_name ?? 'My Customer' }}</option>
                            </select>
                            <input type="hidden" name="customer_id" value="{{ $currentCustomerId }}">
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="plant_code">Plant Code</label>
                        <input type="text" class="form-control" id="plant_code" name="plant_code">
                    </div>
                    <div class="form-group">
                        <label for="plant_location">Location</label>
                        <input type="text" class="form-control" id="plant_location" name="location">
                    </div>
                    <div class="form-group">
                        <label for="plant_description">Description</label>
                        <textarea class="form-control" id="plant_description" name="description" rows="3"></textarea>
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
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const deptTable = $('#deptTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('rbac.department') }}",
                    data: function(data) {
                        data.start_date = $('#dept_start_date').val();
                        data.end_date = $('#dept_end_date').val();
                    }
                },
                columns: [
                    { data: 'no', name: 'no', orderable: false, searchable: false },
                    { data: 'dept_name', name: 'dept_name' },
                    { data: 'customer', name: 'customer' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'updated_at', name: 'updated_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                responsive: true,
                pageLength: 10
            });

            $('#deptFilterBtn').on('click', function() {
                deptTable.draw();
            });

            $('#deptResetBtn').on('click', function() {
                $('#dept_start_date, #dept_end_date').val('');
                deptTable.draw();
            });

            $('#deptAddForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('rbac.department.store') }}",
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#deptAddModal').modal('hide');
                        $('.modal-backdrop').remove();
                        deptTable.draw();
                        toastr.success(response.success);
                        $('#deptAddForm')[0].reset();
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON.errors || {};
                        Object.values(errors).forEach(msg => toastr.error(msg[0]));
                    }
                });
            });

            $(document).on('click', '.dept-edit-btn', function() {
                const id = $(this).data('id');
                const editUrl = "{{ route('rbac.department.edit', ':id') }}".replace(':id', id);

                $.get(editUrl, function(response) {
                    $('#dept_id').val(response.id);
                    $('#dept_edit_name').val(response.dept_name);
                    $('#dept_edit_customer_id').val(response.customer_id ?? '');
                    $('#deptEditModal').modal('show');
                });
            });

            $('#deptEditForm').on('submit', function(e) {
                e.preventDefault();
                const id = $('#dept_id').val();
                const updateUrl = "{{ route('rbac.department.update', ':id') }}".replace(':id', id);

                $.ajax({
                    url: updateUrl,
                    type: 'PUT',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#deptEditModal').modal('hide');
                        $('.modal-backdrop').remove();
                        deptTable.draw(false);
                        toastr.success(response.success);
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON.errors || {};
                        Object.values(errors).forEach(msg => toastr.error(msg[0]));
                    }
                });
            });

            $(document).on('click', '.dept-delete-btn', function() {
                const id = $(this).data('id');
                const deleteUrl = "{{ route('rbac.department.destroy', ':id') }}".replace(':id', id);

                if (confirm('Are you sure you want to delete this department?')) {
                    $.ajax({
                        url: deleteUrl,
                        type: 'DELETE',
                        success: function(response) {
                            deptTable.draw(false);
                            toastr.success(response.success);
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON.error || 'Something went wrong.');
                        }
                    });
                }
            });

            const sectionTable = $('#sectionTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('rbac.section') }}",
                    data: function(data) {
                        data.start_date = $('#section_start_date').val();
                        data.end_date = $('#section_end_date').val();
                    }
                },
                columns: [
                    { data: 'no', name: 'no', orderable: false, searchable: false },
                    { data: 'section_name', name: 'section_name' },
                    { data: 'dept_name', name: 'dept_name' },
                    { data: 'customer', name: 'customer' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'updated_at', name: 'updated_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                responsive: true,
                pageLength: 10
            });

            const loadSectionDepartments = function(customerId, selectedId = null) {
                const target = $('#section_dept_id');
                if (customerId === '' || typeof customerId === 'undefined') {
                    target.html('<option value="">Select Department</option>');
                    return;
                }
                const resolvedCustomerId = customerId === null ? 'null' : customerId;
                const url = "{{ route('rbac.departments.by-customer', ['customer_id' => ':customer']) }}".replace(':customer', resolvedCustomerId);
                $.get(url, function(records) {
                    let options = '<option value="">Select Department</option>';
                    records.forEach(function(record) {
                        const selected = selectedId && selectedId == record.id ? 'selected' : '';
                        options += `<option value="${record.id}" ${selected}>${record.dept_name}</option>`;
                    });
                    target.html(options);
                });
            };

            $('#sectionFilterBtn').on('click', function() {
                sectionTable.draw();
            });

            $('#sectionResetBtn').on('click', function() {
                $('#section_start_date, #section_end_date').val('');
                sectionTable.draw();
            });

            $('#section_customer_id').on('change', function() {
                loadSectionDepartments($(this).val());
            });

            @if(!$isInternal)
                $('#sectionAddBtn').on('click', function() {
                    loadSectionDepartments(@json($currentCustomerId));
                });
            @endif

            $('#sectionModal').on('hidden.bs.modal', function() {
                $('#sectionForm')[0].reset();
                $('#section_id').val('');
                $('#section_form_method').val('POST');
                $('#sectionModalLabel').text('Add Section');
                $('#section_dept_id').html('<option value="">Select Department</option>');
            });

            $('#sectionForm').on('submit', function(e) {
                e.preventDefault();
                const id = $('#section_id').val();
                const url = id ? `/rbac/section/${id}` : "{{ route('rbac.section.store') }}";
                const type = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: type,
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#sectionModal').modal('hide');
                        $('.modal-backdrop').remove();
                        sectionTable.draw(false);
                        toastr.success(response.success);
                        $('#sectionForm')[0].reset();
                        $('#section_id').val('');
                        $('#section_form_method').val('POST');
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON.errors || {};
                        Object.values(errors).forEach(msg => toastr.error(msg[0]));
                    }
                });
            });

            $(document).on('click', '.section-edit-btn', function() {
                const id = $(this).data('id');
                const editUrl = `/rbac/section/${id}/edit`;

                $.get(editUrl, function(response) {
                    $('#sectionModalLabel').text('Edit Section');
                    $('#section_id').val(response.id);
                    $('#section_name').val(response.section_name);
                    $('#section_customer_id').val(response.customer_id ?? '');
                    $('#section_description').val(response.description);
                    $('#section_form_method').val('PUT');
                    loadSectionDepartments(response.customer_id, response.dept_id);
                    $('#sectionModal').modal('show');
                }).fail(function() {
                    toastr.error('Failed to load section data for editing.');
                });
            });

            $(document).on('click', '.section-delete-btn', function() {
                const id = $(this).data('id');
                if (confirm('Are you sure you want to delete this section?')) {
                    $.ajax({
                        url: `/rbac/section/${id}`,
                        type: 'DELETE',
                        success: function(response) {
                            sectionTable.draw(false);
                            toastr.success(response.success);
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON.error || 'Something went wrong.');
                        }
                    });
                }
            });

            const positionTable = $('#positionTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('rbac.position') }}",
                    data: function(data) {
                        data.start_date = $('#position_start_date').val();
                        data.end_date = $('#position_end_date').val();
                    }
                },
                columns: [
                    { data: 'no', name: 'no', orderable: false, searchable: false },
                    { data: 'position_name', name: 'position_name' },
                    { data: 'section_name', name: 'section_name' },
                    { data: 'customer', name: 'customer' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'updated_at', name: 'updated_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                responsive: true,
                pageLength: 10
            });

            const loadPositionSections = function(customerId, selectedId = null) {
                const target = $('#position_section_id');
                if (customerId === '' || typeof customerId === 'undefined') {
                    target.html('<option value="">Select Section</option>');
                    return;
                }
                const resolvedCustomerId = customerId === null ? 'null' : customerId;
                const url = "{{ route('rbac.sections.by-customer', ['customer_id' => ':customer']) }}".replace(':customer', resolvedCustomerId);
                $.get(url, function(response) {
                    let options = '<option value="">Select Section</option>';
                    response.forEach(function(section) {
                        const selected = selectedId && selectedId == section.id ? 'selected' : '';
                        options += `<option value="${section.id}" ${selected}>${section.section_name}</option>`;
                    });
                    target.html(options);
                });
            };

            $('#positionFilterBtn').on('click', function() {
                positionTable.draw();
            });

            $('#positionResetBtn').on('click', function() {
                $('#position_start_date, #position_end_date').val('');
                positionTable.draw();
            });

            $('#position_customer_id').on('change', function() {
                loadPositionSections($(this).val());
            });

            @if(!$isInternal)
                $('#positionAddBtn').on('click', function() {
                    loadPositionSections(@json($currentCustomerId));
                });
            @endif

            $('#positionModal').on('hidden.bs.modal', function() {
                $('#positionForm')[0].reset();
                $('#position_id').val('');
                $('#position_form_method').val('POST');
                $('#positionModalLabel').text('Add Position');
                $('#position_section_id').html('<option value="">Select Section</option>');
            });

            $('#positionForm').on('submit', function(e) {
                e.preventDefault();
                const id = $('#position_id').val();
                const url = id ? `/rbac/position/${id}` : "{{ route('rbac.position.store') }}";
                const type = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: type,
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#positionModal').modal('hide');
                        $('.modal-backdrop').remove();
                        positionTable.draw(false);
                        toastr.success(response.success);
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON.errors || {};
                        Object.values(errors).forEach(msg => toastr.error(msg[0]));
                    }
                });
            });

            $(document).on('click', '.position-edit-btn', function() {
                const id = $(this).data('id');
                const editUrl = `/rbac/position/${id}/edit`;

                $.get(editUrl, function(response) {
                    $('#positionModalLabel').text('Edit Position');
                    $('#position_id').val(response.id);
                    $('#position_name').val(response.position_name);
                    $('#position_customer_id').val(response.customer_id ?? '');
                    $('#position_description').val(response.description);
                    $('#position_form_method').val('PUT');
                    loadPositionSections(response.customer_id, response.section_id);
                    $('#positionModal').modal('show');
                }).fail(function() {
                    toastr.error('Failed to load position data for editing.');
                });
            });

            $(document).on('click', '.position-delete-btn', function() {
                const id = $(this).data('id');
                if (confirm('Are you sure you want to delete this position?')) {
                    $.ajax({
                        url: `/rbac/position/${id}`,
                        type: 'DELETE',
                        success: function(response) {
                            positionTable.draw(false);
                            toastr.success(response.success);
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON.error || 'Something went wrong.');
                        }
                    });
                }
            });

            const roleTable = $('#roleTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('rbac.role') }}"
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'role_name', name: 'role_name' },
                    { data: 'customer', name: 'customer' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'updated_at', name: 'updated_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                responsive: true,
                pageLength: 10
            });

            $('#roleAddBtn').on('click', function() {
                $('#roleModalLabel').text('Add Role');
                $('#roleForm').attr('action', "{{ route('rbac.role.store') }}");
                $('#roleForm').find('input[name="_method"]').remove();
                $('#roleForm')[0].reset();
                @if(!$isInternal)
                    $('#role_customer_id').val('{{ $currentCustomerId ?? '' }}');
                @endif
            });

            $(document).on('click', '.role-edit-btn', function() {
                const id = $(this).data('id');
                const editUrl = "{{ route('rbac.role.edit', ':id') }}".replace(':id', id);

                $.get(editUrl, function(response) {
                    $('#roleModalLabel').text('Edit Role');
                    $('#role_name').val(response.role_name);
                    $('#roleForm').attr('action', "{{ route('rbac.role.update', ':id') }}".replace(':id', id));
                    $('#roleForm').find('input[name="_method"]').remove();
                    $('#roleForm').append('<input type="hidden" name="_method" value="PUT">');
                    $('#role_customer_id').val(response.customer_id ?? '');
                    $('#roleModal').modal('show');
                }).fail(function() {
                    toastr.error('Failed to load role details.');
                });
            });

            $('#roleForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                $.ajax({
                    url: form.attr('action'),
                    type: form.find('input[name="_method"]').val() === 'PUT' ? 'PUT' : 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        $('#roleModal').modal('hide');
                        $('.modal-backdrop').remove();
                        roleTable.draw(false);
                        toastr.success(response.success);
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON.errors || {};
                        Object.values(errors).forEach(msg => toastr.error(msg[0]));
                        if (!Object.keys(errors).length && xhr.responseJSON && xhr.responseJSON.error) {
                            toastr.error(xhr.responseJSON.error);
                        }
                    }
                });
            });

            $(document).on('click', '.role-delete-btn', function() {
                const id = $(this).data('id');
                if (confirm('Are you sure you want to delete this role?')) {
                    $.ajax({
                        url: "{{ route('rbac.role.destroy', ':id') }}".replace(':id', id),
                        type: 'POST',
                        data: {
                            _method: 'DELETE'
                        },
                        success: function(response) {
                            roleTable.draw(false);
                            toastr.success(response.success);
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON.error || 'Something went wrong.');
                        }
                    });
                }
            });

            const plantTable = $('#plantTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('rbac.plant') }}",
                    data: function(data) {
                        data.start_date = $('#plant_start_date').val();
                        data.end_date = $('#plant_end_date').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'plant_name', name: 'plant_name' },
                    { data: 'customer', name: 'customer' },
                    { data: 'plant_code', name: 'plant_code' },
                    { data: 'location', name: 'location' },
                    { data: 'description', name: 'description' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'updated_at', name: 'updated_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                responsive: true,
                pageLength: 10
            });

            $('#plantFilterBtn').on('click', function() {
                plantTable.draw();
            });

            $('#plantResetBtn').on('click', function() {
                $('#plant_start_date, #plant_end_date').val('');
                plantTable.draw();
            });

            $('#plantAddBtn').on('click', function() {
                $('#plantModalLabel').text('Add Plant');
                $('#plantForm')[0].reset();
                $('#plant_id').val('');
                $('#plant_form_method').val('POST');
            });

            $('#plantForm').on('submit', function(e) {
                e.preventDefault();
                const id = $('#plant_id').val();
                const url = id ? `/rbac/plant/${id}` : "{{ route('rbac.plant.store') }}";
                const type = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: type,
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#plantModal').modal('hide');
                        $('.modal-backdrop').remove();
                        plantTable.draw(false);
                        toastr.success(response.success);
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON.errors || {};
                        Object.values(errors).forEach(msg => toastr.error(msg[0]));
                    }
                });
            });

            $(document).on('click', '.plant-edit-btn', function() {
                const id = $(this).data('id');
                const editUrl = `/rbac/plant/${id}/edit`;

                $.get(editUrl, function(response) {
                    $('#plantModalLabel').text('Edit Plant');
                    $('#plant_id').val(response.id);
                    $('#plant_name').val(response.plant_name);
                    $('#plant_code').val(response.plant_code);
                    $('#plant_location').val(response.location);
                    $('#plant_description').val(response.description);
                    $('#plant_customer_id').val(response.customer_id ?? '');
                    $('#plant_form_method').val('PUT');
                    $('#plantModal').modal('show');
                }).fail(function() {
                    toastr.error('Failed to load plant data.');
                });
            });

            $(document).on('click', '.plant-delete-btn', function() {
                const id = $(this).data('id');
                if (confirm('Are you sure you want to delete this plant?')) {
                    $.ajax({
                        url: `/rbac/plant/${id}`,
                        type: 'DELETE',
                        success: function(response) {
                            plantTable.draw(false);
                            toastr.success(response.success);
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON.error || 'Something went wrong.');
                        }
                    });
                }
            });
        });
    </script>
@endpush
