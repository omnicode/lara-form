<?php

namespace Tests\Stores;

use LaraForm\Stores\BindStore;
use phpmock\MockBuilder;
use Tests\Core\BaseStoreTest;

class BindStoreTest extends BaseStoreTest
{
    /**
     * @var
     */
    protected $bindStore;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();
        if (empty($this->bindStore)) {
            $this->bindStore = $this->newBindStore('dotGet');
        };
    }

    /**
     * @throws \ReflectionException
     */
    public function testSetModel()
    {
        $this->bindStore->setModel('value');
        $returned = $this->getProtectedAttributeOf($this->bindStore, 'data');
        $this->assertEquals('value', $returned);
    }

    /**
     *
     */
    public function testGet()
    {
        $this->methodWillReturn('value', 'dotGet', $this->bindStore);
        $returned = $this->bindStore->get('name');
        $this->assertEquals('value', $returned);
    }

    /**
     *
     */
    public function testData()
    {
        $this->bindStore->setModel('value');
        $returned = $this->bindStore->data();
        $this->assertEquals('value', $returned);
    }

    /**
     *
     */
    public function testBotGet()
    {
        $bindStore = $this->newBindStore('dataGet');
        $this->methodWillReturnArgument(1, 'dataGet', $bindStore);
        $returned = $this->invokeMethod($bindStore, 'dotGet', ['user.name', null]);
        $this->assertEquals(['user', 'name'], $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testDataGetWhenKeyCountZero()
    {
        $args = ['model', [], null];
        $bindStore = $this->newBindStore(null);
        $returned = $this->invokeMethod($bindStore, 'dataGet', $args);
        $this->assertEquals('model', $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testDataGetWhenTargetIsArray()
    {
        $args = [['field-1', 'field-2'], ['user'], null];
        $bindStore = $this->newBindStore(['arrayGet']);
        $this->methodWillReturnTrue('arrayGet', $bindStore);
        $returned = $this->invokeMethod($bindStore, 'dataGet', $args);
        $this->assertTrue($returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testDataGetWhenTargetIsObject()
    {
        $args = [$this, ['user'], null];
        $bindStore = $this->newBindStore(['objectGet']);
        $this->methodWillReturnTrue('objectGet', $bindStore);
        $returned = $this->invokeMethod($bindStore, 'dataGet', $args);
        $this->assertTrue($returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testDataGetWhenReturnDefault()
    {
        $args = ['', ['user'], 'default'];
        $bindStore = $this->newBindStore();
        $returned = $this->invokeMethod($bindStore, 'dataGet', $args);
        $this->assertEquals('default', $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testArrayGetWhenEmptyInTarget()
    {
        $args = [['email' => 'email@example.com'], ['user'], 'default'];
        $bindStore = $this->newBindStore();
        $returned = $this->invokeMethod($bindStore, 'arrayGet', $args);
        $this->assertEquals('default', $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testArrayGetWhenExistInTarget()
    {
        $args = [['user' => 'jon'], ['user'], 'default'];
        $bindStore = $this->newBindStore('dataGet');
        $this->methodWillReturnTrue('dataGet', $bindStore);
        $returned = $this->invokeMethod($bindStore, 'arrayGet', $args);
        $this->assertTrue($returned);
    }

    /**
     * @throws \ReflectionException
     * @throws \phpmock\MockEnabledException
     */
    public function testObjectGetWhenNotProperty()
    {
        $returned = $this->getObjectTesting('property_exists');
        $this->assertEquals('default', $returned);
    }

    /**
     * @throws \ReflectionException
     * @throws \phpmock\MockEnabledException
     */
    public function testObjectGetWhenPropertyExist()
    {
        $returned = $this->getObjectTesting('property_exists', true);
        $this->assertTrue($returned);
    }

    /**
     * @throws \ReflectionException
     * @throws \phpmock\MockEnabledException
     */
    public function testObjectGetWhenExistGetMethod()
    {
        $returned = $this->getObjectTesting('method_exists', true);
        $this->assertTrue($returned);
    }

    /**
     * @param $func
     * @param bool $val
     * @return mixed
     * @throws \ReflectionException
     * @throws \phpmock\MockEnabledException
     */
    private function getObjectTesting($func,$val = false)
    {

        $builder = new MockBuilder();
        $builder->setNamespace("LaraForm\Stores");
        $builder->setName($func);
        $builder->setFunction(function () use ($val){
            return $val;
        });
        $mock = $builder->build();
        $mock->enable();
        $bindStore = $this->newBindStore('dataGet');
        $args = [$bindStore, ['data'], 'default'];
        if ($val) {
            $this->methodWillReturnTrue('dataGet', $bindStore);;
        }
        $returned = $this->invokeMethod($bindStore, 'objectGet', $args);
        $mock->disable();
        return $returned;
    }
    
    /**
     * @param null $methods
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function newBindStore($methods = null)
    {
        return $this->newInstance(BindStore::class, [], $methods);
    }
}
