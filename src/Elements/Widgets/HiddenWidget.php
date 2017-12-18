<?php

namespace LaraForm\Elements\Widgets;

/**
 * Processes and creates input tag for hidden type
 *
 * Class HiddenWidget
 * @package LaraForm\Elements\Widgets
 */
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
     * @return mixed|void
     */
    public function checkAttributes(&$attr)
    {
        $attr['type'] = 'hidden';
        $this->setHtmlAttributes('type', 'hidden');
        parent::checkAttributes($attr);
    }
}