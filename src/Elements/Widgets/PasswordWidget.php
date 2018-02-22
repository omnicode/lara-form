<?php
declare(strict_types=1);

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
    public function render(): string
    {
        return $this->parentRender();
    }


    /**
     * @param $attr
     */
    public function checkAttributes(array &$attr): void
    {
        $attr['type'] = 'password';
        $this->setOtherHtmlAttributes('type', 'password');
        $this->parentCheckAttributes($attr);
    }
}