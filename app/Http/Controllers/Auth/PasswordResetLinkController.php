<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => trans(Password::INVALID_USER)]);
        }

        // Generate a 6-character alphanumeric token
        $token = Str::upper(Str::random(6));

        // Store the token in the password_resets table
        DB::table('password_resets')->updateOrInsert(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => bcrypt($token),
                'created_at' => now(),
            ]
        );

        // Send the custom notification with the 6-character token
        $user->sendPasswordResetNotification($token);

        // Redirect to the token entry form with email pre-filled
        return redirect()->route('password.token.form', ['email' => $user->email])
            ->with('status', 'A reset code has been sent to your email. Please enter it below.');
    }
}
