<?php
namespace LaraForm\Elements\Components\Inputs;

use AdamWathan\BootForms\Facades\BootForm;
use Illuminate\Support\Facades\Config;
use LaraForm\Elements\Element;

class Submit extends Element
{
    /**
     * @param string $label
     * @param array $options
     * @return mixed
     */
    public function toHtml($label = '', $options = [])
    {
        if (!$label) {
            if ($label === false) {
                $label = '';
            } else {
                $cfgLabel = __(Config::get('lara_form.label.submit', 'Save'));
                $label = !empty($cfgLabel) ? __($cfgLabel) : __('Submit');
            }
        }

        if (!isset($options['class'])) {
            $options['class'] = '';
        }

        if (!empty($options['btn'])) {

            if ($options['btn'] === true) {
                $options['btn'] = __(Config::get('lara_form.label.submit_btn', 'default'));;
            }

            $options['class'] .= ' btn btn-' . $options['btn'];
        }

        // @TODO - combine with Assistant::input
        if (!empty($options['position']) && $options['position'] == 'right') {
            $options['class'] .= ' icon-right';
        }

        $label = $this->getTitleIcon($label, $options);
        $submit = BootForm::submit($label);

        if (!empty($options['class'])) {
            $submit->addClass($options['class']);
        }
        unset($options['class']);

        foreach ($options as $k => $val) {
            $submit->attribute($k, $val);
        }

        return $submit;
    }

    /**
     * @param $title
     * @param $options
     * @return string
     */
    public function getTitleIcon($title, &$options)
    {
        $icon = false;
        if (!empty($options['icon'])) {
            $icon = '<i class="fa fa-fw fa-' . $options['icon'] . '"></i>';
            unset($options['icon']);
        }

        if ($icon) {
            if (!empty($options['position']) && $options['position'] == 'right') {
                $title .= $icon;
            } else {
                $title = $icon.$title;
            }

            unset($options['position']);
        }

        return $title;
    }


}