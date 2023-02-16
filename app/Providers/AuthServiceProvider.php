<?php

namespace App\Providers;

use App\Extensions\DocumentUserProvider;
use App\Models\User;
use App\Services\Auth\DocumentGuard;
use App\Services\Contracts\DocumentServiceInterface;
use App\Services\DocumentCollection;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

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

        /**
         * Custom Auth Provider use json file
         */

        $this->app->bind(DocumentCollection::class, function ($app) {
            $file = storage_path('public/users/users.json');
            return new DocumentCollection($file);
        });

        $this->app->bind(User::class, function ($app) {
            return new User($app->make(DocumentCollection::class));
        });

        /**
         * Add Custom Guard Provider
         */
        Auth::provider('document', function ($app, array $config) {
            return new DocumentUserProvider($app->make(User::class));
        });

        Gate::define('mutu', function ($user) {
            return $user->type == "mutu";
        });

        Gate::define('ppi', function ($user) {
            return $user->type == "ppi";
        });

        Gate::define('supersu', function ($user) {
            return $user->type == "supersu";
        });

        // dd(Gate::abilities());
    }

    public function register()
    {
        $this->app->bind(
            DocumentServiceInterface::class,
            DocumentCollection::class
        );
    }
}
