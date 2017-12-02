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
        $formHtml = $this->makeSingleton('form', ['start', $options]);
        $token = md5(str_random(80));
        $this->formProtection->setToken($token);

        $unlockFields = $this->getGeneralUnlockFieldsBy($options);
        $unlockFields[] = '_token';

        $method = $this->getMethodBy($model, $options);
        if ($method) {
            $unlockFields[] = '_method';
        }

        $this->formProtection->setUnlockFields($unlockFields);
        $hidden = '';
        if ($method != 'get') {
            $hidden = $this->hidden(config('lara_form.label.form_protection', 'laraform_token'), ['value' => $token]);
        }
        return $formHtml . $hidden;
    }

    /**
     * @param $model
     * @param $options
     * @param bool $unSet
     * @return null|string
     */
    protected function getMethodBy($model, &$options, $unSet = true)
    {
        $method = null;
        if (isset($options['method'])) {
            if (in_array($options['method'], ['get', 'post', 'put', 'patch', 'delete'])) {
                $method = $options['method'];
            }
            if ($unSet) {
                unset($options['method']);
            }
        } elseif (!empty($model)) {
            $method = 'put';
        }

        return $method;
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
            if (in_array($attr['type'], ['checkbox', 'radio', 'submit', 'file'])) {
                $method = $attr['type'];
            }
        }
        if (isset($arrgs[0])) {
            $value = '';
            if ($method == 'hidden') {
                $value = isset($attr['value']) ? $attr['value'] : 0;
            }

            $this->formProtection->addField($arrgs[0], $attr, $value);
        }

        return $this->makeSingleton($method, $arrgs);
    }

    /**
     * @param $method
     * @param $arrgs
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \LogicException
     */
    public function makeSingleton($method, $arrgs)
    {
        $modelName = ucfirst($method);
        $classNamspace = 'LaraForm\Elements\Components\\' . $modelName . 'Widget';
        if (!isset($this->maked[$modelName])) {
            $this->maked[$modelName] = app($classNamspace, [$this->errorStore, $this->oldInputStore, $arrgs]);
        }
        if (!empty($this->model)) {
            $this->maked[$modelName]->setModel($this->model);
        }
        return $this->maked[$modelName]->render($arrgs);
    }
}
