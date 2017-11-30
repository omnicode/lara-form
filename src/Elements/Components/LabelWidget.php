<?php

namespace LaraForm\Elements\Components;

use LaraForm\Elements\Widget;

class LabelWidget extends Widget
{
    /**
     * @param $option
     * @return string
     */
    public function render($option)
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