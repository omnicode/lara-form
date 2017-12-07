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
     */
    public function render($option)
    {

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
    public function toHtml($name, $attr, $cTemplate = false)
    {
        if (!$cTemplate) {
            if (isset($attr['type']) && in_array($attr['type'], $this->types)) {
                $template = $this->getTemplate($attr['type']);
            }else{
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
        $this->generateId($attr);

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
        $notD = ['hidden', 'submit', 'reset', 'button', 'radio', 'checkbox', 'label'];
        if (!in_array($this->htmlAttributes['type'], $notD) && !$cTemplate) {
            $attr += $this->getValue($this->name);
        }
        $notLabel = ['hidden', 'submit', 'reset', 'button'];
        if (!in_array($this->htmlAttributes['type'], $notLabel)) {
            if (!empty($attr['label'])) {
                $this->renderLabel($attr['label'], $attr);
                $this->unlokAttributes['label'] = $attr['label'];
            } elseif(!isset($attr['label'])) {
                $this->renderLabel($this->name, $attr);
            }
        }
        if (!isset($attr['class']) && $this->htmlAttributes['type'] !== 'hidden') {
            $this->htmlClass[] = $this->config['css']['inputClass'];
        } elseif (isset($attr['class']) && $attr['class'] == false) {
            $this->unlokAttributes['class'] = $attr['class'];
        }
        $this->otherHtmlAttributes = $attr;
        parent::inspectionAttributes($attr);
    }

}