<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index()
    {
        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $users = User::all();
        return view('dashboard.users.index', compact('users'));
    }

    public function updateRole(Request $request, User $user)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'role' => 'required|in:admin,staff,customer'
        ]);

        $user->update([
            'role' => $request->role
        ]);

        return back()->with('success', 'User role updated successfully!');
    }

    public function toggleActive(Request $request, User $user)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $user->update([
            'is_active' => !$user->is_active
        ]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "User {$status} successfully!");
    }
}