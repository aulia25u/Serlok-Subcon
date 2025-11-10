<?php

namespace App\Http\Controllers\LOI;

use App\Http\Controllers\Controller;
use App\Models\SalesInformation;
use App\Models\ski\RfqmasterSKI;
use App\Models\TeamFeasibilityChecklistItem;
use App\Models\TeamFeasibilityCommitment;
use App\Models\TeamFeasibilityRevision;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TeamFeasibilityCommitmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('loi.feasability-commitment.index');
    }

    /**
     * Get data for DataTables
     */
    public function getData(Request $request)
    {
        $query = TeamFeasibilityCommitment::query()
            ->select(['id', 'document_no', 'part_name', 'part_no', 'model', 'customer_name', 'conclusion_status', 'created_at'])
            ->orderBy('created_at', 'desc');

        // Server-side filtering
        if ($request->has('search') && $request->search['value']) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('document_no', 'like', "%{$search}%")
                    ->orWhere('part_no', 'like', "%{$search}%")
                    ->orWhere('part_name', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        $totalRecords = TeamFeasibilityCommitment::count();
        $filteredRecords = $query->count();

        // Pagination
        if ($request->has('start') && $request->has('length')) {
            $query->skip($request->start)->take($request->length);
        }

        $data = $query->get()->map(function ($item, $index) use ($request) {
            // Determine status badge
            $statusBadge = '<span class="badge badge-secondary">Draft</span>';
            if ($item->conclusion_status === 'feasible') {
                $statusBadge = '<span class="badge badge-success">Feasible</span>';
            } elseif ($item->conclusion_status === 'feasible_with_changes') {
                $statusBadge = '<span class="badge badge-warning">Feasible (Revisi)</span>';
            } elseif ($item->conclusion_status === 'not_feasible') {
                $statusBadge = '<span class="badge badge-danger">Not Feasible</span>';
            }

            return [
                'DT_RowIndex' => $request->start + $index + 1,
                'id' => $item->id,
                'document_no' => $item->document_no ?? '-',
                'part_name' => $item->part_name ?? '-',
                'part_no' => $item->part_no ?? '-',
                'model' => $item->model ?? '-',
                'customer_name' => $item->customer_name ?? '-',
                'status' => $statusBadge,
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
        return view('loi.feasability-commitment.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'part_name' => 'required|string',
            'part_no' => 'required|string',
            'model' => 'nullable|string',
            'customer_name' => 'nullable|string',
            'customer_id' => 'required|string',
            'conclusion_status' => 'nullable|in:feasible,feasible_with_changes,not_feasible',
            'conclusion_notes' => 'nullable|string',
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

            // Generate document number in format: <increment>/FEAS/<RomanMonth>/<Year>
            // Increment resets every year.
            $year = date('Y');
            $month = date('n');
            $romanMonth = $this->monthToRoman($month);

            // Find the latest document created this year
            $lastDocThisYear = TeamFeasibilityCommitment::whereYear('created_at', $year)
                ->orderBy('created_at', 'desc')
                ->first();

            $nextNumber = 1;
            if ($lastDocThisYear && $lastDocThisYear->document_no) {
                // Expecting format like "123/FEAS/X/2025" - extract leading number
                if (preg_match('/^(\d+)\/FEAS\/(I|II|III|IV|V|VI|VII|VIII|IX|X|XI|XII)\/(\d{4})$/', strtoupper($lastDocThisYear->document_no), $matches)) {
                    $lastYear = $matches[3];
                    $lastNumber = intval($matches[1]);
                    if ($lastYear == $year) {
                        $nextNumber = $lastNumber + 1;
                    }
                } else {
                    // Fallback: if the document_no doesn't match expected format, try to extract leading digits
                    if (preg_match('/^(\d+)/', $lastDocThisYear->document_no, $m)) {
                        $nextNumber = intval($m[1]) + 1;
                    } else {
                        // Last resort: count how many records this year and increment
                        $countThisYear = TeamFeasibilityCommitment::whereYear('created_at', $year)->count();
                        $nextNumber = $countThisYear + 1;
                    }
                }
            }

            // Zero-pad to 4 digits for consistency (assumption). If you prefer no padding, remove str_pad.
            $documentNo = str_pad($nextNumber, 4, '0', STR_PAD_LEFT).'/FEAS/'.$romanMonth.'/'.$year;

            // Create main commitment
            $commitment = TeamFeasibilityCommitment::create([
                'document_no' => $documentNo,
                'part_name' => $request->part_name,
                'part_no' => $request->part_no,
                'model' => $request->model,
                'customer_name' => $request->customer_name,
                'customer_id' => $request->customer_id,
                'conclusion_status' => $request->conclusion_status,
                'conclusion_notes' => $request->conclusion_notes,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
                // Sign-off users
                'general_mgr_id' => $request->general_mgr_id,
                'factory_mgr_id' => $request->factory_mgr_id,
                'qa_mgr_id' => $request->qa_mgr_id,
                'qc_id' => $request->qc_id,
                'engineering_id' => $request->engineering_id,
                'production_id' => $request->production_id,
                'maintenance_id' => $request->maintenance_id,
                'ppic_id' => $request->ppic_id,
                'purchasing_id' => $request->purchasing_id,
                'sales_id' => $request->sales_id,
            ]);

            // Create checklist items (hardcoded structure based on form)
            $this->createChecklistItems($commitment->id, $request);

            // Create revision records
            if ($request->has('revisions')) {
                foreach ($request->revisions as $revision) {
                    TeamFeasibilityRevision::create([
                        'feasibility_commitment_id' => $commitment->id,
                        'revision_number' => $revision['revision_number'],
                        'revision_date' => $revision['revision_date'],
                        'revision_contains' => $revision['revision_contains'],
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Team Feasibility Commitment created successfully',
                'data' => $commitment,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating Team Feasibility Commitment: '.$e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error creating data: '.$e->getMessage(),
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
        $commitment = TeamFeasibilityCommitment::with(['checklistItems', 'revisions', 'generalMgr.userDetail', 'factoryMgr.userDetail', 'qaMgr.userDetail', 'qc.userDetail', 'engineering.userDetail', 'production.userDetail', 'maintenance.userDetail', 'ppic.userDetail', 'purchasing.userDetail', 'sales.userDetail'])->findOrFail($id);

        // Format checklist items untuk lebih mudah diakses di frontend
        $checklistItems = [];
        foreach ($commitment->checklistItems as $item) {
            $checklistItems[$item->item_code] = [
                'is_checkbox' => $item->is_checkbox,
                'check_result' => $item->check_result,
                'checkbox_value' => $item->checkbox_value,
                'notes' => $item->notes,
                'pic' => $item->pic,
            ];
        }

        // Format revisions
        $revisions = $commitment->revisions->map(function ($revision) {
            return [
                'revision_number' => str_pad($revision->revision_number, 2, '0', STR_PAD_LEFT),
                'revision_date' => $revision->revision_date->format('d/m/Y'),
                'revision_contains' => $revision->revision_contains,
            ];
        });

        $data = [
            'id' => $commitment->id,
            'document_no' => $commitment->document_no,
            'part_name' => $commitment->part_name,
            'part_no' => $commitment->part_no,
            'model' => $commitment->model,
            'customer_name' => $commitment->customer_name,
            'customer_id' => $commitment->customer_id,
            'conclusion_status' => $commitment->conclusion_status,
            'conclusion_notes' => $commitment->conclusion_notes,
            'checklist_items' => $checklistItems,
            'revisions' => $revisions,
            // Sign-off users - get name from userDetail->employee_name
            'general_mgr' => $commitment->generalMgr && $commitment->generalMgr->userDetail ? $commitment->generalMgr->userDetail->employee_name : ($commitment->generalMgr ? $commitment->generalMgr->username : '-'),
            'factory_mgr' => $commitment->factoryMgr && $commitment->factoryMgr->userDetail ? $commitment->factoryMgr->userDetail->employee_name : ($commitment->factoryMgr ? $commitment->factoryMgr->username : '-'),
            'qa_mgr' => $commitment->qaMgr && $commitment->qaMgr->userDetail ? $commitment->qaMgr->userDetail->employee_name : ($commitment->qaMgr ? $commitment->qaMgr->username : '-'),
            'qc' => $commitment->qc && $commitment->qc->userDetail ? $commitment->qc->userDetail->employee_name : ($commitment->qc ? $commitment->qc->username : '-'),
            'engineering' => $commitment->engineering && $commitment->engineering->userDetail ? $commitment->engineering->userDetail->employee_name : ($commitment->engineering ? $commitment->engineering->username : '-'),
            'production' => $commitment->production && $commitment->production->userDetail ? $commitment->production->userDetail->employee_name : ($commitment->production ? $commitment->production->username : '-'),
            'maintenance' => $commitment->maintenance && $commitment->maintenance->userDetail ? $commitment->maintenance->userDetail->employee_name : ($commitment->maintenance ? $commitment->maintenance->username : '-'),
            'ppic' => $commitment->ppic && $commitment->ppic->userDetail ? $commitment->ppic->userDetail->employee_name : ($commitment->ppic ? $commitment->ppic->username : '-'),
            'purchasing' => $commitment->purchasing && $commitment->purchasing->userDetail ? $commitment->purchasing->userDetail->employee_name : ($commitment->purchasing ? $commitment->purchasing->username : '-'),
            'sales' => $commitment->sales && $commitment->sales->userDetail ? $commitment->sales->userDetail->employee_name : ($commitment->sales ? $commitment->sales->username : '-'),
        ];

        return view('loi.feasability-commitment.show', ['commitment' => $data]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $commitment = TeamFeasibilityCommitment::with(['checklistItems', 'revisions'])->findOrFail($id);

        // Format checklist items untuk lebih mudah diakses di frontend
        $checklistItems = [];
        foreach ($commitment->checklistItems as $item) {
            $checklistItems[$item->item_code] = [
                'is_checkbox' => $item->is_checkbox,
                'check_result' => $item->check_result,
                'checkbox_value' => $item->checkbox_value,
                'notes' => $item->notes,
                'pic' => $item->pic,
            ];
        }

        // Format revisions
        $revisions = $commitment->revisions->map(function ($revision) {
            return [
                'revision_number' => str_pad($revision->revision_number, 2, '0', STR_PAD_LEFT),
                'revision_date' => $revision->revision_date->format('Y-m-d'),
                'revision_contains' => $revision->revision_contains,
            ];
        });

        $data = [
            'id' => $commitment->id,
            'document_no' => $commitment->document_no,
            'part_name' => $commitment->part_name,
            'part_no' => $commitment->part_no,
            'model' => $commitment->model,
            'customer_name' => $commitment->customer_name,
            'customer_id' => $commitment->customer_id,
            'conclusion_status' => $commitment->conclusion_status,
            'conclusion_notes' => $commitment->conclusion_notes,
            'checklist_items' => $checklistItems,
            'revisions' => $revisions,
            // Sign-off users
            'general_mgr_id' => $commitment->general_mgr_id,
            'factory_mgr_id' => $commitment->factory_mgr_id,
            'qa_mgr_id' => $commitment->qa_mgr_id,
            'qc_id' => $commitment->qc_id,
            'engineering_id' => $commitment->engineering_id,
            'production_id' => $commitment->production_id,
            'maintenance_id' => $commitment->maintenance_id,
            'ppic_id' => $commitment->ppic_id,
            'purchasing_id' => $commitment->purchasing_id,
            'sales_id' => $commitment->sales_id,
        ];

        return view('loi.feasability-commitment.edit', ['commitment' => $data]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'part_name' => 'required|string',
            'part_no' => 'required|string',
            'model' => 'nullable|string',
            'customer_name' => 'nullable|string',
            'customer_id' => 'required|string',
            'conclusion_status' => 'nullable|in:feasible,feasible_with_changes,not_feasible',
            'conclusion_notes' => 'nullable|string',
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

            $commitment = TeamFeasibilityCommitment::findOrFail($id);

            // Update main commitment
            $commitment->update([
                'part_name' => $request->part_name,
                'part_no' => $request->part_no,
                'model' => $request->model,
                'customer_name' => $request->customer_name,
                'customer_id' => $request->customer_id,
                'conclusion_status' => $request->conclusion_status,
                'conclusion_notes' => $request->conclusion_notes,
                'updated_by' => Auth::id(),
                // Sign-off users
                'general_mgr_id' => $request->general_mgr_id,
                'factory_mgr_id' => $request->factory_mgr_id,
                'qa_mgr_id' => $request->qa_mgr_id,
                'qc_id' => $request->qc_id,
                'engineering_id' => $request->engineering_id,
                'production_id' => $request->production_id,
                'maintenance_id' => $request->maintenance_id,
                'ppic_id' => $request->ppic_id,
                'purchasing_id' => $request->purchasing_id,
                'sales_id' => $request->sales_id,
            ]);

            // Delete existing checklist items and create new ones
            TeamFeasibilityChecklistItem::where('feasibility_commitment_id', $commitment->id)->delete();
            $this->createChecklistItems($commitment->id, $request);

            // Delete existing revisions and create new ones
            TeamFeasibilityRevision::where('feasibility_commitment_id', $commitment->id)->delete();
            if ($request->has('revisions')) {
                foreach ($request->revisions as $revision) {
                    TeamFeasibilityRevision::create([
                        'feasibility_commitment_id' => $commitment->id,
                        'revision_number' => $revision['revision_number'],
                        'revision_date' => $revision['revision_date'],
                        'revision_contains' => $revision['revision_contains'],
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Team Feasibility Commitment updated successfully',
                'data' => $commitment,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating Team Feasibility Commitment: '.$e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error updating data: '.$e->getMessage(),
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
            $commitment = TeamFeasibilityCommitment::findOrFail($id);
            $commitment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Team Feasibility Commitment deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting Team Feasibility Commitment: '.$e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error deleting data: '.$e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Get RFQ Parts from SKI database
     */
    public function getRfqParts(Request $request)
    {
        try {
            $search = $request->get('q', '');

            $parts = RfqmasterSKI::select('id', 'partno', 'partname', 'customer_id')
                ->where(function ($query) use ($search) {
                    if ($search) {
                        $query->where('partname', 'like', "%{$search}%")->orWhere('partno', 'like', "%{$search}%");
                    }
                })
                ->limit(50)
                ->get();

            $formatted = $parts->map(function ($part) {
                return [
                    'id' => $part->id,
                    'text' => $part->partname.' ('.$part->partno.')',
                    'partno' => $part->partno,
                    'partname' => $part->partname,
                    'customer_id' => $part->customer_id,
                ];
            });

            return response()->json(['results' => $formatted]);
        } catch (\Exception $e) {
            Log::error('Error fetching RFQ parts: '.$e->getMessage());

            return response()->json(['results' => []]);
        }
    }

    /**
     * Get part details including customer info
     */
    public function getPartDetails(Request $request)
    {
        try {
            $partId = $request->get('part_id');

            $part = RfqmasterSKI::with('customer')->find($partId);

            if (! $part) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Part not found',
                    ],
                    404,
                );
            }

            // Get model from sales information
            $salesInfo = SalesInformation::where('part_no', $part->partno)->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'part_no' => $part->partno,
                    'part_name' => $part->partname,
                    'customer_id' => $part->customer_id,
                    'customer_name' => $part->customer ? $part->customer->name : '',
                    'model' => $salesInfo ? $salesInfo->model : '',
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching part details: '.$e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error fetching part details',
                ],
                500,
            );
        }
    }

    /**
     * Get users for signature dropdowns
     */
    public function getUsers(Request $request)
    {
        try {
            $users = User::with('userDetail')
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->userDetail ? $user->userDetail->employee_name : $user->username,
                        'email' => $user->email,
                        'position' => $user->userDetail ? $user->userDetail->position : null,
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

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error fetching users',
                ],
                500,
            );
        }
    }

    /**
     * Create default checklist items based on form structure
     */
    private function createChecklistItems($commitmentId, $request)
    {
        $items = $this->getChecklistStructure();

        foreach ($items as $item) {
            $itemCode = $item['code'];
            $checkResult = $request->input("check_result_{$itemCode}");
            $checkboxValue = null;
            $notes = $request->input("notes_{$itemCode}");

            // Get is_checkbox from user input instead of structure
            $isCheckbox = $request->input("is_checkbox_{$itemCode}", false);

            // Handle special cases for new radio button fields
            if ($itemCode === '1_1') {
                // Similar product - "Ya, yaitu:"
                $similarProductDetail = $request->input('similar_product_detail');
                if ($similarProductDetail === 'yes') {
                    $checkboxValue = $request->input('checkbox_value_1_1');
                    $isCheckbox = true; // Set is_checkbox to true if this field is used
                }
            } elseif ($itemCode === '1_2') {
                // Similar product - "Tidak"
                $similarProductDetail = $request->input('similar_product_detail');
                if ($similarProductDetail === 'no') {
                    $checkboxValue = null;
                    $isCheckbox = true; // Set is_checkbox to true if this field is used

                }
            } elseif ($itemCode === '3_2_1') {
                // Special requirement - "Ya, yaitu:"
                $specialRequirementDetail = $request->input('special_requirement_detail');
                if ($specialRequirementDetail === 'yes') {
                    $checkboxValue = $request->input('checkbox_value_3_2_1');
                    $isCheckbox = true; // Set is_checkbox to true if this field is used
                }
            } elseif ($itemCode === '3_2_2') {
                // Special requirement - "Tidak"
                $specialRequirementDetail = $request->input('special_requirement_detail');
                if ($specialRequirementDetail === 'no') {
                    $checkboxValue = null;
                    $isCheckbox = true; // Set is_checkbox to true if this field is used
                }
            } else {
                // Regular checkbox items
                $checkboxValue = $request->input("checkbox_{$itemCode}");
            }

            TeamFeasibilityChecklistItem::create([
                'feasibility_commitment_id' => $commitmentId,
                'item_code' => $item['code'],
                'checkpoint_description' => $item['description'],
                'check_result' => $checkResult,
                'pic' => $item['pic'],
                'notes' => $notes,
                'is_checkbox' => $isCheckbox, // Get from user input
                'checkbox_value' => $checkboxValue, // Store as varchar (text)
                'order_sequence' => $item['order'],
            ]);
        }
    }

    /**
     * Update checklist items
     */
    private function updateChecklistItems($commitmentId, $request) {}

    /**
     * Get the hardcoded checklist structure based on the form
     */
    private function getChecklistStructure()
    {
        return [
            ['code' => '1', 'description' => 'Apakah Ada similar product ?', 'pic' => 'ENG', 'order' => 1],
            ['code' => '1_1', 'description' => 'Ya, yaitu:', 'pic' => 'ENG', 'order' => 2],
            ['code' => '1_2', 'description' => 'Tidak', 'pic' => 'ENG', 'order' => 3],
            ['code' => '2', 'description' => 'Biaya', 'pic' => 'SLS', 'order' => 4],
            ['code' => '2_1', 'description' => 'Apakah target biaya proses produksi bisa dipenuhi', 'pic' => 'SLS', 'order' => 5],
            ['code' => '3', 'description' => 'Kemampuan Proses Produksi', 'pic' => 'ENG', 'order' => 6],
            ['code' => '3_1', 'description' => 'Apakah produk bisa dibuat dengan memenuhi semua karakteristik yang ditentukan oleh pelanggan? (semua toleransi di drawing)', 'pic' => 'ENG', 'order' => 7],
            ['code' => '3_2', 'description' => 'Ada persyaratan karakteristik khusus', 'pic' => 'ENG', 'order' => 8],
            ['code' => '3_2_1', 'description' => 'Ya, yaitu:', 'pic' => 'ENG', 'order' => 9],
            ['code' => '3_2_2', 'description' => 'Tidak', 'pic' => 'ENG', 'order' => 10],
            ['code' => '3_3', 'description' => 'Jika ya, Apakah semua persyaratan bisa dikuti ?', 'pic' => 'ENG', 'order' => 11],
            ['code' => '4', 'description' => 'Kapasitas Produksi', 'pic' => 'PPIC', 'order' => 12],
            ['code' => '4_1', 'description' => 'Apakah kapasitas produksi/supply di supplier bisa memenuhi target order', 'pic' => 'PPIC', 'order' => 13],
            ['code' => '4_2', 'description' => 'Apakah kapasitas produksi di internal mencukupi', 'pic' => 'PPIC', 'order' => 14],
            ['code' => '4_2_1', 'description' => 'di Proses Extruder', 'pic' => 'PPIC', 'order' => 15],
            ['code' => '4_2_2', 'description' => 'di Proses Manual / Waya', 'pic' => 'PPIC', 'order' => 16],
            ['code' => '4_2_3', 'description' => 'di Proses Cutting', 'pic' => 'PPIC', 'order' => 17],
            ['code' => '4_2_4', 'description' => 'di Proses Assy', 'pic' => 'PPIC', 'order' => 18],
            ['code' => '4_2_5', 'description' => 'di Proses inspection', 'pic' => 'PPIC', 'order' => 19],
            ['code' => '5', 'description' => 'Tenggat waktu', 'pic' => 'Purc/Eng', 'order' => 20],
            ['code' => '5_1', 'description' => 'Dapatkan semua material dan bahan pembantu tersedia tepat waktu?', 'pic' => 'Purc/Eng', 'order' => 21],
            ['code' => '5_2', 'description' => 'Apakah tooling produksi bisa selesai tepat waktu', 'pic' => 'Purc/Eng', 'order' => 22],
            ['code' => '5_3', 'description' => 'Apakah dapat mengikuti jadual pelanggan untuk:', 'pic' => 'All Team', 'order' => 23],
            ['code' => '5_3_1', 'description' => 'Kirim sample', 'pic' => 'All Team', 'order' => 24],
            ['code' => '5_3_2', 'description' => 'Mulai produksi massal', 'pic' => 'PPC', 'order' => 25],
            ['code' => '6', 'description' => 'Persyaratan Lingkungan', 'pic' => 'Purc/QA', 'order' => 26],
            ['code' => '6_1', 'description' => 'Dapatkan persyaratan SoC/RoHS dipenuhi?', 'pic' => 'Purc/QA', 'order' => 27],
            ['code' => '7', 'description' => 'Peraturan Pemerintah', 'pic' => 'Produksi', 'order' => 28],
            ['code' => '7_1', 'description' => 'Pembuangan material/produk dan daur ulang', 'pic' => 'Produksi', 'order' => 29],
            ['code' => '7_2', 'description' => 'Keselamatan dan kesehatan kerja', 'pic' => 'GA', 'order' => 30],
            ['code' => '7_3', 'description' => 'Lingkungan lainnya', 'pic' => 'GA', 'order' => 31],
            ['code' => '7_other', 'description' => 'Lain-lain', 'pic' => '', 'order' => 32],
            ['code' => '8', 'description' => 'Lain-lain', 'pic' => '', 'order' => 33],
            ['code' => '8_1', 'description' => 'Apakah perlu menambah fasilitas mesin/tooling/pengetahuan untuk menjalankan produk baru ? Jika ya, apakah dapat memenuhinya?', 'pic' => 'Eng/Prod', 'order' => 34],
            ['code' => '8_2', 'description' => 'Apakah perlu menambah fasilitas mesin/tooling/pengetahuan untuk menguji produk baru ? Jika ya, apakah dapat memenuhinya?', 'pic' => 'QA/Eng', 'order' => 35],
        ];
    }

    /**
     * Convert month number (1-12) to Roman numeral (I-XII)
     */
    private function monthToRoman(int $month)
    {
        $map = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
        ];

        return $map[$month] ?? (string) $month;
    }
}
