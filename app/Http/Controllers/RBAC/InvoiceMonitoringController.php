<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\DtInvoice;
use App\Models\InvoiceEditHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InvoiceMonitoringController extends Controller
{
    public function index()
    {
        // Get distinct Chat_Ids from dt_invoice
        $chatIds = DB::connection('mysql_invoice')
            ->table('dt_invoice')
            ->distinct()
            ->pluck('Chat_Id');

        // Get customers that have those chat IDs
        $customers = Customer::whereIn('telegram_chat_id', $chatIds)->get();

        return view('rbac.invoice-monitoring.index', compact('customers'));
    }

    public function getData(Request $request)
    {
        $query = DB::connection('mysql_invoice')
            ->table('dt_invoice')
            ->select('dt_invoice.*');

        // Apply filters
        if ($request->has('customer_id') && $request->customer_id != '') {
            $customer = Customer::find($request->customer_id);
            if ($customer) {
                $query->where('dt_invoice.Chat_Id', $customer->telegram_chat_id);
            }
        }

        if ($request->has('month') && $request->month != '') {
            $query->whereMonth('dt_invoice.Tanggal_Input', $request->month);
        }

        if ($request->has('year') && $request->year != '') {
            $query->whereYear('dt_invoice.Tanggal_Input', $request->year);
        }

        // Apply keyword filter
        if ($request->has('keyword') && $request->keyword != '' && $request->has('keyword_value') && $request->keyword_value != '') {
            $keyword = $request->keyword;
            $value = $request->keyword_value;

            // Map keyword to database column
            $columnMap = [
                'process_id' => 'ProcessID',
                'nomor_invoice' => 'Nomor_Invoice',
                'tanggal_struk' => 'Tanggal_Struk',
                'supplier' => 'Toko',
                'items' => 'Items',
                'jumlah' => 'Jumlah',
                'satuan' => 'Satuan',
                'harga_satuan' => 'Harga_Satuan',
                'payment' => 'Payment',
                'keterangan' => 'Keterangan',
            ];

            if (isset($columnMap[$keyword])) {
                $column = $columnMap[$keyword];
                
                // For numeric fields, use exact match
                if (in_array($keyword, ['jumlah', 'harga_satuan', 'payment'])) {
                    $query->where('dt_invoice.' . $column, $value);
                } else {
                    // For text fields, use LIKE
                    $query->where('dt_invoice.' . $column, 'like', '%' . $value . '%');
                }
            }
        }

        // Get total records before pagination
        $totalRecords = $query->count();

        // Handle pagination
        $perPage = $request->get('per_page', 25);
        
        if ($perPage === 'all') {
            $data = $query->orderBy('dt_invoice.id', 'desc')->get();
        } else {
            $data = $query->orderBy('dt_invoice.id', 'desc')
                ->paginate((int)$perPage);
        }

        // Get unique Chat_Ids from the result
        $chatIds = $data->pluck('Chat_Id')->unique();
        
        // Fetch customer names from the default database
        $customers = Customer::whereIn('telegram_chat_id', $chatIds)
            ->pluck('name', 'telegram_chat_id');

        // Format data for DataTables
        if ($perPage === 'all') {
            $formattedData = $data->map(function ($item, $index) use ($customers) {
                return $this->formatInvoiceData($item, $index + 1, $customers);
            });

            return response()->json([
                'data' => $formattedData,
                'total' => $totalRecords,
                'per_page' => 'all',
                'current_page' => 1,
                'last_page' => 1,
            ]);
        } else {
            $formattedData = $data->map(function ($item, $index) use ($data, $customers) {
                return $this->formatInvoiceData($item, ($data->currentPage() - 1) * $data->perPage() + $index + 1, $customers);
            });

            return response()->json([
                'data' => $formattedData,
                'total' => $data->total(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'field' => 'required|in:Nomor_Invoice,Tanggal_Struk,Toko,Items,Jumlah,Satuan,Harga_Satuan,Discount,Refunds,Pajak,Ongkir,Disc_Ongkir,Voucher,Asuransi_Pengiriman,Biaya_Layanan,Payment,Keterangan',
            'value' => 'required',
        ]);

        try {
            // Get the invoice record
            $invoice = DB::connection('mysql_invoice')
                ->table('dt_invoice')
                ->where('id', $id)
                ->first();

            if (!$invoice) {
                return response()->json(['error' => 'Invoice not found'], 404);
            }

            $field = $request->field;
            $value = $request->value;
            $oldValue = $invoice->$field;
            $oldTotal = $invoice->Total_Per_Item;

            // Prepare update data
            $updateData = [$field => $value];

            // If updating Jumlah or Harga_Satuan, recalculate Total_Per_Item
            if ($field === 'Jumlah' || $field === 'Harga_Satuan') {
                $jumlah = $field === 'Jumlah' ? $value : $invoice->Jumlah;
                $hargaSatuan = $field === 'Harga_Satuan' ? $value : $invoice->Harga_Satuan;

                $totalPerItem = floatval($jumlah) * floatval($hargaSatuan);
                $updateData['Total_Per_Item'] = $totalPerItem;
            }

            // If updating fields that affect Grand_Total, recalculate it
            if (in_array($field, ['Discount', 'Refunds', 'Pajak', 'Ongkir', 'Disc_Ongkir', 'Voucher', 'Asuransi_Pengiriman', 'Biaya_Layanan'])) {
                $subTotal = $invoice->Total_Per_Item;
                $discount = $field === 'Discount' ? $value : ($invoice->Discount ?? 0);
                $refunds = $field === 'Refunds' ? $value : ($invoice->Refunds ?? 0);
                $pajak = $field === 'Pajak' ? $value : ($invoice->Pajak ?? 0);
                $ongkir = $field === 'Ongkir' ? $value : ($invoice->Ongkir ?? 0);
                $discOngkir = $field === 'Disc_Ongkir' ? $value : ($invoice->Disc_Ongkir ?? 0);
                $voucher = $field === 'Voucher' ? $value : ($invoice->Voucher ?? 0);
                $asuransi = $field === 'Asuransi_Pengiriman' ? $value : ($invoice->Asuransi_Pengiriman ?? 0);
                $biayaLayanan = $field === 'Biaya_Layanan' ? $value : ($invoice->Biaya_Layanan ?? 0);

                $subAfterDiscount = $subTotal - floatval($discount);
                $grandTotal = $subAfterDiscount - floatval($refunds) + floatval($pajak) + floatval($ongkir) - floatval($discOngkir) - floatval($voucher) + floatval($asuransi) + floatval($biayaLayanan);

                $updateData['Sub_After_Discount'] = $subAfterDiscount;
                $updateData['Grand_Total'] = $grandTotal;
            }

            // Update the record
            DB::connection('mysql_invoice')
                ->table('dt_invoice')
                ->where('id', $id)
                ->update($updateData);

            // Get updated record
            $updatedInvoice = DB::connection('mysql_invoice')
                ->table('dt_invoice')
                ->where('id', $id)
                ->first();

            // Log the change to history
            InvoiceEditHistory::create([
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->Nomor_Invoice,
                'process_id' => $invoice->ProcessID ?? null,
                'telegram_chat_id' => $invoice->Chat_Id ?? null,
                'field_name' => $field,
                'old_value' => $oldValue,
                'new_value' => $value,
                'old_total' => $oldTotal,
                'new_total' => $updatedInvoice->Total_Per_Item,
                'edited_by' => Auth::id(),
                'editor_name' => Auth::user()->userDetail->employee_name ?? Auth::user()->name,
                'edited_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data updated successfully',
                'data' => [
                    'nomor_invoice' => $updatedInvoice->Nomor_Invoice ?? '-',
                    'tanggal_struk' => $updatedInvoice->Tanggal_Struk ? date('d-m-Y', strtotime($updatedInvoice->Tanggal_Struk)) : '-',
                    'toko' => $updatedInvoice->Toko ?? '-',
                    'items' => $updatedInvoice->Items ?? '-',
                    'jumlah' => number_format($updatedInvoice->Jumlah, 2),
                    'satuan' => $updatedInvoice->Satuan,
                    'harga_satuan' => number_format($updatedInvoice->Harga_Satuan, 2, ',', '.'),
                    'sub_total' => number_format($updatedInvoice->Total_Per_Item, 2, ',', '.'),
                    'discount' => number_format($updatedInvoice->Discount, 0, ',', '.'),
                    'sub_after_discount' => number_format($updatedInvoice->Sub_After_Discount, 0, ',', '.'),
                    'refunds' => number_format($updatedInvoice->Refunds, 0, ',', '.'),
                    'pajak' => number_format($updatedInvoice->Pajak, 0, ',', '.'),
                    'ongkir' => number_format($updatedInvoice->Ongkir, 0, ',', '.'),
                    'disc_ongkir' => number_format($updatedInvoice->Disc_Ongkir, 0, ',', '.'),
                    'voucher' => number_format($updatedInvoice->Voucher, 0, ',', '.'),
                    'asuransi_pengiriman' => number_format($updatedInvoice->Asuransi_Pengiriman, 0, ',', '.'),
                    'biaya_layanan' => number_format($updatedInvoice->Biaya_Layanan, 0, ',', '.'),
                    'grand_total' => number_format($updatedInvoice->Grand_Total, 0, ',', '.'),
                    'payment' => $updatedInvoice->Payment == 0 ? 'Cash' : ($updatedInvoice->Payment == 1 ? 'Tunda' : '-'),
                    'keterangan' => $updatedInvoice->Keterangan ?? '-',
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update: ' . $e->getMessage()], 500);
        }
    }

    public function getHistory(Request $request)
    {
        // Check if any filters are applied
        $hasFilters = ($request->has('customer_id') && $request->customer_id != '') ||
                      ($request->has('month') && $request->month != '') ||
                      ($request->has('year') && $request->year != '') ||
                      ($request->has('keyword') && $request->keyword != '' && $request->has('keyword_value') && $request->keyword_value != '');

        $query = InvoiceEditHistory::query()
            ->orderBy('edited_at', 'desc');

        // If filters are applied, join with filtered invoice data via ProcessID
        if ($hasFilters) {
            // Build the same query as getData() to get filtered invoice data
            $invoiceQuery = DB::connection('mysql_invoice')
                ->table('dt_invoice')
                ->select('dt_invoice.ProcessID');

            // Apply the same filters as invoice data
            if ($request->has('customer_id') && $request->customer_id != '') {
                $customer = Customer::find($request->customer_id);
                if ($customer) {
                    $invoiceQuery->where('dt_invoice.Chat_Id', $customer->telegram_chat_id);
                }
            }

            if ($request->has('month') && $request->month != '') {
                $invoiceQuery->whereMonth('dt_invoice.Tanggal_Input', $request->month);
            }

            if ($request->has('year') && $request->year != '') {
                $invoiceQuery->whereYear('dt_invoice.Tanggal_Input', $request->year);
            }

            // Apply keyword filter
            if ($request->has('keyword') && $request->keyword != '' && $request->has('keyword_value') && $request->keyword_value != '') {
                $keyword = $request->keyword;
                $value = $request->keyword_value;

                // Map keyword to database column
                $columnMap = [
                    'process_id' => 'ProcessID',
                    'nomor_invoice' => 'Nomor_Invoice',
                    'tanggal_struk' => 'Tanggal_Struk',
                    'supplier' => 'Toko',
                    'items' => 'Items',
                    'jumlah' => 'Jumlah',
                    'satuan' => 'Satuan',
                    'harga_satuan' => 'Harga_Satuan',
                    'payment' => 'Payment',
                    'keterangan' => 'Keterangan',
                ];

                if (isset($columnMap[$keyword])) {
                    $column = $columnMap[$keyword];
                    
                    // For numeric fields, use exact match
                    if (in_array($keyword, ['jumlah', 'harga_satuan', 'payment'])) {
                        $invoiceQuery->where('dt_invoice.' . $column, $value);
                    } else {
                        // For text fields, use LIKE
                        $invoiceQuery->where('dt_invoice.' . $column, 'like', '%' . $value . '%');
                    }
                }
            }

            // Get unique ProcessIDs from filtered invoice data
            $processIds = $invoiceQuery->distinct()
                ->pluck('ProcessID')
                ->filter() // Remove null values
                ->toArray();

            // Filter history by matching process_id
            if (!empty($processIds)) {
                $query->whereIn('process_id', $processIds);
            } else {
                // If no matching ProcessIDs found, return empty result
                $query->whereRaw('1 = 0');
            }
        }
        // If no filters applied, show all history (no additional filtering)

        // Get total records
        $totalRecords = $query->count();

        // Handle pagination
        $perPage = $request->get('per_page', 25);
        
        if ($perPage === 'all') {
            $data = $query->get();
        } else {
            $data = $query->paginate((int)$perPage);
        }

        // Format data
        if ($perPage === 'all') {
            $formattedData = $data->map(function ($item, $index) {
                return $this->formatHistoryData($item, $index + 1);
            });

            return response()->json([
                'data' => $formattedData,
                'total' => $totalRecords,
                'per_page' => 'all',
                'current_page' => 1,
                'last_page' => 1,
            ]);
        } else {
            $formattedData = $data->map(function ($item, $index) use ($data) {
                return $this->formatHistoryData($item, ($data->currentPage() - 1) * $data->perPage() + $index + 1);
            });

            return response()->json([
                'data' => $formattedData,
                'total' => $data->total(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
            ]);
        }
    }

    private function formatInvoiceData($item, $index, $customers)
    {
        return [
            'no' => $index,
            'id' => $item->id,
            'process_id' => $item->ProcessID,
            'customer_name' => $customers[$item->Chat_Id] ?? 'N/A',
            'chat_id' => $item->Chat_Id,
            'tanggal_input' => $item->Tanggal_Input ? date('d-m-Y', strtotime($item->Tanggal_Input)) : '-',
            'waktu_input' => $item->Waktu_Input ? date('H:i:s', strtotime($item->Waktu_Input)) : '-',
            'nomor_invoice' => $item->Nomor_Invoice ?? '-',
            'tanggal_struk' => $item->Tanggal_Struk ? date('d-m-Y', strtotime($item->Tanggal_Struk)) : '-',
            'toko' => $item->Toko ?? '-',
            'items' => $item->Items ?? '-',
            'jumlah' => number_format($item->Jumlah, 2),
            'jumlah_raw' => $item->Jumlah,
            'satuan' => $item->Satuan ?? '-',
            'harga_satuan' => number_format($item->Harga_Satuan, 2, ',', '.'),
            'harga_satuan_raw' => $item->Harga_Satuan,
            'sub_total' => number_format($item->Total_Per_Item, 2, ',', '.'),
            'discount' => number_format($item->Discount, 0, ',', '.'),
            'discount_raw' => $item->Discount,
            'sub_after_discount' => number_format($item->Sub_After_Discount, 0, ',', '.'),
            'refunds' => number_format($item->Refunds, 0, ',', '.'),
            'refunds_raw' => $item->Refunds,
            'pajak' => number_format($item->Pajak, 0, ',', '.'),
            'pajak_raw' => $item->Pajak,
            'ongkir' => number_format($item->Ongkir, 0, ',', '.'),
            'ongkir_raw' => $item->Ongkir,
            'disc_ongkir' => number_format($item->Disc_Ongkir, 0, ',', '.'),
            'disc_ongkir_raw' => $item->Disc_Ongkir,
            'voucher' => number_format($item->Voucher, 0, ',', '.'),
            'voucher_raw' => $item->Voucher,
            'asuransi_pengiriman' => number_format($item->Asuransi_Pengiriman, 0, ',', '.'),
            'asuransi_pengiriman_raw' => $item->Asuransi_Pengiriman,
            'biaya_layanan' => number_format($item->Biaya_Layanan, 0, ',', '.'),
            'biaya_layanan_raw' => $item->Biaya_Layanan,
            'grand_total' => number_format($item->Grand_Total, 0, ',', '.'),
            'input_by' => $item->Input_By ?? '-',
            'file_link' => $item->File_Link ?? '-',
            'payment' => $item->Payment == 0 ? 'Cash' : ($item->Payment == 1 ? 'Tunda' : '-'),
            'payment_raw' => $item->Payment,
            'keterangan' => $item->Keterangan ?? '-',
            'sync_status' => $item->Sync_status == 1 ? '<span style="color: green;">Synced</span>' : ($item->Sync_status ?? '-'),
            'sync_time' => $item->Sync_time ? date('d-m-Y H:i:s', strtotime($item->Sync_time)) : '-',
            'checked' => $item->checked ? 'Yes' : 'No',
        ];
    }

    private function formatHistoryData($item, $index)
    {
        // Get customer name from telegram_chat_id stored in history
        $customerName = 'N/A';
        if ($item->telegram_chat_id) {
            $customer = Customer::where('telegram_chat_id', $item->telegram_chat_id)->first();
            if ($customer) {
                $customerName = $customer->name;
            }
        }

        // Get process_id from history table directly
        $processId = $item->process_id ?? '-';

        return [
            'no' => $index,
            'id' => $item->id,
            'process_id' => $processId,
            'invoice_number' => $item->invoice_number,
            'customer_name' => $customerName,
            'field_name' => $item->field_name,
            'old_value' => $item->old_value ?? '-',
            'new_value' => $item->new_value ?? '-',
            'old_total' => 'Rp ' . number_format($item->old_total, 2, ',', '.'),
            'new_total' => 'Rp ' . number_format($item->new_total, 2, ',', '.'),
            'editor_name' => $item->editor_name,
            'edited_at' => $item->edited_at->format('d-m-Y H:i:s'),
            'ip_address' => $item->ip_address ?? '-',
        ];
    }
}
