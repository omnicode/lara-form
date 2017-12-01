<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 01-Dec-17
 * Time: 06:07 PM
 */

namespace LaraForm\Elements\Components;


use LaraForm\Elements\Widget;

class ErrorsWidget extends Widget
{
    public function render($option)
    {
        if (empty($this->errors->hasErrors())) {
            return $this->html;
        }

        $ul = $this->config['templates']['errorList'];
        $li = $this->config['templates']['errorItem'];
dd($this->errors->getErrors()->all());
        foreach ($this->errors->getErrors() as $name => $error) {
            dump($name.'   '.$error);
        }
    }

    public function inspectionAttributes(&$attr)
    {

    }
}