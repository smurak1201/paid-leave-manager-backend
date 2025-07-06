<?php

// =============================
// routes/api.php
// -----------------------------
// このファイルはLaravelの「APIルート」を定義します。
// ここで定義したURLパス（例: /api/employees）は、
// フロントエンドや外部からのAPIリクエストを受け付け、
// 指定したコントローラのメソッドに処理を委譲します。
// ルート定義の書き方や役割を理解することで、
// バックエンドAPIの拡張や保守がしやすくなります。
// =============================

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeesController;
use App\Http\Controllers\LeaveGrantMasterController;
use App\Http\Controllers\LeaveUsageController;

// 認証API
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// 従業員APIはEmployeesControllerに統一
Route::middleware('auth:sanctum')->get('/employees', [EmployeesController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    // 従業員API
    Route::post('/employees', [EmployeesController::class, 'store']);
    Route::put('/employees/{id}', [EmployeesController::class, 'update']);
    Route::delete('/employees/{id}', [EmployeesController::class, 'destroy']);

    // 有給付与マスター
    Route::get('/leave-grant-master', [LeaveGrantMasterController::class, 'index']);
    Route::post('/leave-grant-master', [LeaveGrantMasterController::class, 'store']);
    Route::put('/leave-grant-master/{id}', [LeaveGrantMasterController::class, 'update']);
    Route::delete('/leave-grant-master/{id}', [LeaveGrantMasterController::class, 'destroy']);

    // 有給取得履歴 RESTful API
    Route::get('/leave-usages', [LeaveUsageController::class, 'index']); // 一覧取得
    Route::post('/leave-usages', [LeaveUsageController::class, 'store']); // 追加
    Route::delete('/leave-usages/{id}', [LeaveUsageController::class, 'destroy']); // id指定で削除
    Route::get('/leave-summary', [LeaveUsageController::class, 'showSummary']);
});

// テスト用

// CORS動作確認用プリフライトテストルート
Route::options('/cors-test', function () {
    return response()->json(['ok' => true]);
});

Route::get('/test', function () {
    return response()->json(['message' => 'Test route working']);
});
