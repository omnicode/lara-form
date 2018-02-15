<?php

namespace Tests\LaraForm\Stores;

use LaraForm\Stores\ErrorStore;
use Tests\LaraForm\Core\BaseStoreTest;

class ErrorStoreTest extends BaseStoreTest
{
    /**
     * @var
     */
    protected $errorStore;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();
        if (empty($this->errorStore)) {
            $this->errorStore = $this->newErrorStore(['hasErrors','transformKey']);
        };
    }

    /**
     * @throws \ReflectionException
     */
    public function testConstruct()
    {
        $returned = $this->getProtectedAttributeOf($this->errorStore, 'session');
        $this->assertEquals(session(), $returned);
    }

    /**
     *
     */
    public function testHasErrorWhenEmptyErrors()
    {
        $this->methodWillReturnFalse('hasErrors', $this->errorStore);
        $returned = $this->errorStore->hasError('key');
        $this->assertFalse($returned);
    }

    /**
     *
     */
    public function testHasErrorWhenExistErrors()
    {
        $this->withSession(['errors'=>['field'=>'message']]);
        $errorStore = $this->newErrorStore(['hasErrors', 'transformKey', 'getErrors','has']);
        $this->methodWillReturnTrue('hasErrors', $errorStore);
        $this->methodWillReturn('field', 'transformKey', $errorStore);
        $this->methodWillReturn($errorStore , 'getErrors', $errorStore);
        $this->methodWillReturn('message', 'has', $errorStore);
        $returned = $errorStore->hasError('field');
        $this->assertEquals('message',$returned);
    }

    /**
     *
     */
    public function testGetErrorWhenNotError()
    {
        $errorStore = $this->newErrorStore('hasError');
        $this->methodWillReturnFalse('hasError', $errorStore);
        $returned = $errorStore->getError('key');
        $this->assertNull($returned);
    }

    /**
     *
     */
    public function testGetErrorWhenExistError()
    {
        $errorStore = $this->newErrorStore(['hasError', 'transformKey', 'getErrors','first']);
        $this->methodWillReturnTrue('hasError', $errorStore);
        $this->methodWillReturnTrue('hasError', $errorStore);
        $this->methodWillReturn('field', 'transformKey', $errorStore);
        $this->methodWillReturn($errorStore , 'getErrors', $errorStore);
        $this->methodWillReturn('message', 'first', $errorStore);
        $returned = $errorStore->getError('key');
        $this->assertEquals('message',$returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testHasErrors()
    {
        $errorStore = $this->newErrorStore('has');
        $this->setProtectedAttributeOf($errorStore,'session',$errorStore);
        $this->methodWillReturnTrue('has', $errorStore);
        $returned = $errorStore->hasErrors();
        $this->assertTrue($returned);

    }

    /**
     *
     */
    public function testGetErrorsWhenNotErrors()
    {
        $errorStore = $this->newErrorStore('hasErrors');
        $this->methodWillReturnFalse('hasErrors', $errorStore);
        $returned = $errorStore->getErrors();
        $this->assertNull($returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetErrorsWhenExistErrors()
    {
        $errorStore = $this->newErrorStore(['hasErrors', 'get']);
        $this->setProtectedAttributeOf($errorStore, 'session', $errorStore);
        $this->methodWillReturnTrue('hasErrors', $errorStore);
        $this->methodWillReturnTrue('get', $errorStore);
        $returned = $errorStore->getErrors();
        $this->assertTrue($returned);
}
    /**
     * @param null $methods
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function newErrorStore($methods = null)
    {
        return $this->newInstance(ErrorStore::class, [], $methods);
    }
}
