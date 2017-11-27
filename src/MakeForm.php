<?php

namespace LaraForm;

use Aws\Middleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use LaraForm\Elements\Components\Inputs\Input;
use LaraForm\Elements\Components\Inputs\RadioButton;
use LaraForm\Elements\Components\Inputs\Hidden;
use LaraForm\Elements\Components\Inputs\Password;
use LaraForm\Elements\Components\Inputs\Submit;
use LaraForm\Elements\Components\CheckBox;
use LaraForm\Elements\Components\Label;
use LaraForm\Elements\Components\Select;
use LaraForm\Elements\Components\Textarea;

class MakeForm
{
    protected $routes = [];
    protected $_complect = [];
    protected $_requestMethods = ['get', 'post', 'put', 'patch', 'delete'];
    protected $_defaultConfig = [
        'templates' => [
            // Used for button elements in button().
            'button' => '<button{{attrs}}>{{text}}</button>',
            // Used for checkboxes in checkbox() and multiCheckbox().
            'checkbox' => '<input type="checkbox" name="{{name}}" value="{{value}}"{{attrs}}>',
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
            'file' => '<input type="file" name="{{name}}"{{attrs}}>',
            // Fieldset element used by allControls().
            'fieldset' => '<fieldset{{attrs}}>{{content}}</fieldset>',
            // Open tag used by create().
            'formStart' => '<form{{attrs}}>',
            // Close tag used by end().
            'formEnd' => '</form>',
            // General grouping container for control(). Defines input/label ordering.
            'formGroup' => '{{label}}{{input}}',
            // Wrapper content used to hide other content.
            'hiddenBlock' => '<div style="display:none;">{{content}}</div>',
            // Generic input element.
            'input' => '<input type="{{type}}" name="{{name}}"{{attrs}}/>',
            // Submit input element.
            'inputSubmit' => '<input type="{{type}}"{{attrs}}/>',
            // Container element used by control().
            'inputContainer' => '<div class="input {{type}}{{required}}">{{content}}</div>',
            // Container element used by control() when a field has an error.
            'inputContainerError' => '<div class="input {{type}}{{required}} error">{{content}}{{error}}</div>',
            // Label element when inputs are not nested inside the label.
            'label' => '<label{{attrs}}>{{text}}</label>',
            // Label element used for radio and multi-checkbox inputs.
            'nestingLabel' => '{{hidden}}<label{{attrs}}>{{input}}{{text}}</label>',
            // Legends created by allControls()
            'legend' => '<legend>{{text}}</legend>',
            // Multi-Checkbox input set title element.
            'multicheckboxTitle' => '<legend>{{text}}</legend>',
            // Multi-Checkbox wrapping container.
            'multicheckboxWrapper' => '<fieldset{{attrs}}>{{content}}</fieldset>',
            // Option element used in select pickers.
            'option' => '<option value="{{value}}"{{attrs}}>{{text}}</option>',
            // Option group element used in select pickers.
            'optgroup' => '<optgroup label="{{label}}"{{attrs}}>{{content}}</optgroup>',
            // Select element,
            'select' => '<select name="{{name}}"{{attrs}}>{{content}}</select>',
            // Multi-select element,
            'selectMultiple' => '<select name="{{name}}[]" multiple="multiple"{{attrs}}>{{content}}</select>',
            // Radio input element,
            'radio' => '<input type="radio" name="{{name}}" value="{{value}}"{{attrs}}>',
            // Wrapping container for radio input/label,
            'radioWrapper' => '{{label}}',
            // Textarea input element,
            'textarea' => '<textarea name="{{name}}"{{attrs}}>{{value}}</textarea>',
            // Container for submit buttons.
            'submitContainer' => '<div class="submit">{{content}}</div>',
        ]
    ];

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

        // $options['params'] => []
        $action = $this->action($options);
        $htmlAttributes['action'] = $action;
        $htmlAttributes['method'] = 'POST';
        $htmlAttributes['accept-charset'] = 'utf-8';
        if (!empty($options['file'])) {
            $htmlAttributes['enctype'] = 'multipart/form-data';
        }
        $htmlAttributes += $options;
        // dd($htmlAttributes);
        $template = $this->_defaultConfig['templates']['formStart'];
        $attrs = $this->formatAttributes($htmlAttributes);
        $rep = ['attrs' => $attrs];
        $form = $this->formatTemplate($template, $rep);
        return $form;

    }

    /**
     * @param $template
     * @param $attributes
     * @return string
     */
    public function formatTemplate($template, array $attributes)
    {
        $fild = '';
        foreach ($attributes as $key => $attribute) {
            $fild .= str_ireplace('{{' . $key . '}}', ' ' . $attribute, $template);
        }

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

    protected function action($options = [])
    {
        if (!empty($options['params']) && !is_array($options['params'])) {
            $options['params'] = [
                $options['params']
            ];
        }else{
            $options['params'] = [];
        }


        if (isset($options['action'])) {
            $action = $options['action'];
        } elseif (isset($options['url'])) {
            $action = $options['url'];
        } else {
            return request()->url();
        }

        if (filter_var($action, FILTER_VALIDATE_URL)) {
            return $action;
        }

        $routes = $this->getRoutes();
        if (!is_array($action) && isset($routes[$action])) {
            return route($action,$options['params']);
        }

    }

    protected function getRoutes()
    {
        if (empty($this->routes)) {
            collect(Route::getRoutes())->map(function ($route) {
                $method = array_last(explode('\\', $route->getActionName()));
                $this->routes[$route->getName()] = $method;
            });
        }

        return dd($this->routes);
    }
}