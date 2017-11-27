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
    ];


    protected function toInputFild($name, $attr, $cTemplate = false)
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
        }else{
            $template = $cTemplate;
        }

        if (!empty($attr['lable'])) {
            $lableName = $attr['lable'];
        } else {
            $lableName = $this->getLableName($name);
        }

        $htmlAttribute['name'] = $name;
        $htmlAttribute['id'] = $lableName ? $lableName : $name;
        $htmlAttribute = array_merge($htmlAttribute,$attr);
        foreach ($attr as $index => $item) {
            if (!empty($item)) {
                $htmlAttribute += [$index => $item];
            }
        }

        if (!$cTemplate && !isset($htmlAttribute['type'])) {
            $htmlAttribute['type'] = 'text';
        }

        $scoreAttribute['attrs'] = $this->formatAttributes($htmlAttribute);
        return dump($this->formatTemplate($template, $scoreAttribute));
    }
}