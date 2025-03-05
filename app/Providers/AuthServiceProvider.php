<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{

    /**
     * The policy mappings for the application.
     *
     * @var array
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

        // Passport::routes();

        Passport::routes(null, ['middleware' => [
            'universal',
            InitializeTenancyByDomain::class
        ]]);

        Passport::loadKeysFrom(base_path('storage'));

        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });
        /**
         * Defining the user Roles
         */
        /*Gate::define('isAdmin', function ($user) {
            // if ($user->isAdmin()) {
            //     return true;
            // }

            // for simplicity
            return $user->type === 'admin';
        });

        Gate::define('isUser', function ($user) {
            // if ($user->isUser()) {
            //     return true;
            // }

            // for simplicity
            return $user->type === 'user';
        });*/
    }
}
