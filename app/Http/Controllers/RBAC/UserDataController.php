<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\Position;
use App\Models\Role;
use App\Models\Plant;
use App\Models\Section;
use App\Models\Dept;
use App\Services\ActivityLogService;
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
        if ($request->ajax()) {
            // Eager load the new, complex relationship chain
            $query = User::with([
                'userDetail.position.section.dept', // The full chain
                'userDetail.role',
            ])
            ->when($request->start_date, function ($q) use ($request) {
                return $q->whereDate('created_at', '>=', $request->start_date);
            })
            ->when($request->end_date, function ($q) use ($request) {
                return $q->whereDate('created_at', '<=', $request->end_date);
            });

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('no', function ($row) {
                    static $no = 0;
                    return ++$no;
                })
                ->addColumn('full_name', function ($row) {
                    return $row->userDetail->employee_name ?? '-';
                })
                ->addColumn('role_name', function ($row) {
                    return $row->userDetail->role->role_name ?? '-';
                })
                ->addColumn('dept_name', function ($row) {
                    // Access the department name through the nested relationships
                    return $row->userDetail->position->section->dept->dept_name ?? '-';
                })
                ->addColumn('section_name', function ($row) {
                    // Access the section name through the nested relationships
                    return $row->userDetail->position->section->section_name ?? '-';
                })
                ->addColumn('position_name', function ($row) {
                    // Access the position name through the nested relationship
                    return $row->userDetail->position->position_name ?? '-';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button class="btn btn-sm btn-primary edit-btn" data-toggle="modal" data-target="#addModal" data-id="' . $row->id . '">
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
        
        // Pass all necessary data for the dropdowns to the view
        $departments = Dept::all();
        $sections = Section::all();
        $positions = Position::all();
        $roles = Role::all();

        return view('rbac.user-data.index', compact('departments', 'sections', 'positions', 'roles'));
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
            'gender' => 'required|in:Male,Female',
            'dept_id' => 'required|exists:depts,id',
            'section_id' => 'required|exists:sections,id',
            'position_id' => 'required|exists:positions,id', // Validate position_id
            'role_id' => 'required|exists:roles,id'
            // 'plant_id' => 'required|exists:plants,id', // Removed plant_id validation
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
    
            UserDetail::create([
                'user_id' => $user->id,
                'position_id' => $request->position_id, // Store position_id on UserDetail
                'role_id' => $request->role_id,
                // 'plant_id' => $request->plant_id, // Removed plant_id
                'employee_name' => $request->full_name,
                'employee_id' => $request->username, // Use username as employee_id
                'gender' => $request->gender,
            ]);

            DB::commit();

            // Log activity
            ActivityLogService::logCreate('user_details', $user->userDetail->id, [
                'username' => $request->username,
                'email' => $request->email,
                'employee_name' => $request->full_name,
                'gender' => $request->gender,
                'position_id' => $request->position_id,
                'role_id' => $request->role_id,
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
        $user = User::with(['userDetail.position.section.dept', 'userDetail.role'])->findOrFail($id);

        $response = [
            'user' => $user,
            'sections' => collect(), // Default empty
            'positions' => collect(), // Default empty
        ];

        // Check if user has position and section relationships
        if ($user->userDetail && $user->userDetail->position && $user->userDetail->position->section) {
            $deptId = $user->userDetail->position->section->dept_id;
            $sectionId = $user->userDetail->position->section_id;

            $response['sections'] = Section::where('dept_id', $deptId)->get();
            $response['positions'] = Position::where('section_id', $sectionId)->get();
        } else {
            $response['message'] = 'User position data is incomplete. Please set department, section, and position.';
        }

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
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'full_name' => 'required|string|max:255',
            'gender' => 'required|in:Male,Female',
            'dept_id' => 'required|exists:depts,id',
            'section_id' => 'required|exists:sections,id',
            'position_id' => 'required|exists:positions,id', // Validate position_id
            'role_id' => 'required|exists:roles,id'
            // 'plant_id' => 'required|exists:plants,id', // Removed plant_id validation
        ]);

        DB::beginTransaction();

        try {
            $user = User::findOrFail($id);
            $oldValues = $user->toArray();
            $oldValues['user_detail'] = $user->userDetail->toArray();

            $user->update([
                'username' => $request->username,
                'email' => $request->email,
            ]);

            if ($request->password) {
                $user->update(['password' => Hash::make($request->password)]);
            }

            $user->userDetail->update([
                'position_id' => $request->position_id,
                'role_id' => $request->role_id,
                // 'plant_id' => $request->plant_id, // Removed plant_id
                'employee_name' => $request->full_name,
                'gender' => $request->gender,
            ]);

            DB::commit();

            // Log activity
            $newValues = $user->toArray();
            $newValues['user_detail'] = $user->userDetail->toArray();
            ActivityLogService::logUpdate('user_details', $user->userDetail->id, $oldValues, $newValues);

            return response()->json(['success' => 'User updated successfully.']);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('User update failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update user.'], 500);
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