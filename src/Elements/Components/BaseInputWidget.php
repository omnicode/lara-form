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
            if (isset($attr['type']) && in_array($attr['type'], $this->types)) {
                $template = $this->getTemplate($attr['type']);
            }else{
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
     * @param $attr
     * @param $cTemplate
     * @internal param $attrs
     * @internal param $attr
     */
    public function generalInspectionAttributes(&$attr, $cTemplate)
    {
        $this->generateId($attr);

        if (isset($attr['type'])) {
            $this->htmlAttributes['type'] = $attr['type'];
            unset($attr['type']);
        } else {
            $this->htmlAttributes['type'] = 'text';
        }
        if (isset($attr['value']) && $cTemplate) {
            $this->htmlAttributes['value'] = $attr['value'];
            unset($attr['value']);
        }
        $notD = ['hidden', 'submit', 'reset', 'button', 'radio', 'checkbox', 'label'];
        if (!in_array($this->htmlAttributes['type'], $notD) && !$cTemplate) {
            $attr += $this->getValue($this->name);
        }
        $notLabel = ['hidden', 'submit', 'reset', 'button'];
        if (!in_array($this->htmlAttributes['type'], $notLabel)) {
            if (isset($attr['label']) && $attr['label'] !== false) {
                $this->renderLabel($attr['label'], $attr);
                unset($attr['label']);
            } else {
                $this->renderLabel($this->name, $attr);
            }
        }
        if (!isset($attr['class']) && $this->htmlAttributes['type'] !== 'hidden') {
            $attr['class'] = $this->config['css']['inputClass'];
        } elseif (isset($attr['class']) && $attr['class'] == false) {
            unset($attr['class']);
        }
    }

}