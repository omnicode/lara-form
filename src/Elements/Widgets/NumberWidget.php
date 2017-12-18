<?php

namespace LaraForm\Elements\Widgets;

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
    public function checkAttributes(&$attr)
    {
        $this->otherHtmlAttributes['type'] = 'number';
        $attr['type'] = 'number';
        parent::checkAttributes($attr);
    }
}