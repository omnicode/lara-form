<?php

namespace LaraForm\ServiceProvider;

use LaraForm\Facades\LaraForm;
use LaraForm\FormBuilder;
use LaraForm\FormProtection;
use LaraForm\Middleware\LaraFormMiddleware;
use LaraForm\Stores\BindStore;
use LaraForm\Stores\ErrorStore;
use LaraForm\Stores\OldInputStore;
use LaraForm\Stores\OptionStore;
use LaraSupport\LaraServiceProvider;

class LaraFormServiceProvider extends LaraServiceProvider
{
    /**
     *
     */
    public function register()
    {
        $this->registerAlias('LaraForm', LaraForm::class);
        $this->registerMiddleware(LaraFormMiddleware::class);
        $this->registerSingletons([
            'laraform.protection' => FormProtection::class,
            'laraform.error' => ErrorStore::class,
            'laraform.oldInput' => OldInputStore::class,
            'laraform.options' => OptionStore::class,
            'laraform.bind' => BindStore::class,
            'laraform' => FormBuilder::class,
        ]);
    }

    /**
     *
     */
    public function boot()
    {
        $this->mergeConfig(__DIR__);
        $this->mergeConfig(__DIR__, 'lara_form_core', false);
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
