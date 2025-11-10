<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\Customer; // Needed to get customer names for dropdowns
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Handle stats request for dashboard
            if ($request->has('stats')) {
                $active = Subscription::where('status', 'active')->count();
                $non_active = Subscription::where('status', 'non-active')->count();
                $penagihan = Subscription::where('status', 'penagihan')->count();
                $total_income = Subscription::sum('income');
                return response()->json(['active' => $active, 'non_active' => $non_active, 'penagihan' => $penagihan, 'total_income' => $total_income]);
            }

            if ($request->has('penagihan')) {
                $penagihanSubscriptions = Subscription::with('customer')
                    ->where('status', 'penagihan')
                    ->orderBy('valid_until', 'asc')
                    ->get()
                    ->map(function ($subscription) {
                        return [
                            'customer_name' => $subscription->customer->name ?? 'N/A',
                            'valid_until_formatted' => $subscription->valid_until ? $subscription->valid_until->format('d M Y') : '',
                        ];
                    });

                return response()->json(['data' => $penagihanSubscriptions]);
            }

            $query = Subscription::with('customer');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('customer_name', function ($row) {
                    return $row->customer->name ?? 'N/A';
                })
                ->addColumn('invoice_date_formatted', function ($row) {
                    return $row->invoice_date ? $row->invoice_date->format('d M Y') : '';
                })
                ->addColumn('valid_until_formatted', function ($row) {
                    return $row->valid_until ? $row->valid_until->format('d M Y') : '';
                })
                ->addColumn('income_formatted', function ($row) {
                    return $row->income ? 'Rp ' . number_format($row->income, 0, ',', '.') : '-';
                })
                ->addColumn('status_badge', function ($row) {
                    $badgeClass = 'badge-secondary';
                    if ($row->status == 'active') {
                        $badgeClass = 'badge-success';
                    } elseif ($row->status == 'penagihan') {
                        $badgeClass = 'badge-warning';
                    }
                    return '<span class="badge ' . $badgeClass . '">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('invoice_pdf', function ($row) {
                    if ($row->invoice_path && Storage::exists($row->invoice_path)) {
                        return '<a href="' . Storage::url($row->invoice_path) . '" target="_blank" class="btn btn-sm btn-info"><i class="fas fa-file-pdf"></i> View PDF</a>';
                    }
                    return 'No PDF';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button class="btn btn-sm btn-info view-btn" data-id="' . $row->id . '">
                                <i class="fas fa-eye"></i> View
                            </button>';
                    $btn .= ' <button class="btn btn-sm btn-primary edit-btn" data-id="' . $row->id . '">
                                <i class="fas fa-edit"></i> Edit
                            </button>';
                    $btn .= ' <button class="btn btn-sm btn-danger delete-btn" data-id="' . $row->id . '">
                                <i class="fas fa-trash"></i> Delete
                            </button>';
                    return $btn;
                })
                ->rawColumns(['status_badge', 'invoice_pdf', 'action'])
                ->make(true);
        }

        $customers = Customer::all(); // For dropdown in add/edit forms
        return view('rbac.subscription.index', compact('customers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'invoice_date' => 'required|date',
            'status' => 'required|in:active,non-active,penagihan',
            'income' => 'required|numeric|min:0',
            'invoice_file' => 'required|file|mimes:pdf|max:2048', // Max 2MB
        ]);

        $invoiceDate = Carbon::parse($request->invoice_date);
        $validUntil = $invoiceDate->copy()->addDays(30);

        $invoicePath = $request->file('invoice_file')->store('public/invoices');

        Subscription::create([
            'customer_id' => $request->customer_id,
            'invoice_date' => $invoiceDate,
            'valid_until' => $validUntil,
            'status' => $request->status,
            'income' => $request->income,
            'invoice_path' => $invoicePath,
        ]);

        return response()->json(['success' => 'Subscription created successfully.']);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $subscription = Subscription::with('customer')->findOrFail($id);
        return response()->json($subscription);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $subscription = Subscription::findOrFail($id);
        return response()->json($subscription);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'invoice_date' => 'required|date',
            'status' => 'required|in:active,non-active,penagihan',
            'income' => 'required|numeric|min:0',
            'invoice_file' => 'nullable|file|mimes:pdf|max:2048', // Max 2MB, nullable for update
        ]);

        $subscription = Subscription::findOrFail($id);

        $invoiceDate = Carbon::parse($request->invoice_date);
        $validUntil = $invoiceDate->copy()->addDays(30);

        $data = [
            'customer_id' => $request->customer_id,
            'invoice_date' => $invoiceDate,
            'valid_until' => $validUntil,
            'status' => $request->status,
            'income' => $request->income,
        ];

        if ($request->hasFile('invoice_file')) {
            // Delete old file
            if ($subscription->invoice_path && Storage::exists($subscription->invoice_path)) {
                Storage::delete($subscription->invoice_path);
            }
            $data['invoice_path'] = $request->file('invoice_file')->store('public/invoices');
        }

        $subscription->update($data);

        return response()->json(['success' => 'Subscription updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $subscription = Subscription::findOrFail($id);

        // Delete associated file
        if ($subscription->invoice_path && Storage::exists($subscription->invoice_path)) {
            Storage::delete($subscription->invoice_path);
        }

        $subscription->delete();

        return response()->json(['success' => 'Subscription deleted successfully.']);
    }
}
