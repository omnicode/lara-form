<?php

namespace LaraForm\Elements\Components;

class EmailWidget extends BaseInputWidget
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
        return $this->html = $this->toHtml($this->name, $attr);
    }


    /**
     * @param $attr
     */
    public function inspectionAttributes(&$attr)
    {
        if (isset($attr['type'])) {
            unset($attr['type']);
        }
        $attr['type'] = 'email';
    }
}