<?php

namespace LaraForm\Elements\Components;

class InputWidget extends BaseInputWidget
{
    /**
     * @var array
     */
    protected $otherInput = ['checkbox', 'radio', 'submit','file'];

    /**
     * @param $option
     * @return mixed|string
     */
    public function render($option)
    {
        $name = $option[0];
        $attr = !empty($option[1]) ? $option[1] : [];
        if (isset($attr['type'])) {
            if (in_array($attr['type'], $this->otherInput)) {
                return $this->html = $this->createObject($attr['type'], [$option]);
            }
        }

        return $this->toHtml($name, $attr);
    }
}