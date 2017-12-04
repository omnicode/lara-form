<?php

namespace LaraForm\Elements\Components;

use LaraForm\Elements\Widget;

class TextareaWidget extends BaseInputWidget
{
    /**
     * @param $option
     * @return string
     */
    public function render($option)
    {
        $template = $this->getTemplate('textarea');
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
        $attr += $this->getValue($this->name);
        $attr['class'] = isset($attr['class']) ? $attr['class'] : $this->config['css']['textareaClass'];
        if (isset($attr['type'])) {
            unset($attr['type']);
        }
    }
}