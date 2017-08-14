<?php
namespace LaraForm\Elements;

use Cake\Utility\Inflector;

abstract class Element
{
    /**
     * @param $name
     * @param $options
     * @return bool|null|string|\Symfony\Component\Translation\TranslatorInterface
     */
    public function getLabel($name, &$options = [])
    {
        $label = false;
        if (isset($options['label'])) {
            if ($options['label'] !== false) {
                $label = $options['label'];
            }
            unset($options['label']);
        } else {
            $label = __(Inflector::humanize(str_replace('_id', '', $name)));
        }

        return $label;
    }

    abstract function toHtml($name, $options = []);
}
