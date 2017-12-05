<?php

namespace LaraForm\Elements;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use LaraForm\Stores\BoundStore;
use LaraForm\Stores\ErrorStore;
use LaraForm\Stores\OldInputStore;

class Widget implements WidgetInterface
{
    /**
     * @var array
     */
    protected $htmlAttributes = [];

    /**
     * @var array
     */
    protected $unlokAttributes = [];

    /**
     * @var array
     */
    protected $htmlClass = [];

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
     * @var mixed
     */
    public $config;

    /**
     * @var array
     */
    public $localTemplates = [];

    /**
     * @var array
     */
    public $globalTemplates = [];

    /**
     * @var
     */
    public $attributes;

    /**
     * @var bool
     */
    public $containerTemplate = false;

    /**
     * @var array
     */
    public $containerParams = true;

    /**
     * @var bool
     */
    public $isContainer = true;

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
     * @var
     */
    public $bound = null;

    /**
     * Widget constructor.
     * @param ErrorStore $errorStore
     * @param OldInputStore $oldInputStore
     * @param array $params
     */
    public function __construct(ErrorStore $errorStore, OldInputStore $oldInputStore, $params = [])
    {
        $this->config = config('lara_form');
        $this->errors = $errorStore;
        $this->oldInputs = $oldInputStore;
        $this->addTemplate($params);
        $this->addContainerAttributes($params);
    }

    /**
     * @param $data
     */
    public function setModel($data)
    {
        $this->bound = new BoundStore($data);

    }

    /**
     * @param $templates
     */
    protected function addTemplate($templates)
    {
        if (!empty($templates['local'])) {
            foreach ($templates['local'] as $key => $value) {
                if (isset($this->config['templates'][$key])) {
                    $this->localTemplates[$key] = $value;
                }
            }
        }
        if (!empty($templates['global'])) {
            foreach ($templates['global'] as $key => $value) {
                if (isset($this->config['templates'][$key])) {
                    $this->globalTemplates[$key] = $value;
                }
            }
        };
    }

    /**
     * @param $params
     */
    protected function addContainerAttributes($params)
    {
        if (isset($params['divGlobal'])) {

        }
        if (isset($params['divLocal'])) {

        }
       //TODO create system for dinamic controll by container filds
    }

    /**
     * @param $templateName
     * @param bool $unset
     * @return mixed|null
     */
    protected function getTemplate($templateName, $unset = true)
    {
        $template = null;
        if (!empty($this->localTemplates[$templateName])) {
            $template = $this->localTemplates[$templateName];
        } elseif (!empty($this->globalTemplates[$templateName])) {
            $template = $this->globalTemplates[$templateName];
        } elseif (!empty($this->config['templates'][$templateName])) {
            $template = $this->config['templates'][$templateName];
        }

        return $template;
    }

    /**
     * @param $name
     * @return array
     */
    public function getValue($name)
    {
        $value = '';
        $data = [];
        if (!empty($this->bound)) {
            $value = $this->bound->get($name, null);
        }
        if ($this->oldInputs->hasOldInput()) {
            $value = $this->oldInputs->getOldInput($name);
        }
        $data['value'] = $value;
        return $data;
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
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function formatTemplate($template, $attributes)
    {
        if (empty($attributes)) {
            return $template;
        }
        $from = [];
        $to = [];
        $this->transformTemplate($template);
        foreach ($attributes as $index => $attribute) {
            $from[] = '{%' . $index . '%}';
            $to[] = $attribute;
        }

        return str_ireplace($from, $to, $template);
    }

    /**
     * @param $template
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    private function transformTemplate(&$template)
    {
        $start = $this->config['seperator']['start'];
        $end = $this->config['seperator']['end'];
        $seperatorsStart = ['[', '{', '('];
        $seperatorsEnd = [']', '}', ')'];
        if (!starts_with($start, $seperatorsStart) && !ends_with($end, $seperatorsEnd)) {
            abort(300, 'Sintax error, allowed symbols for start ' . implode(',', $seperatorsStart) . ' and for end ' . implode(',', $seperatorsEnd));
        }
        $template = str_ireplace([$start, $end], ['{%', '%}'], $template);
    }

    /**
     * @param $attributes
     * @return string
     */
    public function formatAttributes($attributes)
    {
        $attr = '';
        if (empty($attributes)) {
            return $attr;
        }
        if (!empty($this->unlokAttributes)) {
            $attributes = array_diff($attributes, $this->unlokAttributes);
        }
        if (!isset($attributes['class'])) {
            $attributes['class'] = $this->formatClass();
        }
        $attributes = array_filter($attributes, function ($value) {
            if (!empty($value) && $value !== '' && $value !== false) {
                return $value;
            }
        });
        foreach ($attributes as $index => $attribute) {
            if (is_string((string)$index)) {
                $attr .= $index . '="' . $attribute . '" ';
            } else {
                $attr .= $attribute . ' ';
            }

        }

        return $attr;
    }

    /**
     * @return string
     */
    protected function formatClass()
    {
        $class = '';
        if (!empty($this->htmlClass)) {
            $uniqueClass = array_unique($this->htmlClass);
            $arrayClass = array_filter($uniqueClass, function ($value) {
                if (!empty($value) || $value !== false || $value !== '') {
                    return $value;
                }
            });
            $class = implode(' ', $arrayClass);
        }
        $this->htmlClass = [];
        return $class;
    }

    /**
     * @return string
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function completeTemplate()
    {
        $containerAttributes = [
            'text' => $this->label,
            'label' => $this->label,
            'hidden' => $this->hidden,
            'content' => $this->html,
        ];
        $containerAttributes += $this->setError($this->name);
        $containerAttributes += $this->getContatinerAttributes();
        if (!$this->containerParams || !$this->isContainer) {
            $this->containerParams = true;
            return $this->html;
        }
        if ($this->containerTemplate) {
            $container = $this->containerTemplate;
        } elseif ($this->htmlAttributes['type'] !== 'hidden') {
            $container = $this->getTemplate('inputContainer');
        } else {
            return $this->html;
        }
        return $this->formatTemplate($container, $containerAttributes);
    }

    /**
     * @return array
     */
    private function getContatinerAttributes()
    {
        $params = [
            'required' => '',
            'type' => '',
            'containerAttrs' => '',
            'class' => '',
        ];

        if (!is_array($this->containerParams)) {
            return $params;
        }
        if (!empty($this->containerParams['required'])) {
            $params['required'] = $this->containerParams['required'];
            unset($this->containerParams['required']);
        }
        if (!empty($this->containerParams['type'])) {
            $params['type'] = $this->containerParams['type'];
            unset($this->containerParams['type']);
        }
        if (!empty($this->containerParams['class'])) {
            $class = $this->containerParams['class'];
            if (!is_array($class)) {
                $class = [$class];
            }
            $this->htmlClass = $class;
            $params['class'] = $this->formatClass();
            unset($this->containerParams['class']);
        }
        $params['containerAttrs'] = $this->formatAttributes($this->containerParams);
        return $params;
    }

    /**
     * @param $name
     * @return array
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
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
            $errorParams['help'] = $this->formatTemplate($helpBlockTemplate, $errorAttr);
            $errorParams['error'] = $this->config['css']['errorClass'];
        }
        return $errorParams;
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
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
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
            $this->unlokAttributes['id'] = $attr['id'];
        } else {
            $attr['id'] = isset($attr['id']) ? $attr['id'] : $this->getId($this->name);
        }
        if ($this->config['label']['idPrefix'] && !isset($attr['idPrefix'])) {
            $attr['id'] = $this->config['label']['idPrefix'] . $attr['id'];
        } elseif (isset($attr['idPrefix']) && $attr['id'] !== false) {
            $attr['id'] = $attr['idPrefix'] . $attr['id'];
            $this->unlokAttributes['idPrefix'] = $attr['idPrefix'];
        }
    }

    /**
     * @param $attr
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function generateLabel(&$attr)
    {
        if (isset($attr['label']) && $attr['label'] !== false) {
            $this->renderLabel($attr['label'], $attr);
            $this->unlokAttributes[] = $attr['label'];
        } else {
            $this->renderLabel($this->name, $attr);
        }
    }

    /**
     * @param $attr
     */
    public function setContatinerParams(&$attr)
    {
        if (isset($attr['_div'])) {
            if ($attr['_div'] === false) {
                $this->containerParams = false;
            }
            if (is_array($attr['_div'])) {
                $this->containerParams = $attr['_div'];
            }
            unset($attr['_div']);
        }
    }

    /**
     * @param $name
     * @param $value
     * @return string
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function setHidden($name, $value)
    {
        $hiddenTemplate = $this->config['templates']['hiddenInput'];
        $attr = [
            'name' => $name,
            'value' => $value,
        ];
        return $this->formatTemplate($hiddenTemplate, $attr);
    }
}
