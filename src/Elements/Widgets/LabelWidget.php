<?php

namespace LaraForm\Elements\Widgets;

use LaraForm\Elements\Widget;

class LabelWidget extends Widget
{
    /**
     * @return string
     */
    public function render()
    {
        return $this->setLabel($this->name,$this->attr);
    }

    /**
     * @param $attr
     */
    public function checkAttributes(&$attr)
    {

    }
}