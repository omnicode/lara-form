<?php

namespace LaraForm\Stores;

class OldInputStore
{
    private $session;

    public function __construct()
    {
        $this->session = session();
    }

    public function hasOldInput()
    {
        return ($this->session->get('_old_input')) ? true : false ;
    }

    public function getOldInput($key)
    {
        return $this->session->getOldInput($this->transformKey($key));
    }

    protected function transformKey($key)
    {
        return str_ireplace(['.', '[]', '[', ']'], ['_', '', '.', ''], $key);
    }
}