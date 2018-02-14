<?php


namespace LaraForm\Traits;

use Route;

trait FormControl
{
    /**
     * Keepes here all routes
     *
     * @var array
     */
    private $routes = [];

    /**
     * From the form parameters evaluates the action and returns it
     *
     * @param array $options
     * @return mixed|string
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getAction(&$options = [])
    {
        if (!empty($options['route'])) {
            $route = $options['route'];
            unset($options['route']);

            if (!is_array($route)) {
                $route = [$route];
            }

            return route(...$route);
        }

        if (!empty($options['url']) && is_string($options['url'])) {
            $url = $options['url'];
            unset($options['url']);
            return $url;
        }

        if (isset($options['action'])) {
            $action = $options['action'];

            if (!is_array($action)) {
                //if action is url
                // deprecated, will be removed in next version
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
            $currentRoute = $this->getCurrentRoute();
            $currentController = get_class($currentRoute->getController());
            $methodName = $currentController . '@' . $methodName;
        }

        foreach ($allRoutes as $routeName => $method) {
             if (ends_with($methodName,$method)) {
                 $route = $routeName;
                 break;
             }
        }

        if (empty($route)) {
            abort(405, '[' . $methodName . '] method not allowed!');
        }

        return route($route, $action);
    }


    /**
     * Returns a query method, by default it is a post, but if you specify model is a put
     * It is possible and in the manual to give a method
     *
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
        } elseif (!empty($this->model)) {
            $method = 'put';
        }

        return $method;
    }

    /**
     * Returns an array of routes where the keys are the
     * names of the routes and the value is controller and method
     *
     * @return array
     */
    private function getRoutes()
    {
        if (empty($this->routes)) {
            collect(Route::getRoutes())->map(function ($route) {
                $this->routes[$route->getName()] = $route->getActionName();
            });
        }

        return $this->routes;
    }

    /**
     * Return current route
     *
     * @return mixed
     */
    private function getCurrentRoute()
    {
        return Route::getCurrentRoute();
    }
}