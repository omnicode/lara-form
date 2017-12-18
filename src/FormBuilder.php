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
 *
 * Class FormBuilder
 * @package LaraForm
 */
class FormBuilder extends BaseFormBuilder
{
    use FormControl;

    /**
     * Keeped here object FormProtection
     *
     * @var FormProtection
     */
    protected $formProtection;

    /**
     * Keeped here object ErrorStore
     *
     * @var ErrorStore
     */
    protected $errorStore;

    /**
     * Keeped here object OldInputStore
     *
     * @var OldInputStore
     */
    protected $oldInputStore;

    /**
     * Keeped here objects by  already created fields
     *
     * @var array
     */
    protected $maked = [];

    /**
     * Keeped here model that was passed in form
     *
     * @var $model
     */
    protected $model;

    /**
     * Designate the start and end of the form
     *
     * @var bool
     */
    protected $isForm = false;

    /**
     * Keeped here object of the current field
     *
     * @var widget
     */
    protected $widget;

    /**
     * Keeped here object OptionStore
     *
     * @var OptionStore
     */
    protected $optionStore;

    /**
     * Keeped modifications for the view template of one element
     *
     * @var array
     */
    protected $inlineTemplates = [
        'pattern' => [],
        'div' => [],
        'class_concat' => true
    ];

    /**
     * Keeped modifications for the view templates inside in form
     *
     * @var array
     */
    protected $localTemplates = [
        'pattern' => [],
        'div' => [],
        'class_concat' => true
    ];

    /**
     * Keeped modifications for the view templates inside in page
     *
     * @var array
     */
    protected $globalTemplates = [
        'pattern' => [],
        'div' => [],
        'class_concat' => true
    ];

    /**
     * Accepts an objects and assigns the properties
     *
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
     * Opens the form, and begins to store data about the fields
     * Warning!
     * The attributes of the action and method must be passed in the second parameter or not transmitted at all!!!
     *
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
     * Closes the form
     *
     * @return mixed
     */
    public function end()
    {
        $this->formProtection->confirm();
        $end = $this->make('form', ['end']);
        $this->resetProperties();
        return $end;
    }

    /**
     * Accepts changes for presentation templates within a form or on a page
     *
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
        $data = $this->complateTemplatesAndParams();
        $this->widget->setArguments($this->optionStore->getOprions());
        $this->widget->setParams($data);
        $this->optionStore->resetOptions();

        return $this->widget->render();
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
     * Instantiates field objects and returns an object OptionStore to create a chain
     *
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
     * Completing modifying templates and their parame
     *
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
     * Checks whether the template modification has been transferred from a separate field
     *
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
     * Remove proprties
     */
    private function resetProperties()
    {
        $this->isForm = false;
        $this->maked = [];
        $this->localTemplates['pattern'] = [];
        $this->localTemplates['div'] = [];
    }

    /**
     * locally or globally stores modifications and template parameters in properties
     *
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

    /**
     * From the form parameters get a list of fields that should not be validated
     *
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
        $unlockFields[] = config('lara_form.token_name', 'laraform_token');
        return $unlockFields;
    }
}
