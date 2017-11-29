<?php

namespace LaraForm\Elements\Components;

use LaraForm\Elements\Widget;

class SelectWidget extends Widget
{
    /**
     * @var bool
     */
    protected $selected = false;

    /**
     * @var array
     */
    protected $disabled = [];

    /**
     * @var
     */
    protected $optionsArray;

    /**
     * @param $params
     * @return string
     * @internal param $option
     */
    public function render($params)
    {
        $selectTemplate = $this->_defaultConfig['templates']['select'];
        $name = array_shift($params);
        $attr = !empty($params[0]) ? $params[0] : [];
        $attr['class'] = isset($attr['class']) ? $attr['class'] : $this->_defaultConfig['css']['inputClass'];
        $this->inspectionAttributes($attr);
        $optionsHtml = $this->renderOptions();
        $selectAttrs = [
            'content' => $optionsHtml,
            'name' => $name,
            'attrs' => $this->formatAttributes($attr)
        ];
        $this->renderLabel($name, $params);
        $this->html = $this->formatTemplate($selectTemplate, $selectAttrs);
        return $this->label.$this->html;
    }

    /**
     * @param bool $gropup
     * @return bool
     * @internal param $optionTemplate
     */
    protected function renderOptions($gropup = false)
    {
        $optionTemplate = $this->_defaultConfig['templates']['option'];
        if ($gropup) {
            $options = $gropup;
        } else {
            $options = $this->optionsArray;
        }
        if (empty($options)) {
            return false;
        }
        $optionsHtml = '';
        foreach ($options as $index => $option) {
            $optAttrs = [];
            if (is_array($option)) {
                $optionsHtml .=$this->renderOptgroup($index, $option);
                continue;
            }
            $optAttrs[] = $this->isDisabled($index);
            $optAttrs[] = $this->isSelected($index);
            $rep = [
                'text' => $option,
                'value' => $index,
                'attrs' => $this->formatAttributes($optAttrs)
            ];
            $optionsHtml .= $this->formatTemplate($optionTemplate, $rep);
        }
        return $optionsHtml;
    }

    /**
     * @param $groupName
     * @param $options
     * @internal param $option
     * @return string
     */
    protected function renderOptgroup($groupName, $options)
    {
        $optgroupTemplate = $this->_defaultConfig['templates']['optgroup'];
        $childOptionsHtml = $this->renderOptions($options);

        $rep = [
            'label' => $groupName,
            'content' => $childOptionsHtml,
        ];
       return $this->formatTemplate($optgroupTemplate, $rep);
    }

    /**
     * @param $attr
     */
    protected function inspectionAttributes(&$attr)
    {
        if (!empty($attr['options'])) {
            $this->optionsArray = is_array($attr['options']) ? $attr['options'] : [$attr['options']];
            unset($attr['options']);
        } else {
            $this->optionsArray = ['--Select--'];
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

    /**
     * @param $str
     * @return string
     */
    protected function isDisabled($str)
    {
        if (in_array($str, $this->disabled)) {
            return 'disabled';
        }
        return '';
    }

    /**
     * @param $str
     * @return string
     */
    protected function isSelected($str)
    {
        if ($this->selected == $str) {
            return 'selected';
        }
        return '';
    }
}