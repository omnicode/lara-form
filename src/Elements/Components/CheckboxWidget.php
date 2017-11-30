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
        $template = $this->config['templates']['checkbox'];
        $this->name = array_shift($option);
        $attr = !empty($option[0]) ? $option[0] : [];
        if (strpos($this->name,'[]')) {
            $attr['multiple'] = true;
        }
        $this->inspectionAttributes($attr);
        $this->containerTemplate = $this->config['templates']['nestingLabel'];
        return $this->toHtml($this->name, $attr, $template);
    }


    /**
     * @param $attr
     */
    public function inspectionAttributes(&$attr)
    {
        $attr['value'] = isset($attr['value']) ? $attr['value'] : 1;
        $attr['class'] = isset($attr['class']) ? $attr['class'] : $this->config['css']['checkboxClass'];

        if (isset($attr['type'])) {
            unset($attr['type']);
        }
        if (isset($attr['multiple'])) {
            if (!strpos($this->name,'[]')) {
                $this->name .='[]';
            }
            unset($attr['multiple']);
        }

        if (isset($attr['hidden']) && $attr['hidden'] == false) {
            unset($attr['hidden']);
        } else {
            $this->hidden = $this->toHtml($this->name, ['type' => 'hidden', 'value' => '0', 'name' => $this->name, 'id' => false]);
        }
    }
}