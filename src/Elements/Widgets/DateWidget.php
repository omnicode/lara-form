<?php
declare(strict_types=1);

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
    public function render(): string
    {
        return $this->parentRender();
    }


    /**
     * @param $attr
     */
    public function checkAttributes(array &$attr): void
    {
        $attr['type'] = 'date';
        $this->setOtherHtmlAttributes('type', 'date');
        $this->parentCheckAttributes($attr);
    }
}