<?php

namespace LaraForm\Tests;

use Illuminate\Support\Facades\URL;
use LaraForm\Facades\LaraForm;
use LaraForm\FormBuilder;
use LaraForm\FormProtection;
use LaraForm\Stores\BindStore;
use LaraForm\Stores\ErrorStore;
use LaraForm\Stores\OldInputStore;
use LaraForm\Stores\OptionStore;
use LaraForm\Tests\Core\BaseFormBuilderTest;
use LaraForm\Traits\FormControl;
use LaraTest\Traits\AssertionTraits;
use LaraTest\Traits\MockTraits;

class FormBuilderTest extends BaseFormBuilderTest
{

    /**
     * @var
     */
    protected $formBuilder;

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws \PHPUnit_Framework_Exception
     * @throws \PHPUnit_Framework_MockObject_RuntimeException
     */
    public function setUp()
    {
        parent::setUp();
        if (empty($this->formBuilder)) {
            $mthods = ['generateToken', 'getAction', 'getMethod', 'isForm', 'getIsForm', 'addTemplatesAndParams'];
            $this->formBuilder = $this->newFormBuilder($mthods);
        };

    }

    /**
     * @param null $methods
     * @return \PHPUnit_Framework_MockObject_MockObject
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws \PHPUnit_Framework_Exception
     * @throws \PHPUnit_Framework_MockObject_RuntimeException
     */
    private function newFormBuilder($methods = null)
    {
        $formBulder = $this->getMockBuilder(FormBuilder::class)
            ->setConstructorArgs([
                app(FormProtection::class),
                app(ErrorStore::class),
                app(OldInputStore::class),
                app(OptionStore::class),
                app(BindStore::class),
            ])
            ->setMethods($methods)
            ->getMock();
        return $formBulder;
    }

    /**
     * @throws \PHPUnit_Framework_Exception
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testConstruct()
    {
        $this->assertClassAttributeInstanceOf(FormProtection::class, $this->formBuilder, 'formProtection');
        $this->assertClassAttributeInstanceOf(ErrorStore::class, $this->formBuilder, 'errorStore');
        $this->assertClassAttributeInstanceOf(OldInputStore::class, $this->formBuilder, 'oldInputStore');
        $this->assertClassAttributeInstanceOf(OptionStore::class, $this->formBuilder, 'optionStore');
        $this->assertClassAttributeInstanceOf(BindStore::class, $this->formBuilder, 'bindStore');
    }


    /**
     * @throws \Exception
     * @expectedException Exception
     */
    public function testCreateWhenIsFormTrue()
    {
        $this->formBuilder->create(null, []);
        $this->formBuilder->create(null, []);
    }

    /**
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testCreateWhenIsFormFalse()
    {
        $charset = config('lara_form.charset');
        $pattern =
            '<form action="/foo/bar" method="POST" accept-charset="' . $charset . '" >' .
            '<input type="hidden" name="_token" value="">' .
            '<input type="hidden" name="_method" value="PUT">' .
            '<input type="hidden" name="laraform_token" value="11111"/>';

        $this->methodWillReturn('11111', 'generateToken', $this->formBuilder);
        $this->methodWillReturn('/foo/bar', 'getAction', $this->formBuilder);
        $this->methodWillReturn('put', 'getMethod', $this->formBuilder);

        $form = '' . $this->formBuilder->create(null);
        $isForm = $this->getProtectedAttributeOf($this->formBuilder, 'isForm');
        $model = $this->getProtectedAttributeOf($this->formBuilder, 'model');

        $this->assertEquals(true, $isForm);
        $this->assertEquals(null, $model);
        $this->assertEquals($pattern, $form);
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws \PHPUnit_Framework_Exception
     * @throws \PHPUnit_Framework_ExpectationFailedException
     * @throws \PHPUnit_Framework_MockObject_RuntimeException
     * @throws \RuntimeException
     */
    public function testEndWhenIsFormTrue()
    {
        $formBuilder = $this->newFormBuilder(['make','resetProperties']);
        $val = str_random(5);
        $this->methodWillReturn($val,'make',$formBuilder);
        $this->methodWillReturnTrue('resetProperties',$formBuilder);
        $this->getProtectedMethod($formBuilder,'setIsForm',[true]);
        $end = $formBuilder->end();
        $this->assertEquals($val,$end);
    }

    /**
     * @throws \PHPUnit_Framework_AssertionFailedError
     */
    public function testEndWhenIsFormFalse()
    {
        $end = $this->formBuilder->end();
        $this->assertEmpty($end);
    }

    /**
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testGetIsForm()
    {
        $getterIsForm = $this->getProtectedMethod($this->formBuilder,'getIsForm');
        $isForm = $this->getProtectedAttributeOf($this->formBuilder,'isForm');
        $this->assertEquals($getterIsForm,$isForm);
    }

    /**
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testGetFieldTypeForButtonType()
    {
        $this->assertGetFieldType('button', 'submit');
    }

    /**
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testGetFieldTypeForResetType()
    {
        $this->assertGetFieldType('reset', 'submit');
    }

    /**
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testGetFieldTypeForSubmitType()
    {
        $this->assertGetFieldType('submit', 'submit');
    }


    /**
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testGetFieldTypeForCheckboxType()
    {
        $this->assertGetFieldType('checkbox', 'checkbox');
    }

    /**
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testGetFieldTypeForRadioType()
    {
        $this->assertGetFieldType('radio', 'radio');
    }

    /**
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testGetFieldTypeForFileType()
    {
        $this->assertGetFieldType('file', 'file');
    }

    /**
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testGetFieldTypeForTextareaType()
    {
        $this->assertGetFieldType('textarea', 'textarea');
    }

    /**
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testGetFieldTypeForHiddenType()
    {
        $this->assertGetFieldType('hidden', 'hidden');
    }

    /**
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testGetFieldTypeForLabelType()
    {
        $this->assertGetFieldType('label', 'label');
    }

    /**
     * @throws \PHPUnit_Framework_AssertionFailedError
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testFixFieldWithHiddenByDefaultValue()
    {
        $this->assertFixField('name', 'hidden');
    }

    /**
     * @throws \PHPUnit_Framework_AssertionFailedError
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testFixFieldWithHiddenByCustomValue()
    {
        $this->assertFixField('name', 'hidden', [5]);
    }

    /**
     * @throws \PHPUnit_Framework_AssertionFailedError
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testFixFieldWithReadonly()
    {
        $this->assertFixField('name', 'input', ['readonly' => true, 'value' => 55]);
    }

    /**
     * @throws \PHPUnit_Framework_AssertionFailedError
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testFixFieldWithSubmit()
    {
        $this->assertFixField('name', 'submit', [], true);
    }

    /**
     * @throws \PHPUnit_Framework_AssertionFailedError
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testFixFieldWithReset()
    {
        $this->assertFixField('name', 'reset', [], true);
    }

    /**
     * @throws \PHPUnit_Framework_AssertionFailedError
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testFixFieldWithButton()
    {
        $this->assertFixField('name', 'button', [], true);
    }

    /**
     * @throws \PHPUnit_Framework_AssertionFailedError
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testFixFieldWithLabel()
    {
        $this->assertFixField('name', 'label', [], true);
    }

    /**
     *
     */
    public function testSetTemplateByArgumentsLocal()
    {
        $this->setTemplateByArgs('localTemplates', false);
    }


    /**
     *
     */
    public function testSetTemplateByArgumentsGlobal()
    {
        $this->setTemplateByArgs('globalTemplates', true);
    }

    /**
     * @throws \PHPUnit_Framework_AssertionFailedError
     */
    public function testSetTemplatesByArrayNotOptions()
    {
        $tmp = [
            'input' => '<input type="{%type%}" name="{%name%}" {%attrs%}/>',
            'checkbox' => '<input type="checkbox" name="{%name%}" {%attrs%}/>',
        ];
        $this->methodWillReturnTrue('addTemplatesAndParams', $this->formBuilder);
        $this->formBuilder->setTemplate($tmp);
        $this->assertTrue(true); //todo
    }

    /**
     * @throws \PHPUnit_Framework_AssertionFailedError
     */
    public function testSetTemplatesByArrayIssetOptionsIsGlobal()
    {
        $tmp = [
            'input' => '<input type="{%type%}" name="{%name%}" {%attrs%}/>',
            'checkbox' => '<input type="checkbox" name="{%name%}" {%attrs%}/>',
            '_options' => [
                'global' => true
            ]
        ];

        $this->formBuilder->setTemplate($tmp);
        $this->methodWillReturnTrue('addTemplatesAndParams', $this->formBuilder);
        $this->formBuilder->setTemplate($tmp);
        $this->assertTrue(true); //todo
    }

    /**
     * @throws \PHPUnit_Framework_AssertionFailedError
     */
    public function testSetIsForm()
    {
        $this->getProtectedMethod($this->formBuilder,'setIsForm',[true]);
        $isForm = $this->getProtectedAttributeOf($this->formBuilder,'isForm');
        $this->assertTrue($isForm);
    }

    /**
     * @throws \Exception
     * @expectedException Exception
     */
    public function testSetIsFormSecondTime()
    {
        $this->getProtectedMethod($this->formBuilder,'setIsForm',[true]);
        $this->getProtectedMethod($this->formBuilder,'setIsForm',[true]);
    }

   /* public function testSetTemplatesByArrayIssetOptionsIsDiv()
    {  //todo
        $tmp = [
            '_options' => [
                'div' => [
                    'class' => 'has-error'
                ]
            ]
        ];

        $this->formBuilder->setTemplate($tmp);
        $propery = $this->getProtectedAttributeOf($this->formBuilder, 'localTemplates');
        $this->assertEquals($propery['div'], $tmp['_options']['div']);
    }

    public function testSetTemplatesByArrayIssetOptionsIsClassConcat()
    { //todo
        $tmp = [
            '_options' => [
                'class_concat' => false
            ]
        ];

        $this->formBuilder->setTemplate($tmp);
        $propery = $this->getProtectedAttributeOf($this->formBuilder, 'localTemplates');
        $this->assertEquals($propery['class_concat'], $tmp['_options']['class_concat']);
    }

    public function testSetTemplatesByArrayIssetOptionsIsLabelArray()
    { //todo
        $tmp = [
            '_options' => [
                'label' => [
                    'class' => 'has-error'
                ]
            ]
        ];

        $this->formBuilder->setTemplate($tmp);
        $propery = $this->getProtectedAttributeOf($this->formBuilder, 'localTemplates');

        foreach ($propery['label'] as $index => $option) {
            $this->assertEquals($tmp['_options']['label'][$index], $option);
        }
    }

    public function testSetTemplatesByArrayIssetOptionsIsLabelText()
    { //todo
        $tmp = [
            '_options' => [
                'label' => [
                    'text' => 'labelName'
                ]
            ]
        ];

        $this->formBuilder->setTemplate($tmp);
        $propery = $this->getProtectedAttributeOf($this->formBuilder, 'localTemplates');
        $this->assertEquals(empty($propery['label']['text']), true);
    }*/

    /**
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testGetGeneralUnlockFieldsWhenExistInOptions()
    {
        $options = [
            '_unlockFields' => [
                'data-id',
                'data-url',
                'action',
            ]

        ];
        $array = $options['_unlockFields'];
        $unlockedFields = $this->getProtectedMethod($this->formBuilder, 'getGeneralUnlockFieldsBy', [&$options]);

        $array[] = '_method';
        $array[] = '_token';
        $array[] = config('lara_form.token_name');
        asort($array);
        asort($unlockedFields);
        $array = array_values($array);
        $unlockedFields = array_values($unlockedFields);
        $this->assertEquals($array, $unlockedFields);
    }

    /**
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testGetGeneralUnlockFieldsWhenEmptyOptions()
    {
        $options = [];
        $unlockedFields = $this->getProtectedMethod($this->formBuilder, 'getGeneralUnlockFieldsBy', [&$options]);

        $array[] = '_method';
        $array[] = '_token';
        $array[] = config('lara_form.token_name');
        asort($array);
        asort($unlockedFields);
        $array = array_values($array);
        $unlockedFields = array_values($unlockedFields);
        $this->assertEquals($array, $unlockedFields);
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws \PHPUnit_Framework_Exception
     * @throws \PHPUnit_Framework_ExpectationFailedException
     * @throws \PHPUnit_Framework_MockObject_RuntimeException
     */
    public function testAddTemplatesAndParams()
    {
        $data = [
            'input' => '<input type="{%type%}" name="{%name%}" {%attrs%}/>',
            'checkbox' => '<input type="checkbox" name="{%name%}" {%attrs%}/>'
        ];
        $container = [

        ];
        $options = [
            'label' => [
                'text' => 'labelName',
                'class' => 'text-center'
            ],
            'div' => [
                'class' => 'has-error'
            ],
            'class_concat' => false,
            'escept' => true
        ];
        $formBuilder = $this->newFormBuilder();
        $this->getProtectedMethod($formBuilder, 'addTemplatesAndParams', [$data, &$container, $options]);
        unset($options['label']['text']);
        $this->assertEquals($data, $container['pattern']);
        $this->assertEquals($options['div'], $container['div']);
        $this->assertEquals($options['label'], $container['label']);
        $this->assertEquals($container['class_concat'], $options['class_concat']);
        $this->assertEquals($container['escept'], $options['escept']);
    }

    /**
     * @throws \PHPUnit_Framework_AssertionFailedError
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testResetProperties()
    {
        $this->formBuilder->setTemplate([
            'input' => '<input type="{%type%}" name="{%name%}" {%attrs%}/>',
            'checkbox' => '<input type="checkbox" name="{%name%}" {%attrs%}/>',
            '_options' => [
                'label' => [
                    'text' => 'labelName'
                ],
                'div' => [
                    'class' => 'has-error'
                ]
            ]
        ]);
        $this->formBuilder->create(null, []);
        $this->formBuilder->input('name', ['class_concat' => false]);
        $this->getProtectedMethod($this->formBuilder, 'resetProperties');
        $isForm = $this->getProtectedAttributeOf($this->formBuilder, 'isForm');
        $local = $this->getProtectedAttributeOf($this->formBuilder, 'localTemplates');
        $default = $this->getProtectedAttributeOf($this->formBuilder, 'templateDefaultParams');
        $maked = $this->getProtectedAttributeOf($this->formBuilder, 'maked');
        $this->assertEmpty($maked);
        $this->assertEquals($local, $default);
        $this->assertEquals(false, $isForm);

    }

    /**
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testHasTemplate()
    {
        $attr = [
            'name',
            [
                'template' => [
                    'input' => '<input type="{%type%}" name="{%name%}" {%attrs%}/>',
                    'checkbox' => '<input type="checkbox" name="{%name%}" {%attrs%}/>'
                ],
                'div' => [
                    'class' => 'has-error'
                ],
                'label' => [
                    'class' => 'pull-right'
                ],
                'class_concat' => false,
                'escept' => true,
            ]
        ];
        $params = $attr;
        $this->getProtectedMethod($this->formBuilder, 'hasTemplate', [&$attr]);
        $inline = $this->getProtectedAttributeOf($this->formBuilder, 'inlineTemplates');
        $this->assertEquals($inline['pattern'], $params[1]['template']);
        $this->assertEquals($inline['div'], $params[1]['div']);
        $this->assertEquals($inline['label'], $params[1]['label']);
        $this->assertEquals($inline['class_concat'], $params[1]['class_concat']);
        $this->assertEquals($inline['escept'], $params[1]['escept']);
    }

    /**
     * @throws \PHPUnit_Framework_Exception
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testMake()
    {
        $method = 'input';
        $attr = [
            'username',
            [
                'data-id' => 1,
                'class' => 'has-error'
            ]
        ];
        $modelName = ucfirst($method);
        $classNamspace = config('lara_form_core.method_full_name') . $modelName . config('lara_form_core.method_sufix');
        $make = $this->getProtectedMethod($this->formBuilder, 'make', [$method, $attr]);
        $makedField = $this->getProtectedAttributeOf($this->formBuilder, 'maked')[$modelName];
        $this->assertInstanceOf($classNamspace, $makedField);
        $this->assertInstanceOf(OptionStore::class, $make);
    }

    /**
     * @throws \PHPUnit_Framework_Exception
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    public function testMakeMultiMakedWidget()
    {
        $this->testMake();
        $this->testMake();
        $this->testMake();
        $this->testMake(); //todo
        $maked = $this->getProtectedAttributeOf($this->formBuilder, 'maked');
        $this->assertEquals(1, count($maked));
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws \PHPUnit_Framework_Exception
     * @throws \PHPUnit_Framework_ExpectationFailedException
     * @throws \PHPUnit_Framework_MockObject_RuntimeException
     * @throws \RuntimeException
     */
    public function testCall()
    {
        $formBuilder = $this->newFormBuilder(['getFieldType','fixField','hasTemplate','make']);
        $this->methodWillReturnTrue('getFieldType', $formBuilder);
        $this->methodWillReturnTrue('fixField', $formBuilder);
        $this->methodWillReturnTrue('hasTemplate', $formBuilder);
        $val = str_random(5);
        $this->methodWillReturn($val, 'make', $formBuilder);
        $returned = $formBuilder->__call('input', []);
        $this->assertEquals($val, $returned);
    }

    /**
     * @param $name
     * @param $method
     * @param array $attr
     * @param bool $empty
     * @throws \PHPUnit_Framework_AssertionFailedError
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    private function assertFixField($name, $method, $attr = [], $empty = false)
    {
        if (empty($attr['value'])) {
            $attr['value'] = '';
        }
        $params = [[$name], $attr, $method];
        $this->formBuilder->expects($this->any())->method('getIsForm')->willReturn(true);
        $this->getProtectedMethod($this->formBuilder, 'fixField', $params);
        $formProtection = $this->getProtectedAttributeOf($this->formBuilder, 'formProtection');

        if ($empty) {
            $this->assertEmpty($formProtection->fields);
        } else {
            $this->assertEquals($formProtection->fields, [$name => $attr['value']]);
        }
    }

    /**
     * @param $type
     * @param $value
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    private function assertGetFieldType($type, $value)
    {
        $params = [['type' => $type], 'input'];
        $method = $this->getProtectedMethod($this->formBuilder, 'getFieldType', $params);
        $this->assertEquals($value, $method);
    }

    /**
     * @param $prop
     * @param $param
     * @throws \PHPUnit_Framework_ExpectationFailedException
     */
    private function setTemplateByArgs($prop, $param)
    {
        $templateName = 'input';
        $templatePattern = '<input type="{%type%}" name="{%name%}" {%attrs%}/>';
        $this->formBuilder->setTemplate($templateName, $templatePattern, $param);
        $propery = $this->getProtectedAttributeOf($this->formBuilder, $prop);
        $this->assertEquals($templatePattern, $propery['pattern'][$templateName]);
    }
}
