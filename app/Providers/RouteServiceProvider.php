<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        // Register the middleware group for admin
        Route::middlewareGroup('admin', [
            \App\Http\Middleware\AdminMiddleware::class,
        ]);

        parent::boot();
    }

    
}

