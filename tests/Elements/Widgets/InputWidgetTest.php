<?php

namespace Tests\Elements\Widgets;

use LaraForm\Elements\Widgets\InputWidget;
use LaraForm\Stores\ErrorStore;
use LaraForm\Stores\OldInputStore;
use Tests\Elements\WidgetTest;

class InputWidgetTest extends WidgetTest
{
    protected $inputWidget;

    /**
     * @throws \ReflectionException
     */
    public function setUp()
    {
        parent::setUp();
        if (empty($this->inputWidget)) {
            $this->inputWidget = $this->newInputWidget(['parentRender', 'parentCheckAttributes']);
        };

        $this->setProtectedAttributeOf($this->inputWidget, 'config', config('lara_form'));
    }

    /**
     *
     */
    public function testRender()
    {
        $this->methodWillReturnTrue('parentRender', $this->inputWidget);
        $returned = $this->inputWidget->render();
        $this->assertTrue($returned);
    }

    /**
     *
     */
    public function testCheckAttributes()
    {
        $attr = [];
        $this->inputWidget->checkAttributes($attr);
    }

    /**
     * @param null $methods
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function newInputWidget($methods = null)
    {
        $inputWidget = $this->newInstance(InputWidget::class,
            [app(ErrorStore::class), app(OldInputStore::class)],
            $methods);
        return $inputWidget;
    }
}
