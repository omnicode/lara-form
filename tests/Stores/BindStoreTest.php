<?php

namespace Tests\LaraForm\Stores;

use LaraForm\Stores\BindStore;
use Tests\LaraForm\Core\BaseStoreTest;

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
        $returned = $this->getProtectedMethod($bindStore, 'dotGet', ['user.name', null]);
        $this->assertEquals(['user', 'name'], $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testDataGetWhenKeyCountZero()
    {
        $args = ['model', [], null];
        $bindStore = $this->newBindStore(null);
        $returned = $this->getProtectedMethod($bindStore, 'dataGet', $args);
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
        $returned = $this->getProtectedMethod($bindStore, 'dataGet', $args);
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
        $returned = $this->getProtectedMethod($bindStore, 'dataGet', $args);
        $this->assertTrue($returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testDataGetWhenReturnDefault()
    {
        $args = ['', ['user'], 'default'];
        $bindStore = $this->newBindStore();
        $returned = $this->getProtectedMethod($bindStore, 'dataGet', $args);
        $this->assertEquals('default', $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testArrayGetWhenEmptyInTarget()
    {
        $args = [['email' => 'email@example.com'], ['user'], 'default'];
        $bindStore = $this->newBindStore();
        $returned = $this->getProtectedMethod($bindStore, 'arrayGet', $args);
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
        $returned = $this->getProtectedMethod($bindStore, 'arrayGet', $args);
        $this->assertTrue($returned);
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
