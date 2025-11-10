<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        // Get filter options
        $users = \App\Models\User::with('userDetail')
            ->whereHas('userDetail')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->userDetail->employee_name ?? $user->name
                ];
            });

        $totalLogs = ActivityLog::count();

        return view('rbac.history.index', compact('users', 'totalLogs'));
    }

    public function data(Request $request)
    {
        try {
            $query = ActivityLog::with('user.userDetail')
                ->when($request->start_date, function ($q) use ($request) {
                    return $q->whereDate('created_at', '>=', $request->start_date);
                })
                ->when($request->end_date, function ($q) use ($request) {
                    return $q->whereDate('created_at', '<=', $request->end_date);
                })
                ->when($request->user_filter, function ($q) use ($request) {
                    return $q->where('user_id', $request->user_filter);
                })
                ->when($request->action_filter, function ($q) use ($request) {
                    return $q->where('action', $request->action_filter);
                })
                ->when($request->table_filter, function ($q) use ($request) {
                    return $q->where('table_name', $request->table_filter);
                })
                ->orderBy('created_at', 'desc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('user_name', function ($row) {
                    // Debug: Handle missing userDetail gracefully
                    if (!$row->user) {
                        return 'Unknown User';
                    }
                    return $row->user->userDetail->employee_name ?? $row->user->name ?? 'Unknown User';
                })
                ->addColumn('action_badge', function ($row) {
                    return $row->action_badge;
                })
                ->addColumn('table_name_formatted', function ($row) {
                    return $row->table_name_formatted;
                })
                ->addColumn('changes', function ($row) {
                    $changes = [];

                    if ($row->action === 'create' && $row->new_values) {
                        $changes[] = '<strong>Created:</strong> ' . $this->formatValues($row->new_values);
                    } elseif ($row->action === 'update' && ($row->old_values || $row->new_values)) {
                        $changes[] = '<strong>From:</strong> ' . $this->formatValues($row->old_values);
                        $changes[] = '<strong>To:</strong> ' . $this->formatValues($row->new_values);
                    } elseif ($row->action === 'delete' && $row->old_values) {
                        $changes[] = '<strong>Deleted:</strong> ' . $this->formatValues($row->old_values);
                    }

                    return implode('<br>', $changes);
                })
                ->addColumn('timestamp', function ($row) {
                    return $row->created_at->format('Y-m-d H:i:s');
                })
                ->rawColumns(['action_badge', 'changes'])
                ->make(true);
        } catch (\Exception $e) {
            // Return error response for debugging
            return response()->json([
                'error' => 'DataTable error: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    private function formatValues($values)
    {
        if (!$values) return '-';

        $formatted = [];
        foreach ($values as $key => $value) {
            $formattedKey = ucwords(str_replace(['_', 'id'], [' ', ''], $key));
            
            // Check if the value is a date string and format it
            if (is_string($value) && preg_match('/^\\d{4}-\\d{2}-\\d{2}T\\d{2}:\\d{2}:\\d{2}\\.\\d{6}Z$/', $value)) {
                $formattedValue = \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s');
            } else {
                $formattedValue = is_array($value) ? json_encode($value) : $value;
            }

            $formatted[] = "$formattedKey: $formattedValue";
        }

        return implode(', ', $formatted);
    }
}
