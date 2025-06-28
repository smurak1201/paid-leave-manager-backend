<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
  // ...existing methods...

  public function render($request, Throwable $exception)
  {
    return response()->json([
      'error' => $exception->getMessage()
    ], $exception->getCode() ?: 500);
  }
}
