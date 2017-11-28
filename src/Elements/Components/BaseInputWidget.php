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
        $lable = '';
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

        if (isset($htmlAttribute['value']) && $cTemplate) {
            $scoreAttribute['value'] = $htmlAttribute['value'];
            unset($htmlAttribute['value']);
        }

        if (!isset($scoreAttribute['type'])) {
            $scoreAttribute['type'] = 'text';
        }
        if ($scoreAttribute['type'] !== 'hidden') {
            if (!isset($attr['lable']) || $attr['lable'] !== false) {
                $for = isset($htmlAttribute['id'])  ? $htmlAttribute['id'] : $name;
                $lableName = $this->getLableName($name);
                $lable = $this->setLable([$lableName,['for' => $for]]);
            }
        }
        $scoreAttribute['attrs'] = $this->formatAttributes($htmlAttribute);
        $this->html = $lable.$this->formatTemplate($template, $scoreAttribute);
        return $this->html;
    }

}