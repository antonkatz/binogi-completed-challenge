<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\RemoteServices\SpotifyTokenContainer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(SpotifyTokenContainer::class, function ($app) {
            return new SpotifyTokenContainer();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
