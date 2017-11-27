<?php

namespace LaraForm;

use AdamWathan\BootForms\Facades\BootForm;
use LaraForm\Elements\Components\Inputs\RadioButton;
use Illuminate\Support\Facades\Config;
use LaraForm\Elements\Components\CheckBox;
use LaraForm\Elements\Components\Inputs\Hidden;
use LaraForm\Elements\Components\Inputs\Input;
use LaraForm\Elements\Components\Inputs\Password;
use LaraForm\Elements\Components\Inputs\Submit;
use LaraForm\Elements\Components\Label;
use LaraForm\Elements\Components\Select;
use LaraForm\Elements\Components\Textarea;

class FormBuilder
{

    protected $methods = [
        'input' => Input::class,
        'password' => Password::class,
        'textarea' => Textarea::class,
        'select' => Select::class,
        'submit' => Submit::class,
        'hidden' => Hidden::class,
        'checkbox' => CheckBox::class,
        'label' => Label::class,
        'radioButton' => RadioButton::class
    ];

    /**
     * @var FormProtection
     */
    protected $formProtection;

    /**
     * @var MakeForm
     */
    protected $make;

    /**
     * FormBuilder constructor.
     * @param FormProtection $formProtection
     * @param MakeForm $makeForm
     */
    public function __construct(
        FormProtection $formProtection,
        MakeForm $makeForm

    ) {
        $this->formProtection = $formProtection;
        $this->make = $makeForm;
    }

    /**
     * @param $methodName
     * @param $attr
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function __call($methodName, $attr)
    {
        /*  try {
              call_user_func([$this->make,$methodName],$attr);
          } catch (\Exception $e) {
                echo '405 [' . $methodName . '] method not allowed';
          }*/

        return $this->{$methodName}(...$attr);

    }

    /**
     * @param $proprtyName
     * @return \Illuminate\Foundation\Application|mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \LogicException
     */
    public function __get($proprtyName)
    {
        $modelName = $this->methods[$proprtyName];
        return app($modelName);
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
        $form = $this->make->open($model, $options);

        $token = md5(str_random(80));
        $this->formProtection->setToken($token);

        $unlockFields = $this->getGeneralUnlockFieldsBy($options);
        $unlockFields[] = '_token';

        $method = $this->getMethodBy($model, $options);
        if ($method) {
            $form->{$method}();
            $unlockFields[] = '_method';
        }

        $this->formProtection->setUnlockFields($unlockFields);

        if ($method != 'get') {
            $hidden = $this->hidden(Config::get('lara_form.label.form_protection', 'laraform_token'), $token);
        } else {
            $hidden = '';
        }
        return dd($form . $hidden);
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
     */
    public function end()
    {
        $this->formProtection->confirm();
        return BootForm::close();
    }

    /**
     * @param $name
     * @param array $options
     * @return mixed
     */
    protected function input($name, array $options = [])
    {
        $this->formProtection->addField($name, $options);
        $hidden = (!empty($options['type']) && $options['type'] == 'file') ? $this->hidden->toHtml($name) : '';
        return $hidden . $this->input->toHtml($name, $options);
    }


    /**
     * @param $name
     * @param array $options
     * @return mixed
     */
    protected function password($name, array $options = [])
    {
        $this->formProtection->addField($name, $options);
        $options['type'] = 'password';
        return $this->input->toHtml($name, $options);
    }

    /**
     * @param $name
     * @param array $options
     * @return mixed
     */
    protected function select($name, $options = [])
    {
        $hidden = '';
        if (isset($options['empty']) && $options['empty'] === false) {
            $hidden = $this->hidden->toHtml(substr($name, 0, -2));
        }
        $this->formProtection->addField($name, $options); // TODO add options
//        TODO for select optional check values
//        $optionValues = $this->select->getOptionValues($options, false);
//        $this->formProtection->addField($name, $options,  array_keys($optionValues));

        return $hidden . $this->select->toHtml($name, $options);
    }

    /**
     * @param string $name
     * @param array $options
     * @return mixed
     */
    protected function submit($name = '', $options = [])
    {
        return $this->submit->toHtml($name, $options);
    }

    /**
     * @param $name
     * @param array $options
     * @return mixed
     */
    public function radioButton($name, array $options = [])
    {
        $this->formProtection->addField($name, $options);
        return $this->radioButton->toHtml($name, $options);
    }

    /**
     * @param $name
     * @param array $options
     * @return string
     */
    protected function checkbox($name, array $options = [])
    {

        $this->formProtection->addField($name, $options);
        $checkbox = $this->checkbox->toHtml($name, $options);

        if (isset($options['hidden']) && $options['hidden'] === false) {
            $hidden = '';
            unset($options['hidden']);
        } else {
            $hidden = ends_with($name, '[]') ? '' : $this->hidden->toHtml($name, 0);
        }

        if (empty($options['checked'])) {
            unset($options['checked']);
        }

        foreach ($options as $k => $v) {
            if ($k == 'class') {
                $checkbox->class($v);
            } else {
                $checkbox->attribute($k, $v);
            }
        }

        return $hidden . $checkbox;
    }

    /**
     * @param $name
     * @param array $options
     * @return string
     */
    protected function hidden($name, $options = [])
    {
        $this->formProtection->addField($name, $options, $this->hidden->getValue($options));
        return $this->hidden->toHtml($name, $options);
    }

    /**
     * @param $name
     * @param array $options
     * @return string
     */
    protected function textarea($name, $options = [])
    {
        $this->formProtection->addField($name, $options);
        return $this->textarea->toHtml($name, $options);
    }
}
