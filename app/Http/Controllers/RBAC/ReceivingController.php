<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use App\Models\Receiving;
use App\Models\MasterItem;
use Illuminate\Http\Request;
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

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('incoming_date', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
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
                ->addColumn('action', function ($row) {
                    $btn = '<button data-id="' . $row->id . '" class="btn btn-primary btn-sm receiving-edit-btn me-1">Edit</button>';
                    $btn .= '<button data-id="' . $row->id . '" class="btn btn-danger btn-sm receiving-delete-btn">Delete</button>';
                    return $btn;
                })
                ->rawColumns(['action'])
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

        Receiving::create($data);

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

        $receiving->update($data);

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

        $receiving->delete();
        return response()->json(['success' => 'Receiving record deleted successfully.']);
    }
}
