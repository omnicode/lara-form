<?php

namespace Test;

use LaraForm\FormBuilder;
use LaraForm\FormProtection;
use LaraForm\Stores\ErrorStore;
use LaraForm\Stores\OldInputStore;

class FormBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FormProtection
     */
    protected $formProtection;

    /**
     * @var ErrorStore
     */
    protected $errorStore;

    /**
     * @var OldInputStore
     */
    protected $oldInputStore;


    public function setUp()
    {
        $this->formProtection = app(FormProtection::class);
        $this->errorStore = app(ErrorStore::class);
        $this->oldInputStore = app(OldInputStore::class);
    }
}
