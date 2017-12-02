<?php

namespace LaraForm\ServiceProvider;

use Illuminate\Support\ServiceProvider;
use LaraForm\FormBuilder;
use LaraForm\FormProtection;
use LaraForm\Middleware\LaraFormMiddleware;
use LaraForm\Stores\ErrorStore;
use LaraForm\Stores\OldInputStore;

class LaraFormServiceProvider extends ServiceProvider
{
    /**
     *
     */
    public function register()
    {
        $this->registerFormProtection();
        $this->registerFormElements();
        $this->registerFormBuilder();
        $this->registerMiddleware(LaraFormMiddleware::class);
    }

    /**
     * Register the Debugbar Middleware
     * @param  string $middleware
     */
    protected function registerMiddleware($middleware)
    {
        $router = $this->app['router'];
        $router->pushMiddlewareToGroup('web', $middleware);
    }


    /**
     *
     */
    protected function registerFormProtection()
    {
        $this->app->singleton('laraform.protection', function ($app) {
            return new FormProtection();
        });
    }


    /**
     *
     */
    protected function registerFormElements()
    {
        $this->app->singleton('laraform.error', function ($app) {
            return new ErrorStore();
        });
        $this->app->singleton('laraform.oldInput', function ($app) {
            return new OldInputStore();
        });
    }

    /**
     *
     */
    protected function registerFormBuilder()
    {

        $this->app->singleton('laraform', function ($app) {
            return new FormBuilder(
                $app['laraform.protection'],
                $app['laraform.error'],
                $app['laraform.oldInput']
            );
        });
    }

    /**
     * Get the services provided by the provider.
     * @return array
     */
    public function provides()
    {
        return ['laraform'];
    }
}
