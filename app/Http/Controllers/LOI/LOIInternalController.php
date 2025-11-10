<?php

namespace App\Http\Controllers\LOI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\LOIInternal;
use App\Models\Itempart;
use App\Models\UploadHasilMeeting;
use App\Models\UploadDrawingPart;

class LOIInternalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('loi.internals.index');
    }

    /**
     * Get data for DataTable
     */
    public function getData(Request $request)
    {
        try {
            $query = LOIInternal::query();

            // Join directly with parts table using rfqmaster_id = parts.id
            $query->leftJoin('parts', 'loi_internals.rfqmaster_id', '=', 'parts.id')
                  ->select(
                      'loi_internals.*',
                      'parts.partno as rfq_partno',
                      'parts.id as part_id'
                  );

            // Apply search filter
            if ($request->has('search') && !empty($request->search['value'])) {
                $searchValue = $request->search['value'];
                $query->where(function($q) use ($searchValue) {
                    $q->where('loi_internals.document_no', 'like', "%{$searchValue}%")
                      ->orWhere('loi_internals.customer_name', 'like', "%{$searchValue}%")
                      ->orWhere('parts.partno', 'like', "%{$searchValue}%");
                });
            }

            // Get total count before pagination
            $totalRecords = LOIInternal::count();
            $filteredRecords = $query->count();

            // Apply pagination
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $query->offset($start)->limit($length);

            // Apply ordering
            $orderColumn = $request->input('order.0.column', 0);
            $orderDir = $request->input('order.0.dir', 'asc');
            $columns = ['id', 'rfq_partno', 'document_no', 'document_date', 'link_hasil_meeting', 'link_loi_external', 'action'];
            
            if (isset($columns[$orderColumn])) {
                $query->orderBy($columns[$orderColumn], $orderDir);
            }

            $loiInternals = $query->get();

            $data = [];
            foreach ($loiInternals as $index => $loi) {
                // Check if there are hasil meetings for this part
                $hasilMeetingsCount = UploadHasilMeeting::where('part_id', $loi->part_id)->count();
                $hasilMeetingButton = $hasilMeetingsCount > 0 
                    ? '<button class="btn btn-sm btn-info view-hasil-meeting-btn" data-id="' . $loi->id . '">
                        <i class="fas fa-eye"></i> View (' . $hasilMeetingsCount . ')
                       </button>'
                    : '-';

                // Check if there are drawing parts for this part
                $drawingPartsCount = UploadDrawingPart::where('part_id', $loi->part_id)->count();
                $drawingPartButton = $drawingPartsCount > 0 
                    ? '<button class="btn btn-sm btn-info view-loi-external-btn" data-id="' . $loi->id . '">
                        <i class="fas fa-eye"></i> View (' . $drawingPartsCount . ')
                       </button>'
                    : '-';

                $data[] = [
                    'no' => $start + $index + 1,
                    'rfq_no' => $loi->rfq_partno ?? '-',
                    'document_no' => $loi->document_no ?? '-',
                    'document_date' => $loi->document_date ? $loi->document_date->format('Y-m-d') : '-',
                    'link_hasil_meeting' => $hasilMeetingButton,
                    'link_loi_external' => $drawingPartButton,
                    'action' => '<button class="btn btn-primary btn-sm details-btn" data-id="' . $loi->id . '">Details</button>',
                ];
            }

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error fetching data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Hasil Meeting details for modal
     */
    public function getHasilMeetingDetails($id)
    {
        try {
            $loi = LOIInternal::findOrFail($id);
            
            // Get part_id directly from parts table using rfqmaster_id = parts.id
            $partId = DB::table('parts')
                ->where('parts.id', $loi->rfqmaster_id)
                ->value('parts.id');

            if (!$partId) {
                return response()->json([]);
            }

            // Get upload hasil meetings
            $hasilMeetings = UploadHasilMeeting::where('part_id', $partId)->get();

            $data = $hasilMeetings->map(function($meeting) {
                return [
                    'id' => $meeting->id,
                    'title' => 'Hasil Meeting',
                    'type' => 'Meeting',
                    'image' => $meeting->image
                ];
            });

            return response()->json($data);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error fetching details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get LOI External details for modal
     */
    public function getLoiExternalDetails($id)
    {
        try {
            $loi = LOIInternal::findOrFail($id);
            
            // Get part_id directly from parts table using rfqmaster_id = parts.id
            $partId = DB::table('parts')
                ->where('parts.id', $loi->rfqmaster_id)
                ->value('parts.id');

            if (!$partId) {
                return response()->json([]);
            }

            // Get upload drawing parts
            $drawingParts = UploadDrawingPart::where('part_id', $partId)->get();

            $data = $drawingParts->map(function($drawing) {
                return [
                    'id' => $drawing->id,
                    'title' => 'Drawing Part',
                    'type' => 'Drawing',
                    'image' => $drawing->image
                ];
            });

            return response()->json($data);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error fetching details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->json(['success' => true]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'rfqmaster_id' => 'required|exists:parts,id',
                'document_no' => 'required|string|max:255',
                'customer_name' => 'required|string|max:255',
                'document_date' => 'required|date',
            ]);

            LOIInternal::create([
                'rfqmaster_id' => $request->rfqmaster_id,
                'document_no' => $request->document_no,
                'customer_name' => $request->customer_name,
                'document_date' => $request->document_date,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'LOI Internal created successfully.'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating LOI Internal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get parts for Select2 dropdown
     */
    public function getParts(Request $request)
    {
        try {
            $search = $request->get('q', '');
            
            $query = Itempart::query();
            
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('partno', 'like', "%{$search}%")
                      ->orWhere('partname', 'like', "%{$search}%")
                      ->orWhere('customername', 'like', "%{$search}%");
                });
            }
            
            $parts = $query->limit(50)->get();
            
            $formatted = $parts->map(function ($part) {
                return [
                    'id' => $part->id,
                    'text' => $part->partno . ' - ' . $part->partname . ' (' . $part->customername . ')',
                    'partno' => $part->partno,
                    'partname' => $part->partname,
                    'customername' => $part->customername
                ];
            });
            
            return response()->json(['results' => $formatted]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching parts: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
