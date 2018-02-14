<?php

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
    public function render();

    /**
     * @param $attr
     * @return mixed
     */
    public function checkAttributes(&$attr);

}