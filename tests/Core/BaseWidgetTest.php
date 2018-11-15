<?php

namespace Tests\Core;

use LaraForm\Core\BaseWidget;
use Tests\LaraFormTestCase;
use TestsTestCase;

class BaseWidgetTest extends LaraFormTestCase
{
    /**
     * @var
     */
    protected $baseWidget;

    /**
     * @throws \ReflectionException
     */
    public function setUp()
    {
        parent::setUp();
        if (empty($this->baseWidget)) {
            $this->baseWidget = $this->newBaseWidget();
        };

        $this->setProtectedAttributeOf($this->baseWidget, 'config', config('lara_form'));
    }

    /**
     * @throws \ReflectionException
     */
    public function testAddTemplateAndAttributes()
    {
        \Config::set('lara_form.templates', ['field' => 'content']);
        $params = [
            [
                'div' => [],
                'label' => [],
                'escept' => true,
                'class_concat' => true,
                'pattern' => [
                    'field' => 'customContent'
                ]
            ],
            'permission'
        ];
        $this->setProtectedAttributeOf($this->baseWidget, 'config', config('lara_form'));
        $this->invokeMethod($this->baseWidget, 'addTemplateAndAttributes', $params);
        $div = $this->getProtectedAttributeOf($this->baseWidget, 'containerParams');
        $classConcat = $this->getProtectedAttributeOf($this->baseWidget, 'classConcat');
        $label = $this->getProtectedAttributeOf($this->baseWidget, 'labelAttr');
        $escept = $this->getProtectedAttributeOf($this->baseWidget, 'escept');
        $templates = $this->getProtectedAttributeOf($this->baseWidget, 'templates');
        $this->assertEquals($params[0]['div'], $div['permission']);
        $this->assertEquals($params[0]['label'], $label['permission']);
        $this->assertEquals($params[0]['class_concat'], $classConcat['permission']);
        $this->assertEquals($params[0]['escept'], $escept['permission']);
        $this->assertArrayHasKey('field', $templates['permission']);
    }

    /**
     * @throws \ReflectionException
     */
    public function testFormatTemplateWhenEmptyAttributes()
    {
        $returned = $this->invokeMethod($this->baseWidget, 'formatTemplate', ['template', []]);
        $this->assertEquals('template', $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testFormatTemplateWhenExistAttributes()
    {
        $data = [
            '<input name="{%name%}">',
            [
                'name' => 'user'
            ]
        ];
        $baseWidget = $this->newBaseWidget('transformTemplate');
        $returned = $this->invokeMethod($baseWidget, 'formatTemplate', $data);
        $this->assertEquals('<input name="user">', $returned);
    }

    /**
     * @throws \Exception
     * @expectedException Exception
     */
    public function testTransformTemplateWhenExistInvalidSeparators()
    {
        \Config::set('lara_form.separator.start','#');
        \Config::set('lara_form.separator.end','&');
        $tmp = '<input name="#name$">';
        $this->setProtectedAttributeOf($this->baseWidget, 'config', config('lara_form'));
        $this->invokeMethod($this->baseWidget, 'transformTemplate', [&$tmp]);
    }

    /**
     * @throws \ReflectionException
     */
    public function testTransformTemplateWhenExistValidSeparators()
    {
        \Config::set('lara_form.separator', ['start' => '{', 'end' => '}']);
        $template = '<input name="{name}">';
        $this->setProtectedAttributeOf($this->baseWidget, 'config', config('lara_form'));
        $this->invokeMethod($this->baseWidget, 'transformTemplate', [&$template]);
        $this->assertEquals('<input name="{%name%}">', $template);
    }

    /**
     * @throws \ReflectionException
     */
    public function testFormatAttributesWhenEmptyAttr()
    {
        $baseWidget = $this->newBaseWidget('formatClass');
        $this->methodWillReturn('', 'formatClass', $baseWidget);
        $returned = $this->invokeMethod($baseWidget, 'formatAttributes', [[]]);
        $this->assertEquals('', $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testFormatAttributesWhenExistAttrbutes()
    {
        $data = [
            'id' => 'field-id',
            'readonly',
            'readonly'
        ];
        $baseWidget = $this->newBaseWidget(['setOtherHtmlAttributes', 'formatClass']);
        $this->methodWillReturn('col', 'formatClass', $baseWidget);
        $returned = $this->invokeMethod($baseWidget, 'formatAttributes', [$data]);
        $this->assertEquals('id="field-id" readonly class="col" ', $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testFormatClassWhenEmptyClasses()
    {
        $returned = $this->invokeMethod($this->baseWidget, 'formatClass', [[]]);
        $this->assertEquals('', $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testFormatClassWhenEmptyClassesButExistHtmlClass()
    {
        $data = ['col', 'col', 'header', 'col header', 'col-md-2 col-md-2'];
        $this->setProtectedAttributeOf($this->baseWidget, 'htmlClass', $data);
        $returned = $this->invokeMethod($this->baseWidget, 'formatClass', [[]]);
        $this->assertEquals('col header col-md-2', $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testCompleteTemplateWhenExistCurrentTemplate()
    {
        $currentTemplate = config('lara_form.templates.inputContainer');
        $mockMethods = ['formatTemplate', 'getErrorByFieldName', 'getContainerAllAttributes', 'resetProperties'];
        $baseWidget = $this->newBaseWidget($mockMethods);

        $this->setProtectedAttributeOf($baseWidget, 'currentTemplate', $currentTemplate);
        $this->setProtectedAttributeOf($baseWidget, 'containerParams', ['inline' => [], 'global' => [], 'local' => []]);

        $this->methodWillReturn([], 'resetProperties', $baseWidget);
        $this->methodWillReturn([], 'getErrorByFieldName', $baseWidget);
        $this->methodWillReturn([], 'getContainerAllAttributes', $baseWidget);
        $this->methodWillReturn($currentTemplate, 'formatTemplate', $baseWidget);

        $returned = $this->invokeMethod($baseWidget, 'completeTemplate');
        $this->assertEquals($currentTemplate, $returned);
    }

    /**
     * @throws \PHPUnit_Framework_Constraint
     * @throws \ReflectionException
     */
    public function testCompleteTemplateWhenExistInHtmlAttributes()
    {
        $currentTemplate = config('lara_form.templates.inputContainer');
        $mockMethods = ['formatTemplate', 'getErrorByFieldName', 'getContainerAllAttributes', 'resetProperties', 'getHtmlAttributes', 'getTemplate'];
        $baseWidget = $this->newBaseWidget($mockMethods);
        $this->setProtectedAttributeOf($baseWidget, 'containerParams', ['inline' => [], 'global' => [], 'local' => []]);
        $baseWidget->expects($this->any(2))->method('getHtmlAttributes')->willReturn('input');
        $this->methodWillReturn($currentTemplate, 'getTemplate', $baseWidget);
        $this->methodWillReturn([], 'resetProperties', $baseWidget);
        $this->methodWillReturn([], 'getErrorByFieldName', $baseWidget);
        $this->methodWillReturn([], 'getContainerAllAttributes', $baseWidget);
        $this->methodWillReturnArgument(0, 'formatTemplate', $baseWidget);

        $returned = $this->invokeMethod($baseWidget, 'completeTemplate');
        $this->assertEquals($currentTemplate, $returned);
    }

    /**
     * @throws \PHPUnit_Framework_Constraint
     * @throws \ReflectionException
     */
    public function testCompleteTemplateWhenTypeOfParamsNotArray()
    {
        $currentTemplate = config('lara_form.templates.inputContainer');
        $mockMethods = ['formatTemplate', 'getErrorByFieldName', 'getContainerAllAttributes', 'resetProperties', 'getHtmlAttributes', 'getTemplate'];
        $baseWidget = $this->newBaseWidget($mockMethods);
        $this->setProtectedAttributeOf($baseWidget, 'containerParams', ['inline' => false, 'global' => false, 'local' => false]);
        $baseWidget->expects($this->any(2))->method('getHtmlAttributes')->willReturn('input');
        $this->methodWillReturn($currentTemplate, 'getTemplate', $baseWidget);
        $this->methodWillReturn([], 'resetProperties', $baseWidget);
        $this->methodWillReturn([], 'getErrorByFieldName', $baseWidget);
        $this->methodWillReturn([], 'getContainerAllAttributes', $baseWidget);
        $this->methodWillReturnArgument(0, 'formatTemplate', $baseWidget);

        $returned = $this->invokeMethod($baseWidget, 'completeTemplate');
        $this->assertEquals(strip_tags($currentTemplate), $returned);
    }

    /**
     * @throws \PHPUnit_Framework_Constraint
     * @throws \ReflectionException
     */
    public function testCompleteTemplateWhenExistHiddenInHtmlAttributes()
    {
        $baseWidget = $this->newBaseWidget('getHtmlAttributes');
        $this->setProtectedAttributeOf($baseWidget, 'html', 'html');
        $baseWidget->expects($this->any(2))->method('getHtmlAttributes')->willReturn('hidden');
        $returned = $this->invokeMethod($baseWidget, 'completeTemplate');
        $this->assertEquals('html', $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testResetProperties()
    {
        $this->setProtectedAttributeOf($this->baseWidget, 'icon', 'icon');
        $this->setProtectedAttributeOf($this->baseWidget, 'label', 'label');
        $this->setProtectedAttributeOf($this->baseWidget, 'htmlClass', ['class']);
        $this->setProtectedAttributeOf($this->baseWidget, 'attr', ['attr']);
        $this->invokeMethod($this->baseWidget, 'resetProperties');
        $icon = $this->getProtectedAttributeOf($this->baseWidget, 'icon');
        $label = $this->getProtectedAttributeOf($this->baseWidget, 'label');
        $htmlClass = $this->getProtectedAttributeOf($this->baseWidget, 'htmlClass');
        $attr = $this->getProtectedAttributeOf($this->baseWidget, 'attr');
        $this->assertEquals('', $icon);
        $this->assertEquals('', $label);
        $this->assertEquals([], $htmlClass);
        $this->assertEquals([], $attr);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetModifiedDataFromInline()
    {
        $data = [
            'inline' => [
                'field' => 'content'
            ]
        ];
        $returned = $this->invokeMethod($this->baseWidget, 'getModifiedData', [$data]);
        $this->assertEquals($data['inline'], $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetModifiedDataFromLocal()
    {
        $data = [
            'local' => [
                'field' => 'content'
            ]
        ];
        $returned = $this->invokeMethod($this->baseWidget, 'getModifiedData', [$data]);
        $this->assertEquals($data['local'], $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetModifiedDataFromGlobal()
    {
        $data = [
            'global' => [
                'field' => 'content'
            ]
        ];
        $returned = $this->invokeMethod($this->baseWidget, 'getModifiedData', [$data]);
        $this->assertEquals($data['global'], $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetModifiedDataDefault()
    {
        $returned = $this->invokeMethod($this->baseWidget, 'getModifiedData', [[], 'default']);
        $this->assertEquals('default', $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetTemplateFormInline()
    {
        $this->getTemplateBy('inline');
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetTemplateFormLocal()
    {
        $this->getTemplateBy('local');
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetTemplateFormGlobal()
    {
        $this->getTemplateBy('global');
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetLabelAttributes()
    {
        $baseWidget = $this->newBaseWidget('getModifiedData');
        $this->setProtectedAttributeOf($baseWidget, 'labelAttr', ['value']);
        $this->methodWillReturnArgument(0, 'getModifiedData', $baseWidget);
        $returned = $this->invokeMethod($baseWidget, 'getLabelAttributes');
        $this->assertEquals(['value'], $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetHtmlClassControlByArgumentOne()
    {
        $this->getModifiedDataArgumentOne('getHtmlClassControl', 'classConcat',['v']);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetHtmlClassControlByArgumentTwo()
    {
        \Config::set('lara_form.css.class_control', true);
        $this->getModifiedDataArgumentTwo('getHtmlClassControl');
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetIsEsceptByArgumentOne()
    {
        $baseWidget = $this->newBaseWidget('getModifiedData');
        $this->setProtectedAttributeOf($baseWidget, 'escept', [true]);
        $this->methodWillReturnTrue( 'getModifiedData', $baseWidget);
        $returned = $this->invokeMethod($baseWidget, 'getIsEscept');
        $this->assertEquals(true, $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetIsEsceptByArgumentTwo()
    {
        \Config::set('lara_form.escept', true);
        $this->getModifiedDataArgumentTwo('getIsEscept');
    }

    /**
     * @throws \PHPUnit_Framework_Constraint
     * @throws \ReflectionException
     */
    public function testGetContainerAllAttributes()
    {
        $data = [
            'inline' => [],
            'global' => [],
            'local' => [],
        ];
        $params1 = $this->containerAttrGenerate();
        $params2 = $this->containerAttrGenerate();
        $params3 = $this->containerAttrGenerate();
        $baseWidget = $this->newBaseWidget('getContainerAttributes');
        $this->setProtectedAttributeOf($baseWidget, 'containerParams', $data);
        $baseWidget->expects($this->at(0))->method('getContainerAttributes')->willReturn($params1);
        $baseWidget->expects($this->at(1))->method('getContainerAttributes')->willReturn($params2);
        $baseWidget->expects($this->at(2))->method('getContainerAttributes')->willReturn($params3);
        $returned = $this->invokeMethod($baseWidget, 'getContainerAllAttributes');
        $this->assertEquals($params3, $returned);
    }

    /**
     * @throws \PHPUnit_Framework_Constraint
     * @throws \ReflectionException
     */
    public function testGetContainerAttributes()
    {
        $mockMethods = ['containerAttributeRequiredAndDisabled', 'containerAttributeType', 'containerAttributeClass', 'formatAttributes'];
        $baseWidget = $this->newBaseWidget($mockMethods);
        $baseWidget->expects($this->any(2))->method('containerAttributeRequiredAndDisabled')->willReturn([]);
        $this->methodWillReturn([], 'containerAttributeType', $baseWidget);
        $this->methodWillReturn([], 'containerAttributeClass', $baseWidget);
        $this->methodWillReturn('data', 'formatAttributes', $baseWidget);
        $returned = $this->invokeMethod($baseWidget, 'getContainerAttributes', [['containerAttrs' => 'data']]);
        $this->assertEquals(['containerAttrs' => 'data'], $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testContainerAttributeRequiredAndDisabledWhenNotParamAndGetOtherHtmlAttributesReturnedTrue()
    {
        $data = [];
        $baseWidget = $this->newBaseWidget('getOtherHtmlAttributes');
        $this->methodWillReturnTrue('getOtherHtmlAttributes', $baseWidget);
        $returned = $this->invokeMethod($baseWidget, 'containerAttributeRequiredAndDisabled', [&$data, 'required']);
        $this->assertEquals(['required' => 'required'], $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testContainerAttributeRequiredAndDisabledWhenExistParamAndGetOtherHtmlAttributesReturnedTrue()
    {
        $data = ['required' => 'customClass'];
        $baseWidget = $this->newBaseWidget('getOtherHtmlAttributes');
        $this->methodWillReturnTrue('getOtherHtmlAttributes', $baseWidget);
        $returned = $this->invokeMethod($baseWidget, 'containerAttributeRequiredAndDisabled', [&$data, 'required']);
        $this->assertEquals(['required' => 'customClass'], $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testContainerAttributeRequiredWhenExistParamAndGetOtherHtmlAttributesReturnedFalse()
    {
        $data = ['required' => 'customClass'];
        $baseWidget = $this->newBaseWidget('getOtherHtmlAttributes');
        $this->methodWillReturnFalse('getOtherHtmlAttributes', $baseWidget);
        $returned = $this->invokeMethod($baseWidget, 'containerAttributeRequiredAndDisabled', [&$data, 'required']);
        $this->assertEquals([], $data);
        $this->assertEquals([], $returned);
    }


    /**
     * @throws \PHPUnit_Framework_Constraint
     * @throws \ReflectionException
     */
    public function testContainerAttributeTypeWhenEmptyParamAndGetOtherHtmlAttributesReturnedTrue()
    {
        $data = [];
        $baseWidget = $this->newBaseWidget('getOtherHtmlAttributes');
        $baseWidget->expects($this->any(2))->method('getOtherHtmlAttributes')->willReturn(true);
        $returned = $this->invokeMethod($baseWidget, 'containerAttributeType', [&$data]);
        $this->assertEquals(['type' => true], $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testContainerAttributeTypeWhenEmptyParamAndGetOtherHtmlAttributesReturnedFalse()
    {
        $data = [];
        $baseWidget = $this->newBaseWidget(['getOtherHtmlAttributes', 'getHtmlAttributes']);
        $this->methodWillReturnFalse('getOtherHtmlAttributes', $baseWidget);
        $this->methodWillReturnTrue('getHtmlAttributes', $baseWidget);
        $returned = $this->invokeMethod($baseWidget, 'containerAttributeType', [&$data]);
        $this->assertEquals(['type' => true], $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testContainerAttributeTypeWhenExistParam()
    {
        $data = ['type' => 'value'];
        $returned = $this->invokeMethod($this->baseWidget, 'containerAttributeType', [&$data]);
        $this->assertEquals([], $data);
        $this->assertEquals(['type' => 'value'], $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testContainerAttributeClassWhenEmptyClass()
    {
        $data = [];
        $returned = $this->invokeMethod($this->baseWidget, 'containerAttributeClass', [&$data]);
        $this->assertEquals([], $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testContainerAttributeClassWhenExistClass()
    {
        $data = ['class' => 'col-12'];
        $baseWidget = $this->newBaseWidget('formatClass');
        $this->methodWillReturn('class', 'formatClass', $baseWidget);
        $returned = $this->invokeMethod($baseWidget, 'containerAttributeClass', [&$data]);
        $this->assertEquals(['class' => 'class'], $returned);
        $this->assertEquals([], $data);
    }

    /**
     * @throws \ReflectionException
     */
    public function testSetOtherHtmlAttributesWhenKeyIsArray()
    {
        $data = [
            [
                'field-1',
                'field-2',
                'field-3'
            ]
        ];
        $this->invokeMethod($this->baseWidget, 'setOtherHtmlAttributes', $data);
        $returned = $this->getProtectedAttributeOf($this->baseWidget, 'otherHtmlAttributes');
        $this->assertEquals($data[0], $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testSetOtherHtmlAttributesByKeyValue()
    {
        $this->invokeMethod($this->baseWidget, 'setOtherHtmlAttributes', ['key', 'value']);
        $returned = $this->getProtectedAttributeOf($this->baseWidget, 'otherHtmlAttributes');
        $this->assertEquals(['key' => 'value'], $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testAssignOtherhtmlAtrributes()
    {
        $this->invokeMethod($this->baseWidget, 'assignOtherhtmlAtrributes', [['data']]);
        $returned = $this->getProtectedAttributeOf($this->baseWidget, 'otherHtmlAttributes');
        $this->assertEquals(['data'], $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetOtherHtmlAttributesWhenEmptyKey()
    {
        $this->getAttributesByKey('otherHtmlAttributes', 'getOtherHtmlAttributes');
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetOtherHtmlAttributeByKeyWhenNotFound()
    {
        $this->getAttributesByKey('otherHtmlAttributes', 'getOtherHtmlAttributes', 'key3', true);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetOtherHtmlAttributeByKey()
    {
        $this->getAttributesByKey('otherHtmlAttributes', 'getOtherHtmlAttributes', 'key2');
    }

    /**
     * @throws \ReflectionException
     */
    public function testSetHtmlAttributes()
    {
        $this->invokeMethod($this->baseWidget, 'setHtmlAttributes', ['key', 'value']);
        $returned = $this->getProtectedAttributeOf($this->baseWidget, 'htmlAttributes');
        $this->assertEquals(['key' => 'value'], $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetHtmlAttributesWhenEmptyKey()
    {
        $this->getAttributesByKey('htmlAttributes', 'getHtmlAttributes');
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetHtmlAttributesByKeyWhenNotFound()
    {
        $this->getAttributesByKey('htmlAttributes', 'getHtmlAttributes', 'key3', true);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetHtmlAttributesByKey()
    {
        $this->getAttributesByKey('htmlAttributes', 'getHtmlAttributes', 'key1');
    }

    /**
     * @param $attribute
     * @param $method
     * @param null $key
     * @param bool $isEmpty
     * @throws \ReflectionException
     */
    private function getAttributesByKey($attribute, $method, $key = null, $isEmpty = false)
    {
        $data = ['key1' => 'value1', 'key2' => 'value2'];
        $this->setProtectedAttributeOf($this->baseWidget, $attribute, $data);
        $returned = $this->invokeMethod($this->baseWidget, $method, [$key]);
        if ($isEmpty) {
            $this->assertFalse($returned);
        } else {
            if (!empty($key)) {
                $data = $data[$key];
            }
            $this->assertEquals($data, $returned);
        }
    }

    /**
     * @return array
     */
    private function containerAttrGenerate()
    {
        $params = [
            'required' => str_random(10),
            'disabled' => str_random(10),
            'type' => str_random(10),
            'containerAttrs' => str_random(10),
            'class' => str_random(10),
        ];
        return $params;
    }

    /**
     * @param $method
     * @param $prop
     * @throws \ReflectionException
     */
    private function getModifiedDataArgumentOne($method, $prop, $val = 'value')
    {
        $baseWidget = $this->newBaseWidget('getModifiedData');
        $this->setProtectedAttributeOf($baseWidget, $prop, $val);
        $this->methodWillReturnArgument(0, 'getModifiedData', $baseWidget);
        $returned = $this->invokeMethod($baseWidget, $method);
        $this->assertEquals($val, $returned);
    }

    /**
     * @param $method
     * @throws \ReflectionException
     */
    private function getModifiedDataArgumentTwo($method)
    {
        $baseWidget = $this->newBaseWidget('getModifiedData');
        $this->setProtectedAttributeOf($baseWidget, 'config', config('lara_form'));
        $this->methodWillReturnArgument(1, 'getModifiedData', $baseWidget);
        $returned = $this->invokeMethod($baseWidget, $method);
        $this->assertEquals(true, $returned);
    }

    /**
     * @param $permision
     * @throws \ReflectionException
     */
    private function getTemplateBy($permision)
    {
        $tmp = 'custom_template' . '_' . $permision;
        $data = [
            $permision => [
                'input' => $tmp
            ]
        ];
        $this->setProtectedAttributeOf($this->baseWidget, 'templates', $data);
        $returned = $this->invokeMethod($this->baseWidget, 'getTemplate', ['input']);
        $this->assertEquals($tmp, $returned);
    }

    /**
     * @param null $methods
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function newBaseWidget($methods = null)
    {
        return $this->newInstance(BaseWidget::class, [], $methods);
    }
}
