<?php

namespace Tests\Elements\Widgets;

use LaraForm\Elements\Widgets\InputWidget;
use LaraForm\Elements\Widgets\NumberWidget;
use LaraForm\Stores\ErrorStore;
use LaraForm\Stores\OldInputStore;
use Tests\Elements\WidgetTest;

class NumberWidgetTest extends WidgetTest
{
    protected $numberWidget;

    /**
     * @throws \ReflectionException
     */
    public function setUp()
    {
        parent::setUp();
        if (empty($this->numberWidget)) {
            $this->numberWidget = $this->newNumberWidget(['parentRender', 'parentCheckAttributes']);
        };

        $this->setProtectedAttributeOf($this->numberWidget, 'config', config('lara_form'));
    }

    /**
     *
     */
    public function testRender()
    {
        $this->methodWillReturnTrue('parentRender', $this->numberWidget);
        $returned = $this->numberWidget->render();
        $this->assertTrue($returned);
    }

    /**
     *
     */
    public function testCheckAttributes()
    {
        $attr = [];
        $this->numberWidget->checkAttributes($attr);
        $this->assertEquals(['type' => 'number'],$attr);
    }

    /**
     * @param null $methods
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function newNumberWidget($methods = null)
    {
        $numberWidget = $this->newInstance(NumberWidget::class,
            [app(ErrorStore::class), app(OldInputStore::class)],
            $methods);
        return $numberWidget;
    }
}
