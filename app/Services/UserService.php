<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function getActiveUsers(Request $request)
    {
        $query = User::where('is_active', 1);

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }

        return $query->orderBy('name')->paginate(10)->withQueryString();
    }

    public function getDeletedUsers(Request $request)
    {
        $query = User::where('is_active', 0);

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }

        return $query->orderBy('name')->paginate(10)->withQueryString();
    }

    public function createUser(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'phone' => $data['phone'],
            'is_active' => true,
            'email_verified_at' => Carbon::now(),
        ]);
    }

    public function updateUser(User $user, array $data)
    {
        $user->name = $data['name'];
        $user->email = $data['email'];
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->role = $data['role'];
        $user->phone = $data['phone'];
        $user->email_verified_at = Carbon::now();
        $user->save();
    }

    public function deactivateUser(User $user)
    {
        $user->is_active = false;
        $user->save();
    }

    public function restoreUser($id)
    {
        $user = User::findOrFail($id);
        $user->is_active = true;
        $user->save();
        return $user;
    }
}