<?php

namespace LaraForm;

use Illuminate\Support\Facades\Config;
use LaraForm\Core\BaseFormProtection;

class FormProtection extends BaseFormProtection
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
    protected $pathForTime;

    /**
     * @var
     */
    protected $pathForUnlock;

    protected $created_time;

    /**
     * FormProtection constructor.
     */
    public function __construct()
    {
        $this->sessionPrePath = config('lara_form.session.pre_path', 'laraforms');
        $this->pathForUnlock = config('lara_form.session.path_for.unlock', 'is_unlock');
        $this->pathForCheck = config('lara_form.session.path_for.check', 'is_check');
        $this->pathForTime = config('lara_form.session.path_for.time', 'created_time');
        $this->fields = [];
    }

    /**
     * @param $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    public function setTime()
    {
        $this->created_time = time();
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
     * @param bool $isAjax
     * @return bool
     */
    public function validate($data, $isAjax = false)
    {
        $tokenName = config('lara_form.label.form_protection', 'laraform_token');
        $token = !empty($data[$tokenName]) ? $data[$tokenName] : false;

        if (!$token) {
            return false;
        }

        if (!session()->has($this->sessionPath($token))) {
            return false;
        }

        $checkedFields = $this->getCheckedFieldsBy($token);
        $data = $this->removeUnlockFields($data, $token, $tokenName);


        if (!$isAjax) {
            session()->forget($this->sessionPath($token)); // TODO correct dellete all session or only $token
        }

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
     * @return bool
     */
    public function removeByTime()
    {
        $maxTime = \config('lara_form.session.max_time', false);
        if (!$maxTime) {
            return false;
        }
        $maxSeccounds = $maxTime * 60 * 60;
        $formHistory = session(config('lara_form.session.pre_path'));
        $timeName = config('lara_form.session.path_for.time');
        $currentTime = time();
        $betweenTime = $currentTime - $maxSeccounds;
        $newHistory = array_filter($formHistory, function ($value) use ($timeName, $betweenTime) {
            if (isset($value[$timeName])) {
                if ($value[$timeName] > $betweenTime) {
                    return $value;
                }
            }
        });
        session()->put(config('lara_form.session.pre_path'), $newHistory);
    }

    /**
     * @return bool
     */
    public function removeByCount()
    {
        $maxCount = \config('lara_form.session.max_count', false);
        if (!$maxCount) {
            return false;
        }
        $formHistory = session(config('lara_form.session.pre_path'));
        $between = count($formHistory) - $maxCount;
        if ($intervalCount > 0) {
            $newHistory = array_slice($formHistory, 0, $between);
            session()->put(config('lara_form.session.pre_path'), $newHistory);
        }
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
            unset($this->fields[$field]);
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

        $field = implode('.', $arr);

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
            $this->pathForUnlock => $this->unlockFields,
            $this->pathForTime => $this->created_time,
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
     * @param $token
     * @return mixed
     */
    private function getCreatedTime($token)
    {
        $path = $token . '.' . $this->pathForTime;
        return session($this->sessionPath($path));
    }

    /**
     * @param $data
     * @param $token
     * @param $tokenName
     * @return mixed
     */
    private function removeUnlockFields($data, $token, $tokenName)
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

        unset($data[$tokenName]);
        return $data;
    }
}
