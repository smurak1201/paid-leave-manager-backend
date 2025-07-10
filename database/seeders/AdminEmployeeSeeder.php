<?php

// =====================================================
// AdminEmployeeSeeder.php
// -----------------------------------------------------
// 【有給休暇管理アプリ】管理者従業員用シーダー
// -----------------------------------------------------
// ▼主な役割
//   - 管理者権限の従業員レコードを初期投入
// ▼設計意図
//   - テスト・初期セットアップ用のDB投入サンプル
// ▼使い方
//   - db:seedコマンド等で実行
// =====================================================

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminEmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('employees')->insert([
            'employee_id' => '0000',
            'last_name' => '管理',
            'first_name' => '者',
            'joined_at' => now(),
            'password' => Hash::make('p@ssw0rd'),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
