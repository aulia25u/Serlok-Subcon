@extends('layouts.app')

@section('title', 'Company Management')

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
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Company Management</h3>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs" id="companyTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="tab-department-link" data-toggle="tab" href="#tab-department"
                            role="tab" aria-controls="tab-department" aria-selected="true">Department</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="tab-section-link" data-toggle="tab" href="#tab-section" role="tab"
                            aria-controls="tab-section" aria-selected="false">Section</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="tab-position-link" data-toggle="tab" href="#tab-position" role="tab"
                            aria-controls="tab-position" aria-selected="false">Position</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="tab-plant-link" data-toggle="tab" href="#tab-plant" role="tab"
                            aria-controls="tab-plant" aria-selected="false">Plant</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="tab-role-link" data-toggle="tab" href="#tab-role" role="tab"
                            aria-controls="tab-role" aria-selected="false">Role</a>
                    </li>
                </ul>
                <div class="tab-content pt-3" id="companyTabContent">
                    <div class="tab-pane fade show active" id="tab-department" role="tabpanel"
                        aria-labelledby="tab-department-link">
                        <div class="d-flex justify-content-between align-items-center mb-3 d-none" style="display: none !important;">
                            <h5 class="mb-0">Department</h5>
                            <button type="button" class="btn btn-primary" id="deptAddBtn" data-toggle="modal"
                                data-target="#deptAddModal">
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
                        <div class="d-flex justify-content-between align-items-center mb-3" style="display: none !important;">
                            <h5 class="mb-0">Section</h5>
                            <button type="button" class="btn btn-primary" id="sectionAddBtn" data-toggle="modal"
                                data-target="#sectionModal">
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
                        <div class="mb-3 d-none" style="display: none !important;">
                            <h5 class="mb-0">Position</h5>
                            <button type="button" class="btn btn-primary" id="positionAddBtn" data-toggle="modal"
                                data-target="#positionModal">
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
                        <div class="d-flex justify-content-between align-items-center mb-3" style="display: none !important;">
                            <h5 class="mb-0">Plant</h5>
                            <button type="button" class="btn btn-primary" id="plantAddBtn" data-toggle="modal"
                                data-target="#plantModal">
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
                        <div class="d-flex justify-content-between align-items-center mb-3" style="display: none !important;">
                            <h5 class="mb-0">Role</h5>
                            <button type="button" class="btn btn-primary" id="roleAddBtn" data-toggle="modal"
                                data-target="#roleModal">
                                <i class="fas fa-plus"></i> Add New
                            </button>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="role_start_date">Start Date</label>
                                <input type="date" id="role_start_date" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label for="role_end_date">End Date</label>
                                <input type="date" id="role_end_date" class="form-control">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="button" class="btn btn-info mr-2" id="roleFilterBtn">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <button type="button" class="btn btn-secondary" id="roleResetBtn">
                                    <i class="fas fa-undo"></i> Reset
                                </button>
                            </div>
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
    <div class="modal fade" id="deptAddModal" tabindex="-1" role="dialog" aria-labelledby="deptAddModalLabel"
        aria-hidden="true">
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

    <div class="modal fade" id="deptEditModal" tabindex="-1" role="dialog" aria-labelledby="deptEditModalLabel"
        aria-hidden="true">
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
    <div class="modal fade" id="sectionModal" tabindex="-1" role="dialog" aria-labelledby="sectionModalLabel"
        aria-hidden="true">
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
                                    <option value="{{ $currentCustomerId }}">
                                        {{ optional($customers->first())->customer_name ?? 'My Customer' }}
                                    </option>
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
    <div class="modal fade" id="positionModal" tabindex="-1" role="dialog" aria-labelledby="positionModalLabel"
        aria-hidden="true">
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
                                    <option value="{{ $currentCustomerId }}">
                                        {{ optional($customers->first())->customer_name ?? 'My Customer' }}
                                    </option>
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
    <div class="modal fade" id="plantModal" tabindex="-1" role="dialog" aria-labelledby="plantModalLabel"
        aria-hidden="true">
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
                                    <option value="{{ $currentCustomerId }}">
                                        {{ optional($customers->first())->customer_name ?? 'My Customer' }}
                                    </option>
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

            // --- Department ---
            // Re-implementing Department manually because of the split modal/form IDs in HTML
            const deptTable = $('#deptTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('rbac.department') }}",
                    data: function (data) {
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
                pageLength: 10,
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
                            $('#deptAddForm')[0].reset();
                            $('#deptAddModal').find('.modal-title').text('Add Department');
                            $('#deptAddForm').find('input[name="id"]').val('');
                            $('#deptAddForm').find('input[name="_method"]').val('POST');
                            $('#deptAddModal').modal('show');
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
            });

            $('#deptFilterBtn').on('click', () => deptTable.draw());
            $('#deptResetBtn').on('click', () => {
                $('#dept_start_date, #dept_end_date').val('');
                deptTable.draw();
            });

            $('#deptAddForm').on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('rbac.department.store') }}",
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        $('#deptAddModal').modal('hide');
                        $('.modal-backdrop').remove();
                        deptTable.draw();
                        toastr.success(response.success);
                        $('#deptAddForm')[0].reset();
                    },
                    error: function (xhr) {
                        const errors = xhr.responseJSON.errors || {};
                        Object.values(errors).forEach(msg => toastr.error(msg[0]));
                    }
                });
            });

            $(document).on('click', '.dept-edit-btn', function () {
                const id = $(this).data('id');
                $.get("{{ route('rbac.department.edit', ':id') }}".replace(':id', id), function (response) {
                    $('#dept_id').val(response.id);
                    $('#dept_edit_name').val(response.dept_name);
                    $('#dept_edit_customer_id').val(response.customer_id ?? '');
                    $('#deptEditModal').modal('show');
                });
            });

            $('#deptEditForm').on('submit', function (e) {
                e.preventDefault();
                const id = $('#dept_id').val();
                $.ajax({
                    url: "{{ route('rbac.department.update', ':id') }}".replace(':id', id),
                    type: 'PUT',
                    data: $(this).serialize(),
                    success: function (response) {
                        $('#deptEditModal').modal('hide');
                        $('.modal-backdrop').remove();
                        deptTable.draw(false);
                        toastr.success(response.success);
                    },
                    error: function (xhr) {
                        const errors = xhr.responseJSON.errors || {};
                        Object.values(errors).forEach(msg => toastr.error(msg[0]));
                    }
                });
            });

            $(document).on('click', '.dept-delete-btn', function () {
                const id = $(this).data('id');
                if (confirm('Are you sure you want to delete this department?')) {
                    $.ajax({
                        url: "{{ route('rbac.department.destroy', ':id') }}".replace(':id', id),
                        type: 'DELETE',
                        success: function (response) {
                            deptTable.draw(false);
                            toastr.success(response.success);
                        },
                        error: function (xhr) {
                            toastr.error(xhr.responseJSON.error || 'Something went wrong.');
                        }
                    });
                }
            });


            // --- Helper for Dependent Dropdowns ---
            const loadOptions = function (url, targetSelector, defaultText, valueField, textField, selectedId = null) {
                const target = $(targetSelector);
                if (!url) {
                    target.html(`<option value="">${defaultText}</option>`);
                    return;
                }
                $.get(url, function (records) {
                    let options = `<option value="">${defaultText}</option>`;
                    records.forEach(function (record) {
                        const selected = selectedId && selectedId == record[valueField] ? 'selected' : '';
                        options += `<option value="${record[valueField]}" ${selected}>${record[textField]}</option>`;
                    });
                    target.html(options);
                });
            };

            // --- Section ---
            const sectionManager = new CrudManager({
                entity: 'section',
                routes: {
                    index: "{{ route('rbac.section') }}",
                    store: "{{ route('rbac.section.store') }}"
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
                modalId: '#sectionModal',
                formId: '#sectionForm',
                tableId: '#sectionTable',
                filterBtnId: '#sectionFilterBtn',
                resetBtnId: '#sectionResetBtn',
                addBtnId: '#sectionAddBtn',
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
                                sectionManager.showAddModal();
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
                    @if(!$isInternal)
                        loadOptions(
                            "{{ route('rbac.departments.by-customer', ['customer_id' => $currentCustomerId]) }}",
                            '#section_dept_id', 'Select Department', 'id', 'dept_name'
                        );
                    @endif
                                                },
                onEdit: function (response) {
                    $('#section_name').val(response.section_name);
                    $('#section_customer_id').val(response.customer_id ?? '');
                    $('#section_description').val(response.description);

                    const customerId = response.customer_id || 'null';
                    loadOptions(
                        "{{ route('rbac.departments.by-customer', ['customer_id' => ':id']) }}".replace(':id', customerId),
                        '#section_dept_id', 'Select Department', 'id', 'dept_name', response.dept_id
                    );
                },
                onModalHidden: function () {
                    $('#section_dept_id').html('<option value="">Select Department</option>');
                }
            });

            $('#section_customer_id').on('change', function () {
                const customerId = $(this).val() || 'null';
                loadOptions(
                    "{{ route('rbac.departments.by-customer', ['customer_id' => ':id']) }}".replace(':id', customerId),
                    '#section_dept_id', 'Select Department', 'id', 'dept_name'
                );
            });


            // --- Position ---
            const positionManager = new CrudManager({
                entity: 'position',
                routes: {
                    index: "{{ route('rbac.position') }}",
                    store: "{{ route('rbac.position.store') }}"
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
                modalId: '#positionModal',
                formId: '#positionForm',
                tableId: '#positionTable',
                filterBtnId: '#positionFilterBtn',
                resetBtnId: '#positionResetBtn',
                addBtnId: '#positionAddBtn',
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
                                positionManager.showAddModal();
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
                    @if(!$isInternal)
                        loadOptions(
                            "{{ route('rbac.sections.by-customer', ['customer_id' => $currentCustomerId]) }}",
                            '#position_section_id', 'Select Section', 'id', 'section_name'
                        );
                    @endif
                                                },
                onEdit: function (response) {
                    $('#position_name').val(response.position_name);
                    $('#position_customer_id').val(response.customer_id ?? '');
                    $('#position_description').val(response.description);

                    const customerId = response.customer_id || 'null';
                    loadOptions(
                        "{{ route('rbac.sections.by-customer', ['customer_id' => ':id']) }}".replace(':id', customerId),
                        '#position_section_id', 'Select Section', 'id', 'section_name', response.section_id
                    );
                },
                onModalHidden: function () {
                    $('#position_section_id').html('<option value="">Select Section</option>');
                }
            });

            $('#position_customer_id').on('change', function () {
                const customerId = $(this).val() || 'null';
                loadOptions(
                    "{{ route('rbac.sections.by-customer', ['customer_id' => ':id']) }}".replace(':id', customerId),
                    '#position_section_id', 'Select Section', 'id', 'section_name'
                );
            });


            // --- Plant ---
            const plantManager = new CrudManager({
                entity: 'plant',
                routes: {
                    index: "{{ route('rbac.plant') }}",
                    store: "{{ route('rbac.plant.store') }}"
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
                                plantManager.showAddModal();
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
                modalId: '#plantModal',
                formId: '#plantForm',
                tableId: '#plantTable',
                filterBtnId: '#plantFilterBtn',
                resetBtnId: '#plantResetBtn',
                addBtnId: '#plantAddBtn',
                dateFilters: true,
                onEdit: function (response) {
                    $('#plant_name').val(response.plant_name);
                    $('#plant_code').val(response.plant_code);
                    $('#plant_location').val(response.location);
                    $('#plant_description').val(response.description);
                    $('#plant_customer_id').val(response.customer_id ?? '');
                }
            });


            // --- Role ---
            const roleManager = new CrudManager({
                entity: 'role',
                routes: {
                    index: "{{ route('rbac.role') }}",
                    store: "{{ route('rbac.role.store') }}"
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'role_name', name: 'role_name' },
                    { data: 'customer', name: 'customer' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'updated_at', name: 'updated_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
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
                                roleManager.showAddModal();
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
                modalId: '#roleModal',
                formId: '#roleForm',
                tableId: '#roleTable',
                filterBtnId: '#roleFilterBtn',
                resetBtnId: '#roleResetBtn',
                addBtnId: '#roleAddBtn',
                dateFilters: true,
                onAdd: function () {
                    @if(!$isInternal)
                        $('#role_customer_id').val('{{ $currentCustomerId ?? '' }}');
                    @endif
                                                },
                onEdit: function (response) {
                    $('#role_name').val(response.role_name);
                    $('#role_customer_id').val(response.customer_id ?? '');
                }
            });

        });
    </script>
@endpush