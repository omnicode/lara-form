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
        $this->name = array_shift($option);
        $attr = !empty($option[0]) ? $option[0] : [];
        $this->inspectionAttributes($attr);
        return $this->toHtml($this->name, $attr);
    }


    /**
     * @param $attrs
     * @internal param $attr
     */
    public function inspectionAttributes(&$attr)
    {
        if (isset($attr['type'])) {
            unset($attr['type']);
        }
        $attr['type'] = 'number';
    }
}