<?php

namespace Tests\LaraForm\Elements\Widgets;

use LaraForm\Elements\Widgets\FileWidget;
use LaraForm\Stores\ErrorStore;
use LaraForm\Stores\OldInputStore;
use Tests\LaraForm\Elements\WidgetTest;

class FileWidgetTest extends WidgetTest
{
    protected $fileWidget;

    /**
     * @throws \ReflectionException
     */
    public function setUp()
    {
        parent::setUp();
        if (empty($this->fileWidget)) {
            $methods = ['getTemplate', 'formatClass', 'formatNestingLabel', 'checkAttributes', 'btn', 'multipleByBrackets'];
            $this->fileWidget = $this->newFileWidget($methods);
        };

        $this->setProtectedAttributeOf($this->fileWidget, 'config', config('lara_form'));
    }

    /**
     * @throws \ReflectionException
     */
    public function testRenderWhenExistName()
    {
        $this->setProtectedAttributeOf($this->fileWidget, 'name', 'upload');
        $this->renderByName();
    }

    /**
     * @throws \ReflectionException
     */
    public function testRenderWhenEmptyName()
    {
        \Config::set('lara_form.text.submit_name', 'upload');
        $this->setProtectedAttributeOf($this->fileWidget, 'config', config('lara_form'));
        $this->renderByName();
        $name = $this->getProtectedAttributeOf($this->fileWidget, 'name');
        $this->assertEquals('upload', $name);
    }

    /**
     * @throws \ReflectionException
     */
    public function testCheckAttributesWhenMulti()
    {
        $mockMethods = [
            'parentCheckAttributes',
            'generateClass',
            'getTemplate',
            'btn',
            'multipleByBrackets'
        ];
        $attr = [
            'type' => 'text',
            'value' => 'text',
            'label' => false,
            'multiple' => true
        ];
        $fileWidget = $this->newFileWidget($mockMethods);
        $this->methodWillReturnArgument(0, 'getTemplate', $fileWidget);
        $this->setProtectedAttributeOf($fileWidget, 'config', config('lara_form'));
        $fileWidget->checkAttributes($attr);
        $fileTemplate = $this->getProtectedAttributeOf($fileWidget, 'fileTemplate');
        $this->assertEquals([], $attr);
        $this->assertEquals('fileMultiple', $fileTemplate);
    }

    /**
     * @throws \ReflectionException
     */
    public function testCheckAttributesWhenExistAccept()
    {
        $mockMethods = [
            'parentCheckAttributes',
            'generateClass',
            'getTemplate',
            'btn',
            'multipleByBrackets'
        ];
        $attr = [
            'label' => 'label',
            'accept'=> [
                '.jpg',
                '.png',
                '.jpeg',
                '.gif',
            ]
        ];
        $pattern =[
            'label' => 'label',
            'accept' => '.jpg, .png, .jpeg, .gif'
        ];
        $fileWidget = $this->newFileWidget($mockMethods);
        $this->methodWillReturnArgument(0, 'getTemplate', $fileWidget);
        $this->setProtectedAttributeOf($fileWidget, 'config', config('lara_form'));
        $this->setProtectedAttributeOf($fileWidget, 'name', 'upload');
        $fileWidget->checkAttributes($attr);
        $fileTemplate = $this->getProtectedAttributeOf($fileWidget, 'fileTemplate');
        $name = $this->getProtectedAttributeOf($fileWidget, 'name');
        $this->assertEquals($pattern, $attr);
        $this->assertEquals('file', $fileTemplate);
        $this->assertEquals('upload', $name);
    }

    /**
     *
     */
    private function renderByName()
    {
        $this->methodWillReturn('currentTemplate', 'getTemplate', $this->fileWidget);
        $this->methodWillReturn('customClass', 'formatClass', $this->fileWidget);
        $this->methodWillReturnArgument(2, 'formatNestingLabel', $this->fileWidget);
        $returned = $this->fileWidget->render();
        $this->assertEquals(['class' => 'customClass'], $returned);
    }

    /**
     * @param null $methods
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function newFileWidget($methods = null)
    {
        $fileWidget = $this->newInstance(
            FileWidget::class,
            [app(ErrorStore::class), app(OldInputStore::class)],
            $methods);
        return $fileWidget;
    }
}
