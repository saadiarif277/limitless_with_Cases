<?php

namespace App\Providers;

use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\App; // Correct facade

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
        Carbon::serializeUsing(function ($carbon) {
            return $carbon->format('F j, Y - h:i:s A T');
        });

        if (App::environment('production')) { // Fixed typo
            URL::forceScheme('https');
        }
    }
}
