<?php

namespace App\Providers;

//use App\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;


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
        //
        VerifyCsrfToken::except([
            '/get-updates'
        ]);

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
