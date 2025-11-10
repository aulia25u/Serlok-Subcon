@extends('layouts.app')

@section('title', 'LOI - Edit Team Feasibility Commitment')
@section('page-title', 'LOI - Edit Team Feasibility Commitment')

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2_min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3_0/dist/select2-bootstrap-5-theme.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2_min.css">
    <style>
        .form-header {
            background-color: #f8f9fa;
            padding: 15px;
            border: 1px solid #dee2e6;
            margin-bottom: 20px;
        }

        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 10px;
        }

        .header-left,
        .header-right {
            flex: 1;
        }

        .header-center {
            flex: 2;
            text-align: center;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .checklist-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .checklist-table th,
        .checklist-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        .checklist-table th {
            background-color: #f8f9fa;
            text-align: center;
            font-weight: bold;
        }

        .checkpoint-main {
            font-weight: bold;
            background-color: #e9ecef;
        }

        .checkpoint-sub {
            padding-left: 30px;
        }

        .checkpoint-sub-sub {
            padding-left: 60px;
        }

        .keterangan-col {
            width: 100px;
            text-align: center;
        }

        .pic-col {
            width: 120px;
            text-align: center;
        }

        .notes-col {
            width: 200px;
        }

        .checkbox-cell {
            text-align: center;
            vertical-align: middle;
        }

        /* Center align checkboxes and radio buttons */
        .checkbox-cell input[type="radio"],
        .checkbox-cell input[type="checkbox"] {
            margin: 0 auto;
            display: block;
            vertical-align: middle;
        }

        .conclusion-section {
            margin-top: 30px;
            border: 1px solid #dee2e6;
            padding: 20px;
        }

        .conclusion-options {
            display: flex;
            gap: 20px;
            margin: 15px 0;
        }

        .conclusion-option {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .sign-off-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .sign-off-table th,
        .sign-off-table td {
            border: 1px solid #000;
            padding: 15px;
            text-align: center;
        }

        .sign-off-table th {
            background-color: #f8f9fa;
        }

        .revision-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .revision-table th,
        .revision-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        .revision-table th {
            background-color: #f8f9fa;
        }

        .select2-container {
            width: 100% !important;
        }

        .sign-off-table td {
            padding: 10px;
            vertical-align: middle;
        }

        .sign-off-table select {
            width: 100%;
            min-width: 120px;
        }
    </style>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form id="feasibilityForm">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="commitment_id" value="{{ $commitment['id'] }}">

                            <!-- Header Section -->
                            <div class="form-header">
                                <div class="header-row">
                                    <div class="header-left">
                                        <img src="{{ asset('/icon.png') }}" alt="Logo"
                                            style="height: 60px;">
                                    </div>
                                    <div class="header-center">
                                        <div>PT. SHIMADA KARYA INDONESIA</div>
                                        <div style="margin-top: 10px; font-size: 1rem;">TEAM FEASIBILITY COMMITMENT - EDIT</div>
                                    </div>
                                    <div class="header-right" style="text-align: right;">
                                        <div>FR ENG 01 01</div>
                                        <div>Ed/Rev : 01/01</div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="row mb-2">
                                            <label class="col-sm-4 col-form-label">DOCUMENT NO</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control form-control-sm" readonly
                                                    value="{{ $commitment['document_no'] }}">
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <label class="col-sm-4 col-form-label">PART NAME</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control form-control-sm" id="part_name"
                                                    name="part_name" value="{{ $commitment['part_name'] }}" readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <label class="col-sm-4 col-form-label">PART NUMBER</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control form-control-sm" id="part_no"
                                                    name="part_no" value="{{ $commitment['part_no'] }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row mb-2">
                                            <label class="col-sm-4 col-form-label">MODEL</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control form-control-sm" id="model"
                                                    name="model" value="{{ $commitment['model'] }}" readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <label class="col-sm-4 col-form-label">CUSTOMER NAME</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control form-control-sm"
                                                    id="customer_name" name="customer_name" value="{{ $commitment['customer_name'] }}" readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-8">
                                                <input hidden type="text" class="form-control form-control-sm"
                                                    id="customer_id" name="customer_id" value="{{ $commitment['customer_id'] }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Checklist Table -->
                            <table class="checklist-table">
                                <thead style="position: sticky; top: 0; border: 1px solid #000;">
                                    <tr>
                                        <th style="border: 1px solid #000;" rowspan="2" style="width: 50px;">NO</th>
                                        <th style="border: 1px solid #000;" rowspan="2">Point Cek</th>
                                        <th style="border: 1px solid #000;" colspan="2" style="width: 150px;">Ketentuan
                                        </th>
                                        <th style="border: 1px solid #000;" rowspan="2" class="pic-col">PIC</th>
                                        <th style="border: 1px solid #000;" rowspan="2" class="notes-col">Keterangan</th>
                                    </tr>
                                    <tr>

                                        <th style="width: 75px; border: 1px solid #000;">OK</th>
                                        <th style="width: 75px; border: 1px solid #000;">Tidak OK</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Item 1 -->
                                    <tr class="checkpoint-main">
                                        <td>1</td>
                                        <td>Apakah Ada similar product ?</td>
                                        <td class="checkbox-cell">
                                            <input type="radio" name="check_result_1" value="ok"
                                                {{ isset($commitment['checklist_items']['1']) && $commitment['checklist_items']['1']['check_result'] == 'ok' ? 'checked' : '' }}>
                                        </td>
                                        <td class="checkbox-cell">
                                            <input type="radio" name="check_result_1" value="tidak_ok"
                                                {{ isset($commitment['checklist_items']['1']) && $commitment['checklist_items']['1']['check_result'] == 'tidak_ok' ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">ENG</td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" name="notes_1"
                                                value="{{ $commitment['checklist_items']['1']['notes'] ?? '' }}">
                                        </td>
                                    </tr>
                                    <tr class="checkpoint-sub">
                                        <td></td>
                                        <td>
                                            <input type="radio" name="similar_product_detail" value="yes"
                                                id="similar_yes_1"
                                                {{ isset($commitment['checklist_items']['1_1']) && $commitment['checklist_items']['1_1']['is_checkbox'] ? 'checked' : '' }}>
                                            <label for="similar_yes_1">Ya, yaitu:</label>
                                            <input type="text" class="form-control form-control-sm d-inline-block"
                                                style="width: 300px;" name="checkbox_value_1_1"
                                                id="similar_product_input_1" placeholder="Masukkan nama produk similar"
                                                value="{{ $commitment['checklist_items']['1_1']['checkbox_value'] ?? '' }}"
                                                {{ isset($commitment['checklist_items']['1_1']) && $commitment['checklist_items']['1_1']['is_checkbox'] ? '' : 'disabled' }}>
                                        </td>
                                        <td colspan="2"></td>
                                        <td class="text-center">ENG</td>
                                        <td></td>
                                    </tr>
                                    <tr class="checkpoint-sub">
                                        <td></td>
                                        <td>
                                            <input type="radio" name="similar_product_detail" value="no"
                                                id="similar_no_1"
                                                {{ isset($commitment['checklist_items']['1_2']) && $commitment['checklist_items']['1_2']['is_checkbox'] ? 'checked' : '' }}>
                                            <label for="similar_no_1">Tidak</label>
                                        </td>
                                        <td colspan="2"></td>
                                        <td class="text-center">ENG</td>
                                        <td></td>
                                    </tr>

                                    <!-- Item 2 -->
                                    <tr class="checkpoint-main">
                                        <td>2</td>
                                        <td>Biaya</td>
                                        <td colspan="2"></td>
                                        <td class="text-center">SLS</td>
                                        <td></td>
                                    </tr>
                                    <tr class="checkpoint-sub">
                                        <td></td>
                                        <td>1.1 Apakah target biaya proses produksi bisa dipenuhi</td>
                                        <td class="checkbox-cell">
                                            <input type="radio" name="check_result_2_1" value="ok"
                                                {{ isset($commitment['checklist_items']['2_1']) && $commitment['checklist_items']['2_1']['check_result'] == 'ok' ? 'checked' : '' }}>
                                        </td>
                                        <td class="checkbox-cell">
                                            <input type="radio" name="check_result_2_1" value="tidak_ok"
                                                {{ isset($commitment['checklist_items']['2_1']) && $commitment['checklist_items']['2_1']['check_result'] == 'tidak_ok' ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">SLS</td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" name="notes_2_1"
                                                value="{{ $commitment['checklist_items']['2_1']['notes'] ?? '' }}">
                                        </td>
                                    </tr>

                                    <!-- Item 3 -->
                                    <tr class="checkpoint-main">
                                        <td>3</td>
                                        <td>Kemampuan Proses Produksi</td>
                                        <td colspan="2"></td>
                                        <td class="text-center">ENG</td>
                                        <td></td>
                                    </tr>
                                    <tr class="checkpoint-sub">
                                        <td></td>
                                        <td>3.1 Apakah produk bisa dibuat dengan memenuhi semua karakteristik<br>yang
                                            ditentukan oleh pelanggan? (semua toleransi di drawing)</td>
                                        <td class="checkbox-cell">
                                            <input type="radio" name="check_result_3_1" value="ok"
                                                {{ isset($commitment['checklist_items']['3_1']) && $commitment['checklist_items']['3_1']['check_result'] == 'ok' ? 'checked' : '' }}>
                                        </td>
                                        <td class="checkbox-cell">
                                            <input type="radio" name="check_result_3_1" value="tidak_ok"
                                                {{ isset($commitment['checklist_items']['3_1']) && $commitment['checklist_items']['3_1']['check_result'] == 'tidak_ok' ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">ENG</td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" name="notes_3_1"
                                                value="{{ $commitment['checklist_items']['3_1']['notes'] ?? '' }}">
                                        </td>
                                    </tr>
                                    <tr class="checkpoint-sub">
                                        <td></td>
                                        <td>3.2 Ada persyaratan karakteristik khusus</td>
                                        <td class="checkbox-cell">
                                            <input type="radio" name="check_result_3_2" value="ok"
                                                {{ isset($commitment['checklist_items']['3_2']) && $commitment['checklist_items']['3_2']['check_result'] == 'ok' ? 'checked' : '' }}>
                                        </td>
                                        <td class="checkbox-cell">
                                            <input type="radio" name="check_result_3_2" value="tidak_ok"
                                                {{ isset($commitment['checklist_items']['3_2']) && $commitment['checklist_items']['3_2']['check_result'] == 'tidak_ok' ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">ENG</td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" name="notes_3_2"
                                                value="{{ $commitment['checklist_items']['3_2']['notes'] ?? '' }}">
                                        </td>
                                    </tr>
                                    <tr class="checkpoint-sub-sub">
                                        <td></td>
                                        <td>
                                            <input type="radio" name="special_requirement_detail" value="yes"
                                                id="special_yes_3"
                                                {{ isset($commitment['checklist_items']['3_2_1']) && $commitment['checklist_items']['3_2_1']['is_checkbox'] ? 'checked' : '' }}>
                                            <label for="special_yes_3">Ya, yaitu:</label>
                                            <input type="text" class="form-control form-control-sm d-inline-block"
                                                style="width: 300px;" name="checkbox_value_3_2_1"
                                                id="special_requirement_input_3" placeholder="Masukkan persyaratan khusus"
                                                value="{{ $commitment['checklist_items']['3_2_1']['checkbox_value'] ?? '' }}"
                                                {{ isset($commitment['checklist_items']['3_2_1']) && $commitment['checklist_items']['3_2_1']['is_checkbox'] ? '' : 'disabled' }}>
                                        </td>
                                        <td colspan="2"></td>
                                        <td class="text-center">ENG</td>
                                        <td></td>
                                    </tr>
                                    <tr class="checkpoint-sub-sub">
                                        <td></td>
                                        <td>
                                            <input type="radio" name="special_requirement_detail" value="no"
                                                id="special_no_3"
                                                {{ isset($commitment['checklist_items']['3_2_2']) && $commitment['checklist_items']['3_2_2']['is_checkbox'] ? 'checked' : '' }}>
                                            <label for="special_no_3">Tidak</label>
                                        </td>
                                        <td colspan="2"></td>
                                        <td class="text-center">ENG</td>
                                        <td></td>
                                    </tr>
                                    <tr class="checkpoint-sub">
                                        <td></td>
                                        <td>3.3 Jika ya, Apakah semua persyaratan bisa dikuti ?</td>
                                        <td class="checkbox-cell">
                                            <input type="radio" name="check_result_3_3" value="ok"
                                                {{ isset($commitment['checklist_items']['3_3']) && $commitment['checklist_items']['3_3']['check_result'] == 'ok' ? 'checked' : '' }}>
                                        </td>
                                        <td class="checkbox-cell">
                                            <input type="radio" name="check_result_3_3" value="tidak_ok"
                                                {{ isset($commitment['checklist_items']['3_3']) && $commitment['checklist_items']['3_3']['check_result'] == 'tidak_ok' ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">ENG</td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" name="notes_3_3"
                                                value="{{ $commitment['checklist_items']['3_3']['notes'] ?? '' }}">
                                        </td>
                                    </tr>

                                    <!-- Item 4 -->
                                    <tr class="checkpoint-main">
                                        <td>4</td>
                                        <td>Kapasitas Produksi</td>
                                        <td colspan="2"></td>
                                        <td></td>
                                        <td></td>

                                    </tr>
                                    <tr class="checkpoint-sub">
                                        <td></td>
                                        <td>4.1 Apakah kapasitas produksi/supply di supplier bisa<br>memenuhi target order
                                        </td>
                                        <td class="checkbox-cell">
                                            <input type="radio" name="check_result_4_1" value="ok">
                                        </td>
                                        <td class="checkbox-cell">
                                            <input type="radio" name="check_result_4_1" value="tidak_ok">
                                        </td>
                                        <td class="text-center">PPIC</td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" name="notes_4_1">
                                        </td>
                                    </tr>
                                    <tr class="checkpoint-sub">
                                        <td></td>
                                        <td>4.2 Apakah kapasitas produksi di internal mencukupi</td>
                                        <td class="checkbox-cell">
                                            <input type="radio" name="check_result_4_2" value="ok">
                                        </td>
                                        <td class="checkbox-cell">
                                            <input type="radio" name="check_result_4_2" value="tidak_ok">
                                        </td>
                                        <td class="text-center">PPIC</td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" name="notes_4_2">
                                        </td>
                                    </tr>
                                    <tr class="checkpoint-sub-sub">
                                        <td></td>
                                        <td>
                                            <input type="checkbox" name="is_checkbox_4_2_1" value="1"> di Proses
                                            Extruder
                                        </td>
                                        <td colspan="2"></td>
                                        <td class="text-center">PPIC</td>
                                        <td></td>
                                    </tr>
                                    <tr class="checkpoint-sub-sub">
                                        <td></td>
                                        <td>
                                            <input type="checkbox" name="is_checkbox_4_2_2" value="1"> di Proses
                                            Manual
                                            / Waya
                                        </td>
                                        <td colspan="2"></td>
                                        <td class="text-center">PPIC</td>
                                        <td></td>
                                    </tr>
                                    <tr class="checkpoint-sub-sub">
                                        <td></td>
                                        <td>
                                            <input type="checkbox" name="is_checkbox_4_2_3" value="1"> di Proses
                                            Cutting
                                        </td>
                                        <td colspan="2"></td>
                                        <td class="text-center">PPIC</td>
                                        <td></td>
                                    </tr>
                                    <tr class="checkpoint-sub-sub">
                                        <td></td>
                                        <td>
                                            <input type="checkbox" name="is_checkbox_4_2_4" value="1"> di Proses
                                            Assy
                                        </td>
                                        <td colspan="2"></td>
                                        <td class="text-center">PPIC</td>
                                        <td></td>
                                    </tr>
                                    <tr class="checkpoint-sub-sub">
                                        <td></td>
                                        <td>
                                            <input type="checkbox" name="is_checkbox_4_2_5" value="1"> di Proses
                                            inspection
                                        </td>
                                        <td colspan="2"></td>
                                        <td class="text-center">PPIC</td>
                                        <td></td>
                                    </tr>

                                    <!-- Item 5 -->
                                    <tr class="checkpoint-main">
                                        <td>5</td>
                                        <td>Tenggat waktu</td>
                                        <td colspan="2"></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr class="checkpoint-sub">
                                        <td></td>
                                        <td>5.1 Dapatkan semua material dan bahan pembantu tersedia tepat<br>waktu?</td>
                                        <td class="checkbox-cell">
                                            <input type="radio" name="check_result_5_1" value="ok">
                                        </td>
                                        <td class="checkbox-cell">
                                            <input type="radio" name="check_result_5_1" value="tidak_ok">
                                        </td>
                                        <td class="text-center">Purc/Eng</td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" name="notes_5_1">
                                        </td>
                                    </tr>
                                    <tr class="checkpoint-sub">
                                        <td></td>
                                        <td>5.2 Apakah tooling produksi bisa selesai tepat waktu</td>
                                        <td class="checkbox-cell">
                                            <input type="radio" name="check_result_5_2" value="ok">
                                        </td>
                                        <td class="checkbox-cell">
                                            <input type="radio" name="check_result_5_2" value="tidak_ok">
                                        </td>
                                        <td class="text-center">Purc/Eng</td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" name="notes_5_2">
                                        </td>
                                    </tr>
                                    <tr class="checkpoint-sub">
                                        <td></td>
                                        <td>5.3 Apakah dapat mengikuti jadual pelanggan untuk:</td>
                                        <td colspan="2"></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr class="checkpoint-sub-sub">
                                        <td></td>
                                        <td>
                                            <input type="checkbox" name="is_checkbox_5_3_1" value="1"> Kirim sample
                                        </td>
                                        <td colspan="2"></td>
                                        <td class="text-center">All Team</td>
                                        <td></td>
                                    </tr>
                                    <tr class="checkpoint-sub-sub">
                                        <td></td>
                                        <td>
                                            <input type="checkbox" name="is_checkbox_5_3_2" value="1"> Mulai
                                            produksi
                                            massal
                                        </td>
                                        <td colspan="2"></td>
                                        <td class="text-center">PPC</td>
                                        <td></td>
                                    </tr>

                                    <!-- Item 6 -->
                                    <tr class="checkpoint-main">
                                        <td>6</td>
                                        <td>Persyaratan Lingkungan</td>
                                        <td colspan="2"></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr class="checkpoint-sub">
                                        <td></td>
                                        <td>6.1 Dapatkan persyaratan SoC/RoHS dipenuhi?</td>
                                        <td class="checkbox-cell">
                                            <input type="radio" name="check_result_6_1" value="ok">
                                        </td>
                                        <td class="checkbox-cell">
                                            <input type="radio" name="check_result_6_1" value="tidak_ok">
                                        </td>
                                        <td class="text-center">Purc/QA</td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" name="notes_6_1">
                                        </td>
                                    </tr>

                                    <!-- Item 7 -->
                                    <tr class="checkpoint-main">
                                        <td>7</td>
                                        <td>Peraturan Pemerintah</td>
                                        <td colspan="2"></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr class="checkpoint-sub">
                                        <td></td>
                                        <td>7.1 Pembuangan material/produk dan daur ulang</td>
                                        <td class="checkbox-cell">
                                            <input type="radio" name="check_result_7_1" value="ok">
                                        </td>
                                        <td class="checkbox-cell">
                                            <input type="radio" name="check_result_7_1" value="tidak_ok">
                                        </td>
                                        <td class="text-center">Produksi</td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" name="notes_7_1">
                                        </td>
                                    </tr>
                                    <tr class="checkpoint-sub">
                                        <td></td>
                                        <td>7.2 Keselamatan dan kesehatan kerja</td>
                                        <td class="checkbox-cell">
                                            <input type="radio" name="check_result_7_2" value="ok">
                                        </td>
                                        <td class="checkbox-cell">
                                            <input type="radio" name="check_result_7_2" value="tidak_ok">
                                        </td>
                                        <td class="text-center">GA</td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" name="notes_7_2">
                                        </td>
                                    </tr>
                                    <tr class="checkpoint-sub">
                                        <td></td>
                                        <td>7.3 Lingkungan lainnya</td>
                                        <td class="checkbox-cell">
                                            <input type="radio" name="check_result_7_3" value="ok">
                                        </td>
                                        <td class="checkbox-cell">
                                            <input type="radio" name="check_result_7_3" value="tidak_ok">
                                        </td>
                                        <td class="text-center">GA</td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" name="notes_7_3">
                                        </td>
                                    </tr>
                                    <tr class="checkpoint-sub">
                                        <td></td>
                                        <td>Lain-lain:</td>
                                        <td colspan="2"></td>
                                        <td></td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm"
                                                name="notes_7_other" placeholder="Masukkan keterangan lain-lain">
                                        </td>
                                    </tr>

                                    <!-- Item 8 -->
                                    <tr class="checkpoint-main">
                                        <td>8</td>
                                        <td>Lain-lain</td>
                                        <td colspan="2"></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr class="checkpoint-sub">
                                        <td></td>
                                        <td>
                                            <input type="checkbox" name="is_checkbox_8_1" value="1">
                                            8.1 Apakah perlu menambah fasilitas mesin/tooling/pengetahuan<br>untuk
                                            menjalankan produk baru ?<br>Jika ya, apakah dapat memenuhinya?
                                        </td>
                                        <td class="checkbox-cell">
                                            <input type="radio" name="check_result_8_1" value="ok">
                                        </td>
                                        <td class="checkbox-cell">
                                            <input type="radio" name="check_result_8_1" value="tidak_ok">
                                        </td>
                                        <td class="text-center">Eng/Prod</td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" name="notes_8_1">
                                        </td>
                                    </tr>
                                    <tr class="checkpoint-sub">
                                        <td></td>
                                        <td>
                                            <input type="checkbox" name="is_checkbox_8_2" value="1">
                                            8.2 Apakah perlu menambah fasilitas mesin/tooling/pengetahuan<br>untuk
                                            menguji produk baru ?<br>Jika ya, apakah dapat memenuhinya?
                                        </td>
                                        <td class="checkbox-cell">
                                            <input type="radio" name="check_result_8_2" value="ok">
                                        </td>
                                        <td class="checkbox-cell">
                                            <input type="radio" name="check_result_8_2" value="tidak_ok">
                                        </td>
                                        <td class="text-center">QA/Eng</td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" name="notes_8_2">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- Conclusion Section -->
                            <div class="conclusion-section">
                                <h5><strong>Conclusion</strong></h5>
                                <div class="conclusion-options">
                                    <div class="conclusion-option">
                                        <input type="radio" name="conclusion_status" value="feasible" id="feasible"
                                            {{ isset($commitment['conclusion_status']) && $commitment['conclusion_status'] == 'feasible' ? 'checked' : '' }}>
                                        <label for="feasible">Feasible - Produk dapat diproduksi sesuai spesifikasi tanpa
                                            revisi.</label>
                                    </div>
                                </div>
                                <div class="conclusion-options">
                                    <div class="conclusion-option">
                                        <input type="radio" name="conclusion_status" value="feasible_with_changes"
                                            id="feasible_with_changes"
                                            {{ isset($commitment['conclusion_status']) && $commitment['conclusion_status'] == 'feasible_with_changes' ? 'checked' : '' }}>
                                        <label for="feasible_with_changes">Feasible - Perubahan direkomendasikan (lihat
                                            lampiran).</label>
                                    </div>
                                </div>
                                <div class="conclusion-options">
                                    <div class="conclusion-option">
                                        <input type="radio" name="conclusion_status" value="not_feasible"
                                            id="not_feasible"
                                            {{ isset($commitment['conclusion_status']) && $commitment['conclusion_status'] == 'not_feasible' ? 'checked' : '' }}>
                                        <label for="not_feasible">Not Feasible - Revisi desain diperlukan untuk
                                            menghasilkan
                                            produk sesuai spesifikasi requirements.</label>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <label><strong>Notes:</strong></label>
                                    <textarea class="form-control" name="conclusion_notes" rows="3">{{ $commitment['conclusion_notes'] ?? '' }}</textarea>
                                </div>
                            </div>

                            <!-- Revision Table -->
                            <div class="mt-4">
                                <h5><strong>Revision History</strong></h5>
                                <table class="revision-table" id="revisionTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 100px;">Rev No</th>
                                            <th style="width: 150px;">Rev Date</th>
                                            <th>Rev Contains</th>
                                            <th style="width: 80px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="revisionTableBody">
                                        @forelse($commitment['revisions'] as $index => $revision)
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control form-control-sm"
                                                    name="revisions[{{ $index }}][revision_number]" value="{{ $revision['revision_number'] }}" readonly>
                                            </td>
                                            <td>
                                                <input type="date" class="form-control form-control-sm"
                                                    name="revisions[{{ $index }}][revision_date]" value="{{ $revision['revision_date'] }}" required>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm"
                                                    name="revisions[{{ $index }}][revision_contains]" value="{{ $revision['revision_contains'] }}" required>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-danger remove-revision" {{ $index == 0 ? 'disabled' : '' }}>
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <!-- Default revision row if no revisions exist -->
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control form-control-sm"
                                                    name="revisions[0][revision_number]" value="01" readonly>
                                            </td>
                                            <td>
                                                <input type="date" class="form-control form-control-sm"
                                                    name="revisions[0][revision_date]" value="{{ date('Y-m-d') }}" required>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm"
                                                    name="revisions[0][revision_contains]" value="Initial revision" required>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-danger remove-revision" disabled>
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <button type="button" class="btn btn-sm btn-success mt-2" id="addRevisionBtn">
                                    <i class="fas fa-plus"></i> Add Revision
                                </button>
                            </div>

                            <!-- Sign-off Section -->
                            <table class="sign-off-table">
                                <thead>
                                    <tr>
                                        <th>General Mgr</th>
                                        <th>Factory Mgr</th>
                                        <th>QA Mgr</th>
                                        <th>QC</th>
                                        <th>Engineering</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <select class="form-control form-control-sm user-select"
                                                name="general_mgr_id">
                                                <option value="">Select...</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control form-control-sm user-select"
                                                name="factory_mgr_id">
                                                <option value="">Select...</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control form-control-sm user-select" name="qa_mgr_id">
                                                <option value="">Select...</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control form-control-sm user-select" name="qc_id">
                                                <option value="">Select...</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control form-control-sm user-select"
                                                name="engineering_id">
                                                <option value="">Select...</option>
                                            </select>
                                        </td>


                                    </tr>
                                </tbody>
                            </table>

                            <table class="sign-off-table">
                                <thead>
                                    <tr>

                                        <th>Production</th>
                                        <th>Maintenance</th>
                                        <th>PPIC</th>
                                        <th>Purchasing</th>
                                        <th>Sales</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <select class="form-control form-control-sm user-select" name="production_id">
                                                <option value="">Select...</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control form-control-sm user-select"
                                                name="maintenance_id">
                                                <option value="">Select...</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control form-control-sm user-select" name="ppic_id">
                                                <option value="">Select...</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control form-control-sm user-select" name="purchasing_id">
                                                <option value="">Select...</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control form-control-sm user-select" name="sales_id">
                                                <option value="">Select...</option>
                                            </select>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <!-- Action Buttons -->
                            <div class="mt-4 text-center">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                    <i class="fas fa-save"></i> Update
                                </button>
                                <a href="{{ route('loi.feasability-commitment.index') }}"
                                    class="btn btn-secondary btn-lg">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
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
            let revisionCount = {{ count($commitment['revisions'] ?? []) }}; // Set dari jumlah revisi yang ada

            // Checklist items data from backend
            const checklistItems = @json($commitment['checklist_items'] ?? []);

            // Pre-fill checklist items
            prefillChecklistItems(checklistItems);

            // Trigger change event for radio buttons to enable/disable dependent fields
            // For similar product detail (item 1)
            if ($('input[name="similar_product_detail"]:checked').length > 0) {
                $('input[name="similar_product_detail"]:checked').trigger('change');
            }

            // For special requirement detail (item 3_2)
            if ($('input[name="special_requirement_detail"]:checked').length > 0) {
                $('input[name="special_requirement_detail"]:checked').trigger('change');
            }

            // Initialize Select2 for part selection (disabled in edit mode)
            // Part selection is read-only in edit mode

            // Load users for signature dropdowns
            loadUsers();

            // Function to pre-fill checklist items
            function prefillChecklistItems(items) {
                // Loop through all checklist items and pre-fill
                Object.keys(items).forEach(function(itemCode) {
                    const item = items[itemCode];

                    // Pre-fill radio buttons for check_result
                    if (item.check_result) {
                        $(`input[name="check_result_${itemCode}"][value="${item.check_result}"]`).prop('checked', true);
                    }

                    // Pre-fill notes
                    if (item.notes) {
                        $(`input[name="notes_${itemCode}"]`).val(item.notes);
                        $(`textarea[name="notes_${itemCode}"]`).val(item.notes);
                    }

                    // Pre-fill checkboxes
                    if (item.is_checkbox) {
                        $(`input[name="is_checkbox_${itemCode}"]`).prop('checked', true);
                    }

                    // Pre-fill checkbox values
                    if (item.checkbox_value) {
                        $(`input[name="checkbox_${itemCode}"]`).val(item.checkbox_value);
                    }
                });
            }

            // Add Revision Row
            $('#addRevisionBtn').on('click', function() {
                const newRevNo = String(revisionCount + 1).padStart(2, '0');
                const today = new Date().toISOString().split('T')[0];

                const newRow = `
                    <tr>
                        <td>
                            <input type="text" class="form-control form-control-sm"
                                name="revisions[${revisionCount}][revision_number]"
                                value="${newRevNo}" readonly>
                        </td>
                        <td>
                            <input type="date" class="form-control form-control-sm"
                                name="revisions[${revisionCount}][revision_date]"
                                value="${today}" required>
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm"
                                name="revisions[${revisionCount}][revision_contains]"
                                placeholder="Enter revision description" required>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-danger remove-revision">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;

                $('#revisionTableBody').append(newRow);
                revisionCount++;
            });

            // Remove Revision Row
            $(document).on('click', '.remove-revision', function() {
                if ($('#revisionTableBody tr').length > 1) {
                    $(this).closest('tr').remove();
                    // Reindex revision numbers
                    reindexRevisions();
                }
            });

            // Reindex revision numbers and form names
            function reindexRevisions() {
                $('#revisionTableBody tr').each(function(index) {
                    $(this).find('input[name*="revision_number"]').attr('name',
                        `revisions[${index}][revision_number]`);
                    $(this).find('input[name*="revision_date"]').attr('name',
                        `revisions[${index}][revision_date]`);
                    $(this).find('input[name*="revision_contains"]').attr('name',
                        `revisions[${index}][revision_contains]`);

                    // Update revision number display
                    const revNo = String(index + 1).padStart(2, '0');
                    $(this).find('input[name*="revision_number"]').val(revNo);

                    // Disable remove button for first row
                    if (index === 0) {
                        $(this).find('.remove-revision').prop('disabled', true);
                    } else {
                        $(this).find('.remove-revision').prop('disabled', false);
                    }
                });
                revisionCount = $('#revisionTableBody tr').length;
            }

            // Load users function
            function loadUsers() {
                $.ajax({
                    url: '{{ route('loi.feasability-commitment.users') }}',
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            // Sign-off user IDs from backend
                            const signOffUsers = {
                                general_mgr_id: '{{ $commitment["general_mgr_id"] ?? "" }}',
                                factory_mgr_id: '{{ $commitment["factory_mgr_id"] ?? "" }}',
                                qa_mgr_id: '{{ $commitment["qa_mgr_id"] ?? "" }}',
                                qc_id: '{{ $commitment["qc_id"] ?? "" }}',
                                engineering_id: '{{ $commitment["engineering_id"] ?? "" }}',
                                production_id: '{{ $commitment["production_id"] ?? "" }}',
                                maintenance_id: '{{ $commitment["maintenance_id"] ?? "" }}',
                                ppic_id: '{{ $commitment["ppic_id"] ?? "" }}',
                                purchasing_id: '{{ $commitment["purchasing_id"] ?? "" }}',
                                sales_id: '{{ $commitment["sales_id"] ?? "" }}'
                            };

                            $('.user-select').each(function() {
                                const userSelect = $(this);
                                const selectName = userSelect.attr('name');
                                const currentValue = signOffUsers[selectName];

                                userSelect.empty();
                                userSelect.append('<option value="">Select...</option>');

                                response.data.forEach(function(user) {
                                    const selected = (currentValue && currentValue == user.id) ? 'selected' : '';
                                    userSelect.append(
                                        `<option value="${user.id}" ${selected}>${user.name}</option>`
                                    );
                                });
                            });
                        } else {
                            console.error('Response success is false:', response);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to load users list.'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        let errorMessage = 'Failed to load users list.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error Loading Users',
                            text: errorMessage,
                            footer: 'Status: ' + xhr.status + ' - ' + status
                        });
                    }
                });
            }

            // Handle radio button changes for similar product (section 1)
            $('input[name="similar_product_detail"]').on('change', function() {
                if ($(this).val() === 'yes') {
                    $('#similar_product_input_1').prop('disabled', false);
                } else {
                    $('#similar_product_input_1').prop('disabled', true).val('');
                }
            });

            // Handle radio button changes for special requirement (section 3_2)
            $('input[name="special_requirement_detail"]').on('change', function() {
                if ($(this).val() === 'yes') {
                    $('#special_requirement_input_3').prop('disabled', false);
                } else {
                    $('#special_requirement_input_3').prop('disabled', true).val('');
                }
            });

            // Form submission
            $('#feasibilityForm').on('submit', function(e) {
                e.preventDefault();

                // Disable submit button
                $('#submitBtn').prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin"></i> Updating...');

                // Serialize form data
                const formData = $(this).serialize();

                $.ajax({
                    url: '{{ route("loi.feasability-commitment.update", ["id" => $commitment["id"]]) }}',
                    type: 'PUT',
                    data: formData,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.href =
                                '{{ route('loi.feasability-commitment.index') }}';
                        });
                    },
                    error: function(xhr) {
                        let errorMessage = 'An error occurred while updating data.';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            errorMessage += '<br><ul>';
                            $.each(errors, function(key, value) {
                                errorMessage += '<li>' + value[0] + '</li>';
                            });
                            errorMessage += '</ul>';
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            html: errorMessage
                        });

                        // Re-enable submit button
                        $('#submitBtn').prop('disabled', false).html(
                            '<i class="fas fa-save"></i> Update');
                    }
                });
            });
        });
    </script>
@endpush
