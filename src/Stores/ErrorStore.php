<?php

namespace LaraForm\Stores;

class ErrorStore
{
    private $session;

    public function __construct()
    {
        $this->session = session();
    }

    public function hasError($key)
    {
        if (! $this->hasErrors()) {
            return false;
        }

        $key = $this->transformKey($key);

        return $this->getErrors()->has($key);
    }

    public function getError($key)
    {
        if (! $this->hasError($key)) {
            return null;
        }

        $key = $this->transformKey($key);

        return $this->getErrors()->first($key);
    }

    public function hasErrors()
    {
        return $this->session->has('errors');
    }

    public function getErrors()
    {
        return $this->hasErrors() ? $this->session->get('errors') : null;
    }

    protected function transformKey($key)
    {
        return str_ireplace(['.', '[]', '[', ']'], ['_', '', '.', ''], $key);
    }
}