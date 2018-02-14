<?php

namespace LaraForm\ServiceProvider;

use AdamWathan\BootForms\BootFormsServiceProvider;
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
use LaraSupport\LaraServiceProvider;

class LaraFormServiceProvider extends LaraServiceProvider
{
    /**
     *
     */
    public function register()
    {
        $this->registerProviders(BootFormsServiceProvider::class);
        $this->registerAliases(
            [
                'BootForm' => \AdamWathan\BootForms\Facades\BootForm::class,
                'LaraForm' => \LaraForm\Facades\LaraForm::class,
            ]
        );
        $this->registerFormProtection();
        $this->registerFormElements();
        $this->registerFormBuilder();
        $this->registerMiddleware(LaraFormMiddleware::class);
    }

    /**
     *
     */
    protected function registerFormProtection()
    {
        $this->registerSingleton('laraform.protection', FormProtection::class);
    }


    /**
     *
     */
    protected function registerFormElements()
    {
        $this->registerSingletons(
            [
                'laraform.element.inputs.password' => Password::class,
                'laraform.element.inputs.submit' => Submit::class,
                'laraform.element.inputs.hidden' => Hidden::class,
                'laraform.element.inputs.input' => Input::class,
                'laraform.element.radio.button' => RadioButton::class,
                'laraform.element.checkbox' => CheckBox::class,
                'laraform.element.textarea' => Textarea::class,
                'laraform.element.select' => Select::class,
                'laraform.element.label' => Label::class
            ]
        );
    }

    /**
     *
     */
    protected function registerFormBuilder()
    {
        $this->registerSingleton('laraform', FormBuilder::class);
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
