@extends('layouts.app')

@section('title', 'Edit Estimate New Item')
@section('page-title', 'Edit Estimate New Item')

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .form-section {
            margin-bottom: 30px;
            border: 1px solid #dee2e6;
            padding: 20px;
            border-radius: 5px;
        }

        .form-section h4 {
            margin-top: 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #007bff;
            margin-bottom: 20px;
        }

        .approval-section {
            display: flex;
            justify-content: space-around;
            gap: 15px;
            margin-top: 20px;
        }

        .approval-box {
            flex: 1;
            text-align: center;
            border: 1px solid #dee2e6;
            padding: 15px;
            border-radius: 5px;
        }

        .approval-box strong {
            display: block;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .approval-box input,
        .approval-box select {
            margin-bottom: 5px;
        }

        .approval-box small {
            display: block;
            color: #6c757d;
            font-size: 12px;
        }

        .process-row {
            background-color: #f8f9fa;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }

        .material-row,
        .additional-part-row {
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            border-left: 3px solid #007bff;
        }

        .btn-remove-row {
            margin-top: 28px;
        }
    </style>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <form id="estimateForm">
                    @csrf
                    <input type="hidden" name="estimate_id" id="estimate_id" value="{{ $salesInfo->id }}">

                    <!-- Sales Information Section -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Sales Information</h3>
                        </div>
                        <div class="card-body">
                            @include('loi.estimate-new-item.partials.sales-information-form', [
                                'salesInfo' => $salesInfo,
                            ])
                        </div>
                    </div>

                    <!-- Production Process Information Section -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Production Process Information (Diisi oleh bagian Engineering)</h3>
                        </div>
                        <div class="card-body">
                            @include('loi.estimate-new-item.partials.production-process-form', [
                                'salesInfo' => $salesInfo,
                            ])
                        </div>
                    </div>



                    <!-- Approval Section -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Approval</h3>
                        </div>
                        <div class="card-body">
                            @include('loi.estimate-new-item.partials.approval-form', [
                                'salesInfo' => $salesInfo,
                            ])
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 text-right">
                                    <a href="{{ route('loi.estimate-new-item') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@push('js')
    <script>
        $(document).ready(function() {
            console.log('Edit Form Initialized');
            console.log('Estimate ID:', $('#estimate_id').val());
            $('select.form-control').select2({
                theme: "bootstrap4",
            });

            // Load existing data
            const salesInfo = @json($salesInfo);
            console.log('Sales Info:', salesInfo);
            console.log('Approved By ID:', salesInfo.approved_by);
            console.log('Checked By 1 ID:', salesInfo.checked_by_1);
            console.log('Checked By 2 ID:', salesInfo.checked_by_2);
            console.log('Prepared By ID:', salesInfo.prepared_by);

            // ===== AJAX Functions =====

            // Load Customers
            function loadCustomers() {
                console.log('Loading customers from API...');
                $.ajax({
                    url: '{{ route('loi.estimate-new-item.customers') }}',
                    method: 'GET',
                    success: function(response) {
                        console.log('Customers API response:', response);
                        if (response.success) {
                            $('#customer_id').empty();
                            $('#customer_id').append('<option value="">Select Customer</option>');
                            response.data.forEach(function(customer) {
                                const selected = customer.id == salesInfo.customer_id ?
                                    'selected' : '';
                                $('#customer_id').append(
                                    `<option value="${customer.id}" ${selected}>${customer.name} (${customer.kode})</option>`
                                );
                            });
                            console.log('Loaded ' + response.data.length + ' customers');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading customers:', error);
                    }
                });
            }

            // Load Parts
            function loadParts() {
                console.log('Loading parts from API...');
                $.ajax({
                    url: '{{ route('loi.estimate-new-item.parts') }}',
                    method: 'GET',
                    success: function(response) {
                        console.log('Parts API response:', response);
                        if (response.success) {
                            $('#part_no').empty();
                            $('#part_no').append('<option value="">Select Part No</option>');
                            response.data.forEach(function(part) {
                                const selected = part.partno == salesInfo.part_no ? 'selected' :
                                    '';
                                $('#part_no').append(
                                    `<option value="${part.partno}" data-partname="${part.partname}" ${selected}>${part.partno} | ${part.partname}</option>`
                                );
                            });
                            console.log('Loaded ' + response.data.length + ' parts');

                            // Trigger change to populate part_name if there's a selected value
                            if (salesInfo.part_no) {
                                $('#part_no').trigger('change');
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading parts:', error);
                    }
                });
            }

            // Load Materials
            function loadMaterials(callback) {
                console.log('Loading materials from API...');
                $.ajax({
                    url: '{{ route('loi.estimate-new-item.materials') }}',
                    method: 'GET',
                    success: function(response) {
                        console.log('Materials API response:', response);
                        if (response.success) {
                            $('.material-select').each(function() {
                                const materialSelect = $(this);
                                const currentValue = materialSelect.val();

                                materialSelect.empty();
                                materialSelect.append(
                                    '<option value="">Select Material</option>');

                                response.data.forEach(function(material) {
                                    materialSelect.append(
                                        `<option value="${material.id}">${material.name}</option>`
                                    );
                                });

                                if (currentValue) {
                                    materialSelect.val(currentValue);
                                }
                            });
                            console.log('Loaded ' + response.data.length + ' materials');

                            // Execute callback after materials are loaded
                            if (callback && typeof callback === 'function') {
                                callback();
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading materials:', error);
                    }
                });
            }

            // Load Additional Parts
            function loadAdditionalParts(callback) {
                console.log('Loading additional parts from API...');
                $.ajax({
                    url: '{{ route('loi.estimate-new-item.additional-parts') }}',
                    method: 'GET',
                    success: function(response) {
                        console.log('Additional Parts API response:', response);
                        if (response.success) {
                            $('.additional-part-select').each(function() {
                                const partSelect = $(this);
                                const currentValue = partSelect.val();

                                partSelect.empty();
                                partSelect.append('<option value="">Select Part</option>');

                                response.data.forEach(function(part) {
                                    partSelect.append(
                                        `<option value="${part.id}">${part.partname}</option>`
                                    );
                                });

                                if (currentValue) {
                                    partSelect.val(currentValue);
                                }
                            });
                            console.log('Loaded ' + response.data.length + ' additional parts');

                            // Execute callback after additional parts are loaded
                            if (callback && typeof callback === 'function') {
                                callback();
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading additional parts:', error);
                    }
                });
            }

            // Load Machines
            function loadMachines(callback) {
                console.log('Loading machines from API...');
                $.ajax({
                    url: '{{ route('loi.estimate-new-item.machines') }}',
                    method: 'GET',
                    success: function(response) {
                        console.log('Machines API response:', response);
                        if (response.success) {
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

                                if (currentValue) {
                                    machineSelect.val(currentValue);
                                }
                            });
                            console.log('Loaded ' + response.data.length + ' machines');

                            // Execute callback after machines are loaded
                            if (callback && typeof callback === 'function') {
                                callback();
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading machines:', error);
                    }
                });
            }

            // Load Users
            function loadUsers() {
                console.log('Loading users from API...');
                $.ajax({
                    url: '{{ route('loi.estimate-new-item.users') }}',
                    method: 'GET',
                    success: function(response) {
                        console.log('Users API response:', response);
                        if (response.success) {
                            $('.user-select').each(function() {
                                const userSelect = $(this);
                                const fieldName = userSelect.attr('name');
                                let currentValue = null;

                                console.log('Processing user select field:', fieldName);

                                // Get the current value from salesInfo based on field name
                                if (fieldName === 'approved_by') {
                                    // Check if it's an object (eager loaded) or just an ID
                                    currentValue = typeof salesInfo.approved_by === 'object' &&
                                        salesInfo.approved_by !== null ?
                                        salesInfo.approved_by.id :
                                        salesInfo.approved_by;
                                } else if (fieldName === 'checked_by_1') {
                                    currentValue = typeof salesInfo.checked_by_1 === 'object' &&
                                        salesInfo.checked_by_1 !== null ?
                                        salesInfo.checked_by_1.id :
                                        salesInfo.checked_by_1;
                                } else if (fieldName === 'checked_by_2') {
                                    currentValue = typeof salesInfo.checked_by_2 === 'object' &&
                                        salesInfo.checked_by_2 !== null ?
                                        salesInfo.checked_by_2.id :
                                        salesInfo.checked_by_2;
                                } else if (fieldName === 'prepared_by') {
                                    currentValue = typeof salesInfo.prepared_by === 'object' &&
                                        salesInfo.prepared_by !== null ?
                                        salesInfo.prepared_by.id :
                                        salesInfo.prepared_by;
                                }

                                console.log('Current value for ' + fieldName + ':',
                                    currentValue, 'Type:', typeof currentValue);

                                userSelect.empty();
                                userSelect.append('<option value="">Select User...</option>');

                                response.data.forEach(function(user) {
                                    const selected = (currentValue && user.id ==
                                        currentValue) ? 'selected' : '';
                                    userSelect.append(
                                        `<option value="${user.id}" ${selected}>${user.name}</option>`
                                    );
                                });

                                console.log('Field ' + fieldName + ' set to user ID:',
                                    currentValue);
                            });
                            console.log('Loaded ' + response.data.length + ' users');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading users:', error);
                    }
                });
            }

            // ===== Material Calculation Helper Functions =====

            let materialRowCount = 1; // Start with 1 because we have the first row by default

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
                });
            }

            // ===== Additional Part Helper Functions =====

            let additionalPartRowCount = 1; // Start with 1 because we have the first row by default

            window.addAdditionalPartRow = function() {
                additionalPartRowCount++;
                const newRow = `
                    <tr>
                        <td>${additionalPartRowCount}</td>
                        <td>
                            <select class="form-control additional-part-select" name="additional_parts[${additionalPartRowCount-1}][material_id]">
                                <option value="">Select</option>
                                <!-- Will be populated automatically -->
                            </select>
                        </td>
                        <td><input type="text" class="form-control part-no-field" name="additional_parts[${additionalPartRowCount-1}][part_no]" readonly></td>
                        <td><input type="text" class="form-control part-spec-field" name="additional_parts[${additionalPartRowCount-1}][specification]"></td>
                        <td><input type="number" class="form-control" name="additional_parts[${additionalPartRowCount-1}][qty_unit]"></td>
                        <td><input type="text" class="form-control part-supplier-field" name="additional_parts[${additionalPartRowCount-1}][supplier]" readonly></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger btn-remove-additional" onclick="removeAdditionalPartRow(this)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#additionalPartTableBody').append(newRow);

                // Reload additional parts for the new row
                loadAdditionalParts();

                updateAdditionalPartRowNumbers();
            };

            window.removeAdditionalPartRow = function(btn) {
                $(btn).closest('tr').remove();
                additionalPartRowCount--;
                updateAdditionalPartRowNumbers();
            };

            function updateAdditionalPartRowNumbers() {
                $('#additionalPartTableBody tr').each(function(index) {
                    $(this).find('td:first').text(index + 1);
                });
            }

            // ===== Event Handlers =====

            // Add Material button click handler
            $('#btnAddMaterial').click(function() {
                addMaterialRow();
            });

            // Add Additional Part button click handler
            $('#btnAddAdditionalPart').click(function() {
                addAdditionalPartRow();
            });

            // ===== Important Point Helper Functions =====

            let importantPointRowCount = 1;

            window.addImportantPointRow = function() {
                importantPointRowCount++;
                const newRow = `
                    <tr>
                        <td>${importantPointRowCount}</td>
                        <td><input type="text" class="form-control" name="important_points[${importantPointRowCount-1}][item]"></td>
                        <td><input type="text" class="form-control" name="important_points[${importantPointRowCount-1}][note]"></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger btn-remove-point" onclick="removeImportantPointRow(this)">
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
                importantPointRowCount--;
                updateImportantPointNumbers();
            };

            function updateImportantPointNumbers() {
                $('#importantPointTableBody tr').each(function(index) {
                    $(this).find('td:first').text(index + 1);
                });
            }

            // Add Important Point button click handler
            $('#btnAddImportantPoint').click(function() {
                addImportantPointRow();
            });

            // Handle part number change - auto fill part data
            $('#part_no').change(function() {
                const partNo = $(this).val();
                console.log('Part number changed:', partNo);

                if (!partNo) {
                    // Reset part_name field if no part selected
                    $('#part_name').val('');
                    return;
                }

                // Get the selected option's part name
                const selectedOption = $(this).find('option:selected');
                const partName = selectedOption.data('partname');

                if (partName) {
                    $('#part_name').val(partName);
                    console.log('Part name set to:', partName);
                }
            });

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
                    }
                });
            });

            // Handle additional part change - auto fill part details
            $(document).on('change', '.additional-part-select', function() {
                const partId = $(this).val();
                const row = $(this).closest('tr');

                console.log('Additional part changed, ID:', partId);

                if (!partId) {
                    row.find('.part-no-field').val('');
                    row.find('.part-spec-field').val('');
                    row.find('.part-supplier-field').val('');
                    return;
                }

                // Load additional part details
                console.log('Loading additional part details for:', partId);

                $.ajax({
                    url: '{{ route('loi.estimate-new-item.additional-part-details') }}',
                    method: 'GET',
                    data: {
                        material_id: partId
                    },
                    success: function(response) {
                        console.log('Additional part details API response:', response);
                        if (response.success) {
                            row.find('.part-no-field').val(response.data.part_no || '');
                            row.find('.part-spec-field').val(response.data.specification || '');
                            row.find('.part-supplier-field').val(response.data.supplier || '');
                            console.log('Additional part details loaded');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading additional part details:', error);
                    }
                });
            });


            // ===== Load All Dropdowns =====

            // Load all dropdowns
            console.log('Loading all dropdowns on page load...');
            loadCustomers();
            loadParts();
            loadUsers();

            // Load materials with callback to populate material data after loading
            loadMaterials(function() {
                console.log('Materials loaded, now populating material data...');
                populateMaterialData();
            });

            // Load additional parts with callback to populate additional parts data after loading
            loadAdditionalParts(function() {
                console.log('Additional parts loaded, now populating additional parts data...');
                populateAdditionalPartsData();
            });

            // Load machines with callback to populate manufacturing processes after loading
            loadMachines(function() {
                console.log('Machines loaded, now populating manufacturing processes...');
                populateManufacturingProcesses();
            });

            // Important points and tooling are now populated directly in the Blade template
            // Set the importantPointRowCount based on existing data
            if (salesInfo.important_points && salesInfo.important_points.length > 0) {
                importantPointRowCount = salesInfo.important_points.length;
            }

            // Populate existing data after a short delay to ensure dropdowns are loaded
            setTimeout(function() {
                populateExistingData();
            }, 1000);

            // ===== Populate Material Data =====
            function populateMaterialData() {
                console.log('Populating material calculations...');

                if (salesInfo.material_calculations && salesInfo.material_calculations.length > 0) {
                    // First, add all needed rows
                    for (let i = 1; i < salesInfo.material_calculations.length; i++) {
                        $('#btnAddMaterial').click();
                    }

                    // Wait a bit for rows to be added and materials to be loaded
                    setTimeout(function() {
                        // Reload materials one more time to ensure all rows have options
                        loadMaterials(function() {
                            console.log('Materials reloaded for all rows, now setting values...');

                            // Now populate each row
                            salesInfo.material_calculations.forEach(function(material, index) {
                                const row = $('#materialTableBody tr').eq(index);

                                // Set material dropdown value
                                row.find('select[name="materials[' + index +
                                    '][material_id]"]').val(
                                    material.material_id);

                                // Set other fields
                                row.find('input[name="materials[' + index +
                                    '][specification]"]').val(
                                    material.specification || '');
                                row.find('select[name="materials[' + index +
                                    '][new_material]"]').val(
                                    material.new_material || 'no');
                                row.find('input[name="materials[' + index + '][code]"]')
                                    .val(
                                        material.code || '');
                                row.find('input[name="materials[' + index + '][thick]"]')
                                    .val(
                                        material.thick || '');
                                row.find('input[name="materials[' + index +
                                    '][diameter_in]"]').val(
                                    material.diameter_in || '');
                                row.find('input[name="materials[' + index +
                                    '][diameter_out]"]').val(
                                    material.diameter_out || '');
                                row.find('input[name="materials[' + index + '][length]"]')
                                    .val(
                                        material.length || '');
                                row.find('input[name="materials[' + index + '][volume]"]')
                                    .val(
                                        material.volume || '');
                                row.find('input[name="materials[' + index +
                                    '][weight_estimate]"]').val(
                                    material.weight_estimate || '');
                                row.find('input[name="materials[' + index +
                                    '][weight_actual]"]').val(
                                    material.weight_actual || '');

                                console.log('Material row ' + index + ' populated:',
                                    material);
                            });
                        });
                    }, 500); // Wait 500ms for all rows to be added
                }
            }

            // ===== Populate Additional Parts Data =====
            function populateAdditionalPartsData() {
                console.log('Populating additional parts...');

                if (salesInfo.additional_parts && salesInfo.additional_parts.length > 0) {
                    // First, add all needed rows
                    for (let i = 1; i < salesInfo.additional_parts.length; i++) {
                        $('#btnAddAdditionalPart').click();
                    }

                    // Wait a bit for rows to be added and additional parts to be loaded
                    setTimeout(function() {
                        // Reload additional parts one more time to ensure all rows have options
                        loadAdditionalParts(function() {
                            console.log(
                                'Additional parts reloaded for all rows, now setting values...');

                            // Now populate each row
                            salesInfo.additional_parts.forEach(function(part, index) {
                                const row = $('#additionalPartTableBody tr').eq(index);

                                // Set additional part dropdown value
                                row.find('select[name="additional_parts[' + index +
                                    '][material_id]"]').val(
                                    part.material_id);

                                // Set other fields
                                row.find('input[name="additional_parts[' + index +
                                    '][part_no]"]').val(
                                    part.part_no || '');
                                row.find('input[name="additional_parts[' + index +
                                    '][specification]"]').val(
                                    part.specification || '');
                                row.find('input[name="additional_parts[' + index +
                                    '][qty_unit]"]').val(
                                    part.qty_unit || '');
                                row.find('input[name="additional_parts[' + index +
                                    '][supplier]"]').val(
                                    part.supplier || '');

                                console.log('Additional part row ' + index + ' populated:',
                                    part);
                            });
                        });
                    }, 500); // Wait 500ms for all rows to be added
                }
            }

            // ===== Populate Manufacturing Processes =====
            function populateManufacturingProcesses() {
                console.log('Populating manufacturing processes...');

                if (salesInfo.manufacturing_processes && salesInfo.manufacturing_processes.length > 0) {
                    salesInfo.manufacturing_processes.forEach(function(process) {
                        const processName = process.process_name;
                        const row = $('input[name*="[process_name]"][value="' + processName + '"]').closest(
                            'tr');

                        if (row.length > 0) {
                            // Check the checkbox to enable the row
                            const checkbox = row.find('.process-checkbox');
                            checkbox.prop('checked', true);
                            checkbox.trigger('change');

                            // Set the values
                            setTimeout(function() {
                                // Set machine dropdown
                                row.find('.machine-select').val(process.machine_id);

                                // Set cycle time fields
                                row.find('input[name*="[cycle_time_estimate]"]').val(
                                    process.cycle_time_estimate || '');
                                row.find('input[name*="[cycle_time_actual]"]').val(
                                    process.cycle_time_actual || '');

                                // Set capacity fields
                                row.find('input[name*="[capacity_estimate]"]').val(
                                    process.capacity_estimate || '');
                                row.find('input[name*="[capacity_actual]"]').val(
                                    process.capacity_actual || '');

                                // Set remarks field (could be input or textarea)
                                const remarksField = row.find('[name*="[remarks]"]');
                                remarksField.val(process.remarks || '');

                                console.log('Process "' + processName + '" populated:', process);
                            }, 100);
                        }
                    });
                }
            }

            // ===== Populate Existing Data =====
            function populateExistingData() {
                console.log('Populating existing data...');

                // Approval users are now populated directly in loadUsers() function
                // Material calculations are populated separately after materials are loaded
                // Additional parts are populated separately after additional parts are loaded
                // Manufacturing processes are populated separately after machines are loaded

                console.log('Existing data populated');
            }

            // ===== PROCESS LOCATION - OUT HOUSE TO IN HOUSE TOGGLE =====
            // Check if any process is enabled
            function checkProcessLocation() {
                console.log('Checking process location...');
                const anyProcessChecked = $('.process-checkbox:checked').length > 0;
                console.log('Any process checked:', anyProcessChecked);

                if (anyProcessChecked) {
                    // Switch to In House if any process is checked
                    console.log('Switching to In House');
                    $('#process_location_in_house').prop('checked', true);
                    $('#supplier_name').prop('disabled', true).prop('required', false).val('');
                } else {
                    // Default to Out House if no process is checked
                    console.log('Switching to Out House');
                    $('#process_location_out_house').prop('checked', true);
                    $('#supplier_name').prop('disabled', false).prop('required', false);
                }
            }

            // Handle process checkbox to enable/disable fields
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

            // Run checkProcessLocation on page load after a delay to ensure processes are populated
            setTimeout(function() {
                console.log('Running initial process location check...');
                checkProcessLocation();
            }, 1000);

            // Manual process location change handler
            $('input[name="process_location"]').change(function() {
                console.log('Manual process location change:', $(this).val());
                if ($(this).val() === 'out_house') {
                    $('#supplier_name').prop('disabled', false).prop('required', false);
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
            }

            // Run on page load
            updateModelInputs();

            // Monitor model radio changes
            $('input[name="model"]').change(function() {
                updateModelInputs();
            });

            // Form Submit
            $('#estimateForm').submit(function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const estimateId = $('#estimate_id').val();

                // Add PUT method for update
                formData.append('_method', 'PUT');

                $.ajax({
                    url: '{{ route('loi.estimate-new-item.update', ':id') }}'.replace(':id',
                        estimateId),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Data updated successfully'
                        }).then(() => {
                            window.location.href =
                                "{{ route('loi.estimate-new-item') }}";
                        });
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message ||
                                'An error occurred while updating data'
                        });
                    }
                });
            });
        });
    </script>
@endpush
