<?php

namespace LaraForm\Stores;

use LaraForm\Core\BaseStore;
use LaraForm\FormBuilder;

/**
 * creates a system of Chain-calling methods
 * Class OptionStore
 * @package LaraForm\Stores
 */
class OptionStore extends BaseStore
{
    /**
     * Keeped here object FormBuilder
     * @var FormBuilder
     */
    private $builder;

    /**
     * Keeped here all field attributes
     * @var array
     */
    private $attributes = [];

    /**
     * @param $attrs
     */
    public function setAttributes($attrs)
    {
        $this->attributes = $attrs;
    }

    /**
     * Chain calling this method passes an array of attributes
     * @param $options
     * @param $values
     * @return $this
     */
    public function attr($options, $values = null)
    {
        if (!is_array($options)) {
            if (isset($values)) {
                $options = [$options => $values];
            } else {
                $options = [$options];
            }
        }

        if (isset($this->attributes[1])) {
            $this->attributes[1] += $options;
        } else {
            $this->attributes[] = $options;
        }
        return $this;
    }


    /**
     * Chain calling this method passes an field id
     * @param $strId
     * @return $this
     */
    public function id($strId)
    {
        $this->attributes[1]['id'] = $strId;
        return $this;
    }

    /**
     * Chain calling this method passes an array or arguments of field class
     * @param $var
     * @return $this
     */
    private function _class($var)
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
     * Chain calling this method for data attibutes
     * The first argument is the name
     * The second argument is the value
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
     * Gets the field attibutes
     * @return array
     */
    public function getOprions()
    {
        return $this->attributes;
    }

    /**
     * Remove field attibutes
     */
    public function resetOptions()
    {
        $this->attributes = [];
    }

    /**
     * Accepts an object and assigns the property
     * @param $model
     */
    public function setBuilder($model)
    {
        $this->builder = $model;
    }

    /**
     * When there is a mapping then we call the method __toString() from bulider,
     * because the rendering is done there
     * @return mixed
     */
    public function __toString()
    {
        return $this->builder->__toString();
    }

    public function __call($method, $attr)
    {
        if ($method === 'class') {
            $this->_class(...$attr);
            return $this;
        }

    }
}