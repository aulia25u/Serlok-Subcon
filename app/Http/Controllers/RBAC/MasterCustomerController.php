<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use App\Models\MasterCustomer;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MasterCustomerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = MasterCustomer::with('customer')->select('*');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('customer_name', function(MasterCustomer $masterCustomer) {
                    return $masterCustomer->customer ? $masterCustomer->customer->customer_name : 'N/A';
                })
                ->addColumn('action', function($row){
                    $btn = '<a href="javascript:void(0)" data-id="'.$row->id.'" class="edit btn btn-primary btn-sm">Edit</a> ';
                    $btn .= '<a href="javascript:void(0)" data-id="'.$row->id.'" class="delete btn btn-danger btn-sm">Delete</a>';
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

        MasterCustomer::create($request->all());

        return response()->json(['success' => 'Master Customer created successfully.']);
    }

    public function show(MasterCustomer $masterCustomer)
    {
        TenantService::assertAccess($masterCustomer->customer_id);
        return view('master_customer.show',compact('masterCustomer'));
    }

    public function edit(MasterCustomer $masterCustomer)
    {
        return response()->json($masterCustomer);
    }

    public function update(Request $request, MasterCustomer $masterCustomer)
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
            'customer_code' => 'required|unique:master_customers,customer_code,'.$masterCustomer->id,
            'address' => 'required',
            'npwp' => 'required',
        ]);

        $masterCustomer->update($request->all());

        return response()->json(['success' => 'Master Customer updated successfully']);
    }

    public function destroy(MasterCustomer $masterCustomer)
    {
        TenantService::assertAccess($masterCustomer->customer_id);
        $oldValues = $masterCustomer->toArray();

        $masterCustomer->delete();

        // Log activity
        ActivityLogService::logDelete('master_customers', $masterCustomer->id, $oldValues);

        return response()->json(['success' => 'Master Customer deleted successfully']);
    }
}
