<?php

namespace LaraForm\Elements\Components;


class PasswordWidget extends BaseInputWidget
{
    /**
     * @param $option
     * @return string
     */
    public function render($option)
    {
        $name = $option[0];
        $attr = !empty($option[1]) ? $option[1] : [];
        $this->inspectionAttributes($attr);
        return $this->toHtml($name, $attr);
    }


    /**
     * @param $attr
     */
    public function inspectionAttributes(&$attr)
    {
        if (isset($attr['type'])) {
            unset($attr['type']);
        }
        $attr['type'] = 'password';
    }
}