<?php

namespace LaraForm\Elements\Components;

use LaraForm\Elements\Widget;

class TextareaWidget extends Widget
{
    /**
     * @return string
     */
    public function render()
    {
        $template = $this->getTemplate('textarea');
        $this->inspectionAttributes($this->attr);
        $attributes = [
            'name' => $this->name,
            'attrs' => $this->formatAttributes($this->attr)
        ];
        $attributes += $this->getValue($this->name);
        $this->html = $this->formatTemplate($template, $attributes);
        return $this->completeTemplate();
    }

    /**
     * @param $attr
     */
    public function inspectionAttributes(&$attr)
    {
        if (isset($attr['type'])) {
            unset($attr['type']);
        }
        $this->containerTemplate = $this->getTemplate('inputContainer');
        $this->otherHtmlAttributes['type'] = 'textarea';
        $this->generateClass($attr, $this->config['css']['textareaClass']);
        $this->generateId($attr);
        $this->generateLabel($attr);
        parent::inspectionAttributes($attr);
    }
}