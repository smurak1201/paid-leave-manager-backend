<?php

// =====================================================
// Employee.php
// -----------------------------------------------------
// 【有給休暇管理アプリ】従業員モデル
// -----------------------------------------------------
// ▼主な役割
//   - 従業員テーブルと連携し、従業員情報を管理
// ▼設計意図
//   - Eloquentモデルの基本例・$fillableで一括代入制御
// ▼使い方
//   - コントローラ等から従業員データ操作に利用
// =====================================================

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Employeeモデル
 * - 従業員テーブルと連携し、従業員情報を管理
 * - $fillableで一括代入可能なカラムを指定
 * - Eloquentモデルの基本例
 */
class Employee extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'employees';
    protected $primaryKey = 'employee_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'employee_id',
        'last_name',
        'first_name',
        'joined_at',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
    ];
}
