<?php

namespace LaraForm\Elements;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use LaraForm\Core\BaseWidget;
use LaraForm\Stores\BoundStore;
use LaraForm\Stores\ErrorStore;
use LaraForm\Stores\OldInputStore;

class Widget extends BaseWidget implements WidgetInterface
{
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
    }

    /**
     * @param $data
     */
    public function setParams($data)
    {
        $this->addGlobalTemplate($data['global']);
        $this->addLocalTemplate($data['local']);
        $this->addInlineTemplate($data['inline']);
        $this->addContainerGlobalAttributes($data['divGlobal']);
        $this->addContainerLocalAttributes($data['divLocal']);
        $this->addContainerInlineAttributes($data['divInline']);
    }

    /**
     * @param $data
     */
    public function setModel($data)
    {
        $this->bound = new BoundStore($data);

    }

    /**
     * @param $data
     */
    public function setFixedField($data)
    {
        $this->fixedField = $data;

    }

    /**
     * @param $templateName
     * @param bool $unset
     * @return mixed|null
     */
    protected function getTemplate($templateName, $unset = true)
    {
        $template = $this->config['templates'][$templateName];
        if (!empty($this->inlineTemplates[$templateName])) {
            $template = $this->inlineTemplates[$templateName];
        } elseif (!empty($this->localTemplates[$templateName])) {
            $template = $this->localTemplates[$templateName];
        } elseif (!empty($this->globalTemplates[$templateName])) {
            $template = $this->globalTemplates[$templateName];
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
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function inspectionAttributes(&$attr)
    {
        $this->generateId($attr);

        if (!empty($attr['icon'])) {
            $iconTemplate = $this->getTemplate('icon');
            $this->icon = $this->formatTemplate($iconTemplate, ['name' => $attr['icon']]);
            unset($attr['icon']);
        }
        if (!empty($attr['required'])) {
            $this->otherHtmlAttributes['required'] = 'required';
        }
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
        $this->otherHtmlAttributes = $attributes;
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
    protected function getId($name)
    {
        return lcfirst(str_ireplace(' ', '', ucwords(preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $name))));
    }

    /*
     *
     */
    protected function setLabel($option)
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
     * @param bool $treatment
     * @return string
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function renderLabel($inputName, $option, $treatment = false)
    {
        $for = isset($option['id']) ? $option['id'] : $inputName;
        $labelName = $treatment ? $inputName : $this->getLabelName($inputName);
        $this->label = $this->setLabel([$labelName, ['for' => $for]]);
        return $this->label;
    }

    /**
     * @param $name
     * @return string
     */
    protected function getLabelName($name)
    {
        return ucwords(preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $name));
    }

    /**
     * @param $attr
     */
    protected function generateId(&$attr)
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
    protected function generateLabel(&$attr)
    {
        if (isset($attr['label']) && $attr['label'] !== false) {
            $this->renderLabel($attr['label'], $attr, true);
            $this->unlokAttributes[] = $attr['label'];
        } else {
            $this->renderLabel($this->name, $attr);
        }
    }

    /**
     * @param $attr
     * @param $default
     */
    protected function generateClass(&$attr, $default)
    {
        if (isset($attr['class'])) {
            if ($attr['class'] === false) {
                $this->htmlClass[] = false;
            }elseif (starts_with( $attr['class'],'+')) {
                $replacedClass = str_ireplace('+', '', $attr['class']);
                $this->htmlClass[] = $default ;
                if (strlen($replacedClass) > 0) {
                    $this->htmlClass[] = $replacedClass;
                }
            } else {
                $this->htmlClass[] = $attr['class'];
            }
            unset($attr['class']);
        } else {
            $this->htmlClass[] = $default;
        }
    }

    /**
     * @param $name
     * @param $value
     * @return string
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function setHidden($name, $value = false)
    {
        $hiddenTemplate = $this->config['templates']['hiddenInput'];

        if ($value === false) {
            $value = $this->config['default_value']['hidden'];
        }

        $attr = ['name' => $name, 'value' => $value,];
        return $this->formatTemplate($hiddenTemplate, $attr);
    }
}
