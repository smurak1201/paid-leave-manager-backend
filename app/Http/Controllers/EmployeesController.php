<?php

// =====================================================
// EmployeesController.php
// -----------------------------------------------------
// このコントローラは「従業員」APIの処理を担当します。
// 主な役割:
//   - 従業員情報の一覧取得・追加・編集・削除
//   - 有給休暇管理の基礎となる従業員データの管理
// 設計意図:
//   - RESTfulなAPI設計のサンプル
//   - バリデーション・エラーハンドリングの基本例
// =====================================================

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\EmployeeRequest;

class EmployeesController extends Controller
{
    // 権限制御付き 従業員一覧取得
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user->role === 'admin') {
            // 管理者は全件取得
            $employees = Employee::all();
        } elseif ($user->role === 'viewer' && $user->employee_id) {
            // 閲覧ユーザーは自分の従業員データのみ
            $employees = Employee::where('employee_id', $user->employee_id)->get();
        } else {
            // 権限なし
            return response()->json(['message' => '権限がありません'], 403);
        }
        return response()->json($employees);
    }

    // 従業員追加
    public function store(EmployeeRequest $request)
    {
        $employee = Employee::create($request->validated());
        return response()->json(['result' => 'ok', 'employee' => $employee]);
    }

    // 従業員編集
    public function update(EmployeeRequest $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $employee->update($request->validated());
        return response()->json(['result' => 'ok', 'employee' => $employee]);
    }

    // 従業員削除
    public function destroy($id)
    {
        $employee = Employee::where('employee_id', $id)->firstOrFail();
        $employee->delete();
        return response()->json(['result' => 'ok']);
    }
}
