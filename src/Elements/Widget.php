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
    public $addedTemplates = [];

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
     * @var
     */
    public $bound = null;

    /**
     * Widget constructor.
     * @param ErrorStore $errorStore
     * @param OldInputStore $oldInputStore
     * @param null $setTemplate
     * @param array $params
     */
    public function __construct(ErrorStore $errorStore, OldInputStore $oldInputStore, $setTemplate = [], $params = [])
    {
        //TODO inspection base params
        $this->config = config('lara_form');
        $this->errors = $errorStore;
        $this->oldInputs = $oldInputStore;
        $this->addTemplate($setTemplate);
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
        if (!empty($templates)) {
            foreach ($templates as $key => $value) {
                if (isset($this->config['templates'][$key])) {
                    $this->addedTemplates[$key] = $value;
                }
            }
        }
    }

    /**
     * @param $templateName
     * @param bool $unset
     * @return mixed|null
     */
    protected function getTemplate($templateName, $unset = true)
    {
        $template = null;
        if (!empty($this->addedTemplates[$templateName])) {
            $template = $this->addedTemplates[$templateName];
            if ($unset) {
                unset($this->addedTemplates[$templateName]);
            }
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
     */
    private function transformTemplate(&$template)
    {
        $start = $this->config['seperator']['start'];
        $end = $this->config['seperator']['end'];
        $seperatorsStart = ['[','{','('];
        $seperatorsEnd = [']','}',')'];
        if (!starts_with($start,$seperatorsStart) && !ends_with($end,$seperatorsEnd)) {
            abort(300,'Sintax error, allowed symbols for start '.implode(',',$seperatorsStart).' and for end '.implode(',',$seperatorsEnd));
        }
        $template = str_ireplace([$start, $end], ['{%', '%}'], $template);
    }

    /**
     * @param $attributes
     * @return string
     */
    public function formatAttributes($attributes)
    {
        $attributes = array_filter($attributes, function ($value) {
            if (!empty($value) && $value !== '' && $value !== false) {
                return $value;
            }
        });
        $attr = '';
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
        if ($this->config['label']['idPrefix'] && !isset($attr['idPrefix'])) {
            $attr['id'] = $this->config['idPrefix'] . $attr['id'];
        } elseif (isset($attr['idPrefix']) && $attr['id'] !== false) {
            $attr['id'] = $attr['idPrefix'] . $attr['id'];
            unset($attr['idPrefix']);
        }
    }

    /**
     * @param $name
     * @param $value
     * @return string
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
