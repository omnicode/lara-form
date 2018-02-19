<?php

namespace Tests\Core;

use LaraForm\Core\BaseStore;
use Tests\LaraFormTestCase;

class BaseStoreTest extends LaraFormTestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testTransformKey()
    {
        $str = 'user[name][]';
        $baseStore = $this->newInstance(BaseStore::class);
        $returned = $this->invokeMethod($baseStore,'transformKey',[$str]);
        $this->assertEquals('user.name',$returned);
    }
}
