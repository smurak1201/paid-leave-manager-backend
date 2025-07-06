<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo($request): ?string
    {
        // APIリクエストの場合はリダイレクトせず401を返す
        if ($request->expectsJson()) {
            return null;
        }
        // Webアクセス時のみloginルートへ（必要なら）
        // return route('login');
        return null;
    }
}
