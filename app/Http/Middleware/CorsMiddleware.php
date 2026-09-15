<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CorsMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $headers = $request->headers->get('Access-Control-Allow-Headers');

        if (!in_array('Authorization', $headers)) {
            $headers[] = 'Authorization';
        }

        $request->headers->set('Access-Control-Allow-Headers', $headers);

        return $next($request);
        
        //  return $next($request)
        //     ->header('Access-Control-Allow-Origin', env('FRONTEND_URL'))
        //     ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        //     ->header('Access-Control-Allow-Headers', 'Authorization, Content-Type');
    }
}
