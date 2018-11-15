<?php

namespace Tests\Elements\Widgets;

use LaraForm\Elements\Widgets\BaseInputWidget;
use LaraForm\Stores\ErrorStore;
use LaraForm\Stores\OldInputStore;
use Tests\LaraFormTestCase;
use TestsTestCase;

class BaseInputWidgetTest extends LaraFormTestCase
{
    /**
     * @var
     */
    protected $baseInputWidget;

    /**
     * @throws \ReflectionException
     */
    public function setUp()
    {
        parent::setUp();
        if (empty($this->baseInputWidget)) {
            $this->baseInputWidget = $this->newBaseInputWidget();
        };

        $this->setProtectedAttributeOf($this->baseInputWidget, 'config', config('lara_form'));
    }

    /**
     *
     */
    public function testRender()
    {
        $baseInputWidget = $this->newBaseInputWidget(['checkAttributes', 'formatInputField']);
        $this->setProtectedAttributeOf($baseInputWidget, 'name', 'name');
        $this->methodWillReturn('value','formatInputField', $baseInputWidget);
        $returned = $baseInputWidget->render();
        $this->assertEquals('value',$returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testFormatInputField()
    {
        $mockedMethods = [
            'getTemplate',
            'generalCheckAttributes',
            'setHtmlAttributes',
            'formatAttributes',
            'formatTemplate',
            'getHtmlAttributes',
            'completeTemplate'
        ];
        $baseInputWidget = $this->newBaseInputWidget($mockedMethods);
        $this->methodWillReturn('value','completeTemplate', $baseInputWidget);
        $returned = $this->invokeMethod($baseInputWidget, 'formatInputField', ['name', [], true]);
        $this->assertEquals('value',$returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testFormatInputFieldWhenFalseCTemplate()
    {
        $mockedMethods = [
            'getTemplate',
            'generalCheckAttributes',
            'setHtmlAttributes',
            'formatAttributes',
            'formatTemplate',
            'getHtmlAttributes',
            'completeTemplate'
        ];
        $baseInputWidget = $this->newBaseInputWidget($mockedMethods);
        $this->methodWillReturn('value','completeTemplate', $baseInputWidget);
        $this->methodWillReturn('input', 'getTemplate', $baseInputWidget);
        $this->methodWillReturnArgument(0, 'formatTemplate', $baseInputWidget);
        $returned = $this->invokeMethod($baseInputWidget, 'formatInputField', ['name', []]);
        $html = $this->getProtectedAttributeOf($baseInputWidget, 'html');
        $this->assertEquals('input', $html);
        $this->assertEquals('value',$returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testFormatNestingLabelWhenAnonymous()
    {
        $mockedMethods = [
            'formatInputField',
            'setOtherHtmlAttributes',
            'getTemplate',
            'formatAttributes',
            'formatTemplate',
            'completeTemplate'
        ];
        $data = [
            'template',
            [
                'label_text' => false,
                'type' => 'checkbox'
            ]
        ];
        $templateAttrPattern = [
            'hidden' => '',
            'content' => '',
            'text' => '',
            'icon' => '',
            'attrs' => ''
        ];
        $baseInputWidget = $this->newBaseInputWidget($mockedMethods);
        $this->setProtectedAttributeOf($baseInputWidget, 'name', 'name');
        $this->methodWillReturn('', 'formatAttributes', $baseInputWidget);
        $this->methodWillThrowExceptionWithArgument('formatTemplate', $baseInputWidget);
        $this->expectExceptionMessage($this->getExceptionArgumentsMessage(['',$templateAttrPattern]));
        $this->invokeMethod($baseInputWidget, 'formatNestingLabel', $data);
    }

    /**
     * @throws \PHPUnit_Framework_Constraint
     * @throws \ReflectionException
     */
    public function testGeneralCheckAttributesWhenCTemplateTrue()
    {
        $this->generalCheckAttrTesting(['value' => 'defaultValue']);
    }

    /**
     * @throws \PHPUnit_Framework_Constraint
     * @throws \ReflectionException
     */
    public function testGeneralCheckAttributesWhenExistType()
    {
        $this->generalCheckAttrTesting(['type' => 'password']);
    }

    /**
     * @param $attr
     * @throws \PHPUnit_Framework_Constraint
     * @throws \ReflectionException
     */
    private function generalCheckAttrTesting($attr)
    {
        $mockedMethods = [
            'setHtmlAttributes',
            'getHtmlAttributes',
            'getValue',
            'generateId',
            'generateLabel',
            'generatePlaceholder',
            'generateClass',
            'assignOtherhtmlAtrributes',
            'parentCheckAttributes'
        ];
        $baseInputWidget = $this->newBaseInputWidget($mockedMethods);
        $this->setProtectedAttributeOf($baseInputWidget, 'config', config('lara_form'));
        $this->methodWillReturnTrue('parentCheckAttributes', $baseInputWidget);
        $type = empty($attr['type']) ? 'text' : $attr['type'];
        $baseInputWidget->expects($this->any())->method('getHtmlAttributes')->willReturn($type);
        $baseInputWidget->expects($this->any())->method('getValue')->willReturn(['value' => 'default_value']);
        $this->invokeMethod($baseInputWidget, 'generalCheckAttributes', [&$attr, true]);
    }

    /**
     * @param null $methods
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function newBaseInputWidget($methods = null)
    {
        return $this->newInstance(
            BaseInputWidget::class,
            [app(ErrorStore::class), app(OldInputStore::class)],
            $methods);
    }
}
