<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Inventory;
use Yajra\DataTables\Facades\DataTables;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Inventory::with('masterItem')->select('inventories.*');

            $currentCustomerId = \App\Services\TenantService::currentCustomerId();
            if ($currentCustomerId) {
                $query->whereHas('masterItem.tenantOwner', function ($q) use ($currentCustomerId) {
                    $q->where('customer_id', $currentCustomerId);
                });
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('item_name', function ($row) {
                    return $row->masterItem ? $row->masterItem->item_name : 'N/A';
                })
                ->addColumn('item_code', function ($row) {
                    return $row->masterItem ? $row->masterItem->item_code : 'N/A';
                })
                ->filterColumn('item_name', function ($query, $keyword) {
                    $query->whereHas('masterItem', function ($q) use ($keyword) {
                        $q->where('item_name', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('item_code', function ($query, $keyword) {
                    $query->whereHas('masterItem', function ($q) use ($keyword) {
                        $q->where('item_code', 'like', "%{$keyword}%");
                    });
                })
                ->editColumn('quantity', function ($row) {
                    return $row->quantity . ' ' . ($row->masterItem ? $row->masterItem->unit : '');
                })
                ->addColumn('min_stock', function ($row) {
                    return $row->masterItem ? $row->masterItem->min_stock : 0;
                })
                ->editColumn('updated_at', function ($row) {
                    return $row->updated_at ? $row->updated_at->format('Y-m-d H:i:s') : '';
                })
                ->addColumn('history', function ($row) {
                    return '<button class="btn btn-sm btn-info btn-history" data-id="' . $row->id . '"><i class="fas fa-history"></i></button>';
                })
                ->rawColumns(['history'])
                ->make(true);
        }

        return view('rbac.inventory.index');
    }

    public function history($id)
    {
        $logs = \App\Models\ActivityLog::where('table_name', 'inventories')
            ->where('record_id', $id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get()
            ->map(function ($log) {
                $reason = $log->new_values['reason'] ?? '-';
                return [
                    'date' => $log->created_at->format('Y-m-d H:i:s'),
                    'user' => $log->user ? $log->user->name : 'System',
                    'reason' => $reason,
                    'old_qty' => $log->old_values['quantity'] ?? 0,
                    'new_qty' => $log->new_values['quantity'] ?? 0
                ];
            });

        return response()->json($logs);
    }
}
