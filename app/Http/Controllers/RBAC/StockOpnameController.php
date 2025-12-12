<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use App\Models\InventoryCapture;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Services\TenantService;
use Illuminate\Support\Facades\Auth;

class StockOpnameController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = InventoryCapture::with('masterItem')->select('inventory_captures.*');

            $currentCustomerId = TenantService::currentCustomerId();
            if ($currentCustomerId) {
                $query->whereHas('masterItem.tenantOwner', function ($q) use ($currentCustomerId) {
                    $q->where('customer_id', $currentCustomerId);
                });
            }

            // By default show latest unadjusted, or filter by date
            if ($request->filled('month') && $request->filled('year')) {
                $query->whereMonth('captured_at', $request->month)
                    ->whereYear('captured_at', $request->year);
            }

            return DataTables::of($query)
                ->addIndexColumn()
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
                ->addColumn('item_name', function ($row) {
                    return $row->masterItem ? $row->masterItem->item_name : 'N/A';
                })
                ->addColumn('item_code', function ($row) {
                    return $row->masterItem ? $row->masterItem->item_code : 'N/A';
                })
                ->addColumn('system_qty', function ($row) {
                    return $row->quantity;
                })
                ->editColumn('captured_at', function ($row) {
                    return $row->captured_at ? $row->captured_at->format('Y-m-d H:i:s') : '';
                })
                ->addColumn('date_so', function ($row) {
                    return $row->updated_at ? $row->updated_at->format('Y-m-d H:i:s') : '-';
                })
                ->addColumn('physical_qty', function ($row) {
                    // Input field for physical quantity
                    return '<input type="number" class="form-control quantity-input" data-id="' . $row->id . '" value="' . ($row->physical_quantity ?? $row->quantity) . '">';
                })
                ->addColumn('variance', function ($row) {
                    $phy = $row->physical_quantity ?? $row->quantity;
                    $diff = $phy - $row->quantity;
                    $color = $diff == 0 ? 'text-success' : 'text-danger';
                    return '<span class="' . $color . ' font-weight-bold">' . $diff . '</span>';
                })
                ->editColumn('notes', function ($row) {
                    return '<input type="text" class="form-control notes-input" data-id="' . $row->id . '" value="' . ($row->notes ?? '') . '">';
                })
                ->addColumn('status', function ($row) {
                    return $row->is_adjusted ? '<span class="badge badge-success">Adjusted</span>' : '<span class="badge badge-warning">Pending</span>';
                })
                ->addColumn('history', function ($row) {
                    return '<button class="btn btn-sm btn-info btn-history" data-id="' . $row->id . '"><i class="fas fa-history"></i></button>';
                })
                ->rawColumns(['physical_qty', 'variance', 'notes', 'status', 'history'])
                ->make(true);
        }

        return view('rbac.stock-opname.index');
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:inventory_captures,id',
            'field' => 'required|in:physical_quantity,notes',
            'value' => 'nullable'
        ]);

        $capture = InventoryCapture::findOrFail($request->id);

        // Security Check
        $currentCustomerId = TenantService::currentCustomerId();
        if ($currentCustomerId) {
            // Verify ownership
            if (!$capture->masterItem || !$capture->masterItem->tenantOwner || $capture->masterItem->tenantOwner->customer_id != $currentCustomerId) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        if ($capture->is_adjusted) {
            return response()->json(['error' => 'Cannot update adjusted record'], 400);
        }

        $oldValue = $request->field == 'physical_quantity' ? $capture->physical_quantity : $capture->notes;

        if ($request->field == 'physical_quantity') {
            $capture->physical_quantity = $request->value;
        } else {
            $capture->notes = $request->value;
        }

        $capture->processed_by = Auth::id();
        $capture->save();

        // Log Activity
        \App\Services\ActivityLogService::log(
            'update',
            'inventory_captures',
            $capture->id,
            [$request->field => $oldValue],
            [$request->field => $request->value]
        );

        return response()->json(['success' => true]);
    }

    public function history($id)
    {
        $logs = \App\Models\ActivityLog::where('table_name', 'inventory_captures')
            ->where('record_id', $id)
            ->with('user')
            ->latest()
            ->get()
            ->map(function ($log) {
                return [
                    'date' => $log->created_at->format('Y-m-d H:i:s'),
                    'user' => $log->user ? $log->user->name : 'System',
                    'changes' => $log->new_values ?? [],
                    'old' => $log->old_values ?? [],
                ];
            });

        return response()->json($logs);
    }
}
