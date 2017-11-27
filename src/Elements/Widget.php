<?php

namespace LaraForm\Elements;

use Cake\Utility\Inflector;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;

abstract class Widget
{
    protected $_requestMethods = ['get', 'post', 'put', 'patch', 'delete'];
    protected $_defaultConfig = [
        'templates' => [
            // Used for button elements in button().
            'button' => '<button{{attrs}}>{{text}}</button>',
            // Used for checkboxes in checkbox() and multiCheckbox().
            'checkbox' => '<input type="checkbox" {{attrs}}/>',
            // Input group wrapper for checkboxes created via control().
            'checkboxFormGroup' => '{{label}}',
            // Wrapper container for checkboxes.
            'checkboxWrapper' => '<div class="checkbox">{{label}}</div>',
            // Widget ordering for date/time/datetime pickers.
            'dateWidget' => '{{year}}{{month}}{{day}}{{hour}}{{minute}}{{second}}{{meridian}}',
            // Error message wrapper elements.
            'error' => '<div class="error-message">{{content}}</div>',
            // Container for error items.
            'errorList' => '<ul>{{content}}</ul>',
            // Error item wrapper.
            'errorItem' => '<li>{{text}}</li>',
            // File input used by file().
            'file' => '<input type="file" name="{{name}}" {{attrs}}/>',
            // Fieldset element used by allControls().
            'fieldset' => '<fieldset {{attrs}}>{{content}}</fieldset>',
            // Open tag used by create().
            'formStart' => '<form {{attrs}}>',
            // Close tag used by end().
            'formEnd' => '</form>',
            // General grouping container for control(). Defines input/label ordering.
            'formGroup' => '{{label}}{{input}}',
            // Wrapper content used to hide other content.
            'hiddenBlock' => '<div style="display:none;">{{content}}</div>',
            // Generic input element.
            'input' => '<input {{attrs}}/>',
            // Submit input element.
            'inputSubmit' => '<input type="{{type}}" {{attrs}}/>',
            // Container element used by control().
            'inputContainer' => '<div class="input {{type}}{{required}}">{{content}}</div>',
            // Container element used by control() when a field has an error.
            'inputContainerError' => '<div class="input {{type}}{{required}} error">{{content}}{{error}}</div>',
            // Label element when inputs are not nested inside the label.
            'label' => '<label {{attrs}}>{{text}}</label>',
            // Label element used for radio and multi-checkbox inputs.
            'nestingLabel' => '{{hidden}}<label {{attrs}}>{{input}}{{text}}</label>',
            // Legends created by allControls()
            'legend' => '<legend>{{text}}</legend>',
            // Multi-Checkbox input set title element.
            'multicheckboxTitle' => '<legend>{{text}}</legend>',
            // Multi-Checkbox wrapping container.
            'multicheckboxWrapper' => '<fieldset {{attrs}}>{{content}}</fieldset>',
            // Option element used in select pickers.
            'option' => '<option value="{{value}}" {{attrs}}>{{text}}</option>',
            // Option group element used in select pickers.
            'optgroup' => '<optgroup label="{{label}}" {{attrs}}>{{content}}</optgroup>',
            // Select element,
            'select' => '<select name="{{name}}" {{attrs}}>{{content}}</select>',
            // Multi-select element,
            'selectMultiple' => '<select name="{{name}}[]" multiple="multiple" {{attrs}}>{{content}}</select>',
            // Radio input element,
            'radio' => '<input type="radio" {{attrs}}/>',
            // Wrapping container for radio input/label,
            'radioWrapper' => '{{label}}',
            // Textarea input element,
            'textarea' => '<textarea name="{{name}}" {{attrs}}>{{value}}</textarea>',
            // Container for submit buttons.
            'submitContainer' => '<div class="submit">{{content}}</div>',
        ]
    ];

    /**
     * @param $template
     * @param $attributes
     * @return string
     */
    public function formatTemplate($template, $attributes)
    {
        if (empty($attributes)) {
            return $template;
        }

        $from = [];
        $to = [];
        foreach ($attributes as $index => $attribute) {
            $from[] = '{{' . $index . '}}';
            $to[] = $attribute;
        }
        $fild = str_ireplace($from, $to, $template);
        return $fild;

    }

    /**
     * @param $attributes
     * @return string
     */
    public function formatAttributes($attributes)
    {
        $attr = '';
        foreach ($attributes as $index => $attribute) {
            if (empty($attributes)) {
                continue;
            }
            $attr .= $index . "='" . $attribute . "' ";
        }
        return $attr;
    }

    /**
     * @param array $options
     * @return array|mixed|string
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function action(&$options = [])
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
                $action = [$action];
            }
        } else {
            return request()->url();
        }

        //if action is url
        if (filter_var($action, FILTER_VALIDATE_URL)) {
            return $action;
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
     * @param $name
     * @return mixed
     */
    protected function getLableName($name)
    {
        return str_ireplace(']', '', array_last(explode('[', $name)));
    }

    /**
     * @return array
     */
    protected function getRoutes()
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
    protected function getCurrentRoute()
    {
        return Route::getCurrentRoute();
    }

    /**
     * @param $name
     * @return mixed
     */
    protected function getClassName($name)
    {
        return array_last(explode('\\', $name));
    }

    /**
     * @param $method
     * @param $arrgs
     * @return mixed
     */
    protected function createObject($method,$arrgs)
    {
        $modelName = ucfirst($method);
        $className = 'LaraForm\Elements\Components\\' . $modelName . 'Widget';
        return new $className(...$arrgs);

    }
}
