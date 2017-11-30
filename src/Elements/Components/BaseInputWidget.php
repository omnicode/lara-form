<?php

namespace LaraForm\Elements\Components;

use LaraForm\Elements\Widget;

class BaseInputWidget extends Widget
{
    /**
     * @var array
     */
    protected $htmlAttributes = [];

    /**
     * @var array
     */
    protected $types = [
        'checkbox',
        'radio',
        'submit'
    ];

    /**
     * @param $option
     */
    public function render($option)
    {

    }

    /**
     * @param $attr
     */
    public function inspectionAttributes(&$attr)
    {

    }

    /**
     * @param $name
     * @param $attr
     * @param bool $cTemplate
     * @return string
     * @internal param $this ->name
     */
    public function toHtml($name, $attr, $cTemplate = false)
    {
        if (!$cTemplate) {
            $template = $this->getTemplate($attr);
        } else {
            $template = $cTemplate;
        }

        $this->generalInspectionAttributes($attr, $cTemplate);
        $this->htmlAttributes['name'] = $this->name;
        $this->htmlAttributes['attrs'] = $this->formatAttributes($attr);
        $this->html = $this->formatTemplate($template, $this->htmlAttributes);
        return $this->completeTemplate();
    }

    /**
     * @param $attr
     * @return mixed
     */
    protected function getTemplate(&$attr)
    {
        $templates = $this->config['templates'];
        $template = $templates['input'];
        if (isset($attr['type']) && in_array($attr['type'], $this->types)) {
            if (isset($templates[$attr['type']])) {
                $template = $templates[$attr['type']];
            }
        }
        return $template;
    }

    /**
     * @param $attr
     * @param $cTemplate
     * @internal param $attrs
     * @internal param $attr
     */
    public function generalInspectionAttributes(&$attr, $cTemplate)
    {
        if (isset($attr['id']) && $attr['id'] == false) {
            unset($attr['id']);
        } else {
            $attr['id'] = isset($attr['id']) ? $attr['id'] : $this->getId($this->name);
        }

        if (isset($attr['type'])) {
            $this->htmlAttributes['type'] = $attr['type'];
            unset($attr['type']);
        } else {
            $this->htmlAttributes['type'] = 'text';
        }

        if (!isset($attr['class'])) {
            $attr['class'] = $this->config['css']['inputClass'];
        } elseif (isset($attr['class']) && $attr['class'] == false) {
            unset($attr['class']);
        }

        if (isset($attr['value']) && $cTemplate) {
            $this->htmlAttributes['value'] = $attr['value'];
            unset($attr['value']);
        }
        if ($this->htmlAttributes['type'] !== 'hidden') {
            $this->renderLabel($this->name, $attr);
            if (isset($attr['label'])) {
                unset($attr['label']);
            }
        }
    }
}