<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Show the form for creating a new user (admin only).
     */
    public function create(): View
    {
        $roles = User::ALLOWED_ROLES;
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user (admin only).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:'.implode(',', User::ALLOWED_ROLES)],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_admin' => false,
            'is_active' => true,
        ]);

        return redirect()->route('admin.dashboard')
            ->with('success', __('Потребителят беше обновен успешно!'));
    }

    /**
     * Show the form for editing a user (owner only).
     */
    public function edit(User $user): View
    {
        $roles = User::ALLOWED_ROLES;
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user (owner only).
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'role' => ['required', 'string', 'in:'.implode(',', User::ALLOWED_ROLES)],
            'is_active' => ['nullable', 'boolean'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ];

        $validated = $request->validate($rules);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'is_active' => $request->boolean('is_active'),
        ];
        if (! empty($validated['password'] ?? '')) {
            $data['password'] = Hash::make($validated['password']);
        }
        $user->update($data);

        if ($user->is_active === false) {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        return redirect()->route('admin.dashboard')
            ->with('success', __('Потребителят беше обновен успешно!'));
    }

    /**
     * Toggle user active status (owner only). Prevents deactivating self.
     */
    public function toggleActive(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', __('You cannot deactivate your own account.'));
        }

        $user->update(['is_active' => ! $user->is_active]);

        if (! $user->is_active) {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        $message = $user->is_active
            ? __('User :name has been activated.', ['name' => $user->name])
            : __('User :name has been deactivated.', ['name' => $user->name]);

        return back()->with('success', $message);
    }

    /**
     * Delete a user (admin only). Prevents deleting self.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', __('You cannot delete your own account.'));
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.dashboard')
            ->with('success', __('User :name has been deleted.', ['name' => $name]));
    }
}
