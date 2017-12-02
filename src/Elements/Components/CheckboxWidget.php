<?php

namespace LaraForm\Elements\Components;

class CheckboxWidget extends BaseInputWidget
{
    /**
     * @param $option
     * @return string
     */
    public function render($option)
    {
        $template = $this->config['templates']['checkbox'];
        $this->name = array_shift($option);
        $attr = !empty($option[0]) ? $option[0] : [];
        if (strpos($this->name, '[]')) {
            $attr['multiple'] = true;
        }
        $this->inspectionAttributes($attr);
        $this->containerTemplate = $this->config['templates']['checkboxContainer'];
        $labelTemplate = $this->config['templates']['nestingLabel'];
        $this->toHtml($this->name, $attr, $template);
        $labelAttr = [
            'hidden' => $this->hidden,
            'content' => $this->html,
            'text' => isset($attr['label']) ? $attr['label'] : $this->getLabelName($this->name),
            'attrs' => ''
        ];

        $this->html = $this->formatTemplate($labelTemplate, $labelAttr);
        $this->html = $this->completeTemplate();
        return $this->html;
    }


    /**
     * @param $attr
     */
    public function inspectionAttributes(&$attr)
    {
        $attr['value'] = isset($attr['value']) ? $attr['value'] : 1;
        $attr['class'] = isset($attr['class']) ? $attr['class'] : $this->config['css']['checkboxClass'];

        if (isset($attr['type'])) {
            unset($attr['type']);
        }
        if (isset($attr['checked'])) {
            $attr['checked'] = 'checked';
        }
        if (isset($attr['multiple'])) {
            if (!strpos($this->name, '[]')) {
                $this->name .= '[]';
            }
            unset($attr['multiple']);
        }
        if (isset($attr['hidden']) && $attr['hidden'] == false) {
            unset($attr['hidden']);
        } else {
            $this->hidden = $this->toHtml($this->name, ['type' => 'hidden', 'value' => '0', 'id' => false]);
        }
    }
}