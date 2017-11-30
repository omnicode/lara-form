<?php

namespace LaraForm\Elements\Components;

class FileWidget extends BaseInputWidget
{
    /**
     * @param $option
     * @return mixed
     */
    public function render($option)
    {
        $template = $this->config['templates']['file'];
        $this->name = array_shift($option);
        $attr = !empty($option[0]) ? $option[0] : [];
        $this->inspectionAttributes($attr);
        return $this->html = $this->toHtml($this->name, $attr, $template);
    }

    /**
     * @param $attr
     */
    public function inspectionAttributes(&$attr)
    {
        $attr['class'] = isset($attr['class']) ? $attr['class'] : $this->config['css']['fileClass'];
        if (isset($attr['type'])) {
            unset($attr['type']);
        }
        if (isset($attr['value'])) {
            unset($attr['value']);
        }
    }
}