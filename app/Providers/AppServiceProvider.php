<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;

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
        // ✅ បង្ខំឱ្យប្រើ HTTPS គ្រប់ Route ទាំងអស់ ប្រសិនបើប្រព័ន្ធមិនមែនកំពុងរត់នៅលើ Local (កុំព្យូទ័រផ្ទាល់ខ្លួន)
        if (config('app.env') !== 'local') {
            URL::forceScheme('https');
        }

        // ✅ បញ្ជូនទិន្នន័យលំនាំដើមទៅកាន់ Navbar (កូដចាស់របស់បង)
        View::composer('layouts.navbar', function ($view) {
            $view->with([
                'activeMenus' => [],
                'activeSubMenus' => []
            ]);
        });
    }
}