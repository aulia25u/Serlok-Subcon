<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Outgoing;
use App\Models\MasterItem;
use App\Models\Inventory;
use App\Models\User;
use App\Services\ActivityLogService;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class OutgoingController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Outgoing::with(['masterItem', 'assignedUser', 'creator'])->select('outgoings.*');

            $currentCustomerId = \App\Services\TenantService::currentCustomerId();
            if ($currentCustomerId) {
                $query->whereHas('masterItem.tenantOwner', function ($q) use ($currentCustomerId) {
                    $q->where('customer_id', $currentCustomerId);
                });
            }

            if ($request->filled('outgoing_date')) {
                $dates = explode(' - ', $request->outgoing_date);
                if (count($dates) == 2) {
                    $query->whereBetween('outgoing_date', [$dates[0], $dates[1]]);
                }
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('item_name', function ($row) {
                    return $row->masterItem ? $row->masterItem->item_name : 'N/A';
                })
                ->addColumn('item_code', function ($row) {
                    return $row->masterItem ? $row->masterItem->item_code : 'N/A';
                })
                ->addColumn('assigned_to', function ($row) {
                    return $row->assignedUser ? $row->assignedUser->name : 'N/A';
                })
                ->addColumn('created_by', function ($row) {
                    return $row->creator ? $row->creator->name : 'N/A';
                })
                ->editColumn('quantity', function ($row) {
                    return $row->quantity . ' ' . ($row->masterItem ? $row->masterItem->unit : '');
                })
                ->editColumn('outgoing_date', function ($row) {
                    return $row->outgoing_date ? $row->outgoing_date->format('Y-m-d H:i:s') : '';
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
                ->filterColumn('assigned_to', function ($query, $keyword) {
                    $query->whereHas('assignedUser', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('created_by', function ($query, $keyword) {
                    $query->whereHas('creator', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('status', function ($row) {
                    return $row->status;
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    if ($row->status == 'un-verified') {
                        $btn .= '<button data-id="' . $row->id . '" class="btn btn-success btn-sm outgoing-verify-btn mr-1">Verify</button>';
                        $btn .= '<button data-id="' . $row->id . '" class="btn btn-danger btn-sm outgoing-delete-btn">Delete</button>';
                    }
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

            $users = User::whereHas('userDetail', function ($q) use ($currentCustomerId) {
                $q->where('customer_id', $currentCustomerId);
            })->get();
        } else {
            $masterItems = MasterItem::all();
            $users = User::all();
        }

        return view('rbac.outgoing.index', compact('masterItems', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'master_item_id' => 'required|exists:master_items,id',
            'user_id' => 'required|exists:users,id',
            'quantity' => 'required|numeric|min:0.01',
            'outgoing_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        // RBAC Check
        $currentCustomerId = \App\Services\TenantService::currentCustomerId();
        if ($currentCustomerId) {
            $masterItem = MasterItem::findOrFail($request->master_item_id);
            if ($masterItem->tenantOwner->customer_id !== $currentCustomerId) {
                return response()->json(['error' => 'Unauthorized: Item does not belong to your tenant.'], 403);
            }
        }

        // Stock Validation
        $inventory = Inventory::where('master_item_id', $request->master_item_id)->first();
        if (!$inventory || $inventory->quantity < $request->quantity) {
            return response()->json(['error' => 'Insufficient stock. Available: ' . ($inventory ? $inventory->quantity : 0)], 422);
        }

        $data = $request->all();
        $data['created_by'] = Auth::id();
        $data['status'] = 'un-verified';

        $outgoing = Outgoing::create($data);

        // Log activity
        ActivityLogService::logCreate('outgoings', $outgoing->id, [
            'master_item_id' => $request->master_item_id,
            'user_id' => $request->user_id,
            'quantity' => $request->quantity,
            'outgoing_date' => $request->outgoing_date,
            'notes' => $request->notes,
        ]);

        return response()->json(['success' => 'Outgoing record created successfully.']);
    }

    public function destroy($id)
    {
        $outgoing = Outgoing::with('masterItem.tenantOwner')->findOrFail($id);

        // RBAC Check
        $currentCustomerId = \App\Services\TenantService::currentCustomerId();
        if ($currentCustomerId) {
            if ($outgoing->masterItem->tenantOwner->customer_id !== $currentCustomerId) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        if ($outgoing->status == 'verified') {
            return response()->json(['error' => 'Cannot delete verified record.'], 403);
        }

        $oldValues = $outgoing->toArray();
        $outgoing->delete();

        // Log activity
        ActivityLogService::logDelete('outgoings', $id, $oldValues);
        return response()->json(['success' => 'Outgoing record deleted successfully.']);
    }

    public function verify($id)
    {
        $outgoing = Outgoing::with('masterItem.tenantOwner')->findOrFail($id);

        // RBAC Check
        $currentCustomerId = \App\Services\TenantService::currentCustomerId();
        if ($currentCustomerId) {
            if ($outgoing->masterItem->tenantOwner->customer_id !== $currentCustomerId) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        if ($outgoing->status == 'verified') {
            return response()->json(['error' => 'Record already verified.'], 400);
        }

        $oldValues = $outgoing->toArray();
        $outgoing->update(['status' => 'verified']);

        // Log activity
        $newValues = $outgoing->toArray();
        ActivityLogService::logUpdate('outgoings', $outgoing->id, $oldValues, $newValues);

        return response()->json(['success' => 'Outgoing record verified successfully.']);
    }
}
