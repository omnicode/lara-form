<?php

namespace Label\Elements\Widgets;

use LaraForm\Elements\Widgets\LabelWidget;
use LaraForm\Stores\ErrorStore;
use LaraForm\Stores\OldInputStore;
use Tests\LaraFormTestCase;
use TestsTestCase;

class LabelWidgetTest extends LaraFormTestCase
{
    protected $labelWidget;

    /**
     * @throws \ReflectionException
     */
    public function setUp()
    {
        parent::setUp();
        if (empty($this->labelWidget)) {
            $this->labelWidget = $this->newLabelWidget('renderLabel');
        };

        $this->setProtectedAttributeOf($this->labelWidget, 'config', config('lara_form'));
    }

    /**
     *
     */
    public function testRender()
    {
        $this->setProtectedAttributeOf($this->labelWidget, 'name', 'name');
        $this->methodWillReturn('value','renderLabel', $this->labelWidget);
        $returned = $this->labelWidget->render();
        $this->assertEquals('value',$returned);
    }

    /**
     * @param null $methods
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function newLabelWidget($methods = null)
    {
        return $this->newInstance(
            LabelWidget::class,
            [app(ErrorStore::class), app(OldInputStore::class)],
            $methods);
    }
}
