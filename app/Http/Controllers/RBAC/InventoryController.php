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
                ->editColumn('updated_at', function ($row) {
                    return $row->updated_at ? $row->updated_at->format('Y-m-d H:i:s') : '';
                })
                ->make(true);
        }

        return view('rbac.inventory.index');
    }
}
