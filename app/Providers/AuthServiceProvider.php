<?php

namespace App\Providers;

use App\Models\User;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

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

        Gate::define('manage:users', function (User $user = null) {
            // many to many check if user has permission
            return $user->permissions->pluck('gate')->contains('manage:users');
        });

        Gate::define('manage:campus', function (User $user = null) {
            return $user->permissions->pluck('gate')->contains('manage:campus');
        });

        Gate::define('manage:prosegur', function (User $user = null) {
            return $user->permissions->pluck('gate')->contains('manage:prosegur');
        });

        Gate::define('manage:horizon', function (User $user = null) {
            return $user->permissions->pluck('gate')->contains('manage:horizon');
        });

        Gate::define('manage:automations', function (User $user = null) {
            return $user->permissions->pluck('gate')->contains('manage:automations');
        });
    }
}
