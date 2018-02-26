<?php

namespace Tests\Elements\Widgets;

use LaraForm\Elements\Widgets\HiddenWidget;
use LaraForm\Stores\ErrorStore;
use LaraForm\Stores\OldInputStore;
use Tests\Elements\WidgetTest;

class HiddenWidgetTest extends WidgetTest
{
    protected $hiddenWidget;

    /**
     * @throws \ReflectionException
     */
    public function setUp()
    {
        parent::setUp();
        if (empty($this->hiddenWidget)) {
            $this->hiddenWidget = $this->newHiddenWidget(['parentRender', 'setOtherHtmlAttributes', 'parentCheckAttributes']);
        };

        $this->setProtectedAttributeOf($this->hiddenWidget, 'config', config('lara_form'));
    }

    /**
     *
     */
    public function testRender()
    {
        $this->methodWillReturnTrue('parentRender', $this->hiddenWidget);
        $returned = $this->hiddenWidget->render();
        $this->assertTrue($returned);
    }

    /**
     *
     */
    public function testCheckAttributes()
    {
        $attr = [];
        $this->hiddenWidget->checkAttributes($attr);
        $this->assertEquals(['type' => 'hidden'],$attr);
    }

    /**
     * @param null $methods
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function newHiddenWidget($methods = null)
    {
        $hiddenWidget = $this->newInstance(HiddenWidget::class,
            [app(ErrorStore::class), app(OldInputStore::class)],
            $methods);
        return $hiddenWidget;
    }
}
