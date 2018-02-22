<?php
declare(strict_types=1);

namespace LaraForm\Elements\Widgets;

/**
 * Processes and creates button tags
 * Class SubmitWidget
 * @package LaraForm\Elements\Widgets
 */
class SubmitWidget extends BaseInputWidget
{
    /**
     * @return mixed|string
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function render(): string
    {
        $this->checkAttributes($this->attr);
        $template = $this->getTemplate('button');

        if ($this->name === false) {
            $name = '';
        } elseif (!empty($this->name)) {
            $name = $this->name;
        } else {
            $name = $this->config['text']['submit_name'] ? $this->config['text']['submit_name'] : '';
        }
        $btnAttr = [
            'attrs' => $this->formatAttributes($this->attr),
            'text' => $name,
        ];


        $this->html = $this->formatTemplate($template, $btnAttr);
        $this->currentTemplate = $this->getTemplate('submitContainer');
        return $this->completeTemplate();
    }


    /**
     * @param $attr
     */
    public function checkAttributes(array &$attr): void
    {
        $btn = $this->config['css']['class']['submit'];
        $btnColor = $this->config['css']['class']['submitColor'];
        $defaut = $btn . ' ' . $btnColor;
        $this->btn($attr, $btn, $btnColor);
        $this->generateClass($attr, $defaut);

        if (!empty($attr['type']) && in_array($attr['type'], ['submit', 'button', 'reset'])) {
            $this->setOtherHtmlAttributes('type',$attr['type']);
        } else {
            $this->setOtherHtmlAttributes('type','submit');
            $attr['type'] = 'submit';
        }

        $this->generateId($attr);
        $this->parentCheckAttributes($attr);
    }
}