<?php

namespace LaraForm\Stores;

use LaraForm\Core\BaseStore;

class OptionStore extends BaseStore
{
    /**
     * @var
     */
    private $builder;

    /**
     * @var array
     */
    private $attributes = [];

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function __call($name, $value)
    {
        $value = array_shift($value);
        $this->attributes[1][$name] = $value;
        return $this;
    }

    /**
     * @param $options
     * @return $this
     */
    public function attr($options)
    {
        if (!is_array($options)) {
            $options = [$options];
        }

        $this->attributes += $options;
        return $this;
    }

    /**
     * @return array
     */
    public function getOprions()
    {
        return $this->attributes;
    }

    /**
     *
     */
    public function resetOptions()
    {
        $this->attributes = [];
    }

    /**
     * @param $model
     */
    public function setBuilder($model)
    {
        $this->builder = $model;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->builder->__toString();
    }


}