<?php
declare(strict_types=1);

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
    public function render(): string
    {
        return $this->parentRender();
    }


    /**
     * @param $attr
     */
    public function checkAttributes(array &$attr): void
    {
        $attr['type'] = 'number';
        $this->setOtherHtmlAttributes('type', 'number');
        $this->parentCheckAttributes($attr);
    }
}