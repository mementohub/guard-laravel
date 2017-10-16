<?php

namespace iMemento\Guard\Laravel;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->setupConfig();
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Static User Provider
        Auth::provider('static', function ($app, array $config) {
            return new StaticUserProvider($config['model'], config('permissions'));
        });

        // Custom JWT Guard
        Auth::extend('jwt', function ($app, $name, array $config) {
            return new JwtGuard(Auth::createUserProvider($config['provider']), app('request'));
        });
    }

    protected function setupConfig()
    {
        $source = realpath(__DIR__.'/../resources/config/permissions.php');

        $this->publishes([$source => config_path('permissions.php')], 'config');

        $this->mergeConfigFrom($source, 'permissions');
    }
}
