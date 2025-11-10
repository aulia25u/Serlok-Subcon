<?php

namespace App\Http\Controllers\LOI;

use App\Http\Controllers\Controller;
use App\Models\AdditionalPart;
use App\Models\ImportantPoint;
use App\Models\ManufacturingProcess;
use App\Models\MaterialCalculation;
use App\Models\ProductionProcessInformation;
use App\Models\SalesInformation;
use App\Models\ski\CustomerSKI;
use App\Models\ski\MaterialSKI;
use App\Models\ski\MesinSKI;
use App\Models\ski\RfqmasterSKI;
use App\Models\ski\SupplierSKI;
use App\Models\Tooling;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class EstimateNewItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('loi.estimate-new-item.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('loi.estimate-new-item.create');
    }

    /**
     * Get data for DataTables
     */
    public function getData(Request $request)
    {
        $query = SalesInformation::query()
            ->select([
                'id',
                'date',
                'customer_id',
                'part_no',
                'part_name',
                'decision',
                'created_at',
            ])
            ->orderBy('created_at', 'desc');

        // Server-side filtering
        if ($request->has('search') && $request->search['value']) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('part_no', 'like', "%{$search}%")
                    ->orWhere('part_name', 'like', "%{$search}%")
                    ->orWhere('decision', 'like', "%{$search}%");
            });
        }

        $totalRecords = SalesInformation::count();
        $filteredRecords = $query->count();

        // Pagination
        if ($request->has('start') && $request->has('length')) {
            $query->skip($request->start)->take($request->length);
        }

        $data = $query->get()->map(function ($item, $index) use ($request) {
            return [
                'DT_RowIndex' => $request->start + $index + 1,
                'id' => $item->id,
                'date' => $item->date->format('Y-m-d'),
                'customer' => $item->customer_id ?? '-',
                'part_no' => $item->part_no ?? '-',
                'part_name' => $item->part_name ?? '-',
                'status' => $item->decision ? strtoupper($item->decision) : 'PENDING',
            ];
        });

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'customer_id' => 'nullable',
            'part_no' => 'nullable|string',
            'decision' => 'nullable|in:ok,no',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Create Sales Information
            $salesInfo = SalesInformation::create([
                'date' => $request->date,
                'customer_id' => $request->customer_id,
                'part_no' => $request->part_no,
                'part_name' => $request->part_name,
                'date_masspro' => $request->date_masspro,
                'qty_month' => $request->qty_month,
                'depreciation_periode' => $request->depreciation_periode,
                'tools_depreciation' => $request->tools_depreciation,
                'part_type' => $request->part_type, // Changed from critical_safety and regular_part
                'similar_part' => $request->has('similar_part'),
                'model' => $request->model, // Changed from array to string
                'waya_ply_value' => $request->waya_ply_value,
                'wrapping_ply_value' => $request->wrapping_ply_value,
                'model_other_value' => $request->model_other_value,
                'note' => $request->note,
                'decision' => $request->decision,
                'approved_by' => $request->approved_by,
                'checked_by_1' => $request->checked_by_1,
                'checked_by_2' => $request->checked_by_2,
                'prepared_by' => $request->prepared_by,
            ]);

            // Create Production Process Information
            if ($request->has('process_location')) {
                ProductionProcessInformation::create([
                    'sales_information_id' => $salesInfo->id,
                    'process_location' => $request->process_location,
                    'supplier_name' => $request->supplier_name,
                ]);
            }

            // Create Material Calculations
            if ($request->has('materials') && is_array($request->materials)) {
                foreach ($request->materials as $material) {
                    if (! empty($material['material_id']) || ! empty($material['specification'])) {
                        MaterialCalculation::create([
                            'sales_information_id' => $salesInfo->id,
                            'material_id' => $material['material_id'] ?? null,
                            'specification' => $material['specification'] ?? null,
                            'new_material' => $material['new_material'] ?? 'no',
                            'code' => $material['code'] ?? null,
                            'thick' => $material['thick'] ?? null,
                            'diameter_in' => $material['diameter_in'] ?? null,
                            'diameter_out' => $material['diameter_out'] ?? null,
                            'length' => $material['length'] ?? null,
                            'volume' => $material['volume'] ?? null,
                            'weight_estimate' => $material['weight_estimate'] ?? null,
                            'weight_actual' => $material['weight_actual'] ?? null,
                        ]);
                    }
                }
            }

            // Create Additional Parts
            if ($request->has('additional_parts') && is_array($request->additional_parts)) {
                foreach ($request->additional_parts as $part) {
                    if (! empty($part['material_id']) || ! empty($part['specification'])) {
                        AdditionalPart::create([
                            'sales_information_id' => $salesInfo->id,
                            'material_id' => $part['material_id'] ?? null,
                            'part_no' => $part['part_no'] ?? null,
                            'specification' => $part['specification'] ?? null,
                            'qty_unit' => $part['qty_unit'] ?? null,
                            'supplier' => $part['supplier'] ?? null,
                        ]);
                    }
                }
            }

            // Create Manufacturing Processes
            if ($request->has('processes') && is_array($request->processes)) {
                foreach ($request->processes as $index => $process) {
                    if (isset($process['enabled']) && $process['enabled']) {
                        ManufacturingProcess::create([
                            'sales_information_id' => $salesInfo->id,
                            'process_name' => $process['process_name'] ?? 'Process '.($index + 1),
                            'enabled' => true,
                            'machine_id' => $process['machine_id'] ?? null,
                            'cycle_time_estimate' => $process['cycle_time_estimate'] ?? null,
                            'cycle_time_actual' => $process['cycle_time_actual'] ?? null,
                            'capacity_estimate' => $process['capacity_estimate'] ?? null,
                            'capacity_actual' => $process['capacity_actual'] ?? null,
                            'remarks' => $process['remarks'] ?? null,
                        ]);
                    }
                }
            }

            // Create Important Points
            if ($request->has('important_points') && is_array($request->important_points)) {
                foreach ($request->important_points as $point) {
                    if (! empty($point['item']) || ! empty($point['note'])) {
                        ImportantPoint::create([
                            'sales_information_id' => $salesInfo->id,
                            'item' => $point['item'] ?? null,
                            'note' => $point['note'] ?? null,
                        ]);
                    }
                }
            }

            // Create Toolings
            if ($request->has('tooling') && is_array($request->tooling)) {
                $toolingNames = ['Dies', 'Mandrel', 'Mall Cutting', 'Mall Checking'];
                foreach ($request->tooling as $index => $tool) {
                    if (! empty($tool['cavity']) || ! empty($tool['quantity'])) {
                        Tooling::create([
                            'sales_information_id' => $salesInfo->id,
                            'tooling' => $toolingNames[$index] ?? 'Tooling '.($index + 1),
                            'cavity' => $tool['cavity'] ?? null,
                            'quantity' => $tool['quantity'] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data saved successfully',
                'data' => $salesInfo,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing estimate new item: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $salesInfo = SalesInformation::with([
                'productionProcess',
                'materialCalculations',
                'additionalParts',
                'manufacturingProcesses',
                'importantPoints',
                'toolings',
                'approvedBy',
                'checkedBy1',
                'checkedBy2',
                'preparedBy',
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $salesInfo,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $salesInfo = SalesInformation::with([
                'productionProcess',
                'materialCalculations',
                'additionalParts',
                'manufacturingProcesses',
                'importantPoints',
                'toolings',
            ])->findOrFail($id);

            // Don't load user relationships as objects, just use the IDs
            // The IDs are already in the main model fields

            return view('loi.estimate-new-item.edit', compact('salesInfo'));

        } catch (\Exception $e) {
            return redirect()->route('loi.estimate-new-item')
                ->with('error', 'Data not found');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'customer_id' => 'nullable',
            'part_no' => 'nullable|string',
            'decision' => 'nullable|in:ok,no',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $salesInfo = SalesInformation::findOrFail($id);

            // Update Sales Information
            $salesInfo->update([
                'date' => $request->date,
                'customer_id' => $request->customer_id,
                'part_no' => $request->part_no,
                'part_name' => $request->part_name,
                'date_masspro' => $request->date_masspro,
                'qty_month' => $request->qty_month,
                'depreciation_periode' => $request->depreciation_periode,
                'tools_depreciation' => $request->tools_depreciation,
                'part_type' => $request->part_type, // Changed from critical_safety and regular_part
                'similar_part' => $request->has('similar_part'),
                'model' => $request->model, // Changed from array to string
                'waya_ply_value' => $request->waya_ply_value,
                'wrapping_ply_value' => $request->wrapping_ply_value,
                'model_other_value' => $request->model_other_value,
                'note' => $request->note,
                'decision' => $request->decision,
                'approved_by' => $request->approved_by,
                'checked_by_1' => $request->checked_by_1,
                'checked_by_2' => $request->checked_by_2,
                'prepared_by' => $request->prepared_by,
            ]);

            // Update or Create Production Process
            ProductionProcessInformation::updateOrCreate(
                ['sales_information_id' => $salesInfo->id],
                [
                    'process_location' => $request->process_location,
                    'supplier_name' => $request->supplier_name,
                ]
            );

            // Delete and recreate related records for simplicity
            $salesInfo->materialCalculations()->delete();
            $salesInfo->additionalParts()->delete();
            $salesInfo->manufacturingProcesses()->delete();
            $salesInfo->importantPoints()->delete();
            $salesInfo->toolings()->delete();

            // Recreate Material Calculations
            if ($request->has('materials') && is_array($request->materials)) {
                foreach ($request->materials as $material) {
                    if (! empty($material['material_id']) || ! empty($material['specification'])) {
                        MaterialCalculation::create([
                            'sales_information_id' => $salesInfo->id,
                            'material_id' => $material['material_id'] ?? null,
                            'specification' => $material['specification'] ?? null,
                            'new_material' => $material['new_material'] ?? 'no',
                            'code' => $material['code'] ?? null,
                            'thick' => $material['thick'] ?? null,
                            'diameter_in' => $material['diameter_in'] ?? null,
                            'diameter_out' => $material['diameter_out'] ?? null,
                            'length' => $material['length'] ?? null,
                            'volume' => $material['volume'] ?? null,
                            'weight_estimate' => $material['weight_estimate'] ?? null,
                            'weight_actual' => $material['weight_actual'] ?? null,
                        ]);
                    }
                }
            }

            // Recreate Additional Parts
            if ($request->has('additional_parts') && is_array($request->additional_parts)) {
                foreach ($request->additional_parts as $part) {
                    if (! empty($part['material_id']) || ! empty($part['specification'])) {
                        AdditionalPart::create([
                            'sales_information_id' => $salesInfo->id,
                            'material_id' => $part['material_id'] ?? null,
                            'part_no' => $part['part_no'] ?? null,
                            'specification' => $part['specification'] ?? null,
                            'qty_unit' => $part['qty_unit'] ?? null,
                            'supplier' => $part['supplier'] ?? null,
                        ]);
                    }
                }
            }

            // Recreate Manufacturing Processes
            if ($request->has('processes') && is_array($request->processes)) {
                foreach ($request->processes as $index => $process) {
                    if (isset($process['enabled']) && $process['enabled']) {
                        ManufacturingProcess::create([
                            'sales_information_id' => $salesInfo->id,
                            'process_name' => $process['process_name'] ?? 'Process '.($index + 1),
                            'enabled' => true,
                            'machine_id' => $process['machine_id'] ?? null,
                            'cycle_time_estimate' => $process['cycle_time_estimate'] ?? null,
                            'cycle_time_actual' => $process['cycle_time_actual'] ?? null,
                            'capacity_estimate' => $process['capacity_estimate'] ?? null,
                            'capacity_actual' => $process['capacity_actual'] ?? null,
                            'remarks' => $process['remarks'] ?? null,
                        ]);
                    }
                }
            }

            // Recreate Important Points
            if ($request->has('important_points') && is_array($request->important_points)) {
                foreach ($request->important_points as $point) {
                    if (! empty($point['item']) || ! empty($point['note'])) {
                        ImportantPoint::create([
                            'sales_information_id' => $salesInfo->id,
                            'item' => $point['item'] ?? null,
                            'note' => $point['note'] ?? null,
                        ]);
                    }
                }
            }

            // Recreate Toolings
            if ($request->has('tooling') && is_array($request->tooling)) {
                $toolingNames = ['Dies', 'Mandrel', 'Mall Cutting', 'Mall Checking'];
                foreach ($request->tooling as $index => $tool) {
                    if (! empty($tool['cavity']) || ! empty($tool['quantity'])) {
                        Tooling::create([
                            'sales_information_id' => $salesInfo->id,
                            'tooling' => $toolingNames[$index] ?? 'Tooling '.($index + 1),
                            'cavity' => $tool['cavity'] ?? null,
                            'quantity' => $tool['quantity'] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data updated successfully',
                'data' => $salesInfo,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating estimate new item: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $salesInfo = SalesInformation::findOrFail($id);
            $salesInfo->delete(); // Cascade delete will handle related records

            return response()->json([
                'success' => true,
                'message' => 'Data deleted successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting estimate new item: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get list of customers for dropdown
     */
    public function getCustomers()
    {
        try {
            $customers = CustomerSKI::select('id', 'name', 'kode')
                ->orderBy('name', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $customers,
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching customers: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching customers',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get parts
     */
    public function getParts(Request $request)
    {
        try {
            $customerId = $request->get('customer_id');

            $query = RfqmasterSKI::select('id', 'partno', 'partname', 'qty', 'dom', 'dp', 'td', 'similairpart');

            // Filter by customer_id if provided
            if ($customerId) {
                $query->where('customer_id', $customerId);
            }

            $parts = $query->orderBy('partno', 'asc')->get();

            return response()->json([
                'success' => true,
                'data' => $parts,
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching parts by customer: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching parts',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get part details by part number
     */
    public function getPartDetails(Request $request)
    {
        try {
            $partNo = $request->get('part_no');

            if (! $partNo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Part number is required',
                ], 400);
            }

            $part = RfqmasterSKI::where('partno', $partNo)->first();

            if (! $part) {
                return response()->json([
                    'success' => false,
                    'message' => 'Part not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'part_name' => $part->partname,
                    'qty_month' => $part->qty,
                    'date_masspro' => $part->dom ? $part->dom->format('Y-m-d') : null,
                    'depreciation_periode' => $part->dp,
                    'tools_depreciation' => $part->td,
                    'similar_part' => $part->similairpart == 'Yes' ? 1 : ($part->similairpart == 'No' ? 0 : -1),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching part details: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching part details',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get list of materials for dropdown
     */
    public function getMaterials()
    {
        try {
            $materials = MaterialSKI::select('id', 'name', 'kode_material', 'coding')
                ->orderBy('name', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $materials,
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching materials: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching materials',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get material details by material ID
     */
    public function getMaterialDetails(Request $request)
    {
        try {
            $materialId = $request->get('material_id');

            if (! $materialId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Material ID is required',
                ], 400);
            }

            $material = MaterialSKI::find($materialId);

            if (! $material) {
                return response()->json([
                    'success' => false,
                    'message' => 'Material not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'code' => $material->kode_material ?? $material->coding,
                    'name' => $material->name,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching material details: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching material details',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get additional parts (materials with category_id != 7 and 1)
     */
    public function getAdditionalPartsByCustomer(Request $request)
    {
        try {

            $query = MaterialSKI::select('id', 'kode_material as partno', 'name as partname')
                ->whereNotIn('category_id', [7, 1]);

            $parts = $query->orderBy('name', 'asc')->get();

            return response()->json([
                'success' => true,
                'data' => $parts,
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching additional parts: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching additional parts',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get additional part details by material ID (category_id = 6)
     */
    public function getAdditionalPartDetails(Request $request)
    {
        try {
            $materialId = $request->get('material_id');

            if (! $materialId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Material ID is required',
                ], 400);
            }

            $material = MaterialSKI::where('id', $materialId)
                ->where('category_id', 6)
                ->first();

            if (! $material) {
                return response()->json([
                    'success' => false,
                    'message' => 'Material not found',
                ], 404);
            }

            // Get supplier name from material's supplier_id
            $supplier = null;
            if ($material->supplier_id) {
                $supplier = SupplierSKI::select('id', 'name')
                    ->where('id', $material->supplier_id)
                    ->first();
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'part_no' => $material->kode_material,
                    'specification' => $material->coding,
                    'supplier' => $supplier ? $supplier->name : '',
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching additional part details: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching part details',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get list of machines for manufacturing process
     */
    public function getMachines()
    {
        try {
            $machines = MesinSKI::select('id', 'name', 'kode_caliper')
                ->where('availability', 1)
                ->orderBy('name', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $machines,
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching machines: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching machines',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get list of users for approval dropdowns
     */
    public function getUsers()
    {
        try {
            $users = User::with('userDetail')
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->userDetail->employee_name,
                        'email' => $user->email,
                    ];
                })
                ->sortBy('name')
                ->values();

            return response()->json([
                'success' => true,
                'data' => $users,
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching users: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching users',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
