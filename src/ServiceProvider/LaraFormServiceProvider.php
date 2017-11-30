<?php

namespace LaraForm\ServiceProvider;

use Illuminate\Support\ServiceProvider;
use LaraForm\Elements\Widget;
use LaraForm\FormBuilder;
use LaraForm\FormProtection;
use LaraForm\MakeForm;
use LaraForm\Middleware\LaraFormMiddleware;

class LaraFormServiceProvider extends ServiceProvider
{
    /**
     *
     */
    public function register()
    {
        $this->registerFormProtection();
        $this->registerFormWidget();
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
    protected function registerFormWidget()
    {
        $this->app->singleton('widget', function ($app) {
            return new Widget();
        });
    }

    /**
     *
     */
    protected function registerFormElements()
    {
        $this->app->singleton('laraform.make-form', function ($app) {
            return new MakeForm($app['widget']);
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
                $app['laraform.make-form']
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
