<?php
namespace LaraForm\Elements\Components\Inputs;

use LaraForm\Elements\Element;

class Input extends Element
{
    /**
     * @param string $name
     * @param array $options
     * @return mixed
     */
    public function toHtml($name='', $options = [])
    {

        $type = 'text';
        if (isset($options['type'])) {
            $type = $options['type'];
            unset($options['type']);
        }

        $label = $this->getLabel($name, $options);
        $input = call_user_func('BootForm::' . $type, $label, $name);

        if ($label === false) {
            $input->hideLabel();
        }

        if(!empty($options['class'])) {
            $input->class('form-control '. $options['class']);
            unset($options['class']);
        }

        foreach ($options as $k => $val) {
            $input->attribute($k, $val);
        }

        return $input;
    }
}