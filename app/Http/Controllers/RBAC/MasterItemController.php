<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use App\Models\MasterItem;
use App\Models\TenantOwner;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MasterItemController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = MasterItem::with('tenantOwner')->select('*');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('tenant_name', function (MasterItem $masterItem) {
                    return $masterItem->tenantOwner ? $masterItem->tenantOwner->name : 'N/A';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('master-item.show', $row->id) . '" class="btn btn-info btn-sm me-1">View</a>';
                    $btn .= '<a href="' . route('master-item.edit', $row->id) . '" class="btn btn-primary btn-sm me-1">Edit</a>';
                    $btn .= '<button data-id="' . $row->id . '" class="btn btn-danger btn-sm delete">Delete</button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('master_item.index');
    }

    public function create()
    {
        $tenantOwners = TenantOwner::all();
        return view('master_item.create', compact('tenantOwners'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:tenant_owners,id',
            'item_name' => 'required|string|max:255',
            'item_code' => 'required|string|max:255|unique:master_items,item_code',
            'description' => 'nullable|string',
        ]);

        MasterItem::create($request->all());

        return redirect()->route('master-item.index')
            ->with('success', 'Master Item created successfully.');
    }

    public function show(MasterItem $masterItem)
    {
        return view('master_item.show', compact('masterItem'));
    }

    public function edit(MasterItem $masterItem)
    {
        $tenantOwners = TenantOwner::all();
        return view('master_item.edit', compact('masterItem', 'tenantOwners'));
    }

    public function update(Request $request, MasterItem $masterItem)
    {
        $request->validate([
            'tenant_id' => 'required|exists:tenant_owners,id',
            'item_name' => 'required|string|max:255',
            'item_code' => 'required|string|max:255|unique:master_items,item_code,' . $masterItem->id,
            'description' => 'nullable|string',
        ]);

        $masterItem->update($request->all());

        return redirect()->route('master-item.index')
            ->with('success', 'Master Item updated successfully.');
    }

    public function destroy(MasterItem $masterItem)
    {
        $masterItem->delete();

        return response()->json(['success' => 'Master Item deleted successfully.']);
    }
}
