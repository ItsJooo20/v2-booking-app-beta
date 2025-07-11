<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        $users = $this->userService->getActiveUsers($request);
        return view('admin.users-index', compact('users'));
    }

    public function deleted(Request $request)
    {
        $users = $this->userService->getDeletedUsers($request);
        return view('admin.deleted-users', compact('users'));
    }

    public function create()
    {
        return view('admin.users-create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,technician,user,headmaster',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = $this->userService->createUser($validated);

        return redirect()->route('users.list')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('admin.users-edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        // $tanggal = Carbon::now();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,technician,user,headmaster',
            'phone' => 'nullable|string|max:20',
            // 'email_verified_at' => $tanggal,
        ]);

        $this->userService->updateUser($user, $validated);

        return redirect()->route('users.list')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $this->userService->deactivateUser($user);
        return redirect()->route('users.list')
            ->with('success', 'User deactivated successfully.');
    }

    public function restore($id)
    {
        $user = $this->userService->restoreUser($id);
        return redirect()->route('users.list')
            ->with('success', 'User reactivated successfully.');
    }
}