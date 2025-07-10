<?php

// =====================================================
// LeaveGrantMasterController.php
// -----------------------------------------------------
// 【有給休暇管理アプリ】有給付与マスターAPIコントローラ
// -----------------------------------------------------
// ▼主な役割
//   - 勤続月数ごとの有給付与日数マスターの一覧取得・追加・編集・削除
//   - 有給付与ロジックの基準値管理
// ▼設計意図
//   - RESTfulなAPI設計例・バリデーション/エラーハンドリング例
// ▼使い方
//   - ルーティングでAPIエンドポイントに紐付けて利用
// =====================================================

namespace App\Http\Controllers;

use App\Models\LeaveGrantMaster;
use Illuminate\Http\Request;

class LeaveGrantMasterController extends Controller
{
  // 有給付与マスター一覧取得（管理者のみ許可）
  public function index(Request $request)
  {
    $user = $request->user();
    if ($user->role !== 'admin') {
      return response()->json(['message' => '権限がありません'], 403);
    }
    try {
      $master = LeaveGrantMaster::orderBy('months')->get();
      return response()->json($master, 200);
    } catch (\Exception $e) {
      return response()->json(['error' => 'DBエラー: ' . $e->getMessage()], 500);
    }
  }

  // マスター追加（管理者のみ許可）
  public function store(Request $request)
  {
    $user = $request->user();
    if ($user->role !== 'admin') {
      return response()->json(['message' => '権限がありません'], 403);
    }
    $leaveGrantMaster = LeaveGrantMaster::create($request->all());
    return response()->json($leaveGrantMaster, 201);
  }

  // マスター編集（管理者のみ許可）
  public function update(Request $request, $id)
  {
    $user = $request->user();
    if ($user->role !== 'admin') {
      return response()->json(['message' => '権限がありません'], 403);
    }
    $leaveGrantMaster = LeaveGrantMaster::findOrFail($id);
    $leaveGrantMaster->update($request->all());
    return response()->json($leaveGrantMaster);
  }

  // マスター削除（管理者のみ許可）
  public function destroy(Request $request, $id)
  {
    $user = $request->user();
    if ($user->role !== 'admin') {
      return response()->json(['message' => '権限がありません'], 403);
    }
    LeaveGrantMaster::destroy($id);
    return response()->json(null, 204);
  }
}
