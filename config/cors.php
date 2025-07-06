<?php

// =====================================================
// config/cors.php（CORS設定ファイル）
// -----------------------------------------------------
// このファイルは「CORS（クロスオリジンリソースシェアリング）」の
// 設定を管理します。フロントエンド（React/Vite）とAPI（Laravel）
// 間の通信で発生するCORSエラーを防ぐために重要です。
//
// 【Laravel初心者向けポイント】
// ・allowed_originsで許可するフロントエンドのURLを指定します。
// ・API開発時はCORS設定を正しく行うことで、
//   フロントとバックエンドの連携がスムーズになります。
// =====================================================

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://172.18.119.226:5173',
        'http://localhost:5173',
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
