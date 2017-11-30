<?php

namespace LaraForm;

use Illuminate\Support\Facades\Config;

class FormBuilder
{

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
        return call_user_func([$this->make, $methodName], $attr);
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
        $formHtml = $this->make->open($model, $options);

        $token = md5(str_random(80));
        $this->formProtection->setToken($token);

        $unlockFields = $this->getGeneralUnlockFieldsBy($options);
        $unlockFields[] = '_token';

        $method = $this->getMethodBy($model, $options);
        if ($method) {
            $unlockFields[] = '_method';
        }

        $this->formProtection->setUnlockFields($unlockFields);

        if ($method != 'get') {
            $hidden = $this->hidden(config('lara_form.label.form_protection', 'laraform_token'), ['value' => $token]);
        } else {
            $hidden = '';
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
     */
    public function end()
    {
        $this->formProtection->confirm();
        return $this->make->close();
    }

}
