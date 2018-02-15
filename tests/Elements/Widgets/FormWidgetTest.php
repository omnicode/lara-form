<?php

namespace Tests\LaraForm\Elements\Widgets;

use LaraForm\Elements\Widgets\FormWidget;
use LaraForm\Stores\ErrorStore;
use LaraForm\Stores\OldInputStore;
use Tests\LaraForm\Elements\WidgetTest;

class FormWidgetTest extends WidgetTest
{
    protected $formWidget;

    /**
     * @throws \ReflectionException
     */
    public function setUp()
    {
        parent::setUp();
        if (empty($this->formWidget)) {
            $this->formWidget = $this->newFormWidget(['start', 'end', 'generateClass' ,'parentCheckAttributes']);
        };

        $this->setProtectedAttributeOf($this->formWidget, 'config', config('lara_form'));
    }
    /**
     * @throws \ReflectionException
     */
    public function testRenderStart()
    {
        $this->renderTestBy('start');
    }

    /**
     * @throws \ReflectionException
     */
    public function testRenderEnd()
    {
        $this->renderTestBy('end');
    }


    /**
     *
     */
    public function testCheckAttributes()
    {
        $attr = [
            'file' => true,
            '_unlockFields' => true,
            '_form_token' => 'token',
            '_form_action' => 'action',
            '_form_method' => 'method',
        ];
        $pattern = [
            'accept-charset'  => config('lara_form.charset'),
            'enctype'  => 'multipart/form-data',
        ];
        $this->formWidget->checkAttributes($attr);
        $this->assertEquals($pattern, $attr);
    }

    /**
     * @throws \ReflectionException
     */
    public function testStartWhenMethodIsGet()
    {
        $options = [
          '_form_method' => 'get',
          '_form_action' => 'foo/bar',
          '_form_token' => 'token',
        ];
        $formWidget = $this->newFormWidget(['checkAttributes', 'getTemplate', 'formatAttributes', 'formatTemplate']);
        $this->methodWillReturnArgument(0,'formatAttributes',$formWidget);
        $this->methodWillReturnArgument(1,'formatTemplate',$formWidget);
        $returned = $this->getProtectedMethod($formWidget,'start', [$options]);
        $options['method'] = 'GET';
        $options['action'] = 'foo/bar';
        $this->assertEquals($options,$returned['attrs']);
    }

    /**
     * @throws \ReflectionException
     */
    public function testStartWhenMethodIsPost()
    {
        $options = [
            '_form_method' => 'post',
            '_form_action' => 'foo/bar',
            '_form_token' => 'token',
        ];
        $formWidget = $this->newFormWidget(['checkAttributes', 'getTemplate', 'formatAttributes', 'formatTemplate', 'setHidden']);
        $this->methodWillReturn('','formatAttributes',$formWidget);
        $this->methodWillReturn('','formatTemplate',$formWidget);
        $this->methodWillReturnArgument(1,'setHidden',$formWidget);
        $returned = $this->getProtectedMethod($formWidget,'start', [$options]);
        $pattern = csrf_field().'token';
        $this->assertEquals($pattern,$returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testStartWhenMethodIsOtherType()
    {
        $options = [
            '_form_method' => 'put',
            '_form_action' => 'foo/bar',
            '_form_token' => 'token',
        ];
        $formWidget = $this->newFormWidget(['checkAttributes', 'getTemplate', 'formatAttributes', 'formatTemplate', 'setHidden']);
        $this->methodWillReturn('','formatAttributes',$formWidget);
        $this->methodWillReturn('','formatTemplate',$formWidget);
        $this->methodWillReturnArgument(1,'setHidden',$formWidget);
        $returned = $this->getProtectedMethod($formWidget,'start', [$options]);
        $pattern = csrf_field().method_field('PUT').'token';
        $this->assertEquals($pattern,$returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testEnd()
    {
        $formWidget = $this->newFormWidget(['getTemplate', 'formatTemplate']);
        $this->methodWillReturnArgument(0,'getTemplate',$formWidget);
        $this->methodWillReturnArgument(0,'formatTemplate',$formWidget);
        $returned = $this->getProtectedMethod($formWidget,'end');
        $this->assertEquals('formEnd',$returned);
    }

    /**
     * @param $name
     * @throws \ReflectionException
     */
    private function renderTestBy($name)
    {
        $this->setProtectedAttributeOf($this->formWidget, 'name', $name);
        $this->methodWillReturn($name . 'Method', $name, $this->formWidget);
        $returned = $this->formWidget->render();
        $this->assertEquals($name . 'Method', $returned);
    }

    /**
     * @param null $methods
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function newFormWidget($methods = null)
    {
        return $this->newInstance(
            FormWidget::class,
            [app(ErrorStore::class), app(OldInputStore::class)],
            $methods);
    }
}
