<?php

namespace LaraForm\Elements\Components;

class SubmitWidget extends BaseInputWidget
{
    protected $icon = '';

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
        $template = $this->getTemplate('button');

        $name = !empty($this->name) ? $this->name : '';
        $btnAttr = [
            'attrs' => $this->formatAttributes($attr),
            'text' => $this->icon. $name,
        ];
        return $this->html = $this->formatTemplate($template, $btnAttr);
    }


    /**
     * @param $attr
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function inspectionAttributes(&$attr)
    {
        $btn = $this->config['css']['submitClass'];
        $btnColor = $this->config['css']['submitColor'];

        if (isset($attr['class'])) {
            $this->htmlClass[] = $attr['class'];
            unset($attr['class']);
        }else{
            $this->htmlClass[] = $btn;
            $this->htmlClass[] = $btnColor;
        }
        if (isset($attr['btn'])) {
            if ($attr['btn'] === true) {
                $attr['btn'] = $btnColor;
            }
            $this->htmlClass[] = $btn;
            $this->htmlClass[] = $btn.'-'.$attr['btn'];
            unset($attr['btn']);
        }
        $iconTemplate = $this->getTemplate('icon');
        if (!empty($attr['icon'])) {
            $this->icon = $this->formatTemplate($iconTemplate, ['name' => $attr['icon']]);
            unset($attr['icon']);
        }
        if (!isset($attr['type'])) {
            $attr['type'] = 'submit';
        }
    }

}