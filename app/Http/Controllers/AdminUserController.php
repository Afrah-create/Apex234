<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $priority = ['admin', 'supplier', 'vendor', 'retailer'];
        $query = User::with('roles');
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        $users = $query->get()->sortBy(function($user) use ($priority) {
            $userRoles = $user->roles->pluck('name')->toArray();
            foreach ($priority as $index => $role) {
                if (in_array($role, $userRoles)) {
                    return $index;
                }
            }
            return count($priority);
        })->values();
        $perPage = 10;
        $page = $request->input('page', 1);
        $paginatedUsers = new \Illuminate\Pagination\LengthAwarePaginator(
            $users->forPage($page, $perPage),
            $users->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        $employees = Employee::all();
        $vendors = Vendor::all();
        $vendorApplicants = \App\Models\VendorApplicant::whereIn('status', ['validated', 'pending'])->get();
        return view('admin.users.index', ['users' => $paginatedUsers, 'employees' => $employees, 'vendors' => $vendors, 'vendorApplicants' => $vendorApplicants]);
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
            'password' => 'required|string|min:8',
            'mobile' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'photo_url' => 'nullable|url',
        ]);
        $validated['is_active'] = $request->has('is_active');
        $validated['password'] = bcrypt($validated['password']);
        User::create($validated);
        return Redirect::route('admin.users.index')->with('status', 'User created!');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = \App\Models\Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
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
        if ($request->has('roles')) {
            $user->roles()->sync($request->input('roles'));
        } else {
            $user->roles()->detach();
        }
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