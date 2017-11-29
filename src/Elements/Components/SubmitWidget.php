<?php

namespace LaraForm\Elements\Components;

class SubmitWidget extends BaseInputWidget
{
    /**
     * @param $option
     * @return string
     */
    public function render($option)
    {
        $template = $this->_defaultConfig['templates']['submit'];
        $name = array_shift($option);
        $attr = !empty($option[0]) ? $option[0] : [];
        $attr['class'] = isset($attr['class']) ? $attr['class'] : $this->_defaultConfig['css']['submitClass'];
        if (isset($attr['type'])) {
            unset($attr['type']);
        }

        return $this->html = $this->toHtml($name, $attr, $template);
    }
}