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
        unset($attr['form_token']);
    }

    /**
     * @param $options
     * @return array
     */
    private function start($options)
    {
        $method = $this->getMethod($options);
        $action = $this->getAction($options);
        $token = $options['form_token'];
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

        return ['html' => $form, 'action' => $action , 'method' => $method];
    }

    /**
     * @return string
     */
    private function end()
    {
        $template = $this->getTemplate('formEnd');
        return $this->formatTemplate($template, false);
    }


    /**
     * @param array $options
     * @return array|mixed|string
     */
    private function getAction(&$options = [])
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
            unset($options['route']);
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
     * @param $options
     * @return null|string
     * @internal param $model
     * @internal param bool $unSet
     */
    private function getMethod(&$options)
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
     * @return array
     */
    private function getRoutes()
    {
        if (empty($this->routes)) {
            collect(Route::getRoutes())->map(function ($route) {
                $this->routes[$route->getName()] = class_basename($route->getActionName());
            });
        }

        return $this->routes;
    }

    /**
     * @return mixed
     */
    private function getCurrentRoute()
    {
        return Route::getCurrentRoute();
    }
}