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
        if (!empty($options['file'])) {
            $options['enctype'] = 'multipart/form-data';
            unset($options['file']);
        }
    }

    /**
     * @param $options
     * @return string
     * @throws \RuntimeException
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function start($options)
    {
        $method = $this->getMethod($options);
        $action = $this->getAction($options);
        $token = $options['form_token'];
        unset($options['form_token']);
        $htmlAttributes['action'] = $action;
        $htmlAttributes['method'] = ($method == 'get') ? 'GET' : 'POST';
        $htmlAttributes['accept-charset'] = $this->config['charset'];
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
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function end()
    {
        $template = $this->getTemplate('formEnd');
        return $this->formatTemplate($template, false);
    }


    /**
     * @param $options
     * @return null|string
     * @internal param $model
     * @internal param bool $unSet
     */
    protected function getMethod(&$options)
    {
        $method = 'post';
        if (isset($options['method'])) {
            if (in_array($options['method'], ['get', 'post', 'put', 'patch', 'delete'])) {
                $method = $options['method'];
            }
            unset($options['method']);
        } elseif (!empty($this->bound)) {
            $method = 'put';
        }

        return $method;
    }


    /**
     * @param array $options
     * @return array|mixed|string
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function getAction(&$options = [])
    {
        if (!empty($options['route'])) {
            $route = $options['route'];
            unset($options['route']);
            if (!is_array($route)) {
                $route = [$route];
            }
            return route(...$route);
        }

        if (!empty($options['url'])) {
            $url = $options['url'];
            unset($options['url']);
            return $url;
        }

        if (isset($options['action'])) {
            $action = $options['action'];
            if (!is_array($action)) {
                //if action is url
                if (filter_var($action, FILTER_VALIDATE_URL)) {
                    return $action;
                }
                $action = [$action];
            }
        } else {
            return request()->url();
        }

        $allRoutes = $this->getRoutes();
        $methodName = array_shift($action);

        if (!strpos('@', $methodName)) {
            $curr = $this->getCurrentRoute();
            $controller = $this->getClassName(get_class($curr->getController()));
            $methodName = $controller . '@' . $methodName;
        }

        $routeName = array_search($methodName, $allRoutes);

        if (empty($routeName)) {
            abort(405, '[' . $methodName . '] method not allowed!');
        }

        return route($routeName, $action);
    }

    /**
     * @return array
     */
    public function getRoutes()
    {
        if (empty($this->routes)) {
            collect(Route::getRoutes())->map(function ($route) {
                $this->routes[$route->getName()] = $this->getClassName($route->getActionName());
            });
        }

        return $this->routes;
    }

    /**
     * @return mixed
     */
    public function getCurrentRoute()
    {
        return Route::getCurrentRoute();
    }
}