<?php

namespace LaraForm\Elements\Components;

use LaraForm\Elements\Widget;

class SelectWidget extends Widget
{
    protected $selected = false;
    protected $disabled = [];
    protected $optionsArray;
    protected $optionsHtml;

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
        $this->inspectionAttributes($attr);
        $this->renderOptions();
        $selectAttrs = [
            'content' => $this->optionsHtml,
            'name' => $name,
            'attrs' => $this->formatAttributes($attr)
        ];
        $selectHtml = $this->formatTemplate($selectTemplate, $selectAttrs);
        return $this->html = $selectHtml;
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
        foreach ($options as $index => $option) {
            $optAttrs = [];
            if (is_array($option)) {
                $this->renderOptgroup($index, $option);
            }
            $optAttrs[] = $this->isDisabled($index);
            $optAttrs[] = $this->isSelected($index);
            $rep = [
                'text' => $option,
                'value' => $index,
                'attrs' => $this->formatAttributes($optAttrs)
            ];
            $this->optionsHtml .= $this->formatTemplate($optionTemplate, $rep);
        }
        return $this->optionsHtml;
    }

    /**
     * @param $groupName
     * @param $options
     * @internal param $option
     */
    protected function renderOptgroup($groupName, $options)
    {
        $optgroupTemplate = $this->_defaultConfig['templates']['optgroup'];
        foreach ($options as $index => $option) {
            $optAttrs[] = $this->isDisabled($index);
            $optAttrs[] = $this->isSelected($index);
            $repGroup = [
                'text' => $option,
                'value' => $index,
                'attrs' => $this->formatAttributes($optAttrs)
            ];
            $this->optionsHtml .= $this->formatTemplate($optgroupTemplate, $repGroup);
        }

        $rep = [
            'lable' => $groupName,
            'content' => $this->optionsHtml,
            'attrs' => $this->formatAttributes($option)
        ];
        $this->optionsHtml .= $this->formatTemplate($optionTemplate, $rep);
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