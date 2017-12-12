<?php

namespace LaraForm\Elements\Components;

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
        $this->parseParams($option);
        return $this->renderCheckbox($this->attr);
    }

    /**
     * @param $attr
     * @return string
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    private function renderCheckbox($attr)
    {
        if (strpos($this->name, '[]')) {
            $attr['multiple'] = true;
        }
        $template = $this->getTemplate('checkbox');
        $this->inspectionAttributes($attr);
        $this->containerTemplate = $this->getTemplate('checkboxContainer');
        $this->otherHtmlAttributes['type'] = 'checkbox';
        return $this->formatNestingLabel($template, $attr);
    }

    /**
     * @param $attr
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function inspectionAttributes(&$attr)
    {
        $attr['value'] = isset($attr['value']) ? $attr['value'] : $this->config['default_value']['checkbox'];
        $this->generateClass($attr, $this->config['css']['checkboxClass']);
        $this->generateLabel($attr);
        if (empty($attr['value'])) {
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