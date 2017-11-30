<?php

namespace LaraForm\Elements\Components;

use LaraForm\Elements\Widget;

class BaseInputWidget extends Widget
{
    protected $types = [
        'text',
        'checkbox',
        'radio',
        'submit',
        'number',
        'email',
        'password',
        'hidden'
    ];

    public function render($option)
    {

    }

    /**
     * @param $this->name
     * @param $attr
     * @param bool $cTemplate
     * @return string
     */
    public function toHtml($name, $attr, $cTemplate = false)
    {
        if (!$cTemplate) {
            $template = $this->getTemplate($attr);
        } else {
            $template = $cTemplate;
        }

        $htmlAttribute = [];
        if (isset($attr['id']) && $attr['id'] == false) {
            unset($attr['id']);
        } else {
            $htmlAttribute['id'] = isset($attr['id']) ? $attr['id'] : $this->getId($this->name);
        }


        $htmlAttribute = array_merge($htmlAttribute, $attr);
        $scoreAttribute['name'] = $this->name;

        if (isset($htmlAttribute['type'])) {
            $scoreAttribute['type'] = $htmlAttribute['type'];
            unset($htmlAttribute['type']);
        }

        if (!isset($htmlAttribute['class'])) {
            $htmlAttribute['class'] = $this->config['css']['inputClass'];
        } elseif (isset($htmlAttribute['class']) && $htmlAttribute['class'] == false) {
            unset($htmlAttribute['class']);
        }

        if (isset($htmlAttribute['value']) && $cTemplate) {
            $scoreAttribute['value'] = $htmlAttribute['value'];
            unset($htmlAttribute['value']);
        }

        if (!isset($scoreAttribute['type'])) {
            $scoreAttribute['type'] = 'text';
        }

        if ($scoreAttribute['type'] !== 'hidden') {
            $this->renderLabel($this->name, $htmlAttribute);
            if (isset($htmlAttribute['label'])) {
                unset($htmlAttribute['label']);
            }
        }

        $scoreAttribute['attrs'] = $this->formatAttributes($htmlAttribute);
        $this->html = $this->formatTemplate($template, $scoreAttribute);
        return $this->label . $this->html;
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
                unset($attr['type']);
            }
        }
        return $template;
    }

    /**
     * @param $attr
     * @internal param $attrs
     * @internal param $attr
     */
    public function inspectionAttributes(&$attr)
    {
        if (!empty($attr['options'])) {
            $this->optionsArray = is_array($attr['options']) ? $attr['options'] : [$attr['options']];
            unset($attr['options']);
        }
        if (isset($attr['empty']) && $attr['empty'] !== false) {
            array_unshift($this->optionsArray, $attr['empty']);
            unset($attr['empty']);
        } else {
            $emptyValue = config('lara_form.label.select_empty');
            if ($emptyValue) {
                array_unshift($this->optionsArray, $emptyValue);
            }
        }
        if (isset($attr['label'])) {
            unset($attr['label']);
        }

        if (isset($attr['selected'])) {
            $this->selected = $attr['selected'];
            unset($attr['selected']);
        }

        if (isset($attr['disabled'])) {
            $this->disabled = $attr['disabled'];
            if (!is_array($this->disabled)) {
                $this->disabled = [$this->disabled];
            }
            unset($attr['disabled']);
        }
    }
}