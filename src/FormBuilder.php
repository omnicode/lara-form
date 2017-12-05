<?php

namespace LaraForm;

use Illuminate\Support\Facades\Config;
use LaraForm\Stores\BoundStore;
use LaraForm\Stores\ErrorStore;
use LaraForm\Stores\OldInputStore;

class FormBuilder
{

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
     * @var array
     */
    protected $templates = [
        'pattern' => [],
        'div' => [],
    ];

    /**
     * @var array
     */
    protected $globalTemplates = [
        'pattern' => [],
        'div' => [],
    ];

    /**
     * FormBuilder constructor.
     * @param FormProtection $formProtection
     * @param ErrorStore $errorStore
     * @param OldInputStore $oldInputStore
     */
    public function __construct(
        FormProtection $formProtection,
        ErrorStore $errorStore,
        OldInputStore $oldInputStore
    ) {
        $this->formProtection = $formProtection;
        $this->errorStore = $errorStore;
        $this->oldInputStore = $oldInputStore;
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
     * @param null $model
     * @param array $options
     * @return string
     * @throws \Exception
     * @throws \RuntimeException
     */
    public function create($model = null, $options = [])
    {
        $this->model = $model;
        $token = md5(str_random(80));
        $this->formProtection->setToken($token);
        $this->formProtection->setTime();
        $unlockFields = $this->getGeneralUnlockFieldsBy($options);
        $this->formProtection->setUnlockFields($unlockFields);
        $options['form_token'] = $token;
        return $this->makeSingleton('form', ['start', $options]);
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
            $unlockFields = $this->formProtection->processUnlockFields($options['_unlockFields']); // TODO use
            unset($options['_unlockFields']);
        }
        $unlockFields[] = '_token';
        $unlockFields[] = '_method';
        return $unlockFields;
    }

    /**
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \LogicException
     */
    public function end()
    {
        $this->formProtection->confirm();
        $end = $this->makeSingleton('form', ['end']);
        $this->maked = [];
        $this->templates = [];
        return $end;
    }

    /**
     * @param $method
     * @param $arrgs
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \LogicException
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
            if ($method !== 'submit') {
                $this->formProtection->addField($arrgs[0], $attr, $value);
            }
        }
        $this->hasTemplate($arrgs);
        return $this->makeSingleton($method, $arrgs);
    }

    /**
     * @param $method
     * @param $arrgs
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \LogicException
     */
    private function makeSingleton($method, $arrgs)
    {
        $modelName = ucfirst($method);
        $classNamspace = 'LaraForm\Elements\Components\\' . $modelName . 'Widget';

        if (!isset($this->maked[$modelName])) {
            $templates = [
                'local' => $this->templates['pattern'],
                'divLocal' => $this->templates['div'],
                'global' => $this->globalTemplates['pattern'],
                'divGlobal' => $this->globalTemplates['div'],
            ];
            $this->maked[$modelName] = app($classNamspace, [$this->errorStore, $this->oldInputStore, $templates]);
        }

        if (!empty($this->model)) {
            $this->maked[$modelName]->setModel($this->model);
        }

        return $this->maked[$modelName]->render($arrgs);
    }

    /**
     * @param $attr
     */
    private function hasTemplate(&$attr)
    {
        $templates = false;
        if (!empty($attr[1]['_template'])) {
            $templates = $attr[1]['_template'];
            unset($attr[1]['_template']);
        }
        if (!empty($attr[1]['_globalTemplate'])) {
            $templates = array_merge($attr[1]['_globalTemplate'], ['_global' => true]);
            unset($attr[1]['_globalTemplate']);
        }
        if (isset($attr[1]['_div'])) {
            $templates['_div'] = $attr[1]['_div'];
            unset($attr[1]['_div']);
        }
        if ($templates) {
            $this->setTemplate($templates);
        }
    }

    /**
     * @param $templateName
     * @param bool $templateValue
     * @param bool $global
     */
    public function setTemplate($templateName, $templateValue = false, $global = false)
    {
        if (is_array($templateName)) {
            if (!empty($templateName['_global'])) {
                unset($templateName['_global']);
                foreach ($templateName as $key => $value) {
                    $this->globalTemplates['pattern'][$key] = $value;
                }
                if (isset($templateName['_div'])) {
                    $this->globalTemplates['div'] = $templateName['_div'];
                    unset($templateName['_div']);
                }
            } else {
                foreach ($templateName as $key => $value) {
                    $this->templates['pattern'][$key] = $value;
                }
                if (isset($templateName['_div'])) {
                    $this->templates['div'] = $templateName['_div'];
                    unset($templateName['_div']);
                }
            }
        } elseif ($templateValue) {
            if ($global) {
                $this->globalTemplates['pattern'][$templateName] = $templateValue;
            } else {
                $this->templates['pattern'][$templateName] = $templateValue;
            }
        }

    }
}
