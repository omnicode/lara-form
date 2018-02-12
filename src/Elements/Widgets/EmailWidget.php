<?php

namespace LaraForm\Elements\Widgets;

/**
 * Processes and creates input tag for email type
 *
 * Class EmailWidget
 * @package LaraForm\Elements\Widgets
 */
class EmailWidget extends BaseInputWidget
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
        $this->setOtherHtmlAttributes('type', 'email');
        $attr['type'] = 'email';
        $this->parentCheckAttributes($attr);
    }
}