<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Plant;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class PlantController extends Controller
{
    public function index(Request $request)
    {
        $customerId = TenantService::currentCustomerId();

        if ($request->ajax()) {
            $query = Plant::with('customer');
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
                ->addColumn('customer', function ($row) {
                    return $row->customer->customer_name ?? 'Internal';
                })
                ->addColumn('plant_code', function ($row) {
                    return $row->plant_code ?? '-';
                })
                ->addColumn('location', function ($row) {
                    return $row->location ?? '-';
                })
                ->addColumn('description', function ($row) {
                    return $row->description ?? '-';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d-m-Y H:i:s');
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

        $customers = TenantService::isInternal()
            ? Customer::orderBy('customer_name')->get()
            : collect();

        return view('rbac.plant.index', [
            'customers' => $customers,
            'currentCustomerId' => $customerId,
        ]);
    }

    public function store(Request $request)
    {
        $customerId = TenantService::resolveCustomerId($request->customer_id);

        $request->validate([
            'plant_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('plants')->where(function ($query) use ($customerId) {
                    if (is_null($customerId)) {
                        $query->whereNull('customer_id');
                    } else {
                        $query->where('customer_id', $customerId);
                    }
                }),
            ],
            'plant_code' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        Plant::create([
            'plant_name' => $request->plant_name,
            'plant_code' => $request->plant_code,
            'location' => $request->location,
            'description' => $request->description,
            'customer_id' => $customerId,
        ]);

        return response()->json(['success' => 'Plant created successfully']);
    }

    public function edit($id)
    {
        $plant = Plant::findOrFail($id);
        TenantService::assertAccess($plant->customer_id);
        return response()->json($plant);
    }

    public function update(Request $request, $id)
    {
        $plant = Plant::findOrFail($id);
        TenantService::assertAccess($plant->customer_id);
        $customerId = TenantService::resolveCustomerId($request->customer_id);

        $request->validate([
            'plant_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('plants')->where(function ($query) use ($customerId) {
                    if (is_null($customerId)) {
                        $query->whereNull('customer_id');
                    } else {
                        $query->where('customer_id', $customerId);
                    }
                })->ignore($id),
            ],
            'plant_code' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        $plant->update([
            'plant_name' => $request->plant_name,
            'plant_code' => $request->plant_code,
            'location' => $request->location,
            'description' => $request->description,
            'customer_id' => $customerId,
        ]);

        return response()->json(['success' => 'Plant updated successfully']);
    }

    public function destroy($id)
    {
        $plant = Plant::findOrFail($id);
        TenantService::assertAccess($plant->customer_id);
        $plant->delete();
        return response()->json(['success' => 'Plant deleted successfully']);
    }
}
