<?php

namespace LaraForm\Elements\Components;

class FileWidget extends BaseInputWidget
{
    /**
     * @param $option
     * @return mixed
     */
    public function render($option)
    {
        $template = $this->_defaultConfig['templates']['file'];
        $name = array_shift($option);
        $attr = !empty($option[0]) ? $option[0] : [];
        $attr['class'] = isset($attr['class']) ? $attr['class'] : false;

        if (isset($attr['type'])) {
            unset($attr['type']);
        }
        if (isset($attr['value'])) {
            unset($attr['value']);
        }

        return $this->html = $this->toHtml($name, $attr, $template);
    }
}