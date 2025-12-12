<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan - {{ $suratJalan->document_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
        }

        .header p {
            margin: 5px 0 0;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .info-table td {
            vertical-align: top;
            padding: 5px;
        }

        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .content-table th,
        .content-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        .content-table th {
            background-color: #f0f0f0;
        }

        .signature-table {
            width: 100%;
            margin-top: 50px;
            text-align: center;
        }

        .signature-table td {
            padding-top: 80px;
        }

        @media print {
            @page {
                margin: 0.5cm;
            }

            body {
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }

        .btn-print {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 20px;
        }
    </style>
</head>

<body onload="window.print()">

    <button class="no-print btn-print" onclick="window.print()">Print Document</button>

    <div class="header">
        <h1>Surat Jalan</h1>
        <p>No: {{ $suratJalan->document_number }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%"><strong>Tanggal</strong></td>
            <td width="35%">: {{ $suratJalan->surat_jalan_date->format('d F Y') }}</td>
            <td width="15%"><strong>Kepada Yth.</strong></td>
            <td width="35%">: {{ $suratJalan->customer ? $suratJalan->customer->customer_name : '-' }}</td>
        </tr>
        <tr>
            <td><strong>Pengirim</strong></td>
            <td>:
                {{ optional(optional($suratJalan->employeeJob)->outgoing)->masterItem->tenantOwner->customer->customer_name ?? '-' }}
            </td>
            <td><strong>Alamat</strong></td>
            <td>: {{ $suratJalan->customer ? $suratJalan->customer->address : '-' }}</td>
        </tr>
        <tr>
            <td><strong>Driver</strong></td>
            <td>: {{ $suratJalan->driver_name ?? '-' }}</td>
            <td><strong>No. Polisi</strong></td>
            <td>: {{ $suratJalan->vehicle_number ?? '-' }}</td>
        </tr>
    </table>

    <table class="content-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th width="15%">Qty</th>
                <th width="10%">Satuan</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>{{ optional(optional(optional($suratJalan->employeeJob)->outgoing)->masterItem)->item_code ?? '-' }}
                </td>
                <td>{{ optional(optional(optional($suratJalan->employeeJob)->outgoing)->masterItem)->item_name ?? '-' }}
                </td>
                <td>{{ number_format($suratJalan->employeeJob->qty_ok ?? 0, 0) }}</td>
                <td>{{ optional(optional(optional($suratJalan->employeeJob)->outgoing)->masterItem)->unit ?? '-' }}</td>
                <td>{{ $suratJalan->employeeJob->notes ?? '-' }}</td>
            </tr>
        </tbody>
    </table>

    <table class="signature-table">
        <tr>
            <td width="25%">
                Dibuat Oleh,<br><br><br>
                ( {{ optional(optional($suratJalan->employeeJob)->user)->name ?? 'Admin' }} )
            </td>
            <td width="25%">
                Diketahui Oleh,<br><br><br>
                ( {{ $suratJalan->known_by ?? '....................' }} )
            </td>
            <td width="25%">
                Driver,<br><br><br>
                ( .................... )
            </td>
            <td width="25%">
                Penerima,<br><br><br>
                ( .................... )
            </td>
        </tr>
    </table>

</body>

</html>