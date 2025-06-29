use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveGrantMasterController;
use App\Http\Controllers\LeaveUsageController;
use App\Http\Controllers\EmployeesController;

Route::apiResource('employees', EmployeeController::class);
Route::get('leave-grant-master', [LeaveGrantMasterController::class, 'index']);
Route::get('leave-usages', [LeaveUsageController::class, 'index']);
Route::post('leave-usages', [LeaveUsageController::class, 'store']);
Route::delete('leave-usages', [LeaveUsageController::class, 'destroy']);
Route::get('leave-summary', [LeaveUsageController::class, 'showSummary']);

Route::get('/employees', [EmployeesController::class, 'index']);
Route::post('/employees', [EmployeesController::class, 'store']);
Route::put('/employees/{id}', [EmployeesController::class, 'update']);
Route::delete('/employees/{id}', [EmployeesController::class, 'destroy']);
Route::get('/test', function () {
return response()->json(['message' => 'Test route working']);
});
Route::get('/api/test', function () {
return response()->json(['message' => 'API Test route working']);
});
