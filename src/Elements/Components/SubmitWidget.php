<?php

namespace LaraForm\Elements\Components;

class SubmitWidget extends BaseInputWidget
{
    /**
     * @var array
     */
    protected $_submitTypes = ['submit', 'button', 'reset'];

    /**
     * @param $option
     * @return string
     */
    public function render($option)
    {
        $template = $this->config['templates']['submit'];
        $this->name = array_shift($option);
        $attr = !empty($option[0]) ? $option[0] : [];
        $this->inspectionAttributes($attr);
        return $this->html = $this->toHtml($this->name, $attr, $template);
    }


    /**
     * @param $attr
     */
    public function inspectionAttributes(&$attr)
    {
        $attr['class'] = isset($attr['class']) ? $attr['class'] : $this->config['css']['submitClass'];
        if (!empty($options['btn'])) {
            if ($attr['btn'] === true) {
                $attr['btn'] = $this->config['label']['submit_btn'];
            }
            $attr['class'] .= ' btn btn-' . $attr['btn'];
            unset($attr['btn']);
        }
        // @TODO - combine with Assistant::input
        if (!empty($attr['position']) && $attr['position'] == 'right') {
            $attr['class'] .= ' icon-right';
        }
        if (!isset($attr['type']) || (isset($attr['type']) && !in_array($attr['type'], $this->_submitTypes))) {
            $attr['type'] = 'submit';
        }
    }
}