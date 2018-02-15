<?php

namespace LaraForm\Elements\Widgets;

use LaraForm\Elements\Widget;

/**
 * Processes and creates textarea tag
 *
 * Class TextareaWidget
 * @package LaraForm\Elements\Widgets
 */
class TextareaWidget extends Widget
{

    /**
     * Returns the finished html textarea view
     *
     * @return mixed|string|void
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
     * @return mixed|void
     */
    public function checkAttributes(&$attr)
    {
        if (isset($attr['type'])) {
            unset($attr['type']);
        }
        $this->currentTemplate = $this->getTemplate('inputContainer');
        $this->setOtherHtmlAttributes('type', 'textarea');
        $this->generateClass($attr, $this->config['css']['class']['textarea']);
        $this->generateId($attr);
        $this->generateLabel($attr);
        $this->generatePlaceholder($attr);
        $this->parentCheckAttributes($attr);
    }
}