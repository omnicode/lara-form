<?php

namespace LaraForm\Elements\Components;

class NumberWidget extends BaseInputWidget
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
        $this->otherHtmlAttributes['type'] = 'number';
        $attr['type'] = 'number';
        parent::inspectionAttributes($attr);
    }
}