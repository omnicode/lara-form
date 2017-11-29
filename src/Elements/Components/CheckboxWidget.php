<?php

namespace LaraForm\Elements\Components;

class CheckboxWidget extends BaseInputWidget
{
    /**
     * @param $option
     * @return string
     */
    public function render($option)
    {
        $template = $this->_defaultConfig['templates']['checkbox'];
        $name = array_shift($option);
        $attr = !empty($option[0]) ? $option[0] : [];
        $attr['value'] = isset($attr['value']) ? $attr['value'] : 1;

        if (isset($attr['type'])) {
            unset($attr['type']);
        }
        if (isset($attr['multiple'])) {
            $name .='[]';
            unset($attr['multiple']);
        }

        if (isset($attr['hidden']) && $attr['hidden'] == false) {
            $hidden = '';
            unset($attr['hidden']);
        } else {
            $hidden = $this->toHtml($name, ['type' => 'hidden', 'value' => 0, 'name' => $name, 'id' => false]);
        }

        return $this->html = $hidden . $this->toHtml($name, $attr, $template);
    }
}