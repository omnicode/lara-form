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
        return $this->parentRender();
    }


    /**
     * @param $attr
     */
    public function checkAttributes(&$attr)
    {
        $attr['type'] = 'number';
        $this->setOtherHtmlAttributes('type', 'number');
        $this->parentCheckAttributes($attr);
    }
}