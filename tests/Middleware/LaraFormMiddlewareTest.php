<?php

namespace Tests\LaraForm\Middleware;

use Illuminate\Http\Request;
use LaraForm\FormProtection;
use LaraForm\Middleware\LaraFormMiddleware;
use Tests\LaraForm\Core\BaseStoreTest;

class LaraFormMiddlewareTest extends BaseStoreTest
{
    /**
     * @var
     */
    protected $laraFormMiddleware;

    /**
     * @var
     */
    protected $request;

    /**
     * @var
     */
    protected $closure;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();
        if (empty($this->laraFormMiddleware)) {
            $this->laraFormMiddleware = $this->newLaraFormMiddleware('isGlobalException');
        };
        if (empty($this->request)) {
            $this->request = $this->newInstance(Request::class, [], ['method', 'all', 'query', 'getUri', 'route', 'getName']);
        };
        if (empty($this->closure)) {
            $this->closure = $this->newClosure();
        };
    }

    /**
     * @throws \ReflectionException
     */
    public function testConstruct()
    {
        $returned = $this->getProtectedAttributeOf($this->laraFormMiddleware, 'formProtection');
        $this->assertInstanceOf(FormProtection::class, $returned);
    }

    /**
     *
     */
    public function testHandleWhenRequestMethodIsGet()
    {
        $this->methodWillReturn('GET', 'method', $this->request);
        $returned = $this->laraFormMiddleware->handle($this->request, $this->closure);
        $this->assertInstanceOf(\Closure::class, $returned);
    }

    /**
     *
     */
    public function testHandleWhenGlobalExceptionIsTrue()
    {
        $this->methodWillReturnTrue('isGlobalException', $this->laraFormMiddleware);
        $returned = $this->laraFormMiddleware->handle($this->request, $this->closure);
        $this->assertInstanceOf(\Closure::class, $returned);
    }

    /**
     * @throws \Exception
     * @expectedException Exception
     */
    public function testHandleWhenInvalidRequest()
    {
        $this->methodWillReturnFalse('isGlobalException', $this->laraFormMiddleware);
        $this->methodWillReturn('POST', 'method', $this->request);
        $this->methodWillReturn(['user'], 'query', $this->request);
        $this->methodWillReturn(['user' => '', 'email' => ''], 'all', $this->request);
        $formProtaction = $this->getProtectedAttributeOf($this->laraFormMiddleware, 'formProtection');
        $this->methodWillReturnFalse('validate', $formProtaction);
        $this->laraFormMiddleware->handle($this->request, $this->closure);
    }

    /**
     * @throws \ReflectionException
     */
    public function testIsGlobalExceptionWithUrlsWhenStarMiddel()
    {
        $returned = $this->validationUrlBy(['foo/*/bar']);
        $this->assertFalse($returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testIsGlobalExceptionWithUrlsWhenStarEnd()
    {
        $returned = $this->validationUrlBy(['foo/bar*']);
        $this->assertFalse($returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testIsGlobalExceptionByExceptUrlsWhenStarExistInUrl()
    {
        $url = url('foo/bar/too');
        $this->methodWillReturn($url, 'getUri', $this->request);
        $returned = $this->validationUrlBy(['foo/bar*']);
        $this->assertTrue($returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testIsGlobalExceptionByEqualUrl()
    {
        $url = url('foo/bar');
        $this->methodWillReturn($url, 'getUri', $this->request);
        $returned = $this->validationUrlBy(['foo/bar']);
        $this->assertTrue($returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testIsGlobalExceptionByRoute()
    {
        $this->methodWillReturn($this->request, 'route', $this->request);
        $this->methodWillReturn('user.name', 'getName', $this->request);
        $returned = $this->validationUrlBy([], ['user.name']);
        $this->assertTrue($returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testIsGlobalExceptionByDefaultValue()
    {
        $returned = $this->validationUrlBy();
        $this->assertFalse($returned);
    }

    /**
     * @param $exceptUrl
     * @return mixed
     * @throws \ReflectionException
     */
    private function validationUrlBy($exceptUrl = [], $route = [])
    {
        \Config::set('lara_form.except.url', $exceptUrl);
        \Config::set('lara_form.except.route', $route);
        $laraFormMiddleware = $this->newLaraFormMiddleware();
        return $this->getProtectedMethod($laraFormMiddleware, 'isGlobalException', [$this->request]);
    }

    /**
     * @param null $methods
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function newLaraFormMiddleware($methods = null)
    {
        $formProtaction = $this->newInstance(FormProtection::class, [], 'validate');
        return $this->newInstance(LaraFormMiddleware::class, [$formProtaction], $methods);;
    }

    /**
     * @return \Closure
     */
    private function newClosure()
    {
        return function ($stack) {
            return function () use ($stack) {
                return $stack;
            };
        };
    }
}
