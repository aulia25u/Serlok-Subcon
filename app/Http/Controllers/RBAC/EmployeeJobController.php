<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmployeeJob;
use App\Models\Outgoing;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use App\Services\TenantService;
use App\Services\ActivityLogService;

class EmployeeJobController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = EmployeeJob::with(['outgoing.masterItem', 'user', 'inspector'])->select('employee_jobs.*');

            $currentCustomerId = TenantService::currentCustomerId();
            if ($currentCustomerId) {
                // Filter by tenant via Outgoing -> MasterItem -> TenantOwner
                $query->whereHas('outgoing.masterItem.tenantOwner', function ($q) use ($currentCustomerId) {
                    $q->where('customer_id', $currentCustomerId);
                });
            }

            if ($request->filled('date_range')) {
                $dates = explode(' - ', $request->date_range);
                if (count($dates) == 2) {
                    $query->whereBetween('created_datetime', [$dates[0], $dates[1]]);
                }
            }

            if ($request->filled('status')) {
                $query->where('surat_jalan_status', $request->status);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('outgoing_item', function ($row) {
                    return $row->outgoing && $row->outgoing->masterItem ? $row->outgoing->masterItem->item_name : 'N/A';
                })
                ->addColumn('employee_name', function ($row) {
                    return $row->user ? $row->user->name : 'N/A';
                })
                ->addColumn('inspector_name', function ($row) {
                    return $row->inspector ? $row->inspector->name : 'N/A';
                })
                ->editColumn('created_datetime', function ($row) {
                    return $row->created_datetime ? $row->created_datetime->format('Y-m-d H:i') : '';
                })
                ->editColumn('start_datetime', function ($row) {
                    return $row->start_datetime ? $row->start_datetime->format('Y-m-d H:i') : '';
                })
                ->editColumn('finished_datetime', function ($row) {
                    return $row->finished_datetime ? $row->finished_datetime->format('Y-m-d H:i') : '';
                })
                ->addColumn('outgoing_qty', function ($row) {
                    return $row->outgoing ? $row->outgoing->quantity : 'N/A';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button data-id="' . $row->id . '" class="btn btn-primary btn-sm employee-job-edit-btn mr-1"><i class="fas fa-edit"></i></button>';
                    $btn .= '<button data-id="' . $row->id . '" class="btn btn-danger btn-sm employee-job-delete-btn"><i class="fas fa-trash"></i></button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $currentCustomerId = TenantService::currentCustomerId();
        // Get Outgoings for dropdown (only those that are verified? or all? Let's show all for now, maybe filter by tenant)
        $outgoingsQuery = Outgoing::with(['masterItem', 'assignedUser']);
        if ($currentCustomerId) {
            $outgoingsQuery->whereHas('masterItem.tenantOwner', function ($q) use ($currentCustomerId) {
                $q->where('customer_id', $currentCustomerId);
            });
        }
        $outgoings = $outgoingsQuery->orderBy('created_at', 'desc')->get();

        // Get Users for Inspector dropdown
        if ($currentCustomerId) {
            $users = User::whereHas('userDetail', function ($q) use ($currentCustomerId) {
                $q->where('customer_id', $currentCustomerId);
            })->get();
        } else {
            $users = User::all();
        }

        return view('rbac.employee_job.index', compact('outgoings', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'outgoing_id' => 'required|exists:outgoings,id',
            'start_datetime' => 'required|date',
            'finished_datetime' => 'required|date|after_or_equal:start_datetime',
            'qty_ok' => 'required|integer|min:0',
            'qty_ng' => 'required|integer|min:0',
            'qty_ng_customer' => 'required|integer|min:0',
            'inspector_id' => 'required|exists:users,id',
            'surat_jalan_status' => 'nullable|string',
        ]);

        $outgoing = Outgoing::findOrFail($request->outgoing_id);

        // RBAC Check
        $currentCustomerId = TenantService::currentCustomerId();
        if ($currentCustomerId) {
            if ($outgoing->masterItem->tenantOwner->customer_id !== $currentCustomerId) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        $job = EmployeeJob::create([
            'outgoing_id' => $request->outgoing_id,
            'user_id' => $outgoing->user_id, // Taken from Outgoing Assign To
            'created_datetime' => $outgoing->outgoing_date, // Taken from Outgoing Date
            'start_datetime' => $request->start_datetime,
            'finished_datetime' => $request->finished_datetime,
            'qty_ok' => $request->qty_ok,
            'qty_ng' => $request->qty_ng,
            'qty_ng_customer' => $request->qty_ng_customer,
            'inspector_id' => $request->inspector_id,
            'surat_jalan_status' => $request->surat_jalan_status,
        ]);

        // Log activity
        ActivityLogService::logCreate('employee_jobs', $job->id, [
            'outgoing_id' => $request->outgoing_id,
            'start_datetime' => $request->start_datetime,
            'qty_ok' => $request->qty_ok,
        ]);

        return response()->json(['success' => 'Employee Job created successfully.']);
    }

    public function edit($id)
    {
        $job = EmployeeJob::with('outgoing.masterItem.tenantOwner')->findOrFail($id);

        $currentCustomerId = TenantService::currentCustomerId();
        if ($currentCustomerId) {
            $jobCustomerId = $job->outgoing->masterItem->tenantOwner->customer_id ?? null;
            if ($jobCustomerId !== $currentCustomerId) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        return response()->json($job);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'outgoing_id' => 'required|exists:outgoings,id',
            'start_datetime' => 'required|date',
            'finished_datetime' => 'required|date|after_or_equal:start_datetime',
            'qty_ok' => 'required|integer|min:0',
            'qty_ng' => 'required|integer|min:0',
            'qty_ng_customer' => 'required|integer|min:0',
            'inspector_id' => 'required|exists:users,id',
            'surat_jalan_status' => 'nullable|string',
        ]);

        $job = EmployeeJob::with('outgoing.masterItem.tenantOwner')->findOrFail($id);

        // RBAC Check
        $currentCustomerId = TenantService::currentCustomerId();
        if ($currentCustomerId) {
            if ($job->outgoing->masterItem->tenantOwner->customer_id !== $currentCustomerId) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        // Prepare update data
        // Note: If outgoing_id changes, user_id and created_datetime should also update technically, 
        // but typically one wouldn't change the parent outgoing record easily. 
        // However, let's keep consistency.
        $outgoing = Outgoing::findOrFail($request->outgoing_id);

        // Double check new outgoing RBAC
        if ($currentCustomerId) {
            if ($outgoing->masterItem->tenantOwner->customer_id !== $currentCustomerId) {
                return response()->json(['error' => 'Unauthorized for selected Outgoing'], 403);
            }
        }

        $oldValues = $job->toArray();
        $job->update([
            'outgoing_id' => $request->outgoing_id,
            'user_id' => $outgoing->user_id,
            'created_datetime' => $outgoing->outgoing_date,
            'start_datetime' => $request->start_datetime,
            'finished_datetime' => $request->finished_datetime,
            'qty_ok' => $request->qty_ok,
            'qty_ng' => $request->qty_ng,
            'qty_ng_customer' => $request->qty_ng_customer,
            'inspector_id' => $request->inspector_id,
            'surat_jalan_status' => $request->surat_jalan_status,
        ]);

        // Log activity
        $newValues = $job->toArray();
        ActivityLogService::logUpdate('employee_jobs', $job->id, $oldValues, $newValues);

        return response()->json(['success' => 'Employee Job updated successfully.']);
    }

    public function destroy($id)
    {
        $job = EmployeeJob::with('outgoing.masterItem.tenantOwner')->findOrFail($id);

        // RBAC Check
        $currentCustomerId = TenantService::currentCustomerId();
        if ($currentCustomerId) {
            if ($job->outgoing->masterItem->tenantOwner->customer_id !== $currentCustomerId) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        $oldValues = $job->toArray();
        $job->delete();

        // Log activity
        ActivityLogService::logDelete('employee_jobs', $id, $oldValues);

        return response()->json(['success' => 'Employee Job deleted successfully.']);
    }
}
