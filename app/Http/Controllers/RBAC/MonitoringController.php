<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\DtQueue;
use App\Models\DtLogFailed;
use Illuminate\Support\Facades\DB;

class MonitoringController extends Controller
{
    public function index()
    {
        $dtQueueData = DtQueue::select(
                'Chat_Id',
                DB::raw('COUNT(CASE WHEN Task_type = "get_report" THEN 1 END) as total_task_report'),
                DB::raw('COUNT(CASE WHEN Task_type = "invoice_ocr" THEN 1 END) as total_task_ocr'),
                DB::raw('COUNT(CASE WHEN Task_type = "manual_input" THEN 1 END) as total_task_manual'),
                DB::raw('COUNT(CASE WHEN Status = "Finish" THEN 1 END) as total_finish'),
                DB::raw('COUNT(CASE WHEN Status = "Processing" THEN 1 END) as total_processing'),
                DB::raw('0 as total_failed'),
                DB::raw('MAX(Created_at) as last_data_input')
            )
            ->groupBy('Chat_Id')
            ->get();

        // Get failed counts separately
        $failedCounts = DtLogFailed::select('Chat_Id', DB::raw('COUNT(*) as total_failed'))
            ->whereIn('Status', ['Failed', 'Duplicated'])
            ->groupBy('Chat_Id')
            ->pluck('total_failed', 'Chat_Id');

        // Merge failed counts
        $dtQueueData = $dtQueueData->map(function ($item) use ($failedCounts) {
            $item->total_failed = $failedCounts->get($item->Chat_Id, 0);
            return $item;
        });

        $customers = Customer::all()->keyBy('telegram_chat_id');

        $data = $dtQueueData->map(function ($item) use ($customers) {
            $customer = $customers->get($item->Chat_Id);
            if ($customer) {
                $item->customer_name = $customer->name;
                return $item;
            }
            return null;
        })->filter();

        return view('rbac.monitoring.index', compact('data'));
    }

    public function getProcessingTasks(Request $request)
    {
        $customerName = $request->input('customer_name');

        $customer = Customer::where('name', $customerName)->first();

        if (!$customer) {
            return response()->json([]);
        }

        $processingTasks = DtQueue::with('image')
            ->where('Chat_Id', $customer->telegram_chat_id)
            ->where('Status', 'Processing')
            ->get();

        return response()->json($processingTasks);
    }

    public function getFailedTasks(Request $request)
    {
        $customerName = $request->input('customer_name');

        $customer = Customer::where('name', $customerName)->first();

        if (!$customer) {
            return response()->json([]);
        }

        $failedTasks = DtLogFailed::with('image')
            ->where('Chat_Id', $customer->telegram_chat_id)
            ->get();

        return response()->json($failedTasks);
    }

    public function getChartData(Request $request)
    {
        $customerName = $request->input('customer_name');
        $timeFrame = $request->input('time_frame', 'day');

        $customer = Customer::where('name', $customerName)->first();

        if (!$customer) {
            return response()->json([]);
        }

        $data = DtQueue::where('Chat_Id', $customer->telegram_chat_id)
            ->select(
                DB::raw('COUNT(CASE WHEN Task_type = "get_report" THEN 1 END) as total_task_report'),
                DB::raw('COUNT(CASE WHEN Task_type = "invoice_ocr" THEN 1 END) as total_task_ocr'),
                DB::raw('COUNT(CASE WHEN Task_type = "manual_input" THEN 1 END) as total_task_manual'),
                DB::raw('COUNT(CASE WHEN Status = "Finish" THEN 1 END) as total_finish'),
                DB::raw('COUNT(CASE WHEN Status = "Processing" THEN 1 END) as total_processing')
            )
            ->where(function ($query) use ($timeFrame) {
                switch ($timeFrame) {
                    case 'hour':
                        $query->where('Created_at', '>=', now()->subHour());
                        break;
                    case 'day':
                        $query->where('Created_at', '>=', now()->subDay());
                        break;
                    case 'week':
                        $query->where('Created_at', '>=', now()->subWeek());
                        break;
                    case 'month':
                        $query->where('Created_at', '>=', now()->subMonth());
                        break;
                }
            })
            ->first();

        // Get failed count separately
        $failedCount = DtLogFailed::where('Chat_Id', $customer->telegram_chat_id)
            ->whereIn('Status', ['Failed', 'Duplicated'])
            ->where(function ($query) use ($timeFrame) {
                switch ($timeFrame) {
                    case 'hour':
                        $query->where('Finish_At', '>=', now()->subHour());
                        break;
                    case 'day':
                        $query->where('Finish_At', '>=', now()->subDay());
                        break;
                    case 'week':
                        $query->where('Finish_At', '>=', now()->subWeek());
                        break;
                    case 'month':
                        $query->where('Finish_At', '>=', now()->subMonth());
                        break;
                }
            })
            ->count();

        $data->total_failed = $failedCount;

        return response()->json($data);
    }

    public function getDashboardChartData()
    {
        $startTime = now()->subDay();

        // Get all unique Chat_Id from dt_queue in last 24 hours
        $chatIds = DtQueue::where('Created_at', '>=', $startTime)
            ->distinct()
            ->pluck('Chat_Id')
            ->toArray();

        // Get customers that have these Chat_Ids
        $customers = Customer::whereIn('telegram_chat_id', $chatIds)->get();

        $labels = [];
        for ($i = 23; $i >= 0; $i--) {
            $labels[] = now()->subHours($i)->format('H:00');
        }

        $datasets = [];

        foreach ($customers as $customer) {
            $data = [];
            for ($i = 23; $i >= 0; $i--) {
                $hourStart = now()->subHours($i)->startOfHour();
                $hourEnd = now()->subHours($i)->copy()->endOfHour();

                $count = DtQueue::where('Chat_Id', $customer->telegram_chat_id)
                    ->whereBetween('Created_at', [$hourStart, $hourEnd])
                    ->count();

                $data[] = $count;
            }

            $datasets[] = [
                'label' => $customer->name,
                'data' => $data,
                'borderColor' => 'rgba(' . rand(0, 255) . ',' . rand(0, 255) . ',' . rand(0, 255) . ',1)',
                'backgroundColor' => 'rgba(' . rand(0, 255) . ',' . rand(0, 255) . ',' . rand(0, 255) . ',0.1)',
                'fill' => false,
            ];
        }

        return response()->json([
            'labels' => $labels,
            'datasets' => $datasets,
        ]);
    }
}
