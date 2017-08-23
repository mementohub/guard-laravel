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
        //no need for a setup for now
        //$this->setupConfig();
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Static User Provider
        Auth::provider('static', function ($app, array $config) {
            return new StaticUserProvider($config['model']);
        });

        // Custom JWT Guard
        Auth::extend('jwt', function ($app, $name, array $config) {
            return new JwtGuard(Auth::createUserProvider($config['provider']));
        });
    }

    protected function setupConfig()
    {
        $source = realpath(__DIR__.'/../resources/config/imemento-auth.php');

        $this->publishes([$source => config_path('imemento-auth.php')]);

        $this->mergeConfigFrom($source, 'imemento-auth');
    }
}
