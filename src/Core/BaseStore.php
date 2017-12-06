<?php

namespace LaraForm\Core;

abstract  class BaseStore
{
    protected function transformKey($key)
    {
        return str_replace(['[]', '[', ']'], ['', '.', ''], $key);
    }
}