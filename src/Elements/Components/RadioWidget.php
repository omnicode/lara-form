<?php

namespace LaraForm\Elements\Components;

class RadioWidget extends BaseInputWidget
{
    /**
     * @return string
     */
    public function render()
    {
        $template = $this->config['templates']['radio'];
        $this->inspectionAttributes($attr);
        $this->containerTemplate = $this->getTemplate('radioContainer');
        $this->otherHtmlAttributes['type'] = 'radio';
        return $this->formatNestingLabel($template,$attr);
    }

    /**
     * @param $attr
     */
    public function inspectionAttributes(&$attr)
    {
        $attr['value'] = isset($attr['value']) ? $attr['value'] : $this->config['default_value']['radio'];
        if (!empty($attr['value'])) {
            $val = $this->getValue($this->name)['value'];
            if (!is_array($val)) {
                $val = [$val];
            }
            if (in_array($attr['value'], $val)) {
                $attr['checked'] = true;
            }
        }

        if (isset($attr['checked'])) {
            $attr['checked'] = 'checked';
        }

        $attr['type'] = 'radio';
        $this->generateClass($attr, $this->config['css']['radioClass']);
        $this->generateId($attr,true);
        parent::inspectionAttributes($attr);
    }

}