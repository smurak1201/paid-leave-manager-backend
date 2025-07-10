<?php

// =====================================================
// LeaveGrantMaster.php
// -----------------------------------------------------
// 【有給休暇管理アプリ】有給付与マスターモデル
// -----------------------------------------------------
// ▼主な役割
//   - 有給付与マスターテーブルと連携し、基準値を管理
// ▼設計意図
//   - Eloquentモデルの基本例・$tableでテーブル名指定
// ▼使い方
//   - コントローラ等から有給付与基準値データ操作に利用
// =====================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * LeaveGrantMasterモデル
 * - 有給付与マスタテーブルと連携し、基準値を管理
 * - $tableでテーブル名を指定
 * - Eloquentモデルの基本例
 */
class LeaveGrantMaster extends Model
{
  protected $table = 'leave_grant_master';
}
