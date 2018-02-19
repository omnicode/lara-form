<?php

namespace Tests\Elements\Widgets;

use LaraForm\Elements\Widgets\SelectWidget;
use LaraForm\Stores\ErrorStore;
use LaraForm\Stores\OldInputStore;
use Tests\Elements\WidgetTest;

class SelectWidgetTest extends WidgetTest
{
    protected $selectWidget;

    /**
     * @throws \ReflectionException
     */
    public function setUp()
    {
        parent::setUp();
        if (empty($this->selectWidget)) {
            $methods = [
                'getTemplate',
                'checkAttributes',
                'renderOptions',
                'formatAttributes',
                'completeTemplate',
                'formatTemplate',
                'setHtmlAttributes'
            ];
            $this->selectWidget = $this->newSelectWidget($methods);
        };

        $this->setProtectedAttributeOf($this->selectWidget, 'config', config('lara_form'));
    }

    /**
     * @throws \ReflectionException
     */
    public function testRender()
    {
        $pattern = [
            'content' => 'renderedOptions',
            'name' => null,
            'attrs' => 'formatedAttributes'
        ];
        $this->methodWillReturnArgument(0, 'getTemplate', $this->selectWidget);
        $this->methodWillReturnArgument(1, 'formatTemplate', $this->selectWidget);
        $this->methodWillReturnTrue('completeTemplate', $this->selectWidget);
        $this->methodWillReturn('renderedOptions', 'renderOptions', $this->selectWidget);
        $this->methodWillReturn('formatedAttributes', 'formatAttributes', $this->selectWidget);
        $returned = $this->selectWidget->render();
        $currentTemplate = $this->getProtectedAttributeOf($this->selectWidget, 'currentTemplate');
        $html = $this->getProtectedAttributeOf($this->selectWidget, 'html');
        $this->assertTrue($returned);
        $this->assertEquals('selectContainer', $currentTemplate);
        $this->assertEquals($pattern, $html);
    }

    /**
     * @throws \ReflectionException
     */
    public function testCheckAttributesWhenExistSelectedAndEmptyVal()
    {
        $mockMethods = [
            'generateId',
            'generateLabel',
            'generateClass',
            'getTemplate',
            'multipleByBrackets',
            'strToArray',
            'disabledBy',
            'parentCheckAttributes'
        ];
        $attr = [
            'empty' => '--select--',
            'selected' => ['1', '2', '3']
        ];
        $selectWidget = $this->newSelectWidget($mockMethods);
        $this->methodWillReturnArgument(0, 'getTemplate', $selectWidget);
        $this->methodWillReturn(['1', '2', '3'], 'strToArray', $selectWidget);
        $selectWidget->checkAttributes($attr);
        $selectTemplate = $this->getProtectedAttributeOf($selectWidget, 'selectTemplate');
        $selected = $this->getProtectedAttributeOf($selectWidget, 'selected');
        $optionsArray = $this->getProtectedAttributeOf($selectWidget, 'optionsArray');
        $this->assertEquals([], $attr);
        $this->assertEquals('select', $selectTemplate);
        $this->assertEquals(['1', '2', '3'], $selected);
        $this->assertEquals(['--select--'], $optionsArray);
    }

    /**
     * @throws \ReflectionException
     */
    public function testCheckAttributesWhenExistBindValAndMulti()
    {
        $mockMethods = [
            'generateId',
            'generateLabel',
            'generateClass',
            'getTemplate',
            'multipleByBrackets',
            'strToArray',
            'disabledBy',
            'parentCheckAttributes',
            'getValue',
            'setHidden'
        ];
        $attr = [
            'options' => [
                1 => '1',
                2 => '2',
                3 => '3'
            ],
            'multiple' => true
        ];
        $options = $attr['options'];
        \Config::set('lara_form.text.select_empty', 'se');
        list($selectTemplate, $selected, $optionsArray, $hidden) = $this->selectMulti($mockMethods, $attr);
        $this->assertEquals([], $attr);
        $this->assertEquals('hiddenTemplate', $hidden);
        $this->assertEquals('selectMultiple', $selectTemplate);
        $this->assertEquals([1, 2, 3], $selected);
        $this->assertEquals(['se'] + $options, $optionsArray);
    }

    /**
     * @throws \ReflectionException
     */
    public function testCheckAttributesWhenDisabledAndMulti()
    {
        $mockMethods = [
            'generateId',
            'generateLabel',
            'generateClass',
            'getTemplate',
            'multipleByBrackets',
            'strToArray',
            'disabledBy',
            'parentCheckAttributes',
            'getValue',
            'setHidden'
        ];
        $attr = [
            'options' => [
                1 => '1',
                2 => '2',
                3 => '3'
            ],
            'multiple' => true,
            'disabled' => true
        ];
        $options = $attr['options'];
        \Config::set('lara_form.text.select_empty', 'se');
        list($selectTemplate, $selected, $optionsArray, $hidden) = $this->selectMulti($mockMethods, $attr, false);
        $this->assertEquals(['disabled' => 'disabled'], $attr);
        $this->assertEquals('', $hidden);
        $this->assertEquals('selectMultiple', $selectTemplate);
        $this->assertEquals([1, 2, 3], $selected);
        $this->assertEquals(['se'] + $options, $optionsArray);
    }

    /**
     * @throws \PHPUnit_Framework_Constraint
     * @throws \ReflectionException
     */
    public function testRenderOptionsWhenNotGroups()
    {
        $mockMethods = [
            'getTemplate',
            'renderOptgroups',
            'isDisabled',
            'isSelected',
            'formatAttributes',
            'formatTemplate',
            'strToArray'
        ];

        $options = [
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
        ];
        $str = '';
        foreach ($options as $option) {
            $str .= 'option';
        }
        $selectWidget = $this->newSelectWidget($mockMethods);
        $this->methodWillReturnArgument(0, 'getTemplate', $selectWidget);
        $this->methodWillReturnArgument(0, 'strToArray', $selectWidget);
        $selectWidget->expects($this->any(count($options)))->method('formatTemplate')->will($this->returnArgument(0));
        $selectWidget->expects($this->any(count($options)))->method('isDisabled')->willReturn([]);
        $selectWidget->expects($this->any(count($options)))->method('isSelected')->willReturn([]);
        $this->setProtectedAttributeOf($selectWidget, 'optionsArray', $options);
        $returned = $this->invokeMethod($selectWidget, 'renderOptions', [false]);
        $this->assertEquals($str, $returned);
    }

    /**
     * @throws \PHPUnit_Framework_Constraint
     * @throws \ReflectionException
     */
    public function testRenderOptionsWhenExistGroups()
    {
        $mockMethods = [
            'getTemplate',
            'renderOptgroups',
            'isDisabled',
            'isSelected',
            'formatAttributes',
            'formatTemplate',
            'strToArray'
        ];

        $options = [
            1 => [1, 2, 3],
            2 => [1, 2, 3],
            3 => [1, 2, 3],
            4 => [1, 2, 3],
        ];
        $str = '';
        foreach ($options as $option) {
            $str .= 'group';
        }
        $selectWidget = $this->newSelectWidget($mockMethods);
        $this->methodWillReturnArgument(0, 'getTemplate', $selectWidget);
        $this->methodWillReturnArgument(0, 'strToArray', $selectWidget);
        $selectWidget->expects($this->any(count($options)))->method('renderOptgroups')->willReturn('group');
        $this->setProtectedAttributeOf($selectWidget, 'optionsArray', $options);
        $returned = $this->invokeMethod($selectWidget, 'renderOptions', [false]);
        $this->assertEquals($str, $returned);
    }

    public function testRenderOptgroupsWhenNotDisabled()
    {
        $mockMethods = [
            'getTemplate',
            'renderOptions',
            'getLabelName',
            'formatAttributes',
            'formatTemplate'
        ];
        $options = [
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
        ];
        $pattern = [
            'label' => 'groupName',
            'content' => $options,
            'attrs' => []
        ];
        $selectWidget = $this->newSelectWidget($mockMethods);
        $this->methodWillReturnArgument(1, 'formatTemplate', $selectWidget);
        $this->methodWillReturnArgument(0, 'formatAttributes', $selectWidget);
        $this->methodWillReturnArgument(0, 'getLabelName', $selectWidget);
        $this->methodWillReturnArgument(0, 'renderOptions', $selectWidget);
        $returned = $this->invokeMethod($selectWidget, 'renderOptgroups', ['groupName', $options]);
        $this->assertEquals($pattern, $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testRenderOptgroupsWhenExistDisabled()
    {
        $mockMethods = [
            'getTemplate',
            'renderOptions',
            'getLabelName',
            'formatAttributes',
            'formatTemplate',
            'isDisabled'
        ];
        $options = [
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
        ];
        $pattern = [
            'label' => 'groupName',
            'content' => $options,
            'attrs' => [
                'disabled' => 'disabled'
            ]
        ];

        $selectWidget = $this->newSelectWidget($mockMethods);
        $this->setProtectedAttributeOf($selectWidget, 'groupDisabled', 'disabled');
        $this->methodWillReturn(['disabled' => 'disabled'], 'isDisabled', $selectWidget);
        $this->methodWillReturnArgument(1, 'formatTemplate', $selectWidget);
        $this->methodWillReturnArgument(0, 'formatAttributes', $selectWidget);
        $this->methodWillReturnArgument(0, 'getLabelName', $selectWidget);
        $this->methodWillReturnArgument(0, 'renderOptions', $selectWidget);
        $returned = $this->invokeMethod($selectWidget, 'renderOptgroups', ['groupName', $options]);
        $this->assertEquals($pattern, $returned);

    }

    /**
     * @throws \ReflectionException
     */
    public function testIsDisabledWhenNotDisabledOptions()
    {
        $this->setProtectedAttributeOf($this->selectWidget, 'optionDisabled', ['str']);
        $returned = $this->invokeMethod($this->selectWidget, 'isDisabled', ['str']);
        $this->assertEquals(['disabled' => 'disabled'], $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testIsDisabledWhenExistDisabledOptions()
    {
        $opt = ['str'];
        $returned = $this->invokeMethod($this->selectWidget, 'isDisabled', ['str', $opt]);
        $this->assertEquals(['disabled' => 'disabled'], $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testIsSelected()
    {
        $this->setProtectedAttributeOf($this->selectWidget, 'selected', ['str']);
        $returned = $this->invokeMethod($this->selectWidget, 'isSelected', ['str']);
        $this->assertEquals(['selected' => 'selected'], $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testDisabledBy()
    {
        $attr = [
            'type' => 5
        ];
        $container = [];
        $selectWidget = $this->newSelectWidget('strToArray');
        $this->methodWillReturn([5], 'strToArray', $selectWidget);
        $this->invokeMethod($selectWidget, 'disabledBy', ['type', &$attr, &$container]);
        $this->assertEquals([], $attr);
        $this->assertEquals([5], $container);
    }

    /**
     * @param $mockMethods
     * @param $attr
     * @param bool $hide
     * @return array
     * @throws \ReflectionException
     */
    private function selectMulti($mockMethods, &$attr, $hide = true)
    {
        $keys = array_keys($attr['options']);
        $selectWidget = $this->newSelectWidget($mockMethods);
        $this->setProtectedAttributeOf($selectWidget, 'config', config('lara_form'));
        $this->methodWillReturnArgument(0, 'getTemplate', $selectWidget);
        $this->methodWillReturn(['value' => $keys], 'getValue', $selectWidget);
        $this->methodWillReturn($keys, 'strToArray', $selectWidget);
        if ($hide) {
            $this->methodWillReturn('hiddenTemplate', 'setHidden', $selectWidget);
        }
        $selectWidget->checkAttributes($attr);
        $selectTemplate = $this->getProtectedAttributeOf($selectWidget, 'selectTemplate');
        $selected = $this->getProtectedAttributeOf($selectWidget, 'selected');
        $optionsArray = $this->getProtectedAttributeOf($selectWidget, 'optionsArray');
        $hidden = $this->getProtectedAttributeOf($selectWidget, 'hidden');
        return [$selectTemplate, $selected, $optionsArray, $hidden];
    }

    /**
     * @param null $methods
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function newSelectWidget($methods = null)
    {
        return $this->newInstance(
            SelectWidget::class,
            [app(ErrorStore::class), app(OldInputStore::class)],
            $methods);
    }
}
