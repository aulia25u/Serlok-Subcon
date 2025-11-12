<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Section;
use App\Models\Dept;
use App\Services\ActivityLogService;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SectionController extends Controller
{
    public function index(Request $request)
    {
        $customerId = TenantService::currentCustomerId();

        if ($request->ajax()) {
            $query = Section::with('dept.customer');
            $query = TenantService::scopeQueryByCustomer($query);
            $query = $query
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
                ->addColumn('dept_name', function ($row) {
                    return $row->dept->dept_name ?? '-';
                })
                ->addColumn('customer', function ($row) {
                    return $row->customer->customer_name ?? 'Internal';
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

        $departments = TenantService::scopeQueryByCustomer(
            Dept::orderBy('dept_name')
        )->get();

        $customers = TenantService::isInternal()
            ? Customer::orderBy('customer_name')->get()
            : Customer::where('id', $customerId)->get();

        return view('rbac.section.index', [
            'departments' => $departments,
            'currentCustomerId' => $customerId,
            'customers' => $customers,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'section_name' => 'required|string|max:255',
            'dept_id' => 'required|exists:depts,id',
        ]);

        $dept = Dept::findOrFail($request->dept_id);
        TenantService::assertAccess($dept->customer_id);

        $section = Section::create([
            'section_name' => $request->section_name,
            'dept_id' => $dept->id,
            'customer_id' => $dept->customer_id,
        ]);

        // Log activity
        ActivityLogService::logCreate('sections', $section->id, [
            'section_name' => $request->section_name,
            'dept_id' => $request->dept_id,
        ]);

        return response()->json(['success' => 'Section created successfully']);
    }

    public function edit($id)
    {
        $section = Section::with('dept')->findOrFail($id);
        TenantService::assertAccess($section->customer_id);
        return response()->json($section);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'section_name' => 'required|string|max:255',
            'dept_id' => 'required|exists:depts,id',
        ]);

        $section = Section::findOrFail($id);
        TenantService::assertAccess($section->customer_id);
        $oldValues = $section->toArray();

        $dept = Dept::findOrFail($request->dept_id);
        TenantService::assertAccess($dept->customer_id);

        $section->update([
            'section_name' => $request->section_name,
            'dept_id' => $dept->id,
            'customer_id' => $dept->customer_id,
        ]);

        // Log activity
        $newValues = $section->toArray();
        ActivityLogService::logUpdate('sections', $section->id, $oldValues, $newValues);

        return response()->json(['success' => 'Section updated successfully']);
    }

    public function destroy($id)
    {
        $section = Section::findOrFail($id);
        TenantService::assertAccess($section->customer_id);
        $oldValues = $section->toArray();

        $section->delete();

        // Log activity
        ActivityLogService::logDelete('sections', $id, $oldValues);

        return response()->json(['success' => 'Section deleted successfully']);
    }

    public function getAllSections()
    {
        $sections = TenantService::scopeQueryByCustomer(
            Section::select('id', 'section_name')
        )->get();

        return response()->json($sections);
    }

    public function getByDepartment($dept_id)
    {
        $dept = Dept::findOrFail($dept_id);
        TenantService::assertAccess($dept->customer_id);
        $sections = Section::where('dept_id', $dept_id)
            ->where('customer_id', $dept->customer_id)
            ->get();
        return response()->json($sections);
    }

    public function getByCustomer($customer_id)
    {
        TenantService::assertAccess($customer_id);

        $sections = Section::where('customer_id', $customer_id)
            ->select('id', 'section_name')
            ->orderBy('section_name')
            ->get();

        return response()->json($sections);
    }
}
