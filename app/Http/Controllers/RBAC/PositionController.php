<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Position;
use App\Models\Section;
use App\Services\ActivityLogService;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PositionController extends Controller
{
    public function index(Request $request)
    {
        $customerId = TenantService::currentCustomerId();

        if ($request->ajax()) {
            $query = Position::with('section.dept.customer');
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
                ->addColumn('section_name', function ($row) {
                    return $row->section->section_name ?? '-';
                })
                ->addColumn('dept_name', function ($row) {
                    return $row->section->dept->dept_name ?? '-';
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

        $sections = TenantService::scopeQueryByCustomer(
            Section::with('dept')->orderBy('section_name')
        )->get();

        $customers = TenantService::isInternal()
            ? Customer::orderBy('customer_name')->get()
            : Customer::where('id', $customerId)->get();

        return view('rbac.position.index', [
            'sections' => $sections,
            'currentCustomerId' => $customerId,
            'customers' => $customers,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'position_name' => 'required|string|max:255',
            'section_id' => 'required|exists:sections,id',
        ]);

        $section = Section::findOrFail($request->section_id);
        TenantService::assertAccess($section->customer_id);

        $position = Position::create([
            'position_name' => $request->position_name,
            'section_id' => $section->id,
            'customer_id' => $section->customer_id,
        ]);

        // Log activity
        ActivityLogService::logCreate('positions', $position->id, [
            'position_name' => $request->position_name,
            'section_id' => $request->section_id,
        ]);

        return response()->json(['success' => 'Position created successfully']);
    }

    public function edit($id)
    {
        $position = Position::with('section.dept')->findOrFail($id);
        TenantService::assertAccess($position->customer_id);
        return response()->json($position);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'position_name' => 'required|string|max:255',
            'section_id' => 'required|exists:sections,id',
        ]);

        $position = Position::findOrFail($id);
        TenantService::assertAccess($position->customer_id);
        $oldValues = $position->toArray();

        $section = Section::findOrFail($request->section_id);
        TenantService::assertAccess($section->customer_id);

        $position->update([
            'position_name' => $request->position_name,
            'section_id' => $section->id,
            'customer_id' => $section->customer_id,
        ]);

        // Log activity
        $newValues = $position->toArray();
        ActivityLogService::logUpdate('positions', $position->id, $oldValues, $newValues);

        return response()->json(['success' => 'Position updated successfully']);
    }

    public function destroy($id)
    {
        $position = Position::findOrFail($id);
        TenantService::assertAccess($position->customer_id);
        $oldValues = $position->toArray();

        $position->delete();

        // Log activity
        ActivityLogService::logDelete('positions', $id, $oldValues);

        return response()->json(['success' => 'Position deleted successfully']);
    }

    public function getBySection($section_id)
    {
        $section = Section::findOrFail($section_id);
        TenantService::assertAccess($section->customer_id);
        $positions = Position::where('section_id', $section_id)
            ->where('customer_id', $section->customer_id)
            ->get();
        return response()->json($positions);
    }
}
