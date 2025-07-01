<?php

// =====================================================
// LeaveUsage.php（有給取得履歴モデル）
// -----------------------------------------------------
// このモデルは「有給取得履歴」テーブルと連携し、
// 各従業員の有給消化日を管理します。
//
// 【Laravel初心者向けポイント】
// ・EloquentモデルでDB操作が簡単にできます。
// ・$fillableで一括代入可能なカラムを指定します。
// =====================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveUsage extends Model
{
  protected $fillable = [
    'employee_id',
    'used_date',
  ];
}
