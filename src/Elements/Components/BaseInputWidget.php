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
     * @param $name
     * @param $attr
     * @param bool $cTemplate
     * @return string
     */
    public function toHtml($name, $attr, $cTemplate = false)
    {
        if (!$cTemplate) {
            $template = $this->_defaultConfig['templates']['input'];
            if (isset($attr['type']) && in_array($attr['type'], $this->types)) {
                $templates = $this->_defaultConfig['templates'];
                if (isset($templates[$attr['type']])) {
                    $template = $templates[$attr['type']];
                    unset($attr['type']);
                }
            }
        } else {
            $template = $cTemplate;
        }

        $htmlAttribute = [];
        if (isset($attr['id']) && $attr['id'] == false) {
            unset($attr['id']);
        } else {
            $htmlAttribute['id'] = isset($attr['id']) ? $attr['id'] : $this->getId($name);
        }


        $htmlAttribute = array_merge($htmlAttribute, $attr);
        $scoreAttribute['name'] = $name;

        if (isset($htmlAttribute['type'])) {
            $scoreAttribute['type'] = $htmlAttribute['type'];
            unset($htmlAttribute['type']);
        }

        if (!isset($htmlAttribute['class'])) {
            $htmlAttribute['class'] = $this->_defaultConfig['css']['inputClass'];
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
            $this->renderLabel($name, $htmlAttribute);
            if (isset($htmlAttribute['label'])) {
                unset($htmlAttribute['label']);
            }
        }

        $scoreAttribute['attrs'] = $this->formatAttributes($htmlAttribute);
        $this->html = $this->formatTemplate($template, $scoreAttribute);
        return $this->label.$this->html;
    }

}