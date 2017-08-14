<?php
namespace LaraForm\Elements\Components;

use AdamWathan\BootForms\Facades\BootForm;
use LaraForm\Elements\Element;

class Textarea extends Element
{
    /**
     * @param $name
     * @param array $options
     * @return mixed
     */
    public function toHtml($name, $options = [])
    {
        $label = $this->getLabel($name, $options);
        $textarea = BootForm::textarea(($label ? $label: null), $name);


        foreach ($options as $k => $val) {
            $textarea->attribute($k, $val);
        }

        return $textarea;
    }
}