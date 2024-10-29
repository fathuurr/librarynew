<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
//    public function __construct()
//    {
//        $this->middleware(['role:super_admin']);
//    }

    public function index()
    {
        $users = User::with('roles')->get();
        $roles = Role::all();
        return view('users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'exists:roles,name']
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'balance' => $request->role === 'siswa' ? 10000 : 0
        ]);

        $user->assignRole($request->role);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => $request->password ? ['confirmed', Password::defaults()] : '',
            'role' => ['required', 'exists:roles,name'],
            'balance' => $user->hasRole('siswa') ? ['required', 'numeric', 'min:0'] : ''
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'balance' => $request->balance ?? $user->balance
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        // Update role
        $user->syncRoles([$request->role]);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diupdate');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus');
    }


}
