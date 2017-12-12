<?php

namespace LaraForm\Elements\Components;

use LaraForm\Elements\Widget;

class LabelWidget extends Widget
{
    /**
     * @return string
     */
    public function render()
    {
        return $this->setLabel($option);
    }

    /**
     * @param $attr
     */
    public function inspectionAttributes(&$attr)
    {

    }
}