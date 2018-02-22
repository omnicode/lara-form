<?php
declare(strict_types=1);

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
    public function render(): string
    {
        return $this->parentRender();
    }


    /**
     * @param $attr
     */
    public function checkAttributes(array &$attr): void
    {
        $attr['type'] = 'email';
        $this->setOtherHtmlAttributes('type', 'email');
        $this->parentCheckAttributes($attr);
    }
}