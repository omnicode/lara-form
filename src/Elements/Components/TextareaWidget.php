<?php

namespace LaraForm\Elements\Components;

use LaraForm\Elements\Widget;

class TextareaWidget extends BaseInputWidget
{
    /**
     * @param $option
     * @return string
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function render($option)
    {
        $template = $this->getTemplate('textarea');
        $this->name = array_shift($option);
        $attr = !empty($option[0]) ? $option[0] : [];
        $this->inspectionAttributes($attr);
        $this->containerParams['inline']['type'] = !empty($this->containerParams['inline']['type']) ? $this->containerParams['inline']['type'] :'textarea';
        return $this->html = $this->formatInputField($this->name, $attr, $template);
    }

    /**
     * @param $attr
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function inspectionAttributes(&$attr)
    {
        $this->generateClass($attr,$this->config['css']['textareaClass']);
        if (isset($attr['type'])) {
            unset($attr['type']);
        }
        parent::inspectionAttributes($attr);
    }
}