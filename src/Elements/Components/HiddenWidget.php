<?php

namespace LaraForm\Elements\Components;

class HiddenWidget extends BaseInputWidget
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
        $attr['type'] = 'hidden';
        $this->htmlAttributes['type'] = 'hidden';
        parent::inspectionAttributes($attr);
    }
}