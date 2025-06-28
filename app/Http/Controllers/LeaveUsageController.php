<?php

namespace App\Http\Controllers;

use App\Models\LeaveUsage;
use App\Models\Employee;
use Illuminate\Http\Request;
use Exception;

class LeaveUsageController extends Controller
{
  public function index()
  {
    try {
      $usages = LeaveUsage::orderBy('employee_id')->orderBy('used_date')->get();
      return response()->json($usages, 200);
    } catch (Exception $e) {
      return response()->json(['error' => 'DBエラー: ' . $e->getMessage()], 500);
    }
  }

  public function store(Request $request)
  {
    $validated = $request->validate([
      'employee_id' => 'required|exists:employees,employee_id',
      'used_date' => 'required|date',
    ]);

    $employee = Employee::where('employee_id', $validated['employee_id'])->first();
    if ($validated['used_date'] < $employee->joined_at) {
      return response()->json(['error' => '消化日は入社日以降の日付を指定してください'], 400);
    }

    try {
      LeaveUsage::create($validated);
      return response()->json(['result' => 'ok'], 201);
    } catch (Exception $e) {
      return response()->json(['error' => 'DBエラー: ' . $e->getMessage()], 500);
    }
  }

  public function update(Request $request, $id)
  {
    $leaveUsage = LeaveUsage::findOrFail($id);
    $leaveUsage->update($request->all());
    return response()->json($leaveUsage);
  }

  public function destroy(Request $request)
  {
    $validated = $request->validate([
      'employee_id' => 'required|exists:employees,employee_id',
      'used_date' => 'required|date',
    ]);

    try {
      LeaveUsage::where('employee_id', $validated['employee_id'])
        ->where('used_date', $validated['used_date'])
        ->delete();
      return response()->json(['result' => 'ok'], 200);
    } catch (Exception $e) {
      return response()->json(['error' => 'DBエラー: ' . $e->getMessage()], 500);
    }
  }
}
