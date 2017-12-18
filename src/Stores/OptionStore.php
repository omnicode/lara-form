<?php

namespace LaraForm\Stores;

use LaraForm\Core\BaseStore;
use LaraForm\FormBuilder;

/**
 * creates a system of chain-calling methods
 *
 * Class OptionStore
 * @package LaraForm\Stores
 */
class OptionStore extends BaseStore
{
    /**
     * save here object FormBuilder
     *
     * @var FormBuilder
     */
    private $builder;

    /**
     *
     *
     * @var array
     */
    private $attributes = [];


    /**
     * @param $options
     * @return $this
     */
    public function attr($options)
    {
        if (!is_array($options)) {
            $options = [$options];
        }

        if (isset($this->attributes[1])) {
            $this->attributes[1] += $options;
        } else {
            $this->attributes += $options;
        }
        return $this;
    }


    /**
     * @param $strId
     * @return $this
     */
    public function id($strId)
    {
        $this->attributes[1]['id'] = $strId;
        return $this;
    }

    /**
     * @param $var
     * @return $this
     */
    public function class($var)
    {
        $classies = [];
        if (is_array($var)) {
            $classies = $var;
        } else {
            foreach (func_get_args() as $index => $class) {
                $classies[] = $class;
            }
        }
        $this->attributes[1]['class'] = $classies;
        return $this;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function data($name, $value)
    {
        $this->attributes[1]['data-' . $name] = $value;
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