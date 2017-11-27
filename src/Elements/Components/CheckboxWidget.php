<?php

namespace LaraForm\Elements\Components;

class CheckboxWidget extends BaseInputWidget
{
    public function __construct($option)
    {
        $template = $this->_defaultConfig['templates']['checkbox'];
        $name = array_shift($option);
        $attr = !empty($option[0]) ? $option[0] : [];
        $attr['value'] = 1;
        if (isset($attr['type'])) {
            unset($attr['type']);
        }
        $hidden = $this->toInputFild($name, ['type' => 'hidden', 'value' => 0, 'name' => $name, 'id' => $name . '-hid']);
        return $hidden . $this->toInputFild($name, $attr, $template);
    }
}