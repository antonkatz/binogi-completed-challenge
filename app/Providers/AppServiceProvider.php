<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\RemoteServices\SpotifyTokenContainer;
use App\RemoteServices\SpotifyInfo;

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

        // The below would be the ideal, however, because SpotifyInfo needs a TokenContainer
        // I have not found a way to leverage Laravel's dependency injection
        // $this->app->singleton(SpotifyInfo::class, function ($app) {
        //     SpotifyInfo::setUp();
        // });
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
