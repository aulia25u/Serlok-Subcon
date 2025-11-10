<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use App\Models\Plant;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PlantController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Plant::query()
                ->when($request->start_date, function ($q) use ($request) {
                    return $q->whereDate('created_at', '>=', $request->start_date);
                })
                ->when($request->end_date, function ($q) use ($request) {
                    return $q->whereDate('created_at', '<=', $request->end_date);
                });

            return DataTables::of($query)
                ->addIndexColumn()
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

        return view('rbac.plant.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'plant_name' => 'required|string|max:255|unique:plants,plant_name',
        ]);

        Plant::create($request->all());
        return response()->json(['success' => 'Plant created successfully']);
    }

    public function edit($id)
    {
        $plant = Plant::findOrFail($id);
        return response()->json($plant);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'plant_name' => 'required|string|max:255|unique:plants,plant_name,' . $id,
        ]);

        $plant = Plant::findOrFail($id);
        $plant->update($request->all());
        return response()->json(['success' => 'Plant updated successfully']);
    }

    public function destroy($id)
    {
        $plant = Plant::findOrFail($id);
        $plant->delete();
        return response()->json(['success' => 'Plant deleted successfully']);
    }
}