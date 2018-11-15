<?php
declare(strict_types=1);

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
    public function render(): string
    {
        return $this->parentRender();
    }

    /**
     * @param $attr
     * @return mixed|void
     */
    public function checkAttributes(array &$attr): void
    {
        $attr['type'] = 'hidden';
        $this->setHtmlAttributes('type', 'hidden');
        $this->parentCheckAttributes($attr);
    }
}