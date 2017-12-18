<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 28-Nov-17
 * Time: 05:23 PM
 */

namespace LaraForm\Elements;


interface WidgetInterface
{
    public function render();

    public function checkAttributes(&$attr);

}