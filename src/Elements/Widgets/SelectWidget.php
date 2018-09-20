<?php
declare(strict_types=1);

namespace LaraForm\Elements\Widgets;

use LaraForm\Elements\Widget;
use function Symfony\Component\Debug\Tests\testHeader;

/**
 * Processes and creates select tag
 * Class SelectWidget
 * @package LaraForm\Elements\Widgets
 */
class SelectWidget extends Widget
{
    /**
     * Keeped here select template
     * @var string
     */
    protected $selectTemplate;

    /**
     * Keeped here selected options
     * @var bool
     */
    protected $selected = [];

    /**
     * Keeped here disabled options
     * @var array
     */
    protected $optionDisabled = [];

    /**
     * Keeped here disabled group
     * @var array
     */
    protected $groupDisabled = [];

    /**
     * Keeped here options
     * @var array
     */
    protected $optionsArray = [];


    /**
     * Returns the finished select tag html view
     * @return mixed|string|void
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function render(): string
    {
        $this->checkAttributes($this->attr);
        $optionsHtml = $this->renderOptions();

        $selectAttrs = [
            'content' => $optionsHtml,
            'name' => $this->name,
            'attrs' => $this->formatAttributes($this->attr)
        ];
        $this->setHtmlAttributes('type', 'select');
        $this->currentTemplate = $this->getTemplate('selectContainer');
        $this->html = $this->formatTemplate($this->selectTemplate, $selectAttrs);
        $this->resetAttributes();
        return $this->completeTemplate();
    }

    /**
     * @param $attr
     * @return mixed|void
     */
    public function checkAttributes(array &$attr): void
    {
        $this->generateId($attr);
        $this->generateLabel($attr);
        $this->generateClass($attr, $this->config['css']['class']['select']);
        $this->multipleByBrackets($attr);

        if (isset($attr['multiple'])) {
            $this->selectTemplate = $this->getTemplate('selectMultiple');
            if (empty($attr['disabled'])) {
                $this->hidden = $this->setHidden($this->name);
            }
            unset($attr['multiple']);
        } else {
            $this->selectTemplate = $this->getTemplate('select');
        }

        if (!empty($attr['options'])) {
            $this->optionsArray = is_array($attr['options']) ? $attr['options'] : [$attr['options']];
            unset($attr['options']);
        } else {
            $this->optionsArray = [];
        }

        if (!empty($attr['empty'])) {
            $this->optionsArray = ['' => $attr['empty']] + $this->optionsArray;
            unset($attr['empty']);
        } elseif (!isset($attr['empty']) || (isset($attr['empty']) && $attr['empty'] !== false)) {
            $emptyValue = $this->config['text']['select_empty'];

            if ($emptyValue) {
                $this->optionsArray = ['' => $emptyValue] + $this->optionsArray;
            }
        }

        if (isset($attr['selected'])) {
            $this->selected = $this->strToArray($attr['selected']);
            unset($attr['selected']);
        } else {
            $val = $this->getValue($this->name)['value'];
            if (!empty($val)) {
                $this->selected = $this->strToArray($val);
            }
        }

        if (isset($attr['disabled']) && $attr['disabled'] !== false) {
            $attr['disabled'] = 'disabled';
        }

        $this->disabledBy('option_disabled', $attr, $this->optionDisabled);
        $this->disabledBy('group_disabled', $attr, $this->groupDisabled);
        $this->parentCheckAttributes($attr);
    }

    /**
     * Creates html option tags for select
     * @param bool $gropup
     * @return bool|string
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function renderOptions(?array $gropup = null): string
    {
        $optionTemplate = $this->getTemplate('option');
        $options = empty($gropup) ? $this->optionsArray: $gropup;
        $options = $this->strToArray($options);
        $optionsHtml = '';
        foreach ($options as $index => $option) {
            $optAttrs = [];
            if (is_array($option)) {
                $optionsHtml .= $this->renderOptgroups($index, $option);
                continue;
            }
            $optAttrs += $this->isDisabled($index);
            $optAttrs += $this->isSelected($index);
            $rep = [
                'text' => $option,
                'value' => $index,
                'attrs' => $this->formatAttributes($optAttrs)
            ];
            $optionsHtml .= $this->formatTemplate($optionTemplate, $rep);
        }
        return $optionsHtml;
    }

    /**
     * Creates html group option tags for select
     * @param $groupName
     * @param $options
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function renderOptgroups(string $groupName, array $options): string
    {
        $optgroupTemplate = $this->getTemplate('optgroup');
        $childOptionsHtml = $this->renderOptions($options);
        $groupAttrs = [];

        if (!empty($this->groupDisabled)) {
            $groupAttrs += $this->isDisabled($groupName, $this->groupDisabled);
        }

        $rep = [
            'label' => $this->getLabelName($groupName),
            'content' => $childOptionsHtml,
            'attrs' => $this->formatAttributes($groupAttrs)
        ];
        return $this->formatTemplate($optgroupTemplate, $rep);
    }

    /**
     * @param $str
     * @param array $disabled
     * @return array
     */
    protected function isDisabled($str, array $disabled = []): array
    {
        if (empty($disabled)) {
            $disabled = $this->optionDisabled;
        }
        $arr = [];
        if (in_array($str, $disabled)) {
            $arr['disabled'] = 'disabled';
        }
        return $arr;
    }

    /**
     * @param $str
     * @return array
     */
    protected function isSelected($str): array
    {
        $arr = [];
        if (in_array($str, $this->selected)) {
            $arr['selected'] = 'selected';
        }
        return $arr;
    }

    /**
     * @param $type
     * @param $attr
     * @param $container
     */
    protected function disabledBy(string $type, &$attr, &$container): void
    {
        if (isset($attr[$type]) && $attr[$type] !== false) {
            $container = $this->strToArray($attr[$type]);
            unset($attr[$type]);
        }
    }

    /**
     * Reset current select attributes
     */
    protected function resetAttributes(): void
    {
        $this->selected = [];
        $this->optionDisabled = [];
        $this->optionsArray = [];
        $this->groupDisabled = [];
    }
}
