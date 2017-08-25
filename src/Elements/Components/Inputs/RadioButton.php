<?php
namespace LaraForm\Elements\Components\Inputs;

use AdamWathan\BootForms\Facades\BootForm;
use LaraForm\Elements\Element;

class RadioButton extends Element
{
    /**
     * @param $name
     * @param array $options
     * @return mixed
     */
    public function toHtml($name, $options = [])
    {
        $label = $this->getLabel($name, $options);

//        $isChecked = (empty($options['checked'])) ? true : false;
//        unset($options['checked']);

        $radio = BootForm::radio($label ? $label : null, $name );

        foreach ($options as $k => $v) {
            if ($k == 'class') {
                $radio->class($v);
            } else {
                $radio->attribute($k, $v);
            }
        }
        return $radio;
    }
}