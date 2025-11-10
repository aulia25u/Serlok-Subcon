@extends('layouts.app')

@section('title', 'LOI - Internal')
@section('page-title', 'LOI - Internal')

@section('css')
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .action-btn {
            margin: 0 2px;
        }
        .btn-view {
            background-color: #28a745;
        }
        .btn-view:hover {
            background-color: #218838;
        }
        .btn-edit {
            background-color: #17a2b8;
        }
        .btn-edit:hover {
            background-color: #138496;
        }
        .btn-delete {
            background-color: #dc3545;
        }
        .btn-delete:hover {
            background-color: #c82333;
        }
        .details-btn {
            background-color: #007bff;
            color: white;
            border: none;
        }
        .details-btn:hover {
            background-color: #0056b3;
            color: white;
        }
        
        /* Select2 styling */
        .select2-container {
            width: 100% !important;
            position: relative;
        }
        
        .select2-container--default .select2-selection--single {
            height: 38px !important;
            border: 1px solid #ced4da !important;
            border-radius: 0.25rem !important;
            width: 100% !important;
            background-color: #fff !important;
            display: flex !important;
            align-items: center !important;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 1.5 !important;
            padding-left: 12px !important;
            padding-right: 20px !important;
            color: #495057 !important;
            font-size: 1rem !important;
            font-weight: 400 !important;
            display: flex !important;
            align-items: center !important;
            height: 100% !important;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
            position: absolute !important;
            top: 1px !important;
            right: 1px !important;
            width: 20px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #6c757d transparent transparent transparent !important;
            border-style: solid !important;
            border-width: 5px 4px 0 4px !important;
            height: 0 !important;
            left: 50% !important;
            margin-left: -4px !important;
            margin-top: -2px !important;
            position: absolute !important;
            top: 50% !important;
            width: 0 !important;
        }
        
        .select2-result-part__partno {
            font-weight: bold;
            color: #007bff;
        }
        
        .select2-result-part__partname {
            color: #333;
            margin-top: 2px;
        }
        
        .select2-result-part__customer {
            color: #666;
            font-size: 12px;
            margin-top: 2px;
        }
        
        /* Additional Select2 alignment fixes */
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #80bdff !important;
            outline: 0 !important;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
        }
        
        .select2-dropdown {
            border: 1px solid #ced4da !important;
            border-radius: 0.25rem !important;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
        }
        
        .select2-search--dropdown .select2-search__field {
            border: 1px solid #ced4da !important;
            border-radius: 0.25rem !important;
            padding: 0.375rem 0.75rem !important;
            font-size: 1rem !important;
        }
        
        .select2-results__option {
            padding: 0.375rem 0.75rem !important;
            font-size: 1rem !important;
        }
        
        .select2-results__option--highlighted {
            background-color: #007bff !important;
            color: white !important;
        }
    </style>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">LOI Internal</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createModal">
                                <i class="fas fa-plus"></i> Create New
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped" id="loiInternalTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>RFQ No.</th>
                                    <th>Document No.</th>
                                    <th>Document Date</th>
                                    <th>Link Hasil Meeting</th>
                                    <th>Link LOI External</th>
                                    <th>Action</th>
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

    <!-- Create New Modal -->
    <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">Create New LOI Internal</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="createForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="rfqmaster_id">RFQ Master <span class="text-danger">*</span></label>
                            <select class="form-control" id="rfqmaster_id" name="rfqmaster_id" required>
                                <option value="">Select Part...</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="document_no">Document No <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="document_no" name="document_no" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="customer_name">Customer Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="document_date">Document Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="document_date" name="document_date" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for Hasil Meeting Details -->
    <div class="modal fade" id="hasilMeetingModal" tabindex="-1" role="dialog" aria-labelledby="hasilMeetingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="hasilMeetingModalLabel">Hasil Meeting Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="hasilMeetingList" class="row">
                        <!-- Hasil Meeting items will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for LOI External Details -->
    <div class="modal fade" id="loiExternalModal" tabindex="-1" role="dialog" aria-labelledby="loiExternalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loiExternalModalLabel">LOI External Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="loiExternalList" class="row">
                        <!-- LOI External items will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@stop

@push('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2 for parts dropdown when modal is shown
            $('#createModal').on('shown.bs.modal', function() {
                $('#rfqmaster_id').select2({
                    placeholder: 'Select Part...',
                    allowClear: true,
                    dropdownParent: $('#createModal'),
                    width: '100%',
                    ajax: {
                        url: "{{ route('loi.internals.parts') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term,
                                page: params.page
                            };
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;
                            return {
                                results: data.results,
                                pagination: {
                                    more: (params.page * 30) < data.total_count
                                }
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 1,
                    templateResult: formatPartResult,
                    templateSelection: formatPartSelection
                });
                
                // Force refresh to ensure proper alignment
                setTimeout(function() {
                    $('#rfqmaster_id').trigger('change.select2');
                }, 100);
            });

            // Format function for Select2 results
            function formatPartResult(part) {
                if (part.loading) {
                    return part.text;
                }
                var $container = $(
                    "<div class='select2-result-part clearfix'>" +
                        "<div class='select2-result-part__partno'>" + part.partno + "</div>" +
                        "<div class='select2-result-part__partname'>" + part.partname + "</div>" +
                        "<div class='select2-result-part__customer'>" + part.customername + "</div>" +
                    "</div>"
                );
                return $container;
            }

            // Format function for Select2 selection
            function formatPartSelection(part) {
                return part.partno || part.text;
            }

            // Initialize DataTable
            var table = $('#loiInternalTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('loi.internals.data') }}",
                    type: 'GET'
                },
                columns: [
                    { data: 'no', name: 'no', orderable: false },
                    { data: 'rfq_no', name: 'rfq_no' },
                    { data: 'document_no', name: 'document_no' },
                    { data: 'document_date', name: 'document_date' },
                    { data: 'link_hasil_meeting', name: 'link_hasil_meeting', orderable: false, searchable: false },
                    { data: 'link_loi_external', name: 'link_loi_external', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[0, 'asc']],
                pageLength: 10,
                responsive: true,
                language: {
                    processing: "Loading...",
                    emptyTable: "No data available",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    lengthMenu: "Show _MENU_ entries",
                    search: "Search:",
                    zeroRecords: "No matching records found"
                }
            });

            // Handle View Hasil Meeting button click
            $(document).on('click', '.view-hasil-meeting-btn', function() {
                var loiId = $(this).data('id');
                
                // Show loading
                $('#hasilMeetingList').html('<div class="col-12 text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
                $('#hasilMeetingModal').modal('show');
                
                // Fetch Hasil Meeting details
                $.ajax({
                    url: "{{ url('loi/internals') }}/" + loiId + "/hasil-meeting-details",
                    type: 'GET',
                    success: function(response) {
                        var html = '';
                        if (response.length > 0) {
                            response.forEach(function(item) {
                                // Check if the file is an image based on the file extension
                                var fileExtension = item.image.split('.').pop().toLowerCase();
                                var isImage = ['jpeg', 'jpg', 'png', 'gif', 'svg'].includes(fileExtension);
                                
                                html += '<div class="col-md-4 mb-3">';
                                html += '<div class="card">';
                                html += '<div class="card-header">';
                                html += '<h6 class="card-title">' + item.title + '</h6>';
                                html += '</div>';
                                html += '<div class="card-body text-center">';
                                
                                if (isImage) {
                                    html += '<img src="' + item.image + '" class="img-fluid" alt="' + item.title + '">';
                                } else {
                                    html += '<a href="' + item.image + '" target="_blank">';
                                    html += '<i class="fas fa-file-alt fa-5x"></i>';
                                    html += '<p>' + item.type + '</p>';
                                    html += '</a>';
                                }
                                
                                html += '</div>';
                                html += '</div>';
                                html += '</div>';
                            });
                        } else {
                            html = '<div class="col-12"><p class="text-center">No Hasil Meeting found for this record.</p></div>';
                        }
                        
                        $('#hasilMeetingList').html(html);
                    },
                    error: function(xhr) {
                        $('#hasilMeetingList').html('<div class="col-12"><p class="text-center text-danger">Error loading Hasil Meeting details.</p></div>');
                        console.error('Error loading Hasil Meeting details:', xhr);
                    }
                });
            });

            // Handle View LOI External button click
            $(document).on('click', '.view-loi-external-btn', function() {
                var loiId = $(this).data('id');
                
                // Show loading
                $('#loiExternalList').html('<div class="col-12 text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
                $('#loiExternalModal').modal('show');
                
                // Fetch LOI External details
                $.ajax({
                    url: "{{ url('loi/internals') }}/" + loiId + "/loi-external-details",
                    type: 'GET',
                    success: function(response) {
                        var html = '';
                        if (response.length > 0) {
                            response.forEach(function(item) {
                                // Check if the file is an image based on the file extension
                                var fileExtension = item.image.split('.').pop().toLowerCase();
                                var isImage = ['jpeg', 'jpg', 'png', 'gif', 'svg'].includes(fileExtension);
                                
                                html += '<div class="col-md-4 mb-3">';
                                html += '<div class="card">';
                                html += '<div class="card-header">';
                                html += '<h6 class="card-title">' + item.title + '</h6>';
                                html += '</div>';
                                html += '<div class="card-body text-center">';
                                
                                if (isImage) {
                                    html += '<img src="' + item.image + '" class="img-fluid" alt="' + item.title + '">';
                                } else {
                                    html += '<a href="' + item.image + '" target="_blank">';
                                    html += '<i class="fas fa-file-alt fa-5x"></i>';
                                    html += '<p>' + item.type + '</p>';
                                    html += '</a>';
                                }
                                
                                html += '</div>';
                                html += '</div>';
                                html += '</div>';
                            });
                        } else {
                            html = '<div class="col-12"><p class="text-center">No LOI External found for this record.</p></div>';
                        }
                        
                        $('#loiExternalList').html(html);
                    },
                    error: function(xhr) {
                        $('#loiExternalList').html('<div class="col-12"><p class="text-center text-danger">Error loading LOI External details.</p></div>');
                        console.error('Error loading LOI External details:', xhr);
                    }
                });
            });

            // Handle Details button click
            $(document).on('click', '.details-btn', function() {
                var loiId = $(this).data('id');
                
                // Show loading
                $('#loiExternalList').html('<div class="col-12 text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
                $('#loiExternalModal').modal('show');
                
                // Fetch LOI External details
                $.ajax({
                    url: "{{ url('loi/internals') }}/" + loiId + "/loi-external-details",
                    type: 'GET',
                    success: function(response) {
                        var html = '';
                        if (response.length > 0) {
                            response.forEach(function(item) {
                                // Check if the file is an image based on the file extension
                                var fileExtension = item.image.split('.').pop().toLowerCase();
                                var isImage = ['jpeg', 'jpg', 'png', 'gif', 'svg'].includes(fileExtension);
                                
                                html += '<div class="col-md-4 mb-3">';
                                html += '<div class="card">';
                                html += '<div class="card-header">';
                                html += '<h6 class="card-title">' + item.title + '</h6>';
                                html += '</div>';
                                html += '<div class="card-body text-center">';
                                
                                if (isImage) {
                                    html += '<img src="' + item.image + '" class="img-fluid" alt="' + item.title + '">';
                                } else {
                                    html += '<a href="' + item.image + '" target="_blank">';
                                    html += '<i class="fas fa-file-alt fa-5x"></i>';
                                    html += '<p>' + item.type + '</p>';
                                    html += '</a>';
                                }
                                
                                html += '</div>';
                                html += '</div>';
                                html += '</div>';
                            });
                        } else {
                            html = '<div class="col-12"><p class="text-center">No LOI External found for this record.</p></div>';
                        }
                        
                        $('#loiExternalList').html(html);
                    },
                    error: function(xhr) {
                        $('#loiExternalList').html('<div class="col-12"><p class="text-center text-danger">Error loading LOI External details.</p></div>');
                        console.error('Error loading LOI External details:', xhr);
                    }
                });
            });

            // Handle Create Form submission
            $('#createForm').on('submit', function(e) {
                e.preventDefault();
                
                var formData = {
                    rfqmaster_id: $('#rfqmaster_id').val(),
                    document_no: $('#document_no').val(),
                    customer_name: $('#customer_name').val(),
                    document_date: $('#document_date').val(),
                    _token: $('meta[name="csrf-token"]').attr('content')
                };

                $.ajax({
                    url: "{{ route('loi.internals.store') }}",
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $('#createModal').modal('hide');
                            $('#createForm')[0].reset();
                            $('#rfqmaster_id').val(null).trigger('change');
                            // Destroy Select2 to prevent conflicts
                            if ($('#rfqmaster_id').hasClass('select2-hidden-accessible')) {
                                $('#rfqmaster_id').select2('destroy');
                            }
                            
                            // Refresh DataTable
                            table.ajax.reload();
                            
                            // Show success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            // Validation errors
                            var errors = xhr.responseJSON.errors;
                            var errorMessage = 'Please fix the following errors:\n';
                            for (var field in errors) {
                                errorMessage += '- ' + errors[field][0] + '\n';
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                text: errorMessage
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: xhr.responseJSON.message || 'An error occurred while creating LOI Internal.'
                            });
                        }
                    }
                });
            });

            // Reset form when modal is closed
            $('#createModal').on('hidden.bs.modal', function() {
                $('#createForm')[0].reset();
                $('#rfqmaster_id').val(null).trigger('change');
                // Destroy Select2 to prevent conflicts
                if ($('#rfqmaster_id').hasClass('select2-hidden-accessible')) {
                    $('#rfqmaster_id').select2('destroy');
                }
            });
        });
    </script>
@endpush
