<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PasswordResetByTokenController extends Controller
{
    public function showForm(Request $request)
    {
        return view('auth.password-reset-token');
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|alpha_num|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $record = DB::table('password_resets')->where('email', $request->email)->first();
        if (!$record || !\Illuminate\Support\Facades\Hash::check($request->token, $record->token)) {
            return back()->withErrors(['token' => 'The reset code is invalid or has expired.']);
        }

        $user = \App\Models\User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'No user found for this email.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the password reset record after successful reset
        DB::table('password_resets')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('status', 'Your password has been reset!');
    }
} 