<?php

namespace Label\Elements\Widgets;

use LaraForm\Elements\Widgets\LabelWidget;
use LaraForm\Stores\ErrorStore;
use LaraForm\Stores\OldInputStore;
use Tests\Elements\WidgetTest;

class LabelWidgetTest extends WidgetTest
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
        $this->methodWillReturnTrue('renderLabel',$this->labelWidget);
        $returned = $this->labelWidget->render();
        $this->assertTrue($returned);
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
