<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use App\Models\CalendarPitching;
use App\Models\Customer;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CalendarPitchingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Handle upcoming events request for dashboard
            if ($request->has('upcoming')) {
                $upcomingEvents = CalendarPitching::with('customer')
                    ->where('scheduled_date', '>=', now())
                    ->where('status', '!=', 'completed')
                    ->orderBy('scheduled_date', 'asc')
                    ->limit(10)
                    ->get()
                    ->map(function ($event) {
                        return [
                            'id' => $event->id,
                            'title' => $event->title,
                            'customer_name' => $event->customer->name ?? 'N/A',
                            'scheduled_date_formatted' => $event->scheduled_date ? $event->scheduled_date->format('d M Y') : '',
                            'status' => $event->status,
                        ];
                    });

                $total = CalendarPitching::where('scheduled_date', '>=', now())
                    ->where('status', '!=', 'completed')
                    ->count();

                return response()->json(['total' => $total, 'events' => $upcomingEvents]);
            }

            $query = CalendarPitching::with('customer')
                ->when($request->start_date, function ($q) use ($request) {
                    return $q->whereDate('scheduled_date', '>=', $request->start_date);
                })
                ->when($request->end_date, function ($q) use ($request) {
                    return $q->whereDate('scheduled_date', '<=', $request->end_date);
                })
                ->when($request->status, function ($q) use ($request) {
                    return $q->where('status', $request->status);
                });

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('customer_name', function ($row) {
                    return $row->customer->name ?? '-';
                })
                ->addColumn('location', function ($row) {
                    return $row->location ?? '-';
                })
                ->addColumn('scheduled_date_formatted', function ($row) {
                    return $row->scheduled_date ? \Carbon\Carbon::parse($row->scheduled_date)->setTimezone('Asia/Jakarta')->format('d M Y, H:i') : '-';
                })
                ->addColumn('status_badge', function ($row) {
                    $badges = [
                        'scheduled' => '<span class="badge badge-warning">Scheduled</span>',
                        'completed' => '<span class="badge badge-success">Completed</span>',
                        'cancelled' => '<span class="badge badge-danger">Cancelled</span>',
                    ];
                    return $badges[$row->status] ?? $row->status;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button class="btn btn-sm btn-info view-btn" data-id="' . $row->id . '">
                                <i class="fas fa-eye"></i> View
                            </button>';
                    $btn .= ' <button class="btn btn-sm btn-primary edit-btn" data-id="' . $row->id . '">
                                <i class="fas fa-edit"></i> Edit
                            </button>';
                    $btn .= ' <button class="btn btn-sm btn-danger delete-btn" data-id="' . $row->id . '">
                                <i class="fas fa-trash"></i> Delete
                            </button>';
                    return $btn;
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }

        $customers = Customer::all();
        return view('rbac.calendar-pitching.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::all();
        return view('rbac.calendar-pitching.create', compact('customers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'title' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'scheduled_date' => 'required|date',
            'status' => 'required|in:scheduled,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        CalendarPitching::create($request->all());

        return response()->json(['success' => 'Calendar Pitching created successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $calendarPitching = CalendarPitching::with('customer')->findOrFail($id);
        return response()->json($calendarPitching);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $calendarPitching = CalendarPitching::findOrFail($id);
        return response()->json($calendarPitching);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'title' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'scheduled_date' => 'required|date',
            'status' => 'required|in:scheduled,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $calendarPitching = CalendarPitching::findOrFail($id);
        $calendarPitching->update($request->all());

        return response()->json(['success' => 'Calendar Pitching updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $calendarPitching = CalendarPitching::findOrFail($id);
        $calendarPitching->delete();

        return response()->json(['success' => 'Calendar Pitching deleted successfully']);
    }
}
