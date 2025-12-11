<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            $request->authenticate();

            // Log successful login
            $user = Auth::user();
            $tenantId = null;
            if ($user->userDetail && $user->userDetail->customer_id) {
                $tenantId = $user->userDetail->customer_id;
            }

            \App\Models\ActivityLog::create([
                'user_id' => $user->id,
                'tenant_id' => $tenantId,
                'action' => 'login',
                'table_name' => 'users',
                'record_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'new_values' => ['username' => $request->username, 'status' => 'success']
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Log failed login
            // Try to find if user exists to log user_id (optional, but good for tracking)
            $user = \App\Models\User::where('username', $request->username)->first();

            \App\Models\ActivityLog::create([
                'user_id' => $user ? $user->id : null,
                'tenant_id' => null, // Cannot reliably determine tenant if login fails
                'action' => 'login_failed',
                'table_name' => 'users',
                'record_id' => null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'new_values' => ['username' => $request->username, 'status' => 'failed']
            ]);

            throw $e;
        }

        $request->session()->regenerate();

        // Check if user's tenant is active
        $user = Auth::user();
        $userDetail = $user->userDetail; // Assuming relationship exists on User model or we fetch it

        // If relationship is not directly on User, we might need to fetch it like in TenantService
        if (!$userDetail) {
            $userDetail = \App\Models\UserDetail::where('user_id', $user->id)->first();
        }

        if ($userDetail && $userDetail->customer) {
            if (!$userDetail->customer->is_active) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->back()->withInput($request->only('username'))->with('error_modal', 'Your tenant account is inactive.');
            }
        }

        $intended = $request->session()->pull('url.intended', route('dashboard'));
        $request->session()->put('two_factor_intended_url', $intended);
        $request->session()->put('two_factor_passed', false);

        return redirect()->route('two-factor.challenge');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->forget(['two_factor_passed', 'two_factor_intended_url']);
        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
