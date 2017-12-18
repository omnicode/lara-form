<?php

namespace LaraForm\Elements\Widgets;

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
    public function checkAttributes(&$attr)
    {
        $attr['type'] = 'hidden';
        $this->setHtmlAttributes('type', 'hidden');
        parent::checkAttributes($attr);
    }
}