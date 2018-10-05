<?php

namespace LaraForm\Traits;

/**
 * Trait StrParser
 *
 * @package LaraForm\Traits
 */
trait StrParser
{
    /**
     * @param $name
     *
     * @return string
     */
    protected function parseName($name)
    {
        return ucwords($this->parse($name));
    }

    /**
     * @param $key
     *
     * @return string
     */
    protected function parseKey($key)
    {
        $str = snake_case($this->parse($key));
        return str_slug($str, '_');
    }

    /**
     * @return string
     */
    protected function parse($str)
    {
        return trim(preg_replace('/[^a-zA-Z]/', ' ', $str));
    }
}