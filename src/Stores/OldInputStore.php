<?php
declare(strict_types=1);

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
    protected $session;

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
    public function hasOldInput(): bool
    {
        return ($this->session->get('_old_input')) ? true : false ;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getOldInput($key): ?string
    {
        return $this->session->getOldInput($this->transformKey($key));
    }

}