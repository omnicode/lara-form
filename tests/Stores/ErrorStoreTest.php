<?php

namespace Tests\Stores;

use Illuminate\Support\ViewErrorBag;
use LaraForm\Stores\ErrorStore;
use Tests\Core\BaseStoreTest;
use Tests\LaraFormTestCase;
use TestsTestCase;

class ErrorStoreTest extends LaraFormTestCase
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
            $this->errorStore = $this->newErrorStore(['hasErrors', 'transformKey']);
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
        $viewErrorBag = $this->newInstanceWithDisableArgs(ViewErrorBag::class,['has']);
        $this->withSession(['errors' => ['field' => 'message']]);
        $errorStore = $this->newErrorStore(['hasErrors', 'transformKey','getErrors']);
        $this->methodWillReturnTrue('hasErrors', $errorStore);
        $this->methodWillReturn('field', 'transformKey', $errorStore);
        $this->methodWillReturn($viewErrorBag, 'getErrors', $errorStore);
        $this->methodWillReturnTrue('has', $viewErrorBag);
        $returned = $errorStore->hasError('field');
        $this->assertTrue($returned);
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
        $viewErrorBag = $this->newInstanceWithDisableArgs(ViewErrorBag::class,['first']);
        $errorStore = $this->newErrorStore(['hasError', 'transformKey', 'getErrors']);
        $this->methodWillReturnTrue('hasError', $errorStore);
        $this->methodWillReturnTrue('hasError', $errorStore);
        $this->methodWillReturn('field', 'transformKey', $errorStore);
        $this->methodWillReturn($viewErrorBag, 'getErrors', $errorStore);
        $this->methodWillReturn('message', 'first', $viewErrorBag);
        $returned = $errorStore->getError('key');
        $this->assertEquals('message', $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testHasErrors()
    {
        $errorStore = $this->newErrorStore('has');
        $this->setProtectedAttributeOf($errorStore, 'session', $errorStore);
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
        $viewErrorBag = $this->newInstanceWithDisableArgs(ViewErrorBag::class);
        $errorStore = $this->newErrorStore(['hasErrors', 'get']);
        $this->setProtectedAttributeOf($errorStore, 'session', $errorStore);
        $this->methodWillReturnTrue('hasErrors', $errorStore);
        $this->methodWillReturn($viewErrorBag, 'get', $errorStore);
        $returned = $errorStore->getErrors();
        $this->assertInstanceOf(ViewErrorBag::class, $returned);
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
