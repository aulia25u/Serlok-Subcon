<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use App\Models\Dept;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Dept::query()
                ->when($request->start_date, function ($q) use ($request) {
                    return $q->whereDate('created_at', '>=', $request->start_date);
                })
                ->when($request->end_date, function ($q) use ($request) {
                    return $q->whereDate('created_at', '<=', $request->end_date);
                });

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('Y-m-d H:i:s');
                })
                ->addColumn('no', function ($row) {
                    static $no = 0;
                    return ++$no;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button class="btn btn-sm btn-primary edit-btn" data-id="' . $row->id . '">
                                <i class="fas fa-edit"></i> Edit
                            </button>';
                    $btn .= ' <button class="btn btn-sm btn-danger delete-btn" data-id="' . $row->id . '">
                                <i class="fas fa-trash"></i> Delete
                            </button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('rbac.department.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'dept_name' => 'required|string|max:255|unique:depts,dept_name',
        ]);

        $dept = Dept::create($request->all());

        // Log activity
        ActivityLogService::logCreate('depts', $dept->id, [
            'dept_name' => $request->dept_name,
        ]);

        return response()->json(['success' => 'Department created successfully']);
    }

    public function edit($id)
    {
        $department = Dept::findOrFail($id);
        return response()->json($department);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'dept_name' => 'required|string|max:255|unique:depts,dept_name,' . $id,
        ]);

        $department = Dept::findOrFail($id);
        $oldValues = $department->toArray();

        $department->update($request->all());

        // Log activity
        $newValues = $department->toArray();
        ActivityLogService::logUpdate('depts', $department->id, $oldValues, $newValues);

        return response()->json(['success' => 'Department updated successfully']);
    }

    public function destroy($id)
    {
        $department = Dept::findOrFail($id);
        $oldValues = $department->toArray();

        $department->delete();

        // Log activity
        ActivityLogService::logDelete('depts', $id, $oldValues);

        return response()->json(['success' => 'Department deleted successfully']);
    }

    public function getAllDepartments()
    {
        $departments = Dept::select('id', 'dept_name')->get();

        return response()->json($departments);
    }
}