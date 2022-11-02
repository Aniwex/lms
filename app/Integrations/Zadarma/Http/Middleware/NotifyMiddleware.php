<?php

namespace App\Integrations\Zadarma\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class NotifyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!in_array($request->ip(), config('zadarma.allowed_ips'))) {
            abort(403);
        }

        /**
         * @todo signature check https://zadarma.com/ru/support/api/#api_webhook_speech_recognition
         */

        return $next($request);
    }
}
