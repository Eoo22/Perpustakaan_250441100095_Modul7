<?php

namespace App\Providers;

use App\Models\Peminjaman;
use App\Models\User;
use App\Policies\PeminjamanPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $isStaff = function (User $user): bool {
            $hasSpatieRole = method_exists($user, 'hasAnyRole')
                ? $user->hasAnyRole(['admin', 'petugas'])
                : false;

            return $hasSpatieRole || in_array($user->role ?? null, ['admin', 'petugas'], true);
        };

        if (class_exists(Peminjaman::class)) {
            Gate::policy(Peminjaman::class, PeminjamanPolicy::class);
        }

        // Soal 3: Gate pengelolaan buku untuk admin dan petugas
        Gate::define('kelola-buku', fn(User $user): bool => $isStaff($user));
    }
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            \URL::forceScheme('https');
        }
    }
}