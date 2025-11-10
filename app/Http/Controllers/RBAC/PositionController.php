<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Models\Section;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PositionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Position::with('section.dept')
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

        $sections = Section::with('dept')->get();
        return view('rbac.position.index', compact('sections'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'position_name' => 'required|string|max:255',
            'section_id' => 'required|exists:sections,id',
        ]);

        $position = Position::create($request->all());

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
        return response()->json($position);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'position_name' => 'required|string|max:255',
            'section_id' => 'required|exists:sections,id',
        ]);

        $position = Position::findOrFail($id);
        $oldValues = $position->toArray();

        $position->update($request->all());

        // Log activity
        $newValues = $position->toArray();
        ActivityLogService::logUpdate('positions', $position->id, $oldValues, $newValues);

        return response()->json(['success' => 'Position updated successfully']);
    }

    public function destroy($id)
    {
        $position = Position::findOrFail($id);
        $oldValues = $position->toArray();

        $position->delete();

        // Log activity
        ActivityLogService::logDelete('positions', $id, $oldValues);

        return response()->json(['success' => 'Position deleted successfully']);
    }

    public function getBySection($section_id)
    {
        $positions = Position::where('section_id', $section_id)->get();
        return response()->json($positions);
    }
}