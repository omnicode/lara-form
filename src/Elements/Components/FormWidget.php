<?php

namespace LaraForm\Elements\Components;

use LaraForm\Elements\Widget;

class FormWidget extends Widget
{
    public function render($option)
    {

        $methodName = array_shift($option);
        $attr = !empty($option[0]) ? $option[0] : [];
        if ($methodName === 'start') {
            return $this->start($attr);
        }
        if ($methodName === 'end') {
            return $this->end();
        }

    }

    public function inspectionAttributes(&$attr)
    {

    }

    protected function start($options)
    {
        if (isset($options['method']) && in_array(strtolower($options['method']), ['get', 'post', 'put', 'patch', 'delete'])) {
            $method = $options['method'];
            unset($options['method']);
        } elseif (!empty($model)) {
            $method = 'put';
        } else {
            $method = 'post';
        }

        if (isset($options['_unlockFields'])) {
            unset($options['_unlockFields']);
        }

        $action = $this->action($options);
        $htmlAttributes['action'] = $action;
        $htmlAttributes['method'] = ($method == 'get') ? 'GET' : 'POST';
        $htmlAttributes['accept-charset'] = config('lara_form.charset', 'utf-8');

        if (!empty($options['file'])) {
            $htmlAttributes['enctype'] = 'multipart/form-data';
            unset($options['file']);
        }

        $htmlAttributes += $options;
        $template = $this->config['templates']['formStart'];
        $rep = [
            'attrs' => $this->formatAttributes($htmlAttributes)
        ];
        $form = $this->formatTemplate($template, $rep);

        if ($method !== 'get') {
            $form .= csrf_field();
            if ($method !== 'post') {
                $form .= method_field(strtoupper($method));
            }
        }

        return $form;
    }

    protected function end()
    {
        $template = $this->config['templates']['formEnd'];
        return $this->formatTemplate($template, false);
    }
}