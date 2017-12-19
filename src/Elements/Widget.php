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
     *
     * @param $attr
     */
    public function checkAttributes(&$attr)
    {
        if (!empty($attr['icon'])) {
            $iconTemplate = $this->getTemplate('icon');
            $this->icon = $this->formatTemplate($iconTemplate, ['name' => $attr['icon']]);
            unset($attr['icon']);
        }

        if (!empty($attr['required'])) {
            $this->setOtherHtmlAttributes('required', 'required');
        }
    }

    /**
     * Adds modifying data to templates and their parameters
     *
     * @param $data
     */
    public function setParams($data)
    {
        foreach ($data as $index => $item) {
            $this->addTemplateAndAttributes($item, $index);
        }
    }

    /**
     * Adds and model in BindStore and creates the object
     *
     * @param $data
     */
    public function setModel($data)
    {
        $this->bind = app(BindStore::class, [$data]);

    }

    /**
     * Creates a field name and their options if they exist
     *
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
     * Returns the content of the error message and its style if the error is eating
     *
     * @param $name
     * @return array
     */
    public function getErrorByFieldName($name)
    {
        $errorParams = [
            'help' => '',
            'error' => ''
        ];

        if (!empty($this->errors->hasError($name))) {
            $helpBlockTemplate = $this->config['templates']['helpBlock'];
            $errorAttr['text'] = $this->errors->getError($name);
            $errorParams['help'] = $this->formatTemplate($helpBlockTemplate, $errorAttr);
            $errorParams['error'] = $this->config['css']['class']['error'];
        }

        return $errorParams;
    }


    /**
     * Returns the field value from the link to the model or the one that was before the validation
     *
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
     *
     * @param $name
     * @param $attr
     * @return mixed
     */
    protected function renderLabel($name, $attr)
    {
        $template = $this->config['templates']['label'];

        if (!isset($attr['for'])) {
            $attr['for'] = $name;
        }

        $rep = [
            'attrs' => $this->formatAttributes($attr),
            'text' => $name,
            'icon' => $this->icon
        ];
        return $this->formatTemplate($template, $rep);
    }

    /**
     * Checks and creates attributes for the label field
     *
     * @param $inputName
     * @param $option
     * @param bool $treatment
     * @return string
     */
    protected function checkLabel($inputName, $option, $treatment = false)
    {
        $for = isset($option['id']) ? $option['id'] : $inputName;
        $labelName = $treatment ? $inputName : $this->getLabelName($inputName);
        $this->label = $this->renderLabel($labelName, ['for' => $for]);
        return $this->label;
    }

    /**
     * Generates id by specified parameters
     *
     * @param $attr
     * @param bool $multi
     */
    protected function generateId(&$attr, $multi = false)
    {
        if (isset($attr['id']) && $attr['id'] == false) {
            unset($attr['id']);
        } else {
            $attr['id'] = isset($attr['id']) ? $attr['id'] : $this->getId($this->name);
            if ($this->config['css']['class']['idPrefix'] && !isset($attr['idPrefix'])) {
                $attr['id'] = $this->config['css']['class']['idPrefix'] . $attr['id'];
            } elseif (isset($attr['idPrefix']) && $attr['id'] !== false) {
                $attr['id'] = $attr['idPrefix'] . $attr['id'];
                unset($attr['idPrefix']);
            }
            if ($multi && isset($attr['value'])) {
                $attr['id'] .= '-' . $attr['value'];
            }
        }
    }

    /**
     * Generates label by property attr
     *
     * @param $attr
     */
    protected function generateLabel(&$attr)
    {
        if (!empty($attr['label'])) {
            $this->checkLabel($attr['label'], $attr, true);
            unset($attr['label']);
        } elseif (!isset($attr['label'])) {
            $this->checkLabel($this->name, $attr);
        }
    }

    /**
     * Returns a default value or a modification for concatenating classes,
     *
     * @return mixed
     */
    protected function getHtmlClassControl()
    {
        $concat = $this->config['css']['class_control'];

        if (!$this->classConcat['inline']) {
            $concat = $this->classConcat['inline'];
        } elseif (!$this->classConcat['local']) {
            $concat = $this->classConcat['local'];
        } elseif (!$this->classConcat['global']) {
            $concat = $this->classConcat['global'];
        }

        return $concat;

    }

    /**
     * Generates class by specified parameters
     *
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
                if (!is_array($classes)) {
                    $classes = [$classes];
                }
                if ($this->getHtmlClassControl()) {
                    $this->htmlClass = array_merge([$default],$classes);
                } else {
                    $this->htmlClass = $classes;
                }

                unset($attr['class']);
            }
        } else {
            $this->htmlClass[] = $default;
        }

        if ($format) {
            $attr['class'] = $this->formatClass();
        }
    }

    /**
     * Creates a hidden input field
     *
     * @param $name
     * @param int $value
     * @return mixed
     */
    protected function setHidden($name, $value = 0)
    {
        $hiddenTemplate = $this->config['templates']['hiddenInput'];
        $attr = ['name' => $name, 'value' => $value,];
        return $this->formatTemplate($hiddenTemplate, $attr);
    }

    /**
     * Removes all characters except letters and numbers and creates a name for the label field
     *
     * @param $name
     * @return string
     */
    protected function getLabelName($name)
    {
        return ucwords(preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $name));
    }

    /**
     * Removes all characters except letters and numbers and creates a id by camelcase style
     *
     * @param $name
     * @return mixed
     */
    private function getId($name)
    {
        return lcfirst(str_ireplace(' ', '', ucwords(preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $name))));
    }
}
