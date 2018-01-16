<?php

namespace LaraForm;

use Illuminate\Support\Facades\Config;
use LaraForm\Core\BaseFormBuilder;
use LaraForm\Stores\BindStore;
use LaraForm\Stores\ErrorStore;
use LaraForm\Stores\OldInputStore;
use LaraForm\Stores\OptionStore;
use LaraForm\Traits\FormControl;

/**
 * Creates objects of fields and displays them
 * Class FormBuilder
 * @package LaraForm
 */
class FormBuilder extends BaseFormBuilder
{
    use FormControl;

    /**
     * Keeped here object FormProtection
     * @var FormProtection
     */
    protected $formProtection;

    /**
     * Keeped here object ErrorStore
     * @var ErrorStore
     */
    protected $errorStore;

    /**
     * Keeped here object OldInputStore
     * @var OldInputStore
     */
    protected $oldInputStore;


    /**
     * Keeped here object OptionStore
     * @var OptionStore
     */
    protected $optionStore;

    /**
     * Keeped here object BindStore
     * @var BindStore
     */
    protected $bindStore;

    /**
     * Keeped here objects by  already created fields
     * @var array
     */
    protected $maked = [];

    /**
     * Keeped here model that was passed in form
     * @var $model
     */
    protected $model;

    /**
     * Designate the start and end of the form
     * @var bool
     */
    protected $isForm = false;

    /**
     * Keeped here object of the current field
     * @var widget
     */
    protected $widget;

    /**
     * Keeped modifications for the view template of one element
     * @var array
     */
    protected $inlineTemplates = [
        'pattern' => [],
        'div' => [],
        'label' => [],
        'class_concat' => true
    ];

    /**
     * Keeped modifications for the view templates inside in form
     * @var array
     */
    protected $localTemplates = [
        'pattern' => [],
        'div' => [],
        'label' => [],
        'class_concat' => true
    ];

    /**
     * Keeped modifications for the view templates inside in page
     * @var array
     */
    protected $globalTemplates = [
        'pattern' => [],
        'div' => [],
        'label' => [],
        'class_concat' => true
    ];

    /**
     * Accepts an objects and assigns the properties
     * FormBuilder constructor.
     * @param FormProtection $formProtection
     * @param ErrorStore $errorStore
     * @param OldInputStore $oldInputStore
     * @param OptionStore $optionStore
     * @param BindStore $bindStore
     */
    public function __construct(
        FormProtection $formProtection,
        ErrorStore $errorStore,
        OldInputStore $oldInputStore,
        OptionStore $optionStore,
        BindStore $bindStore
    ) {
        $this->formProtection = $formProtection;
        $this->errorStore = $errorStore;
        $this->oldInputStore = $oldInputStore;
        $this->optionStore = $optionStore;
        $this->bindStore = $bindStore;
    }

    /**
     * Opens the form, and begins to store data about the fields
     * Warning!
     * The attributes of the action and method must be passed in the second parameter or not transmitted at all!!!
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
        $token = $this->generateToken();
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
     * @return OptionStore|string
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \LogicException
     */
    public function end()
    {
        if (!$this->isForm) {
            return '';
        }

        $this->formProtection->confirm();
        $end = $this->make('form', ['end']);
        $this->resetProperties();
        return $end;
    }

    /**
     * Accepts changes for presentation templates within a form or on a page
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
     * @return mixed
     */
    public function __toString()
    {
        $params = $this->optionStore->getOprions();
        $this->hasTemplate($params);
        $data = $this->complateTemplatesAndParams();
        $this->widget->setArguments($params);
        $this->widget->setParams($data);
        $this->optionStore->resetOptions();

        return $this->widget->render();
    }

    /**
     * @param $method
     * @param array $arrgs
     * @return OptionStore
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \LogicException
     */
    public function __call($method, $arrgs = [])
    {
        $attr = !empty($arrgs[1]) ? $arrgs[1] : [];

        if (isset($attr['type'])) {
            // button types
            if (in_array($attr['type'], ['submit', 'reset', 'button'])) {
                $method = 'submit';
            }
            // other field types
            if (in_array($attr['type'], ['checkbox', 'radio', 'file', 'textarea', 'hidden', 'label'])) {
                $method = $attr['type'];
            }
        }

        if (isset($arrgs[0])) {
            $value = '';
            if ($method == 'hidden') {
                $value = isset($attr['value']) ? $attr['value'] : 0;
            }

            if (!empty($attr['readonly'])) {
                $val = $this->bindStore->get($arrgs[0]);
                $value = !empty($val) ? $val : '';
                $value = isset($attr['value']) ? $attr['value'] : $value;
            }

            if (!in_array($method, ['submit', 'button', 'reset', 'label']) && $this->isForm) {
                $this->formProtection->addField($arrgs[0], $attr, $value);
            }
        }

        $this->hasTemplate($arrgs);
        return $this->make($method, $arrgs);
    }

    /**
     * Instantiates field objects and returns an object OptionStore to create a chain
     * @param $method
     * @param $arguments
     * @return OptionStore
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \LogicException
     */
    protected function make($method, $arguments)
    {
        $modelName = ucfirst($method);
        $classNamspace = config('lara_form_core.method_full_name') . $modelName . config('lara_form_core.method_sufix');

        if (empty($this->maked[$modelName])) {
            $this->maked[$modelName] = app($classNamspace, [$this->errorStore, $this->oldInputStore]);
        }

        $this->widget = $this->maked[$modelName];

        if (!empty($this->model)) {
            $this->bindStore->setModel($this->model);
            $this->widget->binding($this->bindStore);
        }

        $this->optionStore->setAttributes($arguments);
        $this->optionStore->setBuilder($this);
        return $this->optionStore;
    }

    /**
     * Completing modifying templates and their parame
     * @return array
     */
    protected function complateTemplatesAndParams()
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
        $this->inlineTemplates['label'] = [];
        $this->inlineTemplates['class_concat'] = true;
        return $data;
    }

    /**
     * Checks whether the template modification has been transferred from a separate field
     * @param $attr
     */
    protected function hasTemplate(&$attr)
    {
        if (!empty($attr[1]['template'])) {
            $this->inlineTemplates['pattern'] = $attr[1]['template'];
            unset($attr[1]['template']);
        }

        if (isset($attr[1]['div'])) {
            $this->inlineTemplates['div'] = $attr[1]['div'];
            unset($attr[1]['div']);
        }
        if (isset($attr[1]['class_concat'])) {
            $this->inlineTemplates['class_concat'] = $attr[1]['class_concat'];
            unset($attr[1]['class_concat']);
        }
        if (!empty($attr[1]['label']) && is_array($attr[1]['label'])) {
            $this->inlineTemplates['label'] = $attr[1]['label'];
            unset($attr[1]['label']);
        }
    }

    /**
     * Remove proprties
     */
    protected function resetProperties()
    {
        $this->isForm = false;
        $this->maked = [];
        $this->localTemplates['pattern'] = [];
        $this->localTemplates['div'] = [];
        $this->localTemplates['label'] = [];
        $this->localTemplates['class_concat'] = true;
    }

    /**
     * locally or globally stores modifications and template parameters in properties
     * @param $data
     * @param $container
     * @param $options
     */
    protected function addTemplatesAndParams($data, &$container, $options)
    {
        if (!empty($options)) {
            if (isset($options['div'])) {
                $container['div'] = $options['div'];
            }
            if (isset($options['class_concat'])) {
                $container['class_concat'] = $options['class_concat'];
            }
            if (!empty($options['label']) && is_array($options['label'])) {
                if (isset($options['label']['text'])) {
                    unset($options['label']['text']);
                }
                $container['label'] = $options['label'];
            }
        }

        foreach ($data as $key => $value) {
            $container['pattern'][$key] = $value;
        }
    }

    /**
     * From the form parameters get a list of fields that should not be validated
     * @param $options
     * @return array|string
     * @throws \Exception
     */
    protected function getGeneralUnlockFieldsBy(&$options)
    {
        $unlockFields = [];

        if (!empty($options['_unlockFields'])) {
            $unlockFields = $this->formProtection->processUnlockFields($options['_unlockFields']);
            unset($options['_unlockFields']);
        }

        $unlockFields[] = '_token';
        $unlockFields[] = '_method';
        $unlockFields[] = config('lara_form.token_name', 'laraform_token');
        return $unlockFields;
    }

    /**
     * @return string
     * @throws \RuntimeException
     */
    protected function generateToken()
    {
        return md5(str_random(80));
    }
}
