<?php

namespace LaraForm\Elements\Components;

class NumberWidget extends BaseInputWidget
{
    /**
     * @param $option
     * @return string
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function render($option)
    {
        return parent::render($option);
    }


    /**
     * @param $attr
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function inspectionAttributes(&$attr)
    {
        $this->otherHtmlAttributes['type'] = 'number';
        $attr['type'] = 'number';
        parent::inspectionAttributes($attr);
    }
}