<?php

namespace LaraForm\Core;

/**
 * Class BaseStore
 * @package LaraForm\Core
 */
abstract  class BaseStore
{
    /**
     * Transforms a multidimensional array into a string
     * @link https://github.com/adamwathan/form/blob/master/src/AdamWathan/Form/Binding/BoundData.php#L70
     * @param $key
     * @return mixed
     */
    protected function transformKey($key)
    {
        return str_ireplace(['[]', '[', ']'], ['', '.', ''], $key);
    }
}