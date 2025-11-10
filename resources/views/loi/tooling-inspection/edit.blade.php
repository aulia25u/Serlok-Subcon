@extends('layouts.app')

@section('title', 'Edit Tooling Inspection')



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

        .inspection-table input,
        .inspection-table select {
            font-size: 13px;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Make tooling input fields narrower */
        .inspection-table input[name*="tooling_"],
        .inspection-table input[name*="x_bar"],
        .inspection-table input[name*="r_value"] {
            min-width: 60px;
            padding: 0.25rem 0.35rem;
        }

        .image-preview-box {
            border: 2px dashed #ddd;
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
        }

        /* Decision Button Styles from Estimate New Item */
        .decision-switch-container {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 20px;
        }

        .decision-switch {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .decision-btn {
            flex: 1;
            padding: 15px;
            font-size: 18px;
            font-weight: bold;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            background-color: white;
            transition: all 0.3s ease;
        }

        .decision-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Hover effects for OK and NG buttons */
        #btnResultOK:hover:not(.active-ok) {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-color: #28a745;
            opacity: 0.8;
        }

        #btnResultNG:hover:not(.active-no) {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            border-color: #dc3545;
            opacity: 0.8;
        }

        .decision-btn.active-ok {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-color: #28a745;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }

        .decision-btn.active-no {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            border-color: #dc3545;
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }

        /* Tooling Type Card Styles */
        .tooling-type-card {
            height: 100%;
        }

        .tooling-type-option {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
        }

        .tooling-type-option:hover {
            background: #f8f9fa;
            border-color: #007bff;
        }

        .tooling-type-option.active {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border-color: #007bff;
            color: white;
            box-shadow: 0 3px 10px rgba(0, 123, 255, 0.3);
        }

        .tooling-type-option input[type="radio"] {
            margin-right: 12px;
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .tooling-type-option label {
            margin-bottom: 0;
            cursor: pointer;
            font-weight: 500;
            flex: 1;
        }

        /* Image Preview Modal Styles */
        .image-preview-box img {
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .image-preview-box img:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* Modal Image Zoom Styles */
        .modal-image-zoom {
            cursor: zoom-out;
            max-width: 100%;
            max-height: 80vh;
            object-fit: contain;
        }

        .modal-dialog-zoom {
            max-width: 90vw;
            margin: 1.75rem auto;
        }

        .modal-body-zoom {
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #000;
        }

        /* Required field validation styles */
        .is-invalid-custom {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
        }

        .tooling-type-card.is-invalid-custom {
            border: 2px solid #dc3545 !important;
        }

        .decision-switch-container.is-invalid-custom {
            border: 2px solid #dc3545 !important;
        }
    </style>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-tools"></i> Tooling Inspection Form</h3>
        </div>
        <form id="toolingInspectionForm" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-body">
                <!-- Basic Information Section -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="customer_id">Customer <span class="text-danger">*</span></label>
                            <select class="form-control" id="customer_id" name="customer_id" required>
                                <option value="">Select Customer</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" data-name="{{ $customer->name }}"
                                        {{ $toolingInspection->customer_id == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->kode }} - {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" id="customer" name="customer" value="{{ $toolingInspection->customer }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="date" name="date"
                                value="{{ $toolingInspection->date ? $toolingInspection->date->format('Y-m-d') : '' }}"
                                required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="quantity">Quantity (PCS) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="quantity" name="quantity" min="1"
                                value="{{ $toolingInspection->quantity }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="part_no">Part Number <span class="text-danger">*</span></label>
                            <select class="form-control" id="part_no" name="part_no" required>
                                <option value="">Select Customer First</option>
                            </select>
                            <small class="form-text text-muted">Part numbers are loaded based on selected customer from
                                feasible commitments</small>
                        </div>
                    </div>
                </div>

                <!-- Drawing Sketch Section -->
                <div class="form-group">
                    <label for="image">Drawing Sketch</label>
                    <input type="file" class="form-control-file" id="image" name="image" accept="image/*">
                    <small class="form-text text-muted">Max size: 5MB. Formats: JPEG, PNG, JPG, GIF. Leave empty to keep
                        current image.</small>
                </div>

                <div class="form-group">
                    <div class="image-preview-box" id="imagePreviewBox">
                        @if ($toolingInspection->image)
                            <img src="{{ asset('storage/' . $toolingInspection->image) }}" alt="Current Drawing Sketch"
                                class="preview-image" data-toggle="tooltip" title="Click to zoom">
                        @else
                            <span class="text-muted">No image selected</span>
                        @endif
                    </div>
                </div>

                <!-- Inspection Items Section -->
                <div class="form-group">
                    <label class="font-weight-bold">Inspection Items</label>
                    <button type="button" class="btn btn-success btn-sm float-right" id="addRowBtn">
                        <i class="fas fa-plus"></i> Add Row
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm inspection-table" id="inspectionTable">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-center" rowspan="2" style="width: 40px; vertical-align: middle;">No</th>
                                <th class="text-center" rowspan="2" style="min-width: 200px; vertical-align: middle;">
                                    Inspection Item</th>
                                <th class="text-center" rowspan="2" style="min-width: 150px; vertical-align: middle;">
                                    Inspection Method/Equipment</th>
                                <th class="text-center" rowspan="2" style="min-width: 120px; vertical-align: middle;">
                                    Standard</th>
                                <th class="text-center" colspan="10">Number of Tooling</th>
                                <th class="text-center" rowspan="2" style="width: 80px; vertical-align: middle;">X̄
                                </th>
                                <th class="text-center" rowspan="2" style="width: 80px; vertical-align: middle;">R
                                </th>
                                <th class="text-center" rowspan="2" style="width: 70px; vertical-align: middle;">
                                    Action
                                </th>
                            </tr>
                            <tr>
                                <th class="text-center" style="width: 70px;">1</th>
                                <th class="text-center" style="width: 70px;">2</th>
                                <th class="text-center" style="width: 70px;">3</th>
                                <th class="text-center" style="width: 70px;">4</th>
                                <th class="text-center" style="width: 70px;">5</th>
                                <th class="text-center" style="width: 70px;">6</th>
                                <th class="text-center" style="width: 70px;">7</th>
                                <th class="text-center" style="width: 70px;">8</th>
                                <th class="text-center" style="width: 70px;">9</th>
                                <th class="text-center" style="width: 70px;">10</th>
                            </tr>
                        </thead>
                        <tbody id="inspectionTableBody">
                        </tbody>
                    </table>
                </div>

                <!-- Result and Tooling Type Section and Notes -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card tooling-type-card">
                            <div class="card-header">
                                <h3 class="card-title font-weight-bold">Tooling Type <span class="text-danger">*</span>
                                </h3>
                            </div>
                            <div class="card-body">
                                <div
                                    class="tooling-type-option {{ $toolingInspection->tooling_type == 'KIGATA' ? 'active' : '' }}">
                                    <input type="radio" name="tooling_type" id="tooling_type_kigata" value="KIGATA"
                                        {{ $toolingInspection->tooling_type == 'KIGATA' ? 'checked' : '' }} required>
                                    <label for="tooling_type_kigata">KIGATA</label>
                                </div>
                                <div
                                    class="tooling-type-option {{ $toolingInspection->tooling_type == 'MANDREL' ? 'active' : '' }}">
                                    <input type="radio" name="tooling_type" id="tooling_type_mandrel" value="MANDREL"
                                        {{ $toolingInspection->tooling_type == 'MANDREL' ? 'checked' : '' }} required>
                                    <label for="tooling_type_mandrel">MANDREL</label>
                                </div>
                                <div
                                    class="tooling-type-option {{ $toolingInspection->tooling_type == 'GONOGO' ? 'active' : '' }}">
                                    <input type="radio" name="tooling_type" id="tooling_type_gonogo" value="GONOGO"
                                        {{ $toolingInspection->tooling_type == 'GONOGO' ? 'checked' : '' }} required>
                                    <label for="tooling_type_gonogo">GONOGO</label>
                                </div>
                                <div
                                    class="tooling-type-option {{ $toolingInspection->tooling_type == 'MALL_CUTTING' ? 'active' : '' }}">
                                    <input type="radio" name="tooling_type" id="tooling_type_mall_cutting"
                                        value="MALL_CUTTING"
                                        {{ $toolingInspection->tooling_type == 'MALL_CUTTING' ? 'checked' : '' }} required>
                                    <label for="tooling_type_mall_cutting">MALL CUTTING</label>
                                </div>
                                <div
                                    class="tooling-type-option {{ $toolingInspection->tooling_type == 'MALL_CHECKING' ? 'active' : '' }}">
                                    <input type="radio" name="tooling_type" id="tooling_type_mall_checking"
                                        value="MALL_CHECKING"
                                        {{ $toolingInspection->tooling_type == 'MALL_CHECKING' ? 'checked' : '' }}
                                        required>
                                    <label for="tooling_type_mall_checking">MALL CHECKING</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="decision-switch-container">
                            <label><strong>Result <span class="text-danger">*</span></strong></label>
                            <div class="decision-switch">
                                <button type="button"
                                    class="btn decision-btn {{ $toolingInspection->result == 'OK' ? 'active-ok' : '' }}"
                                    id="btnResultOK">OK</button>
                                <button type="button"
                                    class="btn decision-btn {{ $toolingInspection->result == 'NG' ? 'active-no' : '' }}"
                                    id="btnResultNG">NG</button>
                            </div>
                            <input type="hidden" name="result" id="result"
                                value="{{ $toolingInspection->result }}" required>
                        </div>

                        <!-- Note Section -->
                        <div class="form-group mt-4">
                            <label for="note">Note</label>
                            <textarea class="form-control" id="note" name="note" rows="6"
                                placeholder="Enter additional notes...">{{ $toolingInspection->note }}</textarea>
                        </div>
                    </div>


                </div>

                <!-- Signature Section -->
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="inspected_by">Inspected By <span class="text-danger">*</span></label>
                            <select class="form-control" id="inspected_by" name="inspected_by" required>
                                <option value="">Select Inspector</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user['id'] }}"
                                        {{ $toolingInspection->inspected_by == $user['id'] ? 'selected' : '' }}>
                                        {{ $user['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="approved_by">Approved By <span class="text-danger">*</span></label>
                            <select class="form-control" id="approved_by" name="approved_by" required>
                                <option value="">Select Approver</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user['id'] }}"
                                        {{ $toolingInspection->approved_by == $user['id'] ? 'selected' : '' }}>
                                        {{ $user['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

            </div>

            <div class="card-footer d-flex justify-content-center">
                <button type="submit" class="btn btn-primary mr-2" id="submitBtn">
                    <i class="fas fa-save"></i> Update Tooling Inspection
                </button>
                <a href="{{ route('loi.tooling-inspection.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
    </div>
    </form>
    </div>

    <!-- Image Zoom Modal -->
    <div class="modal fade" id="imageZoomModal" tabindex="-1" role="dialog" aria-labelledby="imageZoomModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-zoom modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageZoomModalLabel">Drawing Sketch Preview</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body modal-body-zoom">
                    <img src="" alt="Preview" class="modal-image-zoom" id="modalPreviewImage">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            let rowCount = 0;

            // Feasibility data from controller
            const feasibilityData = @json($feasibilityData);

            // Existing inspection items
            const existingItems = @json($toolingInspection->items);

            // Initialize Select2
            $('#customer_id, #part_no, #inspected_by, #approved_by').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });

            // Remove validation error styling when part_no is selected
            $('#part_no').on('select2:select', function() {
                $(this).next('.select2-container').find('.select2-selection').removeClass(
                    'is-invalid-custom');
            });

            // Remove validation error styling when inspected_by is selected
            $('#inspected_by').on('select2:select', function() {
                $(this).next('.select2-container').find('.select2-selection').removeClass(
                    'is-invalid-custom');
            });

            // Remove validation error styling when approved_by is selected
            $('#approved_by').on('select2:select', function() {
                $(this).next('.select2-container').find('.select2-selection').removeClass(
                    'is-invalid-custom');
            });

            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();

            // Keyboard shortcut to close modal (ESC key)
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && $('#imageZoomModal').hasClass('show')) {
                    $('#imageZoomModal').modal('hide');
                }
            });

            // Handle Customer change
            $('#customer_id').change(function() {
                const selectedOption = $(this).find(':selected');
                const customerName = selectedOption.data('name');
                const customerId = $(this).val();

                $('#customer').val(customerName);

                // Load part numbers for selected customer
                loadPartNumbers(customerId);
            });

            // Function to load part numbers based on customer
            function loadPartNumbers(customerId, selectedPartNo = null) {
                const partNoSelect = $('#part_no');
                partNoSelect.empty();

                if (!customerId || !feasibilityData[customerId]) {
                    partNoSelect.append('<option value="">No part numbers available</option>');
                    partNoSelect.prop('disabled', true);
                    return;
                }

                partNoSelect.prop('disabled', false);
                partNoSelect.append('<option value="">Select Part Number</option>');

                const parts = feasibilityData[customerId];
                parts.forEach(function(part) {
                    let optionText = part.part_no;
                    if (part.part_name) {
                        optionText += ' - ' + part.part_name;
                    }
                    if (part.model) {
                        optionText += ' (' + part.model + ')';
                    }

                    const option = $('<option></option>')
                        .val(part.part_no)
                        .text(optionText);

                    if (selectedPartNo && part.part_no === selectedPartNo) {
                        option.prop('selected', true);
                    }

                    partNoSelect.append(option);
                });

                // Reinitialize Select2 after updating options
                partNoSelect.select2({
                    theme: 'bootstrap-5',
                    width: '100%'
                });
            }

            // Load part numbers for the initially selected customer
            const initialCustomerId = $('#customer_id').val();
            const initialPartNo = '{{ $toolingInspection->part_no }}';
            if (initialCustomerId) {
                loadPartNumbers(initialCustomerId, initialPartNo);
            }

            // Handle image preview with validation
            $('#image').change(function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Validate file size (5MB = 5 * 1024 * 1024 bytes)
                    const maxSize = 5 * 1024 * 1024; // 5MB in bytes
                    if (file.size > maxSize) {
                        Swal.fire({
                            icon: 'error',
                            title: 'File Too Large',
                            text: 'Image size must not exceed 5MB. Your file size is ' + (file
                                .size / (1024 * 1024)).toFixed(2) + 'MB',
                        });
                        // Clear the file input
                        $(this).val('');
                        return;
                    }

                    // Validate file type
                    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                    if (!allowedTypes.includes(file.type)) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid File Type',
                            text: 'Only JPEG, PNG, JPG, and GIF files are allowed.',
                        });
                        // Clear the file input
                        $(this).val('');
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#imagePreviewBox').html(
                            '<img src="' + e.target.result +
                            '" alt="Preview" class="preview-image" data-toggle="tooltip" title="Click to zoom">'
                        );

                        // Add click event to open modal
                        $('.preview-image').on('click', function() {
                            const imgSrc = $(this).attr('src');
                            $('#modalPreviewImage').attr('src', imgSrc);
                            $('#imageZoomModal').modal('show');
                        });
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Click event for existing image preview
            $(document).on('click', '.preview-image', function() {
                const imgSrc = $(this).attr('src');
                $('#modalPreviewImage').attr('src', imgSrc);
                $('#imageZoomModal').modal('show');
            });

            // Close modal when clicking on the image
            $('#modalPreviewImage').on('click', function() {
                $('#imageZoomModal').modal('hide');
            });

            // Load existing inspection items
            if (existingItems && existingItems.length > 0) {
                existingItems.forEach(function(item, index) {
                    addInspectionRow(item);
                });
            } else {
                // Add initial row if no existing items
                addInspectionRow();
            }

            // Handle Add Row
            $('#addRowBtn').click(function() {
                addInspectionRow();
            });

            function addInspectionRow(itemData = null) {
                rowCount++;
                const newRow = `
                    <tr data-row="${rowCount}">
                        <td class="text-center">${rowCount}</td>
                        <td>
                            <select class="form-control form-control-sm inspection-select" name="items[${rowCount}][inspection_item]" data-row="${rowCount}" required>
                                <option value="">Select Item</option>
                                @foreach ($masterInspections as $inspection)
                                    <option value="{{ $inspection->inspection_item }}"
                                        data-method="{{ $inspection->inspection_method }}"
                                        data-standard="{{ $inspection->standard }}"
                                        ${itemData && itemData.inspection_item === '{{ $inspection->inspection_item }}' ? 'selected' : ''}>
                                        {{ $inspection->inspection_item }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm inspection-method-input"
                                name="items[${rowCount}][inspection_method]"
                                value="${itemData ? itemData.inspection_method || '' : ''}" readonly>
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm inspection-standard-input"
                                name="items[${rowCount}][standard]"
                                value="${itemData ? itemData.standard || '' : ''}" readonly>
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm" name="items[${rowCount}][tooling_1]"
                                placeholder="1" value="${itemData ? itemData.tooling_1 || '' : ''}">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm" name="items[${rowCount}][tooling_2]"
                                placeholder="2" value="${itemData ? itemData.tooling_2 || '' : ''}">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm" name="items[${rowCount}][tooling_3]"
                                placeholder="3" value="${itemData ? itemData.tooling_3 || '' : ''}">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm" name="items[${rowCount}][tooling_4]"
                                placeholder="4" value="${itemData ? itemData.tooling_4 || '' : ''}">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm" name="items[${rowCount}][tooling_5]"
                                placeholder="5" value="${itemData ? itemData.tooling_5 || '' : ''}">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm" name="items[${rowCount}][tooling_6]"
                                placeholder="6" value="${itemData ? itemData.tooling_6 || '' : ''}">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm" name="items[${rowCount}][tooling_7]"
                                placeholder="7" value="${itemData ? itemData.tooling_7 || '' : ''}">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm" name="items[${rowCount}][tooling_8]"
                                placeholder="8" value="${itemData ? itemData.tooling_8 || '' : ''}">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm" name="items[${rowCount}][tooling_9]"
                                placeholder="9" value="${itemData ? itemData.tooling_9 || '' : ''}">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm" name="items[${rowCount}][tooling_10]"
                                placeholder="10" value="${itemData ? itemData.tooling_10 || '' : ''}">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm" name="items[${rowCount}][x_bar]"
                                placeholder="X̄" value="${itemData ? itemData.x_bar || '' : ''}">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm" name="items[${rowCount}][r_value]"
                                placeholder="R" value="${itemData ? itemData.r_value || '' : ''}">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm remove-row">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#inspectionTableBody').append(newRow);

                // Reinitialize Select2 for new row
                $(`.inspection-select[data-row="${rowCount}"]`).select2({
                    theme: 'bootstrap-5',
                    width: '100%'
                });
            }

            // Handle Remove Row
            $(document).on('click', '.remove-row', function() {
                if ($('#inspectionTableBody tr').length > 1) {
                    $(this).closest('tr').remove();
                    // Renumber rows
                    $('#inspectionTableBody tr').each(function(index) {
                        $(this).find('td:first').text(index + 1);
                    });
                    rowCount = $('#inspectionTableBody tr').length;
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: 'At least one inspection item is required!'
                    });
                }
            });

            // Handle Inspection Item Selection
            $(document).on('change', '.inspection-select', function() {
                const selectedOption = $(this).find(':selected');
                const method = selectedOption.data('method') || '';
                const standard = selectedOption.data('standard') || '';
                const row = $(this).closest('tr');

                row.find('.inspection-method-input').val(method);
                row.find('.inspection-standard-input').val(standard);
            });

            // Handle Result Buttons (like Decision in Estimate New Item)
            $('#btnResultOK').click(function() {
                $('#result').val('OK');
                $(this).addClass('active-ok');
                $('#btnResultNG').removeClass('active-no');
                // Remove validation error styling when selected
                $('.decision-switch-container').removeClass('is-invalid-custom');
            });

            $('#btnResultNG').click(function() {
                $('#result').val('NG');
                $(this).addClass('active-no');
                $('#btnResultOK').removeClass('active-ok');
                // Remove validation error styling when selected
                $('.decision-switch-container').removeClass('is-invalid-custom');
            });

            // Handle Tooling Type Selection (radio buttons with visual feedback)
            $('input[name="tooling_type"]').change(function() {
                $('.tooling-type-option').removeClass('active');
                $(this).closest('.tooling-type-option').addClass('active');
                // Remove validation error styling when selected
                $('.tooling-type-card').removeClass('is-invalid-custom');
            });

            // Click on tooling-type-option div to select radio
            $('.tooling-type-option').click(function() {
                $(this).find('input[type="radio"]').prop('checked', true).trigger('change');
            });

            // Handle Form Submission
            $('#toolingInspectionForm').submit(function(e) {
                e.preventDefault();

                // Validate form
                if (!$('#customer_id').val()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please select a customer!'
                    });
                    return;
                }

                if (!$('#date').val()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please select a date!'
                    });
                    return;
                }

                if (!$('#quantity').val()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please enter quantity!'
                    });
                    return;
                }

                // Validate Part Number (Required)
                if (!$('#part_no').val()) {
                    $('#part_no').next('.select2-container').find('.select2-selection').addClass(
                        'is-invalid-custom');
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please select a part number!'
                    }).then(() => {
                        // Scroll to part number field
                        $('html, body').animate({
                            scrollTop: $('#part_no').offset().top - 100
                        }, 500);
                        $('#part_no').select2('open');
                    });
                    return;
                }

                // Check if at least one inspection item is added
                if ($('#inspectionTableBody tr').length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please add at least one inspection item!'
                    });
                    return;
                }

                // Validate that all inspection items have a selected value
                let hasEmptyInspectionItem = false;
                $('#inspectionTableBody .inspection-select').each(function() {
                    if (!$(this).val()) {
                        hasEmptyInspectionItem = true;
                        return false; // break the loop
                    }
                });

                if (hasEmptyInspectionItem) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please select an inspection item for all rows!'
                    });
                    return;
                }

                // Validate Tooling Type (Required)
                if (!$('input[name="tooling_type"]:checked').val()) {
                    $('.tooling-type-card').addClass('is-invalid-custom');
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please select a tooling type!'
                    }).then(() => {
                        // Scroll to tooling type section
                        $('html, body').animate({
                            scrollTop: $('.tooling-type-card').offset().top - 100
                        }, 500);
                    });
                    return;
                }

                // Validate Result (Required)
                if (!$('#result').val()) {
                    $('.decision-switch-container').addClass('is-invalid-custom');
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please select a result (OK or NG)!'
                    }).then(() => {
                        // Scroll to result section
                        $('html, body').animate({
                            scrollTop: $('.decision-switch-container').offset().top - 100
                        }, 500);
                    });
                    return;
                }

                // Validate Inspected By (Required)
                if (!$('#inspected_by').val()) {
                    $('#inspected_by').next('.select2-container').find('.select2-selection').addClass(
                        'is-invalid-custom');
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please select who inspected this tooling!'
                    }).then(() => {
                        // Scroll to inspected by field
                        $('html, body').animate({
                            scrollTop: $('#inspected_by').offset().top - 100
                        }, 500);
                        $('#inspected_by').select2('open');
                    });
                    return;
                }

                // Validate Approved By (Required)
                if (!$('#approved_by').val()) {
                    $('#approved_by').next('.select2-container').find('.select2-selection').addClass(
                        'is-invalid-custom');
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please select who approved this inspection!'
                    }).then(() => {
                        // Scroll to approved by field
                        $('html, body').animate({
                            scrollTop: $('#approved_by').offset().top - 100
                        }, 500);
                        $('#approved_by').select2('open');
                    });
                    return;
                }

                const formData = new FormData(this);
                $('#submitBtn').prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin"></i> Updating...');

                $.ajax({
                    url: '{{ route('loi.tooling-inspection.update', $toolingInspection->id) }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.href =
                                    '{{ route('loi.tooling-inspection.index') }}';
                            });
                        }
                    },
                    error: function(xhr) {
                        $('#submitBtn').prop('disabled', false).html(
                            '<i class="fas fa-save"></i> Update Tooling Inspection');

                        let errorMessage =
                            'An error occurred while updating the tooling inspection.';

                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }

                            if (xhr.responseJSON.errors) {
                                let errors = xhr.responseJSON.errors;
                                errorMessage = '<ul style="text-align: left;">';
                                $.each(errors, function(key, value) {
                                    errorMessage += '<li>' + value[0] + '</li>';
                                });
                                errorMessage += '</ul>';
                            }
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            html: errorMessage
                        });
                    }
                });
            });
        });
    </script>
@stop
