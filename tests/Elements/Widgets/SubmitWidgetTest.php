<?php

namespace Tests\Elements\Widgets;

use LaraForm\Elements\Widgets\SubmitWidget;
use LaraForm\Stores\ErrorStore;
use LaraForm\Stores\OldInputStore;
use Tests\LaraFormTestCase;
use TestsTestCase;

class SubmitWidgetTest extends LaraFormTestCase
{
    protected $submitWidget;

    /**
     * @throws \ReflectionException
     */
    public function setUp()
    {
        parent::setUp();
        if (empty($this->submitWidget)) {
            $methods = ['getTemplate', 'checkAttributes', 'formatAttributes', 'formatTemplate', 'completeTemplate'];
            $this->submitWidget = $this->newSubmitWidget($methods);
        };

        $this->setProtectedAttributeOf($this->submitWidget, 'config', config('lara_form'));
    }

    /**
     * @throws \PHPUnit_Framework_Constraint
     * @throws \ReflectionException
     */
    public function testRenderWhenNameFalse()
    {
        $this->setProtectedAttributeOf($this->submitWidget, 'name', false);
        $this->methodWillReturnTrue('formatAttributes', $this->submitWidget);
        $this->methodWillReturn('value','completeTemplate', $this->submitWidget);
        $this->methodWillReturnArgument(0, 'formatTemplate', $this->submitWidget);
        $this->submitWidget->expects($this->any(2))->method('getTemplate')->will($this->returnArgument(0));
        $returned = $this->submitWidget->render();
        $template = $this->getProtectedAttributeOf($this->submitWidget, 'html');
        $currentTemplate = $this->getProtectedAttributeOf($this->submitWidget, 'currentTemplate');
        $this->assertEquals('value',$returned);
        $this->assertEquals('button', $template);
        $this->assertEquals('submitContainer', $currentTemplate);
    }

    /**
     * @throws \PHPUnit_Framework_Constraint
     * @throws \ReflectionException
     */
    public function testRenderWhenExistName()
    {
        $this->setProtectedAttributeOf($this->submitWidget, 'name', 'name');
        $this->renderByName('name');
    }

    /**
     * @throws \PHPUnit_Framework_Constraint
     * @throws \ReflectionException
     */
    public function testRenderWhenNameFromConfig()
    {
        \Config::set('lara_form.text.submit_name', 'save');
        $this->setProtectedAttributeOf($this->submitWidget, 'config', config('lara_form'));
        $this->renderByName('save');
    }

    /**
     * @throws \ReflectionException
     */
    public function testCheckAttributesWhenExistBtnAndEmptyType()
    {
        $attr = [];
        $submitWidget = $this->newSubmitWidget(['generateClass', 'setOtherHtmlAttributes', 'generateId', 'parentCheckAttributes', 'btn']);
        $this->setProtectedAttributeOf($submitWidget, 'config', config('lara_form'));
        $submitWidget->checkAttributes($attr);
        $this->assertEquals(['type' => 'submit'],$attr);
    }

    /**
     * @throws \ReflectionException
     */
    public function testCheckAttributesWhenTypeIsButton()
    {
        $attr = ['type' => 'button'];
        $submitWidget = $this->newSubmitWidget(['generateClass', 'setOtherHtmlAttributes', 'generateId', 'parentCheckAttributes', 'btn']);
        $this->setProtectedAttributeOf($submitWidget, 'config', config('lara_form'));
        $this->methodWillThrowExceptionWithArgument('setOtherHtmlAttributes' ,$submitWidget, 1);
        $this->expectExceptionMessage('method attribute is :["type","button"]');
        $submitWidget->checkAttributes($attr);
    }

    /**
     * @param $name
     * @throws \PHPUnit_Framework_Constraint
     * @throws \ReflectionException
     */
    private function renderByName($name)
    {
        $this->methodWillReturnTrue('formatAttributes', $this->submitWidget);
        $this->methodWillReturn('value','completeTemplate', $this->submitWidget);
        $this->methodWillReturn('value', 'formatTemplate', $this->submitWidget);
        $this->submitWidget->expects($this->any(2))->method('getTemplate')->will($this->returnArgument(0));
        $returned = $this->submitWidget->render();
        $html = $this->getProtectedAttributeOf($this->submitWidget, 'html');
        $currentTemplate = $this->getProtectedAttributeOf($this->submitWidget, 'currentTemplate');
        $this->assertEquals('value',$returned);
        $this->assertEquals('value', $html);
        $this->assertEquals('submitContainer', $currentTemplate);
    }

    /**
     * @param null $methods
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function newSubmitWidget($methods = null)
    {
        return $this->newInstance(
            SubmitWidget::class,
            [app(ErrorStore::class), app(OldInputStore::class)],
            $methods);
    }
}
