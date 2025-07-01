<?php

use App\Http\Controllers\EmployeesController;
use App\Http\Controllers\LeaveGrantMasterController;
use App\Http\Controllers\LeaveUsageController;

// 従業員APIはEmployeesControllerに統一
Route::get('/employees', [EmployeesController::class, 'index']);
Route::post('/employees', [EmployeesController::class, 'store']);
Route::put('/employees/{id}', [EmployeesController::class, 'update']);
Route::delete('/employees/{id}', [EmployeesController::class, 'destroy']);

// 有給付与マスター
Route::get('/leave-grant-master', [LeaveGrantMasterController::class, 'index']);

// 有給取得履歴
Route::get('/leave-usages', [LeaveUsageController::class, 'index']);
Route::post('/leave-usages', [LeaveUsageController::class, 'store']);
Route::delete('/leave-usages', [LeaveUsageController::class, 'destroy']);
Route::get('/leave-summary', [LeaveUsageController::class, 'showSummary']);

// テスト用
Route::get('/test', function () {
    return response()->json(['message' => 'Test route working']);
});
