<?php

namespace LaraForm\Elements\Components;

class SubmitWidget extends BaseInputWidget
{
    /**
     * @return string
     */
    public function render()
    {
        $this->inspectionAttributes($this->attr);
        $template = $this->getTemplate('button');
        $defaultName = $this->config['label']['submit_name'] ? $this->config['label']['submit_name'] : '';
        $name = !empty($this->name) ? $this->name : $defaultName;
        $btnAttr = [
            'attrs' => $this->formatAttributes($this->attr),
            'text' => $name,
        ];
        $this->html = $this->formatTemplate($template, $btnAttr);
        $this->containerTemplate = $this->config['templates']['submitContainer'];
        return $this->completeTemplate();
    }


    /**
     * @param $attr
     */
    public function inspectionAttributes(&$attr)
    {
        $btn = $this->config['css']['submitClass'];
        $btnColor = $this->config['css']['submitColor'];
        $defaut = $btn.' '.$btnColor;
        if (isset($attr['btn'])) {
            if ($attr['btn'] === true) {
                $attr['btn'] = $btnColor;
            }
            $this->htmlClass[] = $btn . '-' . $attr['btn'];
            unset($attr['btn']);
        }
        $this->generateClass($attr,$defaut);
        if (!empty($attr['type']) && in_array($attr['type'], ['submit', 'button', 'reset'])) {
            $this->otherHtmlAttributes['type'] = $attr['type'];
        } else {
            $this->otherHtmlAttributes['type'] = 'submit';
            $attr['type'] = 'submit';
        }
        parent::inspectionAttributes($attr);
    }

}