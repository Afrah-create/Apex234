<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'photo_url' => 'nullable|url',
        ]);
        $validated['is_active'] = $request->has('is_active');
        User::create($validated);
        return Redirect::route('admin.users.index')->with('status', 'User created!');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'mobile' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'photo_url' => 'nullable|url',
        ]);
        $validated['is_active'] = $request->has('is_active');
        $user->update($validated);
        return Redirect::route('admin.users.index')->with('status', 'User updated!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return Redirect::route('admin.users.index')->with('status', 'User deleted!');
    }

    public function loginAs($id)
    {
        $user = User::findOrFail($id);
        Auth::login($user);
        return redirect('/dashboard');
    }
} 