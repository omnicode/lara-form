<?php

namespace LaraForm\Elements\Components;

use LaraForm\Elements\Widget;

class BaseInputWidget extends Widget
{
    /**
     * @var array
     */
    protected $types = [
        'checkbox',
        'radio',
        'submit',
        'textarea',
        'file'
    ];

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
        return $this->formatInputField($this->name, $attr);
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

    /**
     * @param $name
     * @param $attr
     * @param bool $cTemplate
     * @return string
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function formatInputField($name, $attr, $cTemplate = false)
    {
        if (!$cTemplate) {
            if (isset($attr['type']) && in_array($attr['type'], $this->types)) {
                $template = $this->getTemplate($attr['type']);
            } else {
                $template = $this->getTemplate('input');
            }
        } else {
            $template = $cTemplate;
        }

        $this->generalInspectionAttributes($attr, $cTemplate);
        $this->htmlAttributes['name'] = $name;
        $this->htmlAttributes['attrs'] = $this->formatAttributes($attr);
        $this->html = $this->formatTemplate($template, $this->htmlAttributes);
        return $this->completeTemplate();
    }

    /**
     * @param $attr
     * @param $cTemplate
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function generalInspectionAttributes(&$attr, $cTemplate)
    {
        if (isset($attr['type'])) {
            $this->htmlAttributes['type'] = $attr['type'];
            $this->unlokAttributes['type'] = $attr['type'];
        } else {
            $this->htmlAttributes['type'] = 'text';
        }
        $this->htmlAttributes['value'] = '';
        if (!empty($attr['value']) && $cTemplate) {
            $this->htmlAttributes['value'] = $attr['value'];
            $this->unlokAttributes['value'] = $attr['value'];
        }
        $notId = ['hidden', 'submit', 'reset', 'button', 'radio', 'checkbox', 'label'];
        if (!in_array($this->htmlAttributes['type'], $notId) && !$cTemplate) {
            $attr += $this->getValue($this->name);
        }
        if (!in_array($this->htmlAttributes['type'], ['hidden', 'submit', 'reset', 'button'])) {
            $this->generateLabel($attr);
        }
        if ($this->htmlAttributes['type'] !== 'hidden') {
            $this->generateClass($attr, $this->config['css']['inputClass']);
        }
        $this->otherHtmlAttributes = $attr;
        parent::inspectionAttributes($attr);
    }

}