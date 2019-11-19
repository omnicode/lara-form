<?php
declare(strict_types=1);

namespace LaraForm\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use LaraForm\FormProtection;

/**
 * Class LaraFormMiddleware
 * @package LaraForm\Middleware
 */
class LaraFormMiddleware
{
    /**
     * @var FormProtection
     */
    protected $formProtection;

    /**
     * LaraFormMiddleware constructor.
     * @param FormProtection $formProtection
     */
    public function __construct(FormProtection $formProtection)
    {
        $this->formProtection = $formProtection;
    }

    /**
     * if the check fails, it will throw an exception
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->method() != 'GET' && !$this->isGlobalException($request)) {

            $data = $request->all();
            foreach ($request->query() as $index => $key) {
                unset($data[$index]);
            }

            $validate = $this->formProtection->validate($request, $data);

            if ($validate !== true) {
                $message = $validate;
                if (app()->environment('production')) {
                    $message = 'Your Action Is Forbidden';
                }
                abort(403, $message);
            }

            unset($request[config('lara_form.token_name', 'laraform_token')]);
        }

        return $next($request);
    }

    /**
     * Checks for to specific exceptions in the configuration file
     *
     * @param $request
     * @return bool
     */
    protected function isGlobalException(Request $request): bool
    {
        $isExcept = false;
        $exceptUrls = config('lara_form.except.url');;
        $exceptRoutes = config('lara_form.except.route');;

        if (!empty($exceptUrls)) {
            $uri = $request->getUri();
            foreach ($exceptUrls as $except) {
                if (str_contains($except, '*')) {
                    if (ends_with($except, '*')) {
                        if (starts_with($uri, url(substr($except, 0, -1)))) {
                            $isExcept = true;
                            break;
                        }
                    }
                } else {
                    if (url($except) == $uri) {
                        $isExcept = true;
                        break;
                    }
                }
            }
        }

        if (!empty($exceptRoutes) && !$isExcept) {
            $routeName = $request->route()->getName();
            if (in_array($routeName, $exceptRoutes)) {
                $isExcept = true;
            }
        }

        return $isExcept;
    }
}
