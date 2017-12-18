<?php

namespace LaraForm\Elements\Widgets;


class PasswordWidget extends BaseInputWidget
{
    /**
     * @return string
     */
    public function render()
    {
        return parent::render();
    }


    /**
     * @param $attr
     */
    public function checkAttributes(&$attr)
    {
        $this->otherHtmlAttributes['type'] = 'password';
        $attr['type'] = 'password';
        parent::checkAttributes($attr);
    }
}