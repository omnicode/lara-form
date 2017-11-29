<?php

namespace LaraForm\Elements\Components;

use LaraForm\Elements\Widget;

class TextareaWidget extends BaseInputWidget
{
    /**
     * @param $option
     * @return string
     */
    public function render($option)
    {
        $template = $this->_defaultConfig['templates']['textarea'];
        $name = array_shift($option);
        $attr = !empty($option[0]) ? $option[0] : [];
        $attr['value'] = isset($attr['value']) ? $attr['value'] : '';
        if (isset($attr['type'])) {
            unset($attr['type']);
        }

        return $this->html = $this->toHtml($name, $attr, $template);
    }
}