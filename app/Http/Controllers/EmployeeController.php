<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

/**
 * EmployeeController（従業員API用コントローラ）
 * -----------------------------------------------------
 * - 従業員情報の一覧取得・追加・編集・削除を担当
 * - RESTfulなAPI設計・例外処理・バリデーションの基本例
 *
 * 【使い方】
 * - ルーティングで /api/employees 系のエンドポイントに対応
 * - 各メソッドはAPIリクエストごとに呼び出される
 */
class EmployeeController extends Controller
{
    /**
     * 従業員一覧取得
     */
    public function index()
    {
        try {
            $employees = Employee::all();
            return response()->json($employees, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'DBエラー: ' . $e->getMessage()], 500);
        }
    }

    /**
     * 従業員追加
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|unique:employees,employee_id',
            'last_name' => 'required',
            'first_name' => 'required',
            'joined_at' => 'required|date',
        ]);

        try {
            Employee::create($validated);
            return response()->json(['result' => 'ok'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'DBエラー: ' . $e->getMessage()], 500);
        }
    }

    /**
     * 従業員編集
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'employee_id' => 'required|unique:employees,employee_id,' . $id,
            'last_name' => 'required',
            'first_name' => 'required',
            'joined_at' => 'required|date',
        ]);

        try {
            $employee = Employee::findOrFail($id);
            $employee->update($validated);
            return response()->json(['result' => 'ok'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'DBエラー: ' . $e->getMessage()], 500);
        }
    }

    /**
     * 従業員削除
     */
    public function destroy($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            $employee->delete();
            return response()->json(['result' => 'ok'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'DBエラー: ' . $e->getMessage()], 500);
        }
    }
}
