@extends('adminlte::page')

@section('title', 'View Tooling Inspection')

@section('content_header')
    <h1>Tooling Inspection Details</h1>
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <style>
        .inspection-table th,
        .inspection-table td {
            vertical-align: middle;
            font-size: 13px;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .image-preview-box {
            border: 2px solid #ddd;
            border-radius: 4px;
            padding: 20px;
            text-align: center;
            background-color: #f9f9f9;
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .image-preview-box img {
            max-width: 100%;
            max-height: 300px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .image-preview-box img:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .tooling-type-badge {
            display: inline-block;
            padding: 8px 16px;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            border-radius: 8px;
            font-weight: bold;
        }

        .info-label {
            font-weight: bold;
            color: #495057;
        }

        .info-value {
            color: #212529;
        }

        /* Modal styles */
        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.8);
        }

        #imageModal .modal-dialog {
            max-width: 90%;
            margin: 30px auto;
        }

        #imageModal .modal-content {
            background: transparent;
            border: none;
        }

        #imageModal .modal-body {
            padding: 0;
            text-align: center;
        }

        #imageModal img {
            max-width: 100%;
            max-height: 85vh;
            object-fit: contain;
            border-radius: 8px;
        }

        #imageModal .close {
            position: absolute;
            top: -10px;
            right: 10px;
            color: white;
            font-size: 40px;
            font-weight: bold;
            z-index: 1051;
            opacity: 0.8;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
        }

        #imageModal .close:hover {
            opacity: 1;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Tooling Inspection Information</h3>
            <div class="card-tools no-print">
                <a href="{{ route('loi.tooling-inspection.edit', $toolingInspection->id) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('loi.tooling-inspection.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Basic Information -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="form-group row">
                        <label class="col-sm-4 info-label">Customer:</label>
                        <div class="col-sm-8 info-value">
                            {{ $toolingInspection->customer ?? 'N/A' }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 info-label">Part Number:</label>
                        <div class="col-sm-8 info-value">
                            {{ $toolingInspection->part_no ?? 'N/A' }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 info-label">Date:</label>
                        <div class="col-sm-8 info-value">
                            {{ \Carbon\Carbon::parse($toolingInspection->date)->format('d M Y') }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 info-label">Quantity:</label>
                        <div class="col-sm-8 info-value">
                            {{ $toolingInspection->quantity }} PCS
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group row">
                        <label class="col-sm-4 info-label">Tooling Type:</label>
                        <div class="col-sm-8">
                            <span class="tooling-type-badge">
                                {{ str_replace('_', ' ', strtoupper($toolingInspection->tooling_type)) }}
                            </span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 info-label">Result:</label>
                        <div class="col-sm-8">
                            @if ($toolingInspection->result == 'OK')
                                <span class="badge badge-success" style="font-size: 16px; padding: 8px 16px;">
                                    <i class="fas fa-check-circle"></i> OK
                                </span>
                            @else
                                <span class="badge badge-danger" style="font-size: 16px; padding: 8px 16px;">
                                    <i class="fas fa-times-circle"></i> NG
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Drawing Sketch -->
            @if ($toolingInspection->image)
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5 class="mb-3">Drawing Sketch</h5>
                        <div class="image-preview-box">
                            <img src="{{ $toolingInspection->image }}" alt="Drawing Sketch" id="previewImage"
                                title="Click to zoom">
                        </div>
                    </div>
                </div>
            @endif

            <!-- Inspection Items Table -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <h5 class="mb-3">Inspection Items</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped inspection-table">
                            <thead class="thead-dark">
                                <tr>
                                    <th rowspan="2" style="text-align: center; vertical-align: middle;">No</th>
                                    <th rowspan="2" style="text-align: center; vertical-align: middle;">Inspection Item
                                    </th>
                                    <th rowspan="2" style="text-align: center; vertical-align: middle;">Method/Equipment
                                    </th>
                                    <th rowspan="2" style="text-align: center; vertical-align: middle;">Standard</th>
                                    <th colspan="10" style="text-align: center;">Number of Tooling</th>
                                    <th rowspan="2" style="text-align: center; vertical-align: middle;">X̄</th>
                                    <th rowspan="2" style="text-align: center; vertical-align: middle;">R</th>
                                </tr>
                                <tr>
                                    @for ($i = 1; $i <= 10; $i++)
                                        <th style="text-align: center;">{{ $i }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($toolingInspection->items as $index => $item)
                                    <tr>
                                        <td style="text-align: center;">{{ $index + 1 }}</td>
                                        <td>{{ $item->inspection_item ?? '-' }}</td>
                                        <td>{{ $item->inspection_method ?? '-' }}</td>
                                        <td>{{ $item->standard ?? '-' }}</td>
                                        @for ($i = 1; $i <= 10; $i++)
                                            <td style="text-align: center;">{{ $item->{'tooling_' . $i} ?? '-' }}</td>
                                        @endfor
                                        <td style="text-align: center;">{{ $item->x_bar ?? '-' }}</td>
                                        <td style="text-align: center;">{{ $item->r_value ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="17" class="text-center">No inspection items found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            @if ($toolingInspection->note)
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5 class="mb-3">Notes</h5>
                        <div class="card">
                            <div class="card-body">
                                {{ $toolingInspection->note }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Signature Section -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><strong>Inspected By</strong></h5>
                        </div>
                        <div class="card-body text-center" style="min-height: 150px;">
                            <div class="signature-area"
                                style="height: 80px; border-bottom: 2px solid #dee2e6; margin-bottom: 10px;">
                                <!-- Space for signature -->
                            </div>
                            <p class="mb-0">
                                <strong>{{ $toolingInspection->inspector && $toolingInspection->inspector->userDetail ? $toolingInspection->inspector->userDetail->employee_name : 'N/A' }}</strong>
                            </p>
                            <small class="text-muted">Inspector</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><strong>Approved By</strong></h5>
                        </div>
                        <div class="card-body text-center" style="min-height: 150px;">
                            <div class="signature-area"
                                style="height: 80px; border-bottom: 2px solid #dee2e6; margin-bottom: 10px;">
                                <!-- Space for signature -->
                            </div>
                            <p class="mb-0">
                                <strong>{{ $toolingInspection->approver && $toolingInspection->approver->userDetail ? $toolingInspection->approver->userDetail->employee_name : 'N/A' }}</strong>
                            </p>
                            <small class="text-muted">Approver</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer no-print">
            <a href="{{ route('loi.tooling-inspection.edit', $toolingInspection->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('loi.tooling-inspection.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
            <button onclick="window.print()" class="btn btn-info">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="background-color: rgba(0,0,0,0.9);">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <div class="modal-body">
                    <img src="" id="modalImage" alt="Drawing Sketch" style="width: 100%; height: auto;">
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Image zoom modal functionality
            $('#previewImage').on('click', function() {
                const imgSrc = $(this).attr('src');
                $('#modalImage').attr('src', imgSrc);
                $('#imageModal').modal('show');
            });

            // Close modal when clicking the image
            $('#modalImage').on('click', function() {
                $('#imageModal').modal('hide');
            });

            // Close modal with ESC key
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && $('#imageModal').hasClass('show')) {
                    $('#imageModal').modal('hide');
                }
            });

            // Close modal when clicking outside the image
            $('#imageModal').on('click', function(e) {
                if ($(e.target).is('#imageModal') || $(e.target).is('.modal-content')) {
                    $('#imageModal').modal('hide');
                }
            });
        });
    </script>
@stop
