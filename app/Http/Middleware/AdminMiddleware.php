<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!auth()->check() || auth()->user()->role !== 'administratorius') {
            abort(403, 'Tik administratorius gali pasiekti šį puslapį.');
        }

        return $next($request);
    }
}
