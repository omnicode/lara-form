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
    protected $templates = [];

    /**
     * @var array
     */
    protected $globalTemplates = [];

    protected $token;
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
        $options['form_token'] = $token;
        $this->token = $token;
        $unlockFields[] = '_token';
        $unlockFields[] = '_method';
        $this->formProtection->setUnlockFields($unlockFields);

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
        $this->maked = [];
        $this->templates = [];
        return $this->makeSingleton('form', ['end']);
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
            if (in_array($attr['type'], ['checkbox', 'radio', 'submit', 'file', 'textarea'])) {
                $method = $attr['type'];
            }
        }
        if (isset($arrgs[0])) {
            $value = '';
            if ($method == 'hidden') {
                $value = isset($attr['value']) ? $attr['value'] : 0;
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
                'local' => $this->templates,
                'global' => $this->globalTemplates,
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
        if (!empty($attr[1]['template'])) {
            $templates = $attr[1]['template'];
            unset($attr[1]['template']);
        }
        if (!empty($attr[1]['globalTemplate'])) {
            $templates = array_merge($attr[1]['globalTemplate'], ['_global' => true]);
            unset($attr[1]['globalTemplate']);
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
                    $this->globalTemplates[$key] = $value;
                }
            } else {
                foreach ($templateName as $key => $value) {
                    $this->templates[$key] = $value;
                }
            }
        } elseif ($templateValue) {
            if ($global) {
                $this->globalTemplates[$templateName] = $templateValue;
            } else {
                $this->templates[$templateName] = $templateValue;
            }
        }
    }
}
