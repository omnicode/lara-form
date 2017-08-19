<?php
namespace LaraForm;

use Illuminate\Support\Facades\Config;


class FormProtection
{
    /**
     * @var
     */
    protected $token;

    /**
     * @var
     */
    protected $unlockFields;

    /**
     * @var array
     */
    protected $fields;

    /**
     * @var
     */
    protected $sessionPrePath;

    /**
     * @var
     */
    protected $pathForCheck;

    /**
     * @var
     */
    protected $pathForUnlock;

    /**
     * FormProtection constructor.
     */
    public function __construct()
    {
        $this->sessionPrePath = Config::get('lara_form.session.pre_path', 'laraforms');
        $this->pathForUnlock= Config::get('lara_form.session.path_for.unlock', 'is_unlock');
        $this->pathForCheck = Config::get('lara_form.session.path_for.check', 'is_check');
        $this->fields = [];
    }

    /**
     * @param $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @param $unlockFields
     * @throws \Exception
     */
    public function setUnlockFields($unlockFields)
    {
        $this->unlockFields = $this->processUnlockFields($unlockFields);
    }

    /**
     * @param $data
     * @return bool
     */
    public function validate($data)
    {
        $tokenName = Config::get('lara_form.label.form_protection', 'laraform_token');
        $token = !empty($data[$tokenName]) ? $data[$tokenName] : false;

        if (!$token) {
            return false;
        }

        if (!session()->has($this->sessionPath($token))) {
            return false;
        }

        $checkedFields = $this->getCheckedFieldsBy($token);
        $data = $this->removeUnlockFields($data, $token);
        session()->forget($this->sessionPrePath); // TODO correct dellete all session or only $token

        if (array_keys($data) != array_keys($checkedFields)) {
            return false;
        }
        foreach ($checkedFields as $key => $value) {
            if (!empty($value)) {
                if (is_array($value)) {
//                    if (!is_array($data[$key])) {
//                        $data[$key] = [$data[$key]];
//                    }
                    //for select input
                    // TODO
                } else {
                    if($checkedFields[$key] != $data[$key]) {
                        //for hidden input
                        return false;
                    }
                }
            }
        }

        return true;
    }


    /**
     * @param $unlockFields
     * @throws \Exception
     */
    public function addUnlockFields($unlockFields)
    {
        $this->unlockFields = array_merge($this->unlockFields, $this->processUnlockFields($unlockFields));
    }

    /**
     * @param $unlockFields
     * @return array|string
     * @throws \Exception
     */
    public function processUnlockFields($unlockFields)
    {
        if (is_string($unlockFields)) {
            $unlockFields = [$unlockFields];
        } else {
            if (!is_array($unlockFields)) {
                throw new \Exception('You can only set string or array');
            }
        }
        return $unlockFields;
    }

    /**
     * @param $field
     * @param array $options
     * @param string $value
     */
    public function addField($field, &$options = [], $value = '')
    {
        if (!empty($options['unlock'])) {
            $this->unlockFields[] = $field;
        } else {
            if (!in_array($field, $this->unlockFields)) {
                $this->fields[$field] = $value;
            }
        }

    }

    /**
     *
     */
    public function confirm()
    {
        $data = [
            $this->pathForCheck => $this->fields,
            $this->pathForUnlock => $this->unlockFields
        ];
        $this->fields = [];
        $this->unlockFields = [];
        session([$this->sessionPath() => $data]);
    }

    /**
     * @param string $path
     * @return string
     */
    private function sessionPath($path = '')
    {
        $path = empty($path) ? $this->token : $path;
        return $this->sessionPrePath . '.' . $path;
    }

    /**
     * @param $token
     * @return bool|mixed
     */
    private function getCheckedFieldsBy($token)
    {
        $path = $token . '.' . $this->pathForCheck;
        return session($this->sessionPath($path));
    }

    /**
     * @param $data
     * @param $token
     * @return mixed
     */
    private function removeUnlockFields($data, $token)
    {
        $path = $token . '.' . $this->pathForUnlock;
        $unlockFields = session($this->sessionPath($path));

        foreach ($unlockFields as $unlockField) {
            if (in_array($unlockField, array_keys($data))) {
                unset($data[$unlockField]);
            }
        }

        return $data;
    }
}
