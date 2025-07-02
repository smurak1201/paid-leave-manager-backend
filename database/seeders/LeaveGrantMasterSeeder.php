<?php

// =====================================================
// LeaveGrantMasterSeeder.php（有給付与マスタ用シーダー）
// -----------------------------------------------------
// このシーダーは「有給付与マスタ」テーブルに、
// 勤続月数ごとの有給付与日数の初期データを投入します。
//
// 【Laravel初心者向けポイント】
// ・Seederはテストや初期セットアップ時にDBへデータを自動投入する仕組みです。
// ・run()メソッド内で投入データを定義し、モデル経由で登録します。
// =====================================================

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LeaveGrantMaster;

class LeaveGrantMasterSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['months' => 6, 'days' => 10],
            ['months' => 18, 'days' => 11],
            ['months' => 30, 'days' => 12],
            ['months' => 42, 'days' => 14],
            ['months' => 54, 'days' => 16],
            ['months' => 66, 'days' => 18],
            ['months' => 78, 'days' => 20],
        ];

        foreach ($data as $entry) {
            LeaveGrantMaster::create($entry);
        }
    }
}
