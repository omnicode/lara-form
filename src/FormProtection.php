<?php
declare(strict_types=1);

namespace LaraForm;

use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use LaraForm\Core\BaseFormProtection;

/**
 * Working with form fields and validating requests for their compliance
 * Class FormProtection
 * @package LaraForm
 */
class FormProtection extends BaseFormProtection
{
    /**
     * Keeped here key for form fields
     * @var string
     */
    protected $token;

    /**
     * Keeped here fields which are not validated
     * @var array
     */
    protected $unlockFields;

    /**
     * Keeped here fields which are validated
     * @var array
     */
    public $fields;

    /**
     * Keeped here configuration for session
     * @var array
     */
    protected $configSession = [];

    /**
     * Keeped here the form opening time
     * @var
     */
    protected $created_time;

    /**
     * Keeped here form action url
     * @var
     */
    protected $url;

    /**
     * Keeped here params for ajax requests
     * @var array
     */
    protected $ajax;

    /**
     * FormProtection constructor.
     */
    public function __construct()
    {
        $this->configSession = config('lara_form.session');
        $this->ajax = config('lara_form.ajax_request');
        $this->fields = [];
    }

    /**
     * @param $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * @param $time
     */
    public function setTime(int $time): void
    {
        $this->created_time = $time;
    }

    /**
     * @param $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @param $unlockFields
     * @throws \Exception
     */
    public function setUnlockFields(array $unlockFields): void
    {
        $this->unlockFields = $this->processUnlockFields($unlockFields);
    }

    /**
     * Validates the field names, and for hidden field the value also, from the request
     * @param Request $request
     * @param $data
     * @return bool
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function validate(Request $request, array $data)
    {
        $this->removeByTime();
        $tokenName = config('lara_form.token_name', 'laraform_token');
        $token = !empty($data[$tokenName]) ? $data[$tokenName] : false;
        if (!$token) {
            return 'Lara Form token not found in request data';
        }

        if (!session()->has($this->sessionPath($token))) {
            return 'Lara Form token not found in session';
        }


        if (!$this->isValidAction($request, $token)) {
            return 'Lara Form request url is invalid';
        }

        $checkedFields = $this->getCheckedFieldsBy($token);
        $data = $this->removeUnlockFields($data, $token);

        if ($request->ajax()) {
            // for ajax request
            $isAjax = $this->verificationForAjax($request);

            if ($isAjax) {
                session()->forget($this->sessionPath($token));
            }
        } else {
            session()->forget($this->sessionPath($token));
        }
        if (array_keys($data) != array_keys($checkedFields)) {
            return 'Lara Form data is not equal to session data';
        }

        foreach ($checkedFields as $field => $value) {
            if (!empty($value)) {
                if (is_array($value)) {
                    // for array input;
                    if (array_keys(array_dot($value)) != array_keys(array_dot($data[$field]))) {
                        return 'Lara Form array data is not equal to session data';
                    }
                } else {
                    //for hidden input
                    if ($checkedFields[$field] != $data[$field]) {
                        return 'Lara Form hidden input value is not equal to session data';
                    }
                }
            }
        }
        
        return true;
    }

    /**
     * If the request is ajax, then the default token is removed
     * from the session except those specified in the configuration file
     * @param $request
     * @return bool
     */
    protected function verificationForAjax(Request $request): bool
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

        return true;
    }

    /**
     * Removes obsolete tokens from the session
     * @return bool
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function removeByTime(): void
    {
        $maxTime = config('lara_form.session.lifetime', false);

        if (!$maxTime) {
            return;
        }
        $formHistory = session(config('lara_form.session.name'));

        if (empty($formHistory)) {
            return;
        }
        if (starts_with($maxTime, '+') || starts_with($maxTime, '-')) {
            throw new \Exception('Time must not begin with a + character or with a character -');
        }

        $maxSeccounds = strtotime('-' . $maxTime);
        $timeName = config('lara_form.session.paths.time');
        $newHistory = array_filter($formHistory, function ($value) use ($timeName, $maxSeccounds) {
            if (isset($value[$timeName])) {
                if ($value[$timeName] > $maxSeccounds) {
                    return $value;
                }
            }
        });

        session()->put(config('lara_form.session.name'), $newHistory);
    }

    /**
     * To monitor the number of tokens in the session and delete them to a specified count
     * @return bool
     */
    public function removeByCount(): void
    {
        $maxCount = config('lara_form.session.max_count', false);

        if (!$maxCount) {
            return;
        }

        $formHistory = session(config('lara_form.session.name'),[]);

        if (count($formHistory) > $maxCount) {
            $newHistory = array_slice($formHistory, -$maxCount);
            session()->put(config('lara_form.session.name'), $newHistory);
        }
    }

    /**
     * @param $unlockFields
     * @return array|string
     * @throws \Exception
     */
    public function processUnlockFields($unlockFields): array
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
     * Adds fields for validation
     * Warning!
     * Those fields that have disabled or unlock attributes will not be added to the list of validation!!!
     * @param $field
     * @param array $options
     * @param string $value
     * @return bool
     */
    public function addField(string $field, &$options = [], $value = ''): void
    {
        if (!empty($options['disabled'])) {
            return;
        }

        if (!empty($options['_unlock'])) {
            unset($options['_unlock']);
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
     * Transforms a multidimensional array into a string
     * @param $field
     */
    public function addArrayInputField(string $field): void
    {

        $arr = explode('[', $field);
        foreach ($arr as $key => $item) {
            $arr[$key] = rtrim($item, ']');
        }

        $field = implode('.', $arr);

        if (ends_with($field, '.')) {
            array_set($this->fields, substr($field, 0, -1), []);
        } else {
            array_set($this->fields, $field, '');
        }
    }

    /**
     * Fields saves in session
     */
    public function confirm(): void
    {
        $data = [
            $this->configSession['paths']['check'] => $this->fields,
            $this->configSession['paths']['unlock'] => $this->unlockFields,
            $this->configSession['paths']['time'] => $this->created_time,
            $this->configSession['paths']['action'] => $this->url,
        ];

        $this->fields = [];
        $this->unlockFields = [];
        session([$this->sessionPath() => $data]);
    }

    /**
     * Verifies the current url corresponds to the one specified in the form
     * @param $token
     * @param $request
     * @return bool
     */
    public function isValidAction(Request $request, string $token): bool
    {
        $path = $token . '.' . $this->configSession['paths']['unlock'];
        $unlockFields = session($this->sessionPath($path));

        if (is_array($unlockFields) && !empty(array_intersect(['action', 'url', 'route'], $unlockFields))) {
            return true;
        }

        $action = $this->getAction($token);
        $urls = [$request->url(), $request->getRequestUri(), $request->fullUrl()];

        if (in_array($action, $urls)) {
            return true;
        }

        return false;
    }

    /***
     * @param $token
     * @return mixed
     */
    protected function getAction(string $token): string
    {
        $path = $token . '.' . $this->configSession['paths']['action'];
        return session($this->sessionPath($path));
    }

    /**
     * @param string $path
     * @return string
     */
    protected function sessionPath(string $path = ''): string
    {
        $path = empty($path) ? $this->token : $path;
        return $this->configSession['name'] . '.' . $path;
    }

    /**
     * @param $token
     * @return bool|mixed
     */
    protected function getCheckedFieldsBy(string $token): ?array
    {
        $path = $token . '.' . $this->configSession['paths']['check'];
        return session($this->sessionPath($path));
    }

    /**
     * Removes unverifiable fields from request
     * @param $data
     * @param $token
     * @return mixed
     */
    protected function removeUnlockFields(array $data, string $token): array
    {
        $path = $token . '.' . $this->configSession['paths']['unlock'];
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
