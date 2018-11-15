<?php

namespace Tests\Elements\Widgets;

use LaraForm\Elements\Widgets\dateWidget;
use LaraForm\Stores\ErrorStore;
use LaraForm\Stores\OldInputStore;
use Tests\LaraFormTestCase;
use TestsTestCase;

class DateWidgetTest extends LaraFormTestCase
{
    protected $dateWidget;

    /**
     * @throws \ReflectionException
     */
    public function setUp()
    {
        parent::setUp();
        if (empty($this->dateWidget)) {
            $this->dateWidget = $this->newdateWidget(['parentRender', 'setOtherHtmlAttributes', 'parentCheckAttributes']);
        };

        $this->setProtectedAttributeOf($this->dateWidget, 'config', config('lara_form'));
    }

    /**
     *
     */
    public function testRender()
    {
        $this->methodWillReturn('value','parentRender', $this->dateWidget);
        $returned = $this->dateWidget->render();
        $this->assertEquals('value',$returned);
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
    private function newdateWidget($methods = null)
    {
        $dateWidget = $this->newInstance(dateWidget::class,
            [app(ErrorStore::class), app(OldInputStore::class)],
            $methods);
        return $dateWidget;
    }
}
