<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;

class UsersController extends Controller
{
    // Daftar user
    public function index()
    {
        $users = User::all();
        return view('admin.users.indexUser', compact('users'));
    }

    // Simpan user baru
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email'    => 'required|email|unique:users',
            'address'  => 'required|string|max:255',
            'password' => 'required|min:6|confirmed',
            'role'     => 'required|string',
        ]);

        User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'address'  => $request->address,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        Alert::success('Berhasil!', 'User berhasil ditambahkan.');
        return redirect()->route('users.index');
    }

    // Update user
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'address'  => 'required|string|max:255',
            'role'     => 'required|string',
            'password' => 'nullable|min:6|confirmed',
        ]);

        $data = $request->only(['name', 'username', 'email', 'address', 'role']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        Alert::info('Diperbarui!', 'User berhasil diperbarui.');
        return redirect()->route('users.index');
    }

    // Hapus user
    public function destroy(User $user)
    {
        $user->delete();

        Alert::warning('Dihapus!', 'User berhasil dihapus.');
        return redirect()->route('users.index');
    }
}
