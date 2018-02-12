<?php

namespace LaraForm\Elements\Widgets;

use LaraForm\Elements\Widget;

/**
 * Processes and creates form tag and hidden fields for
 * method, csrf and lara_form validation
 *
 * Class FormWidget
 * @package LaraForm\Elements\Widgets
 */
class FormWidget extends Widget
{
    /**
     * Returns the finished html form opened or closed view
     *
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
     * @return mixed|void
     */
    public function checkAttributes(&$attr)
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
        $this->generateClass($attr);
        $this->parentCheckAttributes($attr);
    }

    /**
     * Create a opening the form tag by processing and modifying the fields overeating its attributes
     *
     * @param $options
     * @return mixed|string
     */
    private function start($options)
    {
        $method = $options['_form_method'];
        $action = $options['_form_action'];
        $token = $options['_form_token'];
        $htmlAttributes['action'] = $action;
        $htmlAttributes['method'] = ($method == 'get') ? 'GET' : 'POST';
        $this->checkAttributes($options);
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
            $form .= $this->setHidden($this->config['token_name'], $token);
        }

        return $form;
    }

    /**
     * Create a closing the form tag
     *
     * @return mixed
     */
    private function end()
    {
        $template = $this->getTemplate('formEnd');
        return $this->formatTemplate($template, false);
    }
}