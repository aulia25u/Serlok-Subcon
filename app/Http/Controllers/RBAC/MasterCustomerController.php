<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use App\Models\MasterCustomer;
use App\Services\ActivityLogService;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MasterCustomerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = MasterCustomer::with('customer')->select('master_customers.*');
            $query = TenantService::scopeQueryByCustomer($query);

            // Date Filter
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('tenant_name', function (MasterCustomer $masterCustomer) {
                    return $masterCustomer->customer ? $masterCustomer->customer->customer_name : 'N/A';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('d-m-Y H:i:s') : '';
                })
                ->editColumn('updated_at', function ($row) {
                    return $row->updated_at ? $row->updated_at->format('d-m-Y H:i:s') : '';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button data-id="' . $row->id . '" class="btn btn-primary btn-sm master_customer-edit-btn me-1">Edit</button> ';
                    $btn .= '<button data-id="' . $row->id . '" class="btn btn-danger btn-sm master_customer-delete-btn">Delete</button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        $isInternal = TenantService::isInternal();
        $currentCustomerId = TenantService::currentCustomerId();
        $currentTenantOwner = null;
        if (!$isInternal && $currentCustomerId) {
            $currentTenantOwner = \App\Models\TenantOwner::where('customer_id', $currentCustomerId)->first();
        }

        $customers = \App\Models\Customer::all();

        return view('master_customer.index', compact('isInternal', 'currentCustomerId', 'currentTenantOwner', 'customers'));
    }

    // Create method no longer needed as we use modal

    public function store(Request $request)
    {
        $isInternal = TenantService::isInternal();
        $currentCustomerId = TenantService::currentCustomerId();

        if (!$isInternal && $currentCustomerId) {
            $request->merge(['customer_id' => $currentCustomerId]);
        } else {
            $request->merge(['customer_id' => $request->customer_id ?: null]);
        }

        $request->validate([
            'customer_id' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value && !\DB::table('customers')->where('id', $value)->exists()) {
                        $fail('The selected customer id is invalid.');
                    }
                },
            ],
            'customer_name' => 'required',
            'customer_code' => 'required|unique:master_customers,customer_code',
            'address' => 'required',
            'npwp' => 'required',
        ]);

        $masterCustomer = MasterCustomer::create($request->all());

        // Log activity
        ActivityLogService::logCreate('master_customers', $masterCustomer->id, [
            'customer_name' => $request->customer_name,
            'customer_code' => $request->customer_code,
            'address' => $request->address,
            'npwp' => $request->npwp,
            'customer_id' => $masterCustomer->customer_id,
        ]);

        return response()->json(['success' => 'Master Customer created successfully.']);
    }

    public function show(MasterCustomer $masterCustomer)
    {
        TenantService::assertAccess($masterCustomer->customer_id);
        return view('master_customer.show', compact('masterCustomer'));
    }

    public function edit($id)
    {
        $masterCustomer = MasterCustomer::findOrFail($id);
        TenantService::assertAccess($masterCustomer->customer_id);
        return response()->json($masterCustomer);
    }

    public function update(Request $request, $id)
    {
        $masterCustomer = MasterCustomer::findOrFail($id);
        TenantService::assertAccess($masterCustomer->customer_id);

        $isInternal = TenantService::isInternal();
        $currentCustomerId = TenantService::currentCustomerId();

        if (!$isInternal && $currentCustomerId) {
            $request->merge(['customer_id' => $currentCustomerId]);
        } else {
            $request->merge(['customer_id' => $request->customer_id ?: null]);
        }

        $request->validate([
            'customer_id' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value && !\DB::table('customers')->where('id', $value)->exists()) {
                        $fail('The selected customer id is invalid.');
                    }
                },
            ],
            'customer_name' => 'required',
            'customer_code' => 'required|unique:master_customers,customer_code,' . $masterCustomer->id,
            'address' => 'required',
            'npwp' => 'required',
        ]);

        $oldValues = $masterCustomer->toArray();
        $masterCustomer->update($request->all());

        // Log activity
        $newValues = $masterCustomer->toArray();
        ActivityLogService::logUpdate('master_customers', $masterCustomer->id, $oldValues, $newValues);

        return response()->json(['success' => 'Master Customer updated successfully']);
    }

    public function destroy($id)
    {
        $masterCustomer = MasterCustomer::findOrFail($id);

        TenantService::assertAccess($masterCustomer->customer_id);
        $oldValues = $masterCustomer->toArray();

        $masterCustomer->delete();

        // Log activity
        ActivityLogService::logDelete('master_customers', $masterCustomer->id, $oldValues);

        return response()->json(['success' => 'Master Customer deleted successfully']);
    }
}
