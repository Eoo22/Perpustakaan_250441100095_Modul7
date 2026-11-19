<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    private const ROLES = ['admin', 'petugas', 'siswa'];

    public function index(): View
    {
        $users = User::with('roles')->orderBy('name')->paginate(10);

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        return view('users.create', ['roles' => self::ROLES]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['nullable', Rule::in(self::ROLES)],
        ]);

        // Hanya yang berhak mengelola role yang boleh menentukan role
        $role = $request->user()->can('kelola-role-pengguna')
            ? ($data['role'] ?? 'siswa')
            : 'siswa';

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'], // di-hash otomatis oleh cast
        ]);

        $this->setRole($user, $role);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        return view('users.edit', [
            'user' => $user->load('roles'),
            'roles' => self::ROLES,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role' => ['nullable', Rule::in(self::ROLES)],
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];

        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }

        $user->save();

        if ($request->user()->can('kelola-role-pengguna') && ! empty($data['role'])) {
            // Admin tidak boleh mengubah role dirinya sendiri
            if ($user->id === $request->user()->id && $data['role'] !== 'admin') {
                return back()->with('error', 'Anda tidak dapat mengubah role akun Anda sendiri.');
            }

            $this->setRole($user, $data['role']);
        }

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
    }

    /** Set role Spatie, sinkronkan kolom "role" lama, siapkan anggota untuk siswa. */
    private function setRole(User $user, string $role): void
    {
        $user->syncRoles([$role]);
        $user->forceFill(['role' => $role])->save();

        if ($role === 'siswa') {
            $user->anggotaAtauBuat();
        }
    }
}