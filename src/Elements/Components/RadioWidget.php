<?php

namespace LaraForm\Elements\Components;

class RadioWidget extends BaseInputWidget
{
    /**
     * @param $option
     * @return string
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function render($option)
    {
        $template = $this->config['templates']['radio'];
        $this->containerTemplate = $this->getTemplate('radioContainer');
        $labelTemplate = $this->getTemplate('nestingLabel');

        $this->name = array_shift($option);
        $attr = !empty($option[0]) ? $option[0] : [];
        $this->inspectionAttributes($attr);
        $this->toHtml($this->name, $attr, $template);
        $labelAttr = [
            'hidden' => $this->hidden,
            'content' => $this->html,
            'text' => isset($attr['label']) ? $attr['label'] : $this->getLabelName($this->name),
            'attrs' => ''
        ];
        $this->html = $this->formatTemplate($labelTemplate, $labelAttr);
        $this->otherHtmlAttributes['type'] = 'radio';
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
        $attr['value'] = isset($attr['value']) ? $attr['value'] : $this->config['default_value']['radio'];
        $this->htmlClass = isset($attr['class']) ? $attr['class'] : $this->config['css']['radioClass'];
        $attr['class'] = $this->formatClass();
        if (isset($attr['type'])) {
            unset($attr['type']);
        }
        if (isset($attr['checked'])) {
            $attr['checked'] = 'checked';
        }
       /* if (isset($attr['hidden']) && $attr['hidden'] == false) {
            unset($attr['hidden']);
        } else {
            $this->hidden = $this->setHidden($this->name, '');
        }*/
        if (empty($attr['id'])) {
            $attr['id'] = $this->name . '-' . $attr['value'];
        }
        parent::inspectionAttributes($attr);
    }
}