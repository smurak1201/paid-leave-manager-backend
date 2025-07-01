<?php

// =====================================================
// LeaveGrantMasterController.php
// -----------------------------------------------------
// このコントローラは「有給付与マスタ」APIの処理を担当します。
// 主な役割:
//   - 勤続月数ごとの有給付与日数マスタの取得・追加・編集・削除
//   - 有給付与ロジックの基準値を管理
//
// 【Laravel初心者向けポイント】
// ・マスタデータ（制度の基準値など）は専用コントローラで管理します。
// ・index/store/update/destroyなど、RESTfulなAPI設計の基本例です。
// =====================================================

namespace App\Http\Controllers;

use App\Models\LeaveGrantMaster;
use Illuminate\Http\Request;

/**
 * LeaveGrantMasterController
 * - 有給付与マスタ（基準値）APIを担当
 * - 一覧取得・追加・編集・削除の基本例
 * - RESTfulなAPI設計の学習にも最適
 */
class LeaveGrantMasterController extends Controller
{
    /**
     * 有給付与マスタ一覧取得
     */
    public function index()
    {
        try {
            $master = LeaveGrantMaster::orderBy('months')->get();
            return response()->json($master, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'DBエラー: ' . $e->getMessage()], 500);
        }
    }

    /**
     * マスタ追加
     */
    public function store(Request $request)
    {
        $leaveGrantMaster = LeaveGrantMaster::create($request->all());
        return response()->json($leaveGrantMaster, 201);
    }

    /**
     * マスタ編集
     */
    public function update(Request $request, $id)
    {
        $leaveGrantMaster = LeaveGrantMaster::findOrFail($id);
        $leaveGrantMaster->update($request->all());
        return response()->json($leaveGrantMaster);
    }

    /**
     * マスタ削除
     */
    public function destroy($id)
    {
        LeaveGrantMaster::destroy($id);
        return response()->json(null, 204);
    }
}
