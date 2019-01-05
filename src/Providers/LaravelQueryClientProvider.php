<?php

namespace Czbas23\LaravelQueryClient\Providers;

use Illuminate\Support\ServiceProvider;

class LaravelQueryClientProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('laravelqueryclient', function ($app) {
            return new \Czbas23\LaravelQueryClient\LaravelQueryClient;
        });
    }
}
