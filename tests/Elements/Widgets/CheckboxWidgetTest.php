<?php

namespace Tests\Elements\Widgets;

use LaraForm\Elements\Widgets\CheckboxWidget;
use LaraForm\Stores\ErrorStore;
use LaraForm\Stores\OldInputStore;
use Tests\LaraFormTestCase;
use TestsTestCase;

class CheckboxWidgetTest extends LaraFormTestCase
{
    /**
     * @var
     */
    protected $checkboxWidget;

    /**
     * @throws \ReflectionException
     */
    public function setUp()
    {
        parent::setUp();
        if (empty($this->checkboxWidget)) {
            $this->checkboxWidget = $this->newCheckboxWidget(['checkAttributes', 'getTemplate', 'formatNestingLabel']);
        };

        $this->setProtectedAttributeOf($this->checkboxWidget, 'config', config('lara_form'));
    }

    /**
     * @throws \PHPUnit_Framework_Constraint
     * @throws \ReflectionException
     */
    public function testRender()
    {
        $this->checkboxWidget->expects($this->any(2))->method('getTemplate')->will($this->returnArgument(0));
        $this->methodWillReturnArgument(0, 'formatNestingLabel', $this->checkboxWidget);
        $returned = $this->checkboxWidget->render();
        $currentTemplate = $this->getProtectedAttributeOf($this->checkboxWidget, 'currentTemplate');
        $this->assertEquals('checkbox', $returned);
        $this->assertEquals('checkboxContainer', $currentTemplate);
    }

    /**
     * @throws \ReflectionException
     */
    public function testCheckAttributesWhenEmptyValue()
    {
        $attr = [
            'value' => 'defaultValue',
            'multiple' => true
        ];
        $pattern = [
            'checked' => 'checked',
            'type' => 'checkbox',
            'value' => 'defaultValue'
        ];
        $checkboxWidget = $this->newCheckboxWidget(['getValue', 'setHidden', 'generateId', 'parentCheckAttributes', 'strToArray']);
        $this->setProtectedAttributeOf($checkboxWidget, 'name', 'name');
        $this->methodWillReturn(['value' => 'defaultValue'], 'getValue', $checkboxWidget);
        $this->methodWillReturn(['defaultValue'], 'strToArray', $checkboxWidget);
        $checkboxWidget->checkAttributes($attr);
        $name = $this->getProtectedAttributeOf($checkboxWidget, 'name');
        $this->assertEquals($pattern, $attr);
        $this->assertEquals('name[]', $name);
    }

    /**
     * @throws \ReflectionException
     */
    public function testCheckAttributesWhenHiddenFalse()
    {
        $attr = ['hidden' => false];
        $checkboxWidget = $this->newCheckboxWidget(['generateId', 'parentCheckAttributes']);
        $this->setProtectedAttributeOf($checkboxWidget, 'name', 'name');
        $checkboxWidget->checkAttributes($attr);
        $hidden = $this->getProtectedAttributeOf($checkboxWidget, 'hidden');
        $this->assertEquals('', $hidden);
    }

    /**
     * @throws \PHPUnit_Framework_Constraint
     * @throws \ReflectionException
     */
    public function testCheckAttributesWhenHiddenTrue()
    {
        $attr = [];
        $checkboxWidget = $this->newCheckboxWidget(['getValue', 'setHidden', 'generateId', 'parentCheckAttributes']);
        $checkboxWidget->expects($this->once())->method('setHidden')->willReturn('hidden');
        $checkboxWidget->expects($this->any(3))->method('getValue')->willReturn(['value' => 'data']);

        $this->setProtectedAttributeOf($checkboxWidget, 'name', 'field[]');
        $checkboxWidget->checkAttributes($attr);
        $hidden1 = $this->getProtectedAttributeOf($checkboxWidget, 'hidden');

        $this->setProtectedAttributeOf($checkboxWidget, 'name', 'field[]');
        $checkboxWidget->checkAttributes($attr);
        $hidden2 = $this->getProtectedAttributeOf($checkboxWidget, 'hidden');

        $this->setProtectedAttributeOf($checkboxWidget, 'name', 'field[]');
        $checkboxWidget->checkAttributes($attr);
        $hidden3 = $this->getProtectedAttributeOf($checkboxWidget, 'hidden');

        $this->assertEquals('hidden', $hidden1);
        $this->assertEquals('', $hidden2);
        $this->assertEquals('', $hidden3);
    }

    /**
     * @param null $methods
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function newCheckboxWidget($methods = null)
    {
        $checkboxWidget = $this->newInstance(CheckboxWidget::class,
            [app(ErrorStore::class), app(OldInputStore::class)],
            $methods);
        return $checkboxWidget;
    }
}
