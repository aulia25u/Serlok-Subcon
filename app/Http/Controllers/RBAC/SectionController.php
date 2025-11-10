<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Dept;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SectionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Section::with('dept')
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

        $departments = Dept::all();
        return view('rbac.section.index', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'section_name' => 'required|string|max:255',
            'dept_id' => 'required|exists:depts,id',
        ]);

        $section = Section::create($request->all());

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
        return response()->json($section);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'section_name' => 'required|string|max:255',
            'dept_id' => 'required|exists:depts,id',
        ]);

        $section = Section::findOrFail($id);
        $oldValues = $section->toArray();

        $section->update($request->all());

        // Log activity
        $newValues = $section->toArray();
        ActivityLogService::logUpdate('sections', $section->id, $oldValues, $newValues);

        return response()->json(['success' => 'Section updated successfully']);
    }

    public function destroy($id)
    {
        $section = Section::findOrFail($id);
        $oldValues = $section->toArray();

        $section->delete();

        // Log activity
        ActivityLogService::logDelete('sections', $id, $oldValues);

        return response()->json(['success' => 'Section deleted successfully']);
    }

    public function getAllSections()
    {
        $sections = Section::select('id', 'section_name')->get();

        return response()->json($sections);
    }

    public function getByDepartment($dept_id)
    {
        $sections = Section::where('dept_id', $dept_id)->get();
        return response()->json($sections);
    }
}