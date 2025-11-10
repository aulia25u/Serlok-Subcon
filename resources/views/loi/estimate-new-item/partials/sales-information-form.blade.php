<!-- Sales Information Section -->
<section>

    <div class="form-section">
        <div class="form-section-title">
            Sales Information <small>(diisi oleh bagian sales)</small>
        </div>

        <div class="row">
            <!-- Left Column -->
            <div class="col-md-6">
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" class="form-control" id="date" name="date"
                        value="{{ old('date', isset($salesInfo) ? $salesInfo->date->format('Y-m-d') : '') }}" required>
                </div>

                <div class="form-group">
                    <label>Customer</label>
                    <select class="form-control " id="customer_id" name="customer_id" required>
                        <option value="">Select Customer</option>
                        <!-- Will be populated via AJAX -->
                    </select>
                </div>

                <div class="form-group">
                    <label>Part No</label>
                    <select class="form-control" id="part_no" name="part_no" required>
                        <option value="">Select Part No</option>
                        <!-- Will be populated via AJAX -->
                    </select>
                </div>
                <div class="form-group">
                    <label>Part Name</label>
                    <input type="text" class="form-control" id="part_name" name="part_name"
                        value="{{ old('part_name', isset($salesInfo) ? $salesInfo->part_name : '') }}" readonly>
                </div>

                <div class="form-group">
                    <label>Date of Masspro</label>
                    <input type="date" class="form-control" id="date_masspro" name="date_masspro"
                        value="{{ old('date_masspro', isset($salesInfo) && $salesInfo->date_masspro ? $salesInfo->date_masspro->format('Y-m-d') : '') }}">
                </div>
                <div class="form-group">
                    <label>Qty/Month Periode</label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="qty_month" name="qty_month"
                            value="{{ old('qty_month', isset($salesInfo) ? $salesInfo->qty_month : '') }}" readonly>
                        <div class="input-group-append">
                            <span class="input-group-text">Pcs/month</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column  -->
            <div class="col-md-6">


                <div class="form-group">
                    <label>Depreciation Periode</label>
                    <input type="text" class="form-control" id="depreciation_periode" name="depreciation_periode"
                        value="{{ old('depreciation_periode', isset($salesInfo) ? $salesInfo->depreciation_periode : '') }}"
                        readonly>
                </div>
                <div class="form-group">
                    <label>Tools Depreciation</label>
                    <input type="text" class="form-control" id="tools_depreciation" name="tools_depreciation"
                        value="{{ old('tools_depreciation', isset($salesInfo) ? $salesInfo->tools_depreciation : '') }}"
                        readonly>
                </div>
                <div class="form-group">
                    <label><strong>Similar Part:</strong></label>
                    <div>
                        <label class="radio-inline">
                            <input type="radio" id="similar_part_yes" name="similar_part" value="1"
                                {{ old('similar_part', isset($salesInfo) && $salesInfo->similar_part ? 1 : 0) == 1 ? 'checked' : '' }}>
                            <i class="fas fa-check-circle text-success"></i> Yes
                        </label>
                        <label class="radio-inline ml-3">
                            <input type="radio" id="similar_part_no" name="similar_part" value="0"
                                {{ old('similar_part', isset($salesInfo) && $salesInfo->similar_part ? 1 : 0) == 0 ? 'checked' : '' }}>
                            <i class="fas fa-times-circle text-danger"></i> No
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label><strong>Part Type:</strong></label>
                    <div>
                        <label class="radio-inline">
                            <input type="radio" id="part_type_critical" name="part_type" value="critical_safety"
                                {{ old('part_type', isset($salesInfo) ? $salesInfo->part_type : '') == 'critical_safety' ? 'checked' : '' }}>
                            <i class="fas fa-exclamation-triangle warning-icon mr-2"></i> Critical / Safety Part
                        </label>
                        <label class="radio-inline ml-3">
                            <input type="radio" id="part_type_regular" name="part_type" value="regular_part"
                                {{ old('part_type', isset($salesInfo) ? $salesInfo->part_type : '') == 'regular_part' ? 'checked' : '' }}>
                            Regular Part
                        </label>
                    </div>
                </div>


                <div class="form-group">
                    <label><strong>Model:</strong></label>
                    <div class="row">
                        @php
                            $existingModel = isset($salesInfo) && $salesInfo->model ? $salesInfo->model : '';
                        @endphp

                        <!-- Left Column -->
                        <div class="col-md-6">
                            <div class="custom-control custom-radio mb-2">
                                <input type="radio" class="custom-control-input" id="model_hadaka" name="model"
                                    value="hadaka" {{ $existingModel == 'hadaka' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="model_hadaka">Hadaka</label>
                            </div>

                            <div class="custom-control custom-radio mb-2">
                                <input type="radio" class="custom-control-input" id="model_nakabi" name="model"
                                    value="nakabi" {{ $existingModel == 'nakabi' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="model_nakabi">Nakabi</label>
                            </div>

                            <div class="custom-control custom-radio mb-2">
                                <input type="radio" class="custom-control-input" id="model_sotobi" name="model"
                                    value="sotobi" {{ $existingModel == 'sotobi' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="model_sotobi">Sotobi</label>
                            </div>

                            <div class="custom-control custom-radio mb-2">
                                <input type="radio" class="custom-control-input" id="model_wrapping"
                                    name="model" value="wrapping"
                                    {{ $existingModel == 'wrapping' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="model_wrapping">Wrapping</label>
                            </div>

                            <div class="custom-control custom-radio mb-2">
                                <input type="radio" class="custom-control-input" id="model_nakabi_wrapping"
                                    name="model" value="nakabi_wrapping"
                                    {{ $existingModel == 'nakabi_wrapping' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="model_nakabi_wrapping">Nakabi
                                    Wrapping</label>
                            </div>


                            <div class="custom-control custom-radio mb-2">
                                <input type="radio" class="custom-control-input" id="model_nakabi_sotobi"
                                    name="model" value="nakabi_sotobi"
                                    {{ $existingModel == 'nakabi_sotobi' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="model_nakabi_sotobi">Nakabi + Sotobi</label>
                            </div>
                        </div>
                        <!-- Right Column - With Input Fields -->
                        <div class="col-md-6">
                            <div class="custom-control custom-radio mb-2">
                                <input type="radio" class="custom-control-input" id="model_double_nakabi"
                                    name="model" value="double_nakabi"
                                    {{ $existingModel == 'double_nakabi' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="model_double_nakabi">Double Layer +
                                    Nakabi</label>
                            </div>

                            <div class="d-flex align-items-center mb-2">
                                <div class="custom-control custom-radio mr-2" style="min-width: 100px;">
                                    <input type="radio" class="custom-control-input" id="model_waya_ply"
                                        name="model" value="waya_ply"
                                        {{ $existingModel == 'waya_ply' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="model_waya_ply">Waya</label>
                                </div>
                                <input type="text" class="form-control form-control-sm" id="waya_ply_value"
                                    name="waya_ply_value" placeholder="Enter ply" style="max-width: 120px;"
                                    value="{{ old('waya_ply_value', isset($salesInfo) ? $salesInfo->waya_ply_value : '') }}"
                                    {{ $existingModel != 'waya_ply' ? 'disabled' : '' }}>
                                <span class="ml-2">Ply</span>
                            </div>

                            <div class="d-flex align-items-center mb-2">
                                <div class="custom-control custom-radio mr-2" style="min-width: 100px;">
                                    <input type="radio" class="custom-control-input" id="model_wrapping_ply"
                                        name="model" value="wrapping_ply"
                                        {{ $existingModel == 'wrapping_ply' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="model_wrapping_ply">Wrapping</label>
                                </div>
                                <input type="text" class="form-control form-control-sm" id="wrapping_ply_value"
                                    name="wrapping_ply_value" placeholder="Enter ply" style="max-width: 120px;"
                                    value="{{ old('wrapping_ply_value', isset($salesInfo) ? $salesInfo->wrapping_ply_value : '') }}"
                                    {{ $existingModel != 'wrapping_ply' ? 'disabled' : '' }}>
                                <span class="ml-2">Ply</span>
                            </div>

                            <div class="d-flex align-items-center mb-2">
                                <div class="custom-control custom-radio mr-2" style="min-width: 100px;">
                                    <input type="radio" class="custom-control-input" id="model_other"
                                        name="model" value="other"
                                        {{ $existingModel == 'other' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="model_other">Other:</label>
                                </div>
                                <input type="text" class="form-control form-control-sm" id="model_other_value"
                                    name="model_other_value" placeholder="Specify model"
                                    style="flex: 1; max-width: 200px;"
                                    value="{{ old('model_other_value', isset($salesInfo) ? $salesInfo->model_other_value : '') }}"
                                    {{ $existingModel != 'other' ? 'disabled' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
