<?php

namespace Tests\Elements\Widgets;

use LaraForm\Elements\Widgets\PasswordWidget;
use LaraForm\Stores\ErrorStore;
use LaraForm\Stores\OldInputStore;
use Tests\Elements\WidgetTest;

class PasswordWidgetTest extends WidgetTest
{
    protected $passwordWidget;

    /**
     * @throws \ReflectionException
     */
    public function setUp()
    {
        parent::setUp();
        if (empty($this->passwordWidget)) {
            $this->passwordWidget = $this->newPasswordWidget(['parentRender', 'parentCheckAttributes']);
        };

        $this->setProtectedAttributeOf($this->passwordWidget, 'config', config('lara_form'));
    }

    /**
     *
     */
    public function testRender()
    {
        $this->methodWillReturnTrue('parentRender', $this->passwordWidget);
        $returned = $this->passwordWidget->render();
        $this->assertTrue($returned);
    }

    /**
     *
     */
    public function testCheckAttributes()
    {
        $attr = [];
        $this->passwordWidget->checkAttributes($attr);
        $this->assertEquals(['type' => 'password'],$attr);
    }

    /**
     * @param null $methods
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function newPasswordWidget($methods = null)
    {
        $passwordWidget = $this->newInstance(PasswordWidget::class,
            [app(ErrorStore::class), app(OldInputStore::class)],
            $methods);
        return $passwordWidget;
    }
}
