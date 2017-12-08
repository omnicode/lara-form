<?php

namespace LaraForm\Elements\Components;

class FileWidget extends BaseInputWidget
{
    /**
     * @var
     */
    protected $fileTemplate;

    /**
     * @param $option
     * @return string
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function render($option)
    {
        $this->name = array_shift($option);
        $attr = !empty($option[0]) ? $option[0] : [];
        $this->inspectionAttributes($attr);
        $this->containerTemplate = $this->getTemplate('fileContainer');
        $this->otherHtmlAttributes['type'] = 'file';
        return $this->toHtml($this->name, $attr, $this->fileTemplate);
    }

    /**
     * @param $attr
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function inspectionAttributes(&$attr)
    {
        $btn = $this->config['css']['submitClass'];
        $btnColor = $this->config['css']['submitColor'];
        $default = $btn.' '.$btnColor;
        $this->generateClass($attr,$default);
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
        parent::inspectionAttributes($attr);
    }
}