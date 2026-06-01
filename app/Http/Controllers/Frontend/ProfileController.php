<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('frontend.dashboard.profile', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:60'],
            'last_name'  => ['required', 'string', 'max:60'],
            'email'      => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'headline'   => ['nullable', 'string', 'max:120'],
            'bio'        => ['nullable', 'string', 'max:1000'],
            'avatar'     => ['nullable', 'image', 'max:2048'],
            'locale'     => ['nullable', 'in:'.implode(',', array_keys(config('locales.available')))],
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) Storage::disk('public')->delete($user->avatar);
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        if (! empty($data['locale'])) {
            session(['locale' => $data['locale']]);
        }

        return back()->with('status', __('app.profile_update'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        Auth::user()->update(['password' => $request->password]);

        return back()->with('status', 'Пароль изменён.');
    }
}
