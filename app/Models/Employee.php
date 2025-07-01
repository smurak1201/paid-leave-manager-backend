<?php

// =====================================================
// Employee.php（従業員モデル）
// -----------------------------------------------------
// このモデルは「従業員」テーブルと連携し、
// データベースの従業員情報を操作します。
//
// 【Laravel初心者向けポイント】
// ・EloquentモデルはDBテーブルと1対1で対応します。
// ・$fillableで「一括代入可能なカラム」を指定します。
// =====================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
  protected $table = 'employees';

  protected $fillable = [
    'employee_id',
    'last_name',
    'first_name',
    'joined_at',
  ];
}
