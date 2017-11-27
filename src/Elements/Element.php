<?php
namespace LaraForm\Elements;

use Cake\Utility\Inflector;

abstract class Element
{
    /**
     * @param $name
     * @param array $options
     * @param bool $unset
     * @return bool|\Illuminate\Contracts\Translation\Translator|mixed|string|\Symfony\Component\Translation\TranslatorInterface
     */
    public function getLabel($name, &$options = [], $unset = true)
    {
        $label = false;
        if (isset($options['label'])) {
            if ($options['label'] !== false) {
                $label = $options['label'];
            }
            if ($unset) {
                unset($options['label']);
            }
        } else {
            $label = __(Inflector::humanize(str_replace('_id', '', $name)));
        }

        return $label;
    }

    abstract function toHtml($name, $options = []);

    protected function format($name, array $data = [])
    {
        if (!isset($this->_compiled[$name])) {
            throw new RuntimeException("Cannot find template named '$name'.");
        }
        list($template, $placeholders) = $this->_compiled[$name];

        if (isset($data['templateVars'])) {
            $data += $data['templateVars'];
            unset($data['templateVars']);
        }
        $replace = [];
        foreach ($placeholders as $placeholder) {
            $replacement = isset($data[$placeholder]) ? $data[$placeholder] : null;
            if (is_array($replacement)) {
                $replacement = implode('', $replacement);
            }
            $replace[] = $replacement;
        }

        return vsprintf($template, $replace);
    }
}
