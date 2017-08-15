<?php
namespace LaraForm\Elements\Components;

use AdamWathan\BootForms\Facades\BootForm;
use Illuminate\Support\Facades\Config;
use LaraForm\Elements\Element;

class Select extends Element
{
    /**
     * @param $name
     * @param array $options
     * @return mixed
     */
    public function toHtml($name, $options = [])
    {
        $values = $this->getOptionValues($options);
        $label = $this->getLabel($name, $options);
        $select = BootForm::select(($label ? $label: null), $name)->options($values);

        if (isset($options['selected'])) {
            $select->select($options['selected']);
            unset($options['selected']);
        }

        foreach ($options as $k => $val) {
            $select->attribute($k, $val);
        }

        return $select;
    }

    /**
     * @param $options
     * @param bool $unset
     * @return array
     */
    public function getOptionValues(&$options, $unset = true) {
        $options = $this->checkOptions($options);
        $values = [];
        if (isset($options['options'])) {
            if (is_array($options['options'])) {
                $values = $options['options'];
            } elseif (is_string($options['options'])) {
                $class = $options['options'];
                if (class_exists($class)) {
                    $values = getClassConstants($class, true);
                }
            }

            if ($unset) {
                unset($options['options']);
            }
        }

        $emptyStr = __(Config::get('lara_form.label.select_empty', '--Select--'));

        if (isset($options['empty'])) {
            // to disable showing empty
            if ($options['empty'] === false) {
                $emptyStr = false;
            } else {
                $emptyStr = $options['empty'];
            }
        }

        if ($emptyStr) {
            $values = ['' => $emptyStr] + $values;
        }

        return $values;
    }

    /**
     * @param $options
     * @return mixed
     */
    private function checkOptions($options)
    {
        if (isset($options['noform'])) {
            BootForm::open();
            unset($options['noform']);
        }

        return $options;
    }
}