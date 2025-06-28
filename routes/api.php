use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveGrantMasterController;
use App\Http\Controllers\LeaveUsageController;

Route::apiResource('employees', EmployeeController::class);
Route::get('leave-grant-master', [LeaveGrantMasterController::class, 'index']);
Route::get('leave-usages', [LeaveUsageController::class, 'index']);
Route::post('leave-usages', [LeaveUsageController::class, 'store']);
Route::delete('leave-usages', [LeaveUsageController::class, 'destroy']);
Route::get('leave-summary', [LeaveUsageController::class, 'showSummary']);
