<?php

namespace LaraForm\Elements\Widgets;

/**
 * Class InputWidget
 * @package LaraForm\Elements\Widgets
 */
class InputWidget extends BaseInputWidget
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
        parent::checkAttributes($attr);
    }
}