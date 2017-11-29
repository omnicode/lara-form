<?php

namespace LaraForm\Elements\Components;

class RadioWidget extends BaseInputWidget
{
    /**
     * @param $option
     * @return string
     */
    public function render($option)
    {
        $template = $this->_defaultConfig['templates']['radio'];
        $name = array_shift($option);
        $attr = !empty($option[0]) ? $option[0] : [];
        $attr['value'] = isset($attr['value']) ? $attr['value'] : 1;
        $attr['class'] = isset($attr['class']) ? $attr['class'] : false;
        if (empty($attr['id'])) {
            $attr['id'] = $name . '-' . $attr['value'];
        }

        if (isset($attr['type'])) {
            unset($attr['type']);
        }

        return $this->toHtml($name, $attr, $template);
    }
}