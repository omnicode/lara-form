<?php
declare(strict_types=1);

namespace LaraForm;

use Illuminate\Database\Eloquent\Model;
use LaraForm\Core\BaseFormBuilder;
use LaraForm\Stores\BindStore;
use LaraForm\Stores\ErrorStore;
use LaraForm\Stores\OldInputStore;
use LaraForm\Stores\OptionStore;
use LaraForm\Stores\TranslatorStore;
use LaraForm\Traits\FormControl;

/**
 * Creates objects of fields and displays them
 * Class FormBuilder
 *
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
     * Keeped here object OptionStore
     *
     * @var OptionStore
     */
    protected $optionStore;

    /**
     * Keeped here object BindStore
     *
     * @var BindStore
     */
    protected $bindStore;

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
     * @var object
     */
    protected $translator;

    /**
     * @var array
     */
    protected $templateDefaultParams = [
        'pattern' => [],
        'div' => [],
        'label' => [],
        'class_concat' => true,
        'escept' => false
    ];

    /**
     * Keeped modifications for the view template of one element
     *
     * @var array
     */
    protected $inlineTemplates = [];

    /**
     * Keeped modifications for the view templates inside in form
     *
     * @var array
     */
    protected $localTemplates = [];

    /**
     * Keeped modifications for the view templates inside in page
     *
     * @var array
     */
    protected $globalTemplates = [];

    /**
     * Accepts an objects and assigns the properties
     * FormBuilder constructor.
     *
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
        $this->localTemplates = $this->templateDefaultParams;
        $this->globalTemplates = $this->templateDefaultParams;
        $this->inlineTemplates = $this->templateDefaultParams;
    }

    /**
     * Opens the form, and begins to store data about the fields
     * Warning!
     * The attributes of the action and method must be passed in the second parameter or not transmitted at all!!!
     *
     * @param null $model
     * @param array $options
     *
     * @return mixed
     * @throws \Exception
     */
    public function create(?Model $model = null, array $options = []): OptionStore
    {
        $this->model = $model;
        $this->setIsForm(true);
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
        $this->formProtection->setTime(time());
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
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function end(): string
    {
        if (!$this->isForm) {
            return '';
        }

        $this->formProtection->confirm();
        $end = $this->make('form', ['end']);
        $this->resetProperties();
        if (!empty($this->translator)) {
            $this->translator->put();
        }
        return $end . '';
    }

    /**
     * Accepts changes for presentation templates within a form or on a page
     *
     * @param $templateName
     * @param bool $templateValue
     * @param bool $global
     */
    public function setTemplate($templateName, ?string $templateValue = null, bool $global = false): void
    {
        if (is_array($templateName)) {
            $options = [];

            if (!empty($templateName['_options'])) {
                $options = $templateName['_options'];
                unset($templateName['_options']);
            }

            if (empty($options['global'])) {
                $this->addTemplatesAndParams($templateName, $this->localTemplates, $options);
            } else {
                $this->addTemplatesAndParams($templateName, $this->globalTemplates, $options);
            }
        } elseif (!empty($templateValue)) {

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
    public function output(): string
    {
        $args = $this->optionStore->getOptions();
        $this->hasTemplate($args);
        $data = $this->complateTemplatesAndParams();
        $this->widget->setArguments($args);
        $this->widget->setParams($data);
        $this->optionStore->resetOptions();
        return $this->widget->render();
    }

    /**
     * @param $method
     * @param array $arrgs
     *
     * @return OptionStore
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \LogicException
     */
    public function __call(string $method, array $arrgs = []): OptionStore
    {
        $attr = !empty($arrgs[1]) ? $arrgs[1] : [];
        $method = $this->getFieldType($attr, $method);
        $this->fixField($arrgs, $attr, $method);
        $this->hasTemplate($arrgs);
        return $this->make($method, $arrgs);
    }

    /**
     * @param $attr
     * @param $default
     *
     * @return string
     */
    protected function getFieldType(array $attr, string $default): string
    {
        $method = $default;
        if (isset($attr['type'])) {
            // button types
            if (in_array($attr['type'], ['submit', 'reset', 'button'])) {
                $method = 'submit';
            }
            // other field types
            if (in_array($attr['type'], ['checkbox', 'radio', 'file', 'textarea', 'hidden', 'label', 'select'])) {
                $method = $attr['type'];
            }
        }
        return $method;
    }

    /**
     * @param $arrgs
     * @param $attr
     * @param $method
     */
    protected function fixField(array $arrgs, array $attr, string $method): void
    {
        if (!isset($arrgs[0])) {
            return;
        }

        $value = '';
        if ($method == 'hidden') {
            $value = isset($attr['value']) ? $attr['value'] : 0;
        }

        if (!empty($attr['readonly'])) {
            $val = $this->bindStore->get($arrgs[0]);
            $value = !empty($val) ? $val : '';
            $value = isset($attr['value']) ? $attr['value'] : $value;
        }

        if (!in_array($method, ['submit', 'button', 'reset', 'label']) && $this->getIsForm()) {
            $this->formProtection->addField($arrgs[0], $attr, $value);
        }
    }

    /**
     * Instantiates field objects and returns an object OptionStore to create a chain
     *
     * @param $method
     * @param $arguments
     *
     * @return OptionStore
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \LogicException
     */
    protected function make(string $method, array $arguments): OptionStore
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
        $this->makeTranslations($arguments);
        $this->optionStore->setAttributes($arguments);
        $this->optionStore->setBuilder($this);
        return $this->optionStore;
    }

    /**
     * Completing modifying templates and their parame
     *
     * @return array
     */
    protected function complateTemplatesAndParams(): array
    {
        $data = [
            // for once filed
            'inline' => $this->inlineTemplates,
            // for fields in form
            'local' => $this->localTemplates,
            // for all page
            'global' => $this->globalTemplates,
        ];

        $this->inlineTemplates = $this->templateDefaultParams;
        return $data;
    }

    /**
     * Checks whether the template modification has been transferred from a separate field
     *
     * @param $attr
     */
    protected function hasTemplate(array &$attr): void
    {
        $pattern = [
            'pattern' => 'template',
            'div' => 'div',
            'class_concat' => 'class_concat',
            'escept' => 'escept'
        ];
        foreach ($pattern as $key => $item) {
            if (isset($attr[1][$item])) {
                $this->inlineTemplates[$key] = $attr[1][$item];
                unset($attr[1][$item]);
            }
        }
        if (!empty($attr[1]['label']) && is_array($attr[1]['label'])) {
            $this->inlineTemplates['label'] = $attr[1]['label'];
            unset($attr[1]['label']);
        }
    }

    /**
     * Remove proprties
     */
    protected function resetProperties(): void
    {
        $this->setIsForm(false);
        $this->maked = [];
        $this->localTemplates = $this->templateDefaultParams;
    }

    /**
     * locally or globally stores modifications and template parameters in properties
     *
     * @param $data
     * @param $container
     * @param $options
     */
    protected function addTemplatesAndParams(array $data, array &$container, array $options): void
    {
        foreach ($data as $key => $value) {
            $container['pattern'][$key] = $value;
        }
        if (empty($options)) {
            return;
        }
        $array = ['div', 'class_concat', 'escept'];
        foreach ($array as $index => $item) {
            if (isset($options[$item])) {
                $container[$item] = $options[$item];
            }
        }
        if (!empty($options['label']) && is_array($options['label'])) {
            if (isset($options['label']['text'])) {
                unset($options['label']['text']);
            }
            $container['label'] = $options['label'];
        }
    }

    /**
     * From the form parameters get a list of fields that should not be validated
     *
     * @param $options
     *
     * @return array|string
     * @throws \Exception
     */
    protected function getGeneralUnlockFieldsBy(array &$options): array
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
    protected function generateToken(): string
    {
        return md5(str_random(80));
    }

    /**
     * @param $val
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function setIsForm(bool $val): void
    {
        if ($this->isForm && $val) {
            throw new \Exception('Your action is not correct, have is open and not closed tag form!');
        }

        $this->isForm = $val;
    }

    /**
     * @return bool
     */
    protected function getIsForm(): bool
    {
        return $this->isForm;
    }

    /**
     * @param $arguments
     */
    protected function makeTranslations($arguments)
    {
        if (env('APP_ENV') !== config('lara_form.environment')) {
            return;
        }

        $isTrans = config('lara_form.translator.translate');

        $name = array_shift($arguments);
        $attr = [];

        if (!empty($arguments)) {
            $attr = array_shift($arguments);
        }
        
        if (isset($attr['translate'])) {
            $isTrans = (bool)$attr['translate'];
        }
        
        if (!$isTrans) {
            return;
        }
        
        if (empty($this->translator)) {
            $this->translator = app(TranslatorStore::class);
        }

        $key = null;
        $string = $name;
        if (!empty($attr['label'])) {
            $key = $name;
            $string = $attr['label'];
        } elseif (!empty($attr['label_text']) && is_string($attr['label_text'])) {
            $key = $name;
            $string = $attr['label_text'];
        }

        $this->translator->add($string, $key);

        if (!$this->isForm) {
            $this->translator->put();
        }
    }
}
