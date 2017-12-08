<?php

namespace LaraForm\Elements\Components;

use function GuzzleHttp\is_host_in_noproxy;
use function Symfony\Component\Debug\Tests\testHeader;

class CheckboxWidget extends BaseInputWidget
{

    /**
     * @var bool
     */
    private $isHidden = false;

    /**
     * @param $option
     * @return string
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function render($option)
    {
        $template = $this->getTemplate('checkbox');
        $this->name = array_shift($option);
        $attr = !empty($option[0]) ? $option[0] : [];
        return $this->renderCheckbox($attr, $template);
    }

    /**
     * @param $attr
     * @param $template
     * @return string
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    private function renderCheckbox($attr, $template)
    {
        if (strpos($this->name, '[]')) {
            $attr['multiple'] = true;
        }

        $this->inspectionAttributes($attr);
        $this->containerTemplate = $this->getTemplate('checkboxContainer');
        $labelTemplate = $this->getTemplate('nestingLabel');
        $this->toHtml($this->name, $attr, $template);
        $labelAttr = [
            'hidden' => $this->hidden,
            'content' => $this->html,
            'text' => isset($attr['label']) ? $attr['label'] : $this->getLabelName($this->name),
            'attrs' => ''
        ];
        $this->html = $this->formatTemplate($labelTemplate, $labelAttr);
        $this->otherHtmlAttributes['type'] = 'checkbox';
        $this->html = $this->completeTemplate();
        return $this->html;
    }

    /**
     * @param $attr
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function inspectionAttributes(&$attr)
    {
        $attr['value'] = isset($attr['value']) ? $attr['value'] : $this->config['default_value']['checkbox'];
        $this->generateClass($attr,$this->config['css']['checkboxClass']);
        $attr['class'] = $this->formatClass();
        if (empty($attr['value'])) {
            $val = $this->getValue($this->name)['value'];
            if (!is_array($val)) {
                $val = [$val];
            }
            if (in_array($attr['value'], $val)) {
                $attr['checked'] = true;
            }
        }
        $attr['class'] = $this->formatClass();
        if (isset($attr['type'])) {
            unset($attr['type']);
        }
        if (isset($attr['checked'])) {
            $attr['checked'] = 'checked';
        }
        if (isset($attr['multiple'])) {
            if (!strpos($this->name, '[]')) {
                $this->name .= '[]';
            }
            unset($attr['multiple']);
        }
        if (isset($attr['hidden']) && $attr['hidden'] == false) {
            unset($attr['hidden']);
        } else {

            if (ends_with($this->name, '[]')) {
                $hiddenName = str_ireplace('[]', '', $this->name);
            } else {
                $hiddenName = $this->name;
            }

            if (!$this->isHidden) {
                $this->isHide($hiddenName);
                $this->hidden = $this->setHidden($hiddenName, $this->config['default_value']['hidden']);
            } else {
                $this->isHide($hiddenName);
                $this->hidden = '';
            }
        }

        parent::inspectionAttributes($attr);
    }

    private function transform($field)
    {
        $arr = explode('[', $field);
        foreach ($arr as $key => $item) {
            $arr[$key] = rtrim($item, ']');
        }

        $field = implode('.', $arr);
        if (ends_with($field, '.')) {
            array_set($this->fields, substr($field, 0, -1), []);
        } elseif (str_contains('..', $field)) {
            dd('as');
        } else {
            array_set($this->fields, $field, '');
        }
    }

    private function isHide($name)
    {
//        if (strpos('[', $name)) {
//            $name = $this->transform($name);
//        }
        if ($this->in_array_r($name, array_keys($this->fixedField), true)) {
            $this->isHidden = true;
        } else {
            $this->isHidden = false;
        }
    }

    //TODO add system for multi checkbox created once hidden
    private function in_array_r($needle, $haystack, $strict = false)
    {
        foreach ($haystack as $item) {
            if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
                return true;
            }
        }

        return false;
    }

}