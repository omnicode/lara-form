<?php

namespace LaraForm\Elements\Components;

use LaraForm\Elements\Widget;
use function Symfony\Component\Debug\Tests\testHeader;

class SelectWidget extends Widget
{
    /**
     * @var
     */
    protected $selectTemplate;

    /**
     * @var bool
     */
    protected $selected = [];

    /**
     * @var array
     */
    protected $optionDisabled = [];

    /**
     * @var array
     */
    protected $groupDisabled = [];

    /**
     * @var
     */
    protected $optionsArray;


    /**
     * @return mixed|string|void
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function render()
    {
        $this->inspectionAttributes($this->attr);
        $optionsHtml = $this->renderOptions();
        $selectAttrs = [
            'content' => $optionsHtml,
            'name' => $this->name,
            'attrs' => $this->formatAttributes($this->attr)
        ];
        $this->htmlAttributes['type'] = 'select';
        $this->html = $this->formatTemplate($this->selectTemplate, $selectAttrs);
        return $this->completeTemplate();
    }

    /**
     * @param bool $gropup
     * @return bool|string
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function renderOptions($gropup = false)
    {
        $optionTemplate = $this->getTemplate('option');
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
                $optionsHtml .= $this->renderOptgroup($index, $option);
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
     * @return string
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function renderOptgroup($groupName, $options)
    {
        $optgroupTemplate = $this->getTemplate('optgroup', false);
        $childOptionsHtml = $this->renderOptions($options);
        $groupAttrs = [];
        if (!empty($this->groupDisabled)) {
            $groupAttrs = $this->isDisabled($groupName, $this->groupDisabled);
        }
        $rep = [
            'label' => $groupName,
            'content' => $childOptionsHtml,
            'attrs' => $this->formatAttributes($groupAttrs)
        ];
        return $this->formatTemplate($optgroupTemplate, $rep);
    }

    /**
     * @param $attr
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function inspectionAttributes(&$attr)
    {
        $this->generateId($attr);
        $this->generateLabel($attr);
        $this->generateClass($attr,$this->config['css']['selectClass']);
        if (strpos($this->name, '[]')) {
            $attr['multiple'] = true;
            $this->name = str_ireplace('[]', '', $this->name);
        }
        if (isset($attr['multiple'])) {
            $this->selectTemplate = $this->getTemplate('selectMultiple');
            unset($attr['multiple']);
        } else {
            $this->selectTemplate = $this->getTemplate('select');
        }
        if (!empty($attr['options'])) {
            $this->optionsArray = is_array($attr['options']) ? $attr['options'] : [$attr['options']];
            unset($attr['options']);
        }
        if (isset($attr['empty']) && $attr['empty'] !== false) {
            array_unshift($this->optionsArray, $attr['empty']);
            unset($attr['empty']);
        } else {
            $emptyValue = $this->config['label']['select_empty'];
            if ($emptyValue) {
                array_unshift($this->optionsArray, $emptyValue);
            }
        }
        if (isset($attr['selected'])) {
            $this->selected = $attr['selected'];
            if (!is_array($this->selected)) {
                $this->selected = [$this->selected];
            }
            unset($attr['selected']);
        }

        if (isset($attr['disabled']) && $attr['disabled'] != false) {
            $attr['disabled'] = 'disabled';
        }
        if (isset($attr['optionDisabled']) && $attr['optionDisabled'] !== false) {
            $this->optionDisabled = $attr['optionDisabled'];
            if (!is_array($this->optionDisabled)) {
                $this->optionDisabled = [$this->optionDisabled];
            }
            unset($attr['optionDisabled']);
        }
        if (isset($attr['groupDisabled']) && $attr['groupDisabled'] !== false) {
            $this->groupDisabled = $attr['groupDisabled'];
            if (!is_array($this->groupDisabled)) {
                $this->groupDisabled = [$this->groupDisabled];
            }
            unset($attr['groupDisabled']);
        }
        parent::inspectionAttributes($attr);
    }

    /**
     * @param $str
     * @param array|bool $disabled
     * @return array
     */
    protected function isDisabled($str, array $disabled = [])
    {
        if (empty($disabled)) {
            $disabled = $this->optionDisabled;
        }
        $arr = [];
        if (in_array($str, $disabled, true)) {
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
        if (in_array($str, $this->selected, true)) {
            $arr['selected'] = 'selected';
        }
        return $arr;
    }
}