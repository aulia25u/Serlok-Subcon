<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use App\Models\MomCustomer;
use App\Models\Customer;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MomCustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Handle stats request for dashboard
            if ($request->has('stats')) {
                $total = MomCustomer::count();
                return response()->json(['total' => $total]);
            }

            $query = MomCustomer::with('customer')
                ->when($request->start_date, function ($q) use ($request) {
                    return $q->whereDate('meeting_date', '>=', $request->start_date);
                })
                ->when($request->end_date, function ($q) use ($request) {
                    return $q->whereDate('meeting_date', '<=', $request->end_date);
                });

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('customer_name', function ($row) {
                    return $row->customer->name ?? '-';
                })
                ->addColumn('meeting_date_formatted', function ($row) {
                    return $row->meeting_date ? \Carbon\Carbon::parse($row->meeting_date)->setTimezone('Asia/Jakarta')->format('d M Y') : '-';
                })
                ->addColumn('next_meeting_date_formatted', function ($row) {
                    return $row->next_meeting_date ? \Carbon\Carbon::parse($row->next_meeting_date)->setTimezone('Asia/Jakarta')->format('d M Y') : '-';
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
                ->rawColumns(['action'])
                ->make(true);
        }

        $customers = Customer::all();
        return view('rbac.mom-customer.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::all();
        return view('rbac.mom-customer.create', compact('customers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'meeting_date' => 'required|date',
            'attendees' => 'nullable|string',
            'agenda' => 'nullable|string',
            'minutes' => 'nullable|string',
            'action_items' => 'nullable|string',
            'next_meeting_date' => 'nullable|date',
        ]);

        MomCustomer::create($request->all());

        return response()->json(['success' => 'MoM Customer created successfully'])
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $momCustomer = MomCustomer::with('customer')->findOrFail($id);
        return response()->json($momCustomer);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $momCustomer = MomCustomer::findOrFail($id);
        return response()->json($momCustomer);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'meeting_date' => 'required|date',
            'attendees' => 'nullable|string',
            'agenda' => 'nullable|string',
            'minutes' => 'nullable|string',
            'action_items' => 'nullable|string',
            'next_meeting_date' => 'nullable|date',
        ]);

        $momCustomer = MomCustomer::findOrFail($id);
        $momCustomer->update($request->all());

        return response()->json(['success' => 'MoM Customer updated successfully'])
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $momCustomer = MomCustomer::findOrFail($id);
        $momCustomer->delete();

        return response()->json(['success' => 'MoM Customer deleted successfully'])
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}
