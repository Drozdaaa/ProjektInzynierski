<?php

namespace App\Providers;

use App\Models\Restaurant;
use App\Models\User;
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

        //do wyrzucenia 
        Gate::define('can-create-event-in-restaurant', function (User $user, Restaurant $restaurant) {
            if ($user->isUser()) {
                return true;
            }

            if ($user->isManager()) {
                return $restaurant->user_id === $user->id;
            }

            return $user->isAdmin();
        });
    }
}
