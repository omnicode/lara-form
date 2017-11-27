<?php

namespace LaraForm\Elements\Components;

class InputWidget extends BaseInputWidget
{
    protected $otherInput = ['checkbox', 'radio', 'submit'];

    public function __construct($option)
    {
        $name = $option[0];
        $attr = !empty($option[1]) ? $option[1] : [];
        if (isset($attr['type'])) {
            if (in_array($attr['type'], $this->otherInput)) {
                return $this->createObject($attr['type'], [$option]);
            }
        }

        return $this->toInputFild($name, $attr);
    }

}