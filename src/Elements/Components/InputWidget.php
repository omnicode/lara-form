<?php

namespace LaraForm\Elements\Components;

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
    public function inspectionAttributes(&$attr)
    {
        parent::inspectionAttributes($attr);
    }
}