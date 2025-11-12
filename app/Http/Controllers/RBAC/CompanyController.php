<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\TenantService;

class CompanyController extends Controller
{
    public function index()
    {
        $currentCustomerId = TenantService::currentCustomerId();

        $customers = TenantService::isInternal()
            ? Customer::orderBy('customer_name')->get()
            : Customer::where('id', $currentCustomerId)->get();

        return view('rbac.company.index', [
            'customers' => $customers,
            'currentCustomerId' => $currentCustomerId,
        ]);
    }
}
