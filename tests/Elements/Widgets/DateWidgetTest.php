<?php

namespace Tests\Elements\Widgets;

use LaraForm\Elements\Widgets\InputWidget;
use LaraForm\Elements\Widgets\DateWidget;
use LaraForm\Stores\ErrorStore;
use LaraForm\Stores\OldInputStore;
use Tests\Elements\WidgetTest;

class DateWidgetTest extends WidgetTest
{
    protected $dateWidget;

    /**
     * @throws \ReflectionException
     */
    public function setUp()
    {
        parent::setUp();
        if (empty($this->dateWidget)) {
            $this->dateWidget = $this->newNumberWidget(['parentRender', 'parentCheckAttributes']);
        };

        $this->setProtectedAttributeOf($this->dateWidget, 'config', config('lara_form'));
    }

    /**
     *
     */
    public function testRender()
    {
        $this->methodWillReturnTrue('parentRender', $this->dateWidget);
        $returned = $this->dateWidget->render();
        $this->assertTrue($returned);
    }

    /**
     *
     */
    public function testCheckAttributes()
    {
        $attr = [];
        $this->dateWidget->checkAttributes($attr);
        $this->assertEquals(['type' => 'date'],$attr);
    }

    /**
     * @param null $methods
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function newNumberWidget($methods = null)
    {
        $dateWidget = $this->newInstance(DateWidget::class,
            [app(ErrorStore::class), app(OldInputStore::class)],
            $methods);
        return $dateWidget;
    }
}
