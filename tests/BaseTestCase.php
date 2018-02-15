<?php

namespace Tests\LaraForm;

use Illuminate\Container\Container;
use Illuminate\Support\Collection;
use LaraTest\Traits\AssertionTraits;
use LaraTest\Traits\MockTraits;
use Tests\TestCase;

abstract class BaseTestCase extends TestCase
{
    use AssertionTraits,MockTraits;

    /**
     * @param $class
     * @param string $methods
     * @return \PHPUnit_Framework_MockObject_MockObject
     * @throws \InvalidArgumentException
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
     * @throws \ReflectionException
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
     * @throws \ReflectionException
     */
    public function getProtectedMethod(&$object, $methodName, $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }

    /**
     * @param $object
     * @param $methodName
     * @param array $parameters
     * @return mixed
     * @throws \ReflectionException
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    /**
     * @param $obj
     * @param $prop
     * @return mixed
     * @throws \ReflectionException
     */
    public function getProtectedAttributeOf($obj, $prop)
    {
        $property = $this->protectedProp($obj, $prop);
        return $property->getValue($obj);
    }

    public function setProtectedAttributeOf($obj, $prop, $value)
    {
        $property = $this->protectedProp($obj, $prop);
        return $property->setValue($obj,$value);
    }

    private function protectedProp($obj, $prop)
    {
        $reflection = new \ReflectionClass($obj);
        $property = $reflection->getProperty($prop);
        $property->setAccessible(true);
        return $property;
    }
    /**
     * @return PHPUnit_Framework_MockObject_Stub_ReturnArguments
     */
    public static function returnArguments()
    {
        return new \PHPUnit_Framework_MockObject_Stub_ReturnArguments();
    }

    /**
     * @param $className
     * @param array $args
     * @param null $methods
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function newInstance($className,$args = [],$methods = null)
    {
        if (!empty($methods) && !is_array($methods)) {
            $methods = [$methods];
        }
        $instance = $this->getMockBuilder($className)
            ->setConstructorArgs($args)
            ->setMethods($methods)
            ->getMock();
        return $instance;
    }
}