<?php

namespace LaraForm\Elements\Components;

class EmailWidget extends BaseInputWidget
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
        $this->otherHtmlAttributes['type'] = 'email';
        $attr['type'] = 'email';
        parent::inspectionAttributes($attr);
    }
}