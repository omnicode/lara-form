<?php

namespace LaraForm\Stores;

use LaraForm\Core\BaseStore;

class OldInputStore extends BaseStore
{
    /**
     * @var mixed
     */
    private $session;

    /**
     * OldInputStore constructor.
     */
    public function __construct()
    {
        $this->session = session();
    }

    /**
     * @return bool
     */
    public function hasOldInput()
    {
        return ($this->session->get('_old_input')) ? true : false ;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getOldInput($key)
    {
        return $this->session->getOldInput($this->transformKey($key));
    }

}