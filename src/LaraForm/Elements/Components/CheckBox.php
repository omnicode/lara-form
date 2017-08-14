<?php
namespace LaraForm\Elements\Components;

use AdamWathan\BootForms\Facades\BootForm;
use LaraForm\Elements\Element;

class CheckBox extends Element
{

    public function toHtml($name, $options = [])
    {
        $label = $this->getLabel($name, $options);
        return BootForm::checkbox($label ? $label : null, $name);
    }
}