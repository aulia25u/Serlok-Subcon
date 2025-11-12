<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    private function ensureInternal(): void
    {
        if (!TenantService::isInternal()) {
            abort(403, 'Only internal administrators can manage customers.');
        }
    }

    public function index(Request $request)
    {
        $this->ensureInternal();

        if ($request->ajax()) {
            $query = Customer::query()
                ->when($request->start_date, function ($q) use ($request) {
                    return $q->whereDate('created_at', '>=', $request->start_date);
                })
                ->when($request->end_date, function ($q) use ($request) {
                    return $q->whereDate('created_at', '<=', $request->end_date);
                });

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('is_active_label', function ($row) {
                    return $row->is_active
                        ? '<span class="badge badge-success">Active</span>'
                        : '<span class="badge badge-secondary">Inactive</span>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d-m-Y H:i:s');
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button class="btn btn-sm btn-primary edit-btn" data-id="' . $row->id . '">
                                <i class="fas fa-edit"></i> Edit
                            </button>';
                    $btn .= ' <button class="btn btn-sm btn-danger delete-btn" data-id="' . $row->id . '">
                                <i class="fas fa-trash"></i> Delete
                            </button>';
                    return $btn;
                })
                ->rawColumns(['action', 'is_active_label'])
                ->make(true);
        }

        return view('rbac.customer.index');
    }

    public function store(Request $request)
    {
        $this->ensureInternal();

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_code' => 'required|string|max:255|unique:customers,customer_code',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        Customer::create([
            'customer_name' => $request->customer_name,
            'customer_code' => $request->customer_code,
            'contact_person' => $request->contact_person,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'is_active' => $request->boolean('is_active'),
        ]);

        return response()->json(['success' => 'Customer created successfully.']);
    }

    public function edit($id)
    {
        $this->ensureInternal();

        $customer = Customer::findOrFail($id);
        return response()->json($customer);
    }

    public function update(Request $request, $id)
    {
        $this->ensureInternal();

        $customer = Customer::findOrFail($id);

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('customers')->ignore($id),
            ],
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $customer->update([
            'customer_name' => $request->customer_name,
            'customer_code' => $request->customer_code,
            'contact_person' => $request->contact_person,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'is_active' => $request->boolean('is_active'),
        ]);

        return response()->json(['success' => 'Customer updated successfully.']);
    }

    public function destroy($id)
    {
        $this->ensureInternal();

        $customer = Customer::findOrFail($id);
        $customer->delete();

        return response()->json(['success' => 'Customer deleted successfully.']);
    }
}
