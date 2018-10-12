<?php
declare(strict_types=1);

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
    public function render(): string
    {
        $template = $this->getTemplate('radio');
        $this->checkAttributes($this->attr);
        $this->currentTemplate = $this->getTemplate('radioContainer');
        $this->setOtherHtmlAttributes('type', 'radio');
        return $this->formatNestingLabel($template,$this->attr);
    }

    /**
     * @param $attr
     * @return mixed|void
     */
    public function checkAttributes(array &$attr): void
    {
        $attr['value'] =  $attr['value'] ?? 1;
        if (!empty($attr['value'])) {
            $val = $this->getValue($this->name)['value'];
            $val= $this->strToArray($val);
            if (in_array($attr['value'], $val)) {
                $attr['checked'] = true;
            }
        }

        if (!empty($attr['checked'])) {
            $attr['checked'] = 'checked';
        }

        $attr['type'] = 'radio';
        $this->generateId($attr,true);
        $this->parentCheckAttributes($attr);
    }

}
