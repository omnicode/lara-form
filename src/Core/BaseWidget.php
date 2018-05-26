<?php

namespace LaraForm\Core;

/**
 * Creates ready-made templates
 * Class BaseWidget
 * @package LaraForm\Core
 */
abstract class BaseWidget
{
    /**
     * Fields that are required in the template
     * @var array
     */
    protected $htmlAttributes = [];

    /**
     * Other fields that are not required for the template
     * @var array
     */
    protected $otherHtmlAttributes = [];

    /**
     * Contains classes for the field
     * @var array
     */
    protected $htmlClass = [];

    /**
     * Contains the current ready html view
     * @var string
     */
    protected $html = '';

    /**
     * Contains the current html label field
     * @var string
     */
    protected $label = '';

    /**
     * Contains the current html icon field
     * @var string
     */
    protected $icon = '';

    /**
     * Contains the current field name
     * @var
     */
    protected $name;

    /**
     * Contains the current field attributes
     * @var array
     */
    protected $attr = [];


    /**
     * Contains the configuration params
     * @var mixed
     */
    protected $config;

    /**
     * Contains the current selected template for field container
     * @var bool
     */
    protected $currentTemplate = false;

    /**
     * Contains the params for field container
     * @var array
     */
    protected $containerParams = [];

    /**
     * Contains the templates for field container
     * @var array
     */
    protected $templates = [];

    /**
     * Contains the params for html class concatenation
     * @var array
     */
    protected $classConcat = [];

    /**
     * Contains the params for escept
     * @var array
     */
    protected $escept = [];

    /**
     * @var array
     */
    protected $labelAttr = [];

    /**
     * Contains current hidden hollow if that's eating
     * @var string
     */
    protected $hidden = '';

    /**
     * Contains the validation errors
     * @var array
     */
    protected $errors = [];

    /**
     * Contains the data of the fields before the check is
     * @var array
     */
    protected $oldInputs = [];

    /**
     * Contains the passed model
     * @var
     */
    protected $bind = null;


    /**
     * Add templates, parameters, and parameters for concatenating classes to properties
     * @param $data
     * @param $permission
     */
    protected function addTemplateAndAttributes($data, $permission)
    {
        $this->containerParams[$permission] = $data['div'];
        $this->classConcat[$permission] = $data['class_concat'];
        $this->labelAttr[$permission] = $data['label'];
        $this->escept[$permission] = $data['escept'];
        foreach ($data['pattern'] as $key => $value) {
            if (isset($this->config['templates'][$key])) {
                $this->templates[$permission][$key] = $value;
            }
        }
    }

    /**
     * Formats the html template to the specified params
     * @param $template
     * @param $attributes
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
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
     * @param $template
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function transformTemplate(&$template)
    {
        $start = $this->config['separator']['start'];
        $end = $this->config['separator']['end'];
        $seperatorsStart = ['[', '{', '('];
        $seperatorsEnd = [']', '}', ')'];

        if (!starts_with($start, $seperatorsStart) && !ends_with($end, $seperatorsEnd)) {
            $_msg = 'Sintax error, allowed symbols for start '
                . implode(',', $seperatorsStart)
                . ' and for end '
                . implode(',', $seperatorsEnd);
            throw new \Exception($_msg);
        }
        $template = str_ireplace([$start, $end], ['{%', '%}'], $template);
    }

    /**
     * Formats the html attributes
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

        $attributes = array_filter($attributes, function ($value, $key) {
            if (isset($value) && $value !== 0 && $value !==[] && $value !== '' && $value !== false) {
                return [$key => $value];
            }
        }, ARRAY_FILTER_USE_BOTH);

        $attributes = $this->array_iunique($attributes);

        $this->setOtherHtmlAttributes($attributes);

        foreach ($attributes as $index => $attribute) {

            if (is_string($index)) {
                $attr .= $index . '="' . $attribute . '" ';
            } else {
                $attr .= $attribute . ' ';
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
            } else {
                $exClasses = [];
                foreach ($classes as $index => $class) {
                    $exClasses = array_merge($exClasses, explode(' ', $class));
                }
                $classes = $exClasses;
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
     * @param array
     * @return array
     */
    private function array_iunique($array)
    {
        $lowered = array_map('mb_strtolower', $array);
        $onlyStrKeys = array_filter($lowered, function ($key) {
            if (is_string($key)) {
                return $key;
            }
        }, ARRAY_FILTER_USE_KEY );
        $unique = array_unique($lowered);
        return array_intersect_key($array, array_merge($unique, $onlyStrKeys));
    }

    /**
     * Finally creates a view
     * @return mixed|string
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
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
        $this->label = '';
        $this->hidden = '';
        $this->htmlClass = [];
        $this->attr = [];
    }

    /**
     * @param $data
     * @param bool $default
     * @return bool
     */
    protected function getModifiedData($data, $default = false)
    {
        $datum = $default;
        if (!empty($data['inline'])) {
            $datum = $data['inline'];
        } elseif (!empty($data['local'])) {
            $datum = $data['local'];
        } elseif (!empty($data['global'])) {
            $datum = $data['global'];
        }
        return $datum;
    }

    /**
     * Returns a default template or a modified template
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
     * @return bool/array
     */
    protected function getLabelAttributes()
    {
        return $this->getModifiedData($this->labelAttr);
    }

    /**
     * Returns a default value or a modification for concatenating classes,
     * @return mixed
     */
    protected function getHtmlClassControl()
    {
        return $this->getModifiedData($this->classConcat, $this->config['css']['class_control']);
    }

    /**
     * Returns a default value or a modification for escept html tags,
     * @return bool
     */
    protected function getIsEscept()
    {
        return $this->getModifiedData($this->escept, $this->config['escept']);
    }

    /**
     * Returns all parameters for the field container
     * @return array
     */
    protected function getContainerAllAttributes()
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
     * @param $data
     * @return array
     */
    protected function getContainerAttributes($data)
    {
        $attributes = [];

        $attributes += $this->containerAttributeRequiredAndDisabled($data,'required');
        $attributes += $this->containerAttributeRequiredAndDisabled($data,'disabled');
        $attributes += $this->containerAttributeType($data);
        $attributes += $this->containerAttributeClass($data);

        if (!empty($data)) {
            $attributes['containerAttrs'] = $this->formatAttributes($data);
        }

        return $attributes;
    }

    /**
     * @param $data
     * @return array
     */
    protected function containerAttributeRequiredAndDisabled(&$data,$key)
    {
        $params = [];
        if ($this->getOtherHtmlAttributes($key)) {
            if (empty($data[$key])) {
                $params[$key] = $key;
            } else {
                $params[$key] = $data[$key];
                unset($data[$key]);
            }
        } elseif (!empty($data[$key])) {
            unset($data[$key]);
        }
        return $params;
    }

    /**
     * @param $data
     * @return array
     */
    protected function containerAttributeType(&$data)
    {
        $params = [];
        if (empty($data['type'])) {
            $params['type'] = $this->getOtherHtmlAttributes('type') ? $this->getOtherHtmlAttributes('type') : $this->getHtmlAttributes('type');
        } else {
            $params['type'] = $data['type'];
            unset($data['type']);
        }
        return $params;
    }

    /**
     * @param $data
     * @return array
     */
    protected function containerAttributeClass(&$data)
    {
        $params = [];
        if (!empty($data['class'])) {
            $class = $data['class'];
            $class = $this->strToArray($class);
            $this->htmlClass += $class;
            $params['class'] = $this->formatClass();
            unset($data['class']);
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

    /**
     * @param $param
     * @return array
     */
    protected function strToArray($param)
    {
        if (!is_array($param)) {
            $param = [$param];
        }
        return $param;
    }
}