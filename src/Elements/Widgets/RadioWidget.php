<?php

namespace LaraForm\Elements\Widgets;

class RadioWidget extends BaseInputWidget
{
    /**
     * @return string
     */
    public function render()
    {
        $template = $this->config['templates']['radio'];
        $this->checkAttributes($attr);
        $this->currentTemplate = $this->getTemplate('radioContainer');
        $this->otherHtmlAttributes['type'] = 'radio';
        return $this->formatNestingLabel($template,$attr);
    }

    /**
     * @param $attr
     */
    public function checkAttributes(&$attr)
    {
        $attr['value'] = isset($attr['value']) ? $attr['value'] : 1;
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
        $this->generateClass($attr, $this->config['css']['class']['radioClass']);
        $this->generateId($attr,true);
        parent::checkAttributes($attr);
    }

}