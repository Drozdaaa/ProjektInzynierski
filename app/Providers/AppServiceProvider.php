<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cookie\Middleware\EncryptCookies;

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
        $this->app->afterResolving(EncryptCookies::class, function ($middleware) {
            $middleware->disableFor('laravel_session');
            $middleware->disableFor('XSRF-TOKEN');
        });

        Paginator::useBootstrap();

        Gate::define('is-admin', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('is-user', function (User $user) {
            return $user->isUser();
        });

        Gate::define('is-manager', function (User $user) {
            return $user->isManager();
        });

        Gate::define('admin-or-manager', function (User $user) {
            return $user->isAdmin() || $user->isManager();
        });

        Gate::define('restaurant-owner', function (User $user, Restaurant $restaurant) {
            return $user->isAdmin() || ($user->isManager() && $restaurant->user_id === $user->id);
        });

        Gate::define('create-custom-menu', function (User $user) {
            return $user->isUser() || $user->isAdmin() || $user->isManager();
        });
    }
}
