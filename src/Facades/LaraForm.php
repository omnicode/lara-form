<?php
declare(strict_types=1);

namespace LaraForm\Facades;

use Illuminate\Support\Facades\Facade;

class LaraForm extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'laraform';
    }
}