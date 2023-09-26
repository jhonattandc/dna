<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
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
        Paginator::useBootstrap();
        
        Http::macro('q10', function (array $headers) {
            return Http::withHeaders(array_merge(
                ['Cache-Control' => 'no-cache'],
                $headers
            ))->baseUrl(env('Q10_URL'));
        });

        Http::macro('clientify', function (array $headers) {
            return Http::withHeaders(array_merge(
                ['Authorization' => 'token '.env('CLIENTIFY_API_KEY')],
                $headers
            ))->baseUrl(env('CLIENTIFY_URL'));
        });

        Http::macro('thinkific', function (array $headers) {
            return Http::withHeaders(array_merge(
                [
                    'X-Auth-API-Key' => env('THINKIFIC_API_KEY'),
                    'X-Auth-Subdomain' => env('THINKIFIC_SUBDOMAIN')
                ],
                $headers
            ))->baseUrl(env('THINKIFIC_URL'));
        });
    }
}
