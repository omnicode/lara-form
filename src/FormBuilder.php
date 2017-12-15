<?php

namespace LaraForm;

use Illuminate\Support\Facades\Config;
use LaraForm\Core\BaseFormBuilder;
use LaraForm\Stores\BoundStore;
use LaraForm\Stores\ErrorStore;
use LaraForm\Stores\OldInputStore;
use LaraForm\Stores\OptionStore;
use LaraForm\Traits\FormControl;

class FormBuilder extends BaseFormBuilder
{
    use FormControl;

    /**
     * @var FormProtection
     */
    protected $formProtection;

    /**
     * @var ErrorStore
     */
    protected $errorStore;

    /**
     * @var OldInputStore
     */
    protected $oldInputStore;

    /**
     * @var array
     */
    protected $maked = [];

    /**
     * @var $model
     */
    protected $model;

    /**
     * @var bool
     */
    protected $isForm = false;

    /**
     * @var array
     */
    protected $localTemplates = [
        'pattern' => [],
        'div' => [],
        'class_concat' => true
    ];

    /**
     * @var array
     */
    protected $globalTemplates = [
        'pattern' => [],
        'div' => [],
        'class_concat' => true
    ];

    /**
     * @var array
     */
    protected $inlineTemplates = [
        'pattern' => [],
        'div' => [],
        'class_concat' => true
    ];

    /**
     * @var
     */
    protected $widget;

    /**
     * @var OptionStore
     */
    protected $optionStore;

    /**
     * FormBuilder constructor.
     * @param FormProtection $formProtection
     * @param ErrorStore $errorStore
     * @param OldInputStore $oldInputStore
     * @param OptionStore $optionStore
     */
    public function __construct(
        FormProtection $formProtection,
        ErrorStore $errorStore,
        OldInputStore $oldInputStore,
        OptionStore $optionStore
    ) {
        $this->formProtection = $formProtection;
        $this->errorStore = $errorStore;
        $this->oldInputStore = $oldInputStore;
        $this->optionStore = $optionStore;
    }

    /**
     * @param null $model
     * @param array $options
     * @return mixed
     * @throws \Exception
     */
    public function create($model = null, $options = [])
    {
        if ($this->isForm) {
            abort(300, 'Your action is not correct, have is open and not closed tag form!');
        }

        $this->model = $model;
        $this->isForm = true;
        $token = md5(str_random(80));
        $action = $this->getAction($options);
        $method = $this->getMethod($options);
        $options['_form_token'] = $token;
        $options['_form_action'] = $action;
        $options['_form_method'] = $method;

        $form = $this->make('form', ['start', $options]);


        if ($method === 'get') {
            return $form;
        }

        $this->formProtection->setToken($token);
        $this->formProtection->setTime();
        $this->formProtection->setUrl($action);
        $this->formProtection->removeByTime();
        $this->formProtection->removeByCount();
        $unlockFields = $this->getGeneralUnlockFieldsBy($options);
        $this->formProtection->setUnlockFields($unlockFields);

        return $form;
    }

    /**
     * @return mixed
     */
    public function end()
    {
        $this->formProtection->confirm();
        $end = $this->make('form', ['end']);
        $this->resetOldData();
        return $end;
    }

    /**
     * @param $templateName
     * @param bool $templateValue
     * @param bool $global
     */
    public function setTemplate($templateName, $templateValue = false, $global = false)
    {
        if (is_array($templateName)) {
            $options = [];

            if (!empty($templateName['_options'])) {
                $options = $templateName['_options'];
                unset($templateName['_options']);
            }

            if (!empty($options['global'])) {
                $this->addTemplatesAndParams($templateName, $this->globalTemplates, $options);
            } else {
                $this->addTemplatesAndParams($templateName, $this->localTemplates, $options);
            }
        } elseif ($templateValue) {

            if ($global) {
                $this->globalTemplates['pattern'][$templateName] = $templateValue;
            } else {
                $this->localTemplates['pattern'][$templateName] = $templateValue;
            }
        }
    }

    /**
     * @param $method
     * @param $arrgs
     * @return mixed
     */
    public function __call($method, $arrgs = [])
    {
        $attr = !empty($arrgs[1]) ? $arrgs[1] : [];

        if (isset($attr['type'])) {

            if (in_array($attr['type'], ['checkbox', 'radio', 'submit', 'file', 'textarea', 'hidden'])) {
                $method = $attr['type'];
            }
        }

        if (isset($arrgs[0])) {
            $value = '';

            if ($method == 'hidden') {
                $value = isset($attr['value']) ? $attr['value'] : config('lara_form.default_value.hidden');
            }

            if (!in_array($method, ['submit', 'button', 'reset', 'label']) && $this->isForm) {
                $this->formProtection->addField($arrgs[0], $attr, $value);
            }
        }

        $this->hasTemplate($arrgs);
        return $this->make($method, $arrgs);
    }


    /**
     * @param $data
     * @return bool
     */
    protected function validate($data)
    {
        return $this->formProtection->validate($data);
    }

    /**
     * @param $method
     * @param $arguments
     * @return mixed
     */
    private function make($method, $arguments)
    {
        $modelName = ucfirst($method);
        $classNamspace = config('lara_form_core.method_full_name') . $modelName . config('lara_form_core.method_sufix');

        if (empty($this->maked[$modelName])) {
            $this->maked[$modelName] = app($classNamspace, [$this->errorStore, $this->oldInputStore]);
        }

        $this->widget = $this->maked[$modelName];

        if (!empty($this->model)) {
            $this->widget->setModel($this->model);
        }

        $this->optionStore->attr($arguments);
        $this->optionStore->setBuilder($this);
        return $this->optionStore;
    }


    /**
     * @return mixed
     */
    public function __toString()
    {
        $data = $this->complateTemplatesAndParams();
        $this->widget->setArguments($this->optionStore->getOprions());
        $this->widget->setParams($data);
        $this->optionStore->resetOptions();

        return $this->widget->render();
    }


    /**
     * @return array
     */
    private function complateTemplatesAndParams()
    {
        $data = [
            // for once filed
            'inline' => $this->inlineTemplates,
            // for fields in form
            'local' => $this->localTemplates,
            // for all page
            'global' => $this->globalTemplates,
        ];

        $this->inlineTemplates['pattern'] = [];
        $this->inlineTemplates['div'] = [];
        $this->inlineTemplates['class_concat'] = true;
        return $data;
    }

    /**
     * @param $options
     * @return array|string
     * @throws \Exception
     */
    private function getGeneralUnlockFieldsBy(&$options)
    {
        $unlockFields = [];

        if (!empty($options['_unlockFields'])) {
            $unlockFields = $this->formProtection->processUnlockFields($options['_unlockFields']);
            unset($options['_unlockFields']);
        }

        $unlockFields[] = '_token';
        $unlockFields[] = '_method';
        $unlockFields[] = config('lara_form.label.form_protection', 'laraform_token');
        return $unlockFields;
    }

    /**
     * @param $attr
     */
    private function hasTemplate(&$attr)
    {
        if (!empty($attr[1]['template'])) {
            $this->inlineTemplates['pattern'] = $attr[1]['template'];
            unset($attr[1]['template']);
        }

        if (isset($attr[1]['div'])) {
            $this->inlineTemplates['div'] = $attr[1]['div'];
            unset($attr[1]['div']);
        }
    }

    /**
     *
     */
    private function resetOldData()
    {
        $this->isForm = false;
        $this->maked = [];
        $this->localTemplates['pattern'] = [];
        $this->localTemplates['div'] = [];
    }

    /**
     * @param $data
     * @param $container
     * @param $options
     */
    private function addTemplatesAndParams($data, &$container, $options)
    {
        if (!empty($options)) {
            if (isset($options['div'])) {
                $container['div'] = $options['div'];
            }
            if (isset($options['class_concat'])) {
                $container['class_concat'] = $options['class_concat'];
            }
        }

        foreach ($data as $key => $value) {
            $container['pattern'][$key] = $value;
        }
    }
}
