<?php

namespace App\Http\Middleware;

use Closure;

class CorsMiddleware
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
    $response = $next($request);

    $response->headers->set('Content-Type', 'application/json; charset=utf-8');
    $response->headers->set('Access-Control-Allow-Origin', '*');
    $response->headers->set('Access-Control-Allow-Headers', 'Content-Type');

    if ($request->getMethod() === 'OPTIONS') {
      return response('', 200);
    }

    return $response;
  }
}
