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

        foreach ($checkedFields as $field => $value) {
            if (!empty($value)) {
                if (is_array($value)) {
                    // for array input
                    if (array_keys(array_dot($value)) != array_keys(array_dot($data[$field]))) {
                        return false;
                    }
                } else {
                    //for hidden input
                    if ($checkedFields[$field] != $data[$field]) {
                        return false;
                    }
                }
            }
        }

        return true;
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
        if (!empty($options['_unlock']) || !empty($options['disabled'])) {
            // unset($this->fields[$field]);
            $this->unlockFields[] = $field; // TODO allows unlock array input
        } else {
            if (!starts_with($field, $this->unlockFields)) {
                if (str_contains($field, '[') && str_contains($field, ']')) {
                    $this->addArrayInputField($field);
                } else {
                    $this->fields[$field] = $value;
                }
            }
        }
    }

    /**
     * @param $field
     */
    public function addArrayInputField($field)
    {
        $arr = explode('[', $field);

        foreach ($arr as $key => $item) {
            $arr[$key] = rtrim($item, ']');
        }

        $field =  implode('.' , $arr);

        if (ends_with($field, '.')) {
            array_set($this->fields, substr($field, 0, -1), []);
        } elseif (str_contains('..', $field)) {
            dd('as');
        } else {
            array_set($this->fields, $field, '');
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

        foreach ($data as $key => $value) {
            if (ends_with($key, '[]')) {
                $key = substr($key, 0, -2);
            }
            if (starts_with($key, $unlockFields)) { //TODO only str_equals
                unset($data[$key]);
            }
        }
        return $data;
    }
}
