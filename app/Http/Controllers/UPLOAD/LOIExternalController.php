<?php

namespace App\Http\Controllers\UPLOAD;

use App\Http\Controllers\Controller;
use App\Models\Itempart;
use App\Models\UploadLOIExternal as UploadDrawingPart;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class LOIExternalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $page = $request->input('page', 1);
            $perPage = 10;
            $offset = ($page - 1) * $perPage;

            $query = Itempart::query();

            // Apply filters if provided
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
            }

            // Apply search if provided
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('partname', 'LIKE', "%{$search}%")
                      ->orWhere('partno', 'LIKE', "%{$search}%")
                      ->orWhere('hscode', 'LIKE', "%{$search}%")
                      ->orWhere('customername', 'LIKE', "%{$search}%");
                });
            }

            $total = $query->count();
            $items = $query->offset($offset)->limit($perPage)->get();

            $data = $items->map(function($row, $index) use ($offset) {
                return [
                    'id' => $row->id,
                    'no' => $offset + $index + 1,
                    'partname' => $row->partname,
                    'partno' => $row->partno,
                    'hscode' => $row->hscode,
                    'customername' => $row->customername,
                    'action' => '<button class="btn btn-sm btn-info view-btn" data-id="' . $row->id . '">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <button class="btn btn-sm btn-success add-drawing-btn" data-id="' . $row->id . '">
                                    <i class="fas fa-plus"></i> Add
                                </button>'
                ];
            });

            return response()->json([
                'data' => $data,
                'has_more' => ($offset + $perPage) < $total,
                'total' => $total
            ]);
        }

        return view('upload.loiexternal.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'part_id' => 'required|exists:parts,id',
                'attachment_type' => 'required|string|max:255',
                'attachment_title' => 'required|string|max:255',
                'attachment_file' => 'required|file|mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx|max:2048',
            ]);

            $file = $request->file('attachment_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = 'uploads/loiexternals/' . $fileName;

            // Upload the file to the FTP server using the 'ftp' disk
            Storage::disk('ftp')->put($filePath, file_get_contents($file));

            // Get the public URL from the FTP disk configuration
            $fileUrl = Storage::disk('ftp')->url($filePath);

            // Save the full public URL to the 'image' column in the database
            UploadDrawingPart::create([
                'part_id' => $request->input('part_id'),
                'type' => $request->input('attachment_type'),
                'title' => $request->input('attachment_title'),
                'image' => $fileUrl, // Store the full URL
            ]);

            return response()->json(['success' => 'LOI External uploaded successfully.']);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $partId
     * @return \Illuminate\Http\Response
     */
    public function show($partId)
    {
        $drawings = UploadDrawingPart::where('part_id', $partId)->get();

        // Loop through the collection and modify the 'image' attribute
        $drawings->each(function ($drawing) {
            $drawing->image = str_replace('uploads/', '', env('FTP_URL') . '/' . $drawing->image);
        });

        return response()->json($drawings);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $drawing = UploadDrawingPart::findOrFail($id);

        // Get the file path from the full URL
        // The path is everything after the base URL
        $baseFtpUrl = env('FTP_URL');
        $filePath = str_replace($baseFtpUrl . '/', '', $drawing->image);

        // Delete the file from the FTP disk
        Storage::disk('ftp')->delete($filePath);

        // Delete the record from the database
        $drawing->delete();

        return response()->json(['success' => 'LOI External deleted successfully.']);
    }
}
