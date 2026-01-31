<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * Display all users.
     */
    public function index(Request $request): Response
    {
        $users = User::query()
            ->when($request->role, fn ($q, $role) => $q->where('role', $role))
            ->when($request->search, fn ($q, $search) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"))
            ->withCount(['properties', 'buyerTransactions', 'sellerTransactions'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $request->only(['role', 'search']),
            'roleOptions' => UserRole::cases(),
            'counts' => [
                'all' => User::count(),
                'buyers' => User::where('role', UserRole::BUYER)->count(),
                'sellers' => User::where('role', UserRole::SELLER)->count(),
                'admins' => User::where('role', UserRole::ADMIN)->count(),
            ],
        ]);
    }

    /**
     * Display user detail.
     */
    public function show(User $user): Response
    {
        $user->load([
            'properties' => fn ($q) => $q->with('media')->latest()->take(5),
            'buyerTransactions' => fn ($q) => $q->with('property:id,title')->latest()->take(5),
            'sellerTransactions' => fn ($q) => $q->with('property:id,title')->latest()->take(5),
            'requirements' => fn ($q) => $q->latest(),
        ]);

        $user->loadCount(['properties', 'buyerTransactions', 'sellerTransactions', 'favorites']);

        return Inertia::render('Admin/Users/Show', [
            'user' => $user,
        ]);
    }

    /**
     * Update user.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['sometimes', 'string'],
        ]);

        $user->update($validated);

        return back()->with('success', 'Data pengguna berhasil diperbarui');
    }

    /**
     * Toggle user active status.
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        // Don't allow deactivating self
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menonaktifkan akun sendiri');
        }

        // For now, we'll use email_verified_at as a simple toggle
        // In production, you might want a dedicated 'is_active' column
        if ($user->email_verified_at) {
            $user->update(['email_verified_at' => null]);
            $message = 'Pengguna berhasil dinonaktifkan';
        } else {
            $user->update(['email_verified_at' => now()]);
            $message = 'Pengguna berhasil diaktifkan';
        }

        return back()->with('success', $message);
    }
}
