<?php

namespace Tests\Elements\Widgets;

use LaraForm\Elements\Widgets\TextareaWidget;
use LaraForm\Stores\ErrorStore;
use LaraForm\Stores\OldInputStore;
use Tests\Elements\WidgetTest;

class TextareaWidgetTest extends WidgetTest
{
    protected $textaretaWidget;

    /**
     * @throws \ReflectionException
     */
    public function setUp()
    {
        parent::setUp();
        if (empty($this->textaretaWidget)) {
            $mockMethods = ['getTemplate', 'formatAttributes', 'checkAttributes', 'getValue', 'formatTemplate', 'completeTemplate'];
            $this->textaretaWidget = $this->newTextareaWidget($mockMethods);
        };

        $this->setProtectedAttributeOf($this->textaretaWidget, 'config', config('lara_form'));
    }

    /**
     * @throws \ReflectionException
     */
    public function testRender()
    {
        $this->methodWillReturn('formatedTemplate','formatTemplate',$this->textaretaWidget);
        $this->methodWillReturn('completedTemplate','completeTemplate',$this->textaretaWidget);
        $this->methodWillReturn([],'getValue',$this->textaretaWidget);
        $returned = $this->textaretaWidget->render();
        $html = $this->getProtectedAttributeOf($this->textaretaWidget, 'html');
        $this->assertEquals('formatedTemplate',$html);
        $this->assertEquals('completedTemplate',$returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testCheckAttributes()
    {
        $attr = ['type' => str_random(5)];
        $mockMethods = [
            'getTemplate',
            'setOtherHtmlAttributes',
            'generateClass',
            'generateId',
            'generateLabel',
            'generatePlaceholder',
            'parentCheckAttributes',
        ];
        $textareaWidget = $this->newTextareaWidget($mockMethods);
        $this->methodWillReturn('template','getTemplate',$textareaWidget);
        $textareaWidget->checkAttributes($attr);
        $template = $this->getProtectedAttributeOf($textareaWidget,'currentTemplate');
        $this->assertEquals([],$attr);
        $this->assertEquals('template',$template);
    }
    /**
     * @param null $methods
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function newTextareaWidget($methods = null)
    {
        $textaretaWidget = $this->newInstance(TextareaWidget::class,
            [app(ErrorStore::class), app(OldInputStore::class)],
            $methods);
        return $textaretaWidget;
    }
}
