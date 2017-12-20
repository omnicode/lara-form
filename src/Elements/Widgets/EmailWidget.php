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
        return parent::render();
    }


    /**
     * @param $attr
     */
    public function checkAttributes(&$attr)
    {
        $this->setOtherHtmlAttributes('type', 'email');
        $attr['type'] = 'email';
        parent::checkAttributes($attr);
    }
}