<?php

namespace LaraForm\Elements\Widgets;

use LaraForm\Elements\Widget;

/**
 * Returns a list of validation errors
 *
 * Class ErrorListWidget
 * @package LaraForm\Elements\Widgets
 */
class ErrorListWidget extends Widget
{
    /**
     * Returns a list of validation errors
     *
     * @return mixed|string|void
     */
    public function render()
    {
        if (empty($this->errors->hasErrors())) {
            return $this->html;
        }

        $ul = $this->config['templates']['errorList'];
        $li = $this->config['templates']['errorItem'];
        $errors = $this->errors->getErrors()->all();
        $list = '';
        foreach ($errors as $name => $error) {
            $list .= $this->formatTemplate($li,['text' => $error]);
        }
        return $this->formatTemplate($ul,['content' => $list]);
    }

    /**
     * @param $attr
     */
    public function checkAttributes(&$attr)
    {

    }
}