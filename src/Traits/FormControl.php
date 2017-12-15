<?php


namespace LaraForm\Traits;

use Route;

trait FormControl
{
    protected $routes = [];
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
        } elseif (!empty($this->model)) {
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