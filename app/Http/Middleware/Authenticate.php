<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo($request)
    {
        // APIリクエストやJSONリクエスト時は401 Unauthorizedのみ返す
        if ($request->expectsJson() || $request->is('api/*')) {
            return null;
        }
        // Webアクセス時のみloginルートへ（必要な場合のみ）
        // return route('login');
        return null; // loginルート未定義のため常にnull返却
    }
}
