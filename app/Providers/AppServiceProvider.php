<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Http::macro('q10', function (array $headers) {
            return Http::withHeaders(array_merge(
                ['Cache-Control' => 'no-cache'],
                $headers
            ))->baseUrl('https://api.q10.com/v1');
        });
    }
}
