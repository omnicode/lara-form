<?php

namespace LaraForm\Elements\Widgets;

/**
 * Processes and creates input tag for file type
 * Class FileWidget
 * @package LaraForm\Elements\Widgets
 */
class FileWidget extends BaseInputWidget
{
    /**
     * Keeped here template for file input
     * @var string
     */
    private $fileTemplate;

    /**
     * Returns the finished html file input view
     * @return mixed|string
     */
    public function render()
    {
        $this->checkAttributes($this->attr);
        $this->currentTemplate = $this->getTemplate('fileContainer');

        if (empty($this->name)) {
            $this->name = $this->config['text']['submit_name'] ? $this->config['text']['submit_name'] : '';
        }
        $labelAttr = ['class' => $this->formatClass()];
        return $this->formatNestingLabel($this->fileTemplate, $this->attr,$labelAttr);
    }

    /**
     * @param $attr
     * @return mixed|void
     */
    public function checkAttributes(&$attr)
    {
        $btn = $this->config['css']['class']['submit'];
        $btnColor = $this->config['css']['class']['submitColor'];
        $default = $btn . ' ' . $btnColor;

        if (isset($attr['btn'])) {

            if ($attr['btn'] === true) {
                $attr['btn'] = $btnColor;
            }

            $this->htmlClass[] = $btn . '-' . $attr['btn'];
            unset($attr['btn']);
        }

        if (isset($attr['type'])) {
            unset($attr['type']);
        }

        if (isset($attr['value'])) {
            unset($attr['value']);
        }

        if (isset($attr['multiple'])) {
            $this->fileTemplate = $this->gettemplate('fileMultiple');
            unset($attr['multiple']);
        } else {
            $this->fileTemplate = $this->getTemplate('file');
        }

        if (isset($attr['accept'])) {
            if (is_array($attr['accept'])) {
                $attr['accept'] = implode(', ', $attr['accept']);
            }
        }

        if (isset($attr['label']) && $attr['label'] === false) {
            unset($attr['label']);
        }

        $this->generateClass($attr, $default, false);
        parent::checkAttributes($attr);
    }
}