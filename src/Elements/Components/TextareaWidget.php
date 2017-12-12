<?php

namespace LaraForm\Elements\Components;

use LaraForm\Elements\Widget;

class TextareaWidget extends BaseInputWidget
{
    /**
     * @return string
     */
    public function render()
    {
        $template = $this->getTemplate('textarea');
        $this->inspectionAttributes($this->attr);
        $this->containerParams['inline']['type'] = !empty($this->containerParams['inline']['type']) ? $this->containerParams['inline']['type'] :'textarea';
        return $this->html = $this->formatInputField($this->name, $this->attr, $template);
    }

    /**
     * @param $attr
     */
    public function inspectionAttributes(&$attr)
    {
        $this->generateClass($attr,$this->config['css']['textareaClass']);
        if (isset($attr['type'])) {
            unset($attr['type']);
        }
        parent::inspectionAttributes($attr);
    }
}