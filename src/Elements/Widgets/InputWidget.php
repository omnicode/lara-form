<?php
declare(strict_types=1);

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
        $this->parentCheckAttributes($attr);
    }
}