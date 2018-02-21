<?php

namespace LaraForm\Elements\Widgets;

/**
 * Processes and creates input tag for date
 *
 * Class NumberWidget
 * @package LaraForm\Elements\Widgets
 */
class DateWidget extends BaseInputWidget
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
        $attr['type'] = 'date';
        $this->setOtherHtmlAttributes('type', 'date');
        $this->parentCheckAttributes($attr);
    }
}