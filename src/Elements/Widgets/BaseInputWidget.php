<?php

namespace LaraForm\Elements\Widgets;

use LaraForm\Elements\Widget;

/**
 * Processes and creates input tags
 *
 * Class BaseInputWidget
 * @package LaraForm\Elements\Widgets
 */
class BaseInputWidget extends Widget
{
    /**
     * Returns the finished html view
     *
     * @return string
     */
    public function render()
    {
        $this->checkAttributes($this->attr);
        return $this->formatInputField($this->name, $this->attr);
    }

    /**
     * @param $attr
     * @return mixed|void
     */
    public function checkAttributes(&$attr)
    {
        parent::checkAttributes($attr);
    }

    /**
     * Formats input fields according to a given template or by default
     *
     * @param $name
     * @param $attr
     * @param bool $cTemplate
     * @return mixed|string
     */
    protected function formatInputField($name, $attr, $cTemplate = false)
    {
        if ($cTemplate) {
            $template = $cTemplate;
        } else {
            $template = $this->getTemplate('input');
        }

        $this->generalcheckAttributes($attr, $cTemplate);
        $this->setHtmlAttributes('name', $name);
        $this->setHtmlAttributes('attrs', $this->formatAttributes($attr));
        $this->html = $this->formatTemplate($template, $this->getHtmlAttributes());
        return $this->completeTemplate();
    }

    /**
     * Formats the fields inside the label field
     *
     * @param $template
     * @param $attr
     * @param array $labelAttrs
     * @return mixed|string
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
     * Checks and modifies the attributes that were passed in the field
     *
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