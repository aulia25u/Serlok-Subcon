<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use App\Models\InventoryCapture;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Services\TenantService;
use App\Services\ActivityLogService;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = InventoryCapture::with(['masterItem', 'processor'])
                ->select('inventory_captures.*')
                ->where('is_adjusted', false)
                ->whereNotNull('physical_quantity')
                ->whereRaw('physical_quantity != quantity');

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
                ->editColumn('captured_at', function ($row) {
                    return $row->captured_at ? $row->captured_at->format('Y-m-d H:i:s') : '';
                })
                ->addColumn('processed_by', function ($row) {
                    return $row->processor ? $row->processor->name : '-';
                })
                ->editColumn('notes', function ($row) {
                    return $row->notes ?? '-';
                })
                ->addColumn('variance', function ($row) {
                    $diff = $row->physical_quantity - $row->quantity;
                    return '<span class="text-danger font-weight-bold">' . $diff . '</span>';
                })
                ->addColumn('action', function ($row) {
                    return '<button class="btn btn-sm btn-primary btn-approve" data-id="' . $row->id . '">Approve Adjustment</button>';
                })
                ->rawColumns(['variance', 'action'])
                ->make(true);
        }

        return view('rbac.stock-adjustment.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:inventory_captures,id',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $capture = InventoryCapture::findOrFail($request->id);

                if ($capture->is_adjusted) {
                    throw new \Exception("Already adjusted.");
                }

                $inventory = Inventory::where('master_item_id', $capture->master_item_id)->firstOrFail();
                $oldQty = $inventory->quantity;
                $newQty = $capture->physical_quantity;

                // Update Inventory
                $inventory->quantity = $newQty;
                $inventory->save();

                // Mark Capture Used
                $capture->is_adjusted = true;
                $capture->adjusted_at = now();
                $capture->save();

                // Log
                ActivityLogService::log(
                    'update',
                    'inventories',
                    $inventory->id,
                    ['quantity' => $oldQty],
                    ['quantity' => $newQty, 'reason' => 'Stock Opname Adjustment']
                );
            });

            return response()->json(['success' => 'Stock adjusted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
