<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        $supervisors = User::whereHas('role', function($q) {
            $q->where('name', 'atasan');
        })->get();

        return view('admin.users.create', compact('roles', 'supervisors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'employee_id' => 'required|string|unique:users,employee_id',
            'base_salary' => 'required|numeric|min:0',
            'position' => 'required|string|max:100',
            'role_id' => 'required|exists:roles,id',
            'supervisor_id' => 'nullable|exists:users,id',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'employee_id' => $request->employee_id,
            'base_salary' => $request->base_salary,
            'position' => $request->position,
            'role_id' => $request->role_id,
            'supervisor_id' => $request->supervisor_id,
        ]);

        \App\Models\AuditLog::log('user_created', 'User', null);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created successfully');
    }

    public function show(User $user)
    {
        $user->load('role', 'supervisor', 'subordinates');
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $supervisors = User::whereHas('role', function($q) {
            $q->where('name', 'atasan');
        })->where('id', '!=', $user->id)->get();

        return view('admin.users.edit', compact('user', 'roles', 'supervisors'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'phone' => 'nullable|string|max:20',
            'employee_id' => 'required|string|unique:users,employee_id,' . $user->id,
            'base_salary' => 'required|numeric|min:0',
            'position' => 'required|string|max:100',
            'role_id' => 'required|exists:roles,id',
            'supervisor_id' => 'nullable|exists:users,id',
        ]);

        $oldValues = $user->toArray();

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'employee_id' => $request->employee_id,
            'base_salary' => $request->base_salary,
            'position' => $request->position,
            'role_id' => $request->role_id,
            'supervisor_id' => $request->supervisor_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        \App\Models\AuditLog::log('user_updated', 'User', $user->id, $oldValues, $user->toArray());

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        // Prevent deleting own account
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account');
        }

        $user->delete();

        \App\Models\AuditLog::log('user_deleted', 'User', $user->id);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted successfully');
    }
}
