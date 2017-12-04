<?php

namespace LaraForm\Elements\Components;

class CheckboxWidget extends BaseInputWidget
{
    /**
     * @param $option
     * @return string
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function render($option)
    {
        $template = $this->getTemplate('checkbox');
        $this->name = array_shift($option);
        $attr = !empty($option[0]) ? $option[0] : [];
        if (strpos($this->name, '[]')) {
            $attr['multiple'] = true;
        }
        $this->inspectionAttributes($attr);
        $this->containerTemplate = $this->getTemplate('checkboxContainer');
        $labelTemplate = $this->getTemplate('nestingLabel');
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
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function inspectionAttributes(&$attr)
    {
        $attr['value'] = isset($attr['value']) ? $attr['value'] : $this->config['default_value']['checkbox'];
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
            if (ends_with($this->name,'[]')) {
                $hiddenName = str_ireplace('[]','',$this->name);
            }else{
                $hiddenName = $this->name;
            }
            $this->hidden = $this->setHidden($hiddenName,$this->config['default_value']['hidden']);
        }
    }
}