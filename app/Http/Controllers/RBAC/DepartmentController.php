<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Dept;
use App\Services\ActivityLogService;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $customerId = TenantService::currentCustomerId();

        if ($request->ajax()) {
            $query = Dept::with('customer');
            $query = TenantService::scopeQueryByCustomer($query);
            $query = $query
                ->when($request->start_date, function ($q) use ($request) {
                    return $q->whereDate('created_at', '>=', $request->start_date);
                })
                ->when($request->end_date, function ($q) use ($request) {
                    return $q->whereDate('created_at', '<=', $request->end_date);
                });

            return DataTables::of($query)
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('Y-m-d H:i:s');
                })
                ->editColumn('updated_at', function ($row) {
                    return $row->updated_at ? $row->updated_at->format('Y-m-d H:i:s') : '-';
                })
                ->addColumn('no', function ($row) {
                    static $no = 0;
                    return ++$no;
                })
                ->addColumn('customer', function ($row) {
                    return $row->customer->customer_name ?? 'Internal';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button class="btn btn-sm btn-primary edit-btn dept-edit-btn" data-id="' . $row->id . '">
                                <i class="fas fa-edit"></i> Edit
                            </button>';
                    $btn .= ' <button class="btn btn-sm btn-danger delete-btn dept-delete-btn" data-id="' . $row->id . '">
                                <i class="fas fa-trash"></i> Delete
                            </button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $customers = TenantService::isInternal()
            ? Customer::orderBy('customer_name')->get()
            : collect();

        return view('rbac.department.index', [
            'customers' => $customers,
            'currentCustomerId' => $customerId,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'dept_name' => 'required|string|max:255|unique:depts,dept_name',
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        $dept = Dept::create([
            'dept_name' => $request->dept_name,
            'customer_id' => TenantService::resolveCustomerId($request->customer_id),
        ]);

        // Log activity
        ActivityLogService::logCreate('depts', $dept->id, [
            'dept_name' => $request->dept_name,
            'customer_id' => $dept->customer_id,
        ]);

        return response()->json(['success' => 'Department created successfully']);
    }

    public function edit($id)
    {
        $department = Dept::findOrFail($id);
        TenantService::assertAccess($department->customer_id);
        return response()->json($department);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'dept_name' => 'required|string|max:255|unique:depts,dept_name,' . $id,
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        $department = Dept::findOrFail($id);
        TenantService::assertAccess($department->customer_id);
        $oldValues = $department->toArray();

        $department->update([
            'dept_name' => $request->dept_name,
            'customer_id' => TenantService::resolveCustomerId($request->customer_id),
        ]);

        // Log activity
        $newValues = $department->toArray();
        ActivityLogService::logUpdate('depts', $department->id, $oldValues, $newValues);

        return response()->json(['success' => 'Department updated successfully']);
    }

    public function destroy($id)
    {
        $department = Dept::findOrFail($id);
        TenantService::assertAccess($department->customer_id);
        $oldValues = $department->toArray();

        $department->delete();

        // Log activity
        ActivityLogService::logDelete('depts', $id, $oldValues);

        return response()->json(['success' => 'Department deleted successfully']);
    }

    public function getAllDepartments()
    {
        $departments = TenantService::scopeQueryByCustomer(
            Dept::select('id', 'dept_name')
        )->get();

        return response()->json($departments);
    }

    public function getByCustomer($customer_id)
    {
        $resolvedCustomerId = $customer_id === 'null' ? null : (int) $customer_id;
        TenantService::assertAccess($resolvedCustomerId);

        $departments = Dept::where(function ($query) use ($resolvedCustomerId) {
            if (is_null($resolvedCustomerId)) {
                $query->whereNull('customer_id');
            } else {
                $query->where('customer_id', $resolvedCustomerId);
            }
        })
            ->select('id', 'dept_name')
            ->orderBy('dept_name')
            ->get();

        return response()->json($departments);
    }
}
