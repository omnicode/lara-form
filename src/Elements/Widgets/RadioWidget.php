<?php

namespace LaraForm\Elements\Widgets;

/**
 * Processes and creates input tag for radio type
 *
 * Class RadioWidget
 * @package LaraForm\Elements\Widgets
 */
class RadioWidget extends BaseInputWidget
{
    /**
     * @return mixed|string
     */
    public function render()
    {
        $template = $this->config['templates']['radio'];
        $this->checkAttributes($attr);
        $this->currentTemplate = $this->getTemplate('radioContainer');
        $this->setOtherHtmlAttributes('type', 'radio');
        return $this->formatNestingLabel($template,$attr);
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

        $attr['type'] = 'radio';
        $this->generateClass($attr, $this->config['css']['class']['radioClass']);

        if (empty($attr['class'])) {
            $attr['class'] = false;
        }

        $this->generateId($attr,true);
        parent::checkAttributes($attr);
    }

}