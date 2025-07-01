<?php

// =====================================================
// bootstrap/app.php（アプリケーション初期化ファイル）
// -----------------------------------------------------
// このファイルはLaravelアプリの起動・初期設定を行います。
// ルーティングやミドルウェア、例外処理などの設定をまとめて
// アプリケーションインスタンスを生成します。
//
// 【Laravel初心者向けポイント】
// ・アプリ全体の起動処理や設定のエントリーポイントです。
// ・ルートファイルやミドルウェアの追加・変更時はここを編集します。
// =====================================================

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
  ->withRouting(
    web: __DIR__ . '/../routes/web.php',
    api: __DIR__ . '/../routes/api.php',
    commands: __DIR__ . '/../routes/console.php',
    health: '/up',
  )
  ->withMiddleware(function (Middleware $middleware): void {
    //
  })
  ->withExceptions(function (Exceptions $exceptions): void {
    //
  })->create();
