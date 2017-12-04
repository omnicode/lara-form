<?php

namespace LaraForm\Elements\Components;

class SubmitWidget extends BaseInputWidget
{
    protected $icon = '';

    /**
     * @param $option
     * @return string
     */
    public function render($option)
    {
        $this->name = array_shift($option);
        $attr = !empty($option[0]) ? $option[0] : [];
        $this->inspectionAttributes($attr);
        $template = $this->getTemplate('button');
        $btnAttr = [
            'attrs' => $this->formatAttributes($attr),
            'text' => $this->icon. !empty($this->name) ? $this->name : '',
        ];
        return $this->html = $this->formatTemplate($template, $btnAttr);
    }


    /**
     * @param $attr
     */
    public function inspectionAttributes(&$attr)
    {
        $attr['class'] = isset($attr['class']) ? $attr['class'] . ' btn' : $this->config['css']['submitClass'];
        if (!empty($options['btn'])) {
            if ($attr['btn'] === true) {
                $attr['btn'] = $this->config['label']['submit_btn'];
            }
            $attr['class'] .= ' btn btn-' . $attr['btn'];
            unset($attr['btn']);
        }
        $iconTemplate = $this->getTemplate('icon');
        if (!empty($attr['icon'])) {
            $this->icon = $this->formatTemplate($iconTemplate, ['name' => $attr['icon']]);
            unset($attr['icon']);
        }

        if (!empty($attr['position']) && $attr['position'] == 'right') {
            $attr['class'] .= ' icon-right';
        }
        $attr['type'] = 'submit';
    }
}