<?php

namespace LaraForm\Tests;

use Illuminate\Container\Container;
use Illuminate\Support\Collection;
use LaraTest\Traits\AssertionTraits;
use LaraTest\Traits\MockTraits;
use Tests\TestCase;

abstract class BaseTestCase extends TestCase
{
    use AssertionTraits,MockTraits;


    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param $class
     * @param string $methods
     * @return \PHPUnit_Framework_MockObject_MockObject
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws \PHPUnit_Framework_Exception
     * @throws \PHPUnit_Framework_MockObject_RuntimeException
     */
    protected function getMockObject($class, $methods = '')
    {
        $mockBuilder = $this->getMockBuilder($class)
            ->setConstructorArgs([app(Container::class), app(Collection::class)]);

        if ($this->getMethodsBy($methods)) {
            $mockBuilder->setMethods($methods);
        } else {
            $mockBuilder->setMethods(null);
        }
        return $mockBuilder->getMock();
    }

    /**
     * @param $methods
     * @return array|string
     */
    private function getMethodsBy($methods)
    {
        if (is_string($methods) && !empty(trim($methods))) {
            $methods = [$methods];
        }

        if (!empty($methods)) {
            return $methods;
        }

        return [];
    }

    /**
     * @param $class
     * @param $object
     * @param $service
     * @throws \PHPUnit_Framework_Exception
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    protected function assertClassAttributeInstanceOf($class, $object, $service)
    {
        $this->assertInstanceOf($class, $this->getProtectedAttributeOf($object, $service));
    }

    /**
     * @param $object
     * @param $methodName
     * @param array $parameters
     * @return mixed
     */
    public function getProtectedMethod(&$object, $methodName, $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }


}