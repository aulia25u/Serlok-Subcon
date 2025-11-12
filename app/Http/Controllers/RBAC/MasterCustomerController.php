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
            $data = MasterCustomer::with('tenantOwner')->select('*');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('tenant_name', function(MasterCustomer $masterCustomer) {
                    return $masterCustomer->tenantOwner ? $masterCustomer->tenantOwner->name : 'N/A';
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

        return view('master_customer.index', compact('isInternal', 'currentCustomerId', 'currentTenantOwner'));
    }

    // Create method no longer needed as we use modal

    public function store(Request $request)
    {
        $isInternal = TenantService::isInternal();
        $currentCustomerId = TenantService::currentCustomerId();

        if (!$isInternal && $currentCustomerId) {
            $currentTenantOwner = \App\Models\TenantOwner::where('customer_id', $currentCustomerId)->first();
            $request->merge(['tenant_id' => $currentTenantOwner ? $currentTenantOwner->id : null]);
        } else {
            $request->merge(['tenant_id' => $request->tenant_id ?: null]);
        }

        $request->validate([
            'tenant_id' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value && !\DB::table('tenant_owners')->where('id', $value)->exists()) {
                        $fail('The selected tenant id is invalid.');
                    }
                },
            ],
            'customer_name' => 'required',
            'customer_code' => 'required|unique:master_customers,customer_code',
            'address' => 'required',
            'npwp' => 'required',
        ]);

        MasterCustomer::create($request->all());

        return redirect()->route('rbac.master-customer')
                         ->with('success','Master Customer created successfully.');
    }

    public function show(MasterCustomer $masterCustomer)
    {
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
            $currentTenantOwner = \App\Models\TenantOwner::where('customer_id', $currentCustomerId)->first();
            $request->merge(['tenant_id' => $currentTenantOwner ? $currentTenantOwner->id : null]);
        } else {
            $request->merge(['tenant_id' => $request->tenant_id ?: null]);
        }

        $request->validate([
            'tenant_id' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value && !\DB::table('tenant_owners')->where('id', $value)->exists()) {
                        $fail('The selected tenant id is invalid.');
                    }
                },
            ],
            'customer_name' => 'required',
            'customer_code' => 'required|unique:master_customers,customer_code,'.$masterCustomer->id,
            'address' => 'required',
            'npwp' => 'required',
        ]);

        $masterCustomer->update($request->all());

        return redirect()->route('rbac.master-customer')
                         ->with('success','Master Customer updated successfully');
    }

    public function destroy(MasterCustomer $masterCustomer)
    {
        $masterCustomer->delete();

        return response()->json(['success'=>'Master Customer deleted successfully.']);
    }
}
