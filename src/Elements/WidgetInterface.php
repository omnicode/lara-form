<?php
declare(strict_types=1);

namespace LaraForm\Elements;

/**
 * Interface WidgetInterface
 * @package LaraForm\Elements
 */
interface WidgetInterface
{
    /**
     * @return mixed
     */
    public function render(): string;

    /**
     * @param $attr
     * @return mixed
     */
    public function checkAttributes(array &$attr): void;

}