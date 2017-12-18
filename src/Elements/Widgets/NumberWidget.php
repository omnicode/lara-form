<?php

namespace LaraForm\Elements\Widgets;

/**
 * Processes and creates input tag for number
 *
 * Class NumberWidget
 * @package LaraForm\Elements\Widgets
 */
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
        $this->setOtherHtmlAttributes('type', 'number');
        $attr['type'] = 'number';
        parent::checkAttributes($attr);
    }
}