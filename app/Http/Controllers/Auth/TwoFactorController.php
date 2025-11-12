<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Google2FAService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class TwoFactorController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();

        if (! $user) {
            return Redirect::route('login');
        }

        if ($request->session()->get('two_factor_passed')) {
            $intended = $request->session()->pull('two_factor_intended_url', route('dashboard'));

            return Redirect::to($intended);
        }

        return view('auth.two-factor-challenge', [
            'twoFactorEnabled' => (bool) $user->two_factor_enabled,
        ]);
    }

    public function verify(Request $request, Google2FAService $google2FA): RedirectResponse
    {
        $request->validate([
            'two_factor_code' => ['required', 'digits:6'],
        ]);

        $user = $request->user();

        if (! $user || ! $user->two_factor_enabled || ! $user->two_factor_secret) {
            return Redirect::route('two-factor.challenge');
        }

        if (! $google2FA->verify($user->two_factor_secret, $request->input('two_factor_code'))) {
            return Redirect::route('two-factor.challenge')
                ->withErrors(['two_factor_code' => 'The two-factor authentication code is invalid.']);
        }

        $request->session()->put('two_factor_passed', true);
        $intended = $request->session()->pull('two_factor_intended_url', route('dashboard'));

        return Redirect::to($intended);
    }

    public function skip(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user || $user->two_factor_enabled) {
            return Redirect::route('two-factor.challenge');
        }

        $request->session()->put('two_factor_passed', true);
        $intended = $request->session()->pull('two_factor_intended_url', route('dashboard'));

        return Redirect::to($intended);
    }
}
