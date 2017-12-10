<?php

namespace LaraForm\Elements\Components;

class HiddenWidget extends BaseInputWidget
{

    /**
     * @param $option
     * @return string
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function render($option)
    {
        $this->name = array_shift($option);
        $attr = !empty($option[0]) ? $option[0] : [];
        $this->inspectionAttributes($attr);
        return $this->toHtml($this->name, $attr);
    }

    /**
     * @param $attr
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function inspectionAttributes(&$attr)
    {
        if (isset($attr['type'])) {
            unset($attr['type']);
        }
        $this->htmlAttributes['type'] = 'hidden';
        parent::inspectionAttributes($attr);
    }
}