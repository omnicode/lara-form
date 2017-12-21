<?php

namespace LaraForm\Core;

/**
 * Creates ready-made templates
 *
 * Class BaseWidget
 * @package LaraForm\Core
 */
abstract class BaseWidget
{
    /**
     * Fields that are required in the template
     *
     * @var array
     */
    private $htmlAttributes = [];

    /**
     * Other fields that are not required for the template
     *
     * @var array
     */
    private $otherHtmlAttributes = [];

    /**
     * Contains classes for the field
     *
     * @var array
     */
    protected $htmlClass = [];

    /**
     * Contains the current ready html view
     *
     * @var string
     */
    protected $html = '';

    /**
     * Contains the current html label field
     *
     * @var string
     */
    protected $label = '';

    /**
     * Contains the current html icon field
     *
     * @var string
     */
    protected $icon = '';

    /**
     * Contains the current field name
     *
     * @var
     */
    protected $name;

    /**
     * Contains the current field attributes
     *
     * @var array
     */
    protected $attr = [];


    /**
     * Contains the configuration params
     *
     * @var mixed
     */
    protected $config;

    /**
     * Contains the current selected template for field container
     *
     * @var bool
     */
    protected $currentTemplate = false;

    /**
     * Contains the params for field container
     *
     * @var array
     */
    protected $containerParams = [];

    /**
     * Contains the templates for field container
     *
     * @var array
     */
    protected $templates = [];

    /**
     * Contains the params for html class concatenation
     *
     * @var array
     */
    protected $classConcat = [];

    /**
     * @var array
     */
    protected $labelAttr = [];

    /**
     * Contains current hidden hollow if that's eating
     *
     * @var string
     */
    protected $hidden = '';

    /**
     * Contains the validation errors
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Contains the data of the fields before the check is
     *
     * @var array
     */
    protected $oldInputs = [];

    /**
     * Contains the passed model
     *
     * @var
     */
    protected $bind = null;


    /**
     * Add templates, parameters, and parameters for concatenating classes to properties
     *
     * @param $data
     * @param $permission
     */
    protected function addTemplateAndAttributes($data, $permission)
    {
        $this->containerParams[$permission] = $data['div'];
        $this->classConcat[$permission] = $data['class_concat'];
        $this->labelAttr[$permission] = $data['label'];
        foreach ($data['pattern'] as $key => $value) {
            if (isset($this->config['templates'][$key])) {
                $this->templates[$permission][$key] = $value;
            }
        }
    }

    /**
     * Formats the html template to the specified params
     *
     * @param $template
     * @param $attributes
     * @return mixed
     */
    protected function formatTemplate($template, $attributes)
    {
        if (empty($attributes)) {
            return $template;
        }

        $from = [];
        $to = [];
        foreach ($attributes as $index => $attribute) {
            $from[] = '{%' . $index . '%}';
            $to[] = $attribute;
        }

        $this->transformTemplate($template);
        return str_ireplace($from, $to, $template);
    }

    /**
     * Transforms the template into the required form
     *
     * @param $template
     */
    private function transformTemplate(&$template)
    {
        $start = $this->config['separator']['start'];
        $end = $this->config['separator']['end'];
        $seperatorsStart = ['[', '{', '('];
        $seperatorsEnd = [']', '}', ')'];

        if (!starts_with($start, $seperatorsStart) && !ends_with($end, $seperatorsEnd)) {
            abort(300, 'Sintax error, allowed symbols for start ' . implode(',', $seperatorsStart) . ' and for end ' . implode(',', $seperatorsEnd));
        }

        $template = str_ireplace([$start, $end], ['{%', '%}'], $template);
    }

    /**
     * Formats the html attributes
     *
     * @param $attributes
     * @return string
     */
    protected function formatAttributes($attributes)
    {
        $attr = '';
        if (empty($attributes['class'])) {
            $class = $this->formatClass();

            if ($class !== '') {
                $attributes['class'] = $class;
            }
        }

        if (empty($attributes)) {
            return $attr;
        }

        $attributes = array_filter($attributes, function ($value) {
            if (!empty($value) && $value !== '' && $value !== false) {
                return $value;
            }
        });

        $this->setOtherHtmlAttributes($attributes);

        foreach ($attributes as $index => $attribute) {

            if (is_string($index)) {
                $attr .= $index . '="' . $attribute . '" ';
            } else {
                $attr .= $attribute;
            }

        }

        return $attr;
    }

    /**
     * Filteres and format html class
     * @param array $classes
     * @return string
     */
    protected function formatClass($classes = [])
    {
        $class = '';
        if (empty($classes)) {
          $classes = $this->htmlClass;
          $this->htmlClass = [];
        }
        if (!empty($classes)) {
            if (is_string($classes)) {
                $classes = explode(' ', $classes);
            }

            $classes = array_filter($classes, function ($val) {
                $val = trim($val);
                if (!empty($val) && $val !== '' && $val !== false) {
                    return $val;
                }
            });

            if (empty($classes)) {
                return $class;
            }

            $uniqueClass = $this->array_iunique($classes);
            $class = implode(' ', $uniqueClass);
        }

        return $class;
    }

    /**
     * case-insensitive array_unique
     *
     * @param array
     * @return array
     * @link http://stackoverflow.com/a/2276400/932473
     */
    private function array_iunique($array)
    {
        $lowered = array_map('mb_strtolower', $array);
        return array_intersect_key($array, array_unique($lowered));
    }

    /**
     * Finally creates a view
     *
     * @return mixed|string
     */
    protected function completeTemplate()
    {
        $containerAttributes = [
            'text' => $this->label,
            'label' => $this->label,
            'hidden' => $this->hidden,
            'content' => $this->html,
            'icon' => $this->icon
        ];

        if ($this->currentTemplate) {
            $container = $this->currentTemplate;
        } elseif ($this->getHtmlAttributes('type') && $this->getHtmlAttributes('type') !== 'hidden') {
            $container = $this->getTemplate('inputContainer');
        } else {
            return $this->html;
        }

        if (!is_array($this->containerParams['inline']) or
            !is_array($this->containerParams['local']) or
            !is_array($this->containerParams['global'])) {
            $container = strip_tags($container);
        }

        $containerAttributes += $this->getErrorByFieldName($this->name);
        $containerAttributes += $this->getContainerAllAttributes();
        $this->resetProperties();
        return $this->formatTemplate($container, $containerAttributes);
    }

    /**
     * Removes proprties
     */
    protected function resetProperties()
    {
        $this->icon = '';
        $this->htmlClass = [];
        $this->label = '';
        $this->attr = [];
    }

    /**
     * Returns a default template or a modified template
     *
     * @param $templateName
     * @return mixed
     */
    protected function getTemplate($templateName)
    {
        $template = $this->config['templates'][$templateName];

        if (!empty($this->templates['inline'][$templateName])) {
            $template = $this->templates['inline'][$templateName];
        } elseif (!empty($this->templates['local'][$templateName])) {
            $template = $this->templates['local'][$templateName];
        } elseif (!empty($this->templates['global'][$templateName])) {
            $template = $this->templates['global'][$templateName];
        }

        return $template;
    }

    /**
     * Returns all parameters for the field container
     *
     * @return array
     */
    private function getContainerAllAttributes()
    {
        $params = [
            'required' => '',
            'disabled' => '',
            'type' => '',
            'containerAttrs' => '',
            'class' => '',
        ];

        $globalParams = $this->getContainerAttributes($this->containerParams['global']);
        $localParams = $this->getContainerAttributes($this->containerParams['local']);
        $inlineParams = $this->getContainerAttributes($this->containerParams['inline']);
        $params = array_replace($params, $globalParams, $localParams, $inlineParams);
        return $params;
    }

    /**
     * Returns parameters for the field container by permission
     *
     * @param $data
     * @return array
     */
    protected function getContainerAttributes($data)
    {
        $params = [];

        if ($this->getOtherHtmlAttributes('required')) {
            if (!empty($data['required'])) {
                $params['required'] = $data['required'];
                unset($data['required']);
            } else {
                $params['required'] = 'required';
            }
        }elseif (!empty($data['required'])){
            unset($data['required']);
        }

        if ($this->getOtherHtmlAttributes('disabled')) {
            if (!empty($data['disabled'])) {
                $params['disabled'] = $data['disabled'];
                unset($data['disabled']);
            } else {
                $params['disabled'] = 'disabled';
            }
        }elseif (!empty($data['disabled'])){
            unset($data['disabled']);
        }

        if (!empty($data['type'])) {
            $params['type'] = $data['type'];
            unset($data['type']);
        } else {
            $params['type'] = $this->getOtherHtmlAttributes('type') ? $this->getOtherHtmlAttributes('type') : $this->getHtmlAttributes('type');
        }

        if (!empty($data['class'])) {
            $class = $data['class'];

            if (!is_array($class)) {
                $class = [$class];
            }

            $this->htmlClass += $class;
            $params['class'] = $this->formatClass();
            unset($data['class']);
        }

        if (!empty($data)) {
            $params['containerAttrs'] = $this->formatAttributes($data);
        }

        return $params;
    }

    /**
     * @param $key
     * @param null $value
     */
    protected function setOtherHtmlAttributes($key, $value = null)
    {
        if (is_array($key) && empty($value)) {
            $this->otherHtmlAttributes += $key;
        } else {
            $this->otherHtmlAttributes[$key] = $value;
        }
    }

    /**
     * @param $data
     */
    protected function assignOtherhtmlAtrributes($data)
    {
        $this->otherHtmlAttributes = $data;
    }

    /**
     * @param null $key
     * @return array|bool|mixed
     */
    protected function getOtherHtmlAttributes($key = null)
    {
        if (empty($key)) {
            return $this->otherHtmlAttributes;
        }

        return !empty($this->otherHtmlAttributes[$key]) ? $this->otherHtmlAttributes[$key] : false;
    }


    /**
     * @param $key
     * @param $value
     */
    protected function setHtmlAttributes($key, $value)
    {
        $this->htmlAttributes[$key] = $value;
    }

    /**
     * @param null $key
     * @return array|bool|mixed
     */
    protected function getHtmlAttributes($key = null)
    {
        if (empty($key)) {
            return $this->htmlAttributes;
        }

        return isset($this->htmlAttributes[$key]) ? $this->htmlAttributes[$key] : false;
    }
}