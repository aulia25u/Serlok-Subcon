<!-- Production Process Information Section -->
<div class="form-section">
    <div class="form-section-title">
        Production Process Information <small>(diisi oleh bagian Engineering)</small>
    </div>

    <!-- (1). Process Location -->
    <div class="row">
        <div class="col-md-12">
            <label><strong>( 1 ). Process Location</strong></label>
            <div class="ml-3">
                <label class="checkbox-inline">
                    <input type="radio" id="process_location_in_house" name="process_location" value="in_house"> In House
                </label>
                <label class="checkbox-inline">
                    <input type="radio" id="process_location_out_house" name="process_location" value="out_house"
                        checked> Out House
                </label>
                <span class="ml-3">Nama Supplier:</span>
                <input type="text" class="form-control d-inline-block" id="supplier_name" name="supplier_name"
                    style="width: 300px;">
            </div>
        </div>
    </div>

    <!-- (2). Material Calculation -->
    <div class="row mt-4">
        <div class="col-md-12">
            <label><strong>( 2 ). Material Calculation</strong></label>
            <div class="table-responsive">
                <table class="table table-bordered table-form" id="materialTable">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 40px;" rowspan="2">No</th>
                            <th rowspan="2">Material</th>
                            <th rowspan="2">Specification Material</th>
                            <th rowspan="2">New Material<br>(Yes/No)</th>
                            <th rowspan="2">Code Material</th>
                            <th colspan="4">Dimension (mm)</th>
                            <th rowspan="2">Volume<br>(mm³)</th>
                            <th colspan="2">Weight/Pcs (gr)</th>
                            <th style="width: 60px;" rowspan="2">Action</th>
                        </tr>
                        <tr>
                            <th>Thick</th>
                            <th>Ø in</th>
                            <th>Ø out</th>
                            <th>L</th>
                            <th>Estimate</th>
                            <th>Actual (Trial)</th>
                        </tr>
                    </thead>
                    <tbody id="materialTableBody">
                        <tr>
                            <td>1</td>
                            <td>
                                <select class="form-control material-select" name="materials[0][material_id]">
                                    <option value="">Select</option>
                                    <!-- Will be populated via AJAX -->
                                </select>
                            </td>
                            <td><input type="text" class="form-control" name="materials[0][specification]"></td>
                            <td>
                                <select class="form-control" name="materials[0][new_material]">
                                    <option value="no">No</option>
                                    <option value="yes">Yes</option>
                                </select>
                            </td>
                            <td><input type="text" class="form-control material-code" name="materials[0][code]"></td>
                            <td><input type="number" step="0.01" class="form-control dim-field"
                                    name="materials[0][thick]"></td>
                            <td><input type="number" step="0.01" class="form-control dim-field"
                                    name="materials[0][diameter_in]"></td>
                            <td><input type="number" step="0.01" class="form-control dim-field"
                                    name="materials[0][diameter_out]"></td>
                            <td><input type="number" step="0.01" class="form-control dim-field"
                                    name="materials[0][length]"></td>
                            <td><input type="number" step="0.01" class="form-control volume-field"
                                    name="materials[0][volume]"></td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="materials[0][weight_estimate]"></td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="materials[0][weight_actual]"></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-danger btn-remove-material">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn btn-sm btn-success btn-add-row" id="btnAddMaterial">
                    <i class="fas fa-plus"></i> Add Material
                </button>
            </div>
        </div>
    </div>

    <!-- (3). Additional Part -->
    <div class="row mt-4">
        <div class="col-md-12">
            <label><strong>( 3 ). Additional Part</strong></label>
            <div class="table-responsive">
                <table class="table table-bordered table-form" id="additionalPartTable">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 40px;">No</th>
                            <th>Part Name</th>
                            <th>Part No</th>
                            <th>Specification</th>
                            <th>Qty / Unit (Pcs)</th>
                            <th>Supplier</th>
                            <th style="width: 60px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="additionalPartTableBody">
                        <tr>
                            <td>1</td>
                            <td>
                                <select class="form-control additional-part-select"
                                    name="additional_parts[0][material_id]">
                                    <option value="">Select</option>
                                    <!-- Will be populated via AJAX -->
                                </select>
                            </td>
                            <td><input type="text" class="form-control part-no-field"
                                    name="additional_parts[0][part_no]" readonly></td>
                            <td><input type="text" class="form-control part-spec-field"
                                    name="additional_parts[0][specification]"></td>
                            <td><input type="number" class="form-control" name="additional_parts[0][qty_unit]"></td>
                            <td><input type="text" class="form-control part-supplier-field"
                                    name="additional_parts[0][supplier]" readonly></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-danger btn-remove-additional">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn btn-sm btn-success btn-add-row" id="btnAddAdditionalPart">
                    <i class="fas fa-plus"></i> Add Additional Part
                </button>
            </div>
        </div>
    </div>

    <!-- (4). Manufacturing Process / Flow Process -->
    <div class="row mt-4">
        <div class="col-md-12">
            <label><strong>( 4 ). Manufacturing Process / Flow Process</strong> <small>(beri tanda √ pada kotak setiap
                    proses yang dilakukan)</small></label>
            <div class="table-responsive">
                <table class="table table-bordered table-form" id="processTable">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 40px;" rowspan="2">No</th>
                            <th style="width: 200px;" rowspan="2">Process Name</th>
                            <th rowspan="2"></th>
                            <th rowspan="2">Machine Name</th>
                            <th colspan="2">Cycle Time (sec)</th>
                            <th colspan="2">Capacity / hr (pcs)</th>
                            <th rowspan="2">Remarks</th>
                        </tr>
                        <tr>
                            <th>Estimate</th>
                            <th>Actual (Trial)</th>
                            <th>Estimate</th>
                            <th>Actual (Trial)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Extrusion 1</td>
                            <td class="text-center">
                                <input type="checkbox" name="processes[0][enabled]" value="1"
                                    class="process-checkbox">
                                <input type="hidden" name="processes[0][process_name]" value="Extrusion 1">
                            </td>
                            <td>
                                <select class="form-control machine-select" name="processes[0][machine_id]" disabled>
                                    <option value="">Select Machine</option>
                                    <!-- Will be populated via AJAX -->
                                </select>
                            </td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[0][cycle_time_estimate]" disabled></td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[0][cycle_time_actual]" disabled></td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[0][capacity_estimate]" disabled></td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[0][capacity_actual]" disabled></td>
                            <td><input type="text" class="form-control" name="processes[0][remarks]" disabled>
                            </td>
                        </tr>

                        <tr>
                            <td>2</td>
                            <td>Extrusion 2</td>
                            <td class="text-center">
                                <input type="checkbox" name="processes[2][enabled]" value="1"
                                    class="process-checkbox">
                                <input type="hidden" name="processes[2][process_name]" value="Extrusion 2">
                            </td>
                            <td>
                                <select class="form-control machine-select" name="processes[2][machine_id]" disabled>
                                    <option value="">Select Machine</option>
                                </select>
                            </td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[2][cycle_time_estimate]" disabled></td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[2][cycle_time_actual]" disabled></td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[2][capacity_estimate]" disabled></td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[2][capacity_actual]" disabled></td>
                            <td><input type="text" class="form-control" name="processes[2][remarks]" disabled>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Braiding</td>
                            <td class="text-center">
                                <input type="checkbox" name="processes[1][enabled]" value="1"
                                    class="process-checkbox">
                                <input type="hidden" name="processes[1][process_name]" value="Braiding">
                            </td>
                            <td>
                                <select class="form-control machine-select" name="processes[1][machine_id]" disabled>
                                    <option value="">Select Machine</option>
                                </select>
                            </td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[1][cycle_time_estimate]" disabled></td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[1][cycle_time_actual]" disabled></td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[1][capacity_estimate]" disabled></td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[1][capacity_actual]" disabled></td>
                            <td><input type="text" class="form-control" name="processes[1][remarks]" disabled>
                            </td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>Extrusion 3</td>
                            <td class="text-center">
                                <input type="checkbox" name="processes[3][enabled]" value="1"
                                    class="process-checkbox">
                                <input type="hidden" name="processes[3][process_name]" value="Extrusion 3">
                            </td>
                            <td>
                                <select class="form-control machine-select" name="processes[3][machine_id]" disabled>
                                    <option value="">Select Machine</option>
                                </select>
                            </td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[3][cycle_time_estimate]" disabled></td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[3][cycle_time_actual]" disabled></td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[3][capacity_estimate]" disabled></td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[3][capacity_actual]" disabled></td>
                            <td><input type="text" class="form-control" name="processes[3][remarks]" disabled>
                            </td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>Mandreling</td>
                            <td class="text-center">
                                <input type="checkbox" name="processes[4][enabled]" value="1"
                                    class="process-checkbox">
                                <input type="hidden" name="processes[4][process_name]" value="Mandreling">
                            </td>
                            <td>
                                <select class="form-control machine-select" name="processes[4][machine_id]" disabled>
                                    <option value="">Select Machine</option>
                                </select>
                            </td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[4][cycle_time_estimate]" disabled></td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[4][cycle_time_actual]" disabled></td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[4][capacity_estimate]" disabled></td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[4][capacity_actual]" disabled></td>
                            <td><input type="text" class="form-control" name="processes[4][remarks]" disabled>
                            </td>
                        </tr>
                        <tr>
                            <td>6</td>
                            <td>Curring</td>
                            <td class="text-center">
                                <input type="checkbox" name="processes[5][enabled]" value="1"
                                    class="process-checkbox">
                                <input type="hidden" name="processes[5][process_name]" value="Curring">
                            </td>
                            <td>
                                <select class="form-control machine-select" name="processes[5][machine_id]" disabled>
                                    <option value="">Select Machine</option>
                                </select>
                            </td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[5][cycle_time_estimate]" disabled></td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[5][cycle_time_actual]" disabled></td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[5][capacity_estimate]" disabled></td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[5][capacity_actual]" disabled></td>
                            <td><input type="text" class="form-control" name="processes[5][remarks]" disabled>
                            </td>
                        </tr>
                        <tr>
                            <td>7</td>
                            <td>Release Mandrel</td>
                            <td class="text-center">
                                <input type="checkbox" name="processes[6][enabled]" value="1"
                                    class="process-checkbox">
                                <input type="hidden" name="processes[6][process_name]" value="Release Mandrel">
                            </td>
                            <td>
                                <select class="form-control machine-select" name="processes[6][machine_id]" disabled>
                                    <option value="">Select Machine</option>
                                </select>
                            </td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[6][cycle_time_estimate]" disabled></td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[6][cycle_time_actual]" disabled></td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[6][capacity_estimate]" disabled></td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[6][capacity_actual]" disabled></td>
                            <td><input type="text" class="form-control" name="processes[6][remarks]" disabled>
                            </td>
                        </tr>
                        <tr>
                            <td>8</td>
                            <td>Dry Curring</td>
                            <td class="text-center">
                                <input type="checkbox" name="processes[7][enabled]" value="1"
                                    class="process-checkbox">
                                <input type="hidden" name="processes[7][process_name]" value="Dry Curring">
                            </td>
                            <td>
                                <select class="form-control machine-select" name="processes[7][machine_id]" disabled>
                                    <option value="">Select Machine</option>
                                </select>
                            </td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[7][cycle_time_estimate]" disabled></td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[7][cycle_time_actual]" disabled></td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[7][capacity_estimate]" disabled></td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[7][capacity_actual]" disabled></td>
                            <td><input type="text" class="form-control" name="processes[7][remarks]" disabled>
                            </td>
                        </tr>
                        <tr>
                            <td>9</td>
                            <td>Cutting</td>
                            <td class="text-center">
                                <input type="checkbox" name="processes[8][enabled]" value="1"
                                    class="process-checkbox">
                                <input type="hidden" name="processes[8][process_name]" value="Cutting">
                            </td>
                            <td>
                                <select class="form-control machine-select" name="processes[8][machine_id]" disabled>
                                    <option value="">Select Machine</option>
                                </select>
                            </td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[8][cycle_time_estimate]" disabled></td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[8][cycle_time_actual]" disabled></td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[8][capacity_estimate]" disabled></td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[8][capacity_actual]" disabled></td>
                            <td><input type="text" class="form-control" name="processes[8][remarks]" disabled>
                            </td>
                        </tr>
                        <tr>
                            <td>10</td>
                            <td>Marking</td>
                            <td class="text-center">
                                <input type="checkbox" name="processes[9][enabled]" value="1"
                                    class="process-checkbox">
                                <input type="hidden" name="processes[9][process_name]" value="Marking">
                            </td>
                            <td>
                                <select class="form-control machine-select" name="processes[9][machine_id]" disabled>
                                    <option value="">Select Machine</option>
                                </select>
                            </td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[9][cycle_time_estimate]" disabled></td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[9][cycle_time_actual]" disabled></td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[9][capacity_estimate]" disabled></td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[9][capacity_actual]" disabled></td>
                            <td><input type="text" class="form-control" name="processes[9][remarks]" disabled>
                            </td>
                        </tr>
                        <tr>
                            <td>11</td>
                            <td>Inspection</td>
                            <td class="text-center">
                                <input type="checkbox" name="processes[10][enabled]" value="1"
                                    class="process-checkbox">
                                <input type="hidden" name="processes[10][process_name]" value="Inspection">
                            </td>
                            <td>
                                <select class="form-control machine-select" name="processes[10][machine_id]" disabled>
                                    <option value="">Select Machine</option>
                                </select>
                            </td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[10][cycle_time_estimate]" disabled></td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[10][cycle_time_actual]" disabled></td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[10][capacity_estimate]" disabled></td>
                            <td><input type="number" step="0.01" class="form-control"
                                    name="processes[10][capacity_actual]" disabled></td>
                            <td><input type="text" class="form-control" name="processes[10][remarks]" disabled>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- (5). New & Important Point -->
    <div class="row mt-4">
        <div class="col-md-6">
            <label><strong>( 5 ). New & Important Point</strong></label>
            <div class="table-responsive">
                <table class="table table-bordered table-form" id="importantPointTable">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 40px;">No</th>
                            <th>Material / Process / Tools</th>
                            <th>Note</th>
                            <th style="width: 60px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="importantPointTableBody">
                        @if (isset($salesInfo) && $salesInfo->importantPoints && $salesInfo->importantPoints->count() > 0)
                            @foreach ($salesInfo->importantPoints as $index => $point)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><input type="text" class="form-control"
                                            name="important_points[{{ $index }}][item]"
                                            value="{{ old('important_points.' . $index . '.item', $point->item) }}">
                                    </td>
                                    <td><input type="text" class="form-control"
                                            name="important_points[{{ $index }}][note]"
                                            value="{{ old('important_points.' . $index . '.note', $point->note) }}">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-danger btn-remove-point"
                                            onclick="removeImportantPointRow(this)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td>1</td>
                                <td><input type="text" class="form-control" name="important_points[0][item]"></td>
                                <td><input type="text" class="form-control" name="important_points[0][note]"></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger btn-remove-point" disabled>
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                <button type="button" class="btn btn-sm btn-success btn-add-row" id="btnAddImportantPoint">
                    <i class="fas fa-plus"></i> Add Point
                </button>
            </div>
        </div>

        <!-- (6). Tooling -->
        <div class="col-md-6">
            <label><strong>( 6 ). Tooling</strong></label>
            <div class="table-responsive">
                <table class="table table-bordered table-form">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 40px;">No</th>
                            <th>Tooling</th>
                            <th>Cavity / unit (pcs)</th>
                            <th>Quantity (pcs)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $toolingNames = ['Dies', 'Mandrel', 'Mall Cutting', 'Mall Checking'];
                            $existingToolings = [];

                            if (isset($salesInfo) && $salesInfo->toolings) {
                                foreach ($salesInfo->toolings as $tooling) {
                                    $existingToolings[$tooling->tooling] = $tooling;
                                }
                            }
                        @endphp

                        @foreach ($toolingNames as $index => $toolingName)
                            @php
                                $toolingData = $existingToolings[$toolingName] ?? null;
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    {{ $toolingName }}
                                    <input type="hidden" name="tooling[{{ $index }}][name]"
                                        value="{{ $toolingName }}">
                                </td>
                                <td><input type="number" class="form-control"
                                        name="tooling[{{ $index }}][cavity]"
                                        value="{{ old('tooling.' . $index . '.cavity', $toolingData ? $toolingData->cavity : '') }}">
                                </td>
                                <td><input type="number" class="form-control"
                                        name="tooling[{{ $index }}][quantity]"
                                        value="{{ old('tooling.' . $index . '.quantity', $toolingData ? $toolingData->quantity : '') }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
