@extends('layouts.app')

@section('title', 'LOI - Create Estimate New Item')
@section('page-title', 'LOI - Create Estimate New Item')

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .form-header {
            background-color: #f8f9fa;
            padding: 15px;
            border: 1px solid #dee2e6;
            margin-bottom: 20px;
        }

        .form-section {
            border: 1px solid #dee2e6;
            padding: 15px;
            margin-bottom: 20px;
        }

        .form-section-title {
            font-weight: bold;
            background-color: #e9ecef;
            padding: 8px;
            margin: -15px -15px 15px -15px;
            border-bottom: 1px solid #dee2e6;
        }

        .checkbox-inline {
            display: inline-flex;
            align-items: center;
            margin-right: 20px;
        }



        .table-form thead th {
            text-align: center;
            vertical-align: middle;
        }

        .btn-add-row {
            margin-top: 10px;
        }

        .note-decision-section {
            border: 1px solid #dee2e6;
            padding: 20px;
            margin-top: 20px;
        }

        .decision-switch-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100%;
        }

        .decision-switch {
            display: flex;
            gap: 20px;
            margin-top: 15px;
        }

        .decision-btn {
            width: 110px;
            height: 110px;
            font-size: 2rem;
            font-weight: bold;
            border: 3px solid #dee2e6;
            background-color: #f8f9fa;
            color: #6c757d;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .decision-btn:hover {
            transform: scale(1.05);
        }

        .decision-btn.active-ok {
            background-color: #28a745;
            border-color: #28a745;
            color: white;
            box-shadow: 0 0 20px rgba(40, 167, 69, 0.6);
            animation: pulse-ok 1.5s infinite;
        }

        .decision-btn.active-no {
            background-color: #dc3545;
            border-color: #dc3545;
            color: white;
            box-shadow: 0 0 20px rgba(220, 53, 69, 0.6);
            animation: pulse-no 1.5s infinite;
        }

        @keyframes pulse-ok {

            0%,
            100% {
                box-shadow: 0 0 20px rgba(40, 167, 69, 0.6);
            }

            50% {
                box-shadow: 0 0 30px rgba(40, 167, 69, 0.9);
            }
        }

        @keyframes pulse-no {

            0%,
            100% {
                box-shadow: 0 0 20px rgba(220, 53, 69, 0.6);
            }

            50% {
                box-shadow: 0 0 30px rgba(220, 53, 69, 0.9);
            }
        }

        .approval-section {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }

        .approval-box {
            text-align: center;
            border: 1px solid #dee2e6;
            padding: 10px;
            min-height: 100px;
            flex: 1;
            margin: 0 5px;
        }

        .warning-icon {
            color: #ffc107;
            font-size: 1.2rem;
            margin-left: 5px;
        }

        input[type="checkbox"] {
            margin-right: 5px;
        }
    </style>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <img src="{{ asset('favicon.png') }}" alt="Logo" style="height: 50px;">
                                <strong style="font-size: 1.2rem;">PT. SHIMADA KARYA INDONESIA</strong><br>
                                <strong>FGA DEPARTMENT</strong><br>
                                <span>REVIEW & ESTIMATION NEW ITEM</span>
                            </div>
                            <div class="col-md-6 text-right">
                                <small>FR SAL 01 03<br>Ed/Rev. 01/00</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="estimateForm">
                            @csrf
                            <input type="hidden" id="estimate_id" name="estimate_id">

                            @include('loi.estimate-new-item.partials.sales-information-form')

                            @include('loi.estimate-new-item.partials.production-process-form')

                            @include('loi.estimate-new-item.partials.note-decision-form')

                            @include('loi.estimate-new-item.partials.approval-form')

                            <!-- Action Buttons -->
                            <div class="row mt-4">
                                <div class="col-md-12 text-center">
                                    <a href="{{ route('loi.estimate-new-item') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Back to List
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@push('js')
    <script>
        $(document).ready(function() {
            $('select.form-control').select2({
                theme: "bootstrap4",
            });

            let materialRowCount = 1;
            let importantPointRowCount = 1;

            // Set today's date
            $('#date').val(new Date().toISOString().split('T')[0]);

            // Load customers on page load
            console.log('Initializing customer load...');
            loadCustomers();

            // Function to load customers
            function loadCustomers() {
                console.log('Loading customers from API...');
                console.log('URL:', '{{ route('loi.estimate-new-item.customers') }}');

                $.ajax({
                    url: "{{ route('loi.estimate-new-item.customers') }}",
                    method: 'GET',
                    success: function(response) {
                        console.log('Customer API response:', response);
                        if (response.success) {
                            const customerSelect = $('#customer_id');
                            customerSelect.empty();
                            customerSelect.append('<option value="">Select Customer</option>');

                            response.data.forEach(function(customer) {
                                customerSelect.append(
                                    `<option value="${customer.id}">${customer.name} ${customer.kode ? '(' + customer.kode + ')' : ''}</option>`
                                );
                            });
                            console.log('Loaded ' + response.data.length + ' customers');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading customers:', error);
                        console.error('XHR:', xhr);
                        console.error('Status:', status);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load customer list. Check console for details.'
                        });
                    }
                });
            }

            // Handle customer change - load parts
            $('#customer_id').change(function() {
                const customerId = $(this).val();
                const partNoSelect = $('#part_no');

                console.log('Customer changed, ID:', customerId);

                // Reset part fields
                partNoSelect.empty().append('<option value="">Select Part No</option>');
                $('#part_name').val('');
                $('#qty_month').val('');
                $('#date_masspro').val('');
                $('#depreciation_periode').val('');
                $('#tools_depreciation').val('');
                $('input[name="similar_part"]').prop('checked', false);

                if (!customerId) {
                    return;
                }

                // Load parts for selected customer
                console.log('Loading parts for customer:', customerId);
                console.log('URL:', '{{ route('loi.estimate-new-item.parts') }}');

                $.ajax({
                    url: '{{ route('loi.estimate-new-item.parts') }}',
                    method: 'GET',
                    data: {
                        customer_id: customerId
                    },
                    success: function(response) {
                        console.log('Parts API response:', response);
                        if (response.success && response.data.length > 0) {
                            response.data.forEach(function(part) {
                                partNoSelect.append(
                                    `<option value="${part.partno}">${part.partno} | ${part.partname || ''}</option>`
                                );
                            });
                            console.log('Loaded ' + response.data.length + ' parts');
                        } else {
                            console.log('No parts found for customer');
                            Swal.fire({
                                icon: 'info',
                                title: 'No Parts Found',
                                text: 'No parts found for this customer'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading parts:', error);
                        console.error('XHR:', xhr);
                        console.error('Status:', status);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load parts for selected customer. Check console for details.'
                        });
                    }
                });
            });

            // Handle part number change - auto fill part data
            $('#part_no').change(function() {
                const partNo = $(this).val();

                console.log('Part number changed:', partNo);

                if (!partNo) {
                    // Reset fields if no part selected
                    $('#part_name').val('');
                    $('#qty_month').val('');
                    $('#date_masspro').val('');
                    $('#depreciation_periode').val('');
                    $('#tools_depreciation').val('');
                    $('input[name="similar_part"]').prop('checked', false);
                    return;
                }

                // Load part details
                console.log('Loading part details for:', partNo);
                console.log('URL:', '{{ route('loi.estimate-new-item.part-details') }}');

                $.ajax({
                    url: '{{ route('loi.estimate-new-item.part-details') }}',
                    method: 'GET',
                    data: {
                        part_no: partNo
                    },
                    success: function(response) {
                        console.log('Part details API response:', response);
                        if (response.success) {
                            const data = response.data;

                            // Fill form fields
                            $('#part_name').val(data.part_name || '');
                            $('#qty_month').val(data.qty_month || '');
                            $('#date_masspro').val(data.date_masspro || '');
                            $('#depreciation_periode').val(data.depreciation_periode || '');
                            $('#tools_depreciation').val(data.tools_depreciation || '');

                            // Set similar part radio button
                            if (data.similar_part == 1) {
                                $('#similar_part_yes').prop('checked', true);
                            } else if (data.similar_part == 0) {
                                $('#similar_part_no').prop('checked', true);
                            }

                            console.log('Part details loaded successfully');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading part details:', error);
                        console.error('XHR:', xhr);
                        console.error('Status:', status);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load part details. Check console for details.'
                        });
                    }
                });
            });

            // ============= MATERIAL SECTION =============

            // Load materials on page load and populate existing material dropdowns
            console.log('Initializing materials load...');
            loadMaterials();

            // Function to load materials
            function loadMaterials() {
                console.log('Loading materials from API...');
                console.log('URL:', '{{ route('loi.estimate-new-item.materials') }}');

                $.ajax({
                    url: '{{ route('loi.estimate-new-item.materials') }}',
                    method: 'GET',
                    success: function(response) {
                        console.log('Materials API response:', response);
                        if (response.success) {
                            // Populate all material select dropdowns
                            $('.material-select').each(function() {
                                const materialSelect = $(this);
                                materialSelect.empty();
                                materialSelect.append(
                                    '<option value="">Select Material</option>');

                                response.data.forEach(function(material) {
                                    materialSelect.append(
                                        `<option value="${material.id}">${material.name}</option>`
                                    );
                                });
                            });
                            console.log('Loaded ' + response.data.length + ' materials');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading materials:', error);
                        console.error('XHR:', xhr);
                        console.error('Status:', status);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load material list. Check console for details.'
                        });
                    }
                });
            }

            // Handle material change - auto fill code
            $(document).on('change', '.material-select', function() {
                const materialId = $(this).val();
                const row = $(this).closest('tr');
                const codeField = row.find('.material-code');

                console.log('Material changed, ID:', materialId);

                if (!materialId) {
                    codeField.val('');
                    return;
                }

                // Load material details
                console.log('Loading material details for:', materialId);
                console.log('URL:', '{{ route('loi.estimate-new-item.material-details') }}');

                $.ajax({
                    url: '{{ route('loi.estimate-new-item.material-details') }}',
                    method: 'GET',
                    data: {
                        material_id: materialId
                    },
                    success: function(response) {
                        console.log('Material details API response:', response);
                        if (response.success) {
                            codeField.val(response.data.code || '');
                            console.log('Material code loaded:', response.data.code);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading material details:', error);
                        console.error('XHR:', xhr);
                        console.error('Status:', status);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load material details. Check console for details.'
                        });
                    }
                });
            });



            // ============= END MATERIAL SECTION =============



            // Initialize process location on page load
            console.log('Initializing process location...');

            // Monitor process checkbox changes
            $(document).on('change', '.process-checkbox', function() {
                const row = $(this).closest('tr');
                const isChecked = $(this).is(':checked');

                console.log('Process checkbox changed:', $(this).closest('tr').find('input[type="hidden"]')
                    .val(), 'checked:', isChecked);

                // Enable/disable row fields
                row.find('select, input:not([type="checkbox"]):not([type="hidden"])').prop('disabled', !
                    isChecked);

                if (!isChecked) {
                    row.find('select').val('');
                    row.find('input:not([type="checkbox"]):not([type="hidden"])').val('');
                }

                // Check and update process location
                checkProcessLocation();
            });

            // Manual process location change handler
            $('input[name="process_location"]').change(function() {
                console.log('Manual process location change:', $(this).val());
                if ($(this).val() === 'out_house') {
                    $('#supplier_name').prop('disabled', false).prop('required', false);
                    // Clear model selection when Out House is selected
                    $('input[name="model"]').prop('checked', false);
                    // Disable all model value inputs
                    $('#waya_ply_value').prop('disabled', true).val('');
                    $('#wrapping_ply_value').prop('disabled', true).val('');
                    $('#model_other_value').prop('disabled', true).val('');
                } else {
                    $('#supplier_name').prop('disabled', true).prop('required', false).val('');
                }
            });

            // ===== MODEL INPUT FIELDS - ENABLE/DISABLE BASED ON RADIO SELECTION =====

            // Function to update model input fields
            function updateModelInputs() {
                const selectedModel = $('input[name="model"]:checked').val();

                // Disable all model-specific input fields first
                $('#waya_ply_value').prop('disabled', true);
                $('#wrapping_ply_value').prop('disabled', true);
                $('#model_other_value').prop('disabled', true);

                // Enable only the relevant field based on selection
                if (selectedModel === 'waya_ply') {
                    $('#waya_ply_value').prop('disabled', false);
                } else if (selectedModel === 'wrapping_ply') {
                    $('#wrapping_ply_value').prop('disabled', false);
                } else if (selectedModel === 'other') {
                    $('#model_other_value').prop('disabled', false);
                }

                // Auto-select In House when any model is selected
                if (selectedModel) {
                    console.log('Model selected, switching to In House');
                    $('#process_location_in_house').prop('checked', true);
                    $('#supplier_name').prop('disabled', true).prop('required', false).val('');
                }
            }

            // Run on page load
            updateModelInputs();

            // Monitor model radio changes
            $('input[name="model"]').change(function() {
                updateModelInputs();
            });

            // Decision Buttons
            $('#btnDecisionOK').click(function() {
                $('#decision').val('ok');
                $(this).addClass('active-ok');
                $('#btnDecisionNO').removeClass('active-no');
            });

            $('#btnDecisionNO').click(function() {
                $('#decision').val('no');
                $(this).addClass('active-no');
                $('#btnDecisionOK').removeClass('active-ok');
            });

            // Add Row Functions
            window.addMaterialRow = function() {
                materialRowCount++;
                const newRow = `
                    <tr>
                        <td>${materialRowCount}</td>
                        <td>
                            <select class="form-control material-select" name="materials[${materialRowCount-1}][material_id]">
                                <option value="">Select</option>
                                <!-- Will be populated automatically -->
                            </select>
                        </td>
                        <td><input type="text" class="form-control" name="materials[${materialRowCount-1}][specification]"></td>
                        <td>
                            <select class="form-control" name="materials[${materialRowCount-1}][new_material]">
                                <option value="no">No</option>
                                <option value="yes">Yes</option>
                            </select>
                        </td>
                        <td><input type="text" class="form-control material-code" name="materials[${materialRowCount-1}][code]" ></td>
                        <td><input type="number" step="0.01" class="form-control dim-field" name="materials[${materialRowCount-1}][thick]"></td>
                        <td><input type="number" step="0.01" class="form-control dim-field" name="materials[${materialRowCount-1}][diameter_in]"></td>
                        <td><input type="number" step="0.01" class="form-control dim-field" name="materials[${materialRowCount-1}][diameter_out]"></td>
                        <td><input type="number" step="0.01" class="form-control dim-field" name="materials[${materialRowCount-1}][length]"></td>
                        <td><input type="number" step="0.01" class="form-control volume-field" name="materials[${materialRowCount-1}][volume]"></td>
                        <td><input type="number" step="0.01" class="form-control" name="materials[${materialRowCount-1}][weight_estimate]"></td>
                        <td><input type="number" step="0.01" class="form-control" name="materials[${materialRowCount-1}][weight_actual]"></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger btn-remove-material" onclick="removeMaterialRow(this)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#materialTableBody').append(newRow);

                // Reload materials for the new row
                loadMaterials();

                updateMaterialRowNumbers();
            };

            window.removeMaterialRow = function(btn) {
                $(btn).closest('tr').remove();
                materialRowCount--;
                updateMaterialRowNumbers();
            };

            function updateMaterialRowNumbers() {
                $('#materialTableBody tr').each(function(index) {
                    $(this).find('td:first').text(index + 1);

                    // Update button state - first row cannot be deleted
                    if (index === 0) {
                        $(this).find('.btn-remove-material').prop('disabled', true);
                    } else {
                        $(this).find('.btn-remove-material').prop('disabled', false);
                    }
                });
            }

            // Add Material button click handler
            $('#btnAddMaterial').click(function() {
                addMaterialRow();
            });

            // ===========================
            // ADDITIONAL PARTS SECTION
            // ===========================
            console.log('Initializing additional parts...');

            let additionalPartRowCount = 1;

            // Function to load additional parts (optional customer filter)
            function loadAdditionalParts(customerId = null) {
                console.log('Loading additional parts, customer filter:', customerId || 'none (all parts)');
                console.log('URL:', '{{ route('loi.estimate-new-item.additional-parts') }}');



                $.ajax({
                    url: '{{ route('loi.estimate-new-item.additional-parts') }}',
                    method: 'GET',
                    success: function(response) {
                        console.log('Additional parts API response:', response);
                        if (response.success) {
                            // Populate all additional part select dropdowns
                            $('.additional-part-select').each(function() {
                                const partSelect = $(this);
                                partSelect.empty();
                                partSelect.append('<option value="">Select Part</option>');

                                response.data.forEach(function(part) {
                                    partSelect.append(
                                        `<option value="${part.id}">${part.partname}</option>`
                                    );
                                });
                            });
                            console.log('Loaded ' + response.data.length + ' additional parts');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading additional parts:', error);
                        console.error('XHR:', xhr);
                        console.error('Status:', status);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load additional parts list. Check console for details.'
                        });
                    }
                });
            }

            // Load all additional parts on page load
            console.log('Loading all additional parts on page load...');
            loadAdditionalParts();

            // Reload additional parts when customer changes (with filter)
            $('#customer_id').change(function() {
                const customerId = $(this).val();
                console.log('Customer changed, reloading additional parts with filter...');
                loadAdditionalParts(customerId);
            });

            // Handle additional part selection change - auto fill details
            $(document).on('change', '.additional-part-select', function() {
                const partId = $(this).val();
                const customerId = $('#customer_id').val();
                const row = $(this).closest('tr');
                const partNoField = row.find('.part-no-field');
                const partSpecField = row.find('.part-spec-field');
                const partSupplierField = row.find('.part-supplier-field');

                console.log('Additional part changed, ID:', partId);

                if (!partId) {
                    partNoField.val('');
                    partSpecField.val('');
                    partSupplierField.val('');
                    return;
                }

                // Load part details
                console.log('Loading additional part details for:', partId);
                console.log('URL:', '{{ route('loi.estimate-new-item.additional-part-details') }}');

                $.ajax({
                    url: '{{ route('loi.estimate-new-item.additional-part-details') }}',
                    method: 'GET',
                    data: {
                        material_id: partId,
                        customer_id: customerId
                    },
                    success: function(response) {
                        console.log('Additional part details response:', response);
                        if (response.success) {
                            // Auto-fill readonly fields
                            partNoField.val(response.data.part_no);
                            partSpecField.val(response.data.specification);
                            partSupplierField.val(response.data.supplier);

                            console.log('Auto-filled additional part details');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading additional part details:', error);
                        console.error('XHR:', xhr);
                        console.error('Status:', status);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load part details. Check console for details.'
                        });
                    }
                });
            });

            // Add Additional Part Row function
            window.addAdditionalPartRow = function() {
                additionalPartRowCount++;
                const customerId = $('#customer_id').val();

                const newRow = `
                    <tr>
                        <td>${additionalPartRowCount}</td>
                        <td>
                            <select class="form-control additional-part-select" name="additional_parts[${additionalPartRowCount - 1}][material_id]">
                                <option value="">Select</option>
                                <!-- Will be populated via AJAX -->
                            </select>
                        </td>
                        <td><input type="text" class="form-control part-no-field" name="additional_parts[${additionalPartRowCount - 1}][part_no]" readonly></td>
                        <td><input type="text" class="form-control part-spec-field" name="additional_parts[${additionalPartRowCount - 1}][specification]"></td>
                        <td><input type="number" class="form-control" name="additional_parts[${additionalPartRowCount - 1}][qty_unit]"></td>
                        <td><input type="text" class="form-control part-supplier-field" name="additional_parts[${additionalPartRowCount - 1}][supplier]" readonly></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger btn-remove-additional" onclick="removeAdditionalPartRow(this)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#additionalPartTableBody').append(newRow);

                // Reload additional parts for the new row (with customer filter if available)
                loadAdditionalParts(customerId);

                updateAdditionalPartRowNumbers();
            };

            // Remove Additional Part Row function
            window.removeAdditionalPartRow = function(btn) {
                $(btn).closest('tr').remove();
                additionalPartRowCount--;
                updateAdditionalPartRowNumbers();
            };

            // Update row numbers and button states
            function updateAdditionalPartRowNumbers() {
                $('#additionalPartTableBody tr').each(function(index) {
                    $(this).find('td:first').text(index + 1);

                    // Update button state - first row cannot be deleted
                    if (index === 0) {
                        $(this).find('.btn-remove-additional').prop('disabled', true);
                    } else {
                        $(this).find('.btn-remove-additional').prop('disabled', false);
                    }
                });
            }

            // Add button click handler
            $('#btnAddAdditionalPart').click(function() {
                addAdditionalPartRow();
            });

            window.addImportantPointRow = function() {
                importantPointRowCount++;
                const newRow = `
                    <tr>
                        <td>${importantPointRowCount}</td>
                        <td><input type="text" class="form-control" name="important_points[${importantPointRowCount-1}][item]"></td>
                        <td><input type="text" class="form-control" name="important_points[${importantPointRowCount-1}][note]"></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger" onclick="removeImportantPointRow(this)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#importantPointTableBody').append(newRow);
                updateImportantPointNumbers();
            };

            window.removeImportantPointRow = function(btn) {
                $(btn).closest('tr').remove();
                updateImportantPointNumbers();
            };

            function updateImportantPointNumbers() {
                $('#importantPointTableBody tr').each(function(index) {
                    $(this).find('td:first').text(index + 1);
                });
                importantPointRowCount = $('#importantPointTableBody tr').length;
            }

            // Add Important Point button click handler
            $('#btnAddImportantPoint').click(function() {
                addImportantPointRow();
            });

            // Auto-calculate totals
            $(document).on('input', 'input[name="material_qty[]"], input[name="material_price[]"]', function() {
                const row = $(this).closest('tr');
                const qty = parseFloat(row.find('input[name="material_qty[]"]').val()) || 0;
                const price = parseFloat(row.find('input[name="material_price[]"]').val()) || 0;
                row.find('input[name="material_total[]"]').val((qty * price).toFixed(2));
            });

            $(document).on('input', 'input[name="additional_part_qty[]"], input[name="additional_part_price[]"]',
                function() {
                    const row = $(this).closest('tr');
                    const qty = parseFloat(row.find('input[name="additional_part_qty[]"]').val()) || 0;
                    const price = parseFloat(row.find('input[name="additional_part_price[]"]').val()) || 0;
                    row.find('input[name="additional_part_total[]"]').val((qty * price).toFixed(2));
                });

            // ===========================
            // MANUFACTURING PROCESS SECTION
            // ===========================
            console.log('Initializing manufacturing process...');

            // Function to load machines
            function loadMachines() {
                console.log('Loading machines from API...');
                console.log('URL:', '{{ route('loi.estimate-new-item.machines') }}');

                $.ajax({
                    url: '{{ route('loi.estimate-new-item.machines') }}',
                    method: 'GET',
                    success: function(response) {
                        console.log('Machines API response:', response);
                        if (response.success) {
                            // Populate all machine select dropdowns
                            $('.machine-select').each(function() {
                                const machineSelect = $(this);
                                const currentValue = machineSelect.val();

                                machineSelect.empty();
                                machineSelect.append(
                                    '<option value="">Select Machine</option>');

                                response.data.forEach(function(machine) {
                                    machineSelect.append(
                                        `<option value="${machine.id}">${machine.name}</option>`
                                    );
                                });

                                // Restore selected value if exists
                                if (currentValue) {
                                    machineSelect.val(currentValue);
                                }
                            });
                            console.log('Loaded ' + response.data.length + ' machines');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading machines:', error);
                        console.error('XHR:', xhr);
                        console.error('Status:', status);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load machines list. Check console for details.'
                        });
                    }
                });
            }

            // Load Users for Approval Form
            function loadUsers() {
                console.log('Loading users from API...');
                console.log('URL:', '{{ route('loi.estimate-new-item.users') }}');

                $.ajax({
                    url: '{{ route('loi.estimate-new-item.users') }}',
                    method: 'GET',
                    success: function(response) {
                        console.log('Users API response:', response);
                        if (response.success) {
                            // Populate all user select dropdowns
                            $('.user-select').each(function() {
                                const userSelect = $(this);
                                const currentValue = userSelect.val();

                                userSelect.empty();
                                userSelect.append(
                                    '<option value="">Select User...</option>');

                                response.data.forEach(function(user) {
                                    userSelect.append(
                                        `<option value="${user.id}">${user.name}</option>`
                                    );
                                });

                                // Restore selected value if exists
                                if (currentValue) {
                                    userSelect.val(currentValue);
                                }
                            });
                            console.log('Loaded ' + response.data.length + ' users');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading users:', error);
                        console.error('XHR:', xhr);
                        console.error('Status:', status);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load users list. Check console for details.'
                        });
                    }
                });
            }

            // Load machines on page load
            console.log('Loading machines on page load...');
            loadMachines();

            // Load users on page load
            console.log('Loading users on page load...');
            loadUsers();

            // Handle process checkbox to enable/disable fields
            $(document).on('change', '.process-checkbox', function() {
                const row = $(this).closest('tr');
                const isChecked = $(this).is(':checked');

                console.log('Process checkbox changed:', isChecked);

                // Enable/disable all inputs and selects in the row except checkbox
                row.find('select, input:not([type="checkbox"]):not([type="hidden"])').prop('disabled', !
                    isChecked);

                if (!isChecked) {
                    // Clear values when unchecking
                    row.find('select').val('');
                    row.find('input:not([type="checkbox"]):not([type="hidden"])').val('');
                }
            });

            // Form Submit
            $('#estimateForm').submit(function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const estimateId = $('#estimate_id').val();

                const url = estimateId ?
                    '/api/loi/estimate-new-item/' + estimateId :
                    "{{ route('loi.estimate-new-item.store') }}";
                const method = estimateId ? 'PUT' : 'POST';

                if (estimateId) {
                    formData.append('_method', 'PUT');
                }

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Data saved successfully'
                        }).then(() => {
                            window.location.href =
                                "{{ route('loi.estimate-new-item') }}";
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'An error occurred'
                        });
                    }
                });
            });
        });
    </script>
@endpush
