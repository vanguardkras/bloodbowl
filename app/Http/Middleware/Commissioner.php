<?php

namespace App\Http\Middleware;

use Closure;

class Commissioner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!auth()->user()->commissioner) {
            abort('404');
        }

        return $next($request);
    }
}
