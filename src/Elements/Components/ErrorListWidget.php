<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 01-Dec-17
 * Time: 06:07 PM
 */

namespace LaraForm\Elements\Components;


use LaraForm\Elements\Widget;

class ErrorListWidget extends Widget
{
    /**
     * @return string
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
    public function inspectionAttributes(&$attr)
    {

    }
}