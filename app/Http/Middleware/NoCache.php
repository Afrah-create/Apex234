<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Illuminate\Http\Response;

class NoCache
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        if ($response instanceof Response) {
            return $response->header('Cache-Control','no-cache, no-store, max-age=0, must-revalidate')
                            ->header('Pragma','no-cache')
                            ->header('Expires','Sat, 01 Jan 1990 00:00:00 GMT');
        }
        return $response;
    }
}
