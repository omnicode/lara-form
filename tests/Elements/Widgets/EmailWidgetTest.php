<?php

namespace Tests\Elements\Widgets;

use LaraForm\Elements\Widgets\EmailWidget;
use LaraForm\Stores\ErrorStore;
use LaraForm\Stores\OldInputStore;
use Tests\LaraFormTestCase;
use TestsTestCase;

class EmailWidgetTest extends LaraFormTestCase
{
    protected $emailWidget;

    /**
     * @throws \ReflectionException
     */
    public function setUp()
    {
        parent::setUp();
        if (empty($this->emailWidget)) {
            $this->emailWidget = $this->newEmailWidget(['parentRender', 'setOtherHtmlAttributes', 'parentCheckAttributes']);
        };

        $this->setProtectedAttributeOf($this->emailWidget, 'config', config('lara_form'));
    }

    /**
     *
     */
    public function testRender()
    {
        $this->methodWillReturn('value','parentRender', $this->emailWidget);
        $returned = $this->emailWidget->render();
        $this->assertEquals('value',$returned);
    }

    /**
     *
     */
    public function testCheckAttributes()
    {
        $attr = [];
        $this->emailWidget->checkAttributes($attr);
        $this->assertEquals(['type' => 'email'],$attr);
    }

    /**
     * @param null $methods
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function newEmailWidget($methods = null)
    {
        $emailWidget = $this->newInstance(EmailWidget::class,
            [app(ErrorStore::class), app(OldInputStore::class)],
            $methods);
        return $emailWidget;
    }
}
