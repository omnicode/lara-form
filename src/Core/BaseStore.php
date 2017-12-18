<?php

namespace LaraForm\Core;

/**
 * Class BaseStore
 * @package LaraForm\Core
 */
abstract  class BaseStore
{
    /**
     *Transforms a multidimensional array into a string
     *
     * @param $key
     * @return mixed
     */
    protected function transformKey($key)
    {
        return str_replace(['[]', '[', ']'], ['', '.', ''], $key);
    }
}