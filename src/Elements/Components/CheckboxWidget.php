<?php

namespace LaraForm\Elements\Components;

use function GuzzleHttp\is_host_in_noproxy;

class CheckboxWidget extends BaseInputWidget
{

    private $isOutputedMultiHidden = false;

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
        return $this->renderCheckbox($attr, $template);
    }

    /**
     * @param $attr
     * @param $template
     * @return string
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    private function renderCheckbox($attr, $template)
    {
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
        $this->otherHtmlAttributes['type'] = 'checkbox';
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
        $this->htmlClass = isset($attr['class']) ? $attr['class'] : $this->config['css']['checkboxClass'];

        if (empty($attr['value'])) {
            $val = $this->getValue($this->name)['value'];
            if (!is_array($val)) {
                $val = [$val];
            }
            if (in_array($attr['value'], $val)) {
                $attr['checked'] = true;
            }
        }
        $attr['class'] = $this->formatClass();
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
            if (!$this->isOutputedMultiHidden) {
                if (ends_with($this->name, '[]')) {
                    $hiddenName = str_ireplace('[]', '', $this->name);
                } else {
                    $hiddenName = $this->name;
                }
                if (!in_array($hiddenName, array_keys($this->fixedField))) {
                    $this->hidden = $this->setHidden($hiddenName, $this->config['default_value']['hidden']);
                }
            }
        }
        parent::inspectionAttributes($attr);
    }

}