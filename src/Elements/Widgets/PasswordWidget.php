<?php

namespace LaraForm\Elements\Widgets;

/**
 * Processes and creates input tag for password type
 *
 * Class PasswordWidget
 * @package LaraForm\Elements\Widgets
 */
class PasswordWidget extends BaseInputWidget
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
        $this->setOtherHtmlAttributes('type', 'password');
        $attr['type'] = 'password';
        $this->parentCheckAttributes($attr);
    }
}