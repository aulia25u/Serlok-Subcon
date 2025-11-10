@extends('layouts.app')

@section('title', 'Upload Drawing')
@section('page-title', 'Upload Drawing')

{{-- Add the CSS links for Bootstrap and custom styles --}}
@section('css')
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .table-responsive {
            border: 1px solid #dee2e6;
        }
        .sticky-top {
            position: sticky;
            top: 0;
            z-index: 10;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .card-body.card-body-full {
            display: flex;
            flex-direction: column;
            height: calc(100vh - 220px); /* Adjust based on header/footer height */
        }
        #table-container {
            flex: 1;
            min-height: 300px; /* Minimum height for usability */
        }
        #table-container::-webkit-scrollbar {
            width: 8px;
        }
        #table-container::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        #table-container::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        #table-container::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Modal styling improvements */
        .modal-header.bg-success {
            background-color: #28a745 !important;
        }

        .modal-header.bg-danger {
            background-color: #dc3545 !important;
        }

        .modal-content {
            border-radius: 8px;
            border: none;
        }

        .modal-header {
            border-radius: 8px 8px 0 0;
        }

        #errorMessage ul {
            padding-left: 20px;
        }

        #errorMessage li {
            margin-bottom: 5px;
        }
    </style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Upload Drawing Management</h3>
<!--                     <div class="card-tools">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addModal">
                            <i class="fas fa-plus"></i> Add New
                        </button>
                    </div> -->
                </div>
                <div class="card-body card-body-full">
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
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="searchBox" placeholder="Search parts...">
                        </div>
                    </div>
                    <div class="table-responsive" id="table-container" style="overflow-y: auto;">
                        <table class="table table-bordered table-striped" id="plantTable">
                            <thead class="sticky-top bg-white">
                                <tr>
                                    <th>No</th>
                                    <th>Part Name</th>
                                    <th>Part No</th>
                                    <th>HS Code</th>
                                    <th>Customer Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="table-body">
                            </tbody>
                        </table>
                        <div id="loading" class="text-center py-3" style="display: none;">
                            <i class="fas fa-spinner fa-spin"></i> Loading...
                        </div>
                        <div id="no-more-data" class="text-center py-3" style="display: none;">
                            <i class="fas fa-check"></i> All data loaded
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Add New Drawing</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="attachmentForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="part_id" id="partId">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="attachment_type">Attachment Type</label>
                        <input type="text" class="form-control" id="attachment_type" name="attachment_type" required>
                    </div>
                    <div class="form-group">
                        <label for="attachment_title">Attachment Title</label>
                        <input type="text" class="form-control" id="attachment_title" name="attachment_title" required>
                    </div>
                    <div class="form-group">
                        <label for="attachment_file">Attachment File</label>
                        <input type="file" class="form-control-file" id="attachment_file" name="attachment_file" required>
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

<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">View Drawings</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="drawingList" class="row">
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Include Success Modal Component --}}
<x-success-modal />

{{-- Include Error Modal Component --}}
<x-error-modal />
@endsection

@push('scripts')
{{-- Ensure these are loaded after jQuery and before your custom script --}}
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let currentPage = 1;
        let isLoading = false;
        let hasMoreData = true;
        let searchTimeout;

        function loadData(page = 1, reset = false) {
            if (isLoading || (!hasMoreData && !reset)) return;

            isLoading = true;
            $('#loading').show();

            let params = {
                page: page,
                start_date: $('#start_date').val(),
                end_date: $('#end_date').val(),
                search: $('#searchBox').val()
            };

            $.ajax({
                url: "{{ route('upload.drawing') }}",
                type: 'GET',
                data: params,
                success: function(response) {
                    if (reset) {
                        $('#table-body').empty();
                        currentPage = 1;
                        hasMoreData = true;
                    }

                    if (response.data && response.data.length > 0) {
                        let rows = '';
                        response.data.forEach(function(item) {
                            rows += `
                                <tr>
                                    <td>${item.no}</td>
                                    <td>${item.partname}</td>
                                    <td>${item.partno}</td>
                                    <td>${item.hscode}</td>
                                    <td>${item.customername}</td>
                                    <td>${item.action}</td>
                                </tr>
                            `;
                        });
                        $('#table-body').append(rows);
                    }

                    hasMoreData = response.has_more;
                    if (!hasMoreData) {
                        $('#no-more-data').show();
                    } else {
                        $('#no-more-data').hide();
                    }

                    if (reset) {
                        currentPage = 2;
                    } else {
                        currentPage++;
                    }
                },
                error: function(xhr) {
                    console.error('Error loading data:', xhr);
                },
                complete: function() {
                    isLoading = false;
                    $('#loading').hide();
                }
            });
        }

        // Initial load
        loadData(1, true);

        // Infinity scroll detection
        $('#table-container').on('scroll', function() {
            let container = $(this);
            if (container.scrollTop() + container.innerHeight() >= container[0].scrollHeight - 50) {
                loadData(currentPage);
            }
        });

        // Search functionality with debounce
        $('#searchBox').on('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                loadData(1, true);
            }, 300);
        });

        $('#filterBtn').click(function() {
            loadData(1, true);
        });

        $('#resetBtn').click(function() {
            $('#start_date, #end_date, #searchBox').val('');
            loadData(1, true);
        });

        // Handle "Add" button click to open the add modal
        $(document).on('click', '.add-drawing-btn', function() {
            let partId = $(this).data('id');
            $('#attachmentForm')[0].reset();
            $('#addModalLabel').text('Add New Drawing');
            $('#partId').val(partId);
            $('#addModal').modal('show');
        });

        // Handle form submission for file upload
        $('#attachmentForm').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('upload.drawing.store') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#addModal').modal('hide');
                    loadData(1, true); // Reload data instead of table.draw()
                    $('#successMessage').text(response.success);
                    $('#successDescription').text('Drawing telah berhasil disimpan ke dalam sistem.');
                    $('#successModal').modal('show');
                },
                error: function(xhr) {
                    let errorMsg = '';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        $.each(xhr.responseJSON.errors, function(key, value) {
                            errorMsg += '<li>' + value[0] + '</li>';
                        });
                        errorMsg = '<ul class="mb-0 text-left">' + errorMsg + '</ul>';
                    } else if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMsg = xhr.responseJSON.error;
                    } else {
                        errorMsg = 'Terjadi kesalahan yang tidak diketahui. Silakan coba lagi.';
                    }
                    $('#errorMessage').html(errorMsg);
                    $('#errorModal').modal('show');
                }
            });
        });

        // Handle "View" button click
        // Handle "View" button click
        $(document).on('click', '.view-btn', function() {
            let partId = $(this).data('id');
            let viewUrl = "{{ url('upload/drawing') }}/" + partId;

            $.ajax({
                url: viewUrl,
                type: 'GET',
                success: function(response) {
                    let drawingListHtml = '';
                    if (response.length > 0) {
                        response.forEach(drawing => {
                            // Check if the file is an image based on the file extension
                            let fileExtension = drawing.image.split('.').pop().toLowerCase();
                            let isImage = ['jpeg', 'jpg', 'png', 'gif', 'svg'].includes(fileExtension);

                            let imageUrl = drawing.image;

                            drawingListHtml += `
                                <div class="col-md-4 mb-3">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="card-title">${drawing.title}</h6>
                                        </div>
                                        <div class="card-body text-center">
                                            ${isImage ? `<img src="${imageUrl}" class="img-fluid" alt="${drawing.title}">` : `
                                                <a href="${imageUrl}" target="_blank">
                                                    <i class="fas fa-file-alt fa-5x"></i>
                                                    <p>${drawing.type}</p>
                                                </a>
                                            `}
                                        </div>
                                        <div class="card-footer text-right">
                                            <button class="btn btn-danger btn-sm delete-drawing-btn" data-id="${drawing.id}">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                    } else {
                        drawingListHtml = '<div class="col-12"><p class="text-center">No drawings found for this part.</p></div>';
                    }
                    $('#drawingList').html(drawingListHtml);
                    $('#viewModal').modal('show');
                }
            });
        });

        // Handle "Delete" button click inside the view modal
        $(document).on('click', '.delete-drawing-btn', function() {
            let drawingId = $(this).data('id');
            let deleteUrl = `/upload/drawing/${drawingId}`;
            let button = $(this);

            if (confirm('Are you sure you want to delete this drawing?')) {
                $.ajax({
                    url: deleteUrl,
                    type: 'DELETE',
                    success: function(response) {
                        $('#successMessage').text(response.success);
                        $('#successDescription').text('Drawing telah berhasil dihapus dari sistem.');
                        $('#successModal').modal('show');
                        button.closest('.col-md-4').remove();
                        if ($('#drawingList .col-md-4').length === 0) {
                            $('#drawingList').html('<div class="col-12"><p class="text-center">No drawings found for this part.</p></div>');
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = xhr.responseJSON && xhr.responseJSON.error ?
                                      xhr.responseJSON.error :
                                      'Terjadi kesalahan saat menghapus file. Silakan coba lagi.';
                        $('#errorMessage').html(errorMsg);
                        $('#errorModal').modal('show');
                    }
                });
            }
        });
    });
</script>
@endpush
