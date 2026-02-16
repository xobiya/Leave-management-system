<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::query()->with('roles')->orderBy('name', 'asc')->get();
        $roles = Role::query()->orderBy('name', 'asc')->get();

        return view('erp.admin.users', compact('users', 'roles'));
    }

    public function assignRole(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        $user->roles()->sync([$data['role_id']]);

        return back()->with('status', 'role-assigned');
    }
}
