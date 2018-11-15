<?php
declare(strict_types=1);

namespace LaraForm\Elements\Widgets;

use LaraForm\Elements\Widget;

/**
 * Processes and creates input tags
 * Class BaseInputWidget
 * @package LaraForm\Elements\Widgets
 */
class BaseInputWidget extends Widget
{
    /**
     * Returns the finished html view
     * @return string
     */
    public function render(): string
    {
        $this->checkAttributes($this->attr);
        return $this->formatInputField($this->name, $this->attr);
    }

    /**
     * @param $attr
     * @return mixed|void
     */
    public function checkAttributes(array &$attr): void
    {
        parent::checkAttributes($attr);
    }

    /**
     * @return string
     */
    protected function parentRender()
    {
        return self::render();
    }

    /**
     * Formats input fields according to a given template or by default
     * @param $name
     * @param $attr
     * @param bool $cTemplate
     * @return mixed|string
     */
    protected function formatInputField(string $name, array $attr, ?string $cTemplate = null): string
    {
        if (empty($cTemplate)) {
            $template = $this->getTemplate('input');
        } else {
            $template = $cTemplate;
        }

        $this->generalCheckAttributes($attr, $cTemplate);
        $this->setHtmlAttributes('name', $name);
        $this->setHtmlAttributes('attrs', $this->formatAttributes($attr));
        $this->html = $this->formatTemplate($template, $this->getHtmlAttributes());
        return $this->completeTemplate();
    }

    /**
     * Formats the fields inside the label field
     * @param $template
     * @param $attr
     * @param array $labelAttrs
     * @return mixed|string
     */
    protected function formatNestingLabel(string $template, array $attr, array $labelAttrs = []): string
    {
        $anonymous = true;
        $text = '';

        if (isset($attr['label_text']) && $attr['label_text'] === false) {
            $anonymous = false;
        }
        $icon = $this->icon;
        $hidden = $this->hidden;
        $this->icon = '';
        $this->formatInputField($this->name, $attr, $template);

        if (!empty($attr['type'])) {
            $this->setOtherHtmlAttributes('type', $attr['type']);
            unset($attr['type']);
        }
        if ($anonymous) {
            $text = !empty($attr['label']) ? $attr['label'] : $this->getLabelName($this->name);
        }

        $templateAttr = [
            'hidden' => $hidden,
            'content' => $this->html,
            'text' => $text,
            'icon' => $icon,
            'attrs' => $this->formatAttributes($labelAttrs)
        ];

        $labelTemplate = $this->getTemplate('nestingLabel');
        $this->html = $this->formatTemplate($labelTemplate, $templateAttr);
        return $this->completeTemplate();
    }

    /**
     * Checks and modifies the attributes that were passed in the field
     *
     * @param $attr
     * @param $cTemplate
     */
    protected function generalcheckAttributes(array &$attr, ?string $cTemplate): void
    {
        if (!empty($attr['type'])) {
            $this->setHtmlAttributes('type', $attr['type']);
            unset($attr['type']);
        } else {
            $this->setHtmlAttributes('type', 'text');
        }

        $this->setHtmlAttributes('value', '');
        if (!empty($attr['value']) && $cTemplate) {
            $this->setHtmlAttributes('value', $attr['value']);
            unset($attr['value']);
        }

        $idNotFor = ['radio', 'checkbox', 'label'];
        $labelNotFor = ['hidden', 'submit', 'reset', 'button'];
        if (!in_array($this->getHtmlAttributes('type'), array_merge($idNotFor,$labelNotFor)) && !$cTemplate) {
            $attr += $this->getValue($this->name);
        }

        $this->generateId($attr);

        if (!in_array($this->getHtmlAttributes('type'), $labelNotFor)) {
            $this->generateLabel($attr);
            $this->generatePlaceholder($attr);
        }

        $type = $this->getHtmlAttributes('type');
        if ($type !== 'hidden') {
            $defaultClass = $this->config['css']['class'][$type] ?? false;
            $this->generateClass($attr, $defaultClass);
        }
        $this->assignOtherhtmlAtrributes($attr);
        $this->parentCheckAttributes($attr);
    }

}