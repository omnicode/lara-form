<?php

namespace LaraForm\Elements\Components;

use LaraForm\Elements\Widget;

class BaseInputWidget extends Widget
{
    /**
     * @var array
     */
    protected $types = [
        'checkbox',
        'radio',
        'submit',
        'textarea',
        'file'
    ];

    /**
     * @return string
     */
    public function render()
    {
        $this->inspectionAttributes($this->attr);
        return $this->formatInputField($this->name, $this->attr);
    }

    /**
     * @param $attr
     */
    public function inspectionAttributes(&$attr)
    {
        parent::inspectionAttributes($attr);
    }

    /**
     * @param $name
     * @param $attr
     * @param bool $cTemplate
     * @return string
     */
    protected function formatInputField($name, $attr, $cTemplate = false)
    {
        if (!$cTemplate) {
            if (isset($attr['type']) && in_array($attr['type'], $this->types)) {
                $template = $this->getTemplate($attr['type']);
            } else {
                $template = $this->getTemplate('input');
            }
        } else {
            $template = $cTemplate;
        }

        $this->generalInspectionAttributes($attr, $cTemplate);
        $this->htmlAttributes['name'] = $name;
        $this->htmlAttributes['attrs'] = $this->formatAttributes($attr);
        $this->html = $this->formatTemplate($template, $this->htmlAttributes);
        return $this->completeTemplate();
    }

    /**
     * @param $template
     * @param $attr
     * @param array $labelAttrs
     * @return string
     */
    protected function formatNestingLabel($template, $attr , $labelAttrs = [])
    {
        $labelTemplate = $this->getTemplate('nestingLabel');
        $this->formatInputField($this->name, $attr, $template);

        $templateAttr = [
            'hidden' => $this->hidden,
            'content' => $this->html,
            'text' => !empty($attr['label']) ? $attr['label'] : '',
            'attrs' => $this->formatAttributes($labelAttrs)
        ];

        $this->html = $this->formatTemplate($labelTemplate, $templateAttr);
        return $this->completeTemplate();
    }

    /**
     * @param $attr
     * @param $cTemplate
     */
    private function generalInspectionAttributes(&$attr, $cTemplate)
    {
        if (isset($attr['type'])) {
            $this->htmlAttributes['type'] = $attr['type'];
            $this->unlokAttributes['type'] = $attr['type'];
        } else {
            $this->htmlAttributes['type'] = 'text';
        }
        $this->htmlAttributes['value'] = '';
        if (!empty($attr['value']) && $cTemplate) {
            $this->htmlAttributes['value'] = $attr['value'];
            $this->unlokAttributes['value'] = $attr['value'];
        }
        $notId = ['hidden', 'submit', 'reset', 'button', 'radio', 'checkbox', 'label'];
        if (!in_array($this->htmlAttributes['type'], $notId) && !$cTemplate) {
            $attr += $this->getValue($this->name);
        }
        if (!in_array($this->htmlAttributes['type'], ['hidden', 'submit', 'reset', 'button'])) {
            $this->generateLabel($attr);
        }
        if ($this->htmlAttributes['type'] !== 'hidden') {
            $this->generateClass($attr, $this->config['css']['inputClass']);
        }
        $this->otherHtmlAttributes = $attr;
        parent::inspectionAttributes($attr);
    }

}