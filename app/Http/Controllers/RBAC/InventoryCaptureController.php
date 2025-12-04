<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\InventoryCapture;
use App\Models\Inventory;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class InventoryCaptureController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = InventoryCapture::with('masterItem')->select('inventory_captures.*');

            $currentCustomerId = \App\Services\TenantService::currentCustomerId();
            if ($currentCustomerId) {
                $query->whereHas('masterItem.tenantOwner', function ($q) use ($currentCustomerId) {
                    $q->where('customer_id', $currentCustomerId);
                });
            }

            if ($request->filled('month')) {
                $query->whereMonth('captured_at', $request->month);
            }

            if ($request->filled('year')) {
                $query->whereYear('captured_at', $request->year);
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
                ->editColumn('captured_at', function ($row) {
                    return $row->captured_at ? $row->captured_at->format('Y-m-d') : '';
                })
                ->make(true);
        }

        return view('rbac.inventory.capture.index');
    }

    public function store(Request $request)
    {
        try {
            DB::transaction(function () {
                $query = Inventory::with('masterItem');

                $currentCustomerId = \App\Services\TenantService::currentCustomerId();
                if ($currentCustomerId) {
                    $query->whereHas('masterItem.tenantOwner', function ($q) use ($currentCustomerId) {
                        $q->where('customer_id', $currentCustomerId);
                    });
                }

                $inventories = $query->get();
                $capturedAt = now();

                foreach ($inventories as $inventory) {
                    InventoryCapture::create([
                        'master_item_id' => $inventory->master_item_id,
                        'quantity' => $inventory->quantity,
                        'captured_at' => $capturedAt,
                    ]);
                }
            });

            return response()->json(['success' => 'Stock captured successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to capture stock: ' . $e->getMessage()], 500);
        }
    }
}
