<?php

namespace LaraForm\Elements\Components;

class SubmitWidget extends BaseInputWidget
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
        $template = $this->getTemplate('button');
        $name = !empty($this->name) ? $this->name : '';
        $btnAttr = [
            'attrs' => $this->formatAttributes($attr),
            'text' => $name,
        ];
        $this->html = $this->formatTemplate($template, $btnAttr);
        $this->containerTemplate = $this->config['templates']['submitContainer'];
        return $this->completeTemplate();
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
        $this->htmlClass[] = $btn;
        if (isset($attr['class'])) {
            $this->htmlClass[] = $attr['class'];
            unset($attr['class']);
        } else {
            $this->htmlClass[] = $btnColor;
        }
        if (isset($attr['btn'])) {
            if ($attr['btn'] === true) {
                $attr['btn'] = $btnColor;
            }
            $this->htmlClass[] = $btn . '-' . $attr['btn'];
            unset($attr['btn']);
        }
        if (!empty($attr['type']) && in_array($attr['type'], ['submit', 'button', 'reset'])) {
            $this->otherHtmlAttributes['type'] = $attr['type'];
        } else {
            $this->otherHtmlAttributes['type'] = 'submit';
            $attr['type'] = 'submit';
        }
        parent::inspectionAttributes($attr);
    }

}