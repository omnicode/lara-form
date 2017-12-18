<?php

namespace LaraForm\Elements\Widgets;

class CheckboxWidget extends BaseInputWidget
{
    /**
     * @var array
     */
    private $oldCheckboxNames = [];

    /**
     * @return string
     */
    public function render()
    {
        $template = $this->getTemplate('checkbox');
        $this->checkAttributes($this->attr);
        $this->currentTemplate = $this->getTemplate('checkboxContainer');
        return $this->formatNestingLabel($template, $this->attr);
    }

    /**
     * @param $attr
     */
    public function checkAttributes(&$attr)
    {
        $attr['value'] = isset($attr['value']) ? $attr['value'] : 1;

        if (!empty($attr['value'])) {
            $val = $this->getValue($this->name)['value'];

            if (!is_array($val)) {
                $val = [$val];
            }

            if (in_array($attr['value'], $val)) {
                $attr['checked'] = true;
            }
        }

        if (isset($attr['checked'])) {
            $attr['checked'] = 'checked';
        }

        if (isset($attr['multiple']) || strpos($this->name, '[]')) {

            if (!strpos($this->name, '[]')) {
                $this->name .= '[]';
            }

            if (isset($attr['multiple'])) {
                unset($attr['multiple']);
            }
        }

        if (isset($attr['hidden']) && $attr['hidden'] == false) {
            unset($attr['hidden']);
        } else {
            $this->hidden = '';

            if (!in_array($this->name, $this->oldCheckboxNames)) {
                if (ends_with($this->name, '[]')) {
                    $hiddenName = str_ireplace('[]', '', $this->name);
                } else {
                    $hiddenName = $this->name;
                }
                $this->hidden = $this->setHidden($hiddenName, 0);
            }

            $this->oldCheckboxNames[] = $this->name;
        }
        $attr['type'] = 'checkbox';
        $this->generateClass($attr, $this->config['css']['class']['checkboxClass']);
        $this->generateId($attr,true);
        parent::checkAttributes($attr);
    }
}