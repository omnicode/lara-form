<?php

namespace LaraForm\Elements\Components;

class RadioWidget extends BaseInputWidget
{
    /**
     * @param $option
     * @return string
     */
    public function render($option)
    {
        $template = $this->config['templates']['radio'];
        $this->name = array_shift($option);
        $attr = !empty($option[0]) ? $option[0] : [];
        $this->inspectionAttributes($attr);
        if (empty($attr['id'])) {
            $attr['id'] = $this->name . '-' . $attr['value'];
        }
        $this->html = $this->toHtml($this->name, $attr, $template);
        $this->html = $this->completeTemplate();
        return $this->html;
    }


    /**
     * @param $attr
     * @internal param $attrs
     * @internal param $attr
     */
    public function inspectionAttributes(&$attr)
    {
        $attr['value'] = isset($attr['value']) ? $attr['value'] : 1;
        $attr['class'] = isset($attr['class']) ? $attr['class'] : $this->config['css']['radioClass'];
        if (isset($attr['type'])) {
            unset($attr['type']);
        }
        if (isset($attr['checked'])) {
            $attr['checked'] = 'checked';
        }
        if (isset($attr['hidden']) && $attr['hidden'] == false) {
            unset($attr['hidden']);
        } else {
            $this->hidden = $this->setHidden($this->name, '');
        }
    }
}