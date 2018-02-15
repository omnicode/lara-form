<?php

namespace Tests\LaraForm\Elements\Widgets;

use LaraForm\Elements\Widgets\RadioWidget;
use LaraForm\Stores\ErrorStore;
use LaraForm\Stores\OldInputStore;
use Tests\LaraForm\Elements\WidgetTest;

class RadioWidgetTest extends WidgetTest
{
    protected $radioWidget;

    /**
     * @throws \ReflectionException
     */
    public function setUp()
    {
        parent::setUp();
        if (empty($this->radioWidget)) {
            $methods = ['getTemplate','checkAttributes','setOtherHtmlAttributes','formatNestingLabel'];
            $this->radioWidget = $this->newRadioWidget($methods);
        };

        $this->setProtectedAttributeOf($this->radioWidget, 'config', config('lara_form'));
    }

    /**
     * @throws \ReflectionException
     */
    public function testRender()
    {
        $this->radioWidget->expects($this->any(2))->method('getTemplate')->will($this->returnArgument(0));
        $this->methodWillReturnArgument(0, 'formatNestingLabel', $this->radioWidget);
        $returned = $this->radioWidget->render();
        $currentTemplate = $this->getProtectedAttributeOf($this->radioWidget,'currentTemplate');
        $this->assertEquals('radio',$returned);
        $this->assertEquals('radioContainer',$currentTemplate);
    }

    /**
     *
     */
    public function testCheckAttributes()
    {
        $attr = [
            'value' => 'defaultValue',
        ];
        $pattern = [
            'type' => 'radio',
            'checked' => 'checked'
        ];
        $pattern = $pattern+$attr;
        $radioWidget = $this->newRadioWidget(['getValue', 'generateId', 'parentCheckAttributes','strToArray']);
        $this->methodWillReturn(['value' => 'defaultValue'], 'getValue', $radioWidget);
        $this->methodWillReturn(['defaultValue'], 'strToArray', $radioWidget);
        $radioWidget->checkAttributes($attr);
        $this->assertEquals($pattern,$attr);
    }

    /**
     * @param null $methods
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function newRadioWidget($methods = null)
    {
        return $this->newInstance(
            RadioWidget::class,
            [app(ErrorStore::class), app(OldInputStore::class)],
            $methods);
    }
}
