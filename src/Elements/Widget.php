<?php

namespace LaraForm\Elements;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use LaraForm\Core\BaseWidget;
use LaraForm\Stores\BindStore;
use LaraForm\Stores\ErrorStore;
use LaraForm\Stores\OldInputStore;

/**
 * Class Widget
 * @package LaraForm\Elements
 */
class Widget extends BaseWidget implements WidgetInterface
{
    /**
     * Widget constructor.
     * @param ErrorStore $errorStore
     * @param OldInputStore $oldInputStore
     */
    public function __construct(ErrorStore $errorStore, OldInputStore $oldInputStore)
    {
        $this->config = config('lara_form');
        $this->errors = $errorStore;
        $this->oldInputs = $oldInputStore;
    }

    /**
     *
     */
    public function render()
    {

    }

    /**
     * Checks and modifies the attributes that were passed in the field
     * @param $attr
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function checkAttributes(&$attr)
    {
        if (!empty($attr['icon'])) {
            $iconTemplate = $this->getTemplate('icon');
            $this->icon = $this->formatTemplate($iconTemplate, ['name' => $attr['icon']]);
            unset($attr['icon']);
        }
        $this->setOtherHtmlAttributesBy($attr, 'required');
        $this->setOtherHtmlAttributesBy($attr, 'disabled');

        if (!empty($attr['readonly'])) {
            $attr['readonly'] = 'readonly';
        }
        if (isset($attr['autocomplete'])) {
            if ($attr['autocomplete'] === true || $attr['autocomplete'] === 'on') {
                $attr['autocomplete'] = 'on';
            } else {
                $attr['autocomplete'] = 'off';
            }
        }
    }

    /**
     * @param $attr
     */
    protected function parentCheckAttributes(&$attr)
    {
        self::checkAttributes($attr);
    }

    /**
     * Adds modifying data to templates and their parameters
     * @param $data
     */
    public function setParams($data)
    {
        $this->containerParams = [];
        $this->templates = [];
        $this->classConcat = [];
        $this->labelAttr = [];

        foreach ($data as $index => $item) {
            $this->addTemplateAndAttributes($item, $index);
        }
    }

    /**
     * Adds and model in BindStore and creates the object
     * @param $data
     */
    public function binding($data)
    {
        $this->bind = $data;
    }

    /**
     * Creates a field name and their options if they exist
     * @param $arguments
     */
    public function setArguments($arguments)
    {
        $this->name = array_shift($arguments);
        if (!empty($arguments[0])) {
            $this->attr = array_shift($arguments);
        }
    }

    /**
     * @param $name
     * @return array
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function getErrorByFieldName($name)
    {
        $errorParams = [
            'help' => '',
            'error' => ''
        ];

        if (!empty($this->errors->hasError($name))) {
            $helpBlockTemplate = $this->getTemplate('helpBlock');
            $errorAttr['text'] = $this->errors->getError($name);
            $errorParams['help'] = $this->formatTemplate($helpBlockTemplate, $errorAttr);
            $errorParams['error'] = $this->config['css']['class']['error'];
        }

        return $errorParams;
    }

    /**
     * Returns the field value from the link to the model or the one that was before the validation
     * @param $name
     * @return array
     */
    protected function getValue($name)
    {
        $value = '';
        $data = [];

        if (!empty($this->bind)) {
            $value = $this->bind->get($name, null);
        }

        if ($this->oldInputs->hasOldInput()) {
            $value = $this->oldInputs->getOldInput($name);
        }

        $data['value'] = $value;
        return $data;
    }


    /**
     * Creates view for html field label
     * @param $name
     * @param $attr
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function renderLabel($name, $attr)
    {
        $template = $this->getTemplate('label');

        if (!isset($attr['for'])) {
            $attr['for'] = $name;
        }
        if (!empty($attr['class'])) {
            $attr['class'] = $this->formatClass($attr['class']);
        }
        $rep = [
            'attrs' => $this->formatAttributes($attr),
            'text' => $name,
            'icon' => $this->icon
        ];

        return $this->formatTemplate($template, $rep);
    }

    /**
     * @param $text
     * @return string
     */
    protected function escept($text)
    {
        if (!$this->getIsEscept()) {
            return htmlspecialchars($text);
        }
        return $text;
    }

    /**
     * Checks and creates attributes for the label field
     * @param $inputName
     * @param $option
     * @param bool $treatment
     * @param array $labelAttr
     * @return string
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function checkLabel($inputName, $option, $treatment = false, $labelAttr = [])
    {
        $for = isset($option['id']) ? $option['id'] : $inputName;
        $labelName = $treatment ? $this->escept($inputName) : $this->translate($this->getLabelName($inputName));
        $labelAttr = array_merge($labelAttr, ['for' => $for]);
        $this->label = $this->renderLabel($labelName, $labelAttr);
        return $this->label;
    }

    /**
     * Generates id by specified parameters
     * @param $attr
     * @param bool $multi
     */
    protected function generateId(&$attr, $multi = false)
    {
        if (isset($attr['id']) && $attr['id'] == false) {
            unset($attr['id']);
        } else {
            $attr['id'] = isset($attr['id']) ? $attr['id'] : $this->getId($this->name);
            if ($this->config['css']['id_prefix'] && !isset($attr['id_prefix'])) {
                $attr['id'] = $this->config['css']['id_prefix'] . $attr['id'];
            } elseif (isset($attr['id_prefix']) && $attr['id'] !== false) {
                $attr['id'] = $attr['id_prefix'] . $attr['id'];
                unset($attr['id_prefix']);
            }
            if ($multi && isset($attr['value'])) {
                $attr['id'] .= '-' . $attr['value'];
            }
        }
    }

    /**
     * Generates label by property attr
     * @param $attr
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function generateLabel(&$attr)
    {
        $labelName = $this->name;
        $attributes = $this->getLabelAttributes();
        if (!empty($attr['label']) && is_string($attr['label'])) {
            $this->checkLabel($attr['label'], $attr, true);
            unset($attr['label']);
        } elseif (!empty($attributes)) {
            $trant = false;

            if (!empty($attributes['text'])) {
                $labelName = $attributes['text'];
                $trant = true;
                unset($attributes['text']);
            }

            $this->checkLabel($labelName, $attr, $trant, $attributes);
        } elseif (!isset($attr['label']) && $this->config['text']['label']) {
            $this->checkLabel($labelName, $attr);
        }
    }

    /**
     * Generates placeholder by property attr
     * @param $attr
     */
    protected function generatePlaceholder(&$attr)
    {
        if (isset($attr['placeholder'])) {
            if (is_bool($attr['placeholder']) && $attr['placeholder'] !== false) {
                $attr['placeholder'] = $this->translate($this->getLabelName($this->name));
            }
        } elseif ($this->config['text']['placeholder']) {
            $attr['placeholder'] = $this->getLabelName($this->name);
        }
    }

    /**
     * Generates class by specified parameters
     * @param $attr
     * @param bool $default
     * @param bool $format
     */
    protected function generateClass(&$attr, $default = false, $format = true)
    {
        if (isset($attr['class'])) {
            $classes = $attr['class'];

            if ($classes === false) {
                $this->htmlClass = [];
            } else {
                $classes = $this->strToArray($classes);
                if ($this->getHtmlClassControl()) {
                    $this->htmlClass = array_merge([$default], $classes);
                } else {
                    $this->htmlClass = $classes;
                }

                unset($attr['class']);
            }
        } else {
            $this->htmlClass[] = $default;
        }
        if ($this->errors->getError($this->name)) {
            $this->htmlClass[] = $this->config['css']['class']['error'];
        }
        if ($format) {
            $attr['class'] = $this->formatClass();
        }
    }


    /**
     * @param $attr
     * @param $key
     */
    protected function setOtherHtmlAttributesBy(&$attr, $key)
    {
        if (!empty($attr[$key])) {
            $this->setOtherHtmlAttributes($key, $key);
            $attr[$key] = $key;
        }
    }

    /**
     * Creates a hidden input field
     * @param $name
     * @param int $value
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function setHidden($name, $value = 0)
    {
        $hiddenTemplate = $this->getTemplate('hiddenInput');
        $attr = ['name' => $name, 'value' => $value,];
        return $this->formatTemplate($hiddenTemplate, $attr);
    }

    /**
     * Removes all characters except letters and numbers and creates a name for the label field
     * @param $name
     * @return string
     */
    protected function getLabelName($name)
    {
        return ucwords(preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $name));
    }

    /**
     * Removes all characters except letters and numbers and creates a id by camelcase style
     * @param $name
     * @return mixed
     */
    protected function getId($name)
    {
        $str = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $name);
        $case = $this->config['css']['id_case'];
        $camel = camel_case($str);
        if ($case === 'kebab') {
            return kebab_case($camel);
        }
        if ($case === 'snake') {
            return snake_case($camel);
        }
        return $camel;
    }

    /**
     * @param $str
     * @return string|\Symfony\Component\Translation\TranslatorInterface
     */
    protected function translate($str)
    {
        $path = $this->config['translate_directive'];
        if (!empty($path) && !ends_with('.', $path)) {
            $path = $path . '.';
        } else {
            $path = '';
        }
        if (function_exists('__')) {
            return __($path . $str);
        }
        if (function_exists('trans')) {
            return trans($path . $str);
        }
        return $str;
    }

    /**
     * @param $attr
     * @param $btnColor
     */
    protected function btn(&$attr,$btn, $btnColor)
    {
        if (isset($attr['btn'])) {

            if ($attr['btn'] === true) {
                $attr['btn'] = $btnColor;
            }

            $this->htmlClass[] = $btn . '-' . $attr['btn'];
            unset($attr['btn']);
        }
    }

    /**
     * @param $attr
     */
    protected function multipleByBrackets(&$attr)
    {
        if (ends_with($this->name, '[]')) {
            $this->name = substr($this->name, 0, -2);
            $attr['multiple'] = true;
        }
    }
}
