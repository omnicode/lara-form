<?php
namespace LaraForm\ServiceProvider;

use Illuminate\Support\ServiceProvider;
use LaraForm\Elements\Components\CheckBox;
use LaraForm\Elements\Components\Inputs\Hidden;
use LaraForm\Elements\Components\Inputs\Input;
use LaraForm\Elements\Components\Inputs\RadioButton;
use LaraForm\Elements\Components\Inputs\Password;
use LaraForm\Elements\Components\Inputs\Submit;
use LaraForm\Elements\Components\Label;
use LaraForm\Elements\Components\Select;
use LaraForm\Elements\Components\Textarea;
use LaraForm\FormBuilder;
use LaraForm\FormProtection;
use LaraForm\Middleware\LaraFormMiddleware;

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
     *
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
            return new FormProtection(
            );
        });
    }

    /**
     *
     */
    protected function registerFormElements()
    {
        $this->app->singleton('laraform.element.inputs.password', function ($app) {
            return new Password();
        });
        $this->app->singleton('laraform.element.inputs.submit', function ($app) {
            return new Submit();
        });
        $this->app->singleton('laraform.element.inputs.hidden', function ($app) {
            return new Hidden();
        });
        $this->app->singleton('laraform.element.inputs.input', function ($app) {
            return new Input();
        });
        $this->app->singleton('laraform.element.radio.button', function ($app) {
            return new RadioButton();
        });
        $this->app->singleton('laraform.element.checkbox', function ($app) {
            return new CheckBox();
        });
        $this->app->singleton('laraform.element.textarea', function ($app) {
            return new Textarea();
        });
        $this->app->singleton('laraform.element.select', function ($app) {
            return new Select();
        });
        $this->app->singleton('laraform.element.label', function ($app) {
            return new Label();
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
                $app['laraform.element.inputs.password'],
                $app['laraform.element.inputs.submit'],
                $app['laraform.element.inputs.hidden'],
                $app['laraform.element.inputs.input'],
                $app['laraform.element.radio.button'],
                $app['laraform.element.checkbox'],
                $app['laraform.element.textarea'],
                $app['laraform.element.select'],
                $app['laraform.element.label']
            );
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['laraform'];
    }
}
