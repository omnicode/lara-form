<?php
declare(strict_types=1);

namespace LaraForm\Elements\Widgets;

use LaraForm\Elements\Widget;

/**
 * Creates an html tag label
 *
 * Class LabelWidget
 * @package LaraForm\Elements\Widgets
 */
class LabelWidget extends Widget
{
    /**
     * Creates an html tag label
     *
     * @return mixed|void
     */
    public function render(): string
    {
        return $this->renderLabel($this->name,$this->attr);
    }

    /**
     * @param $attr
     * @return mixed|void
     */
    public function checkAttributes(array &$attr): void
    {

    }
}