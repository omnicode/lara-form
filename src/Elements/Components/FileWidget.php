<?php

namespace LaraForm\Elements\Components;

class FileWidget extends BaseInputWidget
{
    /**
     * @var
     */
    private $fileTemplate;

    /**
     * @return string
     */
    public function render()
    {
        $this->inspectionAttributes($this->attr);
        $this->containerTemplate = $this->getTemplate('fileContainer');
        return $this->formatInputField($this->name, $this->attr, $this->fileTemplate);
    }

    /**
     * @param $attr
     */
    public function inspectionAttributes(&$attr)
    {
        $btn = $this->config['css']['submitClass'];
        $btnColor = $this->config['css']['submitColor'];
        $default = $btn.' '.$btnColor;
        if (isset($attr['btn'])) {
            if ($attr['btn'] === true) {
                $attr['btn'] = $btnColor;
            }
            $this->htmlClass[] = $btn . '-' . $attr['btn'];
            unset($attr['btn']);
        }
        $this->generateClass($attr,$default,false);
        if (isset($attr['type'])) {
            unset($attr['type']);
        }
        if (isset($attr['value'])) {
            unset($attr['value']);
        }
        if (isset($attr['multiple'])) {
            $this->fileTemplate = $this->config['templates']['fileMultiple'];
            unset($attr['multiple']);
        } else {
            $this->fileTemplate = $this->config['templates']['file'];
        }
        if (isset($attr['accept'])) {
            if (is_array($attr['accept'])) {
                $attr['accept'] = implode(', ', $attr['accept']);
            }
        }
        $this->otherHtmlAttributes['type'] = 'file';
        parent::inspectionAttributes($attr);
    }
}