<?php

namespace LaraForm\Elements\Components;

class NumberWidget extends BaseInputWidget
{
    /**
     * @param $option
     * @return string
     */
    public function render($option)
    {
        $name = array_shift($option);
        $attr = !empty($option[0]) ? $option[0] : [];

        if (isset($attr['type'])) {
            unset($attr['type']);
        }

        $attr['type'] = 'number';

        return $this->toHtml($name, $attr);
    }
}