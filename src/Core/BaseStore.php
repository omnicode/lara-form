<?php

namespace LaraForm\Core;

abstract  class BaseStore
{
    /**
     * @param $key
     * @return mixed
     */
    protected function transformKey($key)
    {
        return str_replace(['[]', '[', ']'], ['', '.', ''], $key);
    }
}