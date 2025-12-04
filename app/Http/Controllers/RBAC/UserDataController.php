<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\Customer;
use App\Models\Position;
use App\Models\Role;
use App\Models\Section;
use App\Models\Dept;
use App\Models\Plant;
use App\Services\ActivityLogService;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserDataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $customerId = TenantService::currentCustomerId();

        if ($request->ajax()) {
            // Query UserDetail directly to list assignments
            $query = UserDetail::with(['user', 'position.section.dept', 'role', 'customer'])
                ->when($request->start_date, function ($q) use ($request) {
                    return $q->whereDate('created_at', '>=', $request->start_date);
                })
                ->when($request->end_date, function ($q) use ($request) {
                    return $q->whereDate('created_at', '<=', $request->end_date);
                });

            if ($customerId) {
                $query->where('customer_id', $customerId);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('no', function ($row) {
                    static $no = 0;
                    return ++$no;
                })
                ->addColumn('full_name', function ($row) {
                    return $row->employee_name ?? '-';
                })
                ->addColumn('username', function ($row) {
                    return $row->user->username ?? '-';
                })
                ->addColumn('email', function ($row) {
                    return $row->user->email ?? '-';
                })
                ->addColumn('role_name', function ($row) {
                    return $row->role->role_name ?? '-';
                })
                ->addColumn('dept_name', function ($row) {
                    return $row->position?->section?->dept?->dept_name ?? '-';
                })
                ->addColumn('section_name', function ($row) {
                    return $row->position?->section?->section_name ?? '-';
                })
                ->addColumn('position_name', function ($row) {
                    return $row->position?->position_name ?? '-';
                })
                ->addColumn('customer_name', function ($row) {
                    return $row->customer->customer_name ?? 'Internal';
                })
                ->addColumn('action', function ($row) {
                    // We edit the USER, not just the detail, but we pass the user ID
                    $btn = '<button class="btn btn-sm btn-primary userData-edit-btn" data-toggle="modal" data-target="#addModal" data-id="' . $row->user_id . '">
                                <i class="fas fa-edit"></i> Edit
                            </button>';
                    // Delete applies to the specific assignment (UserDetail) or the whole user?
                    // If we delete here, we should probably delete the assignment.
                    // But the current UI expects "Delete User".
                    // Let's keep it as "Delete User" for now, or maybe "Delete Assignment"?
                    // The request was "1 user master can have 2 details".
                    // If I delete here, I should probably delete the UserDetail.
                    // But the controller destroy method deletes the User.
                    // Let's stick to User ID for now and maybe refine later.
                    // Actually, if we list Details, we should probably allow deleting the Detail.
                    // But the Edit modal will manage all details.
                    // So let's keep the Edit button pointing to the User.
                    $btn .= ' <button class="btn btn-sm btn-danger userData-delete-btn" data-id="' . $row->user_id . '">
                                <i class="fas fa-trash"></i> Delete
                            </button>';
                    $btn .= ' <button class="btn btn-sm btn-info userData-detail-btn" data-id="' . $row->user_id . '">
                                <i class="fas fa-eye"></i> Detail
                            </button>';
                    return $btn;
                })
                ->filterColumn('customer_name', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        if (stripos('Internal', $keyword) !== false) {
                            $q->whereNull('customer_id');
                        }
                        $q->orWhereHas('customer', function ($subQ) use ($keyword) {
                            $subQ->where('customer_name', 'like', "%{$keyword}%");
                        });
                    });
                })
                ->filterColumn('username', function ($query, $keyword) {
                    $query->whereHas('user', function ($q) use ($keyword) {
                        $q->where('username', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('email', function ($query, $keyword) {
                    $query->whereHas('user', function ($q) use ($keyword) {
                        $q->where('email', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('full_name', function ($query, $keyword) {
                    $query->where('employee_name', 'like', "%{$keyword}%");
                })
                ->filterColumn('role_name', function ($query, $keyword) {
                    $query->whereHas('role', function ($q) use ($keyword) {
                        $q->where('role_name', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('dept_name', function ($query, $keyword) {
                    $query->whereHas('position.section.dept', function ($q) use ($keyword) {
                        $q->where('dept_name', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('section_name', function ($query, $keyword) {
                    $query->whereHas('position.section', function ($q) use ($keyword) {
                        $q->where('section_name', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('position_name', function ($query, $keyword) {
                    $query->whereHas('position', function ($q) use ($keyword) {
                        $q->where('position_name', 'like', "%{$keyword}%");
                    });
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        // Pass all necessary data for the dropdowns to the view
        $departments = TenantService::scopeQueryByCustomer(
            Dept::orderBy('dept_name')
        )->get();
        $sections = TenantService::scopeQueryByCustomer(
            Section::with('dept')->orderBy('section_name')
        )->get();
        $positions = TenantService::scopeQueryByCustomer(
            Position::with('section')->orderBy('position_name')
        )->get();
        $roles = TenantService::scopeQueryByCustomer(
            Role::orderBy('role_name')
        )->get();
        $customers = TenantService::isInternal()
            ? Customer::orderBy('customer_name')->get()
            : Customer::where('id', $customerId)->get();
        $plants = TenantService::scopeQueryByCustomer(
            Plant::orderBy('plant_name')
        )->get();

        return view('rbac.user-data.index', compact('departments', 'sections', 'positions', 'roles', 'customers', 'plants'))
            ->with('currentCustomerId', $customerId);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'full_name' => 'required|string|max:255',
            'role_id' => 'nullable|exists:roles,id', // Made nullable as it's now in details
            'nip' => 'nullable|string|max:255',
            'employee_status' => 'nullable|string|in:Tetap,Kontrak,Borongan',
            'blacklist_note' => 'nullable|string',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:255',
            'status_active' => 'nullable|boolean',
            'employee_photo' => 'nullable|image|max:2048',
            'details' => 'required|array',
            'details.*.customer_id' => 'required|exists:customers,id',
            'details.*.dept_id' => 'required|exists:depts,id',
            'details.*.section_id' => 'required|exists:sections,id', // Add validation logic if needed
            'details.*.position_id' => 'required|exists:positions,id',
            'details.*.role_id' => 'required|exists:roles,id',
            'gender' => 'required|in:Male,Female',
            'details.*.plant_id' => 'required|exists:plants,id',
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'name' => $request->full_name,
                'password' => Hash::make($request->password),
            ]);

            $photoPath = 'default.png';
            if ($request->hasFile('employee_photo')) {
                $photoPath = $request->file('employee_photo')->store('employee_photos', 'public');
            }

            foreach ($request->details as $detail) {
                $section = Section::findOrFail($detail['section_id']);
                TenantService::assertAccess($section->customer_id);

                UserDetail::create([
                    'user_id' => $user->id,
                    'position_id' => $detail['position_id'],
                    'role_id' => $detail['role_id'],
                    'customer_id' => $section->customer_id,
                    'employee_name' => $request->full_name, // Using master full name
                    'employee_id' => $request->username, // Use username as employee_id
                    'nip' => $request->nip,
                    'employee_status' => $request->employee_status,
                    'blacklist_note' => $request->blacklist_note,
                    'bank_name' => $request->bank_name,
                    'bank_account_name' => $request->bank_account_name,
                    'bank_account_number' => $request->bank_account_number,
                    'gender' => $request->gender,
                    'plant_id' => $detail['plant_id'],
                    'address' => $request->address ?? '-',
                    'phone' => $request->phone ?? '-',
                    'join_date' => now(),
                    'status_active' => $request->status_active ?? 1,
                    'employee_photo' => $photoPath,
                ]);
            }

            // Sync accessible tenants based on details + any extra?
            // For now, let's assume accessible tenants are derived from details OR we keep the explicit field?
            // The previous implementation had 'accessible_tenants'.
            // If we have multiple details, the user implicitly has access to those tenants.
            // But they might need access to tenants where they don't have a specific role yet?
            // Let's keep 'accessible_tenants' if provided, OR merge with details' tenants.

            $accessibleTenants = $request->accessible_tenants ?? [];
            foreach ($request->details as $detail) {
                $section = Section::findOrFail($detail['section_id']);
                if (!in_array($section->customer_id, $accessibleTenants)) {
                    $accessibleTenants[] = $section->customer_id;
                }
            }

            foreach ($accessibleTenants as $tenantId) {
                \App\Models\TenantOwner::create([
                    'user_id' => $user->id,
                    'customer_id' => $tenantId,
                    'is_active' => true,
                ]);
            }

            DB::commit();

            // Log activity
            ActivityLogService::logCreate('users', $user->id, [
                'username' => $request->username,
                'email' => $request->email,
                'employee_name' => $request->full_name,
                'details_count' => count($request->details),
            ]);

            return response()->json(['success' => 'User created successfully.']);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('User creation failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create user: ' . $e->getMessage()], 500); // Return exception message for debugging
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        // Load the full relationship chain for the edit form
        $user = User::with(['userDetails.position.section.dept', 'userDetails.role', 'tenants'])->findOrFail($id);

        $response = [
            'user' => $user,
            'details' => $user->userDetails, // Return all details
            'accessible_tenants' => $user->tenants->pluck('customer_id'),
        ];

        return response()->json($response);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'username' => 'required|unique:users,username,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
            'full_name' => 'required',
            'details' => 'required|array',
            'details.*.customer_id' => 'required|exists:customers,id',
            'details.*.dept_id' => 'required|exists:depts,id',
            'details.*.section_id' => 'required|exists:sections,id',
            'details.*.position_id' => 'required|exists:positions,id',
            'details.*.role_id' => 'required|exists:roles,id',
            'gender' => 'required|in:Male,Female',
            'details.*.plant_id' => 'required|exists:plants,id',
            'nip' => 'nullable|string|max:255',
            'employee_status' => 'nullable|string|in:Tetap,Kontrak,Borongan',
            'blacklist_note' => 'nullable|string',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:255',
            'status_active' => 'nullable|boolean',
            'employee_photo' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();

        try {
            $user = User::findOrFail($id);

            // Capture old values for logging
            $oldValues = [
                'username' => $user->username,
                'email' => $user->email,
                'name' => $user->name,
                'details_count' => $user->userDetails()->count(),
            ];

            $user->update([
                'username' => $request->username,
                'email' => $request->email,
                'name' => $request->full_name,
            ]);

            if ($request->filled('password')) {
                $user->update(['password' => Hash::make($request->password)]);
            }

            // Sync User Details
            // Strategy: Get existing details, compare with request details.
            // Since we don't have IDs in the request details (unless we add them), we might need to delete all and recreate?
            // Or try to match?
            // Recreating is safer for consistency but loses history if we had any (created_at).
            // But UserDetail is mostly a snapshot.
            // Let's try to update if ID exists, create if not.
            // The frontend should send IDs for existing details.

            $existingIds = collect($request->details)->pluck('id')->filter()->toArray();

            // Delete details not in the request
            $user->userDetails()->whereNotIn('id', $existingIds)->delete();

            foreach ($request->details as $detail) {
                $section = Section::findOrFail($detail['section_id']);
                TenantService::assertAccess($section->customer_id);

                $data = [
                    'position_id' => $detail['position_id'],
                    'role_id' => $detail['role_id'],
                    'customer_id' => $section->customer_id,
                    'employee_name' => $request->full_name,
                    'employee_id' => $request->username,
                    'nip' => $request->nip,
                    'employee_status' => $request->employee_status,
                    'blacklist_note' => $request->blacklist_note,
                    'bank_name' => $request->bank_name,
                    'bank_account_name' => $request->bank_account_name,
                    'bank_account_number' => $request->bank_account_number,
                    'gender' => $request->gender,
                    'plant_id' => $detail['plant_id'],
                    'address' => $request->address ?? '-',
                    'phone' => $request->phone ?? '-',
                    'status_active' => $request->status_active ?? 1,
                ];

                if ($request->hasFile('employee_photo')) {
                    $data['employee_photo'] = $request->file('employee_photo')->store('employee_photos', 'public');
                } elseif ($user->userDetails()->exists()) {
                    // Keep existing photo if not uploaded
                    // Actually, we are iterating details.
                    // If we are updating, we should keep the old one.
                    // But here we are constructing data for update/create.
                    // If we don't include 'employee_photo' in $data, it won't be updated (which is good).
                    // But for create, we need a default.
                } else {
                    $data['employee_photo'] = 'default.png';
                }

                if (isset($detail['id']) && $detail['id']) {
                    $user->userDetails()->where('id', $detail['id'])->update($data);
                } else {
                    $data['join_date'] = now();
                    $user->userDetails()->create($data);
                }
            }

            // Sync Accessible Tenants
            $accessibleTenants = $request->accessible_tenants ?? [];

            // Add tenants from details
            foreach ($request->details as $detail) {
                $section = Section::findOrFail($detail['section_id']);
                if (!in_array($section->customer_id, $accessibleTenants)) {
                    $accessibleTenants[] = $section->customer_id;
                }
            }

            // Sync tenant owners
            // Delete ones not in the list
            \App\Models\TenantOwner::where('user_id', $user->id)
                ->whereNotIn('customer_id', $accessibleTenants)
                ->delete();

            // Add new ones
            foreach ($accessibleTenants as $tenantId) {
                \App\Models\TenantOwner::firstOrCreate(
                    ['user_id' => $user->id, 'customer_id' => $tenantId],
                    ['is_active' => true]
                );
            }

            DB::commit();

            ActivityLogService::logUpdate('users', $user->id, $oldValues, [
                'username' => $request->username,
                'email' => $request->email,
                'name' => $request->full_name,
                'details_count' => count($request->details),
            ]);

            return response()->json(['success' => 'User updated successfully.']);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('User update failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update user: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $user = User::findOrFail($id);
            TenantService::assertAccess(optional($user->userDetail)->customer_id);
            $oldValues = $user->toArray();
            $oldValues['user_detail'] = $user->userDetail->toArray();

            $user->userDetail->delete();
            $user->delete();

            DB::commit();

            // Log activity
            ActivityLogService::logDelete('user_details', $id, $oldValues);

            return response()->json(['success' => 'User deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('User deletion failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete user.'], 500);
        }
    }
}
