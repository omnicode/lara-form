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
        return $this->parentRender();
    }

    /**
     * @param $attr
     * @return mixed|void
     */
    public function checkAttributes(&$attr)
    {
        $this->parentCheckAttributes($attr);
    }
}