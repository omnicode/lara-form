<?php

namespace LaraForm\Elements\Widgets;

class InputWidget extends BaseInputWidget
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
        parent::checkAttributes($attr);
    }
}