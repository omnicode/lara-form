<?php

namespace Tests\Stores;

use LaraForm\Stores\OldInputStore;
use Tests\Core\BaseStoreTest;

class OldInputStoreTest extends BaseStoreTest
{
    /**
     * @var
     */
    protected $oldInputStore;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();
        if (empty($this->oldInputStore)) {
            $this->oldInputStore = $this->newInstance(OldInputStore::class,[],['transformKey']);
        };

    }

    /**
     * @throws \ReflectionException
     */
    public function testConstruct()
    {
        $returned = $this->getProtectedAttributeOf($this->oldInputStore, 'session');
        $this->assertEquals(session(), $returned);
    }

    /**
     *
     */
    public function testHasOldInput()
    {
        $this->withSession(['_old_input' => 'value']);
        $returned = $this->oldInputStore->hasOldInput();
        $this->assertTrue($returned);
    }

    /**
     *
     */
    public function testHasOldInputWhenEmpty()
    {
        $returned = $this->oldInputStore->hasOldInput();
        $this->assertFalse($returned);
    }

    /**
     *
     */
    public function testGetOldInput()
    {
        $this->withSession(['_old_input' => ['field' => 'value']]);
        $this->methodWillReturn('field', 'transformKey', $this->oldInputStore);
        $returned = $this->oldInputStore->getOldInput('key');
        $this->assertEquals('value', $returned);
    }
}
