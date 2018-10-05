<?php

namespace Tests\Elements\Widgets;

use Illuminate\Support\ViewErrorBag;
use LaraForm\Elements\Widgets\ErrorListWidget;
use LaraForm\Stores\ErrorStore;
use LaraForm\Stores\OldInputStore;
use Tests\LaraFormTestCase;
use TestsTestCase;

class ErrorListWidgetTest extends LaraFormTestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testRenderWhenEmptyErrors()
    {
        $errorListWidget = $this->newErrorListWidget();
        $errors = $this->newErrorStore();
        $this->methodWillReturnFalse('hasErrors',$errors);
        $this->setProtectedAttributeOf($errorListWidget, 'errors', $errors);
        $this->setProtectedAttributeOf($errorListWidget, 'html', 'html');
        $returned = $errorListWidget->render();
        $this->assertEquals('html', $returned);
    }

    /**
     * @throws \PHPUnit_Framework_Constraint
     * @throws \ReflectionException
     */
    public function testRenderWhenExistErrors()
    {
        $errorListWidget = $this->newErrorListWidget();
        $errors = $this->newErrorStore();
        $viewErrorBag = $this->newInstanceWithDisableArgs(ViewErrorBag::class,['all']);
        $array = range(3,8);
        $this->methodWillReturnTrue('hasErrors',$errors);
        $this->methodWillReturn($viewErrorBag,'getErrors',$errors);
        $this->methodWillReturn($array,'all',$viewErrorBag);
        $this->setProtectedAttributeOf($errorListWidget, 'errors', $errors);
        $errorListWidget->expects($this->any(2))->method('getTemplate')->willReturn('template');
        for ($i = 0; $i <= count($array);$i++){
            $errorListWidget->expects($this->at($i+1))->method('formatTemplate')->willReturn(' item');
        }
        $errorListWidget->
        expects($this->at(count($array)+2))->
        method('formatTemplate')->will($this->returnArgument(0));
        $returned = $errorListWidget->render();
        $this->assertEquals('template', $returned);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function newErrorListWidget()
    {
        $methods = ['getTemplate', 'formatTemplate'];
        $errorListWidget = $this->newInstance(
            ErrorListWidget::class,
            [app(ErrorStore::class), app(OldInputStore::class)],
            $methods);
        return $errorListWidget;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function newErrorStore()
    {
        $methods = ['getErrors', 'hasErrors', 'all'];
        $errors = $this->newInstance(ErrorStore::class, [], $methods);
        return $errors;
    }
}
