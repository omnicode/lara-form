<?php

namespace LaraForm\ServiceProvider;

use Illuminate\Support\ServiceProvider;
use LaraForm\FormBuilder;
use LaraForm\FormProtection;
use LaraForm\Middleware\LaraFormMiddleware;
use LaraForm\Stores\ErrorStore;
use LaraForm\Stores\OldInputStore;
use LaraForm\Stores\OptionStore;

class LaraFormServiceProvider extends ServiceProvider
{
    /**
     *
     */
    public function register()
    {
        $this->registerFormProtection();
        $this->registerMiddleware(LaraFormMiddleware::class);
        $this->registerStores();
        $this->replaceConfig();
        $this->setCoreConfig();
        $this->registerFormBuilder();

    }

    /**
     *
     */
    public function boot()
    {
        $this->publishes([
            dirname(__DIR__) . '/Config/default.php' => config_path('lara_form.php'),
        ]);
    }

    /**
     * Create a core configuration
     */
    public function setCoreConfig()
    {
        $baseConfig = require_once dirname(__DIR__) . '/Config/core.php';
        $this->app['config']->set('lara_form_core', $baseConfig);
    }

    /**
     * Merge the given configuration with the existing configuration.
     */
    protected function replaceConfig()
    {
        $config = $this->app['config']->get('lara_form', []);
        $baseConfig = require_once dirname(__DIR__) . '/Config/default.php';

        $this->app['config']->set('lara_form', array_replace_recursive($baseConfig,$config));
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
     * Register the FormProtection
     */
    protected function registerFormProtection()
    {
        $this->app->singleton('laraform.protection', function ($app) {
            return new FormProtection();
        });
    }


    /**
     * Register the ErroeStore, OldInputStore and OptionStore
     */
    protected function registerStores()
    {
        $this->app->singleton('laraform.error', function ($app) {
            return new ErrorStore();
        });
        $this->app->singleton('laraform.oldInput', function ($app) {
            return new OldInputStore();
        });
        $this->app->singleton('laraform.options', function ($app) {
            return new OptionStore();
        });

    }

    /**
     * Register the FormBuilder
     */
    protected function registerFormBuilder()
    {

        $this->app->singleton('laraform', function ($app) {
            return new FormBuilder(
                $app['laraform.protection'],
                $app['laraform.error'],
                $app['laraform.oldInput'],
                $app['laraform.options']
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
