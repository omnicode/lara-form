<?php

namespace LaraForm;

use Aws\Middleware;
use LaraForm\Elements\Widget;
use Illuminate\Support\Facades\Config;

class MakeForm
{
    /**
     * @var Widget
     */
    public $widget;

    /**
     * MakeForm constructor.
     * @param Widget $widget
     */
    public function __construct(Widget $widget)
    {
        $this->widget = $widget;
    }

    /**
     * @param $model
     * @param $options
     * @return string
     * @throws \RuntimeException
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function open($model, $options)
    {
        if (isset($options['method']) && in_array(strtolower($options['method']), $this->widget->_requestMethods)) {
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

        $action = $this->widget->action($options);
        $htmlAttributes['action'] = $action;
        $htmlAttributes['method'] = ($method == 'get') ? 'GET' : 'POST';
        $htmlAttributes['accept-charset'] = config('lara_form.charset', 'utf-8');

        if (!empty($options['file'])) {
            $htmlAttributes['enctype'] = 'multipart/form-data';
            unset($options['file']);
        }

        $htmlAttributes += $options;
        $template = $this->widget->config['templates']['formStart'];
        $rep = [
            'attrs' => $this->widget->formatAttributes($htmlAttributes)
        ];
        $form = $this->widget->formatTemplate($template, $rep);

        if ($method !== 'get') {
            $form .= csrf_field();
            if ($method !== 'post') {
                $form .= method_field(strtoupper($method));
            }
        }

        return $form;

    }

    /**
     * @return string
     */
    public function close()
    {
        $template = $this->widget->config['templates']['formEnd'];
        return $this->widget->formatTemplate($template, false);
    }

    /**
     * @param $method
     * @param $arrgs
     * @return mixed
     */
    public function __call($method, $arrgs)
    {
       return $this->widget->createObject($method, $arrgs);
    }
}