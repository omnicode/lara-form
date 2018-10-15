<?php
declare(strict_types=1);

namespace LaraForm\Stores;

use Illuminate\Database\Eloquent\Model;
use LaraForm\Core\BaseStore;

/**
 * Binds the model to the form for default value
 *
 * Class BindStore
 * @package LaraForm\Stores
 * @link https://github.com/adamwathan/form/blob/master/src/AdamWathan/Form/Binding/BoundData.php
 */
class BindStore extends BaseStore
{
    /**
     * @var
     */
    protected $data;

    /**
     * @param $data
     */
    public function setModel(Model $data)
    {
        $this->data = $data;
    }

    /**
     * @param $name
     * @param null $default
     * @return mixed
     */
    public function get(string $name, ?string $default = null)
    {
        $data =  $this->dotGet($this->transformKey($name), $default);
        if (is_object($data)) {
            return count($data) > 0 ? $data : '';
        }
        return $data;
    }

    /**
     * @return mixed
     */
    public function data(): Model
    {
        return $this->data;
    }

    /**
     * @param $dotKey
     * @param $default
     * @return mixed
     */
    protected function dotGet(string $dotKey,?string $default)
    {
        $keyParts = explode('.', $dotKey);
        return $this->dataGet($this->data, $keyParts, $default);
    }

    /**
     * @param $target
     * @param $keyParts
     * @param $default
     * @return mixed
     */
    protected function dataGet($target, array $keyParts, ?string $default)
    {
        if (count($keyParts) == 0) {
            return $target;
        }

        if (is_array($target)) {
            return $this->arrayGet($target, $keyParts, $default);
        }

        if (is_object($target)) {
            return $this->objectGet($target, $keyParts, $default);
        }

        return $default;
    }

    /**
     * @param $target
     * @param $keyParts
     * @param $default
     * @return mixed
     */
    protected function arrayGet(array $target, array $keyParts, ?string $default)
    {
        $key = array_shift($keyParts);

        if (!isset($target[$key])) {
            return $default;
        }

        return $this->dataGet($target[$key], $keyParts, $default);
    }

    /**
     * @param $target
     * @param $keyParts
     * @param $default
     * @return mixed
     */
    protected function objectGet(Model $target, array $keyParts, ?string $default)
    {
        $key = array_shift($keyParts);
        if (!(property_exists($target, $key) || method_exists($target, '__get'))) {
            return $default;
        }

        return $this->dataGet($target->{$key}, $keyParts, $default);
    }

}
