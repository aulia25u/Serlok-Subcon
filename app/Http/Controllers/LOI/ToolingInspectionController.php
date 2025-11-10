<?php

namespace App\Http\Controllers\LOI;

use App\Http\Controllers\Controller;
use App\Models\MasterInspection;
use App\Models\ski\CustomerSKI;
use App\Models\TeamFeasibilityCommitment;
use App\Models\ToolingInspection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ToolingInspectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('loi.tooling-inspection.index');
    }

    /**
     * Get data for DataTables
     */
    public function getData(Request $request)
    {
        $query = ToolingInspection::query()
            ->with(['inspector.userDetail', 'approver.userDetail'])
            ->select(['id', 'customer', 'date', 'part_no', 'quantity', 'result', 'tooling_type', 'inspected_by', 'approved_by', 'created_at'])
            ->orderBy('created_at', 'desc');

        // Server-side filtering
        if ($request->has('search') && $request->search['value']) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('customer', 'like', "%{$search}%")
                    ->orWhere('part_no', 'like', "%{$search}%")
                    ->orWhere('result', 'like', "%{$search}%")
                    ->orWhere('tooling_type', 'like', "%{$search}%");
            });
        }

        $totalRecords = ToolingInspection::count();
        $filteredRecords = $query->count();

        // Pagination
        if ($request->has('start') && $request->has('length')) {
            $query->skip($request->start)->take($request->length);
        }

        $data = $query->get()->map(function ($item, $index) use ($request) {
            // Determine result badge
            $resultBadge = '<span class="badge badge-secondary">-</span>';
            if ($item->result === 'OK') {
                $resultBadge = '<span class="badge badge-success">OK</span>';
            } elseif ($item->result === 'NG') {
                $resultBadge = '<span class="badge badge-danger">NG</span>';
            }

            return [
                'DT_RowIndex' => $request->start + $index + 1,
                'id' => $item->id,
                'customer' => $item->customer ?? '-',
                'date' => $item->date ? $item->date->format('d M Y') : '-',
                'part_no' => $item->part_no ?? '-',
                'quantity' => $item->quantity ?? 0,
                'result' => $resultBadge,
                'tooling_type' => $item->tooling_type ? str_replace('_', ' ', $item->tooling_type) : '-',
                'inspector' => $item->inspector && $item->inspector->userDetail ? $item->inspector->userDetail->employee_name : '-',
                'action' => '
                    <div class="btn-group" role="group">
                        <a href="'.route('loi.tooling-inspection.show', $item->id).'" class="btn btn-sm btn-view text-white action-btn" title="View Details">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="'.route('loi.tooling-inspection.edit', $item->id).'" class="btn btn-sm btn-edit text-white action-btn" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn btn-sm btn-delete text-white action-btn" data-id="'.$item->id.'" title="Delete">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                ',
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get feasible customers from TeamFeasibilityCommitment
        $feasibleCustomers = TeamFeasibilityCommitment::whereNotNull('customer_id')
            ->where('conclusion_status', 'feasible')
            ->select('customer_id', 'customer_name')
            ->distinct()
            ->get();

        // Get customer details from CustomerSKI
        $customerIds = $feasibleCustomers->pluck('customer_id')->unique()->filter()->toArray();
        $customers = CustomerSKI::select('id', 'name', 'kode')
            ->whereIn('id', $customerIds)
            ->get();

        // Get all feasible part numbers grouped by customer
        $feasibilityData = TeamFeasibilityCommitment::whereNotNull('customer_id')
            ->where('conclusion_status', 'feasible')
            ->whereNotNull('part_no')
            ->select('customer_id', 'part_no', 'part_name', 'model')
            ->orderBy('part_no')
            ->get()
            ->groupBy('customer_id');

        // Get users for inspector and approver dropdowns
        $users = User::with('userDetail')
            ->whereHas('userDetail')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->userDetail->employee_name ?? $user->username,
                ];
            });

        // Get master inspections
        $masterInspections = MasterInspection::select('id', 'inspection_item', 'inspection_method', 'standard')->get();

        return view('loi.tooling-inspection.create', compact('customers', 'users', 'masterInspections', 'feasibilityData'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|integer',
            'customer' => 'required|string|max:255',
            'date' => 'required|date',
            'part_no' => 'nullable|string|max:255',
            'quantity' => 'required|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'result' => 'nullable|in:OK,NG',
            'tooling_type' => 'nullable|string',
            'note' => 'nullable|string',
            'inspected_by' => 'nullable|exists:users,id',
            'approved_by' => 'nullable|exists:users,id',
            'items' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        try {
            DB::beginTransaction();

            $data = $request->except(['image', 'items']);

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time().'_'.$image->getClientOriginalName();
                $imagePath = 'uploads/tool-inspection/'.$imageName;
                // Upload the file to the FTP server using the 'ftp' disk
                Storage::disk('ftp')->put($imagePath, file_get_contents($image));

                // Get the public URL from the FTP disk configuration
                $fileUrl = Storage::disk('ftp')->url($imagePath);
                $data['image'] = $fileUrl;
            }

            $toolingInspection = ToolingInspection::create($data);

            // Save inspection items
            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $rowNum => $itemData) {
                    $toolingInspection->items()->create([
                        'row_number' => $rowNum,
                        'inspection_item' => $itemData['inspection_item'] ?? null,
                        'inspection_method' => $itemData['inspection_method'] ?? null,
                        'standard' => $itemData['standard'] ?? null,
                        'tooling_1' => $itemData['tooling_1'] ?? null,
                        'tooling_2' => $itemData['tooling_2'] ?? null,
                        'tooling_3' => $itemData['tooling_3'] ?? null,
                        'tooling_4' => $itemData['tooling_4'] ?? null,
                        'tooling_5' => $itemData['tooling_5'] ?? null,
                        'tooling_6' => $itemData['tooling_6'] ?? null,
                        'tooling_7' => $itemData['tooling_7'] ?? null,
                        'tooling_8' => $itemData['tooling_8'] ?? null,
                        'tooling_9' => $itemData['tooling_9'] ?? null,
                        'tooling_10' => $itemData['tooling_10'] ?? null,
                        'x_bar' => $itemData['x_bar'] ?? null,
                        'r_value' => $itemData['r_value'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tooling Inspection created successfully',
                'data' => $toolingInspection,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating tooling inspection: '.$e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Failed to create tooling inspection: '.$e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $toolingInspection = ToolingInspection::with(['items', 'inspector.userDetail', 'approver.userDetail'])->findOrFail($id);

            // modify the 'image' attribute
            $toolingInspection->image = str_replace('uploads/', '', env('FTP_URL').'/'.$toolingInspection->image);

            return view('loi.tooling-inspection.show', compact('toolingInspection'));
        } catch (\Exception $e) {
            return redirect()
                ->route('loi.tooling-inspection.index')
                ->with('error', 'Tooling Inspection not found');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $toolingInspection = ToolingInspection::with(['items'])->findOrFail($id);

            // Get feasible customers from TeamFeasibilityCommitment
            $feasibleCustomers = TeamFeasibilityCommitment::whereNotNull('customer_id')
                ->where('conclusion_status', 'feasible')
                ->select('customer_id', 'customer_name')
                ->distinct()
                ->get();

            // Get customer details from CustomerSKI
            $customerIds = $feasibleCustomers->pluck('customer_id')->unique()->filter()->toArray();
            $customers = CustomerSKI::select('id', 'name', 'kode')
                ->whereIn('id', $customerIds)
                ->get();

            // Get all feasible part numbers grouped by customer
            $feasibilityData = TeamFeasibilityCommitment::whereNotNull('customer_id')
                ->where('conclusion_status', 'feasible')
                ->whereNotNull('part_no')
                ->select('customer_id', 'part_no', 'part_name', 'model')
                ->orderBy('part_no')
                ->get()
                ->groupBy('customer_id');

            // Get users for inspector and approver dropdowns
            $users = User::with('userDetail')
                ->whereHas('userDetail')
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->userDetail->employee_name ?? $user->username,
                    ];
                });

            // Get master inspections
            $masterInspections = MasterInspection::select('id', 'inspection_item', 'inspection_method', 'standard')->get();

            return view('loi.tooling-inspection.edit', compact('toolingInspection', 'customers', 'users', 'masterInspections', 'feasibilityData'));
        } catch (\Exception $e) {
            return redirect()
                ->route('loi.tooling-inspection.index')
                ->with('error', 'Tooling Inspection not found');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|integer',
            'customer' => 'required|string|max:255',
            'date' => 'required|date',
            'part_no' => 'nullable|string|max:255',
            'quantity' => 'required|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'result' => 'nullable|in:OK,NG',
            'tooling_type' => 'nullable|string',
            'note' => 'nullable|string',
            'inspected_by' => 'nullable|exists:users,id',
            'approved_by' => 'nullable|exists:users,id',
            'items' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        try {
            $toolingInspection = ToolingInspection::findOrFail($id);

            DB::beginTransaction();

            $data = $request->except(['image', 'items']);

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($toolingInspection->image) {
                    // Extract the path from the URL
                    $oldImagePath = str_replace(env('FTP_URL').'/', '', $toolingInspection->image);
                    if (Storage::disk('ftp')->exists($oldImagePath)) {
                        Storage::disk('ftp')->delete($oldImagePath);
                    }
                }

                $image = $request->file('image');
                $imageName = time().'_'.$image->getClientOriginalName();
                $imagePath = 'uploads/tool-inspection/'.$imageName;
                // Upload the file to the FTP server using the 'ftp' disk
                Storage::disk('ftp')->put($imagePath, file_get_contents($image));

                // Get the public URL from the FTP disk configuration
                $fileUrl = Storage::disk('ftp')->url($imagePath);
                $data['image'] = $fileUrl;
            }

            $toolingInspection->update($data);

            // Delete existing items
            $toolingInspection->items()->delete();

            // Save new inspection items
            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $rowNum => $itemData) {
                    $toolingInspection->items()->create([
                        'row_number' => $rowNum,
                        'inspection_item' => $itemData['inspection_item'] ?? null,
                        'inspection_method' => $itemData['inspection_method'] ?? null,
                        'standard' => $itemData['standard'] ?? null,
                        'tooling_1' => $itemData['tooling_1'] ?? null,
                        'tooling_2' => $itemData['tooling_2'] ?? null,
                        'tooling_3' => $itemData['tooling_3'] ?? null,
                        'tooling_4' => $itemData['tooling_4'] ?? null,
                        'tooling_5' => $itemData['tooling_5'] ?? null,
                        'tooling_6' => $itemData['tooling_6'] ?? null,
                        'tooling_7' => $itemData['tooling_7'] ?? null,
                        'tooling_8' => $itemData['tooling_8'] ?? null,
                        'tooling_9' => $itemData['tooling_9'] ?? null,
                        'tooling_10' => $itemData['tooling_10'] ?? null,
                        'x_bar' => $itemData['x_bar'] ?? null,
                        'r_value' => $itemData['r_value'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tooling Inspection updated successfully',
                'data' => $toolingInspection,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating tooling inspection: '.$e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Failed to update tooling inspection: '.$e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $toolingInspection = ToolingInspection::findOrFail($id);

            // Delete image if exists
            if ($toolingInspection->image) {
                // Extract the path from the URL
                $imagePath = str_replace(env('FTP_URL').'/', '', $toolingInspection->image);
                if (Storage::disk('ftp')->exists($imagePath)) {
                    Storage::disk('ftp')->delete($imagePath);
                }
            }

            $toolingInspection->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tooling Inspection deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting tooling inspection: '.$e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Failed to delete tooling inspection: '.$e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Get master inspection details by ID
     */
    public function getMasterInspectionDetails($id)
    {
        try {
            $masterInspection = MasterInspection::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'inspection_item' => $masterInspection->inspection_item,
                    'inspection_method' => $masterInspection->inspection_method,
                    'standard' => $masterInspection->standard,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Master Inspection not found',
                ],
                404,
            );
        }
    }
}
