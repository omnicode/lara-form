<?php

namespace Tests\Stores;

use LaraForm\FormBuilder;
use LaraForm\FormProtection;
use LaraForm\Stores\BindStore;
use LaraForm\Stores\ErrorStore;
use LaraForm\Stores\OldInputStore;
use LaraForm\Stores\OptionStore;
use Tests\Core\BaseStoreTest;

class OptionStoreTest extends BaseStoreTest
{
    /**
     * @var
     */
    protected $optionStore;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();
        if (empty($this->optionStore)) {
            $mthods = null;
            $this->optionStore = $this->newOptionStore($mthods);
        };

    }

    /**
     * @throws \ReflectionException
     */
    public function testSetAttributes()
    {
        $this->optionStore->setAttributes('attr');
        $returned = $this->getProtectedAttributeOf($this->optionStore, 'attributes');
        $this->assertEquals('attr', $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testAttrWhenStringByKeyValue()
    {
        $returnThis = $this->optionStore->attr('data-id', 5);
        $attribute = $this->getProtectedAttributeOf($this->optionStore, 'attributes');
        $this->assertEquals([['data-id' => 5]], $attribute);
        $this->assertEquals($this->optionStore, $returnThis);
    }

    /**
     * @throws \ReflectionException
     */
    public function testAttrWhenStringByKey()
    {
        $returnThis = $this->optionStore->attr('selected');
        $attribute = $this->getProtectedAttributeOf($this->optionStore, 'attributes');
        $this->assertEquals([['selected']], $attribute);
        $this->assertEquals($this->optionStore, $returnThis);
    }

    /**
     * @throws \ReflectionException
     */
    public function testAttrWhenNotEmptyAttributes()
    {
        $data = ['name', ['attr']];
        $this->optionStore->setAttributes(['name', ['attr']]);
        $returnedThis = $this->optionStore->attr('value', 5);
        $attribute = $this->getProtectedAttributeOf($this->optionStore, 'attributes');
        $data[1]['value'] = 5;
        $this->assertEquals($data, $attribute);
        $this->assertEquals($this->optionStore, $returnedThis);
    }

    /**
     * @throws \ReflectionException
     */
    public function testId()
    {
        $this->optionStore->setAttributes(['name', ['attr']]);
        $returnedThis = $this->optionStore->id('header');
        $attribute = $this->getProtectedAttributeOf($this->optionStore, 'attributes');
        $this->assertEquals('header', $attribute[1]['id']);
        $this->assertEquals($this->optionStore, $returnedThis);
    }

    /**
     * @throws \ReflectionException
     */
    public function testClassByArray()
    {
        $this->methodClassArguemntBy(['col-md-3', 'hide']);
    }

    /**
     * @throws \ReflectionException
     */
    public function testClassByArguments()
    {
        $this->methodClassArguemntBy(['col-md-3', 'hide', 'col-sm-6']);
    }

    /**
     * @throws \ReflectionException
     */
    public function testData()
    {
        $this->optionStore->setAttributes(['name', ['attr']]);
        $returnedThis = $this->optionStore->data('id', 5);
        $attribute = $this->getProtectedAttributeOf($this->optionStore, 'attributes');
        $this->assertEquals(5, $attribute[1]['data-id']);
        $this->assertEquals($this->optionStore, $returnedThis);
    }

    /**
     *
     */
    public function testGetOptions()
    {
        $data = ['name', ['attr']];
        $this->optionStore->setAttributes($data);
        $returned = $this->optionStore->getOptions();
        $this->assertEquals($data, $returned);
    }

    /**
     * @throws \ReflectionException
     */
    public function testResetOptions()
    {
        $this->optionStore->setAttributes(['name', ['attr']]);
        $this->optionStore->resetOptions();
        $attribute = $this->getProtectedAttributeOf($this->optionStore, 'attributes');
        $this->assertEmpty($attribute);
    }

    /**
     * @throws \ReflectionException
     */
    public function testSetBuilder()
    {
        $this->optionStore->setBuilder(app(FormBuilder::class));
        $builderObject = $this->getProtectedAttributeOf($this->optionStore, 'builder');
        $this->assertInstanceOf(FormBuilder::class, $builderObject);
    }

    /**
     *
     */
    public function testToString()
    {
        $this->outputTesting('__toString');
    }

    /**
     *
     */
    public function testRender()
    {
        $this->outputTesting('render');
    }

    /**
     *
     */
    public function testCallWhenClass()
    {
        $optionStore = $this->newOptionStore(['_class']);
        $returnedThis = $optionStore->class(['header']);
        $this->assertEquals($optionStore, $returnedThis);
    }

    /**
     * @throws \Exception
     * @expectedException Exception
     */
    public function testCallWhenMethodNotExist()
    {
        $this->optionStore->customMethod(['header']);
    }

    /**
     *
     */
    private function outputTesting($func)
    {
        $formBuilder = $this->newFormBuilder(['output']);
        $this->methodWillReturn('output', 'output', $formBuilder);
        $this->optionStore->setBuilder($formBuilder);
        $returned = $this->optionStore->{$func}();
        $this->assertEquals('output', $returned);
    }

    /**
     * @param $data
     * @throws \ReflectionException
     */
    private function methodClassArguemntBy($data)
    {
        $this->optionStore->setAttributes(['name', ['attr']]);
        $returnedThis = $this->invokeMethod($this->optionStore, '_class', [$data]);
        $attribute = $this->getProtectedAttributeOf($this->optionStore, 'attributes');
        $this->assertEquals($data, $attribute[1]['class']);
        $this->assertEquals($this->optionStore, $returnedThis);
    }

    /**
     * @param $methods
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function newOptionStore($methods = null)
    {
        $optionStore = $this->getMockBuilder(OptionStore::class)
            ->setConstructorArgs([])
            ->setMethods($methods)
            ->getMock();
        return $optionStore;
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
