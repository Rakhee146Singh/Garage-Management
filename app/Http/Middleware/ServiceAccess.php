<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ServiceAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $types): Response
    {
        $types = explode('|', $types);
        $flag = false;
        foreach ($types as $type) {
            if ($type == auth()->user()->type) {
                $flag = true;
                return $next($request);
            }
        }
        if (!$flag) {
            return response()->json(['Not Acccess']);
        }
        // if (auth()->user()->type == 'admin') {
        //     return $next($request);
        // } else {
        //     return 'Not Access';
        // }
    }
}
