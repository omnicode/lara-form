<?php

namespace LaraForm;

use Aws\Middleware;
use Illuminate\Support\Facades\Config;
use LaraForm\Elements\Widget;

class MakeForm extends Widget
{
    protected $routes = [];

    /**
     * @param $model
     * @param $options
     * @throws \RuntimeException
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function open($model, $options)
    {
        if (isset($options['method']) && in_array(strtolower($options['method']), $this->_requestMethods)) {
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
        $htmlAttributes['accept-charset'] = Config::get('lara_form.charset', 'utf-8');

        if (!empty($options['file'])) {
            $htmlAttributes['enctype'] = 'multipart/form-data';
            unset($options['file']);
        }

        $htmlAttributes += $options;
        $template = $this->_defaultConfig['templates']['formStart'];
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

        return dump($form);

    }

    /**
     * @return string
     */
    public function close()
    {
        $template = $this->_defaultConfig['templates']['formEnd'];
        return $this->formatTemplate($template, false);
    }

    /**
     * @param $method
     * @param $arrgs
     * @return mixed
     */
    public function __call($method, $arrgs)
    {
       return $this->createObject($method, $arrgs);
    }
}