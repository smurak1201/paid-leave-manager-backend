<?php

// =====================================================
// LeaveGrantMaster.php（有給付与マスタモデル）
// -----------------------------------------------------
// このモデルは「有給付与マスタ」テーブルと連携し、
// 勤続月数ごとの有給付与日数の基準値を管理します。
//
// 【Laravel初心者向けポイント】
// ・$tableで対応するDBテーブル名を指定します。
// ・制度ロジックの基準値を管理する用途で利用します。
// =====================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveGrantMaster extends Model
{
  protected $table = 'leave_grant_master';
}
