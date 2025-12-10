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
                    // Delete disabled by default for master variables to prevent breaking changes
                    // If needed, can be enabled. For now, preventing deletion of critical formats.
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('rbac.master_variable.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'variable_code' => 'required|unique:master_variables,variable_code',
            'variable_name' => 'required|string',
            'variable_value' => 'required|string',
            'description' => 'nullable|string',
        ]);

        MasterVariable::create($request->all());

        return response()->json(['success' => 'Master Variable created successfully.']);
    }

    public function edit($id)
    {
        $variable = MasterVariable::findOrFail($id);
        return response()->json($variable);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'variable_code' => 'required|unique:master_variables,variable_code,' . $id,
            'variable_name' => 'required|string',
            'variable_value' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $variable = MasterVariable::findOrFail($id);
        $variable->update($request->all());

        return response()->json(['success' => 'Master Variable updated successfully.']);
    }

    // public function destroy($id)
    // {
    //     $variable = MasterVariable::findOrFail($id);
    //     $variable->delete();
    //     return response()->json(['success' => 'Master Variable deleted successfully.']);
    // }
}
