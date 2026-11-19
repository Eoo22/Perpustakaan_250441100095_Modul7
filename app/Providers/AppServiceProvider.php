<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // SOAL 3: Gate kelola-buku hanya mengizinkan role admin dan petugas
        Gate::define('kelola-buku', function (User $user): bool {
            $hasRole = method_exists($user, 'hasAnyRole')
                ? $user->hasAnyRole(['admin', 'petugas'])
                : false;

            return $hasRole || in_array($user->role ?? null, ['admin', 'petugas'], true);
        });

        // Gate akses-admin umum
        Gate::define('akses-admin', function (User $user): bool {
            $isAdmin = method_exists($user, 'hasRole')
                ? $user->hasRole('admin')
                : false;

            return in_array($user->role ?? null, ['admin', 'petugas'], true) || $isAdmin;
        });
    }
}