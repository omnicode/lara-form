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
     *
     */
    public function render()
    {

    }

    /**
     * @param $attr
     */
    public function inspectionAttributes(&$attr)
    {
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
     * @param $data
     */
    public function setParams($data)
    {
        foreach ($data as $index => $item) {
            $this->addTemplateAndAttributes($item, $index);
        }
    }

    /**
     * @param $data
     */
    public function setModel($data)
    {
        $this->bound = new BoundStore($data);

    }

    /**
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
     * @return array
     */
    protected function getValue($name)
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

    /*
     *
     */

    protected function setLabel($name, $attr)
    {
        $template = $this->config['templates']['label'];

        if (!isset($attr['for'])) {
            $attr['for'] = $name;
        }
       // dd($this->htmlClass);
        $rep = [
            'attrs' => $this->formatAttributes($attr),
            'text' => $name,
            'icon' => $this->icon
        ];
        return $this->formatTemplate($template, $rep);
    }

    /**
     * @param $inputName
     * @param $option
     * @param bool $treatment
     * @return string
     */
    protected function renderLabel($inputName, $option, $treatment = false)
    {
        $for = isset($option['id']) ? $option['id'] : $inputName;
        $labelName = $treatment ? $inputName : $this->getLabelName($inputName);
        $this->label = $this->setLabel($labelName, ['for' => $for]);
        return $this->label;
    }

    /**
     * @param $attr
     * @param bool $multi
     */
    protected function generateId(&$attr, $multi = false)
    {
        if (isset($attr['id']) && $attr['id'] == false) {
            unset($attr['id']);
        } else {
            $attr['id'] = isset($attr['id']) ? $attr['id'] : $this->getId($this->name);
            if ($this->config['label']['idPrefix'] && !isset($attr['idPrefix'])) {
                $attr['id'] = $this->config['label']['idPrefix'] . $attr['id'];
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
     * @param $attr
     */
    protected function generateLabel(&$attr)
    {
        if (!empty($attr['label'])) {
            $this->renderLabel($attr['label'], $attr, true);
            unset($attr['label']);
        } elseif (!isset($attr['label'])) {
            $this->renderLabel($this->name, $attr);
        }
    }

    /**
     * @return mixed
     */
    protected function getHtmlClassControl()
    {
        $concat = $this->config['label']['class_control']['class_concat'];

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
     * @param $attr
     * @param $default
     * @param bool $format
     */
    protected function generateClass(&$attr, $default, $format = true)
    {
        if (isset($attr['class'])) {

            if ($attr['class'] === false) {
                $this->htmlClass[] = false;
            } else {
                $symbol = $this->config['label']['class_control']['class_concat_symbol'];

                if ($this->getHtmlClassControl() && starts_with($attr['class'],$symbol)) {
                    $replacedClass = substr($attr['class'], strlen($symbol));
                    $this->htmlClass[] = $default;

                    if (strlen($replacedClass) > 0) {
                        $this->htmlClass[] = $replacedClass;
                    }
                } else {
                    $this->htmlClass[] = $attr['class'];
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
     * @param $name
     * @param bool $value
     * @return string
     */
    protected function setHidden($name, $value = false)
    {
        $hiddenTemplate = $this->config['templates']['hiddenInput'];

        if ($value === false) {
            $value = $this->config['default_value']['hidden'];
        }

        $attr = ['name' => $name, 'value' => $value,];
        return $this->formatTemplate($hiddenTemplate, $attr);
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
     * @param $name
     * @return mixed
     */
    private function getId($name)
    {
        return lcfirst(str_ireplace(' ', '', ucwords(preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $name))));
    }
}
