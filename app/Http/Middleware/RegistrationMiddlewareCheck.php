<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;

class RegistrationMiddlewareCheck
{
    public function handle(Request $request, Closure $next)
    {
        // This is just a test file to check if our middleware is registered
        return $next($request);
    }
}
