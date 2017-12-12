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
        $this->parseParams($option);
        $template = $this->getTemplate('textarea');
        $this->inspectionAttributes($this->attr);
        $this->containerParams['inline']['type'] = !empty($this->containerParams['inline']['type']) ? $this->containerParams['inline']['type'] :'textarea';
        return $this->html = $this->formatInputField($this->name, $this->attr, $template);
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