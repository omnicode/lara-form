<?php

namespace Tests;

use App\Http\Controllers\Controller;
use LaraForm\Elements\Widgets\InputWidget;
use LaraForm\FormBuilder;
use LaraForm\FormProtection;
use LaraForm\Stores\BindStore;
use LaraForm\Stores\ErrorStore;
use LaraForm\Stores\OldInputStore;
use LaraForm\Stores\OptionStore;
use phpmock\MockBuilder;
use Tests\Core\BaseFormBuilderTest;

class FormBuilderTest extends BaseFormBuilderTest
{

    /**
     * @var
     */
    protected $formBuilder;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();
        if (empty($this->formBuilder)) {
            $mthods = ['generateToken', 'getAction', 'getMethod', 'isForm', 'getIsForm', 'addTemplatesAndParams', 'make', 'getGeneralUnlockFieldsBy'];
            $this->formBuilder = $this->newFormBuilder($mthods);
        };

    }

    /**
     * @throws \ReflectionException
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
     * @throws \ReflectionException
     */
    public function testCreateWhenIsFormFalseAndGetMethod()
    {
        $this->methodWillReturn('11111', 'generateToken', $this->formBuilder);
        $this->methodWillReturn('/foo/bar', 'getAction', $this->formBuilder);
        $this->methodWillReturn('get', 'getMethod', $this->formBuilder);
        $this->methodWillReturn('maked', 'make', $this->formBuilder);
        $form = $this->formBuilder->create(null);
        $isForm = $this->getProtectedAttributeOf($this->formBuilder, 'isForm');
        $model = $this->getProtectedAttributeOf($this->formBuilder, 'model');
        $this->assertTrue($isForm);
        $this->assertNull($model);
        $this->assertEquals('maked', $form);
    }

    /**
     * @throws \ReflectionException
     */
    public function testCreateWhenIsFormFalse()
    {
        $formProtectionMockMethods = [
            'setToken',
            'setTime',
            'setUrl',
            'removeByTime',
            'removeByCount',
            'setUnlockFields',
        ];
        $this->methodWillReturn('11111', 'generateToken', $this->formBuilder);
        $this->methodWillReturn('/foo/bar', 'getAction', $this->formBuilder);
        $this->methodWillReturn('post', 'getMethod', $this->formBuilder);
        $this->methodWillReturn('maked', 'make', $this->formBuilder);
        $this->methodWillReturn([], 'getGeneralUnlockFieldsBy', $this->formBuilder);
        $formProtection = $this->newInstance(FormProtection::class, [], $formProtectionMockMethods);
        $this->setProtectedAttributeOf($this->formBuilder, 'formProtection', $formProtection);
        $form = $this->formBuilder->create(null);
        $isForm = $this->getProtectedAttributeOf($this->formBuilder, 'isForm');
        $model = $this->getProtectedAttributeOf($this->formBuilder, 'model');
        $this->assertTrue($isForm);
        $this->assertNull($model);
        $this->assertEquals('maked', $form);
    }

    /**
     * @throws \ReflectionException
     */
    public function testEndWhenIsFormTrue()
    {
        $formBuilder = $this->newFormBuilder(['make', 'resetProperties']);
        $val = str_random(5);
        $this->methodWillReturn($val, 'make', $formBuilder);
        $this->methodWillReturnTrue('resetProperties', $formBuilder);
        $this->invokeMethod($formBuilder, 'setIsForm', [true]);
        $end = $formBuilder->end();
        $this->assertEquals($val, $end);
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
     * @throws \ReflectionException
     */
    public function testGetFieldTypeForButtonType()
    {
        $this->assertGetFieldType('button', 'submit');
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetFieldTypeForResetType()
    {
        $this->assertGetFieldType('reset', 'submit');
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetFieldTypeForSubmitType()
    {
        $this->assertGetFieldType('submit', 'submit');
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetFieldTypeForCheckboxType()
    {
        $this->assertGetFieldType('checkbox', 'checkbox');
    }


    /**
     * @throws \ReflectionException
     */
    public function testGetFieldTypeForRadioType()
    {
        $this->assertGetFieldType('radio', 'radio');
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetFieldTypeForFileType()
    {
        $this->assertGetFieldType('file', 'file');
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetFieldTypeForTextareaType()
    {
        $this->assertGetFieldType('textarea', 'textarea');
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetFieldTypeForHiddenType()
    {
        $this->assertGetFieldType('hidden', 'hidden');
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetFieldTypeForLabelType()
    {
        $this->assertGetFieldType('label', 'label');
    }

    /**
     * @throws \ReflectionException
     * @throws \PHPUnit_Framework_Constraint
     */
    public function testFixFieldWithHiddenByDefaultValue()
    {
        $this->assertFixField('name', 'hidden');
    }

    /**
     * @throws \ReflectionException
     * @throws \PHPUnit_Framework_Constraint
     */
    public function testFixFieldWithHiddenByCustomValue()
    {
        $this->assertFixField('name', 'hidden', [5]);
    }

    /**
     * @throws \ReflectionException
     * @throws \PHPUnit_Framework_Constraint
     */
    public function testFixFieldWithReadonly()
    {
        $this->assertFixField('name', 'input', ['readonly' => true, 'value' => 55]);
    }

    /**
     * @throws \ReflectionException
     * @throws \PHPUnit_Framework_Constraint
     */
    public function testFixFieldWithSubmit()
    {
        $this->assertFixField('name', 'submit', [], true);
    }

    /**
     * @throws \ReflectionException
     * @throws \PHPUnit_Framework_Constraint
     */
    public function testFixFieldWithReset()
    {
        $this->assertFixField('name', 'reset', [], true);
    }

    /**
     * @throws \PHPUnit_Framework_Constraint
     * @throws \ReflectionException
     */
    public function testFixFieldWithButton()
    {
        $this->assertFixField('name', 'button', [], true);
    }

    /**
     * @throws \ReflectionException
     * @throws \PHPUnit_Framework_Constraint
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
     * @throws \ReflectionException
     */
    public function testSetTemplatesByArrayWhenNotOptions()
    {
        $tmp = [
            'input' => 'input',
            'checkbox' => 'checkbox',
        ];
        $formBuilder = $this->newFormBuilder('addTemplatesAndParams');
        $this->setProtectedAttributeOf($formBuilder, 'localTemplates', 'local');
        $this->methodWillThrowExceptionWithArgument('addTemplatesAndParams', $formBuilder, 1);
        $this->expectExceptionMessage('method attribute is :[{"input":"input","checkbox":"checkbox"},"local",[]]');
        $formBuilder->setTemplate($tmp);
    }

    /**
     * @throws \ReflectionException
     */
    public function testSetTemplatesByArrayWhenExistOptionsGlobal()
    {
        $tmp = [
            'input' => 'input',
            '_options' => ['global' => true]
        ];
        $this->methodWillReturnTrue('addTemplatesAndParams', $this->formBuilder);
        $this->setProtectedAttributeOf($this->formBuilder, 'globalTemplates', 'global');
        $this->methodWillThrowExceptionWithArgument('addTemplatesAndParams', $this->formBuilder, 1);
        $this->expectExceptionMessage('method attribute is :[{"input":"input"},"global",{"global":true}]');
        $this->formBuilder->setTemplate($tmp);
    }

    /**
     * @throws \ReflectionException
     */
    public function testSetIsForm()
    {
        $this->invokeMethod($this->formBuilder, 'setIsForm', [true]);
        $isForm = $this->getProtectedAttributeOf($this->formBuilder, 'isForm');
        $this->assertTrue($isForm);
    }

    /**
     * @throws \Exception
     * @expectedException Exception
     */
    public function testSetIsFormSecondTime()
    {
        $this->invokeMethod($this->formBuilder, 'setIsForm', [true]);
        $this->invokeMethod($this->formBuilder, 'setIsForm', [true]);
    }

    /**
     * @throws \ReflectionException
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
        $formBuilder = $this->newFormBuilder();
        $formProtection = $this->newInstance(FormProtection::class, [], 'processUnlockFields');
        $this->methodWillReturnArgument(0, 'processUnlockFields', $formProtection);
        $this->setProtectedAttributeOf($formBuilder, 'formProtection', $formProtection);
        $unlockedFields = $this->invokeMethod($formBuilder, 'getGeneralUnlockFieldsBy', [&$options]);
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
     * @throws \ReflectionException
     */
    public function testGetGeneralUnlockFieldsWhenEmptyOptions()
    {
        $options = [];
        $formBuilder = $this->newFormBuilder();
        $unlockedFields = $this->invokeMethod($formBuilder, 'getGeneralUnlockFieldsBy', [&$options]);
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
     * @throws \ReflectionException
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
        $this->invokeMethod($formBuilder, 'addTemplatesAndParams', [$data, &$container, $options]);
        unset($options['label']['text']);
        $this->assertEquals($data, $container['pattern']);
        $this->assertEquals($options['div'], $container['div']);
        $this->assertEquals($options['label'], $container['label']);
        $this->assertEquals($container['class_concat'], $options['class_concat']);
        $this->assertEquals($container['escept'], $options['escept']);
    }

    /**
     * @throws \ReflectionException
     */
    public function testResetProperties()
    {
        $formBuilder = $this->newFormBuilder('setIsForm');
        $this->setProtectedAttributeOf($formBuilder, 'maked', ['value']);
        $this->setProtectedAttributeOf($formBuilder, 'templateDefaultParams', 'localData');
        $this->invokeMethod($formBuilder, 'resetProperties');
        $local = $this->getProtectedAttributeOf($formBuilder, 'localTemplates');
        $maked = $this->getProtectedAttributeOf($formBuilder, 'maked');
        $this->assertEquals([], $maked);
        $this->assertEquals('localData', $local);

    }

    /**
     * @throws \ReflectionException
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
        $this->invokeMethod($this->formBuilder, 'hasTemplate', [&$attr]);
        $inline = $this->getProtectedAttributeOf($this->formBuilder, 'inlineTemplates');
        $this->assertEquals($inline['pattern'], $params[1]['template']);
        $this->assertEquals($inline['div'], $params[1]['div']);
        $this->assertEquals($inline['label'], $params[1]['label']);
        $this->assertEquals($inline['class_concat'], $params[1]['class_concat']);
        $this->assertEquals($inline['escept'], $params[1]['escept']);
    }

    /**
     * @throws \ReflectionException
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
        $formBuilder = $this->newFormBuilder();
        $modelName = ucfirst($method);
        $classNamspace = config('lara_form_core.method_full_name') . $modelName . config('lara_form_core.method_sufix');
        $this->setProtectedAttributeOf($formBuilder, 'model',new \stdClass());
        $make = $this->invokeMethod($formBuilder, 'make', [$method, $attr]);
        $makedField = $this->getProtectedAttributeOf($formBuilder, 'maked')[$modelName];
        $this->assertInstanceOf($classNamspace, $makedField);
        $this->assertInstanceOf(OptionStore::class, $make);
    }

    /**
     * @throws \ReflectionException
     */
    public function testMakeMultiMakedWidget()
    {
        $attr = ['input', ['attr']];
        $formBuilder = $this->newFormBuilder();
        $this->invokeMethod($formBuilder, 'make', $attr);
        $this->invokeMethod($formBuilder, 'make', $attr);
        $this->invokeMethod($formBuilder, 'make', $attr);
        $this->invokeMethod($formBuilder, 'make', $attr);
        $maked = $this->getProtectedAttributeOf($formBuilder, 'maked');
        $this->assertEquals(1, count($maked));
    }

    /**
     * @throws \ReflectionException
     */
    public function testMakeWhenExistModel()
    {
        $attr = ['input', ['attr']];
        $formBuilder = $this->newFormBuilder(['setModel', 'binding', 'setAttributes', 'setBuilder']);
        $this->setProtectedAttributeOf($formBuilder, 'optionStore', $formBuilder);
        $this->setProtectedAttributeOf($formBuilder, 'bindStore', $formBuilder);
        $this->setProtectedAttributeOf($formBuilder, 'maked', ['Input' => $formBuilder]);
        $this->setProtectedAttributeOf($formBuilder, 'model', true);
        $this->methodWillReturnTrue('setModel',$formBuilder);
        $this->methodWillReturnTrue('binding',$formBuilder);
        $this->methodWillReturnTrue('setAttributes',$formBuilder);
        $this->methodWillReturnTrue('setBuilder',$formBuilder);
        $this->invokeMethod($formBuilder, 'make', $attr);
    }

    /**
     *
     */
    public function testCall()
    {
        $formBuilder = $this->newFormBuilder(['getFieldType', 'fixField', 'hasTemplate', 'make']);
        $this->methodWillReturnTrue('getFieldType', $formBuilder);
        $this->methodWillReturnTrue('fixField', $formBuilder);
        $this->methodWillReturnTrue('hasTemplate', $formBuilder);
        $val = str_random(5);
        $this->methodWillReturn($val, 'make', $formBuilder);
        $returned = $formBuilder->__call('input', []);
        $this->assertEquals($val, $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testOutput()
    {
        $inputWidget = $this->newInstance(InputWidget::class, [
            app(ErrorStore::class),
            app(OldInputStore::class)
        ], ['setArguments', 'setParams', 'render']);
        $optionStore = $this->newInstance(OptionStore::class, [], ['getOptions', 'resetOptions']);
        $formBuilder = $this->newFormBuilder(['hasTemplate', 'complateTemplatesAndParams']);
        $this->methodWillReturnTrue('render', $inputWidget);
        $this->setProtectedAttributeOf($formBuilder, 'widget', $inputWidget);
        $this->setProtectedAttributeOf($formBuilder, 'optionStore', $optionStore);
        $returned = $formBuilder->output();
        $this->assertTrue($returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testComplateTemplatesAndParams()
    {
        $data = [
            'inline' => 'inline',
            'local' => 'local',
            'global' => 'global'
        ];
        $this->setProtectedAttributeOf($this->formBuilder, 'inlineTemplates', 'inline');
        $this->setProtectedAttributeOf($this->formBuilder, 'localTemplates', 'local');
        $this->setProtectedAttributeOf($this->formBuilder, 'globalTemplates', 'global');
        $this->setProtectedAttributeOf($this->formBuilder, 'templateDefaultParams', 'pattern');
        $returned = $this->invokeMethod($this->formBuilder, 'complateTemplatesAndParams');
        $inline = $this->getProtectedAttributeOf($this->formBuilder, 'inlineTemplates');
        $this->assertEquals('pattern', $inline);
        $this->assertEquals($data, $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetIsForm()
    {
        $formBuilder = $this->newFormBuilder();
        $this->setProtectedAttributeOf($formBuilder, 'isForm', 'form');
        $returned = $this->invokeMethod($formBuilder, 'getIsForm');
        $this->assertEquals('form', $returned);
    }

    /**
     * @throws \ReflectionException
     * @throws \phpmock\MockEnabledException
     */
    public function testFormControlGetActionWhenExistRoute()
    {
        $options = ['route' => 'create'];
        $builder = new MockBuilder();
        $builder->setNamespace("LaraForm\Traits");
        $builder->setName('route');
        $builder->setFunction(function () {
            return 'foo/bar';
        });
        $mock = $builder->build();
        $mock->enable();
        $formBuilder = $this->newFormBuilder(['getRoutes', 'setRequestMethod']);
        $returned = $this->invokeMethod($formBuilder, 'getAction', [&$options]);
        $this->assertEquals([], $options);
        $this->assertEquals('foo/bar', $returned);
        $mock->disable();
    }

    /**
     * @throws \ReflectionException
     */
    public function testFormControlGetActionWhenExistUrl()
    {
        $options = ['url' => 'foo/bar'];
        $formBuilder = $this->newFormBuilder();
        $returned = $this->invokeMethod($formBuilder, 'getAction', [&$options]);
        $this->assertEquals([], $options);
        $this->assertEquals('foo/bar', $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testFormControlGetActionWhenExistActionWithUrl()
    {
        $url = 'http://foo/bar';
        $options = ['action' => $url];
        $formBuilder = $this->newFormBuilder();
        $returned = $this->invokeMethod($formBuilder, 'getAction', [&$options]);
        $this->assertEquals($url, $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testFormControlGetActionWhenExistActionisMethod()
    {
        $options = ['action' => 'create'];
        $formBuilder = $this->newFormBuilder('getActionWithMethod');
        $this->methodWillReturnTrue('getActionWithMethod', $formBuilder);
        $returned = $this->invokeMethod($formBuilder, 'getAction', [&$options]);
        $this->assertTrue($returned);
    }

    /**
     * @throws \ReflectionException
     * @throws \phpmock\MockEnabledException
     */
    public function testGetActionWithMethodWhenNotParams()
    {
        $options = [];
        $formBuilder = $this->newFormBuilder(['url']);
        $this->methodWillReturnTrue('url', $formBuilder);
        $builder = new MockBuilder();
        $builder->setNamespace("LaraForm\Traits");
        $builder->setName('request');
        $builder->setFunction(function () use ($formBuilder) {
            return $formBuilder;
        });
        $mock = $builder->build();
        $mock->enable();
        $returned = $this->invokeMethod($formBuilder, 'getAction', [&$options]);
        $this->assertTrue($returned);
        $mock->disable();
    }

    /**
     * @throws \Exception
     * @expectedException Exception
     */
    public function testGetActionWithMethodWhenNotController()
    {
        $builder = new MockBuilder();
        $builder->setNamespace("LaraForm\Traits");
        $builder->setName('get_class');
        $builder->setFunction(function () {
            return 'class';
        });
        $mock = $builder->build();
        $mock->enable();
        $formBuilder = $this->newFormBuilder(['getRouteName', 'getController', 'getCurrentRoute']);
        $this->methodWillReturn($formBuilder, 'getCurrentRoute', $formBuilder);
        $this->methodWillReturn(Controller::class, 'getController', $formBuilder);
        $this->methodWillReturnFalse('getRouteName', $formBuilder);
        $this->invokeMethod($formBuilder, 'getActionWithMethod', [['method', 'params']]);
        $mock->disable();
    }

    /**
     * @throws \ReflectionException
     * @throws \phpmock\MockEnabledException
     */
    public function testGetActionWithMethodWhenExistController()
    {
        $builder = new MockBuilder();
        $builder->setNamespace("LaraForm\Traits");
        $builder->setName('route');
        $builder->setFunction(function () {
            return 'foo/bar';
        });
        $mock = $builder->build();
        $mock->enable();
        $formBuilder = $this->newFormBuilder('getRouteName');
        $this->methodWillReturn('route', 'getRouteName', $formBuilder);
        $returned = $this->invokeMethod($formBuilder, 'getActionWithMethod', [['Controller@method', 'params']]);
        $this->assertEquals('foo/bar', $returned);
        $mock->disable();
    }

    /**
     * @throws \ReflectionException
     * @throws \phpmock\MockEnabledException
     */
    public function testGetRouteNameWhenExistRoute()
    {
        $data = [
            'route' => [
                'action' => 'foo/bar',
                'method' => 'post',
            ]
        ];
        $builder = new MockBuilder();
        $builder->setNamespace("LaraForm\Traits");
        $builder->setName('ends_with');
        $builder->setFunction(function () {
            return true;
        });
        $mock = $builder->build();
        $mock->enable();
        $formBuilder = $this->newFormBuilder('getRoutes');
        $this->methodWillReturn($data, 'getRoutes', $formBuilder);
        $returned = $this->invokeMethod($formBuilder, 'getRouteName', ['bar']);
        $requestMethod = $this->getProtectedAttributeOf($formBuilder, '_requestMethod');
        $this->assertEquals('post', $requestMethod);
        $this->assertEquals('route', $returned);
        $mock->disable();
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetRouteNameWhenNotRoute()
    {
        $formBuilder = $this->newFormBuilder('getRoutes');
        $this->methodWillReturn([], 'getRoutes', $formBuilder);
        $returned = $this->invokeMethod($formBuilder, 'getRouteName', ['bar']);
        $this->assertFalse($returned);
    }
    /**
     * @throws \ReflectionException
     */
    public function testSetRequestMethod()
    {
        $data = [
            'routeName' => [
                'action' => 'foo/bar',
                'method' => 'post',
            ]
        ];
        $formBuilder = $this->newFormBuilder('getRoutes');
        $this->methodWillReturn($data, 'getRoutes', $formBuilder);
        $this->invokeMethod($formBuilder, 'setRequestMethod', ['routeName']);
        $requestMethod = $this->getProtectedAttributeOf($formBuilder, '_requestMethod');
        $this->assertEquals('post', $requestMethod);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetMethodWhenExistMethodInOption()
    {
        $data = [
            ['method' => 'get'],
            ['method' => 'post'],
            ['method' => 'put'],
            ['method' => 'patch'],
            ['method' => 'delete'],
            ['method' => 'options']
        ];
        $formBuilder = $this->newFormBuilder();
        foreach ($data as $datum) {
            $method = $datum['method'];
            $returned = $this->invokeMethod($formBuilder, 'getMethod', [&$datum]);
            $this->assertEquals($method, $returned);
            $this->assertEquals([], $datum);
        }
    }

    /**
     * @throws \Exception
     * @expectedException Exception
     */
    public function testGetMethodWhenExistInvalidMethodInOption()
    {
        $data = ['method' => 'invalidMethod'];
        $formBuilder = $this->newFormBuilder();
        $this->invokeMethod($formBuilder, 'getMethod', [&$data]);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetMethodWhenMethodFromRoutes()
    {
        $data = [];
        $formBuilder = $this->newFormBuilder();
        $this->setProtectedAttributeOf($formBuilder, '_requestMethod', 'post');
        $returned = $this->invokeMethod($formBuilder, 'getMethod', [&$data]);
        $requestMethod = $this->getProtectedAttributeOf($formBuilder, '_requestMethod');
        $this->assertNull($requestMethod);
        $this->assertEquals('post', $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetMethodWhenExistModelForUpdate()
    {
        $data = [];
        $formBuilder = $this->newFormBuilder();
        $this->setProtectedAttributeOf($formBuilder, 'model', true);
        $returned = $this->invokeMethod($formBuilder, 'getMethod', [&$data]);
        $this->assertEquals('put', $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetRoutes()
    {
        $this->setProtectedAttributeOf($this->formBuilder, '_routes', []);
        $returned = $this->invokeMethod($this->formBuilder, 'getRoutes');
        $status = true;
        foreach ($returned as $key => $item) {
            if (!is_string($key) || !isset($item['action']) || !isset($item['method'])) {
                $status = false;
            }
        }
        $this->assertTrue($status);
    }

    /**
     * @param $name
     * @param $method
     * @param array $attr
     * @param bool $empty
     * @throws \PHPUnit_Framework_Constraint
     * @throws \ReflectionException
     */
    private function assertFixField($name, $method, $attr = [], $empty = false)
    {
        if (empty($attr['value'])) {
            $attr['value'] = '';
        }
        $params = [[$name], $attr, $method];
        $this->formBuilder->expects($this->any())->method('getIsForm')->willReturn(true);
        $this->invokeMethod($this->formBuilder, 'fixField', $params);
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
     * @throws \ReflectionException
     */
    private function assertGetFieldType($type, $value)
    {
        $params = [['type' => $type], 'input'];
        $method = $this->invokeMethod($this->formBuilder, 'getFieldType', $params);
        $this->assertEquals($value, $method);
    }

    /**
     * @param $prop
     * @param $param
     * @throws \ReflectionException
     */
    private function setTemplateByArgs($prop, $param)
    {
        $templateName = 'input';
        $templatePattern = '<input type="{%type%}" name="{%name%}" {%attrs%}/>';
        $this->formBuilder->setTemplate($templateName, $templatePattern, $param);
        $propery = $this->getProtectedAttributeOf($this->formBuilder, $prop);
        $this->assertEquals($templatePattern, $propery['pattern'][$templateName]);
    }

    /**
     * @param null $methods
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function newFormBuilder($methods = null)
    {
        $args = [
            app(FormProtection::class),
            app(ErrorStore::class),
            app(OldInputStore::class),
            app(OptionStore::class),
            app(BindStore::class),
        ];
        return $this->newInstance(FormBuilder::class, $args, $methods);
    }
}
