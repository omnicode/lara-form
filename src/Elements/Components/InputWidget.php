<?php

namespace LaraForm\Elements\Components;

class InputWidget extends BaseInputWidget
{
    /**
     * @var array
     */
    protected $otherInput = ['checkbox', 'radio', 'submit','file'];

    /**
     * @param $option
     * @return mixed|string
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function render($option)
    {
        $this->name = $option[0];
        $attr = !empty($option[1]) ? $option[1] : [];
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
         parent::inspectionAttributes($attr);
    }
}