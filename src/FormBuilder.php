<?php

namespace LaraForm;

use Illuminate\Support\Facades\Config;
use LaraForm\Core\BaseFormBuilder;
use LaraForm\Stores\BoundStore;
use LaraForm\Stores\ErrorStore;
use LaraForm\Stores\OldInputStore;

class FormBuilder extends BaseFormBuilder
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
     * @var bool
     */
    protected $isForm = false;

    /**
     * @var array
     */
    protected $localTemplates = [
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
     * @var array
     */
    protected $inlineTemplates = [
        'pattern' => [],
        'div' => [],
        'group' => false
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
        if ($this->isForm) {
            abort(300,'Your action is not correct ,have is open and not closed tag form!');
        }
        $this->model = $model;
        $this->isForm = true;
        $token = md5(str_random(80));
        $options['form_token'] = $token;
        $formData = $this->makeSingleton('form', ['start', $options]);
        $formHtml = $formData['html'];

        if ($formData['method'] === 'get') {
            return $formHtml;
        }

        $this->formProtection->setToken($token);
        $this->formProtection->setTime();
        $this->formProtection->setUrl($formData['action']);
        $this->formProtection->removeByTime();
        $this->formProtection->removeByCount();
        $unlockFields = $this->getGeneralUnlockFieldsBy($options);
        $this->formProtection->setUnlockFields($unlockFields);
        return $formHtml;
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
        $unlockFields[] = config('lara_form.label.form_protection', 'laraform_token');
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
        $this->isForm = false;
        $this->maked = [];
        $this->localTemplates['pattern'] = [];
        $this->localTemplates['div'] = [];
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
            if (!in_array($method, ['submit', 'button', 'reset', 'label']) && $this->isForm) {
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
        $classNamspace = config('lara_form_core.method_full_name') . $modelName . config('lara_form_core.method_sufix');

        if (!isset($this->maked[$modelName])) {
            $this->maked[$modelName] = app($classNamspace, [$this->errorStore, $this->oldInputStore]);
        }

        if (!empty($this->model)) {
            $this->maked[$modelName]->setModel($this->model);
        }

        if (!empty($this->formProtection->fields)) {
            $this->maked[$modelName]->setFixedField($this->formProtection->fields);
        }

        $templates = [
            // for fields in form
            'local' => $this->localTemplates['pattern'],
            'divLocal' => $this->localTemplates['div'],
            // for all page
            'global' => $this->globalTemplates['pattern'],
            'divGlobal' => $this->globalTemplates['div'],
            // for once filed
            'inline' => $this->inlineTemplates['pattern'],
            'divInline' => $this->inlineTemplates['div'],
        ];

        $this->inlineTemplates['pattern'] = [];
        $this->inlineTemplates['div'] = [];

        $this->maked[$modelName]->setParams($templates);
        return $this->maked[$modelName]->render($arrgs);
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
     * @param $templateName
     * @param bool $templateValue
     * @param bool $global
     */
    public function setTemplate($templateName, $templateValue = false, $global = false)
    {
        if (is_array($templateName)) {
            if (isset($templateName['_options']['div'])) {
                if (!empty($templateName['_options']['global'])) {
                    $this->globalTemplates['div'] = $templateName['_options']['div'];
                } else {
                    $this->localTemplates['div'] = $templateName['_options']['div'];
                }
            }
            if (!empty($templateName['_options']['global'])) {
                unset($templateName['_options']['global']);
                foreach ($templateName as $key => $value) {
                    $this->globalTemplates['pattern'][$key] = $value;
                }
            } else {
                foreach ($templateName as $key => $value) {
                    $this->localTemplates['pattern'][$key] = $value;
                }
            }
        } elseif ($templateValue) {
            if ($global) {
                $this->globalTemplates['pattern'][$templateName] = $templateValue;
            } else {
                $this->localTemplates['pattern'][$templateName] = $templateValue;
            }
        }
    }
}
