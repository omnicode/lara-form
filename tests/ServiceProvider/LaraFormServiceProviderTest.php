<?php

namespace Tests\LaraForm\ServiceProvider;

use LaraForm\FormBuilder;
use LaraForm\FormProtection;
use LaraForm\Middleware\LaraFormMiddleware;
use LaraForm\ServiceProvider\LaraFormServiceProvider;
use LaraForm\Stores\BindStore;
use LaraForm\Stores\ErrorStore;
use LaraForm\Stores\OldInputStore;
use LaraForm\Stores\OptionStore;
use Tests\LaraForm\BaseTestCase;


class LaraFormServiceProviderTest extends BaseTestCase
{

    /**
     * @var
     */
    protected $serviceProvider;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();
        if (empty($this->serviceProvider)) {
            $mthods = ['publishes'];
            $this->serviceProvider = $this->newServiceProvider($mthods);
        };
    }

    /**
     * 
     */
    public function testRegister()
    {
        $muckMethods = [
            'registerFormProtection',
            'registerMiddleware',
            'registerStores',
            'replaceConfig',
            'setCoreConfig',
            'registerFormBuilder'
        ];
        $serviceProvider = $this->newServiceProvider($muckMethods);
        $serviceProvider->register();
    }

    /**
     *
     */
    public function testBoot()
    {
        $this->methodWillReturnTrue('publishes', $this->serviceProvider);
        $this->serviceProvider->boot();
    }
    
    /**
     * @param null $methods
     * @return mixed
     */
    private function newServiceProvider($methods = null)
    {
        return $this->newInstance(LaraFormServiceProvider::class, [app()], $methods);
    }
}
