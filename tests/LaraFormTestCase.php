<?php

namespace Tests;

use LaraTest\Traits\AccessProtectedTraits;
use LaraTest\Traits\MockTraits;
use phpmock\MockBuilder;
use Tests\TestCase;

abstract class LaraFormTestCase extends TestCase
{
    use MockTraits,AccessProtectedTraits;

    /**
     * @param $class
     * @param $object
     * @param $prop
     * @throws \ReflectionException
     */
    protected function assertClassAttributeInstanceOf($class, $object, $prop)
    {
        $this->assertInstanceOf($class, $this->getProtectedAttributeOf($object, $prop));
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

    /**
     * @param $namespace
     * @param $functionName
     * @param bool $value
     * @return \phpmock\Mock
     * @throws \phpmock\MockEnabledException
     */
    protected function mockGlobalFunction($namespace, $functionName, $value = true)
    {
        $builder = new MockBuilder();
        $builder->setNamespace($namespace);
        $builder->setName($functionName);
        $builder->setFunction(function () use ($value) {
            return $value;
        });
        $mock = $builder->build();
        $mock->enable();
        return $mock;
    }
}