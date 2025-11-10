<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\PosQueue;
use Illuminate\Support\Facades\DB;

class PosMonitoringController extends Controller
{
    public function index()
    {
        $customers = Customer::where('pos_status', 'ready')->get();

        // Get POS queue data with customer names, ordered by scheduled first, then by created_at
        $posQueueData = PosQueue::with('customer')
            ->select(
                'pos_queue.*',
                'customers.name as customer_name'
            )
            ->join('customers', 'pos_queue.customer_id', '=', 'customers.id')
            ->orderBy('pos_queue.is_scheduled', 'desc')
            ->orderBy('pos_queue.created_at', 'desc')
            ->get();

        return view('rbac.pos-monitoring.index', compact('customers', 'posQueueData'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'schedule_type' => 'required|in:daily',
            'schedule_time' => 'required|date_format:H:i',
        ]);

        $customer = Customer::find($request->customer_id);

        // Check if a scheduled queue already exists for this customer
        $existingScheduled = PosQueue::where('customer_id', $request->customer_id)
            ->where('is_scheduled', true)
            ->first();

        if ($existingScheduled) {
            return response()->json(['error' => 'A scheduled POS sync already exists for this customer.'], 422);
        }

        PosQueue::create([
            'customer_id' => $request->customer_id,
            'start_date' => '', // Will be set when scheduled run executes
            'end_date' => '', // Will be set when scheduled run executes
            'telegram_chat_id' => $customer->telegram_chat_id,
            'status' => 'scheduled',
            'is_scheduled' => true,
            'schedule_time' => $request->schedule_time,
        ]);

        return response()->json(['success' => 'POS sync schedule has been created successfully.']);
    }

    public function report($posQueueId)
    {
        // Get POS queue data with customer information
        $posQueue = PosQueue::with('customer')->findOrFail($posQueueId);

        // Convert epoch timestamps back to dd-mm-yyyy format for querying
        $startDateFormatted = date('d-m-Y', (int)$posQueue->start_date);
        $endDateFormatted = date('d-m-Y', (int)$posQueue->end_date);

        // Get POS data from dt_pos table based on telegram_chat_id and date range
        $posData = \App\Models\DtPos::where('telegram_chat_id', $posQueue->telegram_chat_id)
            ->whereBetween('date', [$startDateFormatted, $endDateFormatted])
            ->orderBy('id', 'desc')
            ->get();

        // Calculate summary statistics with distinct transactions
        $summary = [
            'total_transactions' => $posData->unique('receipt_number')->count(),
            'total_gross_sales' => $posData->sum('gross_sales'),
            'total_discounts' => $posData->sum('discounts'),
            'total_refunds' => $posData->sum('refunds'),
            'total_net_sales' => $posData->sum('net_sales'),
            'total_tax' => $posData->sum('tax'),
            'total_gratuity' => $posData->sum('gratuity'),
        ];

        return view('rbac.pos-monitoring.report', compact('posQueue', 'posData', 'summary'));
    }

    public function syncData(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:pos_queue,id',
        ]);

        $posQueue = PosQueue::find($request->id);

        // Update status to pending
        $posQueue->update(['status' => 'pending']);

        return response()->json(['success' => 'POS sync data status has been reset to pending.']);
    }

    public function getPosData(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:pos_queue,id',
        ]);

        $posQueue = PosQueue::findOrFail($request->id);
        $customerId = $posQueue->customer_id;

        // Get all manual sync records (is_scheduled = 0) for this customer
        $manualQueues = PosQueue::with('customer')
            ->where('customer_id', $customerId)
            ->where('is_scheduled', false)
            ->select(
                'pos_queue.*',
                'customers.name as customer_name'
            )
            ->join('customers', 'pos_queue.customer_id', '=', 'customers.id')
            ->orderBy('pos_queue.created_at', 'desc')
            ->get();

        $formattedData = $manualQueues->map(function ($queue, $index) {
            // Convert epoch timestamps to DD-MM-YYYY format
            $startDate = date('d-m-Y', (int)$queue->start_date);
            $endDate = date('d-m-Y', (int)$queue->end_date);

            return [
                'no' => $index + 1,
                'customer_name' => $queue->customer_name,
                'start_datetime' => $startDate . ' 00:00',
                'end_datetime' => $endDate . ' 23:59',
                'status' => ucfirst($queue->status),
                'report_url' => route('rbac.pos-monitoring.report', $queue->id),
                'id' => $queue->id
            ];
        });

        return response()->json(['data' => $formattedData]);
    }

    public function deleteQueue(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:pos_queue,id',
        ]);

        $posQueue = PosQueue::find($request->id);
        $customerName = $posQueue->customer->name;

        $posQueue->delete();

        return response()->json(['success' => 'POS queue entry for ' . $customerName . ' has been deleted successfully.']);
    }

    public function getReportData(Request $request, $posQueueId)
    {
        // Get POS queue data with customer information
        $posQueue = PosQueue::with('customer')->findOrFail($posQueueId);

        // Convert epoch timestamps back to dd-mm-yyyy format for querying
        $startDateFormatted = date('d-m-Y', (int)$posQueue->start_date);
        $endDateFormatted = date('d-m-Y', (int)$posQueue->end_date);

        // Base query
        $query = \App\Models\DtPos::where('telegram_chat_id', $posQueue->telegram_chat_id)
            ->whereBetween('date', [$startDateFormatted, $endDateFormatted])
            ->orderBy('id', 'desc');

        // Get total records before pagination
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
            $formattedData = $data->map(function ($transaction, $index) {
                return $this->formatPosData($transaction, $index + 1);
            });

            return response()->json([
                'data' => $formattedData,
                'total' => $totalRecords,
                'per_page' => 'all',
                'current_page' => 1,
                'last_page' => 1,
            ]);
        } else {
            $formattedData = $data->map(function ($transaction, $index) use ($data) {
                return $this->formatPosData($transaction, ($data->currentPage() - 1) * $data->perPage() + $index + 1);
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

    private function formatPosData($transaction, $index)
    {
        return [
            $index,
            $transaction->receipt_number ?? '-',
            $transaction->date ?? '-',
            $transaction->time ?? '-',
            $transaction->category ?? '-',
            $transaction->brand ?? '-',
            $transaction->items ?? '-',
            $transaction->variant ?? '-',
            $transaction->sku ?? '-',
            $transaction->quantity ?? '-',
            $transaction->modifier_applied ?? '-',
            $transaction->discount_applied ?? '-',
            'Rp ' . number_format($transaction->gross_sales ?? 0, 2),
            'Rp ' . number_format($transaction->discounts ?? 0, 2),
            'Rp ' . number_format($transaction->refunds ?? 0, 2),
            'Rp ' . number_format($transaction->net_sales ?? 0, 2),
            'Rp ' . number_format($transaction->gratuity ?? 0, 2),
            'Rp ' . number_format($transaction->tax ?? 0, 2),
            $transaction->sales_type ?? '-',
            $transaction->collected_by ?? '-',
            $transaction->served_by ?? '-',
            $transaction->customer ?? '-',
            $transaction->payment_method ?? '-',
            $transaction->event_type ?? '-',
            $transaction->reason_of_refund ?? '-',
        ];
    }
}
