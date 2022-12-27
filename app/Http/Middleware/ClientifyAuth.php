<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ClientifyAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->bearerToken() !== env('CLIENTIFY_API')) {
            abort(421, "Clientify authorization token not valid");
        }
        return $next($request);
    }
}
