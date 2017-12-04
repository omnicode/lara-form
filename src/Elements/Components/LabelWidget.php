<?php

namespace LaraForm\Elements\Components;

use LaraForm\Elements\Widget;

class LabelWidget extends Widget
{
    /**
     * @param $option
     * @return string
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function render($option)
    {
        return $this->setLabel($option);
    }

    /**
     * @param $attr
     */
    public function inspectionAttributes(&$attr)
    {

    }
}