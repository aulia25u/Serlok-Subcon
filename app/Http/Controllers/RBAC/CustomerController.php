<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Handle stats request for dashboard
            if ($request->has('stats')) {
                $total = Customer::count();
                return response()->json(['total' => $total]);
            }

            $query = Customer::with('userMarketing.userDetail')
                ->when($request->start_date, function ($q) use ($request) {
                    return $q->whereDate('created_at', '>=', $request->start_date);
                })
                ->when($request->end_date, function ($q) use ($request) {
                    return $q->whereDate('created_at', '<=', $request->end_date);
                });

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('join_date_formatted', function ($row) {
                    return $row->join_date ? $row->join_date->format('d M Y') : '';
                })
                ->addColumn('created_at_formatted', function ($row) {
                    return $row->created_at ? $row->created_at->format('d M Y, H:i') : '';
                })
                ->addColumn('user_marketing_name', function ($row) {
                    return $row->userMarketing ? ($row->userMarketing->userDetail->employee_name ?? $row->userMarketing->username) : '-';
                })
                ->addColumn('status_badge', function ($row) {
                    $badgeClass = $row->status == 'active' ? 'badge-success' : 'badge-secondary';
                    return '<span class="badge ' . $badgeClass . '">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('pos_status_badge', function ($row) {
                    $badgeClass = $row->pos_status == 'ready' ? 'badge-success' : 'badge-warning';
                    return '<span class="badge ' . $badgeClass . '">' . ucfirst(str_replace('_', ' ', $row->pos_status)) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="btn-group" role="group">';
                    $btn .= '<button class="btn btn-sm btn-info view-btn" data-id="' . $row->id . '" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>';
                    $btn .= '<button class="btn btn-sm btn-primary edit-btn" data-id="' . $row->id . '" title="Edit Customer">
                                <i class="fas fa-edit"></i>
                            </button>';
                    $btn .= '<button class="btn btn-sm btn-danger delete-btn" data-id="' . $row->id . '" title="Delete Customer">
                                <i class="fas fa-trash"></i>
                            </button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action', 'status_badge', 'pos_status_badge'])
                ->make(true);
        }

        return view('rbac.customer.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'business_category' => 'required|string|max:255',
            'sub_business_category' => 'required|string|max:255',
            'join_date' => 'required|date',
            'telegram_chat_id' => 'nullable|string|max:255',
            'status' => 'required|in:active,non-active',
            'pos_status' => 'required|in:ready,not_ready',
            'user_marketing_id' => 'nullable|exists:users,id',
            'owner' => 'nullable|string|max:255',
        ]);

        Customer::create($request->all());

        return response()->json(['success' => 'Customer created successfully.']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return response()->json($customer);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'business_category' => 'required|string|max:255',
            'sub_business_category' => 'required|string|max:255',
            'join_date' => 'required|date',
            'telegram_chat_id' => 'nullable|string|max:255',
            'status' => 'required|in:active,non-active',
            'pos_status' => 'required|in:ready,not_ready',
            'user_marketing_id' => 'nullable|exists:users,id',
            'owner' => 'nullable|string|max:255',
        ]);

        $customer = Customer::findOrFail($id);
        $customer->update($request->all());

        return response()->json(['success' => 'Customer updated successfully.']);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $customer = Customer::with('userMarketing.userDetail')->findOrFail($id);

        // Format dates for display
        $customer->join_date_formatted = $customer->join_date ? $customer->join_date->format('d M Y') : '-';
        $customer->created_at_formatted = $customer->created_at ? $customer->created_at->format('d M Y, H:i') : '-';
        $customer->updated_at_formatted = $customer->updated_at ? $customer->updated_at->format('d M Y, H:i') : '-';

        // Format status badge
        $badgeClass = $customer->status == 'active' ? 'badge-success' : 'badge-secondary';
        $customer->status_badge = '<span class="badge ' . $badgeClass . '">' . ucfirst($customer->status) . '</span>';

        // Format POS status badge
        $posBadgeClass = $customer->pos_status == 'ready' ? 'badge-success' : 'badge-warning';
        $customer->pos_status_badge = '<span class="badge ' . $posBadgeClass . '">' . ucfirst(str_replace('_', ' ', $customer->pos_status)) . '</span>';

        // Add user marketing name
        $customer->user_marketing_name = $customer->userMarketing ? ($customer->userMarketing->userDetail->employee_name ?? $customer->userMarketing->username) : '-';

        return response()->json($customer);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return response()->json(['success' => 'Customer deleted successfully.']);
    }

    /**
     * Get users for dropdown with search.
     */
    public function getUsers(Request $request)
    {
        $search = $request->get('search');
        $users = \App\Models\User::with('userDetail')
            ->when($search, function($query) use ($search) {
                $query->where('username', 'like', '%' . $search . '%')
                      ->orWhereHas('userDetail', function($q) use ($search) {
                          $q->where('employee_name', 'like', '%' . $search . '%');
                      });
            })
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'text' => $user->userDetail->employee_name ?? $user->username,
                ];
            });

        return response()->json(['results' => $users]);
    }
}
