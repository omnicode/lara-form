<?php

namespace LaraForm\Elements\Components;

use LaraForm\Elements\Widget;
use function Symfony\Component\Debug\Tests\testHeader;

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
        $selectTemplate = $this->config['templates']['select'];
        $this->name = array_shift($params);
        $attr = !empty($params[0]) ? $params[0] : [];
        $this->inspectionAttributes($attr);
        $optionsHtml = $this->renderOptions();
        $selectAttrs = [
            'content' => $optionsHtml,
            'name' => $this->name,
            'attrs' => $this->formatAttributes($attr)
        ];
        $this->renderLabel($this->name, $params);
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
        $optionTemplate = $this->config['templates']['option'];
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
            $optAttrs += $this->isDisabled($index);
            $optAttrs += $this->isSelected($index);
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
        $optgroupTemplate = $this->config['templates']['optgroup'];
        $childOptionsHtml = $this->renderOptions($options);

        $rep = [
            'label' => $groupName,
            'content' => $childOptionsHtml,
            'attrs' => false
        ];
       return $this->formatTemplate($optgroupTemplate, $rep);
    }

    /**
     * @param $attrs
     * @internal param $attr
     */
    public function inspectionAttributes(&$attr)
    {
        $attr['class'] = isset($attr['class']) ? $attr['class'] : $this->config['css']['selectClass'];

        if (!empty($attr['options'])) {
            $this->optionsArray = is_array($attr['options']) ? $attr['options'] : [$attr['options']];
            unset($attr['options']);
        }
        if (isset($attr['empty']) && $attr['empty'] !== false) {
             array_unshift($this->optionsArray,$attr['empty']);
             unset($attr['empty']);
        }else{
            $emptyValue = config('lara_form.label.select_empty');
            if ($emptyValue) {
                array_unshift($this->optionsArray,$emptyValue);
            }
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
     * @return array
     */
    protected function isDisabled($str)
    {
        $arr = [];
        if (in_array($str, $this->disabled)) {
            $arr['disabled'] = 'disabled';
        }
        return $arr;
    }

    /**
     * @param $str
     * @return array
     */
    protected function isSelected($str)
    {
        $arr = [];
        if ($this->selected == $str) {
            $arr['selected'] = 'selected';
        }
        return $arr;
    }
}