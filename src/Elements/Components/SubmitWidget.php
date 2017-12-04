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
        $attr['class'] = isset($attr['class']) ? $attr['class'] . ' btn' : $this->config['css']['submitClass'];
        if (!empty($options['btn'])) {
            if ($attr['btn'] === true) {
                $attr['btn'] = $this->config['label']['submit_btn'];
            }
            $attr['class'] .= ' btn btn-' . $attr['btn'];
            unset($attr['btn']);
        }
        $iconTemplate = $this->getTemplate('icon');
        if (!empty($attr['icon'])) {
            $this->icon = $this->formatTemplate($iconTemplate, ['name' => $attr['icon']]);
            unset($attr['icon']);
        }

        if (!empty($attr['position']) && $attr['position'] == 'right') {
            $attr['class'] .= ' icon-right';
        }
        if (!isset($attr['type'])) {
            $attr['type'] = 'submit';
        }
    }
}