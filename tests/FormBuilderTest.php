<?php

namespace Tests;

use LaraForm\Facades\LaraForm;
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


    /**
     *
     */
    public function setUp()
    {
        $this->formProtection = app(FormProtection::class);
        $this->errorStore = app(ErrorStore::class);
        $this->oldInputStore = app(OldInputStore::class);
    }

    /**
     *
     */
    public function testCreateWhenIsFormFalse()
    {
        $token = md5(str_random(80));
        $options['form_token'] = $token;
       // $formData = $this->make('form', ['start', $options]);
       // $formHtml = $formData['html'];

       /* if ($formData['method'] === 'get') {
            return $formHtml;
       } */

        /* $this->formProtection->setToken($token);
         $this->formProtection->setTime();
         $this->formProtection->setUrl($formData['action']);
         $this->formProtection->removeByTime();
         $this->formProtection->removeByCount();
         $unlockFields = $this->getGeneralUnlockFieldsBy($options);
         $this->formProtection->setUnlockFields($unlockFields);
         */

        $laraForm = new LaraForm($this->formProtection, $this->errorStore, $this->oldInputStorel);
        $createdForm = $laraForm->create(null, []);
        dd($createdForm);
        $this->assertEquals($createdForm, $formHtml);
    }
}
