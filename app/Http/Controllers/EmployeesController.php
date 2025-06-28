<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class EmployeesController extends Controller
{
    // 従業員一覧取得
    public function index()
    {
        $employees = Employee::all();
        return response()->json($employees);
    }

    // 従業員追加
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|unique:employees,employee_id',
            'last_name' => 'required',
            'first_name' => 'required',
            'joined_at' => 'required|date',
        ]);

        $employee = Employee::create($validated);
        return response()->json(['result' => 'ok', 'employee' => $employee]);
    }

    // 従業員編集
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'last_name' => 'required',
            'first_name' => 'required',
            'joined_at' => 'required|date',
        ]);

        $employee = Employee::findOrFail($id);
        $employee->update($validated);
        return response()->json(['result' => 'ok', 'employee' => $employee]);
    }

    // 従業員削除
    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();
        return response()->json(['result' => 'ok']);
    }
}
