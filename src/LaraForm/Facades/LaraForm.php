<?php
namespace LaraForm\Facades;

use Illuminate\Support\Facades\Facade;

class LaraForm extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laraform';
    }
}
