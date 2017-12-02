<?php

namespace LaraForm\Elements;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use LaraForm\Stores\ErrorStore;
use LaraForm\Stores\OldInputStore;

class Widget implements WidgetInterface
{
    /**
     * @var array
     */
    protected $htmlAttributes = [];

    /**
     * @var string
     */
    public $html = '';

    /**
     * @var string
     */
    public $label = '';

    /**
     * @var
     */
    public $name;

    /**
     * @var array
     */
    public $routes = [];

    /**
     * @var array
     */
    public $_requestMethods = ['get', 'post', 'put', 'patch', 'delete'];

    /**
     * @var mixed
     */
    public $config;

    /**
     * @var
     */
    public $attributes;

    /**
     * @var bool
     */
    public $containerTemplate = false;

    /**
     * @var string
     */
    public $hidden = '';

    /**
     * @var array
     */
    public $errors = [];

    /**
     * @var array
     */
    public $oldInputs = [];

    /**
     * Widget constructor.
     */
    public function __construct()
    {
        dump('widget');
        $this->config = config('lara_form');
        $this->errors = new ErrorStore();
        $this->oldInputs = new OldInputStore();
    }

    /**
     * @param $option
     */
    public function render($option)
    {

    }

    /**
     * @param $attr
     */
    public function inspectionAttributes(&$attr)
    {

    }

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
        return str_ireplace($from, $to, $template);
    }

    /**
     * @param $attributes
     * @return string
     */
    public function formatAttributes($attributes)
    {
        /* $attributes = array_filter($attributes, function ($value) {
             if ($value !== '' && $value !== false) {
                 return $value;
             }
         });*/
        $attr = '';
        foreach ($attributes as $index => $attribute) {
            if (is_string((string)$index)) {
                $attr .= $index . '="' . $attribute . '"';
            } else {
                $attr .= $attribute . ' ';
            }

        }

        return $attr;
    }

    /**
     * @return string
     */
    public function completeTemplate()
    {
        $containerAttributes = [
            'required' => '',
            'type' => '',
            'text' => $this->label,
            'label' => $this->label,
            'attrs' => '',
            'hidden' => $this->hidden,
            'containerAttrs' => '',
            'content' => $this->html,
        ];
        $containerAttributes += $this->setError($this->name);
        if ($this->containerTemplate) {
            $container = $this->containerTemplate;
        } elseif ($this->htmlAttributes['type'] !== 'hidden') {
            $container = $this->config['templates']['inputContainer'];
        } else {
            return $this->html;
        }
        return $this->formatTemplate($container, $containerAttributes);
    }

    /**
     * @param $name
     * @return array
     */
    public function setError($name)
    {
        $errorParams = [
            'help' => '',
            'error' => ''
        ];

        if (!empty($this->errors->hasError($name))) {
            $helpBlockTemplate = $this->config['templates']['helpBlock'];
            $errorAttr['text'] = $this->errors->getError($name);
            $errorParams['help'] = $this->formatTemplate($helpBlockTemplate,$errorAttr);
            $errorParams['error'] = $this->config['css']['errorClass'];
        }
        return $errorParams;
    }

    /**
     * @param $name
     * @return array
     */
    public function setOldInput($name)
    {
        $oldInputParams = [];
        if (!empty($this->oldInputs->hasOldInput())) {
            $value = $this->oldInputs->getOldInput($name);
            if (!empty($value)) {
                $oldInputParams['value'] = $value;
            }
        }
        return $oldInputParams;
    }
    /**
     * @param array $options
     * @return array|mixed|string
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function action(&$options = [])
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
     * @param $name
     * @return mixed
     */
    public function getId($name)
    {
        return str_ireplace(' ', '', ucwords(preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $name)));
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

    /**
     * @param $name
     * @return mixed
     */
    public function getClassName($name)
    {
        return array_last(explode('\\', $name));
    }


    /*
     *
     */
    public function setLabel($option)
    {
        $template = $this->config['templates']['label'];
        $name = array_shift($option);
        $attr = !empty($option[0]) ? $option[0] : [];

        if (!isset($attr['for'])) {
            $attr['for'] = $name;
        }

        $rep = [
            'attrs' => $this->formatAttributes($attr),
            'text' => $name
        ];

        return $this->formatTemplate($template, $rep);
    }

    /**
     * @param $inputName
     * @param $option
     * @return string
     */
    public function renderLabel($inputName, $option)
    {
        $for = isset($option['id']) ? $option['id'] : $inputName;
        $labelName = $this->getLabelName($inputName);
        $this->label = $this->setLabel([$labelName, ['for' => $for]]);
        return $this->label;
    }

    /**
     * @param $name
     * @return string
     */
    public function getLabelName($name)
    {
        return ucwords(preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $name));
    }

    /**
     * @param $attr
     */
    public function generateId(&$attr)
    {
        if (isset($attr['id']) && $attr['id'] == false) {
            unset($attr['id']);
        } else {
            $attr['id'] = isset($attr['id']) ? $attr['id'] : $this->getId($this->name);
        }
    }
}
