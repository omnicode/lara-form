<?php

namespace LaraForm\Elements\Widgets;

use LaraForm\Elements\Widget;

class TextareaWidget extends Widget
{
    /**
     * @return string
     */
    public function render()
    {
        $template = $this->getTemplate('textarea');
        $this->checkAttributes($this->attr);
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
    public function checkAttributes(&$attr)
    {
        if (isset($attr['type'])) {
            unset($attr['type']);
        }
        $this->currentTemplate = $this->getTemplate('inputContainer');
        $this->setOtherHtmlAttributes('type', 'textarea');
        $this->generateClass($attr, $this->config['css']['class']['textareaClass']);
        $this->generateId($attr);
        $this->generateLabel();
        parent::checkAttributes($attr);
    }
}