<?php

namespace Tests\Core;

use LaraForm\Core\BaseStore;
use Tests\BaseTestCase;

class BaseStoreTest extends BaseTestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testTransformKey()
    {
        $str = 'user[name][]';
        $baseStore = $this->newInstance(BaseStore::class);
        $returned = $this->getProtectedMethod($baseStore,'transformKey',[$str]);
        $this->assertEquals('user.name',$returned);
    }
}
