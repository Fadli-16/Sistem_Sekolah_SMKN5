<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Define permissions for Perpustakaan (Library)
        Gate::define('manage-perpustakaan', function (User $user) {
            return in_array($user->role, ['super_admin', 'admin_perpus']);
        });
        
        // Define permissions for PPDB
        Gate::define('manage-ppdb', function (User $user) {
            return in_array($user->role, ['super_admin', 'admin_ppdb']);
        });
        
        // Define permissions for Sistem Akademik
        Gate::define('manage-sistem-akademik', function (User $user) {
            return in_array($user->role, ['super_admin', 'admin_sa']);
        });
        
        // Define permissions for Laboratorium
        Gate::define('manage-laboratorium', function (User $user) {
            return in_array($user->role, ['super_admin', 'admin_lab']);
        });
        
        // Define permissions for Magang
        Gate::define('manage-magang', function (User $user) {
            return in_array($user->role, ['super_admin', 'admin_magang']);
        });

        Gate::define('manage-wakil-perusahaan', function (User $user) {
    return $user->role === 'wakil_perusahaan';
        });     
        // Allow all authenticated users to view resources
        Gate::define('view-any-resource', function (User $user) {
            return true; // All authenticated users can view
        });
    }
}