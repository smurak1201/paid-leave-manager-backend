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
            'employee_id' => 'required|unique:employees,employee_id,' . $id,
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

    // modeパラメータによる一括処理
    public function handleMode(Request $request)
    {
        $mode = $request->input('mode');
        if ($mode === 'add') {
            $validated = $request->validate([
                'employee_id' => 'required|unique:employees,employee_id',
                'last_name' => 'required',
                'first_name' => 'required',
                'joined_at' => 'required|date',
            ]);
            $employee = Employee::create($validated);
            return response()->json(['result' => 'ok', 'employee' => $employee]);
        } elseif ($mode === 'edit') {
            $id = $request->input('id');
            $validated = $request->validate([
                'employee_id' => 'required|unique:employees,employee_id,' . $id,
                'last_name' => 'required',
                'first_name' => 'required',
                'joined_at' => 'required|date',
            ]);
            $employee = Employee::findOrFail($id);
            $employee->update($validated);
            return response()->json(['result' => 'ok', 'employee' => $employee]);
        } elseif ($mode === 'delete') {
            $employee_id = $request->input('employee_id');
            $employee = Employee::where('employee_id', $employee_id)->first();
            if ($employee) {
                $employee->delete();
                return response()->json(['result' => 'ok']);
            } else {
                return response()->json(['error' => '該当従業員が見つかりません'], 404);
            }
        } else {
            return response()->json(['error' => '不正なmode指定'], 400);
        }
    }
}
