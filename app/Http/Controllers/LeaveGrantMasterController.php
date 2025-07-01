<?php

// =====================================================
// LeaveGrantMasterController.php
// -----------------------------------------------------
// このコントローラは「有給付与マスター」APIの処理を担当します。
// 主な役割:
//   - 勤続月数ごとの有給付与日数マスターの一覧取得・追加・編集・削除
//   - 有給付与ロジックの基準値管理
// 設計意図:
//   - RESTfulなAPI設計のサンプル
//   - バリデーション・エラーハンドリングの基本例
// =====================================================

namespace App\Http\Controllers;

use App\Models\LeaveGrantMaster;
use Illuminate\Http\Request;

class LeaveGrantMasterController extends Controller
{
  // 有給付与マスター一覧取得
  public function index()
  {
    try {
      $master = LeaveGrantMaster::orderBy('months')->get();
      return response()->json($master, 200);
    } catch (\Exception $e) {
      return response()->json(['error' => 'DBエラー: ' . $e->getMessage()], 500);
    }
  }

  // マスター追加
  public function store(Request $request)
  {
    $leaveGrantMaster = LeaveGrantMaster::create($request->all());
    return response()->json($leaveGrantMaster, 201);
  }

  // マスター編集
  public function update(Request $request, $id)
  {
    $leaveGrantMaster = LeaveGrantMaster::findOrFail($id);
    $leaveGrantMaster->update($request->all());
    return response()->json($leaveGrantMaster);
  }

  // マスター削除
  public function destroy($id)
  {
    LeaveGrantMaster::destroy($id);
    return response()->json(null, 204);
  }
}
