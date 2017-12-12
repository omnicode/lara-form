<?php

namespace LaraForm\Elements\Components;

class RadioWidget extends BaseInputWidget
{
    /**
     * @return string
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function render()
    {
        return $this->renderRadio($this->attr);
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

    /**
     * @param $attr
     */
    public function inspectionAttributes(&$attr)
    {
        $attr['value'] = isset($attr['value']) ? $attr['value'] : $this->config['default_value']['radio'];
        $this->generateClass($attr, $this->config['css']['radioClass']);
        $this->generateLabel($attr);
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
}