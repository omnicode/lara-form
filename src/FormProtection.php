<?php

namespace LaraForm;

use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
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
    public $fields;

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

    /**
     * @var mixed
     */
    protected $pathForUrl;

    /**
     * @var
     */
    protected $created_time;

    /**
     * @var
     */
    protected $url;

    /**
     * @var
     */
    protected $ajax;

    /**
     * FormProtection constructor.
     */
    public function __construct()
    {
        $this->sessionPrePath = config('lara_form.session.pre_path', 'laraforms');
        $this->pathForUnlock = config('lara_form.session.path_for.unlock', 'is_unlock');
        $this->pathForCheck = config('lara_form.session.path_for.check', 'is_check');
        $this->pathForTime = config('lara_form.session.path_for.time', 'created_time');
        $this->pathForUrl = config('lara_form.session.path_for.action', '_action');
        $this->ajax = config('lara_form.ajax_request');
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
     *
     */
    public function setTime()
    {
        $this->created_time = time();
    }

    /**
     * @param $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
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
     * @param Request $request
     * @param $data
     * @return bool
     * @internal param bool $isAjax
     * @internal param bool $currentUrl
     */
    public function validate(Request $request, $data)
    {
        $this->removeByTime();
        $tokenName = config('lara_form.label.form_protection', 'laraform_token');
        $token = !empty($data[$tokenName]) ? $data[$tokenName] : false;

        if (!$token) {
            dd(1);
            return false;
        }

        if (!session()->has($this->sessionPath($token))) {
            dd(2);
            return false;
        }

        if (!$this->isValidAction($token, $request->url())) {
            dd(3);
            return false;
        }

        $checkedFields = $this->getCheckedFieldsBy($token);
        $data = $this->removeUnlockFields($data, $token);

        if ($request->ajax()) {
            $isAjax = $this->verificationForAjax($request);

            if ($isAjax) {
                session()->forget($this->sessionPath($token));
            }
        } else {
            session()->forget($this->sessionPath($token));
        }


        if (array_keys($data) != array_keys($checkedFields)) {
            dd(4);
            return false;
        }

        foreach ($checkedFields as $field => $value) {
            if (!empty($value)) {
                if (is_array($value)) {
                    // for array input
                    if (array_keys(array_dot($value)) != array_keys(array_dot($data[$field]))) {
                        dd(5);
                        return false;
                    }
                } else {
                    //for hidden input
                    if ($checkedFields[$field] != $data[$field]) {
                        dd(6);
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * @param $request
     * @return bool
     */
    protected function verificationForAjax($request)
    {
        if (!empty($this->ajax['url']) && in_array($request->url(), $this->ajax['url'])) {
            return false;
        }
        if (!empty($this->ajax['action'])) {
            $controllerAction = class_basename($request->route()->getAction()['controller']);
            if (in_array($controllerAction, $this->ajax['action'])) {
                return false;
            }
        }

        if (!empty($this->ajax['route'])) {
            $routeName = $request->route()->getName();
            if (in_array($routeName, $this->ajax['route'])) {
                return false;
            }
        }

        return $this->ajax['is_removed'];
    }

    /**
     * @return bool
     */
    public function removeByTime()
    {
        $maxTime = config('lara_form.session.max_time', false);
        if (!$maxTime) {
            return false;
        }
        $formHistory = session(config('lara_form.session.pre_path'));
        if (empty($formHistory)) {
            return false;
        }
        $maxSeccounds = $maxTime * 60 * 60;
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
        if (count($formHistory) > $maxCount) {
            $newHistory = array_slice($formHistory, -$maxCount);
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
            $this->pathForUrl => $this->url,
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
     * @param $currentUrl
     * @return bool
     */
    public function isValidAction($token, $currentUrl)
    {
        $path = $token . '.' . $this->pathForUnlock;
        $unlockFields = session($this->sessionPath($path));
        if (in_array('action', $unlockFields)) {
            return true;
        }
        $action = $this->getAction($token);
        if ($action == $currentUrl) {
            return true;
        }

        return false;
    }

    /***
     * @param $token
     * @return mixed
     */
    protected function getAction($token)
    {
        $path = $token . '.' . $this->pathForUrl;
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
        $globalUnloc = config('lara_form.except.field');
        if (!empty($globalUnloc)) {
            $unlockFields = array_merge($globalUnloc, $unlockFields);
        }
        foreach ($data as $key => $value) {
            if (ends_with($key, '[]')) {
                $key = substr($key, 0, -2);
            }
            if (starts_with($key, $unlockFields)) {
                unset($data[$key]);
            }
        }
        return $data;
    }
}
