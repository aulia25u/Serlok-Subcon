@extends('layouts.app')

@section('title', 'LOI - View Team Feasibility Commitment')
@section('page-title', 'LOI - View Team Feasibility Commitment')

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1_3.0/dist/select2-bootstrap-5-theme.min.css" />
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

        .conclusion-section {
            margin-top: 30px;
            border: 1px solid #dee2e6;
            padding: 20px;
        }

        /* Print styles */
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }

            .checklist-table th,
            .checklist-table td {
                border: 1px solid #000 !important;
            }
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

        /* Disable pointer events for disabled checkboxes */
        input[disabled],
        textarea[disabled] {
            background-color: #e9ecef;
            cursor: not-allowed;
        }

        /* Highlight checked items */
        .checked-mark {
            display: inline-block;
            font-weight: bold;
            color: #28a745;
            /* font-size: 18px; */
            text-align: center;
            width: 100%;
        }

        /* Checked checkbox in Point Cek column */
        .checked-checkbox {
            font-weight: bold;
            color: #28a745;
            /* font-size: 18px; */
        }

        /* Unchecked box style */
        .unchecked-box {
            display: inline-block;
            font-size: 18px;
            color: #6c757d;
            text-align: center;
            width: 100%;
        }

        /* Unchecked checkbox in Point Cek column */
        .unchecked-checkbox {
            font-size: 18px;
            color: #6c757d;
        }

        /* Center align checkboxes and radio buttons */
        .checkbox-cell input[type="radio"],
        .checkbox-cell input[type="checkbox"] {
            margin: 0;
            vertical-align: middle;
        }

        /* Signature card styles */
        .signature-card {
            border: 1px solid #dee2e6;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .signature-card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 10px 15px;
            font-weight: bold;
            text-align: center;
        }

        .signature-card-body {
            padding: 15px;
            text-align: center;
            min-height: 120px;
        }

        .signature-area {
            height: 60px;
            border-bottom: 2px solid #dee2e6;
            margin-bottom: 10px;
        }

        .signature-name {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .signature-role {
            color: #6c757d;
            font-size: 0.875rem;
        }
    </style>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Header Section -->
                        <div class="form-header">
                            <div class="header-row">
                                <div class="header-left">
                                    <img src="{{ asset('/icon.png') }}" alt="Logo" style="height: 60px;">
                                </div>
                                <div class="header-center">
                                    <div>PT. SHIMADA KARYA INDONESIA</div>
                                    <div style="margin-top: 10px; font-size: 1rem;">TEAM FEASIBILITY COMMITMENT</div>
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
                                                value="{{ $commitment['document_no'] ?? 'N/A' }}">
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <label class="col-sm-4 col-form-label">PART NAME</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm" readonly
                                                value="{{ $commitment['part_name'] ?? 'N/A' }}">
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <label class="col-sm-4 col-form-label">PART NUMBER</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm" readonly
                                                value="{{ $commitment['part_no'] ?? 'N/A' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row mb-2">
                                        <label class="col-sm-4 col-form-label">MODEL</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm" readonly
                                                value="{{ $commitment['model'] ?? 'N/A' }}">
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <label class="col-sm-4 col-form-label">CUSTOMER NAME</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm" readonly
                                                value="{{ $commitment['customer_name'] ?? 'N/A' }}">
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <label class="col-sm-4 col-form-label">CUSTOMER ID</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm" readonly
                                                value="{{ $commitment['customer_id'] }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Checklist Table -->
                        <table class="checklist-table">
                            <thead style=" top: 0; border: 1px solid #000;">
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
                            <tbody style="border: 1px solid #000;">
                                @php
                                    $items = $commitment['checklist_items'] ?? [];
                                @endphp

                                <!-- Item 1 -->
                                <tr class="checkpoint-main">
                                    <td>1</td>
                                    <td>Apakah Ada similar product ?</td>
                                    <td class="checkbox-cell">
                                        @if (isset($items['1']) && $items['1']['check_result'] === 'ok')
                                            <span class="checked-mark">☑</span>
                                        @else
                                            <span class="unchecked-box">☐</span>
                                        @endif
                                    </td>
                                    <td class="checkbox-cell">
                                        @if (isset($items['1']) && $items['1']['check_result'] === 'tidak_ok')
                                            <span class="checked-mark">☑</span>
                                        @else
                                            <span class="unchecked-box">☐</span>
                                        @endif
                                    </td>
                                    <td class="text-center">ENG</td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" readonly
                                            value="{{ $items['1']['notes'] ?? '' }}">
                                    </td>
                                </tr>
                                <tr class="checkpoint-sub">
                                    <td></td>
                                    <td>
                                        @if (isset($items['1_1']) && $items['1_1']['is_checkbox'])
                                            <span class="checked-checkbox">☑</span>
                                        @else
                                            <span class="unchecked-checkbox">☐</span>
                                        @endif
                                        Ya, yaitu:
                                        <input type="text" class="form-control form-control-sm d-inline-block"
                                            style="width: 300px;" readonly
                                            value="{{ $items['1_1']['checkbox_value'] ?? '' }}">
                                    </td>
                                    <td colspan="2"></td>
                                    <td class="text-center">ENG</td>
                                    <td></td>
                                </tr>
                                <tr class="checkpoint-sub">
                                    <td></td>
                                    <td>
                                        @if (isset($items['1_2']) && $items['1_2']['is_checkbox'])
                                            <span class="checked-checkbox">☑</span>
                                        @else
                                            <span class="unchecked-checkbox">☐</span>
                                        @endif
                                        Tidak
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
                                        @if (isset($items['2_1']) && $items['2_1']['check_result'] === 'ok')
                                            <span class="checked-mark">☑</span>
                                        @else
                                            <span class="unchecked-box">☐</span>
                                        @endif
                                    </td>
                                    <td class="checkbox-cell">
                                        @if (isset($items['2_1']) && $items['2_1']['check_result'] === 'tidak_ok')
                                            <span class="checked-mark">☑</span>
                                        @else
                                            <span class="unchecked-box">☐</span>
                                        @endif
                                    </td>
                                    <td class="text-center">SLS</td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" readonly
                                            value="{{ $items['2_1']['notes'] ?? '' }}">
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
                                        @if (isset($items['3_1']) && $items['3_1']['check_result'] === 'ok')
                                            <span class="checked-mark">☑</span>
                                        @else
                                            <span class="unchecked-box">☐</span>
                                        @endif
                                    </td>
                                    <td class="checkbox-cell">
                                        @if (isset($items['3_1']) && $items['3_1']['check_result'] === 'tidak_ok')
                                            <span class="checked-mark">☑</span>
                                        @else
                                            <span class="unchecked-box">☐</span>
                                        @endif
                                    </td>
                                    <td class="text-center">ENG</td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" readonly
                                            value="{{ $items['3_1']['notes'] ?? '' }}">
                                    </td>
                                </tr>
                                <tr class="checkpoint-sub">
                                    <td></td>
                                    <td>3.2 Ada persyaratan karakteristik khusus</td>
                                    <td class="checkbox-cell">
                                        @if (isset($items['3_2']) && $items['3_2']['check_result'] === 'ok')
                                            <span class="checked-mark">☑</span>
                                        @else
                                            <span class="unchecked-box">☐</span>
                                        @endif
                                    </td>
                                    <td class="checkbox-cell">
                                        @if (isset($items['3_2']) && $items['3_2']['check_result'] === 'tidak_ok')
                                            <span class="checked-mark">☑</span>
                                        @else
                                            <span class="unchecked-box">☐</span>
                                        @endif
                                    </td>
                                    <td class="text-center">ENG</td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" readonly
                                            value="{{ $items['3_2']['notes'] ?? '' }}">
                                    </td>
                                </tr>
                                <tr class="checkpoint-sub-sub">
                                    <td></td>
                                    <td>
                                        @if (isset($items['3_2_1']) && $items['3_2_1']['is_checkbox'])
                                            <span class="checked-checkbox">☑</span>
                                        @else
                                            <span class="unchecked-checkbox">☐</span>
                                        @endif
                                        Ya, yaitu:
                                        <input type="text" class="form-control form-control-sm d-inline-block"
                                            style="width: 300px;" readonly
                                            value="{{ $items['3_2_1']['checkbox_value'] ?? '' }}">
                                    </td>
                                    <td colspan="2"></td>
                                    <td class="text-center">ENG</td>
                                    <td></td>
                                </tr>
                                <tr class="checkpoint-sub-sub">
                                    <td></td>
                                    <td>
                                        @if (isset($items['3_2_2']) && $items['3_2_2']['is_checkbox'])
                                            <span class="checked-checkbox">☑</span>
                                        @else
                                            <span class="unchecked-checkbox">☐</span>
                                        @endif
                                        Tidak
                                    </td>
                                    <td colspan="2"></td>
                                    <td class="text-center">ENG</td>
                                    <td></td>
                                </tr>
                                <tr class="checkpoint-sub">
                                    <td></td>
                                    <td>3.3 Jika ya, Apakah semua persyaratan bisa dikuti ?</td>
                                    <td class="checkbox-cell">
                                        @if (isset($items['3_3']) && $items['3_3']['check_result'] === 'ok')
                                            <span class="checked-mark">☑</span>
                                        @else
                                            <span class="unchecked-box">☐</span>
                                        @endif
                                    </td>
                                    <td class="checkbox-cell">
                                        @if (isset($items['3_3']) && $items['3_3']['check_result'] === 'tidak_ok')
                                            <span class="checked-mark">☑</span>
                                        @else
                                            <span class="unchecked-box">☐</span>
                                        @endif
                                    </td>
                                    <td class="text-center">ENG</td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" readonly
                                            value="{{ $items['3_3']['notes'] ?? '' }}">
                                    </td>
                                </tr>

                                <!-- Item 4 -->
                                <tr class="checkpoint-main">
                                    <td>4</td>
                                    <td>Kapasitas Produksi</td>
                                    <td colspan="2"></td>
                                    <td class="text-center">PPIC</td>
                                    <td></td>
                                </tr>
                                <tr class="checkpoint-sub">
                                    <td></td>
                                    <td>4.1 Apakah kapasitas produksi/supply di supplier bisa<br>memenuhi target order
                                    </td>
                                    <td class="checkbox-cell">
                                        @if (isset($items['4_1']) && $items['4_1']['check_result'] === 'ok')
                                            <span class="checked-mark">☑</span>
                                        @else
                                            <span class="unchecked-box">☐</span>
                                        @endif
                                    </td>
                                    <td class="checkbox-cell">
                                        @if (isset($items['4_1']) && $items['4_1']['check_result'] === 'tidak_ok')
                                            <span class="checked-mark">☑</span>
                                        @else
                                            <span class="unchecked-box">☐</span>
                                        @endif
                                    </td>
                                    <td class="text-center">PPIC</td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" readonly
                                            value="{{ $items['4_1']['notes'] ?? '' }}">
                                    </td>
                                </tr>
                                <tr class="checkpoint-sub">
                                    <td></td>
                                    <td>4.2 Apakah kapasitas produksi di internal mencukupi</td>
                                    <td class="checkbox-cell">
                                        @if (isset($items['4_2']) && $items['4_2']['check_result'] === 'ok')
                                            <span class="checked-mark">☑</span>
                                        @else
                                            <span class="unchecked-box">☐</span>
                                        @endif
                                    </td>
                                    <td class="checkbox-cell">
                                        @if (isset($items['4_2']) && $items['4_2']['check_result'] === 'tidak_ok')
                                            <span class="checked-mark">☑</span>
                                        @else
                                            <span class="unchecked-box">☐</span>
                                        @endif
                                    </td>
                                    <td class="text-center">PPIC</td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" readonly
                                            value="{{ $items['4_2']['notes'] ?? '' }}">
                                    </td>
                                </tr>
                                <tr class="checkpoint-sub-sub">
                                    <td></td>
                                    <td>
                                        @if (isset($items['4_2_1']) && $items['4_2_1']['is_checkbox'])
                                            <span class="checked-checkbox">☑</span>
                                        @else
                                            <span class="unchecked-checkbox">☐</span>
                                        @endif
                                        di Proses Extruder
                                    </td>
                                    <td colspan="2"></td>
                                    <td class="text-center">PPIC</td>
                                    <td></td>
                                </tr>
                                <tr class="checkpoint-sub-sub">
                                    <td></td>
                                    <td>
                                        @if (isset($items['4_2_2']) && $items['4_2_2']['is_checkbox'])
                                            <span class="checked-checkbox">☑</span>
                                        @else
                                            <span class="unchecked-checkbox">☐</span>
                                        @endif
                                        di Proses Manual / Waya
                                    </td>
                                    <td colspan="2"></td>
                                    <td class="text-center">PPIC</td>
                                    <td></td>
                                </tr>
                                <tr class="checkpoint-sub-sub">
                                    <td></td>
                                    <td>
                                        @if (isset($items['4_2_3']) && $items['4_2_3']['is_checkbox'])
                                            <span class="checked-checkbox">☑</span>
                                        @else
                                            <span class="unchecked-checkbox">☐</span>
                                        @endif
                                        di Proses Cutting
                                    </td>
                                    <td colspan="2"></td>
                                    <td class="text-center">PPIC</td>
                                    <td></td>
                                </tr>
                                <tr class="checkpoint-sub-sub">
                                    <td></td>
                                    <td>
                                        @if (isset($items['4_2_4']) && $items['4_2_4']['is_checkbox'])
                                            <span class="checked-checkbox">☑</span>
                                        @else
                                            <span class="unchecked-checkbox">☐</span>
                                        @endif
                                        di Proses Assy
                                    </td>
                                    <td colspan="2"></td>
                                    <td class="text-center">PPIC</td>
                                    <td></td>
                                </tr>
                                <tr class="checkpoint-sub-sub">
                                    <td></td>
                                    <td>
                                        @if (isset($items['4_2_5']) && $items['4_2_5']['is_checkbox'])
                                            <span class="checked-checkbox">☑</span>
                                        @else
                                            <span class="unchecked-checkbox">☐</span>
                                        @endif
                                        di Proses inspection
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
                                        @if (isset($items['5_1']) && $items['5_1']['check_result'] === 'ok')
                                            <span class="checked-mark">☑</span>
                                        @else
                                            <span class="unchecked-box">☐</span>
                                        @endif
                                    </td>
                                    <td class="checkbox-cell">
                                        @if (isset($items['5_1']) && $items['5_1']['check_result'] === 'tidak_ok')
                                            <span class="checked-mark">☑</span>
                                        @else
                                            <span class="unchecked-box">☐</span>
                                        @endif
                                    </td>
                                    <td class="text-center">Purc/Eng</td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" readonly
                                            value="{{ $items['5_1']['notes'] ?? '' }}">
                                    </td>
                                </tr>
                                <tr class="checkpoint-sub">
                                    <td></td>
                                    <td>5.2 Apakah tooling produksi bisa selesai tepat waktu</td>
                                    <td class="checkbox-cell">
                                        @if (isset($items['5_2']) && $items['5_2']['check_result'] === 'ok')
                                            <span class="checked-mark">☑</span>
                                        @else
                                            <span class="unchecked-box">☐</span>
                                        @endif
                                    </td>
                                    <td class="checkbox-cell">
                                        @if (isset($items['5_2']) && $items['5_2']['check_result'] === 'tidak_ok')
                                            <span class="checked-mark">☑</span>
                                        @else
                                            <span class="unchecked-box">☐</span>
                                        @endif
                                    </td>
                                    <td class="text-center">Purc/Eng</td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" readonly
                                            value="{{ $items['5_2']['notes'] ?? '' }}">
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
                                        @if (isset($items['5_3_1']) && $items['5_3_1']['is_checkbox'])
                                            <span class="checked-checkbox">☑</span>
                                        @else
                                            <span class="unchecked-checkbox">☐</span>
                                        @endif
                                        Kirim sample
                                    </td>
                                    <td colspan="2"></td>
                                    <td class="text-center">All Team</td>
                                    <td></td>
                                </tr>
                                <tr class="checkpoint-sub-sub">
                                    <td></td>
                                    <td>
                                        @if (isset($items['5_3_2']) && $items['5_3_2']['is_checkbox'])
                                            <span class="checked-checkbox">☑</span>
                                        @else
                                            <span class="unchecked-checkbox">☐</span>
                                        @endif
                                        Mulai produksi massal
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
                                    <td class="text-center">Purc/QA</td>
                                    <td></td>
                                </tr>
                                <tr class="checkpoint-sub">
                                    <td></td>
                                    <td>6.1 Dapatkan persyaratan SoC/RoHS dipenuhi?</td>
                                    <td class="checkbox-cell">
                                        @if (isset($items['6_1']) && $items['6_1']['check_result'] === 'ok')
                                            <span class="checked-mark">☑</span>
                                        @else
                                            <span class="unchecked-box">☐</span>
                                        @endif
                                    </td>
                                    <td class="checkbox-cell">
                                        @if (isset($items['6_1']) && $items['6_1']['check_result'] === 'tidak_ok')
                                            <span class="checked-mark">☑</span>
                                        @else
                                            <span class="unchecked-box">☐</span>
                                        @endif
                                    </td>
                                    <td class="text-center">Purc/QA</td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" readonly
                                            value="{{ $items['6_1']['notes'] ?? '' }}">
                                    </td>
                                </tr>

                                <!-- Item 7 -->
                                <tr class="checkpoint-main">
                                    <td>7</td>
                                    <td>Peraturan Pemerintah</td>
                                    <td colspan="2"></td>
                                    <td class="text-center">Produksi</td>
                                    <td></td>
                                </tr>
                                <tr class="checkpoint-sub">
                                    <td></td>
                                    <td>7.1 Pembuangan material/produk dan daur ulang</td>
                                    <td class="checkbox-cell">
                                        @if (isset($items['7_1']) && $items['7_1']['check_result'] === 'ok')
                                            <span class="checked-mark">☑</span>
                                        @else
                                            <span class="unchecked-box">☐</span>
                                        @endif
                                    </td>
                                    <td class="checkbox-cell">
                                        @if (isset($items['7_1']) && $items['7_1']['check_result'] === 'tidak_ok')
                                            <span class="checked-mark">☑</span>
                                        @else
                                            <span class="unchecked-box">☐</span>
                                        @endif
                                    </td>
                                    <td class="text-center">Produksi</td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" readonly
                                            value="{{ $items['7_1']['notes'] ?? '' }}">
                                    </td>
                                </tr>
                                <tr class="checkpoint-sub">
                                    <td></td>
                                    <td>7.2 Keselamatan dan kesehatan kerja</td>
                                    <td class="checkbox-cell">
                                        @if (isset($items['7_2']) && $items['7_2']['check_result'] === 'ok')
                                            <span class="checked-mark">☑</span>
                                        @else
                                            <span class="unchecked-box">☐</span>
                                        @endif
                                    </td>
                                    <td class="checkbox-cell">
                                        @if (isset($items['7_2']) && $items['7_2']['check_result'] === 'tidak_ok')
                                            <span class="checked-mark">☑</span>
                                        @else
                                            <span class="unchecked-box">☐</span>
                                        @endif
                                    </td>
                                    <td class="text-center">GA</td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" readonly
                                            value="{{ $items['7_2']['notes'] ?? '' }}">
                                    </td>
                                </tr>
                                <tr class="checkpoint-sub">
                                    <td></td>
                                    <td>7.3 Lingkungan lainnya</td>
                                    <td class="checkbox-cell">
                                        @if (isset($items['7_3']) && $items['7_3']['check_result'] === 'ok')
                                            <span class="checked-mark">☑</span>
                                        @else
                                            <span class="unchecked-box">☐</span>
                                        @endif
                                    </td>
                                    <td class="checkbox-cell">
                                        @if (isset($items['7_3']) && $items['7_3']['check_result'] === 'tidak_ok')
                                            <span class="checked-mark">☑</span>
                                        @else
                                            <span class="unchecked-box">☐</span>
                                        @endif
                                    </td>
                                    <td class="text-center">GA</td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" readonly
                                            value="{{ $items['7_3']['notes'] ?? '' }}">
                                    </td>
                                </tr>
                                <tr class="checkpoint-sub">
                                    <td></td>
                                    <td>Lain-lain:</td>
                                    <td colspan="2"></td>
                                    <td></td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" readonly
                                            value="{{ $items['7_other']['notes'] ?? '' }}">
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
                                        @if (isset($items['8_1']) && $items['8_1']['is_checkbox'])
                                            <span class="checked-checkbox">☑</span>
                                        @else
                                            <span class="unchecked-checkbox">☐</span>
                                        @endif
                                        8.1 Apakah perlu menambah fasilitas mesin/tooling/pengetahuan<br>untuk
                                        menjalankan produk baru ?<br>Jika ya, apakah dapat memenuhinya?
                                    </td>
                                    <td class="checkbox-cell">
                                        @if (isset($items['8_1']) && $items['8_1']['check_result'] === 'ok')
                                            <span class="checked-mark">☑</span>
                                        @else
                                            <span class="unchecked-box">☐</span>
                                        @endif
                                    </td>
                                    <td class="checkbox-cell">
                                        @if (isset($items['8_1']) && $items['8_1']['check_result'] === 'tidak_ok')
                                            <span class="checked-mark">☑</span>
                                        @else
                                            <span class="unchecked-box">☐</span>
                                        @endif
                                    </td>
                                    <td class="text-center">Eng/Prod</td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" readonly
                                            value="{{ $items['8_1']['notes'] ?? '' }}">
                                    </td>
                                </tr>
                                <tr class="checkpoint-sub">
                                    <td></td>
                                    <td>
                                        @if (isset($items['8_2']) && $items['8_2']['is_checkbox'])
                                            <span class="checked-checkbox">☑</span>
                                        @else
                                            <span class="unchecked-checkbox">☐</span>
                                        @endif
                                        8.2 Apakah perlu menambah fasilitas mesin/tooling/pengetahuan<br>untuk
                                        menguji produk baru ?<br>Jika ya, apakah dapat memenuhinya?
                                    </td>
                                    <td class="checkbox-cell">
                                        @if (isset($items['8_2']) && $items['8_2']['check_result'] === 'ok')
                                            <span class="checked-mark">☑</span>
                                        @else
                                            <span class="unchecked-box">☐</span>
                                        @endif
                                    </td>
                                    <td class="checkbox-cell">
                                        @if (isset($items['8_2']) && $items['8_2']['check_result'] === 'tidak_ok')
                                            <span class="checked-mark">☑</span>
                                        @else
                                            <span class="unchecked-box">☐</span>
                                        @endif
                                    </td>
                                    <td class="text-center">QA/Eng</td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" readonly
                                            value="{{ $items['8_2']['notes'] ?? '' }}">
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Conclusion Section -->
                        <div class="conclusion-section">
                            <h5><strong>Conclusion</strong></h5>
                            <div class="conclusion-options">
                                <div class="conclusion-option">
                                    @if ($commitment['conclusion_status'] === 'feasible')
                                        <span class="checked-checkbox">☑</span>
                                    @else
                                        <span class="unchecked-checkbox">☐</span>
                                    @endif
                                    Feasible - Produk dapat diproduksi sesuai spesifikasi tanpa revisi.
                                </div>
                            </div>
                            <div class="conclusion-options">
                                <div class="conclusion-option">
                                    @if ($commitment['conclusion_status'] === 'feasible_with_changes')
                                        <span class="checked-checkbox">☑</span>
                                    @else
                                        <span class="unchecked-checkbox">☐</span>
                                    @endif
                                    Feasible - Perubahan direkomendasikan (lihat lampiran).
                                </div>
                            </div>
                            <div class="conclusion-options">
                                <div class="conclusion-option">
                                    @if ($commitment['conclusion_status'] === 'not_feasible')
                                        <span class="checked-checkbox">☑</span>
                                    @else
                                        <span class="unchecked-checkbox">☐</span>
                                    @endif
                                    Not Feasible - Revisi desain diperlukan untuk menghasilkan produk sesuai spesifikasi
                                    requirements.
                                </div>
                            </div>

                            <div class="mt-3">
                                <label><strong>Notes:</strong></label>
                                <textarea class="form-control" readonly rows="3">{{ $commitment['conclusion_notes'] ?? '' }}</textarea>
                            </div>
                        </div>

                        <!-- Revision Table -->
                        <div class="mt-4">
                            <h5><strong>Revision History</strong></h5>
                            <table class="revision-table">
                                <thead>
                                    <tr>
                                        <th style="width: 100px;">Rev No</th>
                                        <th style="width: 150px;">Rev Date</th>
                                        <th>Rev Contains</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($commitment['revisions'] as $revision)
                                        <tr>
                                            <td>{{ $revision['revision_number'] }}</td>
                                            <td>{{ $revision['revision_date'] }}</td>
                                            <td>{{ $revision['revision_contains'] }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">No revision history available</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Sign-off Section -->
                        <div class="mt-4">
                            <h5 class="mb-3"><strong>Sign-off Approval</strong></h5>

                            <!-- First Row - 5 Columns -->
                            <div class="row">
                                <!-- General Mgr -->
                                <div class="col" style="flex: 0 0 20%; max-width: 20%;">
                                    <div class="signature-card">
                                        <div class="signature-card-header">General Mgr</div>
                                        <div class="signature-card-body">
                                            <div class="signature-area"></div>
                                            <div class="signature-name">{{ $commitment['general_mgr'] ?? '-' }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Factory Mgr -->
                                <div class="col" style="flex: 0 0 20%; max-width: 20%;">
                                    <div class="signature-card">
                                        <div class="signature-card-header">Factory Mgr</div>
                                        <div class="signature-card-body">
                                            <div class="signature-area"></div>
                                            <div class="signature-name">{{ $commitment['factory_mgr'] ?? '-' }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- QA Mgr -->
                                <div class="col" style="flex: 0 0 20%; max-width: 20%;">
                                    <div class="signature-card">
                                        <div class="signature-card-header">QA Mgr</div>
                                        <div class="signature-card-body">
                                            <div class="signature-area"></div>
                                            <div class="signature-name">{{ $commitment['qa_mgr'] ?? '-' }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- QC -->
                                <div class="col" style="flex: 0 0 20%; max-width: 20%;">
                                    <div class="signature-card">
                                        <div class="signature-card-header">QC</div>
                                        <div class="signature-card-body">
                                            <div class="signature-area"></div>
                                            <div class="signature-name">{{ $commitment['qc'] ?? '-' }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Engineering -->
                                <div class="col" style="flex: 0 0 20%; max-width: 20%;">
                                    <div class="signature-card">
                                        <div class="signature-card-header">Engineering</div>
                                        <div class="signature-card-body">
                                            <div class="signature-area"></div>
                                            <div class="signature-name">{{ $commitment['engineering'] ?? '-' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Second Row - 5 Columns -->
                            <div class="row">
                                <!-- Production -->
                                <div class="col" style="flex: 0 0 20%; max-width: 20%;">
                                    <div class="signature-card">
                                        <div class="signature-card-header">Production</div>
                                        <div class="signature-card-body">
                                            <div class="signature-area"></div>
                                            <div class="signature-name">{{ $commitment['production'] ?? '-' }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Maintenance -->
                                <div class="col" style="flex: 0 0 20%; max-width: 20%;">
                                    <div class="signature-card">
                                        <div class="signature-card-header">Maintenance</div>
                                        <div class="signature-card-body">
                                            <div class="signature-area"></div>
                                            <div class="signature-name">{{ $commitment['maintenance'] ?? '-' }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- PPIC -->
                                <div class="col" style="flex: 0 0 20%; max-width: 20%;">
                                    <div class="signature-card">
                                        <div class="signature-card-header">PPIC</div>
                                        <div class="signature-card-body">
                                            <div class="signature-area"></div>
                                            <div class="signature-name">{{ $commitment['ppic'] ?? '-' }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Purchasing -->
                                <div class="col" style="flex: 0 0 20%; max-width: 20%;">
                                    <div class="signature-card">
                                        <div class="signature-card-header">Purchasing</div>
                                        <div class="signature-card-body">
                                            <div class="signature-area"></div>
                                            <div class="signature-name">{{ $commitment['purchasing'] ?? '-' }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sales -->
                                <div class="col" style="flex: 0 0 20%; max-width: 20%;">
                                    <div class="signature-card">
                                        <div class="signature-card-header">Sales</div>
                                        <div class="signature-card-body">
                                            <div class="signature-area"></div>
                                            <div class="signature-name">{{ $commitment['sales'] ?? '-' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-4 text-center no-print">
                            <button type="button" class="btn btn-success btn-lg" id="printBtn">
                                <i class="fas fa-print"></i> Print
                            </button>
                            <a href="{{ route('loi.feasability-commitment.index') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@push('js')
    <script>
        $(document).ready(function() {
            // Print button handler
            $('#printBtn').on('click', function() {
                window.print();
            });

            console.log('Team Feasibility Commitment - View Mode');
        });
    </script>
@endpush
