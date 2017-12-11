<?php

namespace LaraForm\Elements\Components;

class EmailWidget extends BaseInputWidget
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
        $this->otherHtmlAttributes['type'] = 'email';
        $attr['type'] = 'email';
        parent::inspectionAttributes($attr);
    }
}