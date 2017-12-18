<?php

namespace LaraForm\Stores;

use LaraForm\Core\BaseStore;

/**
 * if the data check has detected an error,
 * then after the redirection to back page,
 * displaying the data entered by the user
 *
 * Class OldInputStore
 * @package LaraForm\Stores
 * @link https://github.com/adamwathan/form/blob/master/src/AdamWathan/Form/OldInput/IlluminateOldInputProvider.php
 */
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