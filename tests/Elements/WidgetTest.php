<?php

namespace Tests\Elements;

use LaraForm\Elements\Widget;
use LaraForm\Stores\BindStore;
use LaraForm\Stores\ErrorStore;
use LaraForm\Stores\OldInputStore;
use Tests\Core\BaseWidgetTest;

class WidgetTest extends BaseWidgetTest
{

    /**
     * @var
     */
    protected $widget;

    /**
     * @throws \ReflectionException
     */
    public function setUp()
    {
        parent::setUp();
        if (empty($this->widget)) {
            $this->widget = $this->newWidget();
        };

        $this->setProtectedAttributeOf($this->widget, 'config', config('lara_form'));
    }

    /**
     * @throws \PHPUnit_Framework_Constraint
     * @throws \ReflectionException
     */
    public function testCheckAttributesWhenExistIcon()
    {
        $data = ['icon' => 'fa-apple'];
        $widget = $this->newWidget(['getTemplate', 'formatTemplate', 'setOtherHtmlAttributesBy']);
        $this->methodWillReturnTrue('getTemplate', $widget);
        $this->methodWillReturn('fa-apple', 'formatTemplate', $widget);
        $widget->expects($this->any(2))->method('setOtherHtmlAttributesBy')->willReturn(true);
        $widget->checkAttributes($data);
        $icon = $this->getProtectedAttributeOf($widget, 'icon');
        $this->assertEquals([], $data);
        $this->assertEquals('fa-apple', $icon);
    }

    /**
     *
     */
    public function testCheckAttributesWhenExistOtherAttributes()
    {
        $data = ['readonly' => true, 'autocomplete' => true];
        $widget = $this->newWidget(['setOtherHtmlAttributesBy']);
        $widget->checkAttributes($data);
        $this->assertEquals(['readonly' => 'readonly', 'autocomplete' => 'on'], $data);
    }

    /**
     * @throws \PHPUnit_Framework_Constraint
     * @throws \ReflectionException
     */
    public function testSetParams()
    {
        $data = range(3, 20);
        $widget = $this->newWidget('addTemplateAndAttributes');
        $widget->expects($this->any(count($data)))->method('addTemplateAndAttributes')->willReturn(true);
        $widget->setParams($data);
        $containerParams = $this->getProtectedAttributeOf($widget, 'containerParams');
        $templates = $this->getProtectedAttributeOf($widget, 'templates');
        $classConcat = $this->getProtectedAttributeOf($widget, 'classConcat');
        $labelAttr = $this->getProtectedAttributeOf($widget, 'labelAttr');
        $this->assertEmpty($containerParams);
        $this->assertEmpty($templates);
        $this->assertEmpty($classConcat);
        $this->assertEmpty($labelAttr);
    }

    /**
     * @throws \ReflectionException
     */
    public function testBinding()
    {
        $this->widget->binding('value');
        $returned = $this->getProtectedAttributeOf($this->widget, 'bind');
        $this->assertEquals('value', $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testSetArguments()
    {
        $data = ['name', ['attr']];
        $this->widget->setArguments($data);
        $name = $this->getProtectedAttributeOf($this->widget, 'name');
        $attr = $this->getProtectedAttributeOf($this->widget, 'attr');
        $this->assertEquals('name', $name);
        $this->assertEquals(['attr'], $attr);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetErrorByFieldNameWhenEmptyErrors()
    {
        $errorStore = $this->newInstance(ErrorStore::class, [], 'hasError');
        $this->methodWillReturnFalse('hasError', $errorStore);
        $this->setProtectedAttributeOf($this->widget, 'errors', $errorStore);
        $returned = $this->widget->getErrorByFieldName('name');
        $this->assertEquals(['help' => '', 'error' => ''], $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetErrorByFieldNameWhenExistErrors()
    {
        $errorStore = $this->newInstance(ErrorStore::class, [], ['hasError', 'getError']);
        $widget = $this->newWidget(['getTemplate', 'formatTemplate']);
        $this->methodWillReturnTrue('hasError', $errorStore);
        $this->methodWillReturnTrue('getError', $errorStore);
        $this->methodWillReturnTrue('getTemplate', $widget);
        $this->methodWillReturn('template', 'formatTemplate', $widget);
        $this->setProtectedAttributeOf($widget, 'errors', $errorStore);
        $returned = $widget->getErrorByFieldName('name');
        $data = ['help' => 'template', 'error' => 'is-invalid'];
        $this->assertEquals($data, $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetValueWhenEmptyData()
    {
        $oldInputStore = $this->newInstance(OldInputStore::class, [], 'hasOldInput');
        $this->methodWillReturnFalse('hasOldInput', $oldInputStore);
        $this->setProtectedAttributeOf($this->widget, 'oldInputs', $oldInputStore);
        $returned = $this->invokeMethod($this->widget, 'getValue', ['name']);
        $this->assertEquals(['value' => ''], $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetValueWhenExistBind()
    {
        $oldInputStore = $this->newInstance(OldInputStore::class, [], 'hasOldInput');
        $bindStore = $this->newInstance(BindStore::class, [], 'get');
        $this->methodWillReturnFalse('hasOldInput', $oldInputStore);
        $this->methodWillReturn('bindData', 'get', $bindStore);
        $this->setProtectedAttributeOf($this->widget, 'oldInputs', $oldInputStore);
        $this->setProtectedAttributeOf($this->widget, 'bind', $bindStore);
        $returned = $this->invokeMethod($this->widget, 'getValue', ['name']);
        $this->assertEquals(['value' => 'bindData'], $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetValueWhenExistOldInputWithBind()
    {
        $oldInputStore = $this->newInstance(OldInputStore::class, [], ['hasOldInput', 'getOldInput']);
        $bindStore = $this->newInstance(BindStore::class, [], 'get');
        $this->methodWillReturnTrue('hasOldInput', $oldInputStore);
        $this->methodWillReturn('oldInputData', 'getOldInput', $oldInputStore);
        $this->methodWillReturn('bindData', 'get', $bindStore);
        $this->setProtectedAttributeOf($this->widget, 'oldInputs', $oldInputStore);
        $this->setProtectedAttributeOf($this->widget, 'bind', $bindStore);
        $returned = $this->invokeMethod($this->widget, 'getValue', ['name']);
        $this->assertEquals(['value' => 'oldInputData'], $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testRenderLabel()
    {
        $data = [
            'attrs' => true,
            'text' => 'name',
            'icon' => ''
        ];
        $widget = $this->newWidget(['getTemplate', 'formatAttributes', 'formatTemplate', 'formatClass']);
        $this->methodWillReturnTrue('formatAttributes', $widget);
        $this->methodWillReturnArgument(1, 'formatTemplate', $widget);
        $returned = $this->invokeMethod($widget, 'renderLabel', ['name', ['class' => 'class']]);
        $this->assertEquals($data, $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testEscept()
    {
        $widget = $this->newWidget('getIsEscept');
        $this->methodWillReturnTrue('getIsEscept', $widget);
        $returned = $this->invokeMethod($widget, 'escept', ['text']);
        $this->assertEquals('text', $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testCheckLabelWhenTreatmentTrue()
    {
        $data = ['inputName', ['id' => 'customId'], true];
        $widget = $this->newWidget(['escept', 'renderLabel']);
        $this->methodWillReturnArgument(1, 'renderLabel', $widget);
        $returned = $this->invokeMethod($widget, 'checkLabel', $data);
        $this->assertEquals(['for' => 'customId'], $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testCheckLabelWhenTreatmentFalse()
    {
        $data = ['inputName', [], false];
        $widget = $this->newWidget(['renderLabel', 'translate', 'getLabelName']);
        $this->methodWillReturnArgument(1, 'renderLabel', $widget);
        $returned = $this->invokeMethod($widget, 'checkLabel', $data);
        $this->assertEquals(['for' => 'inputName'], $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGenerateIdWhenIdFalse()
    {
        $data = ['id' => false];
        $this->invokeMethod($this->widget, 'generateId', [&$data]);
        $this->assertEquals([], $data);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGenerateIdWhenNotCustomId()
    {
        $data = [];
        $widget = $this->newWidget('getId');
        \Config::set('lara_form.css.id_prefix', 'prefix_');
        $this->methodWillReturn('generated_id', 'getId', $widget);
        $this->setProtectedAttributeOf($widget, 'config', config('lara_form'));
        $this->invokeMethod($widget, 'generateId', [&$data]);
        $this->assertEquals(['id' => 'prefix_generated_id'], $data);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGenerateIdWhenExistCustomId()
    {
        $data = ['id' => 'custom_id', 'id_prefix' => 'custom_prefix_'];
        $widget = $this->newWidget();
        $this->setProtectedAttributeOf($widget, 'config', config('lara_form'));
        $this->invokeMethod($widget, 'generateId', [&$data]);
        $this->assertEquals(['id' => 'custom_prefix_custom_id'], $data);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGenerateIdWhenExistMulti()
    {
        $data = ['id' => 'custom_id', 'value' => 5];
        $widget = $this->newWidget();
        $this->setProtectedAttributeOf($widget, 'config', config('lara_form'));
        $this->invokeMethod($widget, 'generateId', [&$data, true]);
        unset($data['value']);
        $this->assertEquals(['id' => 'custom_id-5'], $data);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGenerateLabelWhenExistLabel()
    {
        $data = ['label' => 'custom_label'];
        $widget = $this->newWidget(['getLabelAttributes', 'checkLabel']);
        $this->invokeMethod($widget, 'generateLabel', [&$data]);
        $this->assertEquals([], $data);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGenerateLabel()
    {
        $data = [];
        $attributes = ['text' => 'customLabel'];
        $widget = $this->newWidget(['getLabelAttributes', 'checkLabel']);
        $this->methodWillReturn($attributes, 'getLabelAttributes', $widget);
        $this->invokeMethod($widget, 'generateLabel', [&$data]);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGenerateLabelAuto()
    {
        $data = [];
        $widget = $this->newWidget(['getLabelAttributes', 'checkLabel']);
        $this->setProtectedAttributeOf($widget, 'config', config('lara_form'));
        $this->methodWillReturnFalse('getLabelAttributes', $widget);
        $this->invokeMethod($widget, 'generateLabel', [&$data]);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGeneratePlaceholderWhenExistPlaceholder()
    {
        $data = ['placeholder' => true];
        $widget = $this->newWidget(['translate', 'getLabelName']);
        $this->methodWillReturn('custom_placeholder', 'translate', $widget);
        $this->invokeMethod($widget, 'generatePlaceholder', [&$data]);
        $this->assertEquals(['placeholder' => 'custom_placeholder'], $data);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGeneratePlaceholderWhenExistConfigPlaceholder()
    {
        $data = [];
        \Config::set('lara_form.text.placeholder', true);
        $widget = $this->newWidget(['getLabelName']);
        $this->setProtectedAttributeOf($widget, 'config', config('lara_form'));
        $this->methodWillReturn('custom_placeholder', 'getLabelName', $widget);
        $this->invokeMethod($widget, 'generatePlaceholder', [&$data]);
        $this->assertEquals(['placeholder' => 'custom_placeholder'], $data);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGenerateClassWhenExistClassByValueFalse()
    {
        $data = ['class' => false];
        $this->invokeMethod($this->widget, 'generateClass', [&$data,false,false]);
        $hemlClass = $this->getProtectedAttributeOf($this->widget,'htmlClass');
        $this->assertEquals([],$hemlClass);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGenerateClassWhenNotClass()
    {
        $data = [];
        $this->invokeMethod($this->widget, 'generateClass', [&$data,'default',false]);
        $hemlClass = $this->getProtectedAttributeOf($this->widget,'htmlClass');
        $this->assertEquals(['default'],$hemlClass);
    }
    /**
     * @throws \ReflectionException
     */
    public function testGenerateClassWhenExistClass()
    {
        $data = ['class' => 'custom_class'];
        $errorStore = $this->newInstance(ErrorStore::class,[],'getError');
        \Config::set('lara_form.css.class.error','error_class');
        $this->methodWillReturnTrue('getError',$errorStore);
        $widget = $this->newWidget(['getHtmlClassControl', 'formatClass']);
        $this->setProtectedAttributeOf($widget, 'errors', $errorStore);
        $this->setProtectedAttributeOf($widget, 'config', config('lara_form'));
        $this->methodWillReturnTrue('getHtmlClassControl',$widget);
        $this->methodWillReturn('custom_class_str','formatClass',$widget);
        $this->invokeMethod($widget, 'generateClass', [&$data]);
        $hemlClass = $this->getProtectedAttributeOf($widget,'htmlClass');
        $this->assertEquals(  [false, 'custom_class', 'error_class'], $hemlClass);
        $this->assertEquals(  ['class' => 'custom_class_str'], $data);
    }

    /**
     * @throws \ReflectionException
     */
    public function testSetOtherHtmlAttributesBy()
    {
        $data = ['key' => true];
        $widget = $this->newWidget('setOtherHtmlAttributes');
        $this->methodWillReturnTrue('setOtherHtmlAttributes',$widget);
        $this->invokeMethod($widget, 'setOtherHtmlAttributesBy', [&$data,'key']);
        $this->assertEquals(['key' => 'key'],$data);
    }

    /**
     * @throws \ReflectionException
     */
    public function testSetHidden()
    {
        $widget = $this->newWidget(['getTemplate' , 'formatTemplate']);
        $this->methodWillReturn('hidden','formatTemplate',$widget);
        $returned = $this->invokeMethod($widget,'setHidden',['name']);
        $this->assertEquals('hidden',$returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testTranslate()
    {
        \Config::set('lara_form.translate_directive','form');
        $widget = $this->newWidget(['getTemplate' , 'formatTemplate']);
        $this->setProtectedAttributeOf($widget, 'config', config('lara_form'));
        $returned = $this->invokeMethod($widget,'translate',['str']);
        $this->assertEquals('form.str',$returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetIdCamelCase()
    {
        $this->getIdByCase('camel');
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetIdSnakeCase()
    {
        $this->getIdByCase('snake');
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetIdKebabCase()
    {
        $this->getIdByCase('kebab');
    }

    /**
     * @throws \ReflectionException
     */
    public function testBtnByTrue()
    {
        $attr = ['btn' => true];
        $this->invokeMethod($this->widget, 'btn', [&$attr,'btn','default']);
        $htmlClass = $this->getProtectedAttributeOf($this->widget, 'htmlClass');
        $this->assertEquals([], $attr);
        $this->assertEquals(['btn-default'], $htmlClass);
    }

    /**
     * @throws \ReflectionException
     */
    public function testMultipleByBrackets()
    {
        $attr = [];
        $this->setProtectedAttributeOf($this->widget, 'name', 'name[]');
        $this->invokeMethod($this->widget, 'multipleByBrackets', [&$attr]);
        $name = $this->getProtectedAttributeOf($this->widget, 'name');
        $this->assertEquals('name', $name);
        $this->assertEquals(['multiple' => true], $attr);
    }

    /**
     * @param $case
     * @throws \ReflectionException
     */
    private function getIdByCase($case)
    {
        \Config::set('lara_form.css.id_case',$case);
        $this->setProtectedAttributeOf($this->widget, 'config', config('lara_form'));
        $str = 'customField';
        $returned = $this->invokeMethod($this->widget, 'getId', [$str]);
        $str = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $str);
        $strCaseFunc = $case.'_case';
        $this->assertEquals($strCaseFunc($str),$returned);
    }
    
    /**
     * @param null $methods
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function newWidget($methods = null)
    {
        return $this->newInstance(Widget::class, [
            app(ErrorStore::class),
            app(OldInputStore::class),
        ], $methods);
    }
}
