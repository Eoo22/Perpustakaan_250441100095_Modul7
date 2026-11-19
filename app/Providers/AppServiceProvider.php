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
        Gate::define('kelola-buku', function (User $user) {
            if (method_exists($user, 'hasAnyRole')) {
                return $user->hasAnyRole(['admin', 'petugas']) || in_array($user->role, ['admin', 'petugas']);
            }
            return in_array($user->role, ['admin', 'petugas']);
        });

        // Gate akses-admin umum
        Gate::define('akses-admin', function ($user) {
            return $user->role === 'admin' || $user->role === 'petugas' || (method_exists($user, 'hasRole') && $user->hasRole('admin'));
        });
    }
}
