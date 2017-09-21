<?php
namespace LaraForm\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use LaraForm\FormProtection;

class LaraFormMiddleware
{

    /**
     * @param Request $request
     * @param Closure $next
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->method() != 'GET' && !$this->isGlobalExceptionUrl($request->getUri())) {

            $formProtection = new FormProtection();
            $validate = $formProtection->validate(array_diff($request->all(), $request->query()));
            if($validate === false) {
                abort(401, 'Your Action Is Forbidden');
            }

            unset($request[Config::get('lara_form.label.form_protection', 'laraform_token')]);
        }

        return $next($request);
    }

    /**
     * @param $url
     * @return bool
     */
    private function isGlobalExceptionUrl($url) {
        $isExcept = false;
        $excepts = Config::get('lara_form.except', []);

        foreach ($excepts as $except) {
            if (str_contains($except, '*')) {
                if (ends_with($except, '*')) {
                    if (starts_with($url, url(substr($except, 0, -1)))) {
                        $isExcept = true;
                        break;
                    }
                }
            } else {
                if(url($except) == $url) {
                    $isExcept = true;
                    break;
                }
            }
        }

        return $isExcept;
    }
}
