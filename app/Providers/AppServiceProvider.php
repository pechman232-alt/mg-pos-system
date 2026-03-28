<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View; // 1. Added this import

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
        // 2. This code sends empty arrays to the navbar so the @foreach doesn't crash
        View::composer('layouts.navbar', function ($view) {
            $view->with([
                'activeMenus' => [],
                'activeSubMenus' => []
            ]);
        });
    }
}