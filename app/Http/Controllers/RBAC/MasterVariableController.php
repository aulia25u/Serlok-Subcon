<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterVariable;
use Yajra\DataTables\Facades\DataTables;

class MasterVariableController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = MasterVariable::query();

            // Tenant Scoping
            \App\Services\TenantService::scopeQueryByCustomer($query, 'tenant_id');

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('updated_at', function ($row) {
                    return $row->updated_at ? $row->updated_at->format('Y-m-d H:i') : '';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button data-id="' . $row->id . '" class="btn btn-primary btn-sm master_variable-edit-btn mr-1"><i class="fas fa-edit"></i></button>';
                    //$btn .= '<button data-id="' . $row->id . '" class="btn btn-danger btn-sm master_variable-delete-btn"><i class="fas fa-trash"></i></button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('rbac.master_variable.index');
    }

    public function store(Request $request)
    {
        $tenantId = \App\Services\TenantService::resolveCustomerId();

        $request->validate([
            'variable_code' => [
                'required',
                \Illuminate\Validation\Rule::unique('master_variables')->where(function ($query) use ($tenantId) {
                    return $query->where('tenant_id', $tenantId);
                }),
            ],
            'variable_name' => 'required|string',
            'variable_value' => 'required|string',
            'description' => 'nullable|string',
        ]);

        MasterVariable::create([
            'tenant_id' => $tenantId,
            'variable_code' => $request->variable_code,
            'variable_name' => $request->variable_name,
            'variable_value' => $request->variable_value,
            'description' => $request->description,
        ]);

        return response()->json(['success' => 'Master Variable created successfully.']);
    }

    public function edit($id)
    {
        $variable = MasterVariable::findOrFail($id);

        // Tenant Access Assertion
        \App\Services\TenantService::assertAccess($variable->tenant_id);

        return response()->json($variable);
    }

    public function update(Request $request, $id)
    {
        $variable = MasterVariable::findOrFail($id);

        // Tenant Access Assertion
        \App\Services\TenantService::assertAccess($variable->tenant_id);

        $tenantId = $variable->tenant_id;

        $request->validate([
            'variable_code' => [
                'required',
                \Illuminate\Validation\Rule::unique('master_variables')->ignore($id)->where(function ($query) use ($tenantId) {
                    return $query->where('tenant_id', $tenantId);
                }),
            ],
            'variable_name' => 'required|string',
            'variable_value' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $variable->update($request->all());

        return response()->json(['success' => 'Master Variable updated successfully.']);
    }

    // public function destroy($id)
    // {
    //     $variable = MasterVariable::findOrFail($id);
    //     \App\Services\TenantService::assertAccess($variable->tenant_id);
    //     $variable->delete();
    //     return response()->json(['success' => 'Master Variable deleted successfully.']);
    // }
}
