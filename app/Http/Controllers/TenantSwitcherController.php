<?php

namespace App\Http\Controllers;

use App\Models\TenantOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantSwitcherController extends Controller
{
    public function switch(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
        ]);

        $user = Auth::user();
        $newCustomerId = $request->customer_id;

        // Verify ownership
        $isOwner = TenantOwner::where('user_id', $user->id)
            ->where('customer_id', $newCustomerId)
            ->where('is_active', true)
            ->exists();

        if (!$isOwner) {
            return back()->with('error', 'You do not have permission to switch to this tenant.');
        }

        // Update active tenant in session
        session(['current_tenant_id' => $newCustomerId]);

        return back()->with('success', 'Switched tenant successfully.');
    }
}
