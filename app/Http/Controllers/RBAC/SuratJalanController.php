<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuratJalan;
use App\Models\MasterVariable;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\ActivityLogService;

class SuratJalanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = SuratJalan::with([
                'customer',
                'employeeJob.outgoing.masterItem',
                'employeeJob.user',
                'employeeJob.inspector'
            ]);

            // Tenant Scoping
            \App\Services\TenantService::scopeQueryByCustomer($query, 'tenant_id');

            // Date Range Filter
            if ($request->filled('date_range')) {
                $dates = explode(' - ', $request->date_range);
                if (count($dates) === 2) {
                    $query->whereBetween('surat_jalan_date', [$dates[0] . ' 00:00:00', $dates[1] . ' 23:59:59']);
                }
            }

            // Status Filter
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Customer Filter
            if ($request->filled('customer_id')) {
                $query->where('customer_id', $request->customer_id);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('surat_jalan_date', function ($row) {
                    return $row->surat_jalan_date ? $row->surat_jalan_date->format('Y-m-d H:i') : '';
                })
                ->addColumn('customer_name', function ($row) {
                    return $row->customer ? $row->customer->customer_name : 'N/A';
                })
                ->addColumn('warehouse_outgoing_id', function ($row) {
                    return $row->employeeJob ? $row->employeeJob->outgoing_id : 'N/A';
                })
                ->addColumn('made_by', function ($row) {
                    return $row->employeeJob && $row->employeeJob->user ? $row->employeeJob->user->name : 'N/A';
                })
                ->addColumn('inspect_by', function ($row) {
                    return $row->employeeJob && $row->employeeJob->inspector ? $row->employeeJob->inspector->name : 'N/A';
                })
                ->addColumn('delivery_to', function ($row) {
                    return $row->customer ? $row->customer->customer_name : 'N/A'; // Assuming logic for now
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button data-id="' . $row->id . '" class="btn btn-info btn-sm surat-jalan-detail-btn mr-1" title="Detail"><i class="fas fa-eye"></i></button>';
                    $btn .= '<button data-id="' . $row->id . '" class="btn btn-primary btn-sm surat-jalan-edit-btn mr-1" title="Edit"><i class="fas fa-edit"></i></button>';
                    $btn .= '<a href="' . route('rbac.surat-jalan.print', $row->id) . '" target="_blank" class="btn btn-secondary btn-sm mr-1" title="Print"><i class="fas fa-print"></i></a>';
                    //$btn .= '<button data-id="' . $row->id . '" class="btn btn-danger btn-sm surat-jalan-delete-btn"><i class="fas fa-trash"></i></button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        // Fetch Employee Jobs and Customers with Scoping
        $employeeJobs = \App\Models\EmployeeJob::with(['outgoing.masterItem'])->orderBy('created_at', 'desc');
        $customers = \App\Models\MasterCustomer::query();

        if (!\App\Services\TenantService::isInternal()) {
            $scopedCustomerId = \App\Services\TenantService::currentCustomerId();

            // Scope Employee Jobs (via Outgoing -> MasterItem -> TenantOwner -> Customer)
            $employeeJobs->whereHas('outgoing.masterItem.tenantOwner', function ($q) use ($scopedCustomerId) {
                $q->where('customer_id', $scopedCustomerId);
            });

            // Scope Customers (only self)
            $customers->where('id', $scopedCustomerId);
        }

        $employeeJobs = $employeeJobs->get();
        $customers = $customers->get();

        // Scope Users by Tenant for "Known By". 
        // Logic: Users who belong to this customer (via userDetail) OR are internal?
        // Usually, for Surat Jalan "Known By", we want employees of the tenant.
        $usersQuery = \App\Models\User::query();
        if (!\App\Services\TenantService::isInternal()) {
            $scopedCustomerId = \App\Services\TenantService::currentCustomerId();
            $usersQuery->whereHas('userDetail', function ($q) use ($scopedCustomerId) {
                $q->where('customer_id', $scopedCustomerId);
            });
        }
        $users = $usersQuery->get();

        return view('rbac.surat_jalan.index', compact('employeeJobs', 'customers', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_job_id' => 'required|exists:employee_jobs,id',
            'customer_id' => 'required|exists:master_customers,id',
            'known_by' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $documentNumber = $this->generateDocumentNumber();

            $suratJalan = SuratJalan::create([
                'document_number' => $documentNumber,
                'surat_jalan_date' => now(),
                'status' => $request->status ?? 'Draft',
                'employee_job_id' => $request->employee_job_id,
                'customer_id' => $request->customer_id,
                'tenant_id' => \App\Services\TenantService::resolveCustomerId(),
                'known_by' => $request->known_by,
            ]);

            // Log activity
            ActivityLogService::logCreate('surat_jalans', $suratJalan->id, [
                'document_number' => $suratJalan->document_number,
                'customer_id' => $suratJalan->customer_id,
                'status' => $suratJalan->status,
            ]);

            DB::commit();
            return response()->json(['success' => 'Surat Jalan created successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create Surat Jalan: ' . $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        $suratJalan = SuratJalan::with('employeeJob')->findOrFail($id);

        // Tenant Access Assertion
        \App\Services\TenantService::assertAccess($suratJalan->tenant_id);

        return response()->json($suratJalan);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'employee_job_id' => 'required|exists:employee_jobs,id',
            'customer_id' => 'required|exists:master_customers,id',
            'known_by' => 'nullable|string',
            'status' => 'required|string',
        ]);

        $suratJalan = SuratJalan::findOrFail($id);

        // Tenant Access Assertion
        \App\Services\TenantService::assertAccess($suratJalan->tenant_id);

        $oldValues = $suratJalan->toArray();
        $suratJalan->update([
            'status' => $request->status,
            'employee_job_id' => $request->employee_job_id,
            'customer_id' => $request->customer_id,
            'known_by' => $request->known_by,
        ]);

        // Log activity
        $newValues = $suratJalan->toArray();
        ActivityLogService::logUpdate('surat_jalans', $suratJalan->id, $oldValues, $newValues);

        return response()->json(['success' => 'Surat Jalan updated successfully.']);
    }

    // Detail method
    public function show($id)
    {
        $suratJalan = SuratJalan::with([
            'customer',
            'employeeJob.outgoing.masterItem',
            'employeeJob.user',
            'employeeJob.inspector'
        ])->findOrFail($id);

        // Tenant Access Assertion
        \App\Services\TenantService::assertAccess($suratJalan->tenant_id);

        return response()->json([
            'id_employee_jobs' => $suratJalan->employee_job_id,
            'item_information' => $suratJalan->employeeJob && $suratJalan->employeeJob->outgoing && $suratJalan->employeeJob->outgoing->masterItem ? $suratJalan->employeeJob->outgoing->masterItem->item_name : 'N/A',
            'qty_delivery' => $suratJalan->employeeJob ? $suratJalan->employeeJob->qty_ok : 0,
            // Include other details if needed for display
            'document_number' => $suratJalan->document_number,
        ]);
    }

    public function destroy($id)
    {
        $suratJalan = SuratJalan::findOrFail($id);

        // Tenant Access Assertion
        \App\Services\TenantService::assertAccess($suratJalan->tenant_id);

        $oldValues = $suratJalan->toArray();
        $suratJalan->delete();

        // Log activity
        ActivityLogService::logDelete('surat_jalans', $id, $oldValues);
        return response()->json(['success' => 'Surat Jalan deleted successfully.']);
    }

    private function generateDocumentNumber()
    {
        $query = MasterVariable::query()->where('variable_code', 'DOC_NUM_FORMAT');

        // Scope by Tenant
        \App\Services\TenantService::scopeQueryByCustomer($query, 'tenant_id');

        $formatVar = $query->first();
        $format = $formatVar ? $formatVar->variable_value : 'SJ/{Y}/{m}/{SEQ}';

        $now = now();
        $prefix = str_replace(
            ['{Y}', '{m}'],
            [$now->format('Y'), $now->format('m')],
            $format
        );

        // Split format by {SEQ} to get the part before matches
        $parts = explode('{SEQ}', $prefix);
        $searchPattern = $parts[0] . '%'; // e.g. SJ/2025/12/%

        // Find latest number matching this pattern
        $latest = SuratJalan::where('document_number', 'like', $searchPattern)
            ->orderByRaw('LENGTH(document_number) DESC') // Ensure we get the longest one (highest digits)
            ->orderBy('document_number', 'desc')
            ->first();

        $sequence = 1;
        if ($latest) {
            // Extract numeric part
            // Assuming the sequence is at the position of {SEQ}
            // Simple approach: remove the prefix part from the document number
            // This works if {SEQ} is at the end or we know the structure.
            // If {SEQ} is in the middle, it's harder.
            // Let's assume {SEQ} is the variable part.
            // We can treat $parts[0] as prefix and $parts[1] (if exists) as suffix.

            $docNum = $latest->document_number;
            $prefixStr = $parts[0];
            $suffixStr = isset($parts[1]) ? $parts[1] : '';

            $numStr = substr($docNum, strlen($prefixStr));
            if ($suffixStr) {
                $numStr = substr($numStr, 0, -strlen($suffixStr));
            }

            if (is_numeric($numStr)) {
                $sequence = intval($numStr) + 1;
            }
        }

        // Pad to 3 digits minimum? Or 4? Let's use 3 as a safe default or 4.
        // User didn't specify padding in format, but standard is usually 3-4.
        $paddedSeq = str_pad($sequence, 4, '0', STR_PAD_LEFT);

        return str_replace('{SEQ}', $paddedSeq, $prefix);
    }
    public function print($id)
    {
        $suratJalan = SuratJalan::with([
            'customer',
            'employeeJob.outgoing.masterItem.tenantOwner.customer',
            'employeeJob.user'
        ])->findOrFail($id);

        \App\Services\TenantService::assertAccess($suratJalan->tenant_id);

        return view('rbac.surat_jalan.print', compact('suratJalan'));
    }
}
