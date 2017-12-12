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
     * @param $arguments
     */
    public function setArguments($arguments)
    {
        $this->name = array_shift($arguments);
        $this->attr = array_shift($arguments);
        if (is_null($this->attr)) {
            $this->attr = [];
        }
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
     * @return mixed
     */
    protected function getTemplate($templateName)
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
        $this->label = $this->setLabel([$labelName, ['for' => $for]]);
        return $this->label;
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
     */
    protected function generateLabel(&$attr)
    {
        if (!empty($attr['label'])) {
            $this->renderLabel($attr['label'], $attr, true);
            $this->unlokAttributes[] = $attr['label'];
        } elseif (!isset($attr['label'])) {
            $this->renderLabel($this->name, $attr);
        }
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
            } elseif (starts_with($attr['class'], '+')) {
                $replacedClass = str_ireplace('+', '', $attr['class']);
                $this->htmlClass[] = $default;
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
    private function getLabelName($name)
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
