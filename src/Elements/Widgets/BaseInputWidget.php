<?php

namespace LaraForm\Elements\Widgets;

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
     * @return string
     */
    public function render()
    {
        $this->checkAttributes($this->attr);
        return $this->formatInputField($this->name, $this->attr);
    }

    /**
     * @param $attr
     */
    public function checkAttributes(&$attr)
    {
        parent::checkAttributes($attr);
    }

    /**
     * @param $name
     * @param $attr
     * @param bool $cTemplate
     * @return string
     */
    protected function formatInputField($name, $attr, $cTemplate = false)
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

        $this->generalcheckAttributes($attr, $cTemplate);
        $this->setHtmlAttributes('name', $name);
        $this->setHtmlAttributes('attrs', $this->formatAttributes($attr));
        $this->html = $this->formatTemplate($template, $this->getHtmlAttributes());
        return $this->completeTemplate();
    }

    /**
     * @param $template
     * @param $attr
     * @param array $labelAttrs
     * @return string
     */
    protected function formatNestingLabel($template, $attr, $labelAttrs = [])
    {
        $labelTemplate = $this->getTemplate('nestingLabel');
        $this->formatInputField($this->name, $attr, $template);

        if (!empty($attr['type'])) {
            $this->setOtherHtmlAttributes('type', $attr['type']);
            unset($attr['type']);
        }

        $templateAttr = [
            'hidden' => $this->hidden,
            'content' => $this->html,
            'text' => !empty($attr['label']) ? $attr['label'] : $this->getLabelName($this->name),
            'attrs' => $this->formatAttributes($labelAttrs)
        ];

        $this->html = $this->formatTemplate($labelTemplate, $templateAttr);
        return $this->completeTemplate();
    }

    /**
     * @param $attr
     * @param $cTemplate
     */
    private function generalcheckAttributes(&$attr, $cTemplate)
    {
        if (isset($attr['type'])) {
            $this->setHtmlAttributes('type',$attr['type']);
            unset($attr['type']);
        } else {
            $this->setHtmlAttributes('type','text');
        }

        $this->setHtmlAttributes('value','');
        if (!empty($attr['value']) && $cTemplate) {
            $this->setHtmlAttributes('value',$attr['value']);
            unset($attr['value']);
        }

        $notId = ['hidden', 'submit', 'reset', 'button', 'radio', 'checkbox', 'label'];

        if (!in_array($this->getHtmlAttributes('type'), $notId) && !$cTemplate) {
            $attr += $this->getValue($this->name);
        }

        $this->generateId($attr);
        if (!in_array($this->getHtmlAttributes('type'), ['hidden', 'submit', 'reset', 'button'])) {
            $this->generateLabel($attr);
        }

        if ($this->getHtmlAttributes('type') !== 'hidden') {
            $this->generateClass($attr, $this->config['css']['class']['inputClass']);
        }

        $this->assignOtherhtmlAtrributes($attr);
        parent::checkAttributes($attr);
    }

}