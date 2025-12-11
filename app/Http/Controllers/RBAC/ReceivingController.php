<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use App\Models\Receiving;
use App\Models\MasterItem;
use Illuminate\Http\Request;
use App\Services\ActivityLogService;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class ReceivingController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Receiving::with(['masterItem', 'receiver', 'ngOperator'])->select('receivings.*');

            $currentCustomerId = \App\Services\TenantService::currentCustomerId();
            if ($currentCustomerId) {
                $query->whereHas('masterItem.tenantOwner', function ($q) use ($currentCustomerId) {
                    $q->where('customer_id', $currentCustomerId);
                });
            }

            if ($request->filled('incoming_date')) {
                $dates = explode(' - ', $request->incoming_date);
                if (count($dates) == 2) {
                    $query->whereBetween('incoming_date', [$dates[0] . ' 00:00:00', $dates[1] . ' 23:59:59']);
                }
            }

            if ($request->filled('status')) {
                $query->where('product_status', $request->status);
            }

            if ($request->filled('ng_status')) {
                $query->where('ng_customer', $request->ng_status);
            }

            if ($request->filled('delivery_date')) {
                $query->whereDate('delivery_date_customer', $request->delivery_date);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('item_name', function ($row) {
                    return $row->masterItem ? $row->masterItem->item_name : 'N/A';
                })
                ->addColumn('receiver_name', function ($row) {
                    return $row->receiver ? $row->receiver->name : 'N/A';
                })
                ->editColumn('incoming_date', function ($row) {
                    return $row->incoming_date ? $row->incoming_date->format('d-m-Y H:i:s') : '';
                })
                ->editColumn('delivery_date_customer', function ($row) {
                    return $row->delivery_date_customer ? $row->delivery_date_customer->format('d-m-Y') : '';
                })
                ->filterColumn('item_name', function ($query, $keyword) {
                    $query->whereHas('masterItem', function ($q) use ($keyword) {
                        $q->where('item_name', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('receiver_name', function ($query, $keyword) {
                    $query->whereHas('receiver', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->make(true);
        }

        $currentCustomerId = \App\Services\TenantService::currentCustomerId();
        if ($currentCustomerId) {
            $masterItems = MasterItem::whereHas('tenantOwner', function ($q) use ($currentCustomerId) {
                $q->where('customer_id', $currentCustomerId);
            })->get();
        } else {
            $masterItems = MasterItem::all();
        }

        return view('rbac.receiving.index', compact('masterItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'master_item_id' => 'required|exists:master_items,id',
            'doc_number_internal' => 'nullable|string|max:255',
            'qrcode_customer' => 'nullable|string|max:255',
            'doc_number_customer' => 'nullable|string|max:255',
            'product_status' => 'required|in:Waiting,Verified',
            'delivery_date_customer' => 'nullable|date',
            'qty_pack' => 'required|numeric',
            'qty_per_pack' => 'required|numeric',
            'delivery_by' => 'nullable|string|max:255',
            'ng_customer' => 'required|in:OK,NG',
        ]);

        // RBAC Check
        $currentCustomerId = \App\Services\TenantService::currentCustomerId();
        if ($currentCustomerId) {
            $masterItem = MasterItem::findOrFail($request->master_item_id);
            if ($masterItem->tenantOwner->customer_id !== $currentCustomerId) {
                return response()->json(['error' => 'Unauthorized: Item does not belong to your tenant.'], 403);
            }
        }

        $data = $request->all();
        $data['incoming_date'] = now();
        $data['receive_by'] = Auth::id();

        if ($request->ng_customer === 'NG') {
            $data['ng_operator'] = Auth::id();
        }

        $receiving = Receiving::create($data);

        // Log activity
        ActivityLogService::logCreate('receivings', $receiving->id, [
            'master_item_id' => $request->master_item_id,
            'doc_number_internal' => $request->doc_number_internal,
            'doc_number_customer' => $request->doc_number_customer,
            'qty_pack' => $request->qty_pack,
            'qty_total' => $request->qty_pack * $request->qty_per_pack,
            'status' => $request->product_status,
        ]);

        return response()->json(['success' => 'Receiving record created successfully.']);
    }

    public function edit($id)
    {
        $receiving = Receiving::with('masterItem.tenantOwner')->findOrFail($id);

        // RBAC Check
        $currentCustomerId = \App\Services\TenantService::currentCustomerId();
        if ($currentCustomerId) {
            if ($receiving->masterItem->tenantOwner->customer_id !== $currentCustomerId) {
                abort(403);
            }
        }

        if (request()->ajax()) {
            return response()->json($receiving);
        }
        return abort(404);
    }

    public function update(Request $request, $id)
    {
        $receiving = Receiving::with('masterItem.tenantOwner')->findOrFail($id);

        // RBAC Check
        $currentCustomerId = \App\Services\TenantService::currentCustomerId();
        if ($currentCustomerId) {
            if ($receiving->masterItem->tenantOwner->customer_id !== $currentCustomerId) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        $request->validate([
            'master_item_id' => 'required|exists:master_items,id',
            'doc_number_internal' => 'nullable|string|max:255',
            'qrcode_customer' => 'nullable|string|max:255',
            'doc_number_customer' => 'nullable|string|max:255',
            'product_status' => 'required|in:Waiting,Verified',
            'delivery_date_customer' => 'nullable|date',
            'qty_pack' => 'required|numeric',
            'qty_per_pack' => 'required|numeric',
            'delivery_by' => 'nullable|string|max:255',
            'ng_customer' => 'required|in:OK,NG',
        ]);

        // Additional check if master_item_id changed
        if ($currentCustomerId && $request->master_item_id != $receiving->master_item_id) {
            $newMasterItem = MasterItem::findOrFail($request->master_item_id);
            if ($newMasterItem->tenantOwner->customer_id !== $currentCustomerId) {
                return response()->json(['error' => 'Unauthorized: New item does not belong to your tenant.'], 403);
            }
        }

        $data = $request->all();

        if ($request->ng_customer === 'NG') {
            $data['ng_operator'] = Auth::id();
        } else {
            $data['ng_operator'] = null; // Clear if OK
        }

        $oldValues = $receiving->toArray();
        $receiving->update($data);

        // Log activity
        $newValues = $receiving->toArray();
        ActivityLogService::logUpdate('receivings', $receiving->id, $oldValues, $newValues);

        return response()->json(['success' => 'Receiving record updated successfully.']);
    }

    public function destroy($id)
    {
        $receiving = Receiving::with('masterItem.tenantOwner')->findOrFail($id);

        // RBAC Check
        $currentCustomerId = \App\Services\TenantService::currentCustomerId();
        if ($currentCustomerId) {
            if ($receiving->masterItem->tenantOwner->customer_id !== $currentCustomerId) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        $oldValues = $receiving->toArray();
        $receiving->delete();

        // Log activity
        ActivityLogService::logDelete('receivings', $id, $oldValues);
        return response()->json(['success' => 'Receiving record deleted successfully.']);
    }
}
