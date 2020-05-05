<?php

namespace App\Http\Middleware;

use Closure;

class Commissioner
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $fail = false;
        if (auth()->user()) {
            if (!auth()->user()->commissioner) {
                $fail = true;
            }
        } else {
            $fail = true;
        }

        return $fail ? abort('404') : $next($request);
    }
}
