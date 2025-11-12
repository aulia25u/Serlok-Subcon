<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\Google2FAService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user()->load([
            'userDetail.position.section.dept',
            'userDetail.role',
        ]);
        $photoUrl = null;

        if ($user->userDetail && $user->userDetail->employee_photo) {
            $photoPath = 'profile_photos/' . $user->userDetail->employee_photo;
            if (Storage::disk('public')->exists($photoPath)) {
                $photoUrl = Storage::url($photoPath);
            }
        }

        return view('profile.edit', compact('user', 'photoUrl'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Update User model
        $user->fill([
            'username' => $validated['username'],
            'email' => $validated['email'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Update UserDetail model
        if ($user->userDetail) {
            $user->userDetail->update([
                'employee_name' => $validated['employee_name'],
                'gender' => $validated['gender'],
                'position_id' => $validated['position_id'] ?? $user->userDetail->position_id,
            ]);
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function updatePhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'employee_photo' => ['required', 'image', 'max:2048'],
        ]);

        $user = $request->user();

        if (!$user->userDetail) {
            return Redirect::route('profile.edit')->withErrors(['employee_photo' => 'Please complete your profile before updating the photo.']);
        }

        $photo = $request->file('employee_photo');
        $photoPath = $photo->store('profile_photos', 'public');

        if ($user->userDetail->employee_photo) {
            $existing = 'profile_photos/' . $user->userDetail->employee_photo;
            if (Storage::disk('public')->exists($existing) && $user->userDetail->employee_photo !== 'default.png') {
                Storage::disk('public')->delete($existing);
            }
        }

        $user->userDetail->update([
            'employee_photo' => basename($photoPath),
        ]);

        return Redirect::route('profile.edit')->with('status', 'photo-updated');
    }

    public function prepareTwoFactor(Request $request, Google2FAService $google2FA): RedirectResponse
    {
        $user = $request->user();

        if ($user->two_factor_enabled) {
            return Redirect::route('profile.edit')->with('status', 'two-factor-already-enabled');
        }

        $request->session()->put('two_factor_secret_setup', $google2FA->generateSecretKey());

        return Redirect::route('profile.edit')->with('status', 'two-factor-prepared');
    }

    public function enableTwoFactor(Request $request, Google2FAService $google2FA): RedirectResponse
    {
        $request->validate([
            'two_factor_code' => ['required', 'digits:6'],
        ]);

        $secret = $request->session()->get('two_factor_secret_setup');

        if (! $secret || ! $google2FA->verify($secret, $request->input('two_factor_code'))) {
            return Redirect::route('profile.edit')->withErrors([
                'two_factor_code' => 'The provided code is invalid or the setup window has expired.',
            ]);
        }

        $request->user()->update([
            'two_factor_secret' => $secret,
            'two_factor_enabled' => true,
        ]);

        $request->session()->forget('two_factor_secret_setup');

        return Redirect::route('profile.edit')->with('status', 'two-factor-enabled');
    }

    public function disableTwoFactor(Request $request, Google2FAService $google2FA): RedirectResponse
    {
        $request->validate([
            'two_factor_code' => ['required', 'digits:6'],
        ]);

        $user = $request->user();

        if (! $user->two_factor_enabled || ! $user->two_factor_secret) {
            return Redirect::route('profile.edit')->with('status', 'two-factor-not-enabled');
        }

        if (! $google2FA->verify($user->two_factor_secret, $request->input('two_factor_code'))) {
            return Redirect::route('profile.edit')->withErrors([
                'two_factor_code' => 'The provided code is invalid.',
            ]);
        }

        $user->update([
            'two_factor_secret' => null,
            'two_factor_enabled' => false,
        ]);

        return Redirect::route('profile.edit')->with('status', 'two-factor-disabled');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
