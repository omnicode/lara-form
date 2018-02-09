<?php

namespace LaraForm\Stores;

use LaraForm\Core\BaseStore;

/**
 * Binds validation errors to a form for displaying an error messages
 *
 * Class ErrorStore
 * @package LaraForm\Stores
 * @link https://github.com/adamwathan/form/blob/master/src/AdamWathan/Form/ErrorStore/IlluminateErrorStore.php
 */
class ErrorStore extends BaseStore
{
    /**
     * @var mixed
     */
    protected $session;

    /**
     * ErrorStore constructor.
     */
    public function __construct()
    {
        $this->session = session();
    }

    /**
     * @param $key
     * @return bool
     */
    public function hasError($key)
    {
        if (!$this->hasErrors()) {
            return false;
        }

        $key = $this->transformKey($key);
        return $this->getErrors()->has($key);
    }

    /**
     * @param $key
     * @return null
     */
    public function getError($key)
    {
        if (!$this->hasError($key)) {
            return null;
        }

        $key = $this->transformKey($key);
        return $this->getErrors()->first($key);
    }

    /**
     * @return mixed
     */
    public function hasErrors()
    {
        return $this->session->has('errors');
    }

    /**
     * @return null
     */
    public function getErrors()
    {
        return $this->hasErrors() ? $this->session->get('errors') : null;
    }

}