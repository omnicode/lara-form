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

    protected function addContainerInlineAttributes($params)
    {
        $this->containerParams['inline'] = $params;
    }

    protected function resetContainerLocalAttributes()
    {
        $this->containerParams['local'] = [];
    }

    protected function resetContainerGlobalAttributes()
    {
        $this->containerParams['global'] = [];
    }

    protected function resetContainerInlineAttributes()
    {
        $this->containerParams['inline'] = [];
    }
}