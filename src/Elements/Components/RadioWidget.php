<?php

namespace LaraForm\Elements\Components;

class RadioWidget extends BaseInputWidget
{
    /**
     * @return string
     */
    public function render()
    {
        return $this->renderRadio($this->attr);
    }

    /**
     * @param $attr
     */
    public function inspectionAttributes(&$attr)
    {
        $this->generateClass($attr, $this->config['css']['radioClass']);
        $this->generateLabel($attr);
        $attr['value'] = isset($attr['value']) ? $attr['value'] : $this->config['default_value']['radio'];
        if (!empty($attr['value'])) {
            $val = $this->getValue($this->name)['value'];
            if (!is_array($val)) {
                $val = [$val];
            }
            if (in_array($attr['value'], $val)) {
                $attr['checked'] = true;
            }
        }
        if (isset($attr['type'])) {
            unset($attr['type']);
        }
        if (isset($attr['checked'])) {
            $attr['checked'] = 'checked';
        }
        /* if (isset($attr['hidden']) && $attr['hidden'] == false) {
             unset($attr['hidden']);
         } else {
             $this->hidden = $this->setHidden($this->name, '');
         }*/
        if (empty($attr['id'])) {
            $attr['id'] = $this->name . '-' . $attr['value'];
        }
        parent::inspectionAttributes($attr);
    }

    /**
     * @param $attr
     * @return string
     */
    private function renderRadio($attr)
    {
        $template = $this->config['templates']['radio'];
        $this->inspectionAttributes($attr);
        $this->containerTemplate = $this->getTemplate('radioContainer');
        $this->otherHtmlAttributes['type'] = 'radio';
        return $this->formatNestingLabel($template,$attr);
    }
}