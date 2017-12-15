<?php

namespace LaraForm\Elements\Components;

use LaraForm\Elements\Widget;

class FormWidget extends Widget
{
    /**
     * @return array|string|void
     */
    public function render()
    {
        if ($this->name === 'start') {
            return $this->start($this->attr);
        }
        if ($this->name === 'end') {
            return $this->end();
        }

    }

    /**
     * @param $attr
     */
    public function inspectionAttributes(&$attr)
    {
        if (!empty($attr['file'])) {
            $attr['enctype'] = 'multipart/form-data';
            unset($attr['file']);
        }
        if (!empty($attr['_unlockFields'])) {
            unset($attr['_unlockFields']);
        }
        if (empty($attr['accept-charset'])) {
            $attr['accept-charset'] = $this->config['charset'];
        }
        unset($attr['_form_token']);
        unset($attr['_form_action']);
        unset($attr['_form_method']);
    }

    /**
     * @param $options
     * @return array
     */
    private function start($options)
    {
        $method = $options['_form_method'];
        $action = $options['_form_action'];
        $token = $options['_form_token'];
        $htmlAttributes['action'] = $action;
        $htmlAttributes['method'] = ($method == 'get') ? 'GET' : 'POST';
        $this->inspectionAttributes($options);
        $htmlAttributes += $options;
        $template = $this->getTemplate('formStart');

        $rep = [
            'attrs' => $this->formatAttributes($htmlAttributes)
        ];

        $form = $this->formatTemplate($template, $rep);

        if ($method !== 'get') {
            $form .= csrf_field();
            if ($method !== 'post') {
                $form .= method_field(strtoupper($method));
            }
            $form .= $this->setHidden($this->config['label']['form_protection'], $token);
        }

        return $form;
    }

    /**
     * @return string
     */
    private function end()
    {
        $template = $this->getTemplate('formEnd');
        return $this->formatTemplate($template, false);
    }
}