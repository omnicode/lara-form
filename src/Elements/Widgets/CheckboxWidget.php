<?php

namespace LaraForm\Elements\Widgets;

/**
 * Processes and creates checkbox
 * Class CheckboxWidget
 * @package LaraForm\Elements\Widgets
 */
class CheckboxWidget extends BaseInputWidget
{
    /**
     * Remembers the names of the checkboxes to determine in the future it
     * is worth inserting hidden or not
     * @var array
     */
    private $oldCheckboxNames = [];

    /**
     * Returns the finished html checkbox view
     * @return mixed|string
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
     * @return mixed|void
     */
    public function checkAttributes(&$attr)
    {
        $attr['value'] = isset($attr['value']) ? $attr['value'] : 1;
        if (!empty($attr['value'])) {
            $val = $this->getValue($this->name)['value'];
            $val = $this->strToArray($val);
            if (in_array($attr['value'], $val)) {
                $attr['checked'] = true;
            }
        }

        if (!empty($attr['checked'])) {
            $attr['checked'] = 'checked';
        }

        $multi = false;
        $this->multipleByBrackets($attr);
        if (!empty($attr['multiple'])) {
            $this->name .= '[]';
            unset($attr['multiple']);
            $multi = true;
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
        $this->generateId($attr, $multi);
        $this->parentCheckAttributes($attr);
    }
}