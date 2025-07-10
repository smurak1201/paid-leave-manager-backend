
<?php
// =====================================================
// create_leave_grant_master_table.php
// -----------------------------------------------------
// 【有給休暇管理アプリ】有給付与マスターテーブル用マイグレーション
// -----------------------------------------------------
// ▼主な役割
//   - 勤続月数ごとの有給付与日数の基準値テーブルを作成
// ▼設計意図
//   - months/daysカラムで基準値管理
// ▼使い方
//   - migrateコマンドでDBテーブル作成
// =====================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaveGrantMasterTable extends Migration
{
    public function up()
    {
        Schema::create('leave_grant_master', function (Blueprint $table) {
            $table->id();
            $table->integer('months');
            $table->integer('days');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('leave_grant_master');
    }
}
