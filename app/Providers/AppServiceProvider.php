<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\ServiceProvider;

use Laravel\Passport\PassportServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Passport::ignoreRoutes();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

    }
}
