<?php

namespace LaraForm\Stores;

class ErrorStore
{
    private $session;

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
        if (! $this->hasErrors()) {
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
        if (! $this->hasError($key)) {
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

    /**
     * @param $key
     * @return mixed
     */
    protected function transformKey($key)
    {
        return str_ireplace(['.', '[]', '[', ']'], ['_', '', '.', ''], $key);
    }
}