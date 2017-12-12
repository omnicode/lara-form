<?php

namespace LaraForm\Elements\Components;


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
    public function inspectionAttributes(&$attr)
    {
        $this->otherHtmlAttributes['type'] = 'password';
        $attr['type'] = 'password';
        parent::inspectionAttributes($attr);
    }
}