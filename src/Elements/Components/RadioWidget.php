<?php

namespace LaraForm\Elements\Components;

class RadioWidget extends BaseInputWidget
{
    /**
     * @param $option
     * @return string
     */
    public function render($option)
    {
        $template = $this->config['templates']['radio'];
        $this->name = array_shift($option);
        $attr = !empty($option[0]) ? $option[0] : [];
        $this->inspectionAttributes($attr);
        if (empty($attr['id'])) {
            $attr['id'] = $this->name . '-' . $attr['value'];
        }
        return $this->toHtml($this->name, $attr, $template);
    }


    /**
     * @param $attrs
     * @internal param $attr
     */
    public function inspectionAttributes(&$attr)
    {
        $attr['value'] = isset($attr['value']) ? $attr['value'] : 1;
        $attr['class'] = isset($attr['class']) ? $attr['class'] : $this->config['css']['radioClass'];
        if (isset($attr['type'])) {
            unset($attr['type']);
        }
    }
}