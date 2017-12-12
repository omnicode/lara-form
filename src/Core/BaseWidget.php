<?php

namespace LaraForm\Core;

abstract class BaseWidget
{
    /**
     * @var array
     */
    protected $htmlAttributes = [];

    /**
     * @var array
     */
    protected $otherHtmlAttributes = [];

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
    protected $html = '';

    /**
     * @var string
     */
    protected $label = '';

    /**
     * @var string
     */
    protected $icon = '';
    /**
     * @var
     */
    protected $name;

    /**
     * @var array
     */
    protected $attr = [];

    /**
     * @var array
     */
    protected $routes = [];

    /**
     * @var mixed
     */
    protected $config;

    /**
     * @var array
     */
    protected $localTemplates = [];

    /**
     * @var array
     */
    protected $globalTemplates = [];

    /**
     * @var array
     */
    protected $inlineTemplates = [];

    /**
     * @var
     */
    protected $attributes;

    /**
     * @var bool
     */
    protected $containerTemplate = false;

    /**
     * @var array
     */
    protected $containerParams = [
        'local' => [],
        'global' => [],
        'inline' => [],
    ];

    /**
     * @var string
     */
    protected $hidden = '';

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @var array
     */
    protected $oldInputs = [];

    /**
     * @var
     */
    protected $bound = null;

    /**
     * @var array
     */
    protected $fixedField = [];

    /**
     * @param $templates
     */
    protected function addLocalTemplate($templates)
    {   $this->localTemplates = [];
        foreach ($templates as $key => $value) {
            if (isset($this->config['templates'][$key])) {
                $this->localTemplates[$key] = $value;
            }
        }
    }

    /**
     * @param $templates
     */
    protected function addInlineTemplate($templates)
    {
        $this->inlineTemplates = [];
        foreach ($templates as $key => $value) {
            if (isset($this->config['templates'][$key])) {
                $this->inlineTemplates[$key] = $value;
            }
        }
    }

    /**
     * @param $templates
     */
    protected function addGlobalTemplate($templates)
    {
        foreach ($templates as $key => $value) {
            if (isset($this->config['templates'][$key])) {
                $this->globalTemplates[$key] = $value;
            }
        }
    }

    /**
     * @param $params
     */
    protected function addContainerLocalAttributes($params)
    {
        $this->containerParams['local'] = $params;
    }

    /**
     * @param $params
     */
    protected function addContainerGlobalAttributes($params)
    {
        $this->containerParams['global'] = $params;
    }

    /**
     * @param $params
     */
    protected function addContainerInlineAttributes($params)
    {
        $this->containerParams['inline'] = $params;
    }

    /**
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
        if (!empty($this->unlokAttributes)) {
            $attributes = array_diff($attributes, $this->unlokAttributes);
        }
        $attributes = array_filter($attributes, function ($value) {
            if (!empty($value) && $value !== '' && $value !== false) {
                return $value;
            }
        });
        $this->otherHtmlAttributes += $attributes;
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
     * @return string
     */
    protected function formatClass()
    {
        $class = '';
        if (!empty($this->htmlClass)) {
            if (is_string($this->htmlClass)) {
                $this->htmlClass = explode(' ', $this->htmlClass);
            }
            $this->htmlClass = array_filter($this->htmlClass, function ($val) {
                $val = trim($val);
                if (!empty($val) && $val !== '' && $val !== false) {
                    return $val;
                }
            });
            if (empty($this->htmlClass)) {
                return $class;
            }
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

        if ($this->containerTemplate) {
            $container = $this->containerTemplate;
        } elseif (isset($this->htmlAttributes['type']) && $this->htmlAttributes['type'] !== 'hidden') {
            $container = $this->getTemplate('inputContainer');
        } else {
            return $this->html;
        }

        if (!is_array($this->containerParams['inline']) or
            !is_array($this->containerParams['local']) or
            !is_array($this->containerParams['global'])) {
            $container = strip_tags($container);
        }

        $containerAttributes += $this->setError($this->name);
        $containerAttributes += $this->getContainerAllAttributes();
        return $this->formatTemplate($container, $containerAttributes);
    }

    /**
     * @return array
     */
    private function getContainerAllAttributes()
    {
        $params = [
            'required' => '',
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
     * @param $data
     * @return array
     */
    protected function getContainerAttributes($data)
    {
        $params = [];
        if (!empty($this->otherHtmlAttributes['required'])) {
            if (!empty($data['required'])) {
                $params['required'] = $data['required'];
                unset($data['required']);
            } else {
                $params['required'] = 'required';
            }
        }

        if (!empty($data['type'])) {
            $params['type'] = $data['type'];
            unset($data['type']);
        } else {
            $params['type'] = isset($this->otherHtmlAttributes['type']) ? $this->otherHtmlAttributes['type'] : $this->htmlAttributes['type'];
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
}