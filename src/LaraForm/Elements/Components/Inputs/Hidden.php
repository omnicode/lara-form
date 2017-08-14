<?php
namespace LaraForm\Elements\Components\Inputs;

use LaraForm\Elements\Element;

class Hidden extends Element
{

    /**
     * @param $name
     * @param $options
     * @return string
     */
    public function toHtml($name, $options = [])
    {
        $value = '';
        $optionsStr = '';
        if (is_array($options)) {
            if (isset($options['value'])) {
                $value = $options['value'];
                unset($options['value']);
            }

            // @TODO - generalize with Assistant::link(); !!
            foreach ($options as $attr => $value) {
                $optionsStr .= $attr . '="' . $value . '" ';
            }
        } else {
            $value = $options;
        }

        return '<input type="hidden" name="'.$name.'" value="'.$value.'" '.trim($optionsStr).' />';
    }

    /**
     * @param array $options
     * @return array|mixed|string
     */
    public function getValue($options = [])
    {
        $value = '';
        if (is_array($options)) {
            if (isset($options['value'])) {
                $value = $options['value'];
            }
        } else {
            $value = $options;
        }
        return $value;
    }
}